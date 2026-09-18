<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo mensaje de contacto</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f8;font-family:Arial,sans-serif;color:#333333;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f8;padding:20px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;margin:20px auto;border-radius:12px;overflow:hidden;box-shadow:0 4px 10px rgba(0,0,0,0.05);">
                    <!-- Header -->
                    <tr>
                        <td style="background:#022766;padding:30px;text-align:center;color:#ffffff;">
                            <h1 style="margin:0;font-size:24px;letter-spacing:0.5px;">{{ config('app.name') }}</h1>
                            <p style="margin:5px 0 0 0;font-size:13px;opacity:0.85;">Panel de Notificaciones Administrativas</p>
                        </td>
                    </tr>
                    <!-- Body -->
                    <tr>
                        <td style="padding:35px;">
                            <h2 style="margin-top:0;color:#022766;font-size:20px;">Nuevo mensaje de contacto</h2>
                            <p style="color:#555555;font-size:15px;line-height:1.5;">Se ha recibido un nuevo mensaje desde el formulario del sitio web.</p>
                            <hr style="border:none;border-top:1px solid #e2e8f0;margin:20px 0;">

                            <table width="100%" cellpadding="6" cellspacing="0" style="font-size:14px;">
                                <tr>
                                    <td width="120" style="color:#64748b;font-weight:bold;">Nombre:</td>
                                    <td style="color:#0f172a;font-weight:600;">{{ $name }}</td>
                                </tr>
                                <tr>
                                    <td style="color:#64748b;font-weight:bold;">Correo:</td>
                                    <td style="color:#0f172a;">
                                        <a href="mailto:{{ $email }}" style="color:#022766;text-decoration:none;">{{ $email }}</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="color:#64748b;font-weight:bold;vertical-align:top;">Fecha:</td>
                                    <td style="color:#0f172a;">{{ now()->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>

                            <p style="margin-top:25px;margin-bottom:8px;color:#64748b;font-weight:bold;font-size:14px;">Mensaje transmitido:</p>
                            <div style="background:#f8fafc;border-left:4px solid #022766;padding:18px 20px;border-radius:4px;font-size:14px;line-height:1.6;color:#1e293b;white-space:pre-wrap;">{{ $userMessage }}</div>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="background:#f1f5f9;padding:20px;text-align:center;font-size:12px;color:#64748b;border-top:1px solid #e2e8f0;">
                            Este mensaje fue generado automáticamente desde el sitio web institucional de <strong>{{ config('app.name') }}</strong>.<br>
                            Todos los datos fueron transmitidos bajo controles de validación y seguridad en el servidor.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
