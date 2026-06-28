<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AppointmentController extends Controller
{
    /**
     * Mostrar lista de citas del usuario
     */
    public function index()
    {
        $appointments = auth()->user()->appointments()
            ->with('service')
            ->orderBy('appointment_date', 'desc')
            ->get();

        return view('appointments.index', compact('appointments'));
    }

    /**
     * Mostrar formulario para crear cita
     */
    public function create()
    {
        $services = Service::all();
        return view('appointments.create', compact('services'));
    }

    /**
     * Guardar nueva cita
     */
    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'appointment_date' => 'required|date_format:Y-m-d\TH:i|after:now',
            'notes' => 'nullable|string|max:500',
        ], [
            'service_id.required' => 'Debes seleccionar un servicio',
            'appointment_date.required' => 'Debes seleccionar fecha y hora',
            'appointment_date.after' => 'La fecha debe ser en el futuro',
            'notes.max' => 'Las notas no pueden exceder 500 caracteres',
        ]);

        // Verificar disponibilidad
        $appointmentDate = Carbon::createFromFormat('Y-m-d\TH:i', $request->appointment_date);

        if (!$this->isTimeAvailable($appointmentDate, $request->service_id)) {
            return back()->withErrors(['appointment_date' => 'Este horario no está disponible']);
        }

        // Crear cita
        $appointment = Appointment::create([
            'user_id' => auth()->id(),
            'service_id' => $request->service_id,
            'appointment_date' => $appointmentDate,
            'notes' => $request->notes,
            'confirmation_token' => Str::random(32),
            'status' => 'pending',
        ]);

        // Enviar email de confirmación
        \Mail::send('emails.appointment-confirmation', [
            'appointment' => $appointment,
            'confirmationLink' => route('appointment.confirm', $appointment->confirmation_token),
        ], function ($message) use ($appointment) {
            $message->to($appointment->user->email)
                    ->subject('Confirma tu cita - ' . $appointment->service->name);
        });

        return redirect()->route('appointment.index')
                        ->with('success', 'Cita creada exitosamente. Revisa tu email para confirmar.');
    }

    /**
     * Mostrar detalles de cita
     */
    public function show($id)
    {
        $appointment = Appointment::findOrFail($id);

        // Verificar que es su cita o es admin
        if ($appointment->user_id !== auth()->id()) {
            abort(403);
        }

        return view('appointments.show', compact('appointment'));
    }

    /**
     * Mostrar formulario para editar cita
     */
    public function edit($id)
    {
        $appointment = Appointment::findOrFail($id);

        if ($appointment->user_id !== auth()->id()) {
            abort(403);
        }

        // No se puede editar si ya está confirmada
        if ($appointment->isConfirmed()) {
            return back()->withErrors(['error' => 'No puedes editar una cita confirmada']);
        }

        $services = Service::all();
        return view('appointments.edit', compact('appointment', 'services'));
    }

    /**
     * Actualizar cita
     */
    public function update(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        if ($appointment->user_id !== auth()->id()) {
            abort(403);
        }

        if ($appointment->isConfirmed()) {
            return back()->withErrors(['error' => 'No puedes editar una cita confirmada']);
        }

        $request->validate([
            'service_id' => 'required|exists:services,id',
            'appointment_date' => 'required|date_format:Y-m-d\TH:i|after:now',
            'notes' => 'nullable|string|max:500',
        ]);

        $appointmentDate = Carbon::createFromFormat('Y-m-d\TH:i', $request->appointment_date);

        if (!$this->isTimeAvailable($appointmentDate, $request->service_id, $id)) {
            return back()->withErrors(['appointment_date' => 'Este horario no está disponible']);
        }

        $appointment->update([
            'service_id' => $request->service_id,
            'appointment_date' => $appointmentDate,
            'notes' => $request->notes,
        ]);

        return redirect()->route('appointment.show', $appointment->id)
                        ->with('success', 'Cita actualizada correctamente');
    }

    /**
     * Cancelar cita
     */
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

    /**
     * Confirmar cita por email
     */
    public function confirm($token)
    {
        $appointment = Appointment::where('confirmation_token', $token)->firstOrFail();

        if ($appointment->isConfirmed()) {
            return view('appointments.already-confirmed');
        }

        $appointment->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
            'confirmation_token' => null,
        ]);

        return view('appointments.confirmed', compact('appointment'));
    }

    /**
     * Obtener horarios disponibles (AJAX)
     */
    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'date' => 'required|date_format:Y-m-d',
        ]);

        $service = Service::findOrFail($request->service_id);
        $date = Carbon::createFromFormat('Y-m-d', $request->date);

        // Obtener horario del día
        $dayOfWeek = $date->dayOfWeek;
        $schedule = Schedule::where('day_of_week', $dayOfWeek)
                            ->where('is_active', true)
                            ->first();

        if (!$schedule) {
            return response()->json(['slots' => []]);
        }

        // Generar slots de tiempo disponibles
        $slots = [];
        $current = Carbon::parse($date->format('Y-m-d') . ' ' . $schedule->start_time);
        $end = Carbon::parse($date->format('Y-m-d') . ' ' . $schedule->end_time);

        while ($current->addMinutes($service->duration) <= $end) {
            $slotStart = $current->copy()->subMinutes($service->duration);

            // Verificar si está disponible
            if ($this->isTimeAvailable($slotStart, $service->id)) {
                $slots[] = [
                    'time' => $slotStart->format('H:i'),
                    'display' => $slotStart->format('H:i'),
                ];
            }

            $current = $current->copy()->addMinutes($service->buffer_time);
        }

        return response()->json(['slots' => $slots]);
    }

    /**
     * Verificar si horario está disponible
     */
    private function isTimeAvailable($dateTime, $serviceId, $excludeAppointmentId = null)
    {
        $service = Service::findOrFail($serviceId);
        $endTime = $dateTime->copy()->addMinutes($service->duration);

        $query = Appointment::where('service_id', $serviceId)
                           ->where('status', 'confirmed')
                           ->where(function ($q) use ($dateTime, $endTime) {
                               $q->whereBetween('appointment_date', [$dateTime, $endTime])
                                 ->orWhereBetween('appointment_date', [$dateTime, $endTime]);
                           });

        if ($excludeAppointmentId) {
            $query->where('id', '!=', $excludeAppointmentId);
        }

        return $query->count() === 0;
    }
}
