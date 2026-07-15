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
        .container { max-width: 680px; margin: 40px auto; padding: 0 20px; }

        /* Steps */
        .steps { display: flex; gap: 0; margin-bottom: 30px; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .step { flex: 1; padding: 14px; text-align: center; background: white; font-size: 13px; font-weight: bold; color: #bbb; border-right: 1px solid #f0f0f0; position: relative; }
        .step:last-child { border-right: none; }
        .step.active { background-color: #667eea; color: white; }
        .step.done { background-color: #d4edda; color: #155724; }
        .step-num { font-size: 18px; display: block; margin-bottom: 2px; }

        .card { background: white; border-radius: 10px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .card-title { font-size: 16px; font-weight: bold; color: #667eea; margin-bottom: 18px; display: flex; align-items: center; gap: 8px; }

        .errors { background-color: #f8d7da; color: #721c24; padding: 14px; border-radius: 5px; margin-bottom: 20px; }
        .errors ul { margin: 0; padding-left: 20px; }

        /* Buscador */
        .search-wrapper { position: relative; }
        .search-input { width: 100%; padding: 12px 42px 12px 14px; border: 2px solid #ddd; border-radius: 8px; font-size: 15px; transition: border-color 0.3s; }
        .search-input:focus { outline: none; border-color: #667eea; }
        .search-icon { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: #bbb; font-size: 18px; }
        .search-results { position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #ddd; border-radius: 0 0 8px 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); z-index: 100; max-height: 280px; overflow-y: auto; display: none; }
        .search-result-item { padding: 12px 16px; cursor: pointer; border-bottom: 1px solid #f5f5f5; transition: background-color 0.2s; }
        .search-result-item:hover { background-color: #f0f4ff; }
        .search-result-item:last-child { border-bottom: none; }
        .result-nombre { font-weight: bold; color: #333; font-size: 14px; }
        .result-meta { font-size: 12px; color: #999; margin-top: 2px; }
        .search-no-results { padding: 14px 16px; color: #999; font-size: 14px; text-align: center; }

        /* Paciente seleccionado */
        .patient-selected { background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 8px; padding: 14px 16px; display: none; align-items: center; justify-content: space-between; margin-top: 10px; }
        .patient-selected.show { display: flex; }
        .patient-selected-info .p-nombre { font-weight: bold; color: #155724; font-size: 15px; }
        .patient-selected-info .p-meta { font-size: 12px; color: #5a9e6f; margin-top: 2px; }
        .btn-change { background: none; border: 1px solid #28a745; color: #28a745; padding: 5px 12px; border-radius: 5px; cursor: pointer; font-size: 12px; font-weight: bold; transition: all 0.2s; flex-shrink: 0; }
        .btn-change:hover { background-color: #28a745; color: white; }

        /* Panel registro rápido */
        .quick-register { background-color: #fff8e1; border: 1px solid #ffe082; border-radius: 8px; padding: 20px; margin-top: 12px; display: none; }
        .quick-register.show { display: block; }
        .quick-register-title { font-weight: bold; color: #856404; font-size: 14px; margin-bottom: 14px; }
        .quick-register-title span { font-weight: normal; font-size: 12px; margin-left: 8px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .form-group { display: flex; flex-direction: column; }
        label { margin-bottom: 6px; color: #444; font-weight: 500; font-size: 13px; }
        input, select { padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; transition: border-color 0.3s; }
        input:focus, select:focus { outline: none; border-color: #667eea; }
        .btn-quick-save { background-color: #f0a500; color: white; border: none; padding: 10px 22px; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 14px; margin-top: 14px; transition: background-color 0.2s; }
        .btn-quick-save:hover { background-color: #d4900a; }
        .btn-quick-cancel { background: none; border: none; color: #999; cursor: pointer; font-size: 13px; margin-top: 14px; margin-left: 10px; }

        /* Sección 2: Servicio y fecha */
        .form-group { margin-bottom: 18px; }
        .service-details { background-color: #f9f9f9; padding: 14px; border-radius: 6px; margin-top: 8px; display: none; }
        .service-details.show { display: block; }
        .detail-row { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px; }
        .detail-row:last-child { margin-bottom: 0; }
        .detail-label { color: #666; }
        .detail-value { font-weight: bold; color: #333; }
        textarea { width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; resize: vertical; min-height: 90px; font-family: 'Segoe UI', sans-serif; }
        textarea:focus { outline: none; border-color: #667eea; }
        .error-msg { color: #dc3545; font-size: 12px; margin-top: 4px; }

        /* Botones finales */
        .btn-group { display: flex; gap: 12px; margin-top: 10px; }
        .btn-submit { flex: 1; padding: 13px; background-color: #667eea; color: white; border: none; border-radius: 6px; font-size: 16px; font-weight: bold; cursor: pointer; transition: background-color 0.3s; }
        .btn-submit:hover { background-color: #764ba2; }
        .btn-submit:disabled { background-color: #ccc; cursor: not-allowed; }
        .btn-cancel-link { flex: 1; padding: 13px; background-color: #6c757d; color: white; border-radius: 6px; text-align: center; text-decoration: none; font-size: 15px; font-weight: bold; display: flex; align-items: center; justify-content: center; }
        .btn-cancel-link:hover { background-color: #5a6268; }

        .section-disabled { opacity: 0.4; pointer-events: none; }
        .hint { font-size: 12px; color: #999; margin-top: 4px; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Nueva Cita</h1>
        <a href="{{ route('appointment.index') }}">← Mis Citas</a>
    </div>

    <div class="container">

        @if ($errors->any())
            <div class="errors">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Pasos visuales -->
        <div class="steps">
            <div class="step active" id="step1-indicator">
                <span class="step-num">①</span> Paciente
            </div>
            <div class="step" id="step2-indicator">
                <span class="step-num">②</span> Servicio y Fecha
            </div>
            <div class="step" id="step3-indicator">
                <span class="step-num">③</span> Confirmar
            </div>
        </div>

        <form action="{{ route('appointment.store') }}" method="POST" id="appointmentForm">
            @csrf
            <input type="hidden" name="patient_id" id="patient_id" value="{{ old('patient_id') }}">

            <!-- ── PASO 1: PACIENTE ── -->
            <div class="card" id="step1-card">
                <div class="card-title">👤 Paso 1 — Selecciona o registra al paciente</div>

                <div class="search-wrapper" id="searchWrapper">
                    <input type="text"
                           id="patientSearch"
                           class="search-input"
                           placeholder="Buscar por nombre, expediente o teléfono..."
                           autocomplete="off">
                    <span class="search-icon">🔍</span>
                    <div class="search-results" id="searchResults"></div>
                </div>

                <!-- Paciente seleccionado -->
                <div class="patient-selected" id="patientSelected">
                    <div class="patient-selected-info">
                        <div class="p-nombre" id="selectedNombre">—</div>
                        <div class="p-meta" id="selectedMeta">—</div>
                    </div>
                    <button type="button" class="btn-change" onclick="clearPatient()">
                        ✕ Cambiar
                    </button>
                </div>

                <!-- Botón para mostrar registro rápido -->
                <div style="margin-top: 14px; text-align: center;" id="quickRegisterToggle">
                    <button type="button"
                            onclick="toggleQuickRegister()"
                            style="background:none; border:none; color:#667eea; cursor:pointer; font-size:13px; font-weight:bold; text-decoration:underline;">
                        ＋ Paciente nuevo — registrar solo nombre y contacto
                    </button>
                </div>

                <!-- Formulario registro rápido -->
                <div class="quick-register" id="quickRegister">
                    <div class="quick-register-title">
                        📋 Registro rápido
                        <span>(podrás completar el expediente después)</span>
                    </div>
                    <div class="form-row">
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label for="qr_nombre">Nombre completo *</label>
                            <input type="text" id="qr_nombre" placeholder="Nombre del paciente">
                        </div>
                        <div class="form-group">
                            <label for="qr_telefono">Teléfono</label>
                            <input type="tel" id="qr_telefono" placeholder="10 dígitos">
                        </div>
                        <div class="form-group">
                            <label for="qr_correo">Correo electrónico</label>
                            <input type="email" id="qr_correo" placeholder="correo@ejemplo.com">
                        </div>
                    </div>
                    <button type="button" class="btn-quick-save" onclick="quickRegister()">
                        Registrar y continuar →
                    </button>
                    <button type="button" class="btn-quick-cancel" onclick="toggleQuickRegister()">
                        Cancelar
                    </button>
                    <div id="quickRegisterError" style="color:#dc3545; font-size:13px; margin-top:8px; display:none;"></div>
                </div>
            </div>

            <!-- ── PASO 2: SERVICIO Y FECHA ── -->
            <div class="card section-disabled" id="step2-card">
                <div class="card-title">📅 Paso 2 — Servicio y fecha</div>

                <div class="form-group">
                    <label for="service_id">Servicio *</label>
                    <select id="service_id" name="service_id" required onchange="showServiceDetails()">
                        <option value="">-- Selecciona un servicio --</option>
                        @foreach ($services as $service)
                            <option value="{{ $service->id }}"
                                    data-price="{{ $service->price }}"
                                    data-duration="{{ $service->duration }}"
                                    {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                {{ $service->name }} — ${{ number_format($service->price, 2) }}
                            </option>
                        @endforeach
                    </select>
                    @error('service_id') <span class="error-msg">{{ $message }}</span> @enderror

                    <div class="service-details" id="serviceDetails">
                        <div class="detail-row">
                            <span class="detail-label">Duración:</span>
                            <span class="detail-value" id="duration">—</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Precio:</span>
                            <span class="detail-value" id="price">—</span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="appointment_date">Fecha y hora *</label>
                    <input type="datetime-local"
                           id="appointment_date"
                           name="appointment_date"
                           value="{{ old('appointment_date') }}"
                           required>
                    @error('appointment_date') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="notes">Notas adicionales</label>
                    <textarea id="notes" name="notes"
                              placeholder="Motivo de consulta, síntomas, observaciones...">{{ old('notes') }}</textarea>
                    <span class="hint">Opcional. Máximo 500 caracteres.</span>
                </div>
            </div>

            <!-- ── BOTONES ── -->
            <div class="btn-group">
                <button type="submit" class="btn-submit" id="submitBtn" disabled>
                    📅 Confirmar Cita
                </button>
                <a href="{{ route('appointment.index') }}" class="btn-cancel-link">← Cancelar</a>
            </div>
        </form>
    </div>

    <script>
        // ── Estado global ──
        let patientSelected = false;

        // ── Buscador de pacientes ──
        let searchTimeout = null;
        const searchInput  = document.getElementById('patientSearch');
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
                        searchResults.innerHTML = `<div class="search-no-results">Sin resultados — usa "Paciente nuevo" para registrarlo</div>`;
                    } else {
                        data.forEach(p => {
                            const div = document.createElement('div');
                            div.className = 'search-result-item';
                            div.innerHTML = `
                                <div class="result-nombre">${p.nombre}</div>
                                <div class="result-meta">
                                    ${p.numero_expediente}
                                    ${p.telefono ? ' · ' + p.telefono : ''}
                                </div>`;
                            div.addEventListener('click', () => selectPatient(p));
                            searchResults.appendChild(div);
                        });
                    }
                    searchResults.style.display = 'block';
                });
        }

        function selectPatient(p) {
            document.getElementById('patient_id').value = p.id;
            document.getElementById('selectedNombre').textContent = p.nombre;
            document.getElementById('selectedMeta').textContent =
                p.numero_expediente + (p.telefono ? ' · ' + p.telefono : '');

            document.getElementById('patientSelected').classList.add('show');
            document.getElementById('searchWrapper').style.display = 'none';
            document.getElementById('quickRegisterToggle').style.display = 'none';
            document.getElementById('quickRegister').classList.remove('show');
            searchResults.style.display = 'none';

            enableStep2();
            updateStepIndicators(1);
        }

        function clearPatient() {
            document.getElementById('patient_id').value = '';
            document.getElementById('patientSelected').classList.remove('show');
            document.getElementById('searchWrapper').style.display = 'block';
            document.getElementById('quickRegisterToggle').style.display = 'block';
            searchInput.value = '';
            patientSelected = false;
            disableStep2();
            updateStepIndicators(0);
        }

        // ── Registro rápido ──
        function toggleQuickRegister() {
            const panel = document.getElementById('quickRegister');
            panel.classList.toggle('show');
        }

        function quickRegister() {
            const nombre   = document.getElementById('qr_nombre').value.trim();
            const telefono = document.getElementById('qr_telefono').value.trim();
            const correo   = document.getElementById('qr_correo').value.trim();
            const errDiv   = document.getElementById('quickRegisterError');

            if (!nombre) {
                errDiv.textContent = 'El nombre es requerido';
                errDiv.style.display = 'block';
                return;
            }
            errDiv.style.display = 'none';

            fetch('{{ route('patients.quick-store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ nombre, telefono, correo }),
            })
            .then(r => r.json())
            .then(p => {
                selectPatient(p);
                // Mostrar aviso de expediente incompleto
                document.getElementById('selectedMeta').textContent =
                    p.numero_expediente + ' · ⚠️ Expediente pendiente de completar';
            })
            .catch(() => {
                errDiv.textContent = 'Error al registrar el paciente. Intenta de nuevo.';
                errDiv.style.display = 'block';
            });
        }

        // ── Habilitar / deshabilitar paso 2 ──
        function enableStep2() {
            document.getElementById('step2-card').classList.remove('section-disabled');
            document.getElementById('submitBtn').disabled = false;
            patientSelected = true;
        }

        function disableStep2() {
            document.getElementById('step2-card').classList.add('section-disabled');
            document.getElementById('submitBtn').disabled = true;
        }

        // ── Indicadores de paso ──
        function updateStepIndicators(completedStep) {
            const s1 = document.getElementById('step1-indicator');
            const s2 = document.getElementById('step2-indicator');
            const s3 = document.getElementById('step3-indicator');

            if (completedStep >= 1) {
                s1.classList.remove('active');
                s1.classList.add('done');
                s2.classList.add('active');
            }
        }

        // ── Detalles del servicio ──
        function showServiceDetails() {
            const select  = document.getElementById('service_id');
            const option  = select.options[select.selectedIndex];
            const details = document.getElementById('serviceDetails');

            if (option.value) {
                document.getElementById('duration').textContent = option.dataset.duration + ' minutos';
                document.getElementById('price').textContent    = '$' + parseFloat(option.dataset.price).toFixed(2);
                details.classList.add('show');

                // Marcar paso 3 activo al seleccionar servicio
                document.getElementById('step2-indicator').classList.remove('active');
                document.getElementById('step2-indicator').classList.add('done');
                document.getElementById('step3-indicator').classList.add('active');
            } else {
                details.classList.remove('show');
            }
        }

        // Si hay old('patient_id') al recargar por error de validación
        @if (old('patient_id'))
            document.addEventListener('DOMContentLoaded', function () {
                fetch(`{{ route('patients.search') }}?q={{ old('patient_id') }}`)
                    .then(r => r.json());

                // Reactivar paso 2 si ya había paciente seleccionado
                enableStep2();
                document.getElementById('searchWrapper').style.display = 'none';
                document.getElementById('quickRegisterToggle').style.display = 'none';
            });
        @endif
    </script>
</body>
</html>
