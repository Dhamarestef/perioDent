<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — PerioDent</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5; }

        .navbar { background-color: #667eea; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .navbar h1 { font-size: 22px; }
        .user-info { display: flex; align-items: center; gap: 15px; font-size: 14px; }
        .logout-btn { background-color: rgba(255,255,255,0.2); color: white; padding: 8px 16px; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; transition: background-color 0.3s; }
        .logout-btn:hover { background-color: rgba(255,255,255,0.35); }

        .container { max-width: 1100px; margin: 40px auto; padding: 0 20px; }

        .alert { background-color: #d4edda; color: #155724; padding: 14px 18px; border-radius: 8px; margin-bottom: 24px; border: 1px solid #c3e6cb; }

        .welcome-card { background: white; padding: 28px 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.07); margin-bottom: 30px; border-left: 4px solid #667eea; }
        .welcome-card h2 { color: #667eea; margin-bottom: 8px; font-size: 22px; }
        .welcome-card p  { color: #666; font-size: 14px; line-height: 1.6; }

        .section-title { font-size: 12px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #999; margin-bottom: 16px; }

        .cards-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 18px; margin-bottom: 40px; }

        .card-link { text-decoration: none; }
        .card { background: white; padding: 26px 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.07); text-align: center; transition: transform 0.25s, box-shadow 0.25s; cursor: pointer; border-bottom: 3px solid transparent; }
        .card:hover { transform: translateY(-5px); box-shadow: 0 8px 24px rgba(0,0,0,0.13); border-bottom-color: #667eea; }
        .card-icon { font-size: 38px; margin-bottom: 12px; }
        .card h3 { color: #333; margin-bottom: 6px; font-size: 15px; }
        .card p  { color: #888; font-size: 12px; line-height: 1.4; }

        @media (max-width: 600px) {
            .cards-grid { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>🦷 PerioDent</h1>
        <div class="user-info">
            <span>👤 {{ Auth::user()->name }}</span>
            <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="logout-btn">Cerrar sesión</button>
            </form>
        </div>
    </div>

    <div class="container">

        @if (session('success'))
            <div class="alert">✓ {{ session('success') }}</div>
        @endif

        <div class="welcome-card">
            <h2>Bienvenido, {{ Auth::user()->name }} 👋</h2>
            <p>Panel de control de PerioDent. Selecciona un módulo para comenzar.</p>
        </div>

        <div class="section-title">Módulos principales</div>
        <div class="cards-grid">
            <a href="{{ route('patients.index') }}" class="card-link">
                <div class="card">
                    <div class="card-icon">👥</div>
                    <h3>Pacientes</h3>
                    <p>Gestionar expedientes de pacientes</p>
                </div>
            </a>

            <a href="{{ route('appointment.index') }}" class="card-link">
                <div class="card">
                    <div class="card-icon">📅</div>
                    <h3>Citas</h3>
                    <p>Agendar y gestionar citas</p>
                </div>
            </a>

            <a href="{{ route('treatments.index') }}" class="card-link">
                <div class="card">
                    <div class="card-icon">🦷</div>
                    <h3>Tratamientos</h3>
                    <p>Catálogo de tratamientos y costos</p>
                </div>
            </a>

            <a href="{{ route('medications.index') }}" class="card-link">
                <div class="card">
                    <div class="card-icon">💊</div>
                    <h3>Medicamentos</h3>
                    <p>Catálogo de medicamentos e indicaciones</p>
                </div>
            </a>

            <a href="{{ route('papelera.index') }}" class="card-link">
                <div class="card">
                    <div class="card-icon">🗑️</div>
                    <h3>Papelera</h3>
                    <p>Pacientes, presupuestos, recetas, tratamientos y medicamentos ocultados</p>
                </div>
            </a>

            <a href="{{ route('profile.edit') }}" class="card-link">
                <div class="card">
                    <div class="card-icon">👤</div>
                    <h3>Mi Perfil</h3>
                    <p>Editar información personal</p>
                </div>
            </a>
        </div>
    </div>
</body>
</html>
