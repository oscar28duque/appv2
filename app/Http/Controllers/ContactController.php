<?php

namespace App\Http\Controllers;

use App\Mail\ContactConfirmation;
use App\Mail\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactController extends Controller
{
    /**
     * Muestra la página o vista con el formulario de contacto.
     */
    public function show(): View
    {
        return view('contact');
    }

    /**
     * Procesa y valida los datos del formulario de contacto y envía los correos correspondientes.
     * Implementa controles de validación en servidor, límites de longitud y notificación doble (admin y usuario).
     */
    public function send(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $adminEmail = config('mail.admin_address', config('mail.from.address', 'administracion@empresa.com'));

        try {
            // 1. Envío al administrador del sistema / CMS
            Mail::to($adminEmail)->send(
                new ContactMessage(
                    $validated['name'],
                    $validated['email'],
                    $validated['message']
                )
            );

            // 2. Envío de confirmación automática al usuario que escribió
            Mail::to($validated['email'])->send(
                new ContactConfirmation(
                    $validated['name'],
                    $validated['email'],
                    $validated['message']
                )
            );

            return back()->with('success', '¡Gracias por contactarnos! Tu mensaje fue enviado exitosamente y hemos enviado una confirmación a tu correo.');
        } catch (\Throwable $e) {
            Log::error('Error enviando correos de contacto: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Ocurrió un inconveniente al procesar el envío del correo. Por favor intenta más tarde o verifica la configuración del servidor de correo.');
        }
    }
}
