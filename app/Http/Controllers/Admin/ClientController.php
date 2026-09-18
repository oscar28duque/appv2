<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    /**
     * Directorio de clientes con filtros de búsqueda rápida.
     */
    public function index(Request $request): View
    {
        $query = Client::withCount('reservations')->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('document', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%");
            });
        }

        $clients = $query->paginate(15)->withQueryString();

        return view('admin.clients.index', compact('clients'));
    }

    /**
     * Formulario de creación de cliente.
     */
    public function create(): View
    {
        return view('admin.clients.create');
    }

    /**
     * Almacena el cliente.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'document' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'company' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $client = Client::create($validated);

        return redirect()
            ->route('admin.clients.show', $client)
            ->with('success', 'Cliente registrado correctamente.');
    }

    /**
     * Ficha detallada del cliente con su historial de reservas y pagos.
     */
    public function show(Client $client): View
    {
        $client->load(['reservations.payments', 'reservations.items.item']);

        return view('admin.clients.show', compact('client'));
    }

    /**
     * Formulario de edición.
     */
    public function edit(Client $client): View
    {
        return view('admin.clients.edit', compact('client'));
    }

    /**
     * Actualiza la información del cliente.
     */
    public function update(Request $request, Client $client): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'document' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'company' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $client->update($validated);

        return redirect()
            ->route('admin.clients.show', $client)
            ->with('success', 'Datos del cliente actualizados.');
    }

    /**
     * Elimina el cliente si no tiene reservas activas.
     */
    public function destroy(Client $client): RedirectResponse
    {
        if ($client->reservations()->exists()) {
            return back()->with('error', 'No se puede eliminar el cliente porque posee historial de reservas.');
        }

        $client->delete();

        return redirect()
            ->route('admin.clients.index')
            ->with('success', 'Cliente eliminado.');
    }
}
