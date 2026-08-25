<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cita Cancelada</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #dc3545 0%, #a71d2a 100%); min-height: 100vh; display: flex; justify-content: center; align-items: center; padding: 20px; }
        .container { background: white; border-radius: 10px; padding: 50px 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); max-width: 500px; width: 100%; text-align: center; }
        .icon { font-size: 70px; margin-bottom: 20px; }
        h1 { color: #dc3545; font-size: 26px; margin-bottom: 10px; }
        .subtitle { color: #666; margin-bottom: 30px; font-size: 15px; line-height: 1.6; }
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
        <div class="icon">🗑️</div>

        @if ($alreadyCancelled)
            <h1>Esta cita ya estaba cancelada</h1>
            <p class="subtitle">Este enlace ya había sido usado anteriormente. Tu cita sigue cancelada.</p>
        @else
            <h1>Cita Cancelada</h1>
            <p class="subtitle">Tu cita ha sido cancelada correctamente.</p>
        @endif

        <div class="info-box">
            <div class="info-row">
                <span class="info-label">Paciente:</span>
                <span class="info-value">{{ $appointment->nombrePaciente() }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Fecha y hora:</span>
                <span class="info-value">{{ $appointment->appointment_date->format('d/m/Y H:i') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Duración:</span>
                <span class="info-value">{{ $appointment->duracionTexto() }}</span>
            </div>
        </div>

        <p class="closing-note">Si deseas agendar una nueva cita, por favor comunícate con el consultorio.</p>
    </div>
</body>
</html>
