<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de {{ $user->name }}</title>
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

        .navbar-links a {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .navbar-links a:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .avatar-large {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #667eea;
            margin: 0 auto 20px;
            background-color: #f0f0f0;
        }

        .avatar-placeholder {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background-color: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 60px;
            border: 4px solid #ddd;
            margin: 0 auto 20px;
        }

        h2 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }

        .email {
            color: #667eea;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .info-group {
            margin: 20px 0;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .info-group:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #999;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .info-value {
            color: #333;
            font-size: 16px;
            margin-top: 5px;
        }

        .bio {
            color: #666;
            font-size: 14px;
            line-height: 1.6;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }

        a {
            flex: 1;
            padding: 12px;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
            transition: all 0.3s;
        }

        .btn-edit {
            background-color: #667eea;
            color: white;
        }

        .btn-edit:hover {
            background-color: #764ba2;
        }

        .btn-back {
            background-color: #6c757d;
            color: white;
        }

        .btn-back:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Perfil Público</h1>
        <div class="navbar-links">
            <a href="{{ route('dashboard') }}">← Volver</a>
        </div>
    </div>

    <div class="container">
        <div class="card">
            @if ($user->avatar)
                <img src="{{ asset($user->avatar) }}" alt="{{ $user->name }}" class="avatar-large">
            @else
                <div class="avatar-placeholder">👤</div>
            @endif

            <h2>{{ $user->name }}</h2>
            <p class="email">{{ $user->email }}</p>

            @if ($user->phone)
                <div class="info-group">
                    <div class="info-label">Teléfono</div>
                    <div class="info-value">{{ $user->phone }}</div>
                </div>
            @endif

            @if ($user->bio)
                <div class="info-group">
                    <div class="info-label">Acerca de</div>
                    <div class="info-value bio">{{ $user->bio }}</div>
                </div>
            @endif

            <div class="info-group">
                <div class="info-label">Miembro desde</div>
                <div class="info-value">{{ $user->created_at->format('d/m/Y') }}</div>
            </div>

            @if (auth()->check() && auth()->user()->id === $user->id)
                <div class="button-group">
                    <a href="{{ route('profile.edit') }}" class="btn-edit">✏️ Editar Mi Perfil</a>
                </div>
            @endif

            <div style="margin-top: 20px;">
                <a href="{{ route('dashboard') }}" class="btn-back">← Volver al Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>
