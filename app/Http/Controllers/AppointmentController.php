<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Service;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::with(['service', 'patient'])
            ->where('user_id', auth()->id())
            ->orderBy('appointment_date', 'desc')
            ->get();

        return view('appointments.index', compact('appointments'));
    }

    public function create()
    {
        $services = Service::all();

        // Si viene con ?patient=ID pre-selecciona el paciente
        $prePatient = null;
        if (request('patient')) {
            $prePatient = Patient::find(request('patient'));
        }

        return view('appointments.create', compact('services', 'prePatient'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id'       => 'required|exists:patients,id',
            'service_id'       => 'required|exists:services,id',
            'appointment_date' => 'required|date_format:Y-m-d\TH:i|after:now',
            'notes'            => 'nullable|string|max:500',
        ], [
            'patient_id.required'       => 'Debes seleccionar o registrar un paciente',
            'patient_id.exists'         => 'El paciente seleccionado no es válido',
            'service_id.required'       => 'Debes seleccionar un servicio',
            'service_id.exists'         => 'El servicio seleccionado no es válido',
            'appointment_date.required' => 'Debes seleccionar una fecha y hora',
            'appointment_date.date_format' => 'El formato de fecha no es válido',
            'appointment_date.after'    => 'La fecha debe ser posterior a la fecha actual',
            'notes.max'                 => 'Las notas no pueden exceder 500 caracteres',
        ]);

        $appointmentDate = Carbon::createFromFormat('Y-m-d\TH:i', $request->appointment_date);
        $patient         = Patient::findOrFail($request->patient_id);

        if (!$this->isTimeAvailable($appointmentDate, $request->service_id)) {
            return back()->withErrors(['appointment_date' => 'Este horario no está disponible, elige otro'])
                         ->withInput();
        }

        $appointment = Appointment::create([
            'user_id'            => auth()->id(),
            'patient_id'         => $patient->id,
            'service_id'         => $request->service_id,
            'appointment_date'   => $appointmentDate,
            'notes'              => $request->notes,
            'confirmation_token' => Str::random(32),
            'status'             => 'pending',
        ]);

        // Enviar email solo si el paciente tiene correo
        if ($patient->correo) {
            try {
                \Mail::send('emails.appointment-confirmation', [
                    'appointment'      => $appointment,
                    'confirmationLink' => route('appointment.confirm', $appointment->confirmation_token),
                ], function ($message) use ($patient, $appointment) {
                    $message->to($patient->correo)
                            ->subject('Confirma tu cita — ' . $appointment->service->name);
                });
            } catch (\Exception $e) {
                // Si falla el correo no interrumpimos el flujo
            }
        }

        return redirect()->route('appointment.index')
                         ->with('success', 'Cita agendada correctamente para ' . $patient->nombre);
    }

    public function show($id)
    {
        $appointment = Appointment::with(['service', 'patient', 'user'])
                                  ->findOrFail($id);

        if ($appointment->user_id !== auth()->id()) {
            abort(403);
        }

        return view('appointments.show', compact('appointment'));
    }

    public function edit($id)
    {
        $appointment = Appointment::with(['service', 'patient'])->findOrFail($id);

        if ($appointment->user_id !== auth()->id()) {
            abort(403);
        }

        if ($appointment->isConfirmed()) {
            return back()->withErrors(['error' => 'No puedes editar una cita ya confirmada']);
        }

        $services = Service::all();
        return view('appointments.edit', compact('appointment', 'services'));
    }

    public function update(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        if ($appointment->user_id !== auth()->id()) {
            abort(403);
        }

        if ($appointment->isConfirmed()) {
            return back()->withErrors(['error' => 'No puedes editar una cita ya confirmada']);
        }

        $request->validate([
            'service_id'       => 'required|exists:services,id',
            'appointment_date' => 'required|date_format:Y-m-d\TH:i|after:now',
            'notes'            => 'nullable|string|max:500',
        ], [
            'service_id.required'          => 'Debes seleccionar un servicio',
            'appointment_date.required'    => 'Debes seleccionar una fecha y hora',
            'appointment_date.date_format' => 'El formato de fecha no es válido',
            'appointment_date.after'       => 'La fecha debe ser posterior a la fecha actual',
            'notes.max'                    => 'Las notas no pueden exceder 500 caracteres',
        ]);

        $appointmentDate = Carbon::createFromFormat('Y-m-d\TH:i', $request->appointment_date);

        if (!$this->isTimeAvailable($appointmentDate, $request->service_id, $id)) {
            return back()->withErrors(['appointment_date' => 'Este horario no está disponible, elige otro'])
                         ->withInput();
        }

        $appointment->update([
            'service_id'       => $request->service_id,
            'appointment_date' => $appointmentDate,
            'notes'            => $request->notes,
        ]);

        return redirect()->route('appointment.show', $appointment->id)
                         ->with('success', 'Cita actualizada correctamente');
    }

    public function cancel($id)
    {
        $appointment = Appointment::findOrFail($id);

        if ($appointment->user_id !== auth()->id()) {
            abort(403);
        }

        if ($appointment->isCancelled()) {
            return back()->withErrors(['error' => 'Esta cita ya está cancelada']);
        }

        $appointment->update(['status' => 'cancelled']);

        return back()->with('success', 'Cita cancelada correctamente');
    }

    public function confirm($token)
    {
        $appointment = Appointment::with(['service', 'user', 'patient'])
                                  ->where('confirmation_token', $token)
                                  ->firstOrFail();

        if ($appointment->isConfirmed()) {
            return view('appointments.already-confirmed');
        }

        $appointment->update([
            'status'             => 'confirmed',
            'confirmed_at'       => now(),
            'confirmation_token' => null,
        ]);

        return view('appointments.confirmed', compact('appointment'));
    }

    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'date'       => 'required|date_format:Y-m-d',
        ]);

        $service    = Service::findOrFail($request->service_id);
        $date       = Carbon::createFromFormat('Y-m-d', $request->date);
        $dayOfWeek  = $date->dayOfWeek;

        $schedule = Schedule::where('day_of_week', $dayOfWeek)
                            ->where('is_active', true)
                            ->first();

        if (!$schedule) {
            return response()->json(['slots' => []]);
        }

        $slots   = [];
        $current = Carbon::parse($date->format('Y-m-d') . ' ' . $schedule->start_time);
        $end     = Carbon::parse($date->format('Y-m-d') . ' ' . $schedule->end_time);

        while ($current->copy()->addMinutes($service->duration)->lte($end)) {
            if ($this->isTimeAvailable($current, $service->id)) {
                $slots[] = [
                    'time'    => $current->format('H:i'),
                    'display' => $current->format('H:i'),
                ];
            }
            $current->addMinutes($service->duration + $service->buffer_time);
        }

        return response()->json(['slots' => $slots]);
    }

    private function isTimeAvailable($dateTime, $serviceId, $excludeId = null)
    {
        $service = Service::findOrFail($serviceId);
        $endTime = $dateTime->copy()->addMinutes($service->duration);

        $query = Appointment::where('service_id', $serviceId)
                            ->where('status', '!=', 'cancelled')
                            ->where(function ($q) use ($dateTime, $endTime) {
                                $q->whereBetween('appointment_date', [$dateTime, $endTime])
                                  ->orWhere(function ($q2) use ($dateTime, $endTime) {
                                      $q2->where('appointment_date', '<=', $dateTime)
                                         ->where('appointment_date', '>=', $endTime->copy()->subMinutes(60));
                                  });
                            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->count() === 0;
    }
}
