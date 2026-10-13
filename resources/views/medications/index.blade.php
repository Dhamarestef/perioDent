<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicamentos</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background-color: #f5f5f5; }
        .navbar { background-color: #667eea; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar a, .navbar button { color: white; text-decoration: none; padding: 8px 16px; border-radius: 5px; border: none; cursor: pointer; background: none; font-size: 14px; transition: background-color 0.3s; }
        .navbar a:hover, .navbar button:hover { background-color: rgba(255,255,255,0.2); }
        .container { max-width: 1000px; margin: 40px auto; padding: 0 20px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        h2 { color: #333; font-size: 26px; }
        .btn-new { background-color: #667eea; color: white; padding: 10px 22px; border-radius: 5px; text-decoration: none; font-weight: bold; transition: background-color 0.3s; }
        .btn-new:hover { background-color: #764ba2; }
        .success { background-color: #d4edda; color: #155724; padding: 14px; border-radius: 5px; margin-bottom: 20px; }

        /* Buscador */
        .search-bar { margin-bottom: 25px; }
        .search-bar input { width: 100%; padding: 11px 16px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; }
        .search-bar input:focus { outline: none; border-color: #667eea; box-shadow: 0 0 4px rgba(102,126,234,0.3); }

        .empty-search { background: white; padding: 40px; border-radius: 10px; text-align: center; color: #999; box-shadow: 0 2px 10px rgba(0,0,0,0.06); display: none; }

        .categoria-block { margin-bottom: 35px; }
        .categoria-titulo { font-size: 13px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 2px solid; display: flex; align-items: center; gap: 8px; }
        .titulo-analgesicos { color: #e67e22; border-color: #e67e22; }
        .titulo-antibioticos { color: #667eea; border-color: #667eea; }

        table { width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
        th { background-color: #667eea; color: white; padding: 11px 15px; text-align: left; font-size: 13px; }
        td { padding: 12px 15px; border-bottom: 1px solid #f0f0f0; font-size: 14px; color: #333; vertical-align: top; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background-color: #f9f9f9; }

        .nombre-med { font-weight: 500; color: #333; }
        .indicaciones-text { color: #666; font-size: 13px; line-height: 1.5; }

        .actions { display: flex; gap: 8px; flex-shrink: 0; }
        .btn-sm { padding: 5px 12px; border-radius: 5px; text-decoration: none; font-size: 12px; font-weight: bold; border: none; cursor: pointer; white-space: nowrap; }
        .btn-view { background-color: #667eea; color: white; }
        .btn-edit { background-color: #17a2b8; color: white; }
        .btn-del  { background-color: #dc3545; color: white; }

        .cat-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; }
        .cat-analgesicos  { background-color: #fef3e2; color: #a0520e; }
        .cat-antibioticos { background-color: #eef0ff; color: #4a4db5; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Medicamentos</h1>
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

        <div class="header">
            <h2>💊 Catálogo de Medicamentos</h2>
            <a href="{{ route('medications.create') }}" class="btn-new">+ Nuevo Medicamento</a>
        </div>

        <div class="search-bar">
            <input type="text" id="busquedaMed" placeholder="🔍  Buscar por nombre o indicaciones..." onkeyup="filtrarMedicamentos()">
        </div>

        <div class="empty-search" id="emptySearch">
            <h3>Sin resultados</h3>
            <p>No encontramos medicamentos que coincidan con tu búsqueda.</p>
        </div>

        @foreach ($medications as $categoria => $items)
            <div class="categoria-block" data-categoria-block>
                <div class="categoria-titulo {{ $categoria === 'Analgésicos' ? 'titulo-analgesicos' : 'titulo-antibioticos' }}">
                    {{ $categoria === 'Analgésicos' ? '🟠' : '🔵' }} {{ $categoria }}
                    <span style="font-weight:normal; color:#999;">({{ $items->count() }} medicamentos)</span>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th style="width:35%">Medicamento</th>
                            <th>Indicaciones</th>
                            <th style="width:130px">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $med)
                            <tr data-med-row data-search="{{ strtolower($med->nombre.' '.$med->indicaciones) }}">
                                <td><span class="nombre-med">{{ $med->nombre }}</span></td>
                                <td><span class="indicaciones-text">{{ $med->indicaciones }}</span></td>
                                <td>
                                    <div class="actions">
                                        <a href="{{ route('medications.show', $med->id) }}" class="btn-sm btn-view">Ver</a>
                                        <a href="{{ route('medications.edit', $med->id) }}" class="btn-sm btn-edit">Editar</a>
                                        <form action="{{ route('medications.destroy', $med->id) }}" method="POST"
                                              onsubmit="return confirm('¿Ocultar este medicamento?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-sm btn-del">Ocultar</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>

    <script>
        function filtrarMedicamentos() {
            const q = document.getElementById('busquedaMed').value.toLowerCase().trim();
            const bloques = document.querySelectorAll('[data-categoria-block]');
            let totalVisibles = 0;

            bloques.forEach(bloque => {
                const filas = bloque.querySelectorAll('[data-med-row]');
                let visiblesEnBloque = 0;

                filas.forEach(fila => {
                    const coincide = fila.dataset.search.includes(q);
                    fila.style.display = coincide ? '' : 'none';
                    if (coincide) visiblesEnBloque++;
                });

                bloque.style.display = visiblesEnBloque > 0 ? '' : 'none';
                totalVisibles += visiblesEnBloque;
            });

            document.getElementById('emptySearch').style.display = totalVisibles === 0 ? 'block' : 'none';
        }
    </script>
</body>
</html>
