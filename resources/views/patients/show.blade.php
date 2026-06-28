<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expediente {{ $patient->numero_expediente }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background-color: #f5f5f5; }
        .navbar { background-color: #667eea; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar a { color: white; text-decoration: none; padding: 8px 16px; border-radius: 5px; transition: background-color 0.3s; }
        .navbar a:hover { background-color: rgba(255,255,255,0.2); }
        .container { max-width: 800px; margin: 40px auto; padding: 0 20px; }
        .card { background: white; border-radius: 10px; padding: 35px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .exp-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 2px solid #667eea; }
        .exp-numero { font-size: 22px; font-weight: bold; color: #667eea; }
        .exp-fecha { color: #666; font-size: 14px; margin-top: 4px; }
        .exp-nombre { font-size: 24px; font-weight: bold; color: #333; margin-bottom: 5px; }
        .badge { display: inline-block; padding: 4px 14px; border-radius: 20px; font-size: 13px; font-weight: bold; }
        .badge-f { background-color: #fce4ec; color: #880e4f; }
        .badge-m { background-color: #e3f2fd; color: #0d47a1; }
        .section-title { color: #667eea; font-size: 12px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin: 25px 0 12px; border-bottom: 1px solid #f0f0f0; padding-bottom: 6px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .info-item label { font-size: 11px; color: #999; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 3px; }
        .info-item span { font-size: 15px; color: #333; }
        .info-item.full { grid-column: 1 / -1; }
        .success { background-color: #d4edda; color: #155724; padding: 14px; border-radius: 5px; margin-bottom: 20px; }
        .btn-group { display: flex; gap: 12px; margin-top: 30px; }
        .btn { padding: 10px 22px; border-radius: 5px; text-decoration: none; font-weight: bold; font-size: 14px; border: none; cursor: pointer; transition: all 0.3s; }
        .btn-edit { background-color: #17a2b8; color: white; }
        .btn-edit:hover { background-color: #138496; }
        .btn-back { background-color: #6c757d; color: white; }
        .btn-back:hover { background-color: #5a6268; }
        .btn-del { background-color: #dc3545; color: white; }
        .btn-del:hover { background-color: #c82333; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Expediente</h1>
        <a href="{{ route('patients.index') }}">← Pacientes</a>
    </div>

    <div class="container">
        @if (session('success'))
            <div class="success">✓ {{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="exp-header">
                <div>
                    <div class="exp-nombre">{{ $patient->nombre }}</div>
                    <div class="exp-fecha">Registro: {{ $patient->fecha_registro->format('d/m/Y') }}</div>
                </div>
                <div style="text-align: right;">
                    <div class="exp-numero">{{ $patient->numero_expediente }}</div>
                    <span class="badge {{ $patient->sexo === 'F' ? 'badge-f' : 'badge-m' }}">
                        {{ $patient->sexo === 'F' ? 'Femenino' : 'Masculino' }}
                    </span>
                </div>
            </div>

            <div class="section-title">Datos personales</div>
            <div class="info-grid">
                <div class="info-item">
                    <label>Edad</label>
                    <span>{{ $patient->edad }} años</span>
                </div>
                <div class="info-item">
                    <label>Fecha de nacimiento</label>
                    <span>{{ $patient->fecha_nacimiento->format('d/m/Y') }}</span>
                </div>
                <div class="info-item">
                    <label>Escolaridad</label>
                    <span>{{ $patient->escolaridad }}</span>
                </div>
                <div class="info-item">
                    <label>Estado civil</label>
                    <span>{{ $patient->estado_civil }}</span>
                </div>
            </div>

            <div class="section-title">Ubicación y contacto</div>
            <div class="info-grid">
                <div class="info-item">
                    <label>Lugar de nacimiento</label>
                    <span>{{ $patient->lugar_nacimiento }}</span>
                </div>
                <div class="info-item">
                    <label>Lugar de residencia</label>
                    <span>{{ $patient->lugar_residencia }}</span>
                </div>
                <div class="info-item full">
                    <label>Domicilio actual</label>
                    <span>{{ $patient->domicilio_actual }}</span>
                </div>
                <div class="info-item">
                    <label>Teléfono</label>
                    <span>{{ $patient->telefono ?? '—' }}</span>
                </div>
                <div class="info-item">
                    <label>Correo electrónico</label>
                    <span>{{ $patient->correo ?? '—' }}</span>
                </div>
            </div>

            <div class="btn-group">
                <a href="{{ route('patients.edit', $patient->id) }}" class="btn btn-edit">✏️ Editar</a>
                <a href="{{ route('patients.index') }}" class="btn btn-back">← Volver</a>
                <form action="{{ route('patients.destroy', $patient->id) }}" method="POST"
                      onsubmit="return confirm('¿Eliminar este paciente? Esta acción no se puede deshacer.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-del">🗑 Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
