<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Paciente</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background-color: #f5f5f5; }
        .navbar { background-color: #667eea; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar a { color: white; text-decoration: none; padding: 8px 16px; border-radius: 5px; transition: background-color 0.3s; }
        .navbar a:hover { background-color: rgba(255,255,255,0.2); }
        .container { max-width: 800px; margin: 40px auto; padding: 0 20px; }
        .card { background: white; border-radius: 10px; padding: 35px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #333; margin-bottom: 25px; text-align: center; }
        .expediente-badge { background-color: #e7f3ff; border-left: 4px solid #667eea; padding: 12px 16px; border-radius: 4px; margin-bottom: 25px; color: #004085; font-size: 14px; }
        .expediente-badge strong { font-size: 18px; }
        .section-title { color: #667eea; font-size: 13px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin: 25px 0 15px; border-bottom: 2px solid #f0f0f0; padding-bottom: 8px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 18px; }
        .form-row.full { grid-template-columns: 1fr; }
        .form-row.thirds { grid-template-columns: 1fr 1fr 1fr; }
        .form-group { display: flex; flex-direction: column; }
        label { margin-bottom: 6px; color: #444; font-weight: 500; font-size: 13px; }
        input, select { padding: 10px 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; transition: border-color 0.3s; }
        input:focus, select:focus { outline: none; border-color: #667eea; box-shadow: 0 0 4px rgba(102,126,234,0.3); }
        .radio-group { display: flex; gap: 20px; padding: 10px 0; }
        .radio-group label { display: flex; align-items: center; gap: 6px; font-weight: normal; margin: 0; cursor: pointer; }
        .error-msg { color: #dc3545; font-size: 12px; margin-top: 4px; }
        .errors { background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .errors ul { margin: 0; padding-left: 20px; }
        .btn-group { display: flex; gap: 12px; margin-top: 30px; }
        .btn { flex: 1; padding: 12px; border: none; border-radius: 5px; font-size: 15px; font-weight: bold; cursor: pointer; transition: all 0.3s; text-align: center; text-decoration: none; display: flex; align-items: center; justify-content: center; }
        .btn-save { background-color: #667eea; color: white; }
        .btn-save:hover { background-color: #764ba2; }
        .btn-cancel { background-color: #6c757d; color: white; }
        .btn-cancel:hover { background-color: #5a6268; }
        @media (max-width: 600px) { .form-row, .form-row.thirds { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Editar Paciente</h1>
        <a href="{{ route('patients.show', $patient->id) }}">← Ver expediente</a>
    </div>

    <div class="container">
        <div class="card">
            <h2>✏️ Editar Paciente</h2>

            @if ($errors->any())
                <div class="errors">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="expediente-badge">
                N° de Expediente: <strong>{{ $patient->numero_expediente }}</strong>
            </div>

            <form action="{{ route('patients.update', $patient->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="section-title">Información general</div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="fecha_registro">Fecha de registro *</label>
                        <input type="date" id="fecha_registro" name="fecha_registro"
                               value="{{ old('fecha_registro', $patient->fecha_registro->format('Y-m-d')) }}" required>
                        @error('fecha_registro') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="nombre">Nombre del paciente *</label>
                        <input type="text" id="nombre" name="nombre"
                               value="{{ old('nombre', $patient->nombre) }}" required>
                        @error('nombre') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-row thirds">
                    <div class="form-group">
                        <label for="edad">Edad *</label>
                        <input type="number" id="edad" name="edad"
                               value="{{ old('edad', $patient->edad) }}" min="0" max="150" required>
                        @error('edad') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Sexo *</label>
                        <div class="radio-group">
                            <label>
                                <input type="radio" name="sexo" value="F"
                                       {{ old('sexo', $patient->sexo) === 'F' ? 'checked' : '' }}>
                                (F) Femenino
                            </label>
                            <label>
                                <input type="radio" name="sexo" value="M"
                                       {{ old('sexo', $patient->sexo) === 'M' ? 'checked' : '' }}>
                                (M) Masculino
                            </label>
                        </div>
                        @error('sexo') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="fecha_nacimiento">Fecha de nacimiento *</label>
                        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento"
                               value="{{ old('fecha_nacimiento', $patient->fecha_nacimiento->format('Y-m-d')) }}" required>
                        @error('fecha_nacimiento') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="escolaridad">Escolaridad *</label>
                        <select id="escolaridad" name="escolaridad" required>
                            @foreach(['Primaria','Secundaria','Preparatoria','Licenciatura','Especialidad','Otro'] as $op)
                                <option value="{{ $op }}"
                                    {{ old('escolaridad', $patient->escolaridad) === $op ? 'selected' : '' }}>
                                    {{ $op }}
                                </option>
                            @endforeach
                        </select>
                        @error('escolaridad') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="estado_civil">Estado civil *</label>
                        <select id="estado_civil" name="estado_civil" required>
                            @foreach(['Soltero(a)','Casado(a)','Divorciado(a)','Viudo(a)','Unión libre','Otro'] as $op)
                                <option value="{{ $op }}"
                                    {{ old('estado_civil', $patient->estado_civil) === $op ? 'selected' : '' }}>
                                    {{ $op }}
                                </option>
                            @endforeach
                        </select>
                        @error('estado_civil') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="section-title">Ubicación y contacto</div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="lugar_nacimiento">Lugar de nacimiento *</label>
                        <input type="text" id="lugar_nacimiento" name="lugar_nacimiento"
                               value="{{ old('lugar_nacimiento', $patient->lugar_nacimiento) }}" required>
                        @error('lugar_nacimiento') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="lugar_residencia">Lugar de residencia *</label>
                        <input type="text" id="lugar_residencia" name="lugar_residencia"
                               value="{{ old('lugar_residencia', $patient->lugar_residencia) }}" required>
                        @error('lugar_residencia') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-row full">
                    <div class="form-group">
                        <label for="domicilio_actual">Domicilio actual *</label>
                        <input type="text" id="domicilio_actual" name="domicilio_actual"
                               value="{{ old('domicilio_actual', $patient->domicilio_actual) }}" required>
                        @error('domicilio_actual') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="telefono">Teléfono</label>
                        <input type="tel" id="telefono" name="telefono"
                               value="{{ old('telefono', $patient->telefono) }}">
                        @error('telefono') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="correo">Correo electrónico</label>
                        <input type="email" id="correo" name="correo"
                               value="{{ old('correo', $patient->correo) }}">
                        @error('correo') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-row full">
                    <div class="form-group">
                        <label for="alergias">Alergias</label>
                        <input type="text" id="alergias" name="alergias"
                               value="{{ old('alergias', $patient->alergias) }}"
                               placeholder="Dejar vacío si no tiene alergias conocidas">
                        @error('alergias') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="btn-group">
                    <button type="submit" class="btn btn-save">💾 Guardar Cambios</button>
                    <a href="{{ route('patients.show', $patient->id) }}" class="btn btn-cancel">← Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
