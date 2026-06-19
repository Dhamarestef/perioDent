<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background-color: rgba(0, 0, 0, 0.1);
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h1 {
            font-size: 24px;
        }

        .navbar-links {
            display: flex;
            gap: 15px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .navbar a:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .navbar .btn-primary {
            background-color: white;
            color: #667eea;
            font-weight: bold;
        }

        .navbar .btn-primary:hover {
            background-color: #f0f0f0;
        }

        .container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        .content {
            text-align: center;
            color: white;
        }

        .content h1 {
            font-size: 48px;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .content p {
            font-size: 20px;
            margin-bottom: 30px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        .button-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 30px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s;
            border: 2px solid white;
            font-size: 16px;
        }

        .btn-login {
            background-color: white;
            color: #667eea;
        }

        .btn-login:hover {
            background-color: transparent;
            color: white;
        }

        .btn-register {
            background-color: transparent;
            color: white;
        }

        .btn-register:hover {
            background-color: white;
            color: #667eea;
        }

        @media (max-width: 768px) {
            .content h1 {
                font-size: 36px;
            }

            .content p {
                font-size: 16px;
            }

            .navbar {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>🚀 Mi App</h1>
        <div class="navbar-links">
            @auth
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" style="background: none; border: none; color: white; cursor: pointer; padding: 8px 16px; border-radius: 5px;" onmouseover="this.style.backgroundColor='rgba(255,255,255,0.2)'" onmouseout="this.style.backgroundColor='transparent'">Cerrar Sesión</button>
                </form>
            @else
                <a href="{{ route('login') }}">Iniciar Sesión</a>
                <a href="{{ route('register') }}" class="btn-primary">Registrarse</a>
            @endauth
        </div>
    </div>

    <div class="container">
        <div class="content">
            <h1>Bienvenido a tu Aplicación</h1>
            <p>Una plataforma moderna y segura para tus proyectos</p>

            @guest
                <div class="button-group">
                    <a href="{{ route('login') }}" class="btn btn-login">Iniciar Sesión</a>
                    <a href="{{ route('register') }}" class="btn btn-register">Crear Cuenta</a>
                </div>
            @else
                <div class="button-group">
                    <a href="{{ route('dashboard') }}" class="btn btn-login">Ir al Dashboard</a>
                </div>
            @endauth
        </div>
    </div>
</body>
</html>
