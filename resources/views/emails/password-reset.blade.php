<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background: white;
            padding: 30px;
            border-radius: 0 0 5px 5px;
        }
        .button {
            display: inline-block;
            background-color: #667eea;
            color: white;
            padding: 12px 30px;
            border-radius: 5px;
            text-decoration: none;
            margin: 20px 0;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #999;
        }
        .warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Recupera tu Contraseña</h1>
        </div>

        <div class="content">
            <p>Hola <strong>{{ $user->name }}</strong>,</p>

            <p>Recibimos una solicitud para resetear la contraseña de tu cuenta. Si no fuiste tú, ignora este email.</p>

            <p>Haz click en el botón de abajo para crear una nueva contraseña:</p>

            <div style="text-align: center;">
                <a href="{{ $resetLink }}" class="button">Recuperar Contraseña</a>
            </div>

            <p>O copia y pega este enlace en tu navegador:</p>
            <p style="word-break: break-all; background-color: #f5f5f5; padding: 10px; border-radius: 4px;">
                {{ $resetLink }}
            </p>

            <div class="warning">
                <strong>⏰ Atención:</strong> Este enlace vence en 1 hora por seguridad. Si no lo usas en ese tiempo, tendrás que solicitar otro.
            </div>

            <div class="footer">
                <p>Este es un email automático, por favor no respondas a este correo.</p>
                <p>Si tienes problemas, contacta con nuestro equipo de soporte.</p>
                <p>&copy; {{ date('Y') }} {{ env('APP_NAME', 'Mi Aplicación') }}. Todos los derechos reservados.</p>
            </div>
        </div>
    </div>
</body>
</html>
