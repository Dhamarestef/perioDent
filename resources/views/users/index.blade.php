<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recepcionistas — PerioDent</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5; }

        .navbar { background-color: #667eea; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .navbar h1 { font-size: 22px; }
        .user-info { display: flex; align-items: center; gap: 15px; font-size: 14px; }
        .logout-btn { background-color: rgba(255,255,255,0.2); color: white; padding: 8px 16px; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; transition: background-color 0.3s; }
        .logout-btn:hover { background-color: rgba(255,255,255,0.35); }

        .container { max-width: 1100px; margin: 40px auto; padding: 0 20px; }

        .alert { padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger  { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        .header-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
        .header-row h2 { color: #333; font-size: 22px; }

        .btn { display: inline-block; padding: 9px 16px; border-radius: 6px; border: none; cursor: pointer; font-size: 14px; text-decoration: none; transition: opacity 0.2s; }
        .btn:hover { opacity: 0.88; }
        .btn-primary   { background-color: #667eea; color: white; }
        .btn-secondary { background-color: #e9ecef; color: #333; }
        .btn-success   { background-color: #28a745; color: white; }
        .btn-warning   { background-color: #f0a500; color: white; }
        .btn-sm        { padding: 6px 12px; font-size: 13px; }

        .card { background: white; padding: 24px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.07); }

        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #f8f9fa; text-align: left; }
        th { padding: 12px; font-size: 13px; color: #666; font-weight: 600; }
        td { padding: 12px; border-top: 1px solid #eee; font-size: 14px; color: #333; vertical-align: middle; }

        .badge { padding: 3px 10px; border-radius: 12px; font-size: 12px; }
        .badge-active   { background: #d4edda; color: #155724; }
        .badge-inactive { background: #f8d7da; color: #721c24; }

        .empty { text-align: center; color: #888; padding: 30px; }
    </style>
</head>
<body>
    @php($user = Auth::user())
    <div class="navbar">
        <h1>🦷 PerioDent</h1>
        <div class="user-info">
            <span>👤 {{ $user->name }} @if($user->isDoctor()) · Doctor @else · Recepción @endif</span>
            <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="logout-btn">Cerrar sesión</button>
            </form>
        </div>
    </div>

    <div class="container">

        @if (session('success'))
            <div class="alert alert-success">✓ {{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $e) <div>{{ $e }}</div> @endforeach
            </div>
        @endif

        <div class="header-row">
            <h2>Recepcionistas</h2>
            <div style="display:flex;gap:10px;">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">← Dashboard</a>
                <a href="{{ route('users.activity') }}" class="btn btn-secondary">Ver actividad</a>
                <a href="{{ route('users.create') }}" class="btn btn-primary">+ Nueva recepcionista</a>
            </div>
        </div>

        @if ($receptionists->isEmpty())
            <div class="card empty">Aún no hay recepcionistas registradas.</div>
        @else
            <div class="card" style="overflow-x:auto;padding:0;">
                <table>
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Teléfono</th>
                            <th>Estado</th>
                            <th>Creada</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($receptionists as $r)
                            <tr>
                                <td>{{ $r->name }}</td>
                                <td>{{ $r->email }}</td>
                                <td>{{ $r->phone ?? '—' }}</td>
                                <td>
                                    @if ($r->is_active)
                                        <span class="badge badge-active">Activa</span>
                                    @else
                                        <span class="badge badge-inactive">Desactivada</span>
                                    @endif
                                </td>
                                <td style="font-size:13px;color:#666;">{{ $r->created_at?->format('d/m/Y') }}</td>
                                <td>
                                    <a href="{{ route('users.edit', $r->id) }}" class="btn btn-secondary btn-sm">Editar</a>
                                    <form action="{{ route('users.toggle', $r->id) }}" method="POST" style="display:inline;">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-sm {{ $r->is_active ? 'btn-warning' : 'btn-success' }}">
                                            {{ $r->is_active ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</body>
</html>
