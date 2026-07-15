<?php

namespace App\Http\Controllers;

use App\Models\Treatment;
use Illuminate\Http\Request;

class TreatmentController extends Controller
{
    public function index()
    {
        $treatments = Treatment::orderBy('categoria')->orderBy('abreviacion')->get()
            ->groupBy('categoria');

        return view('treatments.index', compact('treatments'));
    }

    public function create()
    {
        $categorias = Treatment::$categorias;
        return view('treatments.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'abreviacion' => 'required|string|max:50|unique:treatments,abreviacion',
            'nombre'      => 'required|string|max:255',
            'costo'       => 'nullable|numeric|min:0',
            'categoria'   => 'required|in:General,Endodoncia,Cirugia y Periodoncia',
        ], [
            'abreviacion.required' => 'La abreviación es requerida',
            'abreviacion.unique'   => 'Esta abreviación ya existe',
            'nombre.required'      => 'El nombre del tratamiento es requerido',
            'costo.numeric'        => 'El costo debe ser un número válido',
            'categoria.required'   => 'La categoría es requerida',
        ]);

        Treatment::create($request->all());

        return redirect()->route('treatments.index')
                         ->with('success', 'Tratamiento agregado correctamente');
    }

    public function show($id)
    {
        $treatment = Treatment::findOrFail($id);
        return view('treatments.show', compact('treatment'));
    }

    public function edit($id)
    {
        $treatment = Treatment::findOrFail($id);
        $categorias = Treatment::$categorias;
        return view('treatments.edit', compact('treatment', 'categorias'));
    }

    public function update(Request $request, $id)
    {
        $treatment = Treatment::findOrFail($id);

        $request->validate([
            'abreviacion' => 'required|string|max:50|unique:treatments,abreviacion,' . $id,
            'nombre'      => 'required|string|max:255',
            'costo'       => 'nullable|numeric|min:0',
            'categoria'   => 'required|in:General,Endodoncia,Cirugia y Periodoncia',
        ], [
            'abreviacion.required' => 'La abreviación es requerida',
            'abreviacion.unique'   => 'Esta abreviación ya está registrada',
            'nombre.required'      => 'El nombre del tratamiento es requerido',
            'costo.numeric'        => 'El costo debe ser un número válido',
            'categoria.required'   => 'La categoría es requerida',
        ]);

        $treatment->update($request->all());

        return redirect()->route('treatments.index')
                         ->with('success', 'Tratamiento actualizado correctamente');
    }

    public function destroy($id)
    {
        Treatment::findOrFail($id)->delete();

        return redirect()->route('treatments.index')
                         ->with('success', 'Tratamiento eliminado correctamente');
    }
}
