<?php

namespace App\Http\Controllers;

use App\Models\OdontogramTooth;
use App\Models\OdontogramTreatment;
use App\Models\Patient;
use App\Models\Treatment;
use Illuminate\Http\Request;

class OdontogramController extends Controller
{
    /**
     * Numeración FDI de dientes permanentes, en el orden en que se dibujan
     * (arcada superior de derecha a izquierda del paciente, luego inferior).
     */
    protected array $toothNumbers = [
        18, 17, 16, 15, 14, 13, 12, 11, 21, 22, 23, 24, 25, 26, 27, 28,
        48, 47, 46, 45, 44, 43, 42, 41, 31, 32, 33, 34, 35, 36, 37, 38,
    ];

    public function show(Patient $patient)
    {
        $statesRaw = OdontogramTooth::where('patient_id', $patient->id)
            ->pluck('estado', 'tooth_number');

        $states = [];
        foreach ($this->toothNumbers as $n) {
            $states[$n] = $statesRaw[$n] ?? 'normal';
        }

        $treatmentsRaw = OdontogramTreatment::with('treatment')
            ->where('patient_id', $patient->id)
            ->orderBy('created_at')
            ->get();

        $treatmentsByTooth = [];
        foreach ($this->toothNumbers as $n) {
            $treatmentsByTooth[$n] = [];
        }

        foreach ($treatmentsRaw as $t) {
            $treatmentsByTooth[$t->tooth_number][] = [
                'id' => $t->id,
                'treatment_id' => $t->treatment_id,
                'abreviacion' => $t->treatment->abreviacion,
                'nombre' => $t->treatment->nombre,
                'observaciones' => $t->observaciones,
                'finalizado' => (bool) $t->finalizado,
                'fecha_realizacion' => $t->fecha_realizacion?->format('Y-m-d'),
                'fecha_display' => $t->fecha_realizacion?->format('d/m/Y') ?? '—',
            ];
        }

        $catalog = Treatment::orderBy('abreviacion')
            ->get(['id', 'abreviacion', 'nombre'])
            ->map(fn ($t) => [
                'id' => $t->id,
                'abreviacion' => $t->abreviacion,
                'nombre' => $t->nombre,
            ])
            ->values();

        return view('odontogram.show', [
            'patient' => $patient,
            'upperTeeth' => array_slice($this->toothNumbers, 0, 16),
            'lowerTeeth' => array_slice($this->toothNumbers, 16, 16),
            'states' => $states,
            'treatmentsByTooth' => $treatmentsByTooth,
            'catalog' => $catalog,
        ]);
    }

    public function updateEstado(Request $request, Patient $patient, int $tooth)
    {
        $request->validate([
            'estado' => 'required|in:normal,ausente,pendiente,finalizado',
        ]);

        $this->validateToothNumber($tooth);

        $record = OdontogramTooth::updateOrCreate(
            ['patient_id' => $patient->id, 'tooth_number' => $tooth],
            ['estado' => $request->estado]
        );

        return response()->json(['ok' => true, 'estado' => $record->estado]);
    }

    public function storeTreatment(Request $request, Patient $patient, int $tooth)
    {
        $this->validateToothNumber($tooth);

        $request->validate([
            'treatment_id' => 'required|exists:treatments,id',
            'observaciones' => 'nullable|string|max:500',
            'fecha_realizacion' => 'nullable|date',
        ], [
            'treatment_id.required' => 'Selecciona un tratamiento',
            'treatment_id.exists' => 'El tratamiento seleccionado no es válido',
            'observaciones.max' => 'Las observaciones no pueden exceder 500 caracteres',
        ]);

        $treatment = OdontogramTreatment::create([
            'patient_id' => $patient->id,
            'tooth_number' => $tooth,
            'treatment_id' => $request->treatment_id,
            'observaciones' => $request->observaciones,
            'fecha_realizacion' => $request->fecha_realizacion ?: now()->toDateString(),
        ]);

        $treatment->load('treatment');

        return response()->json([
            'ok' => true,
            'treatment' => [
                'id' => $treatment->id,
                'treatment_id' => $treatment->treatment_id,
                'abreviacion' => $treatment->treatment->abreviacion,
                'nombre' => $treatment->treatment->nombre,
                'observaciones' => $treatment->observaciones,
                'finalizado' => $treatment->finalizado,
                'fecha_realizacion' => $treatment->fecha_realizacion?->format('Y-m-d'),
                'fecha_display' => $treatment->fecha_realizacion?->format('d/m/Y') ?? '—',
            ],
        ]);
    }

    public function updateTreatment(Request $request, OdontogramTreatment $treatment)
    {
        $request->validate([
            'observaciones' => 'nullable|string|max:500',
            'finalizado' => 'sometimes|boolean',
            'fecha_realizacion' => 'nullable|date',
        ], [
            'observaciones.max' => 'Las observaciones no pueden exceder 500 caracteres',
        ]);

        $treatment->update($request->only(['observaciones', 'finalizado', 'fecha_realizacion']));

        return response()->json([
            'ok' => true,
            'treatment' => [
                'id' => $treatment->id,
                'observaciones' => $treatment->observaciones,
                'finalizado' => $treatment->finalizado,
                'fecha_realizacion' => $treatment->fecha_realizacion?->format('Y-m-d'),
                'fecha_display' => $treatment->fecha_realizacion?->format('d/m/Y') ?? '—',
            ],
        ]);
    }

    public function destroyTreatment(OdontogramTreatment $treatment)
    {
        $treatment->delete();

        return response()->json(['ok' => true]);
    }

    private function validateToothNumber(int $tooth): void
    {
        if (! in_array($tooth, $this->toothNumbers, true)) {
            abort(404, 'Número de diente no válido');
        }
    }
}
