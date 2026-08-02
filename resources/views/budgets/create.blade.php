<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generar Presupuesto — {{ $patient->nombre }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background-color: #f5f5f5; }
        .navbar { background-color: #667eea; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar a { color: white; text-decoration: none; padding: 8px 16px; border-radius: 5px; transition: background-color 0.3s; }
        .navbar a:hover { background-color: rgba(255,255,255,0.2); }
        .container { max-width: 950px; margin: 30px auto; padding: 0 20px 60px; }
        .card { background: white; border-radius: 10px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 20px; }
        h2 { color: #333; margin-bottom: 20px; }
        .errors { background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .errors ul { margin: 0; padding-left: 20px; }

        .patient-box { background-color: #eef0ff; border: 1px solid #d7dcff; border-radius: 8px; padding: 16px 18px; margin-bottom: 20px; }
        .patient-box .pb-nombre { font-weight: bold; color: #333; font-size: 16px; margin-bottom: 4px; }
        .patient-box .pb-meta { font-size: 13px; color: #5a63b8; }

        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
        .form-group { margin-bottom: 18px; }
        label { display: block; margin-bottom: 6px; color: #444; font-weight: 500; font-size: 13.5px; }
        input, textarea, select { width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; font-family: inherit; }
        input:focus, textarea:focus, select:focus { outline: none; border-color: #667eea; box-shadow: 0 0 4px rgba(102,126,234,0.3); }
        textarea { resize: vertical; min-height: 60px; }
        .hint { font-size: 12px; color: #999; margin-top: 4px; display: block; }

        .descuento-wrapper { position: relative; }
        .descuento-wrapper input { padding-right: 34px; }
        .descuento-suffix { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #999; font-weight: bold; font-size: 13px; pointer-events: none; }

        .section-title { color: #667eea; font-size: 12px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin: 26px 0 14px; padding-bottom: 6px; border-bottom: 2px solid #f0f0f0; }

        /* ── Renglón de concepto ── */
        .item-row { border: 1px solid #e5e5e5; border-radius: 8px; padding: 16px; margin-bottom: 14px; background-color: #fafafa; }
        .item-row-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
        .item-row-title { font-weight: bold; color: #667eea; font-size: 13px; }
        .icon-btn { border: 1px solid #ddd; background: white; width: 28px; height: 28px; border-radius: 5px; cursor: pointer; font-size: 13px; display: flex; align-items: center; justify-content: center; }
        .icon-btn:hover { background: #f0f0f0; }
        .icon-btn.danger { color: #dc3545; border-color: #f3c2c8; }
        .icon-btn.danger:hover { background: #fdecea; }

        .t-search-wrapper { position: relative; }
        .t-search-input { width: 100%; padding: 11px 14px; border: 2px solid #d7dcff; border-radius: 8px; font-size: 14px; }
        .t-search-input:focus { border-color: #667eea; }
        .t-search-results { position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #ddd; border-radius: 0 0 8px 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.12); z-index: 50; max-height: 260px; overflow-y: auto; display: none; }
        .t-search-results.show { display: block; }
        .t-search-item { padding: 10px 14px; cursor: pointer; border-bottom: 1px solid #f5f5f5; }
        .t-search-item:hover { background-color: #f0f4ff; }
        .t-search-item:last-child { border-bottom: none; }
        .tsi-nombre { font-weight: bold; color: #333; font-size: 13.5px; }
        .tsi-meta { font-size: 12px; color: #999; margin-top: 2px; }
        .tsi-costo { font-size: 12px; color: #28a745; font-weight: bold; float: right; }
        .t-search-empty { padding: 14px; color: #999; font-size: 13px; text-align: center; }

        .item-preview { background: white; border: 1px solid #d7dcff; border-radius: 8px; padding: 14px 16px; }
        .ip-top { display: flex; justify-content: space-between; align-items: flex-start; gap: 10px; margin-bottom: 10px; }
        .ip-nombre { font-weight: bold; color: #333; font-size: 15px; }
        .ip-abrev { color: #667eea; font-family: monospace; font-size: 12px; background-color: #eef0ff; padding: 2px 6px; border-radius: 4px; margin-right: 6px; }
        .ip-change { font-size: 12px; color: #667eea; font-weight: bold; text-decoration: none; cursor: pointer; white-space: nowrap; }
        .ip-change:hover { text-decoration: underline; }
        .ip-grid { display: grid; grid-template-columns: 120px 1fr 1fr; gap: 14px; align-items: end; }
        .ip-grid label { margin-bottom: 4px; font-size: 12px; }
        .ip-static { font-size: 14px; color: #333; padding: 10px 12px; background: #f8f9fa; border-radius: 5px; }
        .ip-cantidad-input { width: 100%; }

        .btn-add-item { width: 100%; padding: 12px; border: 2px dashed #c8ccf5; background: #f8f9ff; color: #667eea; border-radius: 8px; font-weight: bold; font-size: 14px; cursor: pointer; margin-top: 4px; }
        .btn-add-item:hover { background: #eef0ff; }

        /* ── Resumen de totales ── */
        .totales-box { background: #f8f9fa; border-radius: 8px; padding: 18px 20px; margin-top: 8px; }
        .totales-row { display: flex; justify-content: space-between; font-size: 14px; color: #555; padding: 5px 0; }
        .totales-row.descuento { color: #28a745; }
        .totales-row.total { font-size: 18px; font-weight: bold; color: #dc3545; border-top: 2px solid #eee; margin-top: 8px; padding-top: 12px; }
        .totales-nota { font-size: 11.5px; color: #999; margin-top: 8px; }

        .btn-group { display: flex; gap: 12px; margin-top: 30px; }
        .btn { padding: 12px 24px; border: none; border-radius: 6px; font-size: 15px; font-weight: bold; cursor: pointer; transition: all 0.3s; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
        .btn-save { background-color: #667eea; color: white; flex: 1; }
        .btn-save:hover { background-color: #764ba2; }
        .btn-cancel { background-color: #6c757d; color: white; }
        .btn-cancel:hover { background-color: #5a6268; }
        .form-error { color: #dc3545; font-size: 12.5px; margin-top: 10px; }

        @media (max-width: 640px) { .form-row, .ip-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>💰 Generar Presupuesto</h1>
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

        <form action="{{ route('budgets.store', $patient->id) }}" method="POST" id="budgetForm">
            @csrf

            <div class="card">
                <h2>💰 Nuevo Presupuesto</h2>

                <div class="patient-box">
                    <div class="pb-nombre">{{ $patient->nombre }}</div>
                    <div class="pb-meta">
                        {{ $patient->numero_expediente }}
                        @if ($patient->edad > 0) · {{ $patient->edad }} años @endif
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="fecha">Fecha *</label>
                        <input type="date" id="fecha" name="fecha" value="{{ old('fecha', $prefillFecha) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="diagnostico">Diagnóstico de presunción</label>
                        <input type="text" id="diagnostico" name="diagnostico"
                               value="{{ old('diagnostico', $prefillDiagnostico ?? null) }}"
                               placeholder="Ej: Endodoncias con fines protésicos">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="descuento_porcentaje">Descuento sobre el total</label>
                        <div class="descuento-wrapper">
                            <input type="number" id="descuento_porcentaje" name="descuento_porcentaje"
                                   value="{{ old('descuento_porcentaje', $prefillDescuento ?? '') }}"
                                   min="0" max="100" step="0.01" placeholder="Ej. 10"
                                   oninput="recalcularTotales()">
                            <span class="descuento-suffix">%</span>
                        </div>
                        <span class="hint">Déjalo vacío si no aplica descuento.</span>
                    </div>
                    <div class="form-group">
                        <label for="promocion_bienvenida">Promoción de bienvenida (opcional)</label>
                        <textarea id="promocion_bienvenida" name="promocion_bienvenida"
                                  placeholder="Ej: 10% de descuento en tu primera limpieza dental">{{ old('promocion_bienvenida', $prefillPromocion ?? null) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="section-title">Conceptos del presupuesto</div>
                <p class="hint" style="margin-bottom:14px;">
                    Selecciona el tratamiento del catálogo — el costo se toma automáticamente. Solo indica la cantidad.
                </p>
                <div id="itemsContainer"></div>
                <button type="button" class="btn-add-item" onclick="addItemRow()">＋ Agregar concepto</button>
                <div class="form-error" id="itemsError" style="display:none;"></div>

                <div class="totales-box">
                    <div class="totales-row">
                        <span>Subtotal</span>
                        <span id="totalSubtotal">$0.00</span>
                    </div>
                    <div class="totales-row descuento" id="totalDescuentoRow" style="display:none;">
                        <span id="totalDescuentoLabel">Descuento</span>
                        <span id="totalDescuentoMonto">-$0.00</span>
                    </div>
                    <div class="totales-row total">
                        <span>TOTAL</span>
                        <span id="totalFinal">$0.00</span>
                    </div>
                    <div class="totales-nota" id="totalNota" style="display:none;">
                        Hay conceptos "Por cotizar" que no están incluidos en este total.
                    </div>
                </div>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn btn-save">💾 Guardar Presupuesto</button>
                <a href="{{ route('patients.show', $patient->id) }}" class="btn btn-cancel">Cancelar</a>
            </div>
        </form>
    </div>

    <script>
        const catalog = @json($catalog);
        const prefillItems = @json($prefillItems);
        let rowCounter = 0;

        function escapeHtml(str) {
            if (str === null || str === undefined) return '';
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        function money(n) {
            return '$' + Number(n).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function filterCatalog(q) {
            q = q.toLowerCase().trim();
            if (!q) return catalog;
            return catalog.filter(t =>
                t.nombre.toLowerCase().includes(q) ||
                t.abreviacion.toLowerCase().includes(q) ||
                t.categoria.toLowerCase().includes(q)
            );
        }

        function buildRow(index, data = {}) {
            const wrapper = document.createElement('div');
            wrapper.className = 'item-row';
            wrapper.dataset.rowIndex = index;
            wrapper.dataset.treatmentId = data.treatment_id || '';
            wrapper.dataset.costoUnitario = (data.costo_unitario ?? '');

            wrapper.innerHTML = `
                <div class="item-row-header">
                    <span class="item-row-title">Concepto ${index + 1}</span>
                    <button type="button" class="icon-btn danger" title="Eliminar" onclick="removeRow(this)">🗑</button>
                </div>
                <div class="item-body"></div>
            `;

            const body = wrapper.querySelector('.item-body');
            if (data.treatment_id) {
                body.appendChild(buildPreview(wrapper, data));
            } else {
                body.appendChild(buildSearch(wrapper));
            }

            return wrapper;
        }

        function buildSearch(rowEl) {
            const el = document.createElement('div');
            el.className = 't-search-wrapper';
            el.innerHTML = `
                <input type="text" class="t-search-input" placeholder="Buscar tratamiento por nombre, abreviación o categoría..." autocomplete="off">
                <div class="t-search-results"></div>
            `;

            const input = el.querySelector('.t-search-input');
            const results = el.querySelector('.t-search-results');

            function renderResults(list) {
                results.innerHTML = '';
                if (list.length === 0) {
                    results.innerHTML = '<div class="t-search-empty">Sin resultados en el catálogo</div>';
                } else {
                    list.forEach(t => {
                        const item = document.createElement('div');
                        item.className = 't-search-item';
                        const costoTxt = t.costo !== null ? money(t.costo) : 'Por cotizar';
                        item.innerHTML = `
                            <span class="tsi-costo">${costoTxt}</span>
                            <div class="tsi-nombre"><span class="ip-abrev">${escapeHtml(t.abreviacion)}</span>${escapeHtml(t.nombre)}</div>
                            <div class="tsi-meta">${escapeHtml(t.categoria)}</div>
                        `;
                        item.addEventListener('click', () => selectTreatment(rowEl, t));
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
            el.className = 'item-preview';
            const cantidad = data.cantidad || 1;
            const costoTxt = data.costo_unitario !== null && data.costo_unitario !== undefined
                ? money(data.costo_unitario) : 'Por cotizar';

            el.innerHTML = `
                <input type="hidden" name="items[${rowEl.dataset.rowIndex}][treatment_id]" value="${data.treatment_id}">
                <div class="ip-top">
                    <div class="ip-nombre">${escapeHtml(data.concepto)}</div>
                    <a class="ip-change" onclick="changeTreatment(this)">Cambiar</a>
                </div>
                <div class="ip-grid">
                    <div class="form-group" style="margin-bottom:0;">
                        <label>Cantidad</label>
                        <input type="number" class="ip-cantidad-input" min="1" step="1"
                               name="items[${rowEl.dataset.rowIndex}][cantidad]"
                               value="${cantidad}" oninput="recalcularTotales()">
                    </div>
                    <div>
                        <label>Costo unitario</label>
                        <div class="ip-static">${costoTxt}</div>
                    </div>
                    <div>
                        <label>Costo global</label>
                        <div class="ip-static costo-global-preview">—</div>
                    </div>
                </div>
                <div class="mp-readonly-badge" style="font-size:10.5px; color:#999; margin-top:8px;">🔒 Precio tomado del catálogo de tratamientos</div>
            `;
            return el;
        }

        function selectTreatment(rowEl, t) {
            rowEl.dataset.treatmentId = t.id;
            rowEl.dataset.costoUnitario = (t.costo ?? '');
            const body = rowEl.querySelector('.item-body');
            body.innerHTML = '';
            body.appendChild(buildPreview(rowEl, {
                treatment_id: t.id,
                concepto: t.nombre,
                costo_unitario: t.costo,
                cantidad: 1,
            }));
            recalcularTotales();
        }

        function changeTreatment(link) {
            const rowEl = link.closest('.item-row');
            rowEl.dataset.treatmentId = '';
            rowEl.dataset.costoUnitario = '';
            const body = rowEl.querySelector('.item-body');
            body.innerHTML = '';
            const searchEl = buildSearch(rowEl);
            body.appendChild(searchEl);
            searchEl.querySelector('.t-search-input').focus();
            recalcularTotales();
        }

        function addItemRow(data = {}) {
            const container = document.getElementById('itemsContainer');
            const row = buildRow(rowCounter, data);
            container.appendChild(row);
            rowCounter++;

            if (!data.treatment_id) {
                row.querySelector('.t-search-input')?.focus();
            }
            recalcularTotales();
        }

        function removeRow(btn) {
            const container = document.getElementById('itemsContainer');
            if (container.children.length <= 1) {
                alert('El presupuesto debe tener al menos un concepto.');
                return;
            }
            btn.closest('.item-row').remove();
            recalcularTotales();
        }

        function recalcularTotales() {
            let subtotal = 0;
            let hayPorCotizar = false;

            document.querySelectorAll('#itemsContainer .item-row').forEach(row => {
                const costoRaw = row.dataset.costoUnitario;
                const cantidadInput = row.querySelector('.ip-cantidad-input');
                const cantidad = cantidadInput ? (parseInt(cantidadInput.value, 10) || 1) : 1;
                const previewEl = row.querySelector('.costo-global-preview');

                if (costoRaw === '' || costoRaw === undefined) {
                    if (previewEl) previewEl.textContent = row.dataset.treatmentId ? 'Por cotizar' : '—';
                    if (row.dataset.treatmentId) hayPorCotizar = true;
                    return;
                }

                const costo = parseFloat(costoRaw);
                const global = costo * cantidad;
                subtotal += global;
                if (previewEl) previewEl.textContent = money(global);
            });

            const descuentoInput = document.getElementById('descuento_porcentaje');
            const descuentoPct = parseFloat(descuentoInput.value);
            const tieneDescuento = descuentoInput.value !== '' && !isNaN(descuentoPct) && descuentoPct > 0;
            const descuentoMonto = tieneDescuento ? subtotal * (descuentoPct / 100) : 0;
            const total = subtotal - descuentoMonto;

            document.getElementById('totalSubtotal').textContent = money(subtotal);
            document.getElementById('totalFinal').textContent = money(total);

            const descRow = document.getElementById('totalDescuentoRow');
            if (tieneDescuento) {
                descRow.style.display = 'flex';
                document.getElementById('totalDescuentoLabel').textContent = `Descuento (${descuentoPct}%)`;
                document.getElementById('totalDescuentoMonto').textContent = '-' + money(descuentoMonto);
            } else {
                descRow.style.display = 'none';
            }

            document.getElementById('totalNota').style.display = hayPorCotizar ? 'block' : 'none';
        }

        document.getElementById('budgetForm').addEventListener('submit', function (e) {
            const errorBox = document.getElementById('itemsError');
            errorBox.style.display = 'none';

            const rows = document.querySelectorAll('#itemsContainer .item-row');
            const missing = [...rows].some(r => !r.dataset.treatmentId);

            if (rows.length === 0 || missing) {
                e.preventDefault();
                errorBox.textContent = 'Selecciona un tratamiento del catálogo en cada renglón antes de guardar.';
                errorBox.style.display = 'block';
                errorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });

        if (prefillItems && prefillItems.length > 0) {
            prefillItems.forEach(item => addItemRow(item));
        } else {
            addItemRow();
        }
    </script>
</body>
</html>
