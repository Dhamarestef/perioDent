<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
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
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }

        .errors {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .errors ul {
            list-style-position: inside;
            margin: 0;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-row.full {
            grid-template-columns: 1fr;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        textarea,
        input[type="file"] {
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            font-family: Arial, sans-serif;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="tel"]:focus,
        textarea:focus,
        input[type="file"]:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.3);
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        .error-message {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
        }

        .avatar-section {
            margin-bottom: 30px;
            padding-bottom: 30px;
            border-bottom: 1px solid #eee;
        }

        .avatar-preview {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
        }

        .avatar-image {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #667eea;
            background-color: #f0f0f0;
        }

        .avatar-placeholder {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            border: 3px solid #ddd;
        }

        .avatar-info h3 {
            color: #333;
            margin-bottom: 5px;
        }

        .avatar-info p {
            color: #666;
            font-size: 13px;
        }

        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
        }

        .file-input-wrapper input[type="file"] {
            position: absolute;
            left: -9999px;
        }

        .file-label {
            display: inline-block;
            padding: 10px 20px;
            background-color: #667eea;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-size: 14px;
            font-weight: bold;
        }

        .file-label:hover {
            background-color: #764ba2;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }

        button {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-save {
            background-color: #667eea;
            color: white;
        }

        .btn-save:hover {
            background-color: #764ba2;
        }

        .btn-cancel {
            background-color: #6c757d;
            color: white;
        }

        .btn-cancel:hover {
            background-color: #5a6268;
        }

        .info-box {
            background-color: #e7f3ff;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            color: #004085;
            font-size: 13px;
            line-height: 1.5;
        }

        @media (max-width: 600px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .avatar-preview {
                flex-direction: column;
                align-items: flex-start;
            }

            .button-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Mi Perfil</h1>
        <div class="navbar-links">
            <a href="{{ route('dashboard') }}">← Volver al Dashboard</a>
            <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit">Cerrar Sesión</button>
            </form>
        </div>
    </div>

    <div class="container">
        <div class="card">
            <h2>✏️ Editar Perfil</h2>

            @if ($errors->any())
                <div class="errors">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="success">
                    ✓ {{ session('success') }}
                </div>
            @endif

            <div class="info-box">
                📝 Actualiza tu información personal. Todos los cambios se guardarán de forma segura.
            </div>

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- AVATAR -->
                <div class="avatar-section">
                    <h3 style="color: #333; margin-bottom: 15px;">Foto de Perfil</h3>

                    <div class="avatar-preview">
                        @if ($user->avatar)
                            <img src="{{ asset($user->avatar) }}" alt="Avatar" class="avatar-image">
                        @else
                            <div class="avatar-placeholder">👤</div>
                        @endif

                        <div class="avatar-info">
                            <h3>{{ $user->name }}</h3>
                            <p>Haz click para cambiar tu foto (máximo 2MB)</p>
                            <div class="file-input-wrapper">
                                <input type="file" id="avatar" name="avatar" accept="image/*">
                                <label for="avatar" class="file-label">Seleccionar Imagen</label>
                            </div>
                            @error('avatar')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- NOMBRE -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Nombre Completo *</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            required
                        >
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email', $user->email) }}"
                            required
                        >
                        @error('email')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- TELÉFONO -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">Teléfono</label>
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            value="{{ old('phone', $user->phone) }}"
                            placeholder="+34 123 456 789"
                        >
                        @error('phone')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- BIOGRAFÍA -->
                <div class="form-row full">
                    <div class="form-group">
                        <label for="bio">Biografía</label>
                        <textarea
                            id="bio"
                            name="bio"
                            placeholder="Cuéntanos un poco sobre ti..."
                        >{{ old('bio', $user->bio) }}</textarea>
                        <small style="color: #999; margin-top: 5px;">Máximo 500 caracteres</small>
                        @error('bio')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- BOTONES -->
                <div class="button-group">
                    <button type="submit" class="btn-save">💾 Guardar Cambios</button>
                    <a href="{{ route('dashboard') }}" class="btn-cancel" style="text-align: center; text-decoration: none; display: flex; align-items: center; justify-content: center;">← Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
