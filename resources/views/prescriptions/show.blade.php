<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receta — {{ $prescription->patient->nombre }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background-color: #f5f5f5; }
        .navbar { background-color: #667eea; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar a, .navbar button { color: white; text-decoration: none; padding: 8px 16px; border-radius: 5px; border: none; cursor: pointer; background: none; font-size: 14px; }
        .navbar a:hover, .navbar button:hover { background-color: rgba(255,255,255,0.2); }
        .container { max-width: 750px; margin: 30px auto; padding: 0 20px 60px; }
        .toolbar { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
        .btn { padding: 10px 20px; border: none; border-radius: 6px; font-size: 14px; font-weight: bold; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
        .btn-print { background-color: #667eea; color: white; }
        .btn-pdf { background-color: #28a745; color: white; }
        .btn-dup { background-color: #17a2b8; color: white; }
        .btn-del { background-color: #dc3545; color: white; }

        .receta { background: white; border-radius: 10px; padding: 50px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
        .receta-header { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #667eea; }
        .receta-header h1 { color: #667eea; font-size: 22px; }

        .datos-paciente { margin-bottom: 25px; font-size: 14px; line-height: 1.8; }
        .datos-paciente strong { color: #333; }

        .alergias-box { background-color: #fff8e1; border-left: 4px solid #f0a500; padding: 10px 16px; margin-bottom: 30px; font-size: 13.5px; color: #856404; font-weight: bold; letter-spacing: 0.3px; }

        .medicamento { margin-bottom: 22px; padding-bottom: 18px; border-bottom: 1px solid #f0f0f0; }
        .medicamento:last-child { border-bottom: none; }
        .med-nombre { font-size: 16px; font-weight: bold; color: #333; }
        .med-presentacion { font-size: 13.5px; color: #666; margin: 3px 0 8px; }
        .med-indicaciones { font-size: 14px; color: #333; line-height: 1.5; }
        .med-obs { font-size: 12.5px; color: #999; font-style: italic; margin-top: 4px; }

        .firma { margin-top: 60px; text-align: center; font-size: 13px; line-height: 1.7; }
        .firma .linea { border-top: 1px solid #333; width: 260px; margin: 0 auto 10px; }
        .firma .nombre-doc { font-weight: bold; font-size: 14px; }

        @media print {
            .navbar, .toolbar { display: none !important; }
            body { background: white; }
            .receta { box-shadow: none; padding: 20px; }
            .container { max-width: 100%; margin: 0; padding: 0; }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Receta Médica</h1>
        <a href="{{ route('patients.show', $prescription->patient->id) }}">← Volver al expediente</a>
    </div>

    <div class="container">
        <div class="toolbar">
            <button type="button" class="btn btn-print" onclick="window.print()">🖨 Imprimir</button>
            <a href="{{ route('prescriptions.pdf', $prescription->id) }}" class="btn btn-pdf">⬇ Descargar PDF</a>
            <a href="{{ route('prescriptions.duplicate', $prescription->id) }}" class="btn btn-dup">📋 Duplicar</a>
            <form action="{{ route('prescriptions.destroy', $prescription->id) }}" method="POST"
                  onsubmit="return confirm('¿Eliminar esta receta?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-del">🗑 Eliminar</button>
            </form>
        </div>

        <div class="receta">
            <div class="receta-header">
                <h1>{{ config('app.name') }}</h1>
            </div>

            <div class="datos-paciente">
                <strong>PACIENTE:</strong> {{ \Illuminate\Support\Str::upper($prescription->patient->nombre) }}<br>
                <strong>EXPEDIENTE:</strong> {{ $prescription->patient->numero_expediente }}
                @if ($prescription->patient->edad > 0)
                    &nbsp;·&nbsp; <strong>EDAD:</strong> {{ $prescription->patient->edad }} años
                @endif
                <br>
                <strong>FECHA:</strong> {{ $prescription->fechaEmisionTexto() }}
            </div>

            <div class="alergias-box">
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
                    CÉDULA PROFESIONAL: {{ $prescription->user->cedula_profesional }}<br>
                @endif
                @if ($prescription->user->cedula_especialidad)
                    CÉDULA DE ESPECIALIDAD:<br>
                    {{ $prescription->user->cedula_especialidad }}
                @endif
            </div>
        </div>
    </div>
</body>
</html>
