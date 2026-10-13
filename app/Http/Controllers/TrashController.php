<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Medication;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\Treatment;
use Illuminate\Support\Facades\DB;

class TrashController extends Controller
{
    /**
     * Lista todo lo ocultado (pacientes, presupuestos, recetas, tratamientos
     * y medicamentos), el último en ser ocultado aparece primero.
     */
    public function index()
    {
        $items = collect();

        foreach (Patient::onlyTrashed()->get() as $p) {
            $items->push([
                'tipo'       => 'paciente',
                'etiqueta'   => 'Paciente',
                'id'         => $p->id,
                'titulo'     => $p->nombre,
                'detalle'    => $p->numero_expediente,
                'deleted_at' => $p->deleted_at,
                'bloqueado'  => false,
            ]);
        }

        foreach (Budget::onlyTrashed()->with(['patient', 'items'])->get() as $b) {
            $items->push([
                'tipo'       => 'presupuesto',
                'etiqueta'   => 'Presupuesto',
                'id'         => $b->id,
                'titulo'     => $b->patient->nombre ?? 'Paciente',
                'detalle'    => $b->fecha->format('d/m/Y') . ' · $' . number_format($b->total(), 2),
                'deleted_at' => $b->deleted_at,
                'bloqueado'  => (bool) ($b->patient && $b->patient->trashed()),
            ]);
        }

        foreach (Prescription::onlyTrashed()->with(['patient', 'items'])->get() as $r) {
            $items->push([
                'tipo'       => 'receta',
                'etiqueta'   => 'Receta',
                'id'         => $r->id,
                'titulo'     => $r->patient->nombre ?? 'Paciente',
                'detalle'    => $r->fecha_emision->format('d/m/Y') . ' · ' . $r->items->count() . ' medicamento(s)',
                'deleted_at' => $r->deleted_at,
                'bloqueado'  => (bool) ($r->patient && $r->patient->trashed()),
            ]);
        }

        foreach (Treatment::onlyTrashed()->get() as $t) {
            $items->push([
                'tipo'       => 'tratamiento',
                'etiqueta'   => 'Tratamiento',
                'id'         => $t->id,
                'titulo'     => $t->abreviacion . ' — ' . $t->nombre,
                'detalle'    => $t->categoria . ' · ' . $t->costo_formateado,
                'deleted_at' => $t->deleted_at,
                'bloqueado'  => false,
            ]);
        }

        foreach (Medication::onlyTrashed()->get() as $m) {
            $items->push([
                'tipo'       => 'medicamento',
                'etiqueta'   => 'Medicamento',
                'id'         => $m->id,
                'titulo'     => $m->nombre,
                'detalle'    => $m->categoria,
                'deleted_at' => $m->deleted_at,
                'bloqueado'  => false,
            ]);
        }

        $items = $items->sortByDesc('deleted_at')->values();

        return view('papelera.index', compact('items'));
    }

    /**
     * Restaura un elemento oculto.
     */
    public function restore(string $tipo, int $id)
    {
        switch ($tipo) {
            case 'paciente':
                Patient::onlyTrashed()->findOrFail($id)->restore();
                $msg = 'Paciente restaurado correctamente';
                break;

            case 'presupuesto':
                $b = Budget::onlyTrashed()->with('patient')->findOrFail($id);
                if ($b->patient && $b->patient->trashed()) {
                    return back()->withErrors(['error' => 'Primero restaura al paciente de este presupuesto.']);
                }
                $b->restore();
                $msg = 'Presupuesto restaurado correctamente';
                break;

            case 'receta':
                $r = Prescription::onlyTrashed()->with('patient')->findOrFail($id);
                if ($r->patient && $r->patient->trashed()) {
                    return back()->withErrors(['error' => 'Primero restaura al paciente de esta receta.']);
                }
                $r->restore();
                $msg = 'Receta restaurada correctamente';
                break;

            case 'tratamiento':
                Treatment::onlyTrashed()->findOrFail($id)->restore();
                $msg = 'Tratamiento restaurado correctamente';
                break;

            case 'medicamento':
                Medication::onlyTrashed()->findOrFail($id)->restore();
                $msg = 'Medicamento restaurado correctamente';
                break;

            default:
                abort(404);
        }

        return redirect()->route('papelera.index')->with('success', $msg);
    }

    /**
     * Borrado definitivo (no se puede deshacer).
     * Las relaciones con onDelete('cascade') limpian los datos dependientes:
     *  - Paciente: se borran sus presupuestos, recetas y odontograma.
     *    Sus citas se conservan pero quedan sin paciente (set null).
     *  - Presupuesto / Receta: se borran sus renglones.
     *  - Tratamiento: se borra de los odontogramas donde estaba registrado;
     *    en los presupuestos el renglón se conserva (treatment_id queda null).
     *  - Medicamento: en las recetas el renglón se conserva (medication_id null).
     */
    public function forceDelete(string $tipo, int $id)
    {
        switch ($tipo) {
            case 'paciente':
                Patient::onlyTrashed()->findOrFail($id)->forceDelete();
                $msg = 'Paciente eliminado definitivamente';
                break;

            case 'presupuesto':
                Budget::onlyTrashed()->findOrFail($id)->forceDelete();
                $msg = 'Presupuesto eliminado definitivamente';
                break;

            case 'receta':
                Prescription::onlyTrashed()->findOrFail($id)->forceDelete();
                $msg = 'Receta eliminada definitivamente';
                break;

            case 'tratamiento':
                Treatment::onlyTrashed()->findOrFail($id)->forceDelete();
                $msg = 'Tratamiento eliminado definitivamente';
                break;

            case 'medicamento':
                Medication::onlyTrashed()->findOrFail($id)->forceDelete();
                $msg = 'Medicamento eliminado definitivamente';
                break;

            default:
                abort(404);
        }

        return redirect()->route('papelera.index')->with('success', $msg);
    }

    /**
     * Vacía la papelera: elimina definitivamente TODO lo ocultado
     * (no se puede deshacer). Primero se borran presupuestos y recetas,
     * luego pacientes, y al final tratamientos y medicamentos,
     * para que las cascadas queden limpias.
     */
    public function empty()
    {
        $total = 0;

        DB::transaction(function () use (&$total) {
            $total += Budget::onlyTrashed()->count();
            Budget::onlyTrashed()->forceDelete();

            $total += Prescription::onlyTrashed()->count();
            Prescription::onlyTrashed()->forceDelete();

            $total += Patient::onlyTrashed()->count();
            Patient::onlyTrashed()->forceDelete();

            $total += Treatment::onlyTrashed()->count();
            Treatment::onlyTrashed()->forceDelete();

            $total += Medication::onlyTrashed()->count();
            Medication::onlyTrashed()->forceDelete();
        });

        if ($total === 0) {
            return redirect()->route('papelera.index')
                             ->withErrors(['error' => 'La papelera ya estaba vacía.']);
        }

        return redirect()->route('papelera.index')
                         ->with('success', "Papelera vaciada: {$total} elemento(s) eliminados definitivamente");
    }
}
