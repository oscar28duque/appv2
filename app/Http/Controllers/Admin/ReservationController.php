<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ReservationConfirmed;
use App\Models\Client;
use App\Models\Item;
use App\Models\Media;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\ReservationItem;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ReservationController extends Controller
{
    /**
     * Listado de reservas con filtros por estado, fechas y buscador.
     */
    public function index(Request $request): View
    {
        $query = Reservation::with(['client', 'items.item', 'payments'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('start_date')) {
            $query->whereDate('start_date', '>=', $request->input('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('end_date', '<=', $request->input('end_date'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('event_name', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                         ->orWhere('document', 'like', "%{$search}%");
                  });
            });
        }

        $reservations = $query->paginate(15)->withQueryString();
        $statuses = Reservation::STATUSES;

        return view('admin.reservations.index', compact('reservations', 'statuses'));
    }

    /**
     * Formulario de creación de reserva.
     */
    public function create(): View
    {
        $clients = Client::orderBy('name')->get();
        $items = Item::where('status', 'disponible')->orderBy('category')->orderBy('name')->get();

        return view('admin.reservations.create', compact('clients', 'items'));
    }

    /**
     * Valida disponibilidad, sobrecupo, calcula tarifas diferenciadas y registra la reserva.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'event_name' => ['required', 'string', 'max:255'],
            'event_location' => ['nullable', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'pricing_type' => ['required', 'in:daily,weekend'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'initial_payment' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'exists:items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $startDate = $validated['start_date'];
        $endDate = $validated['end_date'];
        $pricingType = $validated['pricing_type'];

        // Calcular duración en días
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $days = max(1, $start->diffInDays($end) + 1);

        // 1. Validación de Sobrecupo para cada artículo en el rango de fechas
        $itemsToBook = [];
        $subtotal = 0;

        foreach ($validated['items'] as $entry) {
            $itemId = (int) $entry['item_id'];
            $requestedQty = (int) $entry['quantity'];

            $item = Item::findOrFail($itemId);
            $availableQty = $item->getAvailableQuantityForRange($startDate, $endDate);

            if ($requestedQty > $availableQty) {
                throw ValidationException::withMessages([
                    'items' => "Sobrecupo detectado: El equipo '{$item->name}' sólo dispone de {$availableQty} unidades para el periodo seleccionado (solicitadas: {$requestedQty}).",
                ]);
            }

            // Lógica de facturación diferenciada (Tarifa por día vs. Tarifa plana de fin de semana)
            if ($pricingType === 'weekend') {
                $unitPrice = (float) $item->weekend_rate;
            } else {
                $unitPrice = (float) $item->daily_rate * $days;
            }

            $lineSubtotal = $unitPrice * $requestedQty;
            $subtotal += $lineSubtotal;

            $itemsToBook[] = [
                'item' => $item,
                'quantity' => $requestedQty,
                'unit_price' => $unitPrice,
                'subtotal' => $lineSubtotal,
            ];
        }

        $discount = (float) ($validated['discount'] ?? 0);
        $totalAmount = max(0, $subtotal - $discount);
        $initialPayment = (float) ($validated['initial_payment'] ?? 0);
        $paidAmount = min($totalAmount, $initialPayment);

        // 2. Transacción de creación
        $reservation = DB::transaction(function () use ($validated, $subtotal, $discount, $totalAmount, $paidAmount, $itemsToBook, $pricingType) {
            $year = date('Y');
            $countThisYear = Reservation::whereYear('created_at', $year)->count() + 1;
            $code = 'RSV-' . $year . '-' . str_pad($countThisYear, 4, '0', STR_PAD_LEFT);

            $reservation = Reservation::create([
                'code' => $code,
                'client_id' => $validated['client_id'],
                'event_name' => $validated['event_name'],
                'event_location' => $validated['event_location'] ?? null,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'pricing_type' => $pricingType,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'status' => 'confirmada',
            ]);

            foreach ($itemsToBook as $line) {
                ReservationItem::create([
                    'reservation_id' => $reservation->id,
                    'item_id' => $line['item']->id,
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'subtotal' => $line['subtotal'],
                ]);
            }

            // Registrar abono inicial si aplica
            if ($paidAmount > 0) {
                Payment::create([
                    'reservation_id' => $reservation->id,
                    'amount' => $paidAmount,
                    'payment_method' => $validated['payment_method'] ?? 'transferencia',
                    'payment_date' => now()->toDateString(),
                    'notes' => 'Abono inicial al momento de la reserva.',
                ]);
            }

            return $reservation;
        });

        // 3. Notificación automática por correo al cliente (Requerimiento transversal)
        $client = $reservation->client;
        if (!empty($client->email)) {
            try {
                Mail::to($client->email)->send(new ReservationConfirmed($reservation));
            } catch (\Throwable $e) {
                Log::warning("No se pudo enviar correo de confirmación de reserva #{$reservation->code}: {$e->getMessage()}");
            }
        }

        return redirect()
            ->route('admin.reservations.show', $reservation)
            ->with('success', "Reserva {$reservation->code} creada exitosamente.");
    }

    /**
     * Detalle completo de la reserva.
     */
    public function show(Reservation $reservation): View
    {
        $reservation->load(['client', 'items.item.media', 'payments', 'evidenceMedia']);
        $statuses = Reservation::STATUSES;

        return view('admin.reservations.show', compact('reservation', 'statuses'));
    }

    /**
     * Actualiza el estado de la reserva.
     */
    public function updateStatus(Request $request, Reservation $reservation): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pendiente,confirmada,en_curso,devuelta,cancelada'],
        ]);

        $reservation->status = $validated['status'];
        $reservation->save();

        return back()->with('success', "Estado actualizado a '{$reservation->status}'.");
    }

    /**
     * Registro de abono o pago parcial a la reserva (Requerimiento 3.7 Cuentas por cobrar).
     */
    public function storePayment(Request $request, Reservation $reservation): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'string'],
            'reference' => ['nullable', 'string', 'max:100'],
            'payment_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $amount = (float) $validated['amount'];
        $pending = $reservation->pending_balance;

        if ($amount > $pending) {
            return back()->with('error', "El monto ingresado ($" . number_format($amount, 2) . ") supera el saldo pendiente de la reserva ($" . number_format($pending, 2) . ").");
        }

        DB::transaction(function () use ($reservation, $validated, $amount) {
            Payment::create([
                'reservation_id' => $reservation->id,
                'amount' => $amount,
                'payment_method' => $validated['payment_method'],
                'reference' => $validated['reference'] ?? null,
                'payment_date' => $validated['payment_date'],
                'notes' => $validated['notes'] ?? null,
            ]);

            $reservation->paid_amount += $amount;
            $reservation->save();
        });

        return back()->with('success', 'Abono registrado exitosamente.');
    }

    /**
     * Carga de evidencia fotográfica por entrega (Requerimiento 3.5).
     */
    public function uploadEvidence(Request $request, Reservation $reservation): RedirectResponse
    {
        $request->validate([
            'evidence_file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $file = $request->file('evidence_file');
        $path = $file->store('media/evidencias', 'public');

        $media = Media::create([
            'name' => "Evidencia Entrega {$reservation->code}",
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'active' => true,
        ]);

        $reservation->evidence_media_id = $media->id;
        $reservation->save();

        return back()->with('success', 'Evidencia fotográfica de entrega adjuntada con éxito.');
    }

    /**
     * Proceso de devolución de artículos alquilados (Requerimiento 3.6 & 3.4).
     */
    public function storeReturn(Request $request, Reservation $reservation): RedirectResponse
    {
        $validated = $request->validate([
            'return_date_real' => ['required', 'date'],
            'return_status' => ['required', 'in:sin_novedad,con_danos,faltantes'],
            'return_notes' => ['nullable', 'string'],
        ]);

        $reservation->return_date_real = $validated['return_date_real'];
        $reservation->return_status = $validated['return_status'];
        $reservation->return_notes = $validated['return_notes'] ?? null;
        $reservation->status = 'devuelta';
        $reservation->save();

        return back()->with('success', 'Devolución asentada correctamente. Los equipos han quedado liberados en stock.');
    }
}
