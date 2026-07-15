<?php

namespace App\Http\Controllers;

use App\Models\Medication;
use Illuminate\Http\Request;

class MedicationController extends Controller
{
    public function index()
    {
        $medications = Medication::orderBy('categoria')
                                 ->orderBy('nombre')
                                 ->get()
                                 ->groupBy('categoria');

        return view('medications.index', compact('medications'));
    }

    public function create()
    {
        $categorias = Medication::$categorias;
        return view('medications.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'       => 'required|string|max:255',
            'categoria'    => 'required|in:Analgésicos,Antibióticos',
            'indicaciones' => 'required|string|max:500',
        ], [
            'nombre.required'       => 'El nombre del medicamento es requerido',
            'categoria.required'    => 'La categoría es requerida',
            'categoria.in'          => 'La categoría seleccionada no es válida',
            'indicaciones.required' => 'Las indicaciones son requeridas',
            'indicaciones.max'      => 'Las indicaciones no pueden exceder 500 caracteres',
        ]);

        Medication::create($request->all());

        return redirect()->route('medications.index')
                         ->with('success', 'Medicamento agregado correctamente');
    }

    public function show($id)
    {
        $medication = Medication::findOrFail($id);
        return view('medications.show', compact('medication'));
    }

    public function edit($id)
    {
        $medication = Medication::findOrFail($id);
        $categorias = Medication::$categorias;
        return view('medications.edit', compact('medication', 'categorias'));
    }

    public function update(Request $request, $id)
    {
        $medication = Medication::findOrFail($id);

        $request->validate([
            'nombre'       => 'required|string|max:255',
            'categoria'    => 'required|in:Analgésicos,Antibióticos',
            'indicaciones' => 'required|string|max:500',
        ], [
            'nombre.required'       => 'El nombre del medicamento es requerido',
            'categoria.required'    => 'La categoría es requerida',
            'categoria.in'          => 'La categoría seleccionada no es válida',
            'indicaciones.required' => 'Las indicaciones son requeridas',
            'indicaciones.max'      => 'Las indicaciones no pueden exceder 500 caracteres',
        ]);

        $medication->update($request->all());

        return redirect()->route('medications.index')
                         ->with('success', 'Medicamento actualizado correctamente');
    }

    public function destroy($id)
    {
        Medication::findOrFail($id)->delete();

        return redirect()->route('medications.index')
                         ->with('success', 'Medicamento eliminado correctamente');
    }
}
