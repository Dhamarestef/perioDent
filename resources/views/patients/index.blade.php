<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pacientes</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background-color: #f5f5f5; }
        .navbar { background-color: #667eea; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar a, .navbar button { color: white; text-decoration: none; padding: 8px 16px; border-radius: 5px; border: none; cursor: pointer; background: none; font-size: 14px; transition: background-color 0.3s; }
        .navbar a:hover, .navbar button:hover { background-color: rgba(255,255,255,0.2); }
        .container { max-width: 1100px; margin: 40px auto; padding: 0 20px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        h2 { color: #333; font-size: 26px; }
        .btn-new { background-color: #667eea; color: white; padding: 10px 22px; border-radius: 5px; text-decoration: none; font-weight: bold; transition: background-color 0.3s; }
        .btn-new:hover { background-color: #764ba2; }
        .success { background-color: #d4edda; color: #155724; padding: 14px; border-radius: 5px; margin-bottom: 20px; }
        .empty { background: white; padding: 40px; border-radius: 10px; text-align: center; color: #999; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
        th { background-color: #667eea; color: white; padding: 12px 15px; text-align: left; font-size: 13px; }
        td { padding: 12px 15px; border-bottom: 1px solid #f0f0f0; font-size: 14px; color: #333; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background-color: #f9f9f9; }
        .badge-f { background-color: #fce4ec; color: #880e4f; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .badge-m { background-color: #e3f2fd; color: #0d47a1; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .actions { display: flex; gap: 8px; }
        .btn-sm { padding: 5px 12px; border-radius: 5px; text-decoration: none; font-size: 12px; font-weight: bold; border: none; cursor: pointer; }
        .btn-view { background-color: #667eea; color: white; }
        .btn-edit { background-color: #17a2b8; color: white; }
        .btn-del  { background-color: #dc3545; color: white; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Pacientes</h1>
        <div>
            <a href="{{ route('dashboard') }}">← Dashboard</a>
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

        <div class="header">
            <h2>👥 Lista de Pacientes</h2>
            <a href="{{ route('patients.create') }}" class="btn-new">+ Nuevo Paciente</a>
        </div>

        @if ($patients->isEmpty())
            <div class="empty">
                <h3>📭 No hay pacientes registrados</h3>
                <p>Haz clic en "Nuevo Paciente" para agregar el primero.</p>
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>N° Expediente</th>
                        <th>Nombre</th>
                        <th>Edad</th>
                        <th>Sexo</th>
                        <th>Teléfono</th>
                        <th>Fecha Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($patients as $patient)
                        <tr>
                            <td><strong>{{ $patient->numero_expediente }}</strong></td>
                            <td>{{ $patient->nombre }}</td>
                            <td>{{ $patient->edad }}</td>
                            <td>
                                <span class="{{ $patient->sexo === 'F' ? 'badge-f' : 'badge-m' }}">
                                    {{ $patient->sexo === 'F' ? 'Femenino' : 'Masculino' }}
                                </span>
                            </td>
                            <td>{{ $patient->telefono ?? '—' }}</td>
                            <td>{{ $patient->fecha_registro->format('d/m/Y') }}</td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('patients.show', $patient->id) }}" class="btn-sm btn-view">Ver</a>
                                    <a href="{{ route('patients.edit', $patient->id) }}" class="btn-sm btn-edit">Editar</a>
                                    <form action="{{ route('patients.destroy', $patient->id) }}" method="POST" onsubmit="return confirm('¿Eliminar este paciente?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-sm btn-del">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</body>
</html>
