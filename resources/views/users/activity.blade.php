<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actividad de recepcionistas — PerioDent</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5; }

        .navbar { background-color: #667eea; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .navbar h1 { font-size: 22px; }
        .user-info { display: flex; align-items: center; gap: 15px; font-size: 14px; }
        .logout-btn { background-color: rgba(255,255,255,0.2); color: white; padding: 8px 16px; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; transition: background-color 0.3s; }
        .logout-btn:hover { background-color: rgba(255,255,255,0.35); }

        .container { max-width: 1100px; margin: 40px auto; padding: 0 20px; }

        .header-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
        .header-row h2 { color: #333; font-size: 22px; }

        .btn { display: inline-block; padding: 9px 16px; border-radius: 6px; border: none; cursor: pointer; font-size: 14px; text-decoration: none; transition: opacity 0.2s; }
        .btn:hover { opacity: 0.88; }
        .btn-primary   { background-color: #667eea; color: white; }
        .btn-secondary { background-color: #e9ecef; color: #333; }

        .card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.07); }

        .filters { display: flex; gap: 14px; flex-wrap: wrap; align-items: end; margin-bottom: 20px; }
        .filters label { display: block; font-size: 13px; color: #555; margin-bottom: 6px; }
        .form-control { padding: 8px 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; font-family: inherit; }

        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #f8f9fa; text-align: left; }
        th { padding: 12px; font-size: 13px; color: #666; font-weight: 600; }
        td { padding: 12px; border-top: 1px solid #eee; font-size: 13px; color: #333; }

        .empty { text-align: center; color: #888; padding: 30px; }

        .pagination { margin-top: 20px; }
        .pagination a, .pagination span { display: inline-block; padding: 6px 12px; margin-right: 4px; border: 1px solid #ddd; border-radius: 6px; text-decoration: none; color: #333; font-size: 13px; }
        .pagination .active span { background: #667eea; color: white; border-color: #667eea; }
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
        <div class="header-row">
            <h2>Actividad de recepcionistas</h2>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">← Volver a recepcionistas</a>
        </div>

        <form method="GET" action="{{ route('users.activity') }}" class="card filters">
            <div>
                <label>Recepcionista</label>
                <select name="user_id" class="form-control">
                    <option value="">Todas</option>
                    @foreach ($receptionists as $r)
                        <option value="{{ $r->id }}" @selected(request('user_id') == $r->id)>{{ $r->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Desde</label>
                <input type="date" name="desde" value="{{ request('desde') }}" class="form-control">
            </div>
            <div>
                <label>Hasta</label>
                <input type="date" name="hasta" value="{{ request('hasta') }}" class="form-control">
            </div>
            <div style="display:flex;gap:8px;">
                <button type="submit" class="btn btn-primary">Filtrar</button>
                <a href="{{ route('users.activity') }}" class="btn btn-secondary">Limpiar</a>
            </div>
        </form>

        @if ($logs->isEmpty())
            <div class="card empty">Sin actividad registrada.</div>
        @else
            <div class="card" style="overflow-x:auto;padding:0;">
                <table>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Recepcionista</th>
                            <th>Acción</th>
                            <th>Detalle</th>
                            <th>IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logs as $log)
                            <tr>
                                <td>{{ $log->created_at?->format('d/m/Y H:i') }}</td>
                                <td>{{ $log->user->name ?? '—' }}</td>
                                <td>{{ $log->action }}</td>
                                <td style="color:#666;">{{ $log->description }}</td>
                                <td style="color:#999;font-size:12px;">{{ $log->ip_address }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="pagination">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</body>
</html>
