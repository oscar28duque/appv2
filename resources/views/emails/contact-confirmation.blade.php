<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hemos recibido tu mensaje</title>
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
                            <p style="margin:5px 0 0 0;font-size:13px;opacity:0.85;">Confirmación de Contacto</p>
                        </td>
                    </tr>
                    <!-- Body -->
                    <tr>
                        <td style="padding:35px;">
                            <h2 style="margin-top:0;color:#022766;font-size:20px;">¡Hola, {{ $name }}!</h2>
                            <p style="color:#555555;font-size:15px;line-height:1.6;">
                                Confirmamos que hemos recibido tu mensaje correctamente a través de nuestro sitio web.
                                Nuestro equipo lo revisará y se pondrá en contacto contigo a la mayor brevedad posible.
                            </p>
                            <hr style="border:none;border-top:1px solid #e2e8f0;margin:20px 0;">

                            <h3 style="color:#1e293b;font-size:15px;margin-bottom:10px;">Copia del mensaje enviado:</h3>
                            <div style="background:#f8fafc;border-left:4px solid #0284c7;padding:15px 18px;border-radius:4px;font-size:14px;line-height:1.6;color:#334155;white-space:pre-wrap;">{{ $userMessage }}</div>

                            <p style="margin-top:25px;font-size:13px;color:#64748b;">
                                Si no realizaste esta solicitud o consideras que se trata de un error, por favor haz caso omiso a este mensaje.
                            </p>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="background:#f1f5f9;padding:20px;text-align:center;font-size:12px;color:#64748b;border-top:1px solid #e2e8f0;">
                            Gracias por comunicarte con <strong>{{ config('app.name') }}</strong>.<br>
                            Este es un correo automático, por favor no respondas a este remitente.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
