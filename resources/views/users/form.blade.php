<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $user ? 'Editar recepcionista' : 'Nueva recepcionista' }} — PerioDent</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5; }

        .navbar { background-color: #667eea; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .navbar h1 { font-size: 22px; }
        .user-info { display: flex; align-items: center; gap: 15px; font-size: 14px; }
        .logout-btn { background-color: rgba(255,255,255,0.2); color: white; padding: 8px 16px; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; transition: background-color 0.3s; }
        .logout-btn:hover { background-color: rgba(255,255,255,0.35); }

        .container { max-width: 900px; margin: 40px auto; padding: 0 20px; }

        .alert { padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
        .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        h2 { color: #333; font-size: 22px; margin-bottom: 20px; }

        .card { background: white; padding: 24px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.07); margin-bottom: 20px; }
        .card h3 { color: #667eea; font-size: 16px; margin-bottom: 16px; }

        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

        label { display: block; font-size: 13px; color: #555; margin-bottom: 6px; font-weight: 500; }
        .form-control { width: 100%; padding: 9px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; font-family: inherit; }
        .form-control:focus { outline: none; border-color: #667eea; box-shadow: 0 0 0 3px rgba(102,126,234,0.15); }

        .perm-group { margin-bottom: 18px; }
        .perm-group-title { font-weight: 600; color: #333; font-size: 14px; margin-bottom: 8px; }
        .perm-list { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
        .perm-item { display: flex; align-items: flex-start; gap: 8px; font-size: 13px; color: #555; cursor: pointer; }
        .perm-item input { margin-top: 3px; }

        .actions { display: flex; gap: 10px; }

        .btn { display: inline-block; padding: 9px 16px; border-radius: 6px; border: none; cursor: pointer; font-size: 14px; text-decoration: none; transition: opacity 0.2s; }
        .btn:hover { opacity: 0.88; }
        .btn-primary   { background-color: #667eea; color: white; }
        .btn-secondary { background-color: #e9ecef; color: #333; }
        .btn-warning   { background-color: #f0a500; color: white; }

        .hint { color: #888; font-size: 13px; margin-bottom: 15px; }

        @media (max-width: 700px) {
            .grid-2, .perm-list { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    @php($auth = Auth::user())
    <div class="navbar">
        <h1>🦷 PerioDent</h1>
        <div class="user-info">
            <span>👤 {{ $auth->name }} @if($auth->isDoctor()) · Doctor @else · Recepción @endif</span>
            <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="logout-btn">Cerrar sesión</button>
            </form>
        </div>
    </div>

    <div class="container">
        <h2>{{ $user ? 'Editar recepcionista: ' . $user->name : 'Nueva recepcionista' }}</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $e) <div>{{ $e }}</div> @endforeach
            </div>
        @endif

        <form action="{{ $user ? route('users.update', $user->id) : route('users.store') }}" method="POST">
            @csrf
            @if ($user) @method('PUT') @endif

            <div class="card">
                <h3>Datos de la cuenta</h3>
                <div class="grid-2">
                    <div>
                        <label>Nombre *</label>
                        <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required class="form-control">
                    </div>
                    <div>
                        <label>Correo electrónico *</label>
                        <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required class="form-control">
                    </div>
                    <div>
                        <label>Teléfono</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone ?? '') }}" class="form-control">
                    </div>
                    @unless ($user)
                        <div>
                            <label>Contraseña inicial *</label>
                            <input type="password" name="password" required minlength="8" class="form-control">
                        </div>
                        <div>
                            <label>Confirmar contraseña *</label>
                            <input type="password" name="password_confirmation" required minlength="8" class="form-control">
                        </div>
                    @endunless
                </div>
            </div>

            <div class="card">
                <h3>Permisos</h3>
                <p class="hint">Marca las funciones que podrá usar esta recepcionista. El doctor siempre conserva todos los permisos.</p>

                @foreach ($catalog as $grupo => $permisos)
                    <div class="perm-group">
                        <div class="perm-group-title">{{ $grupo }}</div>
                        <div class="perm-list">
                            @foreach ($permisos as $clave => $etiqueta)
                                <label class="perm-item">
                                    <input type="checkbox" name="permissions[]" value="{{ $clave }}"
                                           @checked(in_array($clave, old('permissions', $selected ?? []), true))>
                                    <span>{{ $etiqueta }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary">
                    {{ $user ? 'Guardar cambios' : 'Crear recepcionista' }}
                </button>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>

        @if ($user)
            <div class="card" style="margin-top:20px;">
                <h3>Restablecer contraseña</h3>
                <form action="{{ route('users.password', $user->id) }}" method="POST">
                    @csrf @method('PUT')
                    <div style="display:grid;grid-template-columns:1fr 1fr auto;gap:16px;align-items:end;">
                        <div>
                            <label>Nueva contraseña</label>
                            <input type="password" name="password" required minlength="8" class="form-control">
                        </div>
                        <div>
                            <label>Confirmar</label>
                            <input type="password" name="password_confirmation" required minlength="8" class="form-control">
                        </div>
                        <div>
                            <button type="submit" class="btn btn-warning">Restablecer</button>
                        </div>
                    </div>
                </form>
            </div>
        @endif
    </div>
</body>
</html>
