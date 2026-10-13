<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $medication->nombre }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background-color: #f5f5f5; }
        .navbar { background-color: #667eea; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar a { color: white; text-decoration: none; padding: 8px 16px; border-radius: 5px; transition: background-color 0.3s; }
        .navbar a:hover { background-color: rgba(255,255,255,0.2); }
        .container { max-width: 650px; margin: 40px auto; padding: 0 20px; }
        .card { background: white; border-radius: 10px; padding: 35px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { background-color: #d4edda; color: #155724; padding: 14px; border-radius: 5px; margin-bottom: 20px; }

        .med-header { margin-bottom: 28px; padding-bottom: 20px; border-bottom: 2px solid #f0f0f0; }
        .med-nombre { font-size: 20px; font-weight: bold; color: #333; margin-bottom: 10px; line-height: 1.4; }
        .cat-badge { display: inline-block; padding: 4px 14px; border-radius: 20px; font-size: 13px; font-weight: bold; }
        .cat-analgesicos  { background-color: #fef3e2; color: #a0520e; }
        .cat-antibioticos { background-color: #eef0ff; color: #4a4db5; }

        .section-label { font-size: 11px; color: #999; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; }
        .indicaciones-box { background-color: #f8f9fa; border-left: 4px solid #667eea; padding: 16px 18px; border-radius: 4px; font-size: 15px; color: #333; line-height: 1.6; margin-bottom: 20px; }

        .meta { font-size: 12px; color: #bbb; margin-top: 20px; padding-top: 15px; border-top: 1px solid #f0f0f0; }

        .btn-group { display: flex; gap: 12px; margin-top: 25px; }
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
        <h1>Detalle de Medicamento</h1>
        <a href="{{ route('medications.index') }}">← Medicamentos</a>
    </div>

    <div class="container">
        @if (session('success'))
            <div class="success">✓ {{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="med-header">
                <div class="med-nombre">💊 {{ $medication->nombre }}</div>
                <span class="cat-badge {{ $medication->categoria === 'Analgésicos' ? 'cat-analgesicos' : 'cat-antibioticos' }}">
                    {{ $medication->categoria }}
                </span>
            </div>

            <div class="section-label">Indicaciones</div>
            <div class="indicaciones-box">{{ $medication->indicaciones }}</div>

            <div class="meta">
                Registrado el {{ $medication->created_at->format('d/m/Y') }}
            </div>

            <div class="btn-group">
                <a href="{{ route('medications.edit', $medication->id) }}" class="btn btn-edit">✏️ Editar</a>
                <a href="{{ route('medications.index') }}" class="btn btn-back">← Volver</a>
                <form action="{{ route('medications.destroy', $medication->id) }}" method="POST"
                      onsubmit="return confirm('¿Ocultar este medicamento?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-del">🙈 Ocultar</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
