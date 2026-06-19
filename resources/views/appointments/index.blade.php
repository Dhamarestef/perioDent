<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Citas</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
        }

        .navbar {
            background-color: #667eea;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar h1 {
            font-size: 24px;
        }

        .navbar-links {
            display: flex;
            gap: 15px;
        }

        .navbar a, .navbar button {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 5px;
            transition: background-color 0.3s;
            border: none;
            cursor: pointer;
            background: none;
            font-size: 14px;
        }

        .navbar a:hover, .navbar button:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        h2 {
            color: #333;
            font-size: 28px;
        }

        .btn-new {
            background-color: #667eea;
            color: white;
            padding: 12px 24px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .btn-new:hover {
            background-color: #764ba2;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }

        .empty {
            background: white;
            padding: 40px;
            border-radius: 10px;
            text-align: center;
            color: #999;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .empty h3 {
            color: #666;
            margin-bottom: 10px;
        }

        .appointments-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .appointment-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .appointment-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        }

        .service-name {
            color: #667eea;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .appointment-date {
            color: #333;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .appointment-price {
            color: #28a745;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-confirmed {
            background-color: #d4edda;
            color: #155724;
        }

        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }

        .card-actions {
            display: flex;
            gap: 10px;
        }

        .card-actions a, .card-actions button {
            flex: 1;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-view {
            background-color: #667eea;
            color: white;
        }

        .btn-view:hover {
            background-color: #764ba2;
        }

        .btn-edit {
            background-color: #17a2b8;
            color: white;
        }

        .btn-edit:hover {
            background-color: #138496;
        }

        .btn-cancel {
            background-color: #dc3545;
            color: white;
        }

        .btn-cancel:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Mis Citas</h1>
        <div class="navbar-links">
            <a href="{{ route('dashboard') }}">← Dashboard</a>
            <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit">Cerrar Sesión</button>
            </form>
        </div>
    </div>

    <div class="container">
        @if (session('success'))
            <div class="success">
                ✓ {{ session('success') }}
            </div>
        @endif

        <div class="header">
            <h2>📅 Mis Citas</h2>
            <a href="{{ route('appointment.create') }}" class="btn-new">+ Agendar Nueva Cita</a>
        </div>

        @if ($appointments->isEmpty())
            <div class="empty">
                <h3>📭 No tienes citas agendadas</h3>
                <p>Haz click en "Agendar Nueva Cita" para reservar una cita.</p>
            </div>
        @else
            <div class="appointments-grid">
                @foreach ($appointments as $appointment)
                    <div class="appointment-card">
                        <div class="service-name">{{ $appointment->service->name }}</div>

                        <div class="appointment-date">
                            📅 {{ $appointment->appointment_date->format('d/m/Y H:i') }}
                        </div>

                        <div class="appointment-price">
                            💰 ${{ number_format($appointment->service->price, 2) }}
                        </div>

                        <div>
                            @if ($appointment->isPending())
                                <span class="status-badge status-pending">⏳ Pendiente de Confirmación</span>
                            @elseif ($appointment->isConfirmed())
                                <span class="status-badge status-confirmed">✓ Confirmada</span>
                            @else
                                <span class="status-badge status-cancelled">✕ Cancelada</span>
                            @endif
                        </div>

                        <div class="card-actions">
                            <a href="{{ route('appointment.show', $appointment->id) }}" class="btn-view">Ver</a>
                            @if ($appointment->isPending())
                                <a href="{{ route('appointment.edit', $appointment->id) }}" class="btn-edit">Editar</a>
                                <form action="{{ route('appointment.cancel', $appointment->id) }}" method="POST" style="flex: 1;">
                                    @csrf
                                    <button type="submit" class="btn-cancel" onclick="return confirm('¿Cancelar esta cita?')">Cancelar</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</body>
</html>
