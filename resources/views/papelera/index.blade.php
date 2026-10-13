<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Papelera</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background-color: #f5f5f5; }
        .navbar { background-color: #667eea; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar a, .navbar button { color: white; text-decoration: none; padding: 8px 16px; border-radius: 5px; border: none; cursor: pointer; background: none; font-size: 14px; transition: background-color 0.3s; }
        .navbar a:hover, .navbar button:hover { background-color: rgba(255,255,255,0.2); }
        .container { max-width: 1000px; margin: 40px auto; padding: 0 20px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; gap: 12px; flex-wrap: wrap; }
        h2 { color: #333; font-size: 26px; }
        .subtitle { color: #888; font-size: 13px; margin-bottom: 22px; }
        .success { background-color: #d4edda; color: #155724; padding: 14px; border-radius: 8px; margin-bottom: 20px; }
        .errors { background-color: #f8d7da; color: #721c24; padding: 14px; border-radius: 8px; margin-bottom: 20px; }

        .filters { display: flex; gap: 8px; margin-bottom: 20px; flex-wrap: wrap; }
        .filter-btn { padding: 8px 16px; border: 2px solid #ddd; border-radius: 20px; background: white; font-size: 13px; font-weight: bold; color: #666; cursor: pointer; }
        .filter-btn.active { border-color: #667eea; background-color: #eef0ff; color: #4a4db5; }

        .empty { background: white; padding: 50px; border-radius: 10px; text-align: center; color: #999; box-shadow: 0 2px 10px rgba(0,0,0,0.06); }
        .empty h3 { color: #555; margin-bottom: 8px; }

        table { width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.07); }
        th { background-color: #667eea; color: white; padding: 12px 15px; text-align: left; font-size: 13px; }
        td { padding: 12px 15px; border-bottom: 1px solid #f0f0f0; font-size: 14px; color: #333; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background-color: #f9f9f9; }

        .tag { display: inline-block; padding: 3px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; }
        .tag-paciente    { background-color: #e3f2fd; color: #0d47a1; }
        .tag-presupuesto { background-color: #fef3e2; color: #a0520e; }
        .tag-receta      { background-color: #eef0ff; color: #4a4db5; }
        .tag-tratamiento { background-color: #e8f5e9; color: #1b5e20; }
        .tag-medicamento { background-color: #fce4ec; color: #880e4f; }

        .muted { color: #999; font-size: 12px; }
        .btn-restore { background-color: #28a745; color: white; padding: 5px 14px; border-radius: 5px; border: none; font-size: 12px; font-weight: bold; cursor: pointer; }
        .btn-restore:hover { background-color: #218838; }
        .blocked-note { color: #b26a00; font-size: 12px; }
        .acciones { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
        .btn-force { background-color: #dc3545; color: white; padding: 5px 14px; border-radius: 5px; border: none; font-size: 12px; font-weight: bold; cursor: pointer; }
        .btn-force:hover { background-color: #c82333; }

        .btn-empty { background-color: #dc3545; color: white; padding: 10px 20px; border-radius: 6px; border: none; font-size: 14px; font-weight: bold; cursor: pointer; transition: background-color 0.3s; }
        .btn-empty:hover { background-color: #a71d2a; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Papelera</h1>
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
            <div class="success">✓ {{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="errors">{{ $errors->first() }}</div>
        @endif

        <div class="header">
            <h2>🗑 Papelera</h2>

            @if ($items->isNotEmpty())
                <form action="{{ route('papelera.empty') }}" method="POST"
                      data-total="{{ $items->count() }}"
                      onsubmit="return confirmarVaciado(this)">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-empty">🗑 Vaciar papelera</button>
                </form>
            @endif
        </div>
        <p class="subtitle">Pacientes, presupuestos, recetas, tratamientos y medicamentos ocultados. Lo más reciente aparece primero.</p>

        @if ($items->isEmpty())
            <div class="empty">
                <h3>La papelera está vacía</h3>
                <p>Lo que ocultes aparecerá aquí.</p>
            </div>
        @else
            <div class="filters">
                <button type="button" class="filter-btn active" data-filter="todos" onclick="filtrar('todos', this)">Todo ({{ $items->count() }})</button>
                <button type="button" class="filter-btn" data-filter="paciente" onclick="filtrar('paciente', this)">Pacientes ({{ $items->where('tipo', 'paciente')->count() }})</button>
                <button type="button" class="filter-btn" data-filter="presupuesto" onclick="filtrar('presupuesto', this)">Presupuestos ({{ $items->where('tipo', 'presupuesto')->count() }})</button>
                <button type="button" class="filter-btn" data-filter="receta" onclick="filtrar('receta', this)">Recetas ({{ $items->where('tipo', 'receta')->count() }})</button>
                <button type="button" class="filter-btn" data-filter="tratamiento" onclick="filtrar('tratamiento', this)">Tratamientos ({{ $items->where('tipo', 'tratamiento')->count() }})</button>
                <button type="button" class="filter-btn" data-filter="medicamento" onclick="filtrar('medicamento', this)">Medicamentos ({{ $items->where('tipo', 'medicamento')->count() }})</button>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Elemento</th>
                        <th>Detalle</th>
                        <th>Ocultado el</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        <tr data-tipo="{{ $item['tipo'] }}">
                            <td><span class="tag tag-{{ $item['tipo'] }}">{{ $item['etiqueta'] }}</span></td>
                            <td>{{ $item['titulo'] }}</td>
                            <td><span class="muted">{{ $item['detalle'] }}</span></td>
                            <td>{{ $item['deleted_at']->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="acciones">
                                    @if ($item['bloqueado'])
                                        <span class="blocked-note">Restaura primero al paciente</span>
                                    @else
                                        <form action="{{ route('papelera.restore', [$item['tipo'], $item['id']]) }}" method="POST"
                                              onsubmit="return confirm('¿Restaurar este elemento?')">
                                            @csrf
                                            <button type="submit" class="btn-restore">↩ Restaurar</button>
                                        </form>
                                    @endif

                                    <form action="{{ route('papelera.force-delete', [$item['tipo'], $item['id']]) }}" method="POST"
                                          data-tipo="{{ $item['tipo'] }}"
                                          onsubmit="return confirmarBorrado(this)">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-force">✕ Eliminar definitivamente</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <script>
        const avisos = {
            paciente: '¿ELIMINAR DEFINITIVAMENTE a este paciente?\n\nSe borrarán también su expediente, odontograma, presupuestos y recetas. Sus citas se conservarán pero quedarán sin paciente.\n\nEsta acción NO se puede deshacer.',
            presupuesto: '¿ELIMINAR DEFINITIVAMENTE este presupuesto?\n\nEsta acción NO se puede deshacer.',
            receta: '¿ELIMINAR DEFINITIVAMENTE esta receta?\n\nEsta acción NO se puede deshacer.',
            tratamiento: '¿ELIMINAR DEFINITIVAMENTE este tratamiento?\n\nSe quitará también de los odontogramas de los pacientes donde esté registrado. En los presupuestos ya emitidos el concepto se conservará.\n\nEsta acción NO se puede deshacer.',
            medicamento: '¿ELIMINAR DEFINITIVAMENTE este medicamento?\n\nEn las recetas ya emitidas el renglón se conservará.\n\nEsta acción NO se puede deshacer.',
        };

        function confirmarBorrado(form) {
            return confirm(avisos[form.dataset.tipo] || '¿Eliminar definitivamente? No se puede deshacer.');
        }

        function confirmarVaciado(form) {
            const total = form.dataset.total;
            return confirm(
                '¿VACIAR LA PAPELERA?\n\n' +
                'Se eliminarán DEFINITIVAMENTE los ' + total + ' elemento(s) de la papelera ' +
                '(pacientes con su expediente y odontograma, presupuestos, recetas, tratamientos y medicamentos). ' +
                'Las citas de los pacientes eliminados se conservarán pero quedarán sin paciente. ' +
                'Los tratamientos eliminados se quitarán de los odontogramas donde estén registrados.\n\n' +
                'Esta acción NO se puede deshacer.'
            );
        }

        function filtrar(tipo, btn) {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.toggle('active', b === btn));
            document.querySelectorAll('tbody tr').forEach(tr => {
                tr.style.display = (tipo === 'todos' || tr.dataset.tipo === tipo) ? '' : 'none';
            });
        }
    </script>
</body>
</html>
