<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presupuesto — {{ $budget->patient->nombre }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background-color: #f5f5f5; }
        .navbar { background-color: #667eea; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar a, .navbar button { color: white; text-decoration: none; padding: 8px 16px; border-radius: 5px; border: none; cursor: pointer; background: none; font-size: 14px; }
        .navbar a:hover, .navbar button:hover { background-color: rgba(255,255,255,0.2); }
        .container { max-width: 800px; margin: 30px auto; padding: 0 20px 60px; }
        .toolbar { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
        .btn { padding: 10px 20px; border: none; border-radius: 6px; font-size: 14px; font-weight: bold; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
        .btn-print { background-color: #667eea; color: white; }
        .btn-pdf { background-color: #28a745; color: white; }
        .btn-edit { background-color: #17a2b8; color: white; }
        .btn-del { background-color: #dc3545; color: white; }

        .doc { background: white; border-radius: 10px; padding: 40px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
        .doc-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 2px solid #f0f0f0; }
        .clinic-name { font-size: 24px; font-weight: bold; letter-spacing: 2px; color: #1a2b4a; }
        .clinic-sub { font-size: 11px; letter-spacing: 1.5px; color: #667eea; margin-top: 2px; }
        .doc-title { text-align: right; }
        .doc-title h2 { font-size: 15px; color: #333; letter-spacing: 0.5px; }

        .meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px 24px; margin-bottom: 24px; font-size: 13.5px; }
        .meta-item strong { color: #333; }
        .meta-item span.val { color: #555; }
        .meta-item.full { grid-column: 1 / -1; }

        table.items { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        table.items th { background-color: #eef0ff; color: #4a4db5; padding: 10px 12px; text-align: left; font-size: 11.5px; text-transform: uppercase; letter-spacing: 0.3px; }
        table.items th.num, table.items td.num { text-align: right; }
        table.items td { padding: 10px 12px; border-bottom: 1px solid #f0f0f0; font-size: 13.5px; color: #333; }

        .totales { margin-top: 14px; margin-left: auto; width: 280px; }
        .totales-row { display: flex; justify-content: space-between; font-size: 13.5px; padding: 5px 12px; color: #555; }
        .totales-row.descuento { color: #28a745; }
        .totales-row.total { font-size: 17px; font-weight: bold; color: #dc3545; border-top: 2px solid #eee; margin-top: 6px; padding-top: 10px; }
        .nota-cotizar { font-size: 11px; color: #999; margin-top: 8px; text-align: right; }

        .promo-box { border: 1.5px dashed #667eea; border-radius: 8px; padding: 16px; margin-top: 26px; }
        .promo-box .promo-label { font-size: 11px; font-weight: bold; color: #667eea; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
        .promo-box .promo-text { font-size: 13.5px; color: #333; }

        .firma { margin-top: 45px; text-align: center; font-size: 12.5px; line-height: 1.6; }
        .firma .linea { border-top: 1px solid #333; width: 240px; margin: 0 auto 8px; }
        .firma .nombre-doc { font-weight: bold; font-size: 13px; }

        @media print {
            .navbar, .toolbar { display: none !important; }
            body { background: white; }
            .doc { box-shadow: none; padding: 20px; }
            .container { max-width: 100%; margin: 0; padding: 0; }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Presupuesto</h1>
        <a href="{{ route('patients.show', $budget->patient->id) }}">← Volver al expediente</a>
    </div>

    <div class="container">
        @if (session('success'))
            <div style="background-color:#d4edda; color:#155724; padding:14px; border-radius:8px; margin-bottom:20px;">✓ {{ session('success') }}</div>
        @endif

        <div class="toolbar">
            <button type="button" class="btn btn-print" onclick="window.print()">🖨 Imprimir</button>
            <a href="{{ route('budgets.pdf', $budget->id) }}" class="btn btn-pdf">⬇ Descargar PDF</a>
            <a href="{{ route('budgets.edit', $budget->id) }}" class="btn btn-edit">✏️ Editar</a>
            <form action="{{ route('budgets.destroy', $budget->id) }}" method="POST"
                  onsubmit="return confirm('¿Ocultar este presupuesto?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-del">🙈 Ocultar</button>
            </form>
        </div>

        <div class="doc">
            <div class="doc-header">
                <div>
                    <div class="clinic-name">{{ config('app.name') }}</div>
                    <div class="clinic-sub">CONSULTORIO DENTAL DE ESPECIALIDADES</div>
                </div>
                <div class="doc-title">
                    <h2>PLAN DE TRATAMIENTO<br>Y PRESUPUESTO</h2>
                </div>
            </div>

            <div class="meta-grid">
                <div class="meta-item"><strong>Fecha:</strong> <span class="val">{{ $budget->fecha->format('d/m/Y') }}</span></div>
                <div class="meta-item"><strong>Expediente:</strong> <span class="val">{{ $budget->patient->numero_expediente }}</span></div>
                <div class="meta-item"><strong>Nombre:</strong> <span class="val">{{ \Illuminate\Support\Str::upper($budget->patient->nombre) }}</span></div>
                <div class="meta-item"><strong>Edad:</strong> <span class="val">{{ $budget->patient->edad > 0 ? $budget->patient->edad . ' años' : '—' }}</span></div>
                @if ($budget->diagnostico)
                    <div class="meta-item full"><strong>Diagnóstico de presunción:</strong> <span class="val">{{ $budget->diagnostico }}</span></div>
                @endif
            </div>

            <table class="items">
                <thead>
                    <tr>
                        <th>Concepto</th>
                        <th class="num">Cantidad</th>
                        <th class="num">Costo unitario</th>
                        <th class="num">Costo global</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($budget->items as $item)
                        <tr>
                            <td>{{ $item->concepto }}</td>
                            <td class="num">{{ $item->cantidad }}</td>
                            <td class="num">{{ $item->costoUnitarioFormateado() }}</td>
                            <td class="num">{{ $item->costoGlobalFormateado() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="totales">
                <div class="totales-row">
                    <span>Subtotal</span>
                    <span>${{ number_format($budget->subtotal(), 2) }}</span>
                </div>
                @if ($budget->tieneDescuento())
                    <div class="totales-row descuento">
                        <span>Descuento ({{ rtrim(rtrim(number_format($budget->descuento_porcentaje, 2), '0'), '.') }}%)</span>
                        <span>-${{ number_format($budget->descuentoMonto(), 2) }}</span>
                    </div>
                @endif
                <div class="totales-row total">
                    <span>TOTAL</span>
                    <span>${{ number_format($budget->total(), 2) }}</span>
                </div>
            </div>
            @if ($budget->tieneConceptosPorCotizar())
                <div class="nota-cotizar">* Hay conceptos "Por cotizar" no incluidos en este total.</div>
            @endif

            @if ($budget->promocion_bienvenida)
                <div class="promo-box">
                    <div class="promo-label">Promoción de bienvenida</div>
                    <div class="promo-text">{{ $budget->promocion_bienvenida }}</div>
                </div>
            @endif

            <div class="firma">
                <div class="linea"></div>
                <div class="nombre-doc">{{ \Illuminate\Support\Str::upper($budget->user->name) }}</div>
                @if ($budget->user->especialidad)
                    {{ \Illuminate\Support\Str::upper($budget->user->especialidad) }}<br>
                @endif
                @if ($budget->user->cedula_profesional)
                    CÉDULA PROFESIONAL: {{ $budget->user->cedula_profesional }}
                @endif
            </div>
        </div>
    </div>
</body>
</html>
