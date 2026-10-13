<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\BudgetItem;
use App\Models\Patient;
use App\Models\Treatment;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class BudgetController extends Controller
{
    public function create(Patient $patient)
    {
        $prefillFecha = now()->toDateString();
        $prefillItems = [];
        $catalog = $this->catalogForPicker();

        return view('budgets.create', compact('patient', 'prefillFecha', 'prefillItems', 'catalog'));
    }

    public function store(Request $request, Patient $patient)
    {
        $validated = $this->validateRequest($request);

        $budget = Budget::create([
            'patient_id' => $patient->id,
            'user_id' => auth()->id(),
            'fecha' => $validated['fecha'],
            'diagnostico' => $validated['diagnostico'] ?? null,
            'descuento_porcentaje' => $this->normalizarDescuento($request->descuento_porcentaje),
            'promocion_bienvenida' => $validated['promocion_bienvenida'] ?? null,
        ]);

        $this->syncItems($budget, $request->items);

        return redirect()->route('budgets.show', $budget->id)
                         ->with('success', 'Presupuesto generado correctamente');
    }

    public function show(Budget $budget)
    {
        $budget->load(['patient', 'user', 'items']);

        return view('budgets.show', compact('budget'));
    }

    public function edit(Budget $budget)
    {
        $budget->load('items.treatment', 'patient');
        $patient = $budget->patient;

        $prefillFecha = $budget->fecha->toDateString();
        $prefillItems = $budget->items->map(fn ($item) => [
            'treatment_id' => $item->treatment_id,
            'concepto' => $item->concepto,
            'costo_unitario' => $item->costo_unitario,
            'cantidad' => $item->cantidad,
        ])->toArray();
        $catalog = $this->catalogForPicker();

        return view('budgets.edit', compact('budget', 'patient', 'prefillFecha', 'prefillItems', 'catalog'));
    }

    public function update(Request $request, Budget $budget)
    {
        $validated = $this->validateRequest($request);

        $budget->update([
            'fecha' => $validated['fecha'],
            'diagnostico' => $validated['diagnostico'] ?? null,
            'descuento_porcentaje' => $this->normalizarDescuento($request->descuento_porcentaje),
            'promocion_bienvenida' => $validated['promocion_bienvenida'] ?? null,
        ]);

        $budget->items()->delete();
        $this->syncItems($budget, $request->items);

        return redirect()->route('budgets.show', $budget->id)
                         ->with('success', 'Presupuesto actualizado correctamente');
    }

    public function pdf(Budget $budget)
    {
        $budget->load(['patient', 'user', 'items']);

        $pdf = Pdf::loadView('budgets.pdf', compact('budget'))
                  ->setPaper('letter');

        return $pdf->download('presupuesto-' . $budget->patient->numero_expediente . '-' . $budget->id . '.pdf');
    }

    public function destroy(Budget $budget)
    {
        $patient = $budget->patient;
        $budget->delete();

        return redirect()->route('patients.show', $patient->id)
                         ->with('success', 'Presupuesto eliminado correctamente');
    }

    private function validateRequest(Request $request): array
    {
        return $request->validate([
            'fecha' => 'required|date',
            'diagnostico' => 'nullable|string|max:500',
            'descuento_porcentaje' => 'nullable|numeric|min:0|max:100',
            'promocion_bienvenida' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.treatment_id' => 'required|exists:treatments,id',
            'items.*.cantidad' => 'required|integer|min:1',
        ], [
            'items.required' => 'Agrega al menos un concepto al presupuesto',
            'items.*.treatment_id.required' => 'Selecciona un tratamiento del catálogo para cada renglón',
            'items.*.treatment_id.exists' => 'Uno de los tratamientos seleccionados no es válido',
            'items.*.cantidad.required' => 'Indica la cantidad para cada renglón',
            'items.*.cantidad.min' => 'La cantidad debe ser al menos 1',
            'descuento_porcentaje.max' => 'El descuento no puede ser mayor a 100%',
            'descuento_porcentaje.min' => 'El descuento no puede ser negativo',
        ]);
    }

    /** Vacío o null => sin descuento. */
    private function normalizarDescuento($valor): ?float
    {
        return ($valor === null || $valor === '') ? null : (float) $valor;
    }

    /**
     * Nunca confiamos en "concepto" o precio que venga del formulario, solo
     * en el treatment_id. El nombre y precio reales se copian desde el
     * catálogo de Tratamientos (Eloquent), para que el presupuesto quede
     * como un histórico fiel a la base de datos en el momento de emitirse.
     */
    private function syncItems(Budget $budget, array $items): void
    {
        $treatments = Treatment::whereIn('id', collect($items)->pluck('treatment_id'))
            ->get()
            ->keyBy('id');

        foreach (array_values($items) as $index => $item) {
            $t = $treatments->get($item['treatment_id']);

            if (! $t) {
                continue;
            }

            BudgetItem::create([
                'budget_id' => $budget->id,
                'treatment_id' => $t->id,
                'concepto' => $t->nombre,
                'cantidad' => $item['cantidad'],
                'costo_unitario' => $t->costo,
                'orden' => $index,
            ]);
        }
    }

    private function catalogForPicker()
    {
        return Treatment::orderBy('categoria')->orderBy('nombre')
            ->get(['id', 'abreviacion', 'nombre', 'categoria', 'costo'])
            ->map(fn ($t) => [
                'id' => $t->id,
                'abreviacion' => $t->abreviacion,
                'nombre' => $t->nombre,
                'categoria' => $t->categoria,
                'costo' => $t->costo,
            ])
            ->values();
    }
}
