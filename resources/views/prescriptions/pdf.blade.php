<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Receta</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
        .header { text-align: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 2px solid #667eea; }
        .header h1 { color: #667eea; font-size: 18px; }
        .datos { margin-bottom: 18px; line-height: 1.8; font-size: 12px; }
        .alergias { background-color: #fff8e1; border-left: 4px solid #f0a500; padding: 8px 14px; margin-bottom: 22px; font-size: 11px; font-weight: bold; }
        .medicamento { margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #eee; }
        .med-nombre { font-size: 13px; font-weight: bold; }
        .med-presentacion { font-size: 11px; color: #555; margin: 2px 0 6px; }
        .med-indicaciones { font-size: 12px; }
        .med-obs { font-size: 10px; color: #888; font-style: italic; margin-top: 3px; }
        .firma { margin-top: 50px; text-align: center; font-size: 11px; line-height: 1.6; }
        .firma .linea { border-top: 1px solid #333; width: 220px; margin: 0 auto 8px; }
        .firma .nombre-doc { font-weight: bold; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name') }}</h1>
    </div>

    <div class="datos">
        <strong>PACIENTE:</strong> {{ \Illuminate\Support\Str::upper($prescription->patient->nombre) }}<br>
        <strong>EXPEDIENTE:</strong> {{ $prescription->patient->numero_expediente }}
        @if ($prescription->patient->edad > 0)
            &nbsp;&nbsp;<strong>EDAD:</strong> {{ $prescription->patient->edad }} años
        @endif
        <br>
        <strong>FECHA:</strong> {{ $prescription->fechaEmisionTexto() }}
    </div>

    <div class="alergias">
        ALERGIAS: {{ \Illuminate\Support\Str::upper($prescription->alergiasTexto()) }}
    </div>

    @foreach ($prescription->items as $item)
        <div class="medicamento">
            <div class="med-nombre">
                {{ $item->nombre_comercial }}
                @if ($item->principio_activo)
                    ({{ $item->principio_activo }})
                @endif
            </div>
            @if ($item->presentacion)
                <div class="med-presentacion">{{ $item->presentacion }}</div>
            @endif
            @if ($item->indicaciones)
                <div class="med-indicaciones">{{ $item->indicaciones }}</div>
            @elseif ($item->dosis || $item->frecuencia || $item->duracion)
                <div class="med-indicaciones">
                    {{ $item->dosis }} {{ $item->frecuencia }} {{ $item->duracion ? 'durante ' . $item->duracion : '' }}
                </div>
            @endif
            @if ($item->observaciones)
                <div class="med-obs">{{ $item->observaciones }}</div>
            @endif
        </div>
    @endforeach

    <div class="firma">
        <div class="linea"></div>
        <div class="nombre-doc">{{ \Illuminate\Support\Str::upper($prescription->user->name) }}</div>
        @if ($prescription->user->especialidad)
            {{ \Illuminate\Support\Str::upper($prescription->user->especialidad) }}<br>
        @endif
        @if ($prescription->user->cedula_profesional)
            CEDULA PROFESIONAL: {{ $prescription->user->cedula_profesional }}<br>
        @endif
        @if ($prescription->user->cedula_especialidad)
            CEDULA DE ESPECIALIDAD:<br>
            {{ $prescription->user->cedula_especialidad }}
        @endif
    </div>
</body>
</html>
