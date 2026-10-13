<?php

namespace App\Http\Controllers;

use App\Models\Medication;
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
        $catalog = $this->catalogForPicker();

        return view('prescriptions.create', compact('patient', 'prefillFecha', 'prefillAlergias', 'prefillItems', 'catalog'));
    }

    public function store(Request $request, Patient $patient)
    {
        $request->validate([
            'fecha_emision' => 'required|date',
            'alergias' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.medication_id' => 'required|exists:medications,id',
            'items.*.observaciones' => 'nullable|string|max:500',
        ], [
            'items.required' => 'Agrega al menos un medicamento a la receta',
            'items.*.medication_id.required' => 'Selecciona un medicamento del catálogo para cada renglón',
            'items.*.medication_id.exists' => 'Uno de los medicamentos seleccionados no es válido',
        ]);

        $prescription = Prescription::create([
            'patient_id' => $patient->id,
            'user_id' => auth()->id(),
            'fecha_emision' => $request->fecha_emision,
            'alergias' => $request->alergias,
        ]);

        // IMPORTANTE: nunca confiamos en nombre_comercial / indicaciones que venga
        // del formulario. Solo confiamos en el medication_id y copiamos la
        // información real desde el catálogo (Eloquent), para que la receta
        // quede como un historial inmutable y fiel a la base de datos.
        $medications = Medication::whereIn('id', collect($request->items)->pluck('medication_id'))
            ->get()
            ->keyBy('id');

        foreach (array_values($request->items) as $index => $item) {
            $med = $medications->get($item['medication_id']);

            if (! $med) {
                continue;
            }

            PrescriptionItem::create([
                'prescription_id'  => $prescription->id,
                'medication_id'    => $med->id,
                'nombre_comercial' => $med->nombre,
                'principio_activo' => $med->principio_activo,
                'presentacion'     => $med->presentacion,
                'indicaciones'     => $med->indicaciones,
                'observaciones'    => $item['observaciones'] ?? null,
                'orden'            => $index,
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

    /**
     * Catálogo completo de medicamentos listo para el buscador del formulario.
     * Se envía completo al frontend (normalmente son pocas decenas de
     * registros) para que la búsqueda sea instantánea, sin ida y vuelta al
     * servidor por cada tecla — así se reduce el número de clics/espera.
     */
    private function catalogForPicker()
    {
        return Medication::orderBy('nombre')
            ->get(['id', 'nombre', 'principio_activo', 'presentacion', 'indicaciones'])
            ->map(fn ($m) => [
                'id'               => $m->id,
                'nombre'           => $m->nombre,
                'principio_activo' => $m->principio_activo,
                'presentacion'     => $m->presentacion,
                'indicaciones'     => $m->indicaciones,
            ])
            ->values();
    }
}
