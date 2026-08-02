<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generar Receta — {{ $patient->nombre }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background-color: #f5f5f5; }
        .navbar { background-color: #667eea; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar a { color: white; text-decoration: none; padding: 8px 16px; border-radius: 5px; transition: background-color 0.3s; }
        .navbar a:hover { background-color: rgba(255,255,255,0.2); }
        .container { max-width: 900px; margin: 30px auto; padding: 0 20px 60px; }
        .card { background: white; border-radius: 10px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 20px; }
        h2 { color: #333; margin-bottom: 20px; }
        .errors { background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .errors ul { margin: 0; padding-left: 20px; }

        .patient-box { background-color: #eef0ff; border: 1px solid #d7dcff; border-radius: 8px; padding: 16px 18px; margin-bottom: 20px; }
        .patient-box .pb-nombre { font-weight: bold; color: #333; font-size: 16px; margin-bottom: 4px; }
        .patient-box .pb-meta { font-size: 13px; color: #5a63b8; }

        .form-group { margin-bottom: 18px; }
        label { display: block; margin-bottom: 6px; color: #444; font-weight: 500; font-size: 13.5px; }
        input, textarea, select { width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; font-family: inherit; }
        input:focus, textarea:focus, select:focus { outline: none; border-color: #667eea; box-shadow: 0 0 4px rgba(102,126,234,0.3); }
        textarea { resize: vertical; min-height: 60px; }
        .hint { font-size: 12px; color: #999; margin-top: 4px; display: block; }

        .section-title { color: #667eea; font-size: 12px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin: 26px 0 14px; padding-bottom: 6px; border-bottom: 2px solid #f0f0f0; }

        /* ── Renglón de medicamento ── */
        .med-row { border: 1px solid #e5e5e5; border-radius: 8px; padding: 16px; margin-bottom: 14px; background-color: #fafafa; }
        .med-row-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
        .med-row-title { font-weight: bold; color: #667eea; font-size: 13px; }
        .icon-btn { border: 1px solid #ddd; background: white; width: 28px; height: 28px; border-radius: 5px; cursor: pointer; font-size: 13px; display: flex; align-items: center; justify-content: center; }
        .icon-btn:hover { background: #f0f0f0; }
        .icon-btn.danger { color: #dc3545; border-color: #f3c2c8; }
        .icon-btn.danger:hover { background: #fdecea; }

        /* Buscador (antes de seleccionar) */
        .med-search-wrapper { position: relative; }
        .med-search-input { width: 100%; padding: 11px 14px; border: 2px solid #d7dcff; border-radius: 8px; font-size: 14px; }
        .med-search-input:focus { border-color: #667eea; }
        .med-search-results { position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #ddd; border-radius: 0 0 8px 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.12); z-index: 50; max-height: 260px; overflow-y: auto; display: none; }
        .med-search-results.show { display: block; }
        .med-search-item { padding: 10px 14px; cursor: pointer; border-bottom: 1px solid #f5f5f5; }
        .med-search-item:hover { background-color: #f0f4ff; }
        .med-search-item:last-child { border-bottom: none; }
        .msi-nombre { font-weight: bold; color: #333; font-size: 13.5px; }
        .msi-meta { font-size: 12px; color: #999; margin-top: 2px; }
        .med-search-empty { padding: 14px; color: #999; font-size: 13px; text-align: center; }

        /* Vista de solo lectura (después de seleccionar) */
        .med-preview { background: white; border: 1px solid #d7dcff; border-radius: 8px; padding: 14px 16px; }
        .mp-top { display: flex; justify-content: space-between; align-items: flex-start; gap: 10px; margin-bottom: 8px; }
        .mp-nombre { font-weight: bold; color: #333; font-size: 15px; }
        .mp-principio { color: #667eea; font-weight: normal; }
        .mp-change { font-size: 12px; color: #667eea; font-weight: bold; text-decoration: none; cursor: pointer; white-space: nowrap; }
        .mp-change:hover { text-decoration: underline; }
        .mp-row { font-size: 13px; color: #555; margin-bottom: 4px; }
        .mp-label { color: #999; text-transform: uppercase; font-size: 10.5px; letter-spacing: 0.4px; display: block; margin-bottom: 2px; }
        .mp-readonly-badge { display: inline-block; font-size: 10.5px; color: #999; margin-top: 6px; }

        .btn-add-med { width: 100%; padding: 12px; border: 2px dashed #c8ccf5; background: #f8f9ff; color: #667eea; border-radius: 8px; font-weight: bold; font-size: 14px; cursor: pointer; margin-top: 4px; }
        .btn-add-med:hover { background: #eef0ff; }

        .btn-group { display: flex; gap: 12px; margin-top: 30px; }
        .btn { padding: 12px 24px; border: none; border-radius: 6px; font-size: 15px; font-weight: bold; cursor: pointer; transition: all 0.3s; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
        .btn-save { background-color: #667eea; color: white; flex: 1; }
        .btn-save:hover { background-color: #764ba2; }
        .btn-cancel { background-color: #6c757d; color: white; }
        .btn-cancel:hover { background-color: #5a6268; }
        .form-error { color: #dc3545; font-size: 12.5px; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>📝 Generar Receta</h1>
        <a href="{{ route('patients.show', $patient->id) }}">← Volver al expediente</a>
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

        <form action="{{ route('prescriptions.store', $patient->id) }}" method="POST" id="prescriptionForm">
            @csrf

            <div class="card">
                <h2>💊 Nueva Receta Médica</h2>

                <div class="patient-box">
                    <div class="pb-nombre">{{ $patient->nombre }}</div>
                    <div class="pb-meta">
                        {{ $patient->numero_expediente }}
                        @if ($patient->edad > 0) · {{ $patient->edad }} años @endif
                    </div>
                </div>

                <div class="form-group">
                    <label for="fecha_emision">Fecha de emisión *</label>
                    <input type="date" id="fecha_emision" name="fecha_emision" value="{{ old('fecha_emision', $prefillFecha) }}" required>
                </div>

                <div class="form-group">
                    <label for="alergias">Alergias</label>
                    <textarea id="alergias" name="alergias">{{ old('alergias', $prefillAlergias) }}</textarea>
                    <span class="hint">Se mostrará tal cual en la receta. Si el paciente no tiene alergias conocidas, deja el texto "Interrogadas y negadas."</span>
                </div>
            </div>

            <div class="card">
                <div class="section-title">Medicamentos</div>
                <p class="hint" style="margin-bottom:14px;">
                    Solo selecciona el medicamento del catálogo — la presentación e indicaciones se cargan automáticamente.
                </p>
                <div id="itemsContainer"></div>
                <button type="button" class="btn-add-med" onclick="addMedRow()">＋ Agregar medicamento</button>
                <div class="form-error" id="itemsError" style="display:none;"></div>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn btn-save">💾 Guardar Receta</button>
                <a href="{{ route('patients.show', $patient->id) }}" class="btn btn-cancel">Cancelar</a>
            </div>
        </form>
    </div>

    <script>
        // Catálogo completo precargado: la búsqueda es instantánea, sin
        // esperar respuestas del servidor por cada tecla.
        const catalog = @json($catalog);
        const prefillItems = @json($prefillItems);
        let rowCounter = 0;

        function escapeHtml(str) {
            if (str === null || str === undefined) return '';
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        function filterCatalog(q) {
            q = q.toLowerCase().trim();
            if (!q) return catalog;
            return catalog.filter(m =>
                m.nombre.toLowerCase().includes(q) ||
                (m.principio_activo || '').toLowerCase().includes(q)
            );
        }

        function buildRow(index, data = {}) {
            const wrapper = document.createElement('div');
            wrapper.className = 'med-row';
            wrapper.dataset.rowIndex = index;
            wrapper.dataset.medicationId = data.medication_id || '';

            wrapper.innerHTML = `
                <div class="med-row-header">
                    <span class="med-row-title">Medicamento ${index + 1}</span>
                    <button type="button" class="icon-btn danger" title="Eliminar" onclick="removeRow(this)">🗑</button>
                </div>
                <div class="med-body"></div>
            `;

            const body = wrapper.querySelector('.med-body');
            if (data.medication_id) {
                body.appendChild(buildPreview(wrapper, data));
            } else {
                body.appendChild(buildSearch(wrapper));
            }

            return wrapper;
        }

        function buildSearch(rowEl) {
            const el = document.createElement('div');
            el.className = 'med-search-wrapper';
            el.innerHTML = `
                <input type="text" class="med-search-input" placeholder="Buscar medicamento por nombre o principio activo..." autocomplete="off">
                <div class="med-search-results"></div>
            `;

            const input = el.querySelector('.med-search-input');
            const results = el.querySelector('.med-search-results');

            function renderResults(list) {
                results.innerHTML = '';
                if (list.length === 0) {
                    results.innerHTML = '<div class="med-search-empty">Sin resultados en el catálogo</div>';
                } else {
                    list.forEach(m => {
                        const item = document.createElement('div');
                        item.className = 'med-search-item';
                        item.innerHTML = `
                            <div class="msi-nombre">${escapeHtml(m.nombre)}${m.principio_activo ? ' <span style="color:#999; font-weight:normal;">(' + escapeHtml(m.principio_activo) + ')</span>' : ''}</div>
                            <div class="msi-meta">${escapeHtml(m.presentacion || '')}</div>
                        `;
                        item.addEventListener('click', () => selectMedication(rowEl, m));
                        results.appendChild(item);
                    });
                }
                results.classList.add('show');
            }

            input.addEventListener('focus', () => renderResults(filterCatalog(input.value)));
            input.addEventListener('input', () => renderResults(filterCatalog(input.value)));
            document.addEventListener('click', (e) => {
                if (!el.contains(e.target)) results.classList.remove('show');
            });

            return el;
        }

        function buildPreview(rowEl, data) {
            const el = document.createElement('div');
            el.className = 'med-preview';
            el.innerHTML = `
                <input type="hidden" name="items[${rowEl.dataset.rowIndex}][medication_id]" value="${data.medication_id}">
                <div class="mp-top">
                    <div class="mp-nombre">
                        ${escapeHtml(data.nombre_comercial)}
                        ${data.principio_activo ? '<span class="mp-principio"> (' + escapeHtml(data.principio_activo) + ')</span>' : ''}
                    </div>
                    <a class="mp-change" onclick="changeMedication(this)">Cambiar</a>
                </div>
                ${data.presentacion ? `
                <div class="mp-row">
                    <span class="mp-label">Presentación</span>
                    ${escapeHtml(data.presentacion)}
                </div>` : ''}
                <div class="mp-row">
                    <span class="mp-label">Indicaciones</span>
                    ${escapeHtml(data.indicaciones || 'Sin indicaciones registradas en el catálogo')}
                </div>
                <div class="form-group" style="margin-top:12px; margin-bottom:0;">
                    <label style="margin-bottom:4px;">Observaciones (opcional)</label>
                    <textarea name="items[${rowEl.dataset.rowIndex}][observaciones]" placeholder="Notas específicas para este paciente...">${escapeHtml(data.observaciones || '')}</textarea>
                </div>
                <div class="mp-readonly-badge">🔒 Datos tomados del catálogo de medicamentos</div>
            `;
            return el;
        }

        function selectMedication(rowEl, m) {
            rowEl.dataset.medicationId = m.id;
            const body = rowEl.querySelector('.med-body');
            body.innerHTML = '';
            body.appendChild(buildPreview(rowEl, {
                medication_id: m.id,
                nombre_comercial: m.nombre,
                principio_activo: m.principio_activo,
                presentacion: m.presentacion,
                indicaciones: m.indicaciones,
                observaciones: '',
            }));
        }

        function changeMedication(link) {
            const rowEl = link.closest('.med-row');
            rowEl.dataset.medicationId = '';
            const body = rowEl.querySelector('.med-body');
            body.innerHTML = '';
            const searchEl = buildSearch(rowEl);
            body.appendChild(searchEl);
            searchEl.querySelector('.med-search-input').focus();
        }

        function addMedRow(data = {}) {
            const container = document.getElementById('itemsContainer');
            const row = buildRow(rowCounter, data);
            container.appendChild(row);
            rowCounter++;

            if (!data.medication_id) {
                row.querySelector('.med-search-input')?.focus();
            }
        }

        function removeRow(btn) {
            const container = document.getElementById('itemsContainer');
            if (container.children.length <= 1) {
                alert('La receta debe tener al menos un medicamento.');
                return;
            }
            btn.closest('.med-row').remove();
        }

        document.getElementById('prescriptionForm').addEventListener('submit', function (e) {
            const errorBox = document.getElementById('itemsError');
            errorBox.style.display = 'none';

            const rows = document.querySelectorAll('#itemsContainer .med-row');
            const missing = [...rows].some(r => !r.dataset.medicationId);

            if (rows.length === 0 || missing) {
                e.preventDefault();
                errorBox.textContent = 'Selecciona un medicamento del catálogo en cada renglón antes de guardar.';
                errorBox.style.display = 'block';
                errorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });

        if (prefillItems && prefillItems.length > 0) {
            prefillItems.forEach(item => addMedRow(item));
        } else {
            addMedRow();
        }
    </script>
</body>
</html>
