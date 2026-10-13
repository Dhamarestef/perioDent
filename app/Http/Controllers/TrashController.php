<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Patient;
use App\Models\Prescription;

class TrashController extends Controller
{
    /**
     * Lista todo lo ocultado (pacientes, presupuestos y recetas),
     * el último en ser ocultado aparece primero.
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

            default:
                abort(404);
        }

        return redirect()->route('papelera.index')->with('success', $msg);
    }
}
