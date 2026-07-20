<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda de Citas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5; }

        .navbar { background-color: #667eea; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar a, .navbar button { color: white; text-decoration: none; padding: 8px 16px; border-radius: 5px; border: none; cursor: pointer; background: none; font-size: 14px; transition: background-color 0.3s; }
        .navbar a:hover, .navbar button:hover { background-color: rgba(255,255,255,0.2); }

        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px 60px; }

        .toolbar { background: white; border-radius: 10px; padding: 16px 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.07); margin-bottom: 20px; display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
        .toolbar h2 { color: #333; font-size: 20px; margin-right: auto; }

        .search-wrapper { position: relative; width: 280px; max-width: 100%; }
        .search-wrapper input { width: 100%; padding: 9px 14px; border: 1px solid #ddd; border-radius: 6px; font-size: 13px; }
        .search-wrapper input:focus { outline: none; border-color: #667eea; }
        .search-results { position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #ddd; border-radius: 0 0 8px 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.12); z-index: 50; max-height: 260px; overflow-y: auto; display: none; }
        .search-results.show { display: block; }
        .search-result-item { padding: 10px 14px; cursor: pointer; border-bottom: 1px solid #f5f5f5; font-size: 13px; }
        .search-result-item:hover { background-color: #f0f4ff; }
        .search-result-item .sr-status { color: #999; font-size: 11px; }

        .btn-new { background-color: #667eea; color: white; padding: 9px 20px; border-radius: 6px; border: none; font-weight: bold; font-size: 13px; cursor: pointer; white-space: nowrap; }
        .btn-new:hover { background-color: #764ba2; }

        .legend { display: flex; gap: 16px; flex-wrap: wrap; width: 100%; padding-top: 10px; border-top: 1px solid #f0f0f0; }
        .legend-item { display: flex; align-items: center; gap: 6px; font-size: 12px; color: #555; }
        .legend-dot { width: 12px; height: 12px; border-radius: 50%; }

        .calendar-card { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.07); }

        .fc { font-family: 'Segoe UI', sans-serif; font-size: 13px; }
        .fc .fc-button-primary { background-color: #667eea; border-color: #667eea; }
        .fc .fc-button-primary:hover { background-color: #764ba2; border-color: #764ba2; }
        .fc .fc-button-primary:disabled { background-color: #a9b3ec; border-color: #a9b3ec; }
        .fc-event { cursor: pointer; border: none !important; }

        /* ── Modal ── */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 1000; align-items: flex-start; justify-content: center; padding: 40px 20px; overflow-y: auto; }
        .modal-overlay.open { display: flex; }
        .modal-box { background: white; border-radius: 10px; width: 100%; max-width: 520px; box-shadow: 0 10px 40px rgba(0,0,0,0.3); }
        .modal-header { display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-bottom: 1px solid #f0f0f0; }
        .modal-header h3 { color: #333; font-size: 18px; }
        .modal-close { background: none; border: none; font-size: 20px; color: #999; cursor: pointer; line-height: 1; padding: 4px 8px; }
        .modal-close:hover { color: #333; }
        .modal-body { padding: 22px 24px; max-height: 70vh; overflow-y: auto; }
        .modal-footer { padding: 16px 24px; border-top: 1px solid #f0f0f0; display: flex; gap: 8px; flex-wrap: wrap; }

        .form-group { margin-bottom: 16px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        label { display: block; font-size: 12.5px; color: #555; margin-bottom: 5px; font-weight: 500; }
        select, input, textarea { width: 100%; padding: 9px 11px; border: 1px solid #ddd; border-radius: 5px; font-size: 13.5px; font-family: inherit; }
        select:focus, input:focus, textarea:focus { outline: none; border-color: #667eea; box-shadow: 0 0 4px rgba(102,126,234,0.3); }
        textarea { resize: vertical; min-height: 60px; }

        .patient-toggle { display: flex; gap: 8px; margin-bottom: 14px; }
        .patient-toggle button { flex: 1; padding: 8px; border: 2px solid #ddd; border-radius: 6px; background: white; font-size: 12.5px; font-weight: bold; color: #666; cursor: pointer; }
        .patient-toggle button.active { border-color: #667eea; background-color: #eef0ff; color: #4a4db5; }

        .patient-search-wrapper { position: relative; }
        .patient-search-results { position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #ddd; border-radius: 0 0 8px 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); z-index: 60; max-height: 220px; overflow-y: auto; display: none; }
        .patient-search-results.show { display: block; }
        .patient-result-item { padding: 10px 14px; cursor: pointer; border-bottom: 1px solid #f5f5f5; font-size: 13px; }
        .patient-result-item:hover { background-color: #f0f4ff; }

        .selected-patient { background-color: #eef0ff; border: 1px solid #d7dcff; border-radius: 8px; padding: 12px 14px; display: flex; align-items: center; gap: 10px; margin-top: 8px; }
        .selected-patient .sp-name { font-weight: bold; color: #333; font-size: 13.5px; }
        .selected-patient .sp-meta { font-size: 11.5px; color: #5a63b8; }
        .selected-patient .sp-change { margin-left: auto; font-size: 11.5px; color: #667eea; cursor: pointer; font-weight: bold; }

        .status-select-wrap { display: none; }
        .status-select-wrap.show { display: block; }

        .form-error { color: #dc3545; font-size: 12px; margin-top: -8px; margin-bottom: 12px; }

        .btn { padding: 10px 18px; border: none; border-radius: 6px; font-size: 13.5px; font-weight: bold; cursor: pointer; transition: all 0.2s; }
        .btn-save { background-color: #667eea; color: white; flex: 1; }
        .btn-save:hover { background-color: #764ba2; }
        .btn-danger { background-color: #dc3545; color: white; }
        .btn-danger:hover { background-color: #c82333; }
        .btn-outline { background-color: white; border: 1px solid #ddd; color: #555; }
        .btn-outline:hover { background-color: #f5f5f5; }

        .readonly-patient { background-color: #f9f9f9; border-radius: 8px; padding: 12px 14px; margin-bottom: 14px; font-size: 13px; color: #555; }
        .readonly-patient strong { color: #333; }
        .readonly-patient a { color: #667eea; font-size: 12px; font-weight: bold; text-decoration: none; margin-left: 8px; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>📅 Agenda de Citas</h1>
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
            <div style="background-color:#d4edda; color:#155724; padding:14px; border-radius:8px; margin-bottom:20px;">✓ {{ session('success') }}</div>
        @endif

        <div class="toolbar">
            <h2>Calendario</h2>

            <div class="search-wrapper" id="searchWrapper">
                <input type="text" id="citaSearch" placeholder="🔍 Buscar por paciente, expediente o teléfono..." autocomplete="off">
                <div class="search-results" id="citaSearchResults"></div>
            </div>

            <button type="button" class="btn-new" onclick="openCreateModal()">＋ Nueva Cita</button>

            <div class="legend">
                @foreach ($estados as $key => $e)
                    <div class="legend-item">
                        <div class="legend-dot" style="background-color: {{ $e['color'] }};"></div>
                        {{ $e['label'] }}
                    </div>
                @endforeach
            </div>
        </div>

        <div class="calendar-card">
            <div id="calendar"></div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal-overlay" id="modalOverlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3 id="modalTitle">Nueva Cita</h3>
                <button class="modal-close" onclick="closeModal()">✕</button>
            </div>
            <div class="modal-body">
                <div class="form-error" id="formError" style="display:none;"></div>

                <!-- Selección de paciente (solo al crear) -->
                <div id="patientSection">
                    <div class="patient-toggle" id="patientToggle">
                        <button type="button" data-mode="registrado" class="active" onclick="setPatientMode('registrado')">👤 Paciente registrado</button>
                        <button type="button" data-mode="nuevo" onclick="setPatientMode('nuevo')">＋ Primera vez</button>
                    </div>

                    <div id="registradoBlock">
                        <div class="patient-search-wrapper" id="patientSearchWrapper">
                            <input type="text" id="patientSearchInput" placeholder="Buscar por nombre, expediente o teléfono..." autocomplete="off">
                            <div class="patient-search-results" id="patientSearchResults"></div>
                        </div>
                        <div class="selected-patient" id="selectedPatientBox" style="display:none;">
                            <div>
                                <div class="sp-name" id="spName">—</div>
                                <div class="sp-meta" id="spMeta">—</div>
                            </div>
                            <div class="sp-change" onclick="clearSelectedPatient()">Cambiar</div>
                        </div>
                    </div>

                    <div id="nuevoBlock" style="display:none;">
                        <div class="form-group">
                            <label for="walkInNombre">Nombre del paciente *</label>
                            <input type="text" id="walkInNombre" placeholder="Nombre completo">
                        </div>
                        <div class="form-group">
                            <label for="walkInTelefono">Teléfono</label>
                            <input type="tel" id="walkInTelefono" placeholder="10 dígitos">
                        </div>
                        <p style="font-size:12px; color:#999; margin-top:-8px; margin-bottom:14px;">Se registrará automáticamente con el servicio <strong>Consulta de primera vez</strong>.</p>
                    </div>
                </div>

                <!-- Info de paciente (solo al ver/editar cita existente) -->
                <div id="patientReadonlySection" style="display:none;"></div>

                <div class="form-group" id="serviceSelectGroup">
                    <label for="serviceSelect">Servicio *</label>
                    <select id="serviceSelect">
                        <option value="">-- Selecciona un servicio --</option>
                        @foreach ($services as $service)
                            <option value="{{ $service->id }}" data-duration="{{ $service->duration }}">
                                {{ $service->name }} — ${{ number_format($service->price, 2) }} ({{ $service->duration }} min)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="startInput">Fecha y hora de inicio *</label>
                        <input type="datetime-local" id="startInput">
                    </div>
                    <div class="form-group">
                        <label for="endInput">Hora de finalización</label>
                        <input type="datetime-local" id="endInput">
                    </div>
                </div>

                <div class="form-group status-select-wrap" id="statusSelectWrap">
                    <label for="statusSelect">Estado de la cita</label>
                    <select id="statusSelect">
                        @foreach ($estados as $key => $e)
                            <option value="{{ $key }}">{{ $e['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="notesInput">Observaciones</label>
                    <textarea id="notesInput" maxlength="500" placeholder="Motivo de consulta, notas..."></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-save" onclick="submitModal()">💾 Guardar</button>
                <button type="button" class="btn btn-danger" id="btnCancelCita" style="display:none;" onclick="cancelCita()">Cancelar cita</button>
                <button type="button" class="btn btn-danger" id="btnDeleteCita" style="display:none;" onclick="deleteCita()">🗑 Eliminar</button>
                <button type="button" class="btn btn-outline" onclick="closeModal()">Cerrar</button>
            </div>
        </div>
    </div>

    @php
        $prePatientData = null;
        if ($prePatient) {
            $prePatientData = [
                'id' => $prePatient->id,
                'nombre' => $prePatient->nombre,
                'numero_expediente' => $prePatient->numero_expediente,
                'telefono' => $prePatient->telefono,
            ];
        }
    @endphp

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/locales/es.global.min.js"></script>
    <script>
        const csrfToken = "{{ csrf_token() }}";
        const baseUrl = "{{ url('/') }}";
        const routes = {
            events: "{{ route('appointment.events') }}",
            searchQuick: "{{ route('appointment.search-quick') }}",
            patientSearch: "{{ route('patients.search') }}",
            quickStore: "{{ route('appointment.quick-store') }}",
            updateDates: (id) => `${baseUrl}/appointments/${id}/dates`,
            updateDetails: (id) => `${baseUrl}/appointments/${id}/details`,
            updateStatus: (id) => `${baseUrl}/appointments/${id}/status`,
            destroy: (id) => `${baseUrl}/appointments/${id}/hard-delete`,
        };

        const prePatient = @json($prePatientData);

        let calendar;
        let mode = 'create'; // 'create' | 'edit'
        let currentAppointmentId = null;
        let selectedPatient = null;
        let patientMode = 'registrado'; // 'registrado' | 'nuevo'
        let isWalkInEditing = false;

        function toLocalInput(dateStr) {
            const d = new Date(dateStr);
            const pad = (n) => String(n).padStart(2, '0');
            return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
        }

        async function apiFetch(url, method, body) {
            const res = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: body !== undefined ? JSON.stringify(body) : undefined,
            });
            const data = await res.json().catch(() => ({}));
            if (!res.ok) {
                const msg = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'Ocurrió un error.');
                throw new Error(msg);
            }
            return data;
        }

        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('calendar');
            calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'es',
                initialView: 'timeGridWeek',
                headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay' },
                slotMinTime: '07:00:00',
                slotMaxTime: '21:00:00',
                height: 'auto',
                nowIndicator: true,
                editable: true,
                eventStartEditable: true,
                eventDurationEditable: true,
                selectable: true,
                events: function (info, successCallback, failureCallback) {
                    fetch(`${routes.events}?start=${info.startStr}&end=${info.endStr}`)
                        .then(r => r.json())
                        .then(successCallback)
                        .catch(failureCallback);
                },
                select: function (info) {
                    openCreateModal(info.startStr, info.endStr);
                    calendar.unselect();
                },
                eventClick: function (info) {
                    openEditModal(info.event);
                },
                eventDrop: function (info) {
                    persistDates(info.event, info.revert);
                },
                eventResize: function (info) {
                    persistDates(info.event, info.revert);
                },
            });
            calendar.render();

            if (prePatient) {
                openCreateModal();
                selectPatient(prePatient);
            }

            document.getElementById('citaSearch').addEventListener('input', debounce(handleCitaSearch, 300));
            document.getElementById('patientSearchInput').addEventListener('input', debounce(handlePatientSearch, 300));
            document.getElementById('serviceSelect').addEventListener('change', recalcEnd);
            document.getElementById('startInput').addEventListener('change', recalcEnd);

            document.addEventListener('click', function (e) {
                if (!e.target.closest('#searchWrapper')) document.getElementById('citaSearchResults').classList.remove('show');
                if (!e.target.closest('#patientSearchWrapper')) document.getElementById('patientSearchResults').classList.remove('show');
            });
        });

        function debounce(fn, delay) {
            let t;
            return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), delay); };
        }

        function persistDates(event, revertFn) {
            apiFetch(routes.updateDates(event.id), 'PUT', {
                appointment_date: event.start.toISOString(),
                end_date: event.end ? event.end.toISOString() : null,
            }).catch(err => {
                alert(err.message);
                revertFn();
            });
        }

        // ── Buscador superior de citas ──
        async function handleCitaSearch(e) {
            const q = e.target.value.trim();
            const box = document.getElementById('citaSearchResults');
            if (q.length < 2) { box.classList.remove('show'); return; }

            const data = await fetch(`${routes.searchQuick}?q=${encodeURIComponent(q)}`).then(r => r.json());
            box.innerHTML = '';
            if (data.length === 0) {
                box.innerHTML = '<div class="search-result-item">Sin resultados</div>';
            } else {
                data.forEach(item => {
                    const div = document.createElement('div');
                    div.className = 'search-result-item';
                    div.innerHTML = `${escapeHtml(item.label)} <div class="sr-status">${escapeHtml(item.status)}</div>`;
                    div.onclick = () => {
                        calendar.gotoDate(item.date);
                        box.classList.remove('show');
                        document.getElementById('citaSearch').value = '';
                        setTimeout(() => {
                            const ev = calendar.getEventById(item.id);
                            if (ev) openEditModal(ev);
                        }, 300);
                    };
                    box.appendChild(div);
                });
            }
            box.classList.add('show');
        }

        function escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str ?? '';
            return div.innerHTML;
        }

        // ── Modal: crear ──
        function openCreateModal(startStr, endStr) {
            mode = 'create';
            currentAppointmentId = null;
            selectedPatient = null;
            patientMode = 'registrado';
            resetForm();

            document.getElementById('modalTitle').textContent = 'Nueva Cita';
            document.getElementById('patientSection').style.display = 'block';
            document.getElementById('patientReadonlySection').style.display = 'none';
            document.getElementById('statusSelectWrap').classList.remove('show');
            document.getElementById('btnCancelCita').style.display = 'none';
            document.getElementById('btnDeleteCita').style.display = 'none';
            setPatientMode('registrado');

            const start = startStr ? new Date(startStr) : new Date();
            document.getElementById('startInput').value = toLocalInput(start);
            if (endStr) {
                document.getElementById('endInput').value = toLocalInput(new Date(endStr));
            }

            document.getElementById('modalOverlay').classList.add('open');
        }

        function resetForm() {
            document.getElementById('formError').style.display = 'none';
            document.getElementById('serviceSelect').value = '';
            document.getElementById('startInput').value = '';
            document.getElementById('endInput').value = '';
            document.getElementById('notesInput').value = '';
            document.getElementById('walkInNombre').value = '';
            document.getElementById('walkInTelefono').value = '';
            document.getElementById('patientSearchInput').value = '';
            document.getElementById('selectedPatientBox').style.display = 'none';
            document.getElementById('patientSearchWrapper').style.display = 'block';
            document.getElementById('serviceSelectGroup').style.display = 'block';
        }

        function setPatientMode(m) {
            patientMode = m;
            document.querySelectorAll('#patientToggle button').forEach(b => b.classList.toggle('active', b.dataset.mode === m));
            document.getElementById('registradoBlock').style.display = m === 'registrado' ? 'block' : 'none';
            document.getElementById('nuevoBlock').style.display = m === 'nuevo' ? 'block' : 'none';
            document.getElementById('serviceSelectGroup').style.display = m === 'registrado' ? 'block' : 'none';
        }

        async function handlePatientSearch(e) {
            const q = e.target.value.trim();
            const box = document.getElementById('patientSearchResults');
            if (q.length < 2) { box.classList.remove('show'); return; }

            const data = await fetch(`${routes.patientSearch}?q=${encodeURIComponent(q)}`).then(r => r.json());
            box.innerHTML = '';
            if (data.length === 0) {
                box.innerHTML = '<div class="patient-result-item">Sin resultados</div>';
            } else {
                data.forEach(p => {
                    const div = document.createElement('div');
                    div.className = 'patient-result-item';
                    div.innerHTML = `<strong>${escapeHtml(p.nombre)}</strong><br><span style="color:#999; font-size:12px;">${escapeHtml(p.numero_expediente)}${p.telefono ? ' · ' + escapeHtml(p.telefono) : ''}</span>`;
                    div.onclick = () => selectPatient(p);
                    box.appendChild(div);
                });
            }
            box.classList.add('show');
        }

        function selectPatient(p) {
            selectedPatient = p;
            document.getElementById('spName').textContent = p.nombre;
            document.getElementById('spMeta').textContent = p.numero_expediente + (p.telefono ? ' · ' + p.telefono : '');
            document.getElementById('selectedPatientBox').style.display = 'flex';
            document.getElementById('patientSearchWrapper').style.display = 'none';
            document.getElementById('patientSearchResults').classList.remove('show');
        }

        function clearSelectedPatient() {
            selectedPatient = null;
            document.getElementById('selectedPatientBox').style.display = 'none';
            document.getElementById('patientSearchWrapper').style.display = 'block';
            document.getElementById('patientSearchInput').value = '';
        }

        function recalcEnd() {
            const select = document.getElementById('serviceSelect');
            const option = select.options[select.selectedIndex];
            const startVal = document.getElementById('startInput').value;
            if (!option || !option.value || !startVal) return;
            const duration = parseInt(option.dataset.duration, 10) || 30;
            const start = new Date(startVal);
            const end = new Date(start.getTime() + duration * 60000);
            document.getElementById('endInput').value = toLocalInput(end);
        }

        // ── Modal: ver / editar cita existente ──
        function openEditModal(event) {
            mode = 'edit';
            currentAppointmentId = event.id;
            const props = event.extendedProps;
            isWalkInEditing = props.isWalkIn;

            document.getElementById('formError').style.display = 'none';
            document.getElementById('modalTitle').textContent = `Cita de ${props.patientName}`;
            document.getElementById('patientSection').style.display = props.isWalkIn ? 'block' : 'none';
            document.getElementById('patientReadonlySection').style.display = props.isWalkIn ? 'none' : 'block';

            if (props.isWalkIn) {
                setPatientMode('nuevo');
                document.getElementById('walkInNombre').value = props.nombreTemporal || '';
                document.getElementById('walkInTelefono').value = props.telefonoTemporal || '';
                document.getElementById('patientToggle').style.display = 'none';
            } else {
                document.getElementById('patientToggle').style.display = 'flex';
                document.getElementById('patientReadonlySection').innerHTML = `
                    <div class="readonly-patient">
                        <strong>${escapeHtml(props.patientName)}</strong> — ${escapeHtml(props.expediente || '')}
                        ${props.phone ? ' · ' + escapeHtml(props.phone) : ''}
                        ${props.patientId ? `<a href="${baseUrl}/patients/${props.patientId}/odontograma">Ver odontograma</a>` : ''}
                    </div>
                `;
            }

            document.getElementById('serviceSelectGroup').style.display = 'block';
            document.getElementById('serviceSelect').value = props.serviceId || '';
            document.getElementById('startInput').value = toLocalInput(event.start);
            document.getElementById('endInput').value = event.end ? toLocalInput(event.end) : '';
            document.getElementById('notesInput').value = props.notes || '';

            document.getElementById('statusSelectWrap').classList.add('show');
            document.getElementById('statusSelect').value = props.status;

            document.getElementById('btnCancelCita').style.display = props.status === 'cancelada' ? 'none' : 'inline-block';
            document.getElementById('btnDeleteCita').style.display = 'inline-block';

            document.getElementById('modalOverlay').classList.add('open');
        }

        function closeModal() {
            document.getElementById('modalOverlay').classList.remove('open');
        }

        document.getElementById('modalOverlay').addEventListener('click', (e) => {
            if (e.target.id === 'modalOverlay') closeModal();
        });

        // ── Guardar (crear o editar) ──
        async function submitModal() {
            const errorBox = document.getElementById('formError');
            errorBox.style.display = 'none';

            const start = document.getElementById('startInput').value;
            const end = document.getElementById('endInput').value;
            const notes = document.getElementById('notesInput').value.trim();
            const serviceId = document.getElementById('serviceSelect').value;

            if (!start) {
                errorBox.textContent = 'Selecciona la fecha y hora de la cita.';
                errorBox.style.display = 'block';
                return;
            }

            try {
                if (mode === 'create') {
                    const payload = {
                        appointment_date: start,
                        end_date: end || null,
                        notes: notes || null,
                    };

                    if (patientMode === 'registrado') {
                        if (!selectedPatient) {
                            errorBox.textContent = 'Selecciona un paciente registrado o cambia a "Primera vez".';
                            errorBox.style.display = 'block';
                            return;
                        }
                        if (!serviceId) {
                            errorBox.textContent = 'Selecciona un servicio.';
                            errorBox.style.display = 'block';
                            return;
                        }
                        payload.patient_id = selectedPatient.id;
                        payload.service_id = serviceId;
                    } else {
                        const nombre = document.getElementById('walkInNombre').value.trim();
                        if (!nombre) {
                            errorBox.textContent = 'Indica el nombre del paciente.';
                            errorBox.style.display = 'block';
                            return;
                        }
                        payload.nombre_temporal = nombre;
                        payload.telefono_temporal = document.getElementById('walkInTelefono').value.trim() || null;
                    }

                    const data = await apiFetch(routes.quickStore, 'POST', payload);
                    calendar.addEvent(data.event);
                    closeModal();
                } else {
                    if (!serviceId) {
                        errorBox.textContent = 'Selecciona un servicio.';
                        errorBox.style.display = 'block';
                        return;
                    }
                    const payload = {
                        service_id: serviceId,
                        appointment_date: start,
                        end_date: end || null,
                        notes: notes || null,
                    };
                    if (isWalkInEditing) {
                        payload.nombre_temporal = document.getElementById('walkInNombre').value.trim();
                        payload.telefono_temporal = document.getElementById('walkInTelefono').value.trim() || null;
                    }

                    const data = await apiFetch(routes.updateDetails(currentAppointmentId), 'PUT', payload);
                    const statusVal = document.getElementById('statusSelect').value;
                    if (statusVal !== data.event.extendedProps.status) {
                        await apiFetch(routes.updateStatus(currentAppointmentId), 'PUT', { status: statusVal });
                    }
                    calendar.refetchEvents();
                    closeModal();
                }
            } catch (err) {
                errorBox.textContent = err.message;
                errorBox.style.display = 'block';
            }
        }

        async function cancelCita() {
            if (!confirm('¿Cancelar esta cita?')) return;
            try {
                await apiFetch(routes.updateStatus(currentAppointmentId), 'PUT', { status: 'cancelada' });
                calendar.refetchEvents();
                closeModal();
            } catch (err) {
                alert(err.message);
            }
        }

        async function deleteCita() {
            if (!confirm('¿Eliminar esta cita definitivamente? Esta acción no se puede deshacer.')) return;
            try {
                await apiFetch(routes.destroy(currentAppointmentId), 'DELETE');
                calendar.refetchEvents();
                closeModal();
            } catch (err) {
                alert(err.message);
            }
        }
    </script>
</body>
</html>
