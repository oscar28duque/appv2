<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva Confirmada</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f8;font-family:Arial,sans-serif;color:#333333;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f8;padding:25px 0;">
        <tr>
            <td align="center">
                <table width="650" cellpadding="0" cellspacing="0" style="max-width:650px;width:100%;background:#ffffff;margin:0 auto;border-radius:12px;overflow:hidden;box-shadow:0 4px 10px rgba(0,0,0,0.05);">
                    <!-- Header -->
                    <tr>
                        <td style="background:#022766;padding:35px 30px;text-align:center;color:#ffffff;">
                            <h1 style="margin:0;font-size:24px;letter-spacing:0.5px;">{{ config('app.name') }}</h1>
                            <p style="margin:6px 0 0 0;font-size:14px;opacity:0.9;">Gestión de Alquiler de Equipos & Logística de Eventos</p>
                        </td>
                    </tr>
                    <!-- Body -->
                    <tr>
                        <td style="padding:35px;">
                            <div style="text-align:right;margin-bottom:15px;">
                                <span style="background:#e0f2fe;color:#0369a1;padding:6px 12px;border-radius:20px;font-size:13px;font-weight:bold;">
                                    Reserva: {{ $reservation->code }}
                                </span>
                            </div>

                            <h2 style="margin-top:0;color:#022766;font-size:20px;">¡Hola, {{ $client->name }}!</h2>
                            <p style="color:#555555;font-size:15px;line-height:1.5;">
                                Tu solicitud de alquiler de equipos para el evento <strong>{{ $reservation->event_name ?? 'Evento Especial' }}</strong> ha sido confirmada satisfactoriamente.
                            </p>

                            <!-- Datos del Evento -->
                            <table width="100%" cellpadding="8" cellspacing="0" style="background:#f8fafc;border-radius:8px;font-size:14px;margin:20px 0;border-left:4px solid #022766;">
                                <tr>
                                    <td width="140" style="color:#64748b;font-weight:bold;">Fecha Inicio:</td>
                                    <td style="color:#0f172a;font-weight:600;">{{ $reservation->start_date->format('d/m/Y') }}</td>
                                    <td width="140" style="color:#64748b;font-weight:bold;">Fecha Fin:</td>
                                    <td style="color:#0f172a;font-weight:600;">{{ $reservation->end_date->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <td style="color:#64748b;font-weight:bold;">Lugar:</td>
                                    <td style="color:#0f172a;" colspan="3">{{ $reservation->event_location ?? 'Por definir / En sede' }}</td>
                                </tr>
                                <tr>
                                    <td style="color:#64748b;font-weight:bold;">Modalidad:</td>
                                    <td style="color:#0f172a;" colspan="3">
                                        {{ $reservation->pricing_type === 'weekend' ? 'Tarifa Plana Fin de Semana' : 'Tarifa Diaria Estándar' }}
                                    </td>
                                </tr>
                            </table>

                            <!-- Detalle de Equipos -->
                            <h3 style="color:#022766;font-size:16px;margin-bottom:10px;">Equipos Reservados</h3>
                            <table width="100%" cellpadding="10" cellspacing="0" style="border-collapse:collapse;font-size:14px;margin-bottom:20px;">
                                <thead>
                                    <tr style="background:#f1f5f9;color:#475569;border-bottom:2px solid #cbd5e1;">
                                        <th align="left">Equipo</th>
                                        <th align="center">Cant.</th>
                                        <th align="right">Tarifa</th>
                                        <th align="right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $detail)
                                        <tr style="border-bottom:1px solid #e2e8f0;">
                                            <td>
                                                <strong>{{ $detail->item->name ?? 'Equipo' }}</strong><br>
                                                <small style="color:#64748b;">Código: {{ $detail->item->code ?? '-' }}</small>
                                            </td>
                                            <td align="center">{{ $detail->quantity }}</td>
                                            <td align="right">${{ number_format($detail->unit_price, 2) }}</td>
                                            <td align="right"><strong>${{ number_format($detail->subtotal, 2) }}</strong></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- Resumen Financiero -->
                            <table width="100%" cellpadding="6" cellspacing="0" style="font-size:14px;max-width:320px;margin-left:auto;margin-bottom:25px;">
                                <tr>
                                    <td style="color:#64748b;">Subtotal:</td>
                                    <td align="right" style="font-weight:600;">${{ number_format($reservation->subtotal, 2) }}</td>
                                </tr>
                                @if($reservation->discount > 0)
                                    <tr>
                                        <td style="color:#16a34a;">Descuento:</td>
                                        <td align="right" style="color:#16a34a;font-weight:600;">-${{ number_format($reservation->discount, 2) }}</td>
                                    </tr>
                                @endif
                                <tr style="border-top:1px solid #cbd5e1;font-size:16px;">
                                    <td style="color:#022766;font-weight:bold;">Total Alquiler:</td>
                                    <td align="right" style="color:#022766;font-weight:bold;">${{ number_format($reservation->total_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="color:#64748b;">Abonado:</td>
                                    <td align="right" style="color:#0284c7;font-weight:600;">${{ number_format($reservation->paid_amount, 2) }}</td>
                                </tr>
                                <tr style="background:#fef3c7;border-radius:6px;">
                                    <td style="color:#b45309;font-weight:bold;">Saldo Pendiente:</td>
                                    <td align="right" style="color:#b45309;font-weight:bold;">${{ number_format($reservation->pending_balance, 2) }}</td>
                                </tr>
                            </table>

                            <p style="color:#64748b;font-size:13px;line-height:1.5;">
                                * Nota: El despacho y entrega de los equipos se efectuará contra el pago del saldo o según las condiciones acordadas. Ante cualquier modificación o consulta, contáctanos indicando tu código de reserva.
                            </p>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="background:#f1f5f9;padding:20px;text-align:center;font-size:12px;color:#64748b;border-top:1px solid #e2e8f0;">
                            <strong>{{ config('app.name') }}</strong> &bull; Soporte técnico y logística de eventos<br>
                            Este es un mensaje generado automáticamente para la confirmación del servicio.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
