<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmación de Cita</title>
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
        .appointment-info {
            background-color: #f0f0f0;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .label {
            font-weight: bold;
            color: #667eea;
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
        .button-group {
            text-align: center;
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .button-cancel {
            display: inline-block;
            background-color: #ffffff;
            color: #dc3545;
            padding: 12px 30px;
            border-radius: 5px;
            text-decoration: none;
            margin: 20px 0;
            font-weight: bold;
            border: 2px solid #dc3545;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✓ Cita Reservada</h1>
        </div>

        <div class="content">
            <p>Hola {{ $appointment->nombrePaciente() }},</p>

            <p>Tu cita ha sido reservada exitosamente. Por favor, confirma tu cita haciendo click en el botón de abajo:</p>

            <div class="appointment-info">
                <div class="info-row">
                    <span class="label">Fecha y Hora:</span>
                    <span>{{ $appointment->appointment_date->format('d/m/Y H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Duración:</span>
                    <span>{{ $appointment->duracionTexto() }}</span>
                </div>
            </div>

            <div class="button-group">
                <a href="{{ $confirmationLink }}" class="button">Confirmar Cita</a>
                <a href="{{ $cancelLink }}" class="button-cancel"
                   onclick="return confirm('¿Seguro que deseas cancelar tu cita?');">
                    Cancelar Cita
                </a>
            </div>

            <p>Enlace para confirmar:</p>
            <p style="word-break: break-all; background-color: #f5f5f5; padding: 10px; border-radius: 4px;">
                {{ $confirmationLink }}
            </p>

            <p>¿Necesitas cancelar? Usa este enlace:</p>
            <p style="word-break: break-all; background-color: #f5f5f5; padding: 10px; border-radius: 4px; color: #dc3545;">
                {{ $cancelLink }}
            </p>

            <p style="margin-top: 20px;">Si tienes preguntas, no dudes en contactarnos.</p>

            <div class="footer">
                <p>{{ config('app.name') }}</p>
                <p>Este es un email automático. Por favor no respondas a este correo.</p>
            </div>
        </div>
    </div>
</body>
</html>
