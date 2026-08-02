<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cita Confirmada</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; justify-content: center; align-items: center; padding: 20px; }
        .container { background: white; border-radius: 10px; padding: 50px 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); max-width: 500px; width: 100%; text-align: center; }
        .icon { font-size: 70px; margin-bottom: 20px; }
        h1 { color: #28a745; font-size: 28px; margin-bottom: 10px; }
        .subtitle { color: #666; margin-bottom: 30px; font-size: 15px; }
        .info-box { background-color: #f9f9f9; border-radius: 8px; padding: 20px; margin-bottom: 30px; text-align: left; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px; }
        .info-row:last-child { margin-bottom: 0; }
        .info-label { color: #666; }
        .info-value { font-weight: bold; color: #333; }
        .closing-note { color: #999; font-size: 13px; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">✅</div>
        <h1>¡Cita Confirmada!</h1>
        <p class="subtitle">Tu cita ha sido confirmada exitosamente.</p>

        <div class="info-box">
            <div class="info-row">
                <span class="info-label">Paciente:</span>
                <span class="info-value">{{ $appointment->nombrePaciente() }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Servicio:</span>
                <span class="info-value">{{ $appointment->service->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Fecha y hora:</span>
                <span class="info-value">{{ $appointment->appointment_date->format('d/m/Y H:i') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Duración:</span>
                <span class="info-value">{{ $appointment->service->duration }} minutos</span>
            </div>
            @if ($appointment->service->price > 0)
                <div class="info-row">
                    <span class="info-label">Precio:</span>
                    <span class="info-value">${{ number_format($appointment->service->price, 2) }}</span>
                </div>
            @endif
        </div>

        <p class="closing-note">Ya puedes cerrar esta ventana. Te esperamos en tu cita.</p>
    </div>
</body>
</html>
