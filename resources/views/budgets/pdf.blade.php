<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Presupuesto</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
        .header { display: table; width: 100%; margin-bottom: 20px; padding-bottom: 14px; border-bottom: 2px solid #667eea; }
        .header .clinic { display: table-cell; }
        .header .titulo { display: table-cell; text-align: right; vertical-align: top; }
        .clinic-name { font-size: 18px; font-weight: bold; color: #1a2b4a; }
        .clinic-sub { font-size: 9px; color: #667eea; letter-spacing: 1px; }
        .titulo h2 { font-size: 12px; color: #333; }

        .meta { margin-bottom: 18px; line-height: 1.8; font-size: 12px; }

        table.items { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        table.items th { background-color: #eef0ff; color: #4a4db5; padding: 6px 8px; text-align: left; font-size: 10px; text-transform: uppercase; }
        table.items th.num, table.items td.num { text-align: right; }
        table.items td { padding: 6px 8px; border-bottom: 1px solid #eee; font-size: 11.5px; }

        .totales { width: 260px; margin-left: auto; margin-top: 10px; }
        .totales-row { padding: 3px 8px; font-size: 12px; }
        .totales-row td { padding: 3px 8px; }
        .totales-row.descuento td { color: #28a745; }
        .totales-row.total td { font-size: 15px; font-weight: bold; color: #dc3545; border-top: 2px solid #eee; padding-top: 8px; }
        .nota-cotizar { font-size: 9.5px; color: #888; text-align: right; margin-top: 4px; }

        .promo-box { border: 1.5px dashed #667eea; padding: 12px; margin-top: 24px; }
        .promo-label { font-size: 9px; font-weight: bold; color: #667eea; text-transform: uppercase; margin-bottom: 4px; }
        .promo-text { font-size: 11.5px; }

        .firma { margin-top: 50px; text-align: center; font-size: 11px; line-height: 1.6; }
        .firma .linea { border-top: 1px solid #333; width: 220px; margin: 0 auto 8px; }
        .firma .nombre-doc { font-weight: bold; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="clinic">
            <div class="clinic-name">{{ config('app.name') }}</div>
            <div class="clinic-sub">CONSULTORIO DENTAL DE ESPECIALIDADES</div>
        </div>
        <div class="titulo">
            <h2>PLAN DE TRATAMIENTO Y PRESUPUESTO</h2>
        </div>
    </div>

    <div class="meta">
        <strong>FECHA:</strong> {{ $budget->fecha->format('d/m/Y') }}<br>
        <strong>NOMBRE:</strong> {{ \Illuminate\Support\Str::upper($budget->patient->nombre) }}
        &nbsp;&nbsp;<strong>EXPEDIENTE:</strong> {{ $budget->patient->numero_expediente }}
        @if ($budget->patient->edad > 0)
            &nbsp;&nbsp;<strong>EDAD:</strong> {{ $budget->patient->edad }} años
        @endif
        <br>
        @if ($budget->diagnostico)
            <strong>DIAGNÓSTICO DE PRESUNCIÓN:</strong> {{ $budget->diagnostico }}
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

    <table class="totales">
        <tr class="totales-row">
            <td>Subtotal</td>
            <td class="num">${{ number_format($budget->subtotal(), 2) }}</td>
        </tr>
        @if ($budget->tieneDescuento())
            <tr class="totales-row descuento">
                <td>Descuento ({{ rtrim(rtrim(number_format($budget->descuento_porcentaje, 2), '0'), '.') }}%)</td>
                <td class="num">-${{ number_format($budget->descuentoMonto(), 2) }}</td>
            </tr>
        @endif
        <tr class="totales-row total">
            <td>TOTAL</td>
            <td class="num">${{ number_format($budget->total(), 2) }}</td>
        </tr>
    </table>
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
</body>
</html>
