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

        .med-row { border: 1px solid #e5e5e5; border-radius: 8px; padding: 18px; margin-bottom: 16px; position: relative; background-color: #fafafa; }
        .med-row-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
        .med-row-title { font-weight: bold; color: #667eea; font-size: 13px; }
        .med-row-actions { display: flex; gap: 6px; }
        .icon-btn { border: none; background: white; border: 1px solid #ddd; width: 28px; height: 28px; border-radius: 5px; cursor: pointer; font-size: 13px; display: flex; align-items: center; justify-content: center; }
        .icon-btn:hover { background: #f0f0f0; }
        .icon-btn.danger { color: #dc3545; border-color: #f3c2c8; }
        .icon-btn.danger:hover { background: #fdecea; }

        .med-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .med-grid .full { grid-column: 1 / -1; }

        .autocomplete-wrapper { position: relative; }
        .autocomplete-results { position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #ddd; border-radius: 0 0 8px 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.12); z-index: 50; max-height: 220px; overflow-y: auto; display: none; }
        .autocomplete-results.show { display: block; }
        .autocomplete-item { padding: 9px 14px; cursor: pointer; font-size: 13px; border-bottom: 1px solid #f5f5f5; }
        .autocomplete-item:hover { background-color: #f0f4ff; }
        .autocomplete-item .ai-nombre { font-weight: bold; color: #333; }
        .autocomplete-item .ai-meta { font-size: 11.5px; color: #999; }

        .btn-add-med { width: 100%; padding: 12px; border: 2px dashed #c8ccf5; background: #f8f9ff; color: #667eea; border-radius: 8px; font-weight: bold; font-size: 14px; cursor: pointer; margin-top: 4px; }
        .btn-add-med:hover { background: #eef0ff; }

        .btn-group { display: flex; gap: 12px; margin-top: 30px; }
        .btn { padding: 12px 24px; border: none; border-radius: 6px; font-size: 15px; font-weight: bold; cursor: pointer; transition: all 0.3s; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
        .btn-save { background-color: #667eea; color: white; flex: 1; }
        .btn-save:hover { background-color: #764ba2; }
        .btn-cancel { background-color: #6c757d; color: white; }
        .btn-cancel:hover { background-color: #5a6268; }
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
                <div id="itemsContainer"></div>
                <button type="button" class="btn-add-med" onclick="addMedRow()">＋ Agregar medicamento</button>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn btn-save">💾 Guardar Receta</button>
                <a href="{{ route('patients.show', $patient->id) }}" class="btn btn-cancel">Cancelar</a>
            </div>
        </form>
    </div>

    <script>
        const searchCatalogUrl = "{{ route('medications.search-catalog') }}";
        const prefillItems = @json($prefillItems);
        let rowCounter = 0;

        function buildRow(index, data = {}) {
            const wrapper = document.createElement('div');
            wrapper.className = 'med-row';
            wrapper.dataset.rowIndex = index;
            wrapper.innerHTML = `
                <div class="med-row-header">
                    <span class="med-row-title">Medicamento</span>
                    <div class="med-row-actions">
                        <button type="button" class="icon-btn" title="Subir" onclick="moveRow(this, -1)">↑</button>
                        <button type="button" class="icon-btn" title="Bajar" onclick="moveRow(this, 1)">↓</button>
                        <button type="button" class="icon-btn danger" title="Eliminar" onclick="removeRow(this)">🗑</button>
                    </div>
                </div>
                <div class="med-grid">
                    <div class="form-group full autocomplete-wrapper">
                        <label>Nombre comercial *</label>
                        <input type="text" name="items[${index}][nombre_comercial]" class="input-nombre" autocomplete="off" value="${data.nombre_comercial ?? ''}" required>
                        <div class="autocomplete-results"></div>
                    </div>
                    <div class="form-group">
                        <label>Principio activo</label>
                        <input type="text" name="items[${index}][principio_activo]" class="input-principio" value="${data.principio_activo ?? ''}">
                    </div>
                    <div class="form-group">
                        <label>Presentación</label>
                        <input type="text" name="items[${index}][presentacion]" class="input-presentacion" placeholder="Ej: 600 mg tabletas" value="${data.presentacion ?? ''}">
                    </div>
                    <div class="form-group">
                        <label>Dosis</label>
                        <input type="text" name="items[${index}][dosis]" class="input-dosis" value="${data.dosis ?? ''}">
                    </div>
                    <div class="form-group">
                        <label>Frecuencia</label>
                        <input type="text" name="items[${index}][frecuencia]" placeholder="Ej: Cada 8 horas" value="${data.frecuencia ?? ''}">
                    </div>
                    <div class="form-group">
                        <label>Duración del tratamiento</label>
                        <input type="text" name="items[${index}][duracion]" placeholder="Ej: 7 días" value="${data.duracion ?? ''}">
                    </div>
                    <div class="form-group full">
                        <label>Indicaciones</label>
                        <textarea name="items[${index}][indicaciones]" placeholder="Ej: Tomar una tableta cada 8 horas durante 7 días">${data.indicaciones ?? ''}</textarea>
                    </div>
                    <div class="form-group full">
                        <label>Observaciones (opcional)</label>
                        <textarea name="items[${index}][observaciones]">${data.observaciones ?? ''}</textarea>
                    </div>
                </div>
            `;
            return wrapper;
        }

        function addMedRow(data = {}) {
            const container = document.getElementById('itemsContainer');
            const row = buildRow(rowCounter, data);
            container.appendChild(row);
            rowCounter++;
        }

        function removeRow(btn) {
            const container = document.getElementById('itemsContainer');
            if (container.children.length <= 1) {
                alert('La receta debe tener al menos un medicamento.');
                return;
            }
            btn.closest('.med-row').remove();
        }

        function moveRow(btn, direction) {
            const row = btn.closest('.med-row');
            const container = document.getElementById('itemsContainer');
            if (direction === -1 && row.previousElementSibling) {
                container.insertBefore(row, row.previousElementSibling);
            } else if (direction === 1 && row.nextElementSibling) {
                container.insertBefore(row.nextElementSibling, row);
            }
        }

        let debounceTimer = null;
        document.getElementById('itemsContainer').addEventListener('input', function (e) {
            if (!e.target.classList.contains('input-nombre')) return;

            const input = e.target;
            const wrapper = input.closest('.autocomplete-wrapper');
            const results = wrapper.querySelector('.autocomplete-results');
            const q = input.value.trim();

            clearTimeout(debounceTimer);
            if (q.length < 2) {
                results.classList.remove('show');
                return;
            }

            debounceTimer = setTimeout(() => {
                fetch(`${searchCatalogUrl}?q=${encodeURIComponent(q)}`)
                    .then(r => r.json())
                    .then(data => {
                        results.innerHTML = '';
                        if (data.length === 0) {
                            results.classList.remove('show');
                            return;
                        }
                        data.forEach(med => {
                            const item = document.createElement('div');
                            item.className = 'autocomplete-item';
                            item.innerHTML = `<div class="ai-nombre">${med.nombre}</div><div class="ai-meta">${med.presentacion ?? ''}</div>`;
                            item.addEventListener('click', () => {
                                const row = input.closest('.med-row');
                                input.value = med.nombre;
                                row.querySelector('.input-principio').value = med.principio_activo ?? '';
                                row.querySelector('.input-presentacion').value = med.presentacion ?? '';
                                row.querySelector('.input-dosis').value = med.dosis_habitual ?? '';
                                results.classList.remove('show');
                            });
                            results.appendChild(item);
                        });
                        results.classList.add('show');
                    });
            }, 300);
        });

        document.addEventListener('click', function (e) {
            if (!e.target.closest('.autocomplete-wrapper')) {
                document.querySelectorAll('.autocomplete-results').forEach(el => el.classList.remove('show'));
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
