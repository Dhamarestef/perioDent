<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Cita</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background-color: #f5f5f5; }
        .navbar { background-color: #667eea; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar a, .navbar button { color: white; text-decoration: none; padding: 8px 16px; border-radius: 5px; border: none; cursor: pointer; background: none; font-size: 14px; transition: background-color 0.3s; }
        .navbar a:hover, .navbar button:hover { background-color: rgba(255,255,255,0.2); }
        .container { max-width: 700px; margin: 40px auto; padding: 0 20px; }
        .card { background: white; border-radius: 10px; padding: 35px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { background-color: #d4edda; color: #155724; padding: 14px; border-radius: 5px; margin-bottom: 20px; }
        .errors { background-color: #f8d7da; color: #721c24; padding: 14px; border-radius: 5px; margin-bottom: 20px; }

        .cita-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 2px solid #667eea; }
        .cita-servicio { font-size: 22px; font-weight: bold; color: #333; margin-bottom: 5px; }
        .cita-fecha { color: #666; font-size: 15px; }

        .status-badge { display: inline-block; padding: 5px 16px; border-radius: 20px; font-size: 13px; font-weight: bold; }
        .status-pending   { background-color: #fff3cd; color: #856404; }
        .status-confirmed { background-color: #d4edda; color: #155724; }
        .status-cancelled { background-color: #f8d7da; color: #721c24; }

        .section-title { color: #667eea; font-size: 12px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin: 25px 0 12px; border-bottom: 1px solid #f0f0f0; padding-bottom: 6px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .info-item label { font-size: 11px; color: #999; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 3px; }
        .info-item span { font-size: 15px; color: #333; }
        .info-item.full { grid-column: 1 / -1; }

        .btn-group { display: flex; gap: 12px; margin-top: 30px; flex-wrap: wrap; }
        .btn { padding: 10px 22px; border-radius: 5px; text-decoration: none; font-weight: bold; font-size: 14px; border: none; cursor: pointer; transition: all 0.3s; }
        .btn-edit   { background-color: #17a2b8; color: white; }
        .btn-edit:hover { background-color: #138496; }
        .btn-back   { background-color: #6c757d; color: white; }
        .btn-back:hover { background-color: #5a6268; }
        .btn-cancel { background-color: #dc3545; color: white; }
        .btn-cancel:hover { background-color: #c82333; }

        .confirmed-box { background-color: #d4edda; border-left: 4px solid #28a745; padding: 14px 18px; border-radius: 4px; margin-top: 20px; color: #155724; font-size: 14px; }
        .pending-box   { background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 14px 18px; border-radius: 4px; margin-top: 20px; color: #856404; font-size: 14px; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Detalle de Cita</h1>
        <div>
            <a href="{{ route('appointment.index') }}">← Mis Citas</a>
            <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit">Cerrar sesión</button>
            </form>
        </div>
    </div>

    <div class="container">
        @if (session('success'))
            <div class="success">✓ {{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="errors">{{ $errors->first() }}</div>
        @endif

        <div class="card">
            <div class="cita-header">
                <div>
                    <div class="cita-servicio">{{ $appointment->service->name }}</div>
                    <div class="cita-fecha">📅 {{ $appointment->appointment_date->format('d/m/Y\TH:i') }}</div>
                </div>
                <div>
                    @if ($appointment->isPending())
                        <span class="status-badge status-pending">⏳ Pendiente de confirmación</span>
                    @elseif ($appointment->isConfirmed())
                        <span class="status-badge status-confirmed">✓ Confirmada</span>
                    @else
                        <span class="status-badge status-cancelled">✕ Cancelada</span>
                    @endif
                </div>
            </div>

            <div class="section-title">Información del servicio</div>
            <div class="info-grid">
                <div class="info-item">
                    <label>Servicio</label>
                    <span>{{ $appointment->service->name }}</span>
                </div>
                <div class="info-item">
                    <label>Duración</label>
                    <span>{{ $appointment->service->duration }} minutos</span>
                </div>
                <div class="info-item">
                    <label>Precio</label>
                    <span>${{ number_format($appointment->service->price, 2) }}</span>
                </div>
                <div class="info-item">
                    <label>Fecha y hora</label>
                    <span>{{ $appointment->appointment_date->format('d/m/Y\TH:i') }}</span>
                </div>
                @if ($appointment->notes)
                    <div class="info-item full">
                        <label>Notas</label>
                        <span>{{ $appointment->notes }}</span>
                    </div>
                @endif
            </div>

            @if ($appointment->isConfirmed())
                <div class="confirmed-box">
                    ✓ Cita confirmada el {{ $appointment->confirmed_at->format('d/m/Y\TH:i') }}
                </div>
            @elseif ($appointment->isPending())
                <div class="pending-box">
                    ⏳ Revisa tu correo electrónico y haz clic en el enlace de confirmación para confirmar esta cita.
                </div>
            @endif

            <div class="section-title">Estado del recordatorio por email</div>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Estado de envío</label>
                        <span style="color: {{ $appointment->emailEstadoColor() }}; font-weight: bold;">
                            {{ $appointment->emailEstadoLabel() }}
                        </span>
                    </div>
                    @if ($appointment->email_reminder_sent_at)
                        <div class="info-item">
                            <label>Enviado el</label>
                            <span>{{ $appointment->email_reminder_sent_at->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                </div>

            <div class="btn-group">
                @if ($appointment->isPending())
                    <a href="{{ route('appointment.edit', $appointment->id) }}" class="btn btn-edit">✏️ Editar</a>
                    <form action="{{ route('appointment.cancel', $appointment->id) }}" method="POST"
                          onsubmit="return confirm('¿Cancelar esta cita?')">
                        @csrf
                        <button type="submit" class="btn btn-cancel">✕ Cancelar cita</button>
                    </form>
                @endif
                <a href="{{ route('appointment.index') }}" class="btn btn-back">← Volver</a>
            </div>
        </div>
    </div>
</body>
</html>
