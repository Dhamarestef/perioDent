<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Odontograma — {{ $patient->nombre }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5; }

        .navbar { background-color: #667eea; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar a, .navbar button { color: white; text-decoration: none; padding: 8px 16px; border-radius: 5px; border: none; cursor: pointer; background: none; font-size: 14px; transition: background-color 0.3s; }
        .navbar a:hover, .navbar button:hover { background-color: rgba(255,255,255,0.2); }

        .container { max-width: 1100px; margin: 30px auto; padding: 0 20px 60px; }

        .patient-card { background: white; border-radius: 10px; padding: 22px 26px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 24px; display: flex; align-items: center; gap: 18px; flex-wrap: wrap; }
        .patient-avatar { width: 56px; height: 56px; border-radius: 50%; background-color: #667eea; color: white; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: bold; flex-shrink: 0; }
        .patient-details h2 { font-size: 19px; color: #333; margin-bottom: 4px; }
        .patient-meta { display: flex; gap: 18px; flex-wrap: wrap; font-size: 13px; color: #777; }
        .patient-meta span strong { color: #444; }
        .patient-back { margin-left: auto; }
        .patient-back a { background-color: #6c757d; color: white; padding: 9px 18px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: bold; }
        .patient-back a:hover { background-color: #5a6268; }

        .odonto-card { background: white; border-radius: 10px; padding: 26px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
        .odonto-card h3 { color: #333; font-size: 17px; margin-bottom: 4px; }
        .odonto-sub { color: #999; font-size: 13px; margin-bottom: 22px; }

        .arch-label { font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #999; margin-bottom: 8px; font-weight: bold; }
        .teeth-row { display: flex; gap: 4px; margin-bottom: 26px; overflow-x: auto; padding-bottom: 6px; }
        .teeth-row .divider { width: 1px; background-color: #ddd; margin: 0 4px; flex-shrink: 0; }

        .tooth-box { width: 54px; min-width: 54px; height: 64px; border: 1.5px solid #ccc; border-radius: 6px; background: white; display: flex; flex-direction: column; align-items: center; justify-content: center; cursor: pointer; transition: all 0.15s; position: relative; }
        .tooth-box:hover { border-color: #667eea; transform: translateY(-2px); box-shadow: 0 3px 8px rgba(102,126,234,0.2); }

        .tooth-box.estado-pendiente { background-color: #fff3cd; border-color: #f0a500; }
        .tooth-box.estado-finalizado { background-color: #d4edda; border-color: #28a745; }
        .tooth-box.estado-ausente { background-color: #f2f2f2; }

        .tooth-abbrs { font-size: 8.5px; color: #667eea; font-weight: bold; height: 11px; max-width: 50px; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; margin-bottom: 2px; }
        .tooth-number { font-weight: bold; font-size: 14px; color: #333; position: relative; }
        .tooth-box.estado-ausente .tooth-number { color: #999; }
        .tooth-box.estado-ausente .tooth-number::after {
            content: ''; position: absolute; left: -6px; right: -6px; top: 50%; border-top: 2px solid #999; transform: rotate(-18deg);
        }

        .legend { display: flex; gap: 22px; flex-wrap: wrap; margin-top: 6px; padding-top: 18px; border-top: 1px solid #f0f0f0; }
        .legend-item { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #555; }
        .legend-swatch { width: 18px; height: 18px; border-radius: 4px; border: 1.5px solid #ccc; }
        .legend-swatch.pendiente { background-color: #fff3cd; border-color: #f0a500; }
        .legend-swatch.finalizado { background-color: #d4edda; border-color: #28a745; }
        .legend-swatch.ausente { background-color: #f2f2f2; }
        .legend-swatch.normal { background-color: white; }

        /* ── Modal ── */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 1000; align-items: flex-start; justify-content: center; padding: 40px 20px; overflow-y: auto; }
        .modal-overlay.open { display: flex; }
        .modal-box { background: white; border-radius: 10px; width: 100%; max-width: 480px; box-shadow: 0 10px 40px rgba(0,0,0,0.3); }
        .modal-header { display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-bottom: 1px solid #f0f0f0; }
        .modal-header h3 { color: #333; font-size: 18px; }
        .modal-close { background: none; border: none; font-size: 20px; color: #999; cursor: pointer; line-height: 1; padding: 4px 8px; }
        .modal-close:hover { color: #333; }
        .modal-body { padding: 22px 24px; max-height: 70vh; overflow-y: auto; }

        .estado-buttons { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-bottom: 24px; }
        .estado-btn { padding: 10px 8px; border: 2px solid #ddd; border-radius: 8px; background: white; cursor: pointer; font-size: 13px; font-weight: bold; color: #555; transition: all 0.15s; }
        .estado-btn:hover { border-color: #667eea; }
        .estado-btn.active { border-color: #667eea; background-color: #eef0ff; color: #4a4db5; }
        .estado-btn.active[data-estado="pendiente"] { border-color: #f0a500; background-color: #fff3cd; color: #856404; }
        .estado-btn.active[data-estado="finalizado"] { border-color: #28a745; background-color: #d4edda; color: #155724; }
        .estado-btn.active[data-estado="ausente"] { border-color: #999; background-color: #f2f2f2; color: #555; }

        .section-label { font-size: 12px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; color: #667eea; margin-bottom: 12px; border-bottom: 1px solid #f0f0f0; padding-bottom: 6px; }

        .treatment-row { border: 1px solid #eee; border-radius: 8px; padding: 12px 14px; margin-bottom: 10px; }
        .treatment-row.finalizado { background-color: #f7fdf8; border-color: #d4edda; }
        .treatment-row-top { display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; }
        .treatment-name { font-weight: bold; font-size: 13px; color: #333; }
        .treatment-abbr { color: #667eea; }
        .treatment-date { font-size: 11px; color: #999; margin-top: 2px; }
        .treatment-obs { font-size: 12.5px; color: #666; margin-top: 6px; line-height: 1.4; }
        .treatment-actions { display: flex; gap: 6px; flex-shrink: 0; }
        .icon-btn { border: none; background: #f5f5f5; width: 26px; height: 26px; border-radius: 5px; cursor: pointer; font-size: 12px; display: flex; align-items: center; justify-content: center; }
        .icon-btn:hover { background: #e6e6e6; }
        .icon-btn.done { background-color: #d4edda; }
        .empty-treatments { color: #aaa; font-size: 13px; text-align: center; padding: 14px 0; }

        .btn-add-treatment { width: 100%; padding: 10px; border: 2px dashed #c8ccf5; background: #f8f9ff; color: #667eea; border-radius: 8px; font-weight: bold; font-size: 13px; cursor: pointer; margin-top: 4px; }
        .btn-add-treatment:hover { background: #eef0ff; }

        .add-form { border: 1px solid #eee; border-radius: 8px; padding: 16px; margin-top: 12px; display: none; }
        .add-form.show { display: block; }
        .add-form label { display: block; font-size: 12px; color: #555; margin-bottom: 5px; font-weight: 500; }
        .add-form select, .add-form textarea, .add-form input { width: 100%; padding: 9px 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 13px; margin-bottom: 12px; font-family: inherit; }
        .add-form textarea { resize: vertical; min-height: 60px; }
        .add-form .btn-row { display: flex; gap: 8px; }
        .btn-mini { flex: 1; padding: 9px; border: none; border-radius: 5px; font-size: 13px; font-weight: bold; cursor: pointer; }
        .btn-mini.save { background-color: #667eea; color: white; }
        .btn-mini.save:hover { background-color: #764ba2; }
        .btn-mini.cancel { background-color: #e9e9e9; color: #555; }
        .btn-mini.cancel:hover { background-color: #ddd; }
        .form-error { color: #dc3545; font-size: 12px; margin: -8px 0 10px; }

        .edit-form { margin-top: 8px; }
        .edit-form textarea { width: 100%; padding: 8px 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 12.5px; min-height: 50px; margin-bottom: 8px; font-family: inherit; }
        .edit-form input[type="date"] { padding: 7px 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 12px; margin-bottom: 8px; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>🦷 Odontograma</h1>
        <div>
            <a href="{{ route('patients.show', $patient->id) }}">← Expediente</a>
            <a href="{{ route('patients.index') }}">Pacientes</a>
            <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit">Cerrar sesión</button>
            </form>
        </div>
    </div>

    <div class="container">
        <div class="patient-card">
            <div class="patient-avatar">{{ strtoupper(substr($patient->nombre, 0, 1)) }}</div>
            <div class="patient-details">
                <h2>{{ $patient->nombre }}</h2>
                <div class="patient-meta">
                    <span><strong>Expediente:</strong> {{ $patient->numero_expediente }}</span>
                    <span><strong>Edad:</strong> {{ $patient->edad > 0 ? $patient->edad . ' años' : '—' }}</span>
                    <span><strong>Sexo:</strong> {{ $patient->sexo === 'F' ? 'Femenino' : 'Masculino' }}</span>
                    @if ($patient->fecha_nacimiento)
                        <span><strong>Nacimiento:</strong> {{ $patient->fecha_nacimiento->format('d/m/Y') }}</span>
                    @endif
                </div>
            </div>
            <div class="patient-back">
                <a href="{{ route('patients.index') }}">← Volver a Pacientes</a>
            </div>
        </div>

        <div class="odonto-card">
            <h3>Odontograma — dentición permanente</h3>
            <p class="odonto-sub">Haz clic sobre un diente para registrar su estado o agregar un tratamiento.</p>

            <div class="arch-label">Arcada superior</div>
            <div class="teeth-row" id="upperRow"></div>

            <div class="arch-label">Arcada inferior</div>
            <div class="teeth-row" id="lowerRow"></div>

            <div class="legend">
                <div class="legend-item"><div class="legend-swatch normal"></div> Normal</div>
                <div class="legend-item"><div class="legend-swatch pendiente"></div> Tratamiento pendiente</div>
                <div class="legend-item"><div class="legend-swatch finalizado"></div> Tratamiento finalizado</div>
                <div class="legend-item"><div class="legend-swatch ausente"></div> Diente ausente</div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal-overlay" id="modalOverlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3 id="modalTitle">Diente</h3>
                <button class="modal-close" onclick="closeModal()">✕</button>
            </div>
            <div class="modal-body">
                <div class="estado-buttons" id="estadoButtons">
                    <button type="button" class="estado-btn" data-estado="normal" onclick="setEstado('normal')">⚪ Normal</button>
                    <button type="button" class="estado-btn" data-estado="ausente" onclick="setEstado('ausente')">➖ Ausente</button>
                    <button type="button" class="estado-btn" data-estado="pendiente" onclick="setEstado('pendiente')">🟡 Pendiente</button>
                    <button type="button" class="estado-btn" data-estado="finalizado" onclick="setEstado('finalizado')">🟢 Finalizado</button>
                </div>

                <div class="section-label">Tratamientos registrados</div>
                <div id="treatmentsList"></div>

                <button type="button" class="btn-add-treatment" id="toggleAddFormBtn" onclick="toggleAddForm()">＋ Agregar tratamiento</button>

                <div class="add-form" id="addForm">
                    <label for="addTreatmentSelect">Tratamiento *</label>
                    <select id="addTreatmentSelect">
                        <option value="">-- Selecciona --</option>
                        @foreach ($catalog as $t)
                            <option value="{{ $t['id'] }}">{{ $t['abreviacion'] }} — {{ $t['nombre'] }}</option>
                        @endforeach
                    </select>

                    <label for="addFecha">Fecha de realización</label>
                    <input type="date" id="addFecha">

                    <label for="addObs">Observaciones (opcional)</label>
                    <textarea id="addObs" maxlength="500" placeholder="Notas sobre el tratamiento..."></textarea>

                    <div class="form-error" id="addFormError" style="display:none;"></div>

                    <div class="btn-row">
                        <button type="button" class="btn-mini save" onclick="submitTreatment()">Guardar</button>
                        <button type="button" class="btn-mini cancel" onclick="toggleAddForm()">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const patientId = {{ $patient->id }};
        const csrfToken = '{{ csrf_token() }}';
        const upperTeeth = @json($upperTeeth);
        const lowerTeeth = @json($lowerTeeth);
        const catalog = @json($catalog);
        let states = @json($states);
        let treatmentsByTooth = @json($treatmentsByTooth);

        let currentTooth = null;
        let editingTreatmentId = null;

        const baseUrl = '{{ url('/') }}';
        const routes = {
            updateEstado: (tooth) => `${baseUrl}/patients/${patientId}/odontograma/tooth/${tooth}`,
            storeTreatment: (tooth) => `${baseUrl}/patients/${patientId}/odontograma/tooth/${tooth}/treatments`,
            updateTreatment: (id) => `${baseUrl}/odontograma/treatments/${id}`,
            destroyTreatment: (id) => `${baseUrl}/odontograma/treatments/${id}`,
        };

        function escapeHtml(str) {
            if (str === null || str === undefined) return '';
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
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
                throw new Error(data.message || 'Ocurrió un error, intenta de nuevo.');
            }
            return data;
        }

        function buildToothBox(n) {
            const box = document.createElement('div');
            box.className = 'tooth-box';
            box.id = `tooth-${n}`;
            box.onclick = () => openModal(n);
            box.innerHTML = `
                <div class="tooth-abbrs" id="abbrs-${n}"></div>
                <div class="tooth-number">${n}</div>
            `;
            return box;
        }

        function renderRow(containerId, teeth) {
            const container = document.getElementById(containerId);
            container.innerHTML = '';
            teeth.forEach((n, i) => {
                container.appendChild(buildToothBox(n));
                if (i === 7) {
                    const divider = document.createElement('div');
                    divider.className = 'divider';
                    container.appendChild(divider);
                }
            });
        }

        function renderTooth(n) {
            const box = document.getElementById(`tooth-${n}`);
            if (!box) return;
            box.classList.remove('estado-ausente', 'estado-pendiente', 'estado-finalizado');
            const estado = states[n];
            if (estado && estado !== 'normal') {
                box.classList.add(`estado-${estado}`);
            }
            const abbrs = (treatmentsByTooth[n] || []).map(t => t.abreviacion).join(' · ');
            document.getElementById(`abbrs-${n}`).textContent = abbrs;
        }

        function renderAllTeeth() {
            [...upperTeeth, ...lowerTeeth].forEach(renderTooth);
        }

        renderRow('upperRow', upperTeeth);
        renderRow('lowerRow', lowerTeeth);
        renderAllTeeth();

        // ── Modal ──
        function openModal(n) {
            currentTooth = n;
            editingTreatmentId = null;
            document.getElementById('modalTitle').textContent = `Diente ${n}`;
            document.querySelectorAll('.estado-btn').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.estado === states[n]);
            });
            renderTreatmentsList();
            document.getElementById('addForm').classList.remove('show');
            document.getElementById('addFecha').value = new Date().toISOString().slice(0, 10);
            document.getElementById('modalOverlay').classList.add('open');
        }

        function closeModal() {
            document.getElementById('modalOverlay').classList.remove('open');
            currentTooth = null;
        }

        document.getElementById('modalOverlay').addEventListener('click', (e) => {
            if (e.target.id === 'modalOverlay') closeModal();
        });

        async function setEstado(estado) {
            if (currentTooth === null) return;
            try {
                await apiFetch(routes.updateEstado(currentTooth), 'PUT', { estado });
                states[currentTooth] = estado;
                document.querySelectorAll('.estado-btn').forEach(btn => {
                    btn.classList.toggle('active', btn.dataset.estado === estado);
                });
                renderTooth(currentTooth);
            } catch (err) {
                alert(err.message);
            }
        }

        function toggleAddForm() {
            document.getElementById('addForm').classList.toggle('show');
        }

        function renderTreatmentsList() {
            const list = document.getElementById('treatmentsList');
            const items = treatmentsByTooth[currentTooth] || [];

            if (items.length === 0) {
                list.innerHTML = '<div class="empty-treatments">Sin tratamientos registrados para este diente.</div>';
                return;
            }

            list.innerHTML = '';
            items.forEach(t => {
                const row = document.createElement('div');
                row.className = 'treatment-row' + (t.finalizado ? ' finalizado' : '');

                if (editingTreatmentId === t.id) {
                    row.innerHTML = `
                        <div class="treatment-name"><span class="treatment-abbr">${escapeHtml(t.abreviacion)}</span> — ${escapeHtml(t.nombre)}</div>
                        <div class="edit-form">
                            <input type="date" id="edit-fecha-${t.id}" value="${t.fecha_realizacion || ''}">
                            <textarea id="edit-obs-${t.id}" maxlength="500" placeholder="Observaciones...">${escapeHtml(t.observaciones)}</textarea>
                            <div class="btn-row">
                                <button type="button" class="btn-mini save" onclick="saveEdit(${t.id})">Guardar</button>
                                <button type="button" class="btn-mini cancel" onclick="cancelEdit()">Cancelar</button>
                            </div>
                        </div>
                    `;
                } else {
                    row.innerHTML = `
                        <div class="treatment-row-top">
                            <div>
                                <div class="treatment-name"><span class="treatment-abbr">${escapeHtml(t.abreviacion)}</span> — ${escapeHtml(t.nombre)}</div>
                                <div class="treatment-date">📅 ${t.fecha_display}${t.finalizado ? ' · ✓ Finalizado' : ''}</div>
                            </div>
                            <div class="treatment-actions">
                                <button type="button" class="icon-btn ${t.finalizado ? 'done' : ''}" title="Marcar finalizado" onclick="toggleFinalizado(${t.id})">✓</button>
                                <button type="button" class="icon-btn" title="Editar" onclick="startEdit(${t.id})">✏️</button>
                                <button type="button" class="icon-btn" title="Eliminar" onclick="removeTreatment(${t.id})">🗑</button>
                            </div>
                        </div>
                        ${t.observaciones ? `<div class="treatment-obs">${escapeHtml(t.observaciones)}</div>` : ''}
                    `;
                }

                list.appendChild(row);
            });
        }

        async function submitTreatment() {
            const treatmentId = document.getElementById('addTreatmentSelect').value;
            const fecha = document.getElementById('addFecha').value;
            const obs = document.getElementById('addObs').value.trim();
            const errorBox = document.getElementById('addFormError');
            errorBox.style.display = 'none';

            if (!treatmentId) {
                errorBox.textContent = 'Selecciona un tratamiento.';
                errorBox.style.display = 'block';
                return;
            }

            try {
                const data = await apiFetch(routes.storeTreatment(currentTooth), 'POST', {
                    treatment_id: treatmentId,
                    fecha_realizacion: fecha,
                    observaciones: obs || null,
                });
                treatmentsByTooth[currentTooth].push(data.treatment);
                document.getElementById('addTreatmentSelect').value = '';
                document.getElementById('addObs').value = '';
                document.getElementById('addForm').classList.remove('show');
                renderTreatmentsList();
                renderTooth(currentTooth);
            } catch (err) {
                errorBox.textContent = err.message;
                errorBox.style.display = 'block';
            }
        }

        function startEdit(id) {
            editingTreatmentId = id;
            renderTreatmentsList();
        }

        function cancelEdit() {
            editingTreatmentId = null;
            renderTreatmentsList();
        }

        async function saveEdit(id) {
            const fecha = document.getElementById(`edit-fecha-${id}`).value;
            const obs = document.getElementById(`edit-obs-${id}`).value.trim();
            try {
                const data = await apiFetch(routes.updateTreatment(id), 'PUT', {
                    fecha_realizacion: fecha,
                    observaciones: obs || null,
                });
                const list = treatmentsByTooth[currentTooth];
                const idx = list.findIndex(t => t.id === id);
                list[idx] = { ...list[idx], ...data.treatment };
                editingTreatmentId = null;
                renderTreatmentsList();
            } catch (err) {
                alert(err.message);
            }
        }

        async function toggleFinalizado(id) {
            const list = treatmentsByTooth[currentTooth];
            const item = list.find(t => t.id === id);
            try {
                const data = await apiFetch(routes.updateTreatment(id), 'PUT', {
                    finalizado: !item.finalizado,
                });
                Object.assign(item, data.treatment);
                renderTreatmentsList();
            } catch (err) {
                alert(err.message);
            }
        }

        async function removeTreatment(id) {
            if (!confirm('¿Eliminar este tratamiento del diente?')) return;
            try {
                await apiFetch(routes.destroyTreatment(id), 'DELETE');
                treatmentsByTooth[currentTooth] = treatmentsByTooth[currentTooth].filter(t => t.id !== id);
                renderTreatmentsList();
                renderTooth(currentTooth);
            } catch (err) {
                alert(err.message);
            }
        }
    </script>
</body>
</html>
