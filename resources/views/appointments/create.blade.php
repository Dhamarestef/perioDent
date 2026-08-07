<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Cita</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background-color: #f5f5f5; }
        .navbar { background-color: #667eea; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar a { color: white; text-decoration: none; padding: 8px 16px; border-radius: 5px; transition: background-color 0.3s; }
        .navbar a:hover { background-color: rgba(255,255,255,0.2); }
        .container { max-width: 620px; margin: 40px auto; padding: 0 20px; }
        .card { background: white; border-radius: 10px; padding: 35px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #333; margin-bottom: 20px; text-align: center; }
        .errors { background-color: #f8d7da; color: #721c24; padding: 14px; border-radius: 5px; margin-bottom: 20px; }
        .errors ul { margin: 0; padding-left: 20px; }

        /* Paciente (modo simple) */
        .patient-card { background-color: #eef0ff; border: 1px solid #d7dcff; border-radius: 8px; padding: 16px 18px; margin-bottom: 24px; display: flex; align-items: center; gap: 14px; }
        .patient-avatar { width: 46px; height: 46px; border-radius: 50%; background-color: #667eea; color: white; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: bold; flex-shrink: 0; }
        .patient-info .p-nombre { font-weight: bold; color: #333; font-size: 16px; }
        .patient-info .p-meta { font-size: 13px; color: #5a63b8; margin-top: 2px; }
        .patient-change { margin-left: auto; font-size: 12px; color: #667eea; text-decoration: none; font-weight: bold; white-space: nowrap; }
        .patient-change:hover { text-decoration: underline; }

        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #333; font-weight: 500; font-size: 14px; }
        select, input[type="datetime-local"], input[type="number"], textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; transition: border-color 0.3s; font-family: inherit; }
        select:focus, input:focus, textarea:focus { outline: none; border-color: #667eea; box-shadow: 0 0 5px rgba(102,126,234,0.3); }
        textarea { resize: vertical; min-height: 90px; }

        .error-msg { color: #dc3545; font-size: 12px; margin-top: 5px; }
        .hint { color: #999; font-size: 12px; margin-top: 5px; display: block; }

        .btn-group { display: flex; gap: 10px; margin-top: 28px; }
        .btn { flex: 1; padding: 12px; border: none; border-radius: 5px; font-size: 15px; font-weight: bold; cursor: pointer; transition: all 0.3s; text-align: center; text-decoration: none; display: flex; align-items: center; justify-content: center; }
        .btn-save { background-color: #667eea; color: white; }
        .btn-save:hover { background-color: #764ba2; }
        .btn-cancel { background-color: #6c757d; color: white; }
        .btn-cancel:hover { background-color: #5a6268; }

        /* Modo búsqueda (cuando no viene un paciente preseleccionado) */
        .search-wrapper { position: relative; }
        .search-input { width: 100%; padding: 12px 42px 12px 14px; border: 2px solid #ddd; border-radius: 8px; font-size: 15px; transition: border-color 0.3s; }
        .search-input:focus { outline: none; border-color: #667eea; }
        .search-icon { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: #bbb; font-size: 18px; }
        .search-results { position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #ddd; border-radius: 0 0 8px 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); z-index: 100; max-height: 260px; overflow-y: auto; display: none; }
        .search-result-item { padding: 12px 16px; cursor: pointer; border-bottom: 1px solid #f5f5f5; }
        .search-result-item:hover { background-color: #f0f4ff; }
        .search-result-item:last-child { border-bottom: none; }
        .result-nombre { font-weight: bold; color: #333; font-size: 14px; }
        .result-meta { font-size: 12px; color: #999; margin-top: 2px; }
        .search-no-results { padding: 14px 16px; color: #999; font-size: 14px; text-align: center; }
        .quick-link { text-align: center; margin-top: 12px; }
        .quick-link a { color: #667eea; font-size: 13px; font-weight: bold; text-decoration: none; }
        .quick-link a:hover { text-decoration: underline; }

        .section-disabled { opacity: 0.4; pointer-events: none; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Agendar Cita</h1>
        <a href="{{ route('appointment.index') }}">← Mis Citas</a>
    </div>

    <div class="container">
        <div class="card">
            @if ($prePatient)
                <h2>📅 Nueva cita para {{ $prePatient->nombre }}</h2>
            @else
                <h2>📅 Agendar Nueva Cita</h2>
            @endif

            @if ($errors->any())
                <div class="errors">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('appointment.store') }}" method="POST">
                @csrf

                @if ($prePatient)
                    {{-- MODO SIMPLE: paciente ya conocido (llega vía ?patient=ID) --}}
                    <input type="hidden" name="patient_id" value="{{ $prePatient->id }}">

                    <div class="patient-card">
                        <div class="patient-avatar">{{ strtoupper(substr($prePatient->nombre, 0, 1)) }}</div>
                        <div class="patient-info">
                            <div class="p-nombre">{{ $prePatient->nombre }}</div>
                            <div class="p-meta">
                                {{ $prePatient->numero_expediente }}
                                @if ($prePatient->telefono) · {{ $prePatient->telefono }} @endif
                            </div>
                        </div>
                        <a href="{{ route('patients.index') }}" class="patient-change">Cambiar paciente</a>
                    </div>
                @else
                    {{-- MODO BÚSQUEDA: sin paciente preseleccionado --}}
                    <input type="hidden" name="patient_id" id="patient_id" value="{{ old('patient_id') }}">

                    <div class="form-group">
                        <label>Paciente *</label>
                        <div class="search-wrapper" id="searchWrapper">
                            <input type="text" id="patientSearch" class="search-input"
                                   placeholder="Buscar por nombre, expediente o teléfono..." autocomplete="off">
                            <span class="search-icon">🔍</span>
                            <div class="search-results" id="searchResults"></div>
                        </div>

                        <div class="patient-card" id="patientSelected" style="display:none; margin-top: 12px; margin-bottom: 0;">
                            <div class="patient-avatar" id="selectedAvatar">?</div>
                            <div class="patient-info">
                                <div class="p-nombre" id="selectedNombre">—</div>
                                <div class="p-meta" id="selectedMeta">—</div>
                            </div>
                            <a href="#" class="patient-change" onclick="clearPatient(); return false;">Cambiar</a>
                        </div>

                        <div class="quick-link" id="quickLink">
                            <a href="{{ route('patients.create') }}">＋ Registrar un paciente nuevo</a>
                        </div>
                        @error('patient_id') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>
                @endif

                <div id="restOfForm" class="{{ $prePatient ? '' : 'section-disabled' }}">
                    <div class="form-group">
                        <label for="duration_minutes">Duración de la cita (minutos) *</label>
                        <input type="number" id="duration_minutes" name="duration_minutes"
                               value="{{ old('duration_minutes', 30) }}" min="5" step="5" required>
                        <span class="hint">El horario de finalización se calculará automáticamente según esta duración.</span>
                        @error('duration_minutes') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="appointment_date">Fecha y hora *</label>
                        <input type="datetime-local" id="appointment_date" name="appointment_date"
                               value="{{ old('appointment_date') }}" required>
                        @error('appointment_date') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="notes">Notas adicionales</label>
                        <textarea id="notes" name="notes"
                                  placeholder="Motivo de consulta, síntomas, observaciones...">{{ old('notes') }}</textarea>
                        <span class="hint">Opcional. Máximo 500 caracteres.</span>
                        @error('notes') <span class="error-msg">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="btn-group">
                    <button type="submit" class="btn btn-save" id="submitBtn" {{ $prePatient ? '' : 'disabled' }}>
                        📅 Agendar Cita
                    </button>
                    <a href="{{ route('appointment.index') }}" class="btn btn-cancel">← Cancelar</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        @if (!$prePatient)
        // Buscador de pacientes (solo cuando no viene preseleccionado)
        let searchTimeout = null;
        const searchInput   = document.getElementById('patientSearch');
        const searchResults = document.getElementById('searchResults');

        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            const q = this.value.trim();
            if (q.length < 2) {
                searchResults.style.display = 'none';
                return;
            }
            searchTimeout = setTimeout(() => fetchPatients(q), 300);
        });

        document.addEventListener('click', function (e) {
            if (!e.target.closest('#searchWrapper')) {
                searchResults.style.display = 'none';
            }
        });

        function fetchPatients(q) {
            fetch(`{{ route('patients.search') }}?q=${encodeURIComponent(q)}`)
                .then(r => r.json())
                .then(data => {
                    searchResults.innerHTML = '';
                    if (data.length === 0) {
                        searchResults.innerHTML = `<div class="search-no-results">Sin resultados</div>`;
                    } else {
                        data.forEach(p => {
                            const div = document.createElement('div');
                            div.className = 'search-result-item';
                            div.innerHTML = `
                                <div class="result-nombre">${p.nombre}</div>
                                <div class="result-meta">${p.numero_expediente}${p.telefono ? ' · ' + p.telefono : ''}</div>`;
                            div.addEventListener('click', () => selectPatient(p));
                            searchResults.appendChild(div);
                        });
                    }
                    searchResults.style.display = 'block';
                });
        }

        function selectPatient(p) {
            document.getElementById('patient_id').value = p.id;
            document.getElementById('selectedAvatar').textContent = p.nombre.charAt(0).toUpperCase();
            document.getElementById('selectedNombre').textContent = p.nombre;
            document.getElementById('selectedMeta').textContent = p.numero_expediente + (p.telefono ? ' · ' + p.telefono : '');

            document.getElementById('patientSelected').style.display = 'flex';
            document.getElementById('searchWrapper').style.display = 'none';
            document.getElementById('quickLink').style.display = 'none';
            searchResults.style.display = 'none';

            document.getElementById('restOfForm').classList.remove('section-disabled');
            document.getElementById('submitBtn').disabled = false;
        }

        function clearPatient() {
            document.getElementById('patient_id').value = '';
            document.getElementById('patientSelected').style.display = 'none';
            document.getElementById('searchWrapper').style.display = 'block';
            document.getElementById('quickLink').style.display = 'block';
            searchInput.value = '';
            document.getElementById('restOfForm').classList.add('section-disabled');
            document.getElementById('submitBtn').disabled = true;
        }
        @endif
    </script>
</body>
</html>
