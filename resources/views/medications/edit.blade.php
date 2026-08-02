<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Medicamento</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background-color: #f5f5f5; }
        .navbar { background-color: #667eea; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar a { color: white; text-decoration: none; padding: 8px 16px; border-radius: 5px; transition: background-color 0.3s; }
        .navbar a:hover { background-color: rgba(255,255,255,0.2); }
        .container { max-width: 620px; margin: 40px auto; padding: 0 20px; }
        .card { background: white; border-radius: 10px; padding: 35px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #333; margin-bottom: 25px; text-align: center; }
        .errors { background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .errors ul { margin: 0; padding-left: 20px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 7px; color: #444; font-weight: 500; font-size: 14px; }
        input[type="text"], select, textarea { width: 100%; padding: 11px 13px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; transition: border-color 0.3s; font-family: 'Segoe UI', sans-serif; }
        input:focus, select:focus, textarea:focus { outline: none; border-color: #667eea; box-shadow: 0 0 4px rgba(102,126,234,0.3); }
        textarea { resize: vertical; min-height: 110px; }
        .error-msg { color: #dc3545; font-size: 12px; margin-top: 4px; }
        .char-counter { text-align: right; font-size: 12px; color: #999; margin-top: 4px; }
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
        <h1>Editar Medicamento</h1>
        <a href="{{ route('medications.show', $medication->id) }}">← Ver medicamento</a>
    </div>

    <div class="container">
        <div class="card">
            <h2>✏️ Editar Medicamento</h2>

            @if ($errors->any())
                <div class="errors">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('medications.update', $medication->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="nombre">Nombre del medicamento *</label>
                    <input type="text" id="nombre" name="nombre"
                           value="{{ old('nombre', $medication->nombre) }}" required>
                    @error('nombre') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="categoria">Categoría *</label>
                    <select id="categoria" name="categoria" required>
                        @foreach ($categorias as $cat)
                            <option value="{{ $cat }}"
                                {{ old('categoria', $medication->categoria) === $cat ? 'selected' : '' }}>
                                {{ $cat }}
                            </option>
                        @endforeach
                    </select>
                    @error('categoria') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="principio_activo">Principio activo</label>
                    <input type="text" id="principio_activo" name="principio_activo"
                           value="{{ old('principio_activo', $medication->principio_activo) }}"
                           placeholder="Ej: Ibuprofeno">
                    @error('principio_activo') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="presentacion">Presentación</label>
                    <input type="text" id="presentacion" name="presentacion"
                           value="{{ old('presentacion', $medication->presentacion) }}"
                           placeholder="Ej: 600 mg tabletas">
                    @error('presentacion') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="dosis_habitual">Dosis habitual</label>
                    <input type="text" id="dosis_habitual" name="dosis_habitual"
                           value="{{ old('dosis_habitual', $medication->dosis_habitual) }}"
                           placeholder="Ej: 1 tableta cada 8 horas">
                    @error('dosis_habitual') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="indicaciones">Indicaciones *</label>
                    <textarea id="indicaciones" name="indicaciones"
                              maxlength="500"
                              oninput="updateCounter(this)"
                              required>{{ old('indicaciones', $medication->indicaciones) }}</textarea>
                    <div class="char-counter"><span id="counter">0</span> / 500</div>
                    @error('indicaciones') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="btn-group">
                    <button type="submit" class="btn btn-save">💾 Guardar Cambios</button>
                    <a href="{{ route('medications.show', $medication->id) }}" class="btn btn-cancel">← Cancelar</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function updateCounter(el) {
            document.getElementById('counter').textContent = el.value.length;
        }
        const ta = document.getElementById('indicaciones');
        if (ta.value) updateCounter(ta);
    </script>
</body>
</html>
