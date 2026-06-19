<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-name {
            font-size: 14px;
        }

        .logout-btn {
            background-color: #764ba2;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .logout-btn:hover {
            background-color: #5a3a7f;
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .welcome-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .welcome-card h2 {
            color: #667eea;
            margin-bottom: 15px;
        }

        .welcome-card p {
            color: #666;
            line-height: 1.6;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        }

        .card-icon {
            font-size: 40px;
            margin-bottom: 15px;
        }

        .card h3 {
            color: #333;
            margin-bottom: 10px;
        }

        .card p {
            color: #666;
            font-size: 14px;
        }

        .alert {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }

        a[href*="profile"] {
            text-decoration: none;
            color: inherit;
        }

        a[href*="profile"] .card {
            cursor: pointer;
        }

        a[href*="profile"] .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Dashboard</h1>
        <div class="user-info">
            <span class="user-name">👤 {{ Auth::user()->name }}</span>
            <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="logout-btn">Cerrar Sesión</button>
            </form>
        </div>
    </div>

    <div class="container">
        @if ($message = Session::get('success'))
            <div class="alert">
                {{ $message }}
            </div>
        @endif

        <div class="welcome-card">
            <h2>Bienvenido, {{ Auth::user()->name }}! 👋</h2>
            <p>
                Has iniciado sesión correctamente. Este es tu panel de control donde puedes acceder a todas las funcionalidades de la aplicación.
            </p>
        </div>

        <div class="cards-grid">
            <a href="{{ route('profile.edit') }}" style="text-decoration: none;">
                <div class="card">
                    <div class="card-icon">👤</div>
                    <h3>Mi Perfil</h3>
                    <p>Ver y editar tu información personal</p>
                </div>
            </a>

            <div class="card">
                <div class="card-icon">🔑</div>
                <h3>Seguridad</h3>
                <p>Cambiar contraseña y opciones de seguridad</p>
            </div>

            <div class="card">
                <div class="card-icon">⚙️</div>
                <h3>Configuración</h3>
                <p>Personaliza tu experiencia en la app</p>
            </div>

            <a href="{{ route('appointment.index') }}" style="text-decoration: none;">
                <div class="card">
                    <div class="card-icon">📅</div>
                    <h3>Mis Citas</h3>
                    <p>Ver y agendar citas</p>
                </div>
            </a>
        </div>
    </div>
</body>
</html>
