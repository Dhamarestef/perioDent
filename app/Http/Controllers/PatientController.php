<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index()
    {
        $patients = Patient::orderBy('created_at', 'desc')->get();
        return view('patients.index', compact('patients'));
    }

    public function create()
    {
        $numeroExpediente = Patient::generarNumeroExpediente();
        return view('patients.create', compact('numeroExpediente'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha_registro'   => 'required|date',
            'nombre'           => 'required|string|max:255',
            'edad'             => 'required|integer|min:0|max:150',
            'sexo'             => 'required|in:F,M',
            'escolaridad'      => 'required|string',
            'estado_civil'     => 'required|string',
            'lugar_nacimiento' => 'required|string|max:255',
            'fecha_nacimiento' => 'required|date',
            'domicilio_actual' => 'required|string|max:500',
            'telefono'         => 'nullable|string|max:20',
            'lugar_residencia' => 'required|string|max:255',
            'correo'           => 'nullable|email|max:255',
        ], [
            'fecha_registro.required'   => 'La fecha de registro es requerida',
            'nombre.required'           => 'El nombre del paciente es requerido',
            'edad.required'             => 'La edad es requerida',
            'edad.integer'              => 'La edad debe ser un número válido',
            'sexo.required'             => 'El sexo es requerido',
            'escolaridad.required'      => 'La escolaridad es requerida',
            'estado_civil.required'     => 'El estado civil es requerido',
            'lugar_nacimiento.required' => 'El lugar de nacimiento es requerido',
            'fecha_nacimiento.required' => 'La fecha de nacimiento es requerida',
            'domicilio_actual.required' => 'El domicilio actual es requerido',
            'lugar_residencia.required' => 'El lugar de residencia es requerido',
            'correo.email'              => 'Ingresa un correo electrónico válido',
        ]);

        Patient::create([
            'numero_expediente' => Patient::generarNumeroExpediente(),
            'fecha_registro'    => $request->fecha_registro,
            'nombre'            => $request->nombre,
            'edad'              => $request->edad,
            'sexo'              => $request->sexo,
            'escolaridad'       => $request->escolaridad,
            'estado_civil'      => $request->estado_civil,
            'lugar_nacimiento'  => $request->lugar_nacimiento,
            'fecha_nacimiento'  => $request->fecha_nacimiento,
            'domicilio_actual'  => $request->domicilio_actual,
            'telefono'          => $request->telefono,
            'lugar_residencia'  => $request->lugar_residencia,
            'correo'            => $request->correo,
        ]);

        return redirect()->route('patients.index')
                         ->with('success', 'Paciente registrado correctamente');
    }

    public function show($id)
    {
        $patient = Patient::findOrFail($id);
        return view('patients.show', compact('patient'));
    }

    public function edit($id)
    {
        $patient = Patient::findOrFail($id);
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);

        $request->validate([
            'fecha_registro'   => 'required|date',
            'nombre'           => 'required|string|max:255',
            'edad'             => 'required|integer|min:0|max:150',
            'sexo'             => 'required|in:F,M',
            'escolaridad'      => 'required|string',
            'estado_civil'     => 'required|string',
            'lugar_nacimiento' => 'required|string|max:255',
            'fecha_nacimiento' => 'required|date',
            'domicilio_actual' => 'required|string|max:500',
            'telefono'         => 'nullable|string|max:20',
            'lugar_residencia' => 'required|string|max:255',
            'correo'           => 'nullable|email|max:255',
        ], [
            'nombre.required'           => 'El nombre del paciente es requerido',
            'edad.required'             => 'La edad es requerida',
            'sexo.required'             => 'El sexo es requerido',
            'escolaridad.required'      => 'La escolaridad es requerida',
            'estado_civil.required'     => 'El estado civil es requerido',
            'lugar_nacimiento.required' => 'El lugar de nacimiento es requerido',
            'fecha_nacimiento.required' => 'La fecha de nacimiento es requerida',
            'domicilio_actual.required' => 'El domicilio actual es requerido',
            'lugar_residencia.required' => 'El lugar de residencia es requerido',
            'correo.email'              => 'Ingresa un correo electrónico válido',
        ]);

        $patient->update($request->except('numero_expediente'));

        return redirect()->route('patients.show', $patient->id)
                         ->with('success', 'Paciente actualizado correctamente');
    }

    public function destroy($id)
    {
        $patient = Patient::findOrFail($id);
        $patient->delete();

        return redirect()->route('patients.index')
                         ->with('success', 'Paciente eliminado correctamente');
    }
}
