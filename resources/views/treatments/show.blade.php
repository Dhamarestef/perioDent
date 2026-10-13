<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $treatment->abreviacion }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background-color: #f5f5f5; }
        .navbar { background-color: #667eea; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar a { color: white; text-decoration: none; padding: 8px 16px; border-radius: 5px; transition: background-color 0.3s; }
        .navbar a:hover { background-color: rgba(255,255,255,0.2); }
        .container { max-width: 600px; margin: 40px auto; padding: 0 20px; }
        .card { background: white; border-radius: 10px; padding: 35px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { background-color: #d4edda; color: #155724; padding: 14px; border-radius: 5px; margin-bottom: 20px; }
        .abrev-header { text-align: center; margin-bottom: 30px; padding-bottom: 25px; border-bottom: 2px solid #f0f0f0; }
        .abrev-code { font-size: 36px; font-weight: bold; color: #667eea; font-family: monospace; background-color: #eef0ff; display: inline-block; padding: 10px 24px; border-radius: 8px; margin-bottom: 12px; }
        .abrev-nombre { font-size: 18px; color: #333; font-weight: 500; }
        .info-group { margin-bottom: 20px; }
        .info-label { font-size: 11px; color: #999; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .info-value { font-size: 16px; color: #333; }
        .costo-grande { font-size: 28px; font-weight: bold; color: #28a745; }
        .costo-cotizar { font-size: 18px; color: #999; font-style: italic; }
        .cat-badge { display: inline-block; padding: 5px 16px; border-radius: 20px; font-size: 13px; font-weight: bold; background-color: #eef0ff; color: #4a4db5; }
        .btn-group { display: flex; gap: 12px; margin-top: 30px; }
        .btn { padding: 10px 22px; border-radius: 5px; text-decoration: none; font-weight: bold; font-size: 14px; border: none; cursor: pointer; transition: all 0.3s; }
        .btn-edit   { background-color: #17a2b8; color: white; }
        .btn-edit:hover { background-color: #138496; }
        .btn-back   { background-color: #6c757d; color: white; }
        .btn-back:hover { background-color: #5a6268; }
        .btn-del    { background-color: #dc3545; color: white; }
        .btn-del:hover { background-color: #c82333; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Detalle de Tratamiento</h1>
        <a href="{{ route('treatments.index') }}">← Tratamientos</a>
    </div>

    <div class="container">
        @if (session('success'))
            <div class="success">✓ {{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="abrev-header">
                <div class="abrev-code">{{ $treatment->abreviacion }}</div>
                <div class="abrev-nombre">{{ $treatment->nombre }}</div>
            </div>

            <div class="info-group">
                <div class="info-label">Costo</div>
                @if ($treatment->costo)
                    <div class="costo-grande">${{ number_format($treatment->costo, 2) }}</div>
                @else
                    <div class="costo-cotizar">Por cotizar</div>
                @endif
            </div>

            <div class="info-group">
                <div class="info-label">Categoría</div>
                <span class="cat-badge">{{ $treatment->categoria }}</span>
            </div>

            <div class="info-group">
                <div class="info-label">Registrado el</div>
                <div class="info-value">{{ $treatment->created_at->format('d/m/Y') }}</div>
            </div>

            <div class="btn-group">
                <a href="{{ route('treatments.edit', $treatment->id) }}" class="btn btn-edit">✏️ Editar</a>
                <a href="{{ route('treatments.index') }}" class="btn btn-back">← Volver</a>
                <form action="{{ route('treatments.destroy', $treatment->id) }}" method="POST"
                      onsubmit="return confirm('¿Ocultar este tratamiento?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-del">🙈 Ocultar</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
