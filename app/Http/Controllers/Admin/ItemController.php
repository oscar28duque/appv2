<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\MaintenanceRecord;
use App\Models\Media;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ItemController extends Controller
{
    /**
     * Catálogo de equipos disponibles para alquiler con filtros por categoría y estado.
     */
    public function index(Request $request): View
    {
        $query = Item::with('media')->latest();

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $items = $query->paginate(12)->withQueryString();
        $categories = Item::CATEGORIES;

        return view('admin.items.index', compact('items', 'categories'));
    }

    /**
     * Formulario de creación de artículo.
     */
    public function create(): View
    {
        $categories = Item::CATEGORIES;
        $mediaItems = Media::latest()->get();

        return view('admin.items.create', compact('categories', 'mediaItems'));
    }

    /**
     * Guarda el nuevo artículo en el inventario.
     * Permite asociar imagen existente o subir una nueva de manera segura.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:items,code'],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', Rule::in(Item::CATEGORIES)],
            'description' => ['nullable', 'string'],
            'total_quantity' => ['required', 'integer', 'min:1'],
            'daily_rate' => ['required', 'numeric', 'min:0'],
            'weekend_rate' => ['required', 'numeric', 'min:0'],
            'media_id' => ['nullable', 'exists:media,id'],
            'file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            'status' => ['required', 'string', Rule::in(['disponible', 'en_mantenimiento', 'dado_de_baja'])],
        ]);

        $mediaId = $validated['media_id'] ?? null;

        // Subida segura de imagen para el artículo
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('media', 'public');

            $media = Media::create([
                'name' => $validated['name'] . ' (' . $validated['code'] . ')',
                'path' => $path,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'active' => true,
            ]);

            $mediaId = $media->id;
        }

        Item::create([
            'code' => strtoupper($validated['code']),
            'name' => $validated['name'],
            'category' => $validated['category'],
            'description' => $validated['description'] ?? null,
            'total_quantity' => $validated['total_quantity'],
            'daily_rate' => $validated['daily_rate'],
            'weekend_rate' => $validated['weekend_rate'],
            'media_id' => $mediaId,
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('admin.items.index')
            ->with('success', 'Equipo registrado exitosamente en el inventario.');
    }

    /**
     * Muestra la ficha técnica del equipo y su historial de mantenimientos.
     */
    public function show(Item $item): View
    {
        $item->load(['media', 'maintenanceRecords' => fn($q) => $q->latest()]);
        $maintenanceTypes = MaintenanceRecord::TYPES;

        return view('admin.items.show', compact('item', 'maintenanceTypes'));
    }

    /**
     * Formulario de edición.
     */
    public function edit(Item $item): View
    {
        $categories = Item::CATEGORIES;
        $mediaItems = Media::latest()->get();

        return view('admin.items.edit', compact('item', 'categories', 'mediaItems'));
    }

    /**
     * Actualiza el artículo.
     */
    public function update(Request $request, Item $item): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('items', 'code')->ignore($item->id)],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', Rule::in(Item::CATEGORIES)],
            'description' => ['nullable', 'string'],
            'total_quantity' => ['required', 'integer', 'min:1'],
            'daily_rate' => ['required', 'numeric', 'min:0'],
            'weekend_rate' => ['required', 'numeric', 'min:0'],
            'media_id' => ['nullable', 'exists:media,id'],
            'file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            'status' => ['required', 'string', Rule::in(['disponible', 'en_mantenimiento', 'dado_de_baja'])],
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('media', 'public');

            $media = Media::create([
                'name' => $validated['name'] . ' (' . $validated['code'] . ')',
                'path' => $path,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'active' => true,
            ]);

            $item->media_id = $media->id;
        } elseif ($request->has('media_id')) {
            $item->media_id = $validated['media_id'];
        }

        $item->code = strtoupper($validated['code']);
        $item->name = $validated['name'];
        $item->category = $validated['category'];
        $item->description = $validated['description'] ?? null;
        $item->total_quantity = $validated['total_quantity'];
        $item->daily_rate = $validated['daily_rate'];
        $item->weekend_rate = $validated['weekend_rate'];
        $item->status = $validated['status'];
        $item->save();

        return redirect()
            ->route('admin.items.index')
            ->with('success', 'Equipo actualizado correctamente.');
    }

    /**
     * Elimina el equipo si no tiene reservas históricas asociadas.
     */
    public function destroy(Item $item): RedirectResponse
    {
        if ($item->reservationItems()->exists()) {
            return back()->with('error', 'No se puede eliminar el equipo porque cuenta con reservas registradas. Puedes cambiar su estado a "dado de baja".');
        }

        $item->delete();

        return redirect()
            ->route('admin.items.index')
            ->with('success', 'Equipo eliminado del catálogo.');
    }

    /**
     * Registra un mantenimiento técnico o baja para el equipo (Requerimiento 3.10).
     */
    public function storeMaintenance(Request $request, Item $item): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'string', Rule::in(array_keys(MaintenanceRecord::TYPES))],
            'cost' => ['required', 'numeric', 'min:0'],
            'description' => ['required', 'string'],
            'status' => ['required', 'string', Rule::in(['programado', 'en_proceso', 'completado'])],
            'maintenance_date' => ['required', 'date'],
        ]);

        $item->maintenanceRecords()->create($validated);

        if ($validated['type'] === 'baja_inventario') {
            $item->status = 'dado_de_baja';
            $item->save();
        } elseif ($validated['status'] !== 'completado') {
            $item->status = 'en_mantenimiento';
            $item->save();
        } else {
            $item->status = 'disponible';
            $item->save();
        }

        return back()->with('success', 'Registro de mantenimiento añadido exitosamente.');
    }
}
