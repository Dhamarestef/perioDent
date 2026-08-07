<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expediente {{ $patient->numero_expediente }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background-color: #f5f5f5; }
        .navbar { background-color: #667eea; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar a { color: white; text-decoration: none; padding: 8px 16px; border-radius: 5px; transition: background-color 0.3s; }
        .navbar a:hover { background-color: rgba(255,255,255,0.2); }
        .container { max-width: 800px; margin: 40px auto; padding: 0 20px; }
        .success { background-color: #d4edda; color: #155724; padding: 14px; border-radius: 8px; margin-bottom: 20px; }

        .card { background: white; border-radius: 10px; padding: 28px 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 20px; }

        /* Header expediente */
        .exp-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 22px; padding-bottom: 18px; border-bottom: 2px solid #667eea; }
        .exp-nombre { font-size: 24px; font-weight: bold; color: #333; margin-bottom: 5px; }
        .exp-fecha  { color: #666; font-size: 13px; }
        .exp-numero { font-size: 20px; font-weight: bold; color: #667eea; margin-bottom: 6px; text-align: right; }
        .badge { display: inline-block; padding: 4px 14px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .badge-f { background-color: #fce4ec; color: #880e4f; }
        .badge-m { background-color: #e3f2fd; color: #0d47a1; }

        /* Alerta expediente incompleto */
        .incomplete-alert { background-color: #fff8e1; border-left: 4px solid #f0a500; padding: 14px 18px; border-radius: 6px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
        .incomplete-alert span { color: #856404; font-size: 14px; }
        .incomplete-alert a { background-color: #f0a500; color: white; padding: 7px 16px; border-radius: 5px; text-decoration: none; font-size: 13px; font-weight: bold; white-space: nowrap; margin-left: 12px; }

        .section-title { color: #667eea; font-size: 12px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin: 22px 0 14px; padding-bottom: 6px; border-bottom: 1px solid #f0f0f0; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .info-item label { font-size: 11px; color: #999; text-transform: uppercase; letter-spacing: 0.4px; display: block; margin-bottom: 3px; }
        .info-item span  { font-size: 15px; color: #333; }
        .info-item.full  { grid-column: 1 / -1; }

        /* Historial de citas */
        .history-table { width: 100%; border-collapse: collapse; font-size: 14px; }
        .history-table th { background-color: #667eea; color: white; padding: 10px 14px; text-align: left; font-size: 12px; }
        .history-table td { padding: 10px 14px; border-bottom: 1px solid #f0f0f0; }
        .history-table tr:last-child td { border-bottom: none; }
        .history-table tr:hover td { background-color: #f9f9f9; }

        .s-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; }
        .s-pending   { background-color: #fff3cd; color: #856404; }
        .s-confirmed { background-color: #d4edda; color: #155724; }
        .s-cancelled { background-color: #f8d7da; color: #721c24; }

        .empty-history { background: #f9f9f9; padding: 20px; border-radius: 8px; text-align: center; color: #999; font-size: 14px; }
        .empty-history a { color: #667eea; font-weight: bold; margin-left: 6px; }

        /* Botones */
        .btn-group { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 8px; }
        .btn { padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 14px; border: none; cursor: pointer; transition: all 0.25s; }
        .btn-edit { background-color: #17a2b8; color: white; }
        .btn-edit:hover { background-color: #138496; }
        .btn-apt  { background-color: #28a745; color: white; }
        .btn-apt:hover  { background-color: #218838; }
        .btn-back { background-color: #6c757d; color: white; }
        .btn-back:hover { background-color: #5a6268; }
        .btn-del  { background-color: #dc3545; color: white; }
        .btn-del:hover  { background-color: #c82333; }

        @media (max-width: 560px) {
            .info-grid { grid-template-columns: 1fr; }
            .exp-header { flex-direction: column; gap: 10px; }
            .btn-group { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Expediente</h1>
        <a href="{{ route('patients.index') }}">← Pacientes</a>
    </div>

    <div class="container">
        @if (session('success'))
            <div class="success">✓ {{ session('success') }}</div>
        @endif

        {{-- Alerta si expediente está incompleto --}}
        @if ($patient->expedienteIncompleto())
            <div class="incomplete-alert">
                <span>⚠️ Este expediente fue creado con registro rápido y tiene campos pendientes de completar.</span>
                <a href="{{ route('patients.edit', $patient->id) }}">Completar ahora →</a>
            </div>
        @endif

        {{-- ── DATOS GENERALES ── --}}
        <div class="card">
            <div class="exp-header">
                <div>
                    <div class="exp-nombre">{{ $patient->nombre }}</div>
                    <div class="exp-fecha">Registro: {{ $patient->fecha_registro->format('d/m/Y') }}</div>
                </div>
                <div style="text-align:right;">
                    <div class="exp-numero">{{ $patient->numero_expediente }}</div>
                    <span class="badge {{ $patient->sexo === 'F' ? 'badge-f' : 'badge-m' }}">
                        {{ $patient->sexo === 'F' ? '♀ Femenino' : '♂ Masculino' }}
                    </span>
                </div>
            </div>

            <div class="section-title">Datos personales</div>
            <div class="info-grid">
                <div class="info-item">
                    <label>Edad</label>
                    <span>{{ $patient->edad > 0 ? $patient->edad . ' años' : '—' }}</span>
                </div>
                <div class="info-item">
                    <label>Fecha de nacimiento</label>
                    <span>{{ $patient->fecha_nacimiento->format('d/m/Y') }}</span>
                </div>
                <div class="info-item">
                    <label>Escolaridad</label>
                    <span>{{ $patient->escolaridad !== 'Otro' ? $patient->escolaridad : '—' }}</span>
                </div>
                <div class="info-item">
                    <label>Estado civil</label>
                    <span>{{ $patient->estado_civil !== 'Otro' ? $patient->estado_civil : '—' }}</span>
                </div>
            </div>

            <div class="section-title">Ubicación y contacto</div>
            <div class="info-grid">
                <div class="info-item">
                    <label>Lugar de nacimiento</label>
                    <span>{{ $patient->lugar_nacimiento !== 'Por completar' ? $patient->lugar_nacimiento : '—' }}</span>
                </div>
                <div class="info-item">
                    <label>Lugar de residencia</label>
                    <span>{{ $patient->lugar_residencia !== 'Por completar' ? $patient->lugar_residencia : '—' }}</span>
                </div>
                <div class="info-item full">
                    <label>Domicilio actual</label>
                    <span>{{ $patient->domicilio_actual !== 'Por completar' ? $patient->domicilio_actual : '—' }}</span>
                </div>
                <div class="info-item">
                    <label>Teléfono</label>
                    <span>{{ $patient->telefono ?? '—' }}</span>
                </div>
                <div class="info-item">
                    <label>Correo electrónico</label>
                    <span>{{ $patient->correo ?? '—' }}</span>
                </div>
            </div>

            <div class="btn-group" style="margin-top: 26px;">
                <a href="{{ route('patients.edit', $patient->id) }}" class="btn btn-edit">✏️ Editar expediente</a>
                <a href="{{ route('appointment.create') }}?patient={{ $patient->id }}" class="btn btn-apt">📅 Nueva cita</a>
                <a href="{{ route('patients.index') }}" class="btn btn-back">← Volver</a>
                <form action="{{ route('patients.destroy', $patient->id) }}" method="POST"
                      onsubmit="return confirm('¿Eliminar este paciente? Esta acción eliminará también sus citas.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-del">🗑 Eliminar</button>
                </form>
            </div>
        </div>

      {{-- ── HISTORIAL DE CITAS ── --}}
        <div class="card">
            <div class="section-title" style="margin-top:0;">
                Historial de citas
                <span style="font-weight:normal; color:#bbb; margin-left:8px;">
                    ({{ $patient->appointments->count() }})
                </span>
            </div>

            @if ($patient->appointments->count() > 0)
               <table class="history-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Duración</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($patient->appointments->sortByDesc('appointment_date') as $apt)
                            <tr>
                                <td>{{ $apt->appointment_date->format('d/m/Y H:i') }}</td>
                                <td>{{ $apt->duracionTexto() }}</td>
                                <td>
                                    @if ($apt->isPending())
                                        <span class="s-badge s-pending">⏳ Pendiente</span>
                                    @elseif ($apt->isConfirmed())
                                        <span class="s-badge s-confirmed">✓ Confirmada</span>
                                    @else
                                        <span class="s-badge s-cancelled">✕ Cancelada</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('appointment.show', $apt->id) }}"
                                       style="color:#667eea; font-size:12px; font-weight:bold; text-decoration:none;">
                                        Ver →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-history">
                    Este paciente no tiene citas registradas aún.
                    <a href="{{ route('appointment.create') }}?patient={{ $patient->id }}">Agendar ahora →</a>
                </div>
            @endif
        </div>

{{-- ── RECETAS MÉDICAS ── --}}
        <div class="card">
            <div class="section-title" style="margin-top:0; display:flex; justify-content:space-between; align-items:center;">
                <span>
                    Recetas médicas
                    <span style="font-weight:normal; color:#bbb; margin-left:8px;">
                        ({{ $patient->prescriptions->count() }})
                    </span>
                </span>
                <a href="{{ route('prescriptions.create', $patient->id) }}"
                   style="background-color:#667eea; color:white; padding:8px 16px; border-radius:5px; text-decoration:none; font-size:13px; font-weight:bold;">
                    ＋ Nueva receta
                </a>
            </div>

            @if ($patient->prescriptions->count() > 0)
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Medicamentos</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($patient->prescriptions->sortByDesc('fecha_emision') as $prescription)
                            <tr>
                                <td>{{ $prescription->fecha_emision->format('d/m/Y') }}</td>
                                <td>{{ $prescription->items->count() }} medicamento(s)</td>
                                <td>
                                    <a href="{{ route('prescriptions.show', $prescription->id) }}"
                                       style="color:#667eea; font-size:12px; font-weight:bold; text-decoration:none;">
                                        Ver →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-history">
                    Este paciente no tiene recetas registradas aún.
                    <a href="{{ route('prescriptions.create', $patient->id) }}">Generar ahora →</a>
                </div>
            @endif
        </div>

    {{-- ── PRESUPUESTOS ── --}}
    <div class="card">
        <div class="section-title" style="margin-top:0; display:flex; justify-content:space-between; align-items:center;">
            <span>
                Presupuestos
                <span style="font-weight:normal; color:#bbb; margin-left:8px;">
                    ({{ $budgets->count() }})
                </span>
            </span>
            <a href="{{ route('budgets.create', $patient->id) }}"
            style="background-color:#667eea; color:white; padding:8px 16px; border-radius:5px; text-decoration:none; font-size:13px; font-weight:bold;">
                ＋ Nuevo presupuesto
            </a>
        </div>

        @if ($budgets->count() > 0)
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Conceptos</th>
                        <th>Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($budgets as $budget)
                        <tr>
                            <td>{{ $budget->fecha->format('d/m/Y') }}</td>
                            <td>{{ $budget->items->count() }} concepto(s)</td>
                            <td>
                                <strong style="color:#dc3545;">${{ number_format($budget->total(), 2) }}</strong>
                                @if ($budget->tieneDescuento())
                                    <span style="font-size:11px; color:#28a745; display:block;">
                                        -{{ rtrim(rtrim(number_format($budget->descuento_porcentaje, 2), '0'), '.') }}% descuento
                                    </span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('budgets.show', $budget->id) }}"
                                style="color:#667eea; font-size:12px; font-weight:bold; text-decoration:none;">
                                    Ver →
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-history">
                Este paciente no tiene presupuestos registrados aún.
                <a href="{{ route('budgets.create', $patient->id) }}">Generar ahora →</a>
            </div>
        @endif
    </div>

    </div>
</body>
</html>
