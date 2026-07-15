<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Tratamiento</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background-color: #f5f5f5; }
        .navbar { background-color: #667eea; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar a { color: white; text-decoration: none; padding: 8px 16px; border-radius: 5px; transition: background-color 0.3s; }
        .navbar a:hover { background-color: rgba(255,255,255,0.2); }
        .container { max-width: 600px; margin: 40px auto; padding: 0 20px; }
        .card { background: white; border-radius: 10px; padding: 35px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #333; margin-bottom: 25px; text-align: center; }
        .errors { background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .errors ul { margin: 0; padding-left: 20px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 7px; color: #444; font-weight: 500; font-size: 14px; }
        input[type="text"], input[type="number"], select { width: 100%; padding: 11px 13px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; transition: border-color 0.3s; }
        input:focus, select:focus { outline: none; border-color: #667eea; box-shadow: 0 0 4px rgba(102,126,234,0.3); }
        .error-msg { color: #dc3545; font-size: 12px; margin-top: 4px; }
        .hint { color: #999; font-size: 12px; margin-top: 4px; }
        .btn-group { display: flex; gap: 12px; margin-top: 28px; }
        .btn { flex: 1; padding: 12px; border: none; border-radius: 5px; font-size: 15px; font-weight: bold; cursor: pointer; transition: all 0.3s; text-align: center; text-decoration: none; display: flex; align-items: center; justify-content: center; }
        .btn-save { background-color: #667eea; color: white; }
        .btn-save:hover { background-color: #764ba2; }
        .btn-cancel { background-color: #6c757d; color: white; }
        .btn-cancel:hover { background-color: #5a6268; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Editar Tratamiento</h1>
        <a href="{{ route('treatments.show', $treatment->id) }}">← Ver tratamiento</a>
    </div>

    <div class="container">
        <div class="card">
            <h2>✏️ Editar Tratamiento</h2>

            @if ($errors->any())
                <div class="errors">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('treatments.update', $treatment->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="abreviacion">Abreviación *</label>
                    <input type="text" id="abreviacion" name="abreviacion"
                           value="{{ old('abreviacion', $treatment->abreviacion) }}"
                           required style="text-transform: uppercase;">
                    @error('abreviacion') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="nombre">Nombre del tratamiento *</label>
                    <input type="text" id="nombre" name="nombre"
                           value="{{ old('nombre', $treatment->nombre) }}" required>
                    @error('nombre') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="costo">Costo ($)</label>
                    <input type="number" id="costo" name="costo"
                           value="{{ old('costo', $treatment->costo) }}"
                           placeholder="0.00" min="0" step="0.01">
                    <span class="hint">Deja en blanco si el costo es por cotizar.</span>
                    @error('costo') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="categoria">Categoría *</label>
                    <select id="categoria" name="categoria" required>
                        @foreach ($categorias as $cat)
                            <option value="{{ $cat }}"
                                {{ old('categoria', $treatment->categoria) === $cat ? 'selected' : '' }}>
                                {{ $cat }}
                            </option>
                        @endforeach
                    </select>
                    @error('categoria') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="btn-group">
                    <button type="submit" class="btn btn-save">💾 Guardar Cambios</button>
                    <a href="{{ route('treatments.show', $treatment->id) }}" class="btn btn-cancel">← Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
