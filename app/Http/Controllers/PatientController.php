<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'alergias'         => 'nullable|string|max:1000',
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

        $patient = Patient::create([
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
            'alergias'          => $request->alergias,
        ]);

        ActivityLog::record(auth()->user(), 'patients.store', null, 'Patient', $patient->id);

        return redirect()->route('patients.index')
                         ->with('success', 'Paciente registrado correctamente');
    }

    public function show($id)
    {
        $patient = Patient::with(['appointments'])->findOrFail($id);

        $prescriptions = $patient->prescriptions()
                                ->orderBy('fecha_emision', 'desc')
                                ->get();

        $budgets = $patient->budgets()
                            ->orderBy('fecha', 'desc')
                            ->get();

        return view('patients.show', compact('patient', 'prescriptions', 'budgets'));
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
            'alergias'         => 'nullable|string|max:1000',
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

        ActivityLog::record(auth()->user(), 'patients.update', null, 'Patient', $patient->id);

        return redirect()->route('patients.show', $patient->id)
                         ->with('success', 'Paciente actualizado correctamente');
    }

    public function destroy($id)
    {
        $patient = Patient::findOrFail($id);

        $citasEliminadas = 0;

        DB::transaction(function () use ($patient, &$citasEliminadas) {
            $citasEliminadas = Appointment::where('patient_id', $patient->id)
                ->where('appointment_date', '>', now())
                ->delete();

            $patient->delete();
        });

        ActivityLog::record(auth()->user(), 'patients.destroy', null, 'Patient', $patient->id);

        $mensaje = 'Paciente ocultado correctamente';
        if ($citasEliminadas > 0) {
            $mensaje .= " y se eliminaron {$citasEliminadas} cita(s) futura(s)";
        }

        return redirect()->route('patients.index')
                         ->with('success', $mensaje);
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $columns = ['id', 'nombre', 'numero_expediente', 'telefono', 'correo'];

        // El doctor ve datos administrativos completos; la recepcionista solo
        // lo mínimo para identificar y agendar.
        if ($request->user()->isDoctor()) {
            $columns = array_merge($columns, ['domicilio_actual', 'lugar_nacimiento', 'edad']);
        }

        $patients = Patient::where('nombre', 'LIKE', "%{$query}%")
                           ->orWhere('numero_expediente', 'LIKE', "%{$query}%")
                           ->orWhere('telefono', 'LIKE', "%{$query}%")
                           ->orderBy('nombre')
                           ->limit(8)
                           ->get($columns);

        return response()->json($patients);
    }

    public function quickStore(Request $request)
    {
        $request->validate([
            'nombre'   => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'correo'   => 'nullable|email|max:255',
        ], [
            'nombre.required' => 'El nombre del paciente es requerido',
            'correo.email'    => 'Ingresa un correo válido',
        ]);

        $patient = Patient::create([
            'numero_expediente' => Patient::generarNumeroExpediente(),
            'fecha_registro'    => now()->toDateString(),
            'nombre'            => $request->nombre,
            'telefono'          => $request->telefono,
            'correo'            => $request->correo,
            'edad'              => 0,
            'sexo'              => 'F',
            'escolaridad'       => 'Otro',
            'estado_civil'      => 'Otro',
            'lugar_nacimiento'  => 'Por completar',
            'fecha_nacimiento'  => now()->toDateString(),
            'domicilio_actual'  => 'Por completar',
            'lugar_residencia'  => 'Por completar',
        ]);

        ActivityLog::record(auth()->user(), 'patients.quick-store', null, 'Patient', $patient->id);

        return response()->json([
            'id'                => $patient->id,
            'nombre'            => $patient->nombre,
            'numero_expediente' => $patient->numero_expediente,
            'telefono'          => $patient->telefono,
            'correo'            => $patient->correo,
            'incompleto'        => true,
        ]);
    }
}
