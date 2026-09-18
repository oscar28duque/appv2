<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\News;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class NewsController extends Controller
{
    /**
     * Listado de noticias en el panel administrativo.
     */
    public function index(): View
    {
        $news = News::with('media')->latest()->paginate(10);

        return view('admin.news.index', compact('news'));
    }

    /**
     * Formulario para redactar una nueva noticia.
     */
    public function create(): View
    {
        $mediaItems = Media::latest()->get();

        return view('admin.news.create', compact('mediaItems'));
    }

    /**
     * Almacena una nueva publicación, asociando una imagen existente o cargando una nueva.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:news,slug'],
            'excerpt' => ['nullable', 'string', 'max:600'],
            'content' => ['required', 'string'],
            'media_id' => ['nullable', 'exists:media,id'],
            'file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            'published' => ['nullable'],
        ]);

        $mediaId = $request->input('media_id');

        // Si se subió un nuevo archivo directo desde el formulario de noticia
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('media', 'public');

            $media = Media::create([
                'name' => $validated['title'] . ' (Portada)',
                'path' => $path,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'active' => true,
            ]);

            $mediaId = $media->id;
        }

        $slug = !empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['title']) . '-' . Str::random(5);

        News::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'excerpt' => $validated['excerpt'] ?? Str::limit(strip_tags($validated['content']), 150),
            'content' => $validated['content'],
            'media_id' => $mediaId,
            'published' => $request->boolean('published'),
        ]);

        return redirect()
            ->route('admin.news.index')
            ->with('success', 'Publicación creada exitosamente.');
    }

    /**
     * Formulario de edición de noticia.
     */
    public function edit(News $news): View
    {
        $news->load('media');
        $mediaItems = Media::latest()->get();

        return view('admin.news.edit', compact('news', 'mediaItems'));
    }

    /**
     * Actualiza la publicación y gestiona el reemplazo de imagen según sección 5.13 de la guía.
     */
    public function update(Request $request, News $news): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('news', 'slug')->ignore($news->id)],
            'excerpt' => ['nullable', 'string', 'max:600'],
            'content' => ['required', 'string'],
            'media_id' => ['nullable', 'exists:media,id'],
            'file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            'published' => ['nullable'],
        ]);

        // Manejo de reemplazo de archivo directo según especificación 5.13:
        if ($request->hasFile('file')) {
            if ($news->media) {
                // Elimina el archivo anterior del disco public para evitar acumulación de residuos
                if (Storage::disk('public')->exists($news->media->path)) {
                    Storage::disk('public')->delete($news->media->path);
                }

                $file = $request->file('file');
                $path = $file->store('media', 'public');

                $news->media->update([
                    'path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ]);
            } else {
                $file = $request->file('file');
                $path = $file->store('media', 'public');

                $media = Media::create([
                    'name' => $validated['title'] . ' (Portada)',
                    'path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'active' => true,
                ]);

                $news->media_id = $media->id;
            }
        } elseif ($request->has('media_id')) {
            $news->media_id = $request->input('media_id');
        }

        $slug = !empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['title']);

        $news->title = $validated['title'];
        $news->slug = $slug;
        $news->excerpt = $validated['excerpt'] ?? Str::limit(strip_tags($validated['content']), 150);
        $news->content = $validated['content'];
        $news->published = $request->boolean('published');
        $news->save();

        return redirect()
            ->route('admin.news.index')
            ->with('success', 'Publicación actualizada exitosamente.');
    }

    /**
     * Elimina la publicación.
     */
    public function destroy(News $news): RedirectResponse
    {
        $news->delete();

        return redirect()
            ->route('admin.news.index')
            ->with('success', 'Publicación eliminada correctamente.');
    }

    /**
     * Alternar estado publicado/borrador rápidamente.
     */
    public function togglePublish(News $news): RedirectResponse
    {
        $news->published = !$news->published;
        $news->save();

        $statusText = $news->published ? 'publicada' : 'retirada a borrador';

        return back()->with('success', "La noticia ahora está {$statusText}.");
    }
}
