<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MediaController extends Controller
{
    /**
     * Muestra la biblioteca multimedia con galería y formulario de carga.
     */
    public function index(Request $request): View|JsonResponse
    {
        $media = Media::latest()->paginate(12);

        if ($request->wantsJson()) {
            return response()->json($media);
        }

        return view('admin.media.index', compact('media'));
    }

    /**
     * Carga y almacena de forma segura una nueva imagen en el servidor.
     * Implementa validación estricta de tipo MIME y tamaño máximo en servidor.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'file' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,webp,gif',
                'max:5120', // Máximo 5 MB (5120 KB)
            ],
        ]);

        $file = $request->file('file');

        // Guarda el archivo en storage/app/public/media usando un nombre seguro autogenerado
        $path = $file->store('media', 'public');

        $media = Media::create([
            'name' => $validated['name'],
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'active' => true,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Archivo cargado correctamente en la biblioteca multimedia.',
                'data' => [
                    'id' => $media->id,
                    'name' => $media->name,
                    'path' => $media->path,
                    'url' => Storage::disk('public')->url($media->path),
                    'mime_type' => $media->mime_type,
                    'size' => $media->size,
                ],
            ], 201);
        }

        return redirect()
            ->route('admin.media.index')
            ->with('success', 'Archivo cargado correctamente en la biblioteca multimedia.');
    }

    /**
     * Reemplaza el archivo físico o actualiza el nombre del recurso.
     * Si se sube una nueva imagen, se elimina físicamente la anterior para evitar residuos.
     */
    public function update(Request $request, Media $media): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'file' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp,gif',
                'max:5120',
            ],
        ]);

        $media->name = $validated['name'];

        if ($request->hasFile('file')) {
            // Elimina el archivo antiguo si existe en el disco public
            if ($media->path && Storage::disk('public')->exists($media->path)) {
                Storage::disk('public')->delete($media->path);
            }

            $file = $request->file('file');
            $newPath = $file->store('media', 'public');

            $media->path = $newPath;
            $media->mime_type = $file->getMimeType();
            $media->size = $file->getSize();
        }

        $media->save();

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Archivo multimedia actualizado exitosamente.',
                'data' => [
                    'id' => $media->id,
                    'name' => $media->name,
                    'path' => $media->path,
                    'url' => Storage::disk('public')->url($media->path),
                    'mime_type' => $media->mime_type,
                    'size' => $media->size,
                ],
            ], 200);
        }

        return redirect()
            ->route('admin.media.index')
            ->with('success', 'Archivo multimedia actualizado exitosamente.');
    }

    /**
     * Elimina el archivo físico de Storage y el registro correspondiente en la base de datos.
     */
    public function destroy(Request $request, Media $media): RedirectResponse|JsonResponse
    {
        // Elimina el archivo del almacenamiento físico
        if ($media->path && Storage::disk('public')->exists($media->path)) {
            Storage::disk('public')->delete($media->path);
        }

        $media->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Archivo multimedia eliminado permanentemente del servidor.',
            ], 200);
        }

        return redirect()
            ->route('admin.media.index')
            ->with('success', 'Archivo multimedia eliminado permanentemente del servidor.');
    }
}
