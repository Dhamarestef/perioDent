<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PrescriptionController extends Controller
{
    public function create(Patient $patient)
    {
        $prefillFecha = now()->toDateString();
        $prefillAlergias = $patient->alergiasParaReceta();
        $prefillItems = [];

        return view('prescriptions.create', compact('patient', 'prefillFecha', 'prefillAlergias', 'prefillItems'));
    }

    public function duplicate(Prescription $prescription)
    {
        $prescription->load('items', 'patient');

        $patient = $prescription->patient;
        $prefillFecha = now()->toDateString();
        $prefillAlergias = $prescription->alergias;
        $prefillItems = $prescription->items->map(function ($item) {
            return [
                'nombre_comercial' => $item->nombre_comercial,
                'principio_activo' => $item->principio_activo,
                'presentacion'     => $item->presentacion,
                'dosis'            => $item->dosis,
                'indicaciones'     => $item->indicaciones,
                'frecuencia'       => $item->frecuencia,
                'duracion'         => $item->duracion,
                'observaciones'    => $item->observaciones,
            ];
        })->toArray();

        return view('prescriptions.create', compact('patient', 'prefillFecha', 'prefillAlergias', 'prefillItems'));
    }

    public function store(Request $request, Patient $patient)
    {
        $request->validate([
            'fecha_emision' => 'required|date',
            'alergias' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.nombre_comercial' => 'required|string|max:255',
            'items.*.principio_activo' => 'nullable|string|max:255',
            'items.*.presentacion' => 'nullable|string|max:255',
            'items.*.dosis' => 'nullable|string|max:255',
            'items.*.indicaciones' => 'nullable|string|max:500',
            'items.*.frecuencia' => 'nullable|string|max:255',
            'items.*.duracion' => 'nullable|string|max:255',
            'items.*.observaciones' => 'nullable|string|max:500',
        ], [
            'items.required' => 'Agrega al menos un medicamento a la receta',
            'items.*.nombre_comercial.required' => 'Cada medicamento necesita un nombre comercial',
        ]);

        $prescription = Prescription::create([
            'patient_id' => $patient->id,
            'user_id' => auth()->id(),
            'fecha_emision' => $request->fecha_emision,
            'alergias' => $request->alergias,
        ]);

        foreach (array_values($request->items) as $index => $item) {
            PrescriptionItem::create([
                'prescription_id' => $prescription->id,
                'nombre_comercial' => $item['nombre_comercial'],
                'principio_activo' => $item['principio_activo'] ?? null,
                'presentacion' => $item['presentacion'] ?? null,
                'dosis' => $item['dosis'] ?? null,
                'indicaciones' => $item['indicaciones'] ?? null,
                'frecuencia' => $item['frecuencia'] ?? null,
                'duracion' => $item['duracion'] ?? null,
                'observaciones' => $item['observaciones'] ?? null,
                'orden' => $index,
            ]);
        }

        return redirect()->route('prescriptions.show', $prescription->id)
                         ->with('success', 'Receta generada correctamente');
    }

    public function show(Prescription $prescription)
    {
        $prescription->load(['patient', 'user', 'items']);

        return view('prescriptions.show', compact('prescription'));
    }

    public function pdf(Prescription $prescription)
    {
        $prescription->load(['patient', 'user', 'items']);

        $pdf = Pdf::loadView('prescriptions.pdf', compact('prescription'))
                  ->setPaper('letter');

        return $pdf->download('receta-' . $prescription->patient->numero_expediente . '-' . $prescription->id . '.pdf');
    }

    public function destroy(Prescription $prescription)
    {
        $patient = $prescription->patient;
        $prescription->delete();

        return redirect()->route('patients.show', $patient->id)
                         ->with('success', 'Receta eliminada correctamente');
    }
}
