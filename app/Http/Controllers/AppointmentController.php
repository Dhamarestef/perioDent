<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AppointmentController extends Controller
{
    /**
     * Vista principal: el calendario interactivo.
     */
    public function index()
    {
        $estados = Appointment::$estados;

        $prePatient = null;
        if (request('patient')) {
            $prePatient = Patient::find(request('patient'));
        }

        return view('appointments.index', compact('estados', 'prePatient'));
    }

    /**
     * Alimenta el calendario (FullCalendar) con las citas en formato evento.
     */
    public function events(Request $request)
    {
        $query = Appointment::with(['patient']);

        if ($request->filled('start')) {
            $query->where('appointment_date', '>=', $request->start);
        }
        if ($request->filled('end')) {
            $query->where('appointment_date', '<=', $request->end);
        }

        $appointments = $query->orderBy('appointment_date')->get();

        return response()->json($appointments->map(fn ($a) => $this->toEvent($a)));
    }

    /**
     * Buscador rápido de citas por nombre, expediente o teléfono (barra superior del calendario).
     */
    public function searchAppointments(Request $request)
    {
        $q = $request->get('q', '');

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $appointments = Appointment::with(['patient'])
            ->where(function ($query) use ($q) {
                $query->where('nombre_temporal', 'LIKE', "%{$q}%")
                    ->orWhere('telefono_temporal', 'LIKE', "%{$q}%")
                    ->orWhereHas('patient', function ($p) use ($q) {
                        $p->where('nombre', 'LIKE', "%{$q}%")
                            ->orWhere('numero_expediente', 'LIKE', "%{$q}%")
                            ->orWhere('telefono', 'LIKE', "%{$q}%");
                    });
            })
            ->orderBy('appointment_date', 'desc')
            ->limit(8)
            ->get();

        return response()->json($appointments->map(function ($a) {
            return [
                'id' => $a->id,
                'date' => $a->appointment_date->toIso8601String(),
                'label' => $a->nombrePaciente() . ' — ' . $a->appointment_date->format('d/m/Y H:i'),
                'status' => $a->estadoLabel(),
            ];
        }));
    }

    /**
     * Creación rápida desde el calendario: paciente registrado o de primera vez.
     */
    public function storeQuick(Request $request)
    {
        $request->validate([
            'appointment_date' => 'required|date',
            'end_date' => 'nullable|date|after:appointment_date',
            'duration_minutes' => 'nullable|integer|min:5',
            'notes' => 'nullable|string|max:500',
            'patient_id' => 'nullable|exists:patients,id',
            'nombre_temporal' => 'required_without:patient_id|string|max:255',
            'telefono_temporal' => 'nullable|string|max:20',
        ], [
            'appointment_date.required' => 'Selecciona la fecha y hora',
            'nombre_temporal.required_without' => 'Indica el nombre del paciente',
        ]);

        $isWalkIn = ! $request->filled('patient_id');
        $duration = $request->duration_minutes ?? 30;

        $start = Carbon::parse($request->appointment_date);
        $end = $request->end_date ? Carbon::parse($request->end_date) : $start->copy()->addMinutes($duration);

        $appointment = Appointment::create([
            'user_id' => auth()->id(),
            'patient_id' => $isWalkIn ? null : $request->patient_id,
            'nombre_temporal' => $isWalkIn ? $request->nombre_temporal : null,
            'telefono_temporal' => $isWalkIn ? $request->telefono_temporal : null,
            'duration_minutes' => $duration,
            'appointment_date' => $start,
            'end_date' => $end,
            'status' => 'programada',
            'notes' => $request->notes,
            'confirmation_token' => Str::random(32),
        ]);

        $appointment->load('patient');

        if ($appointment->patient && $appointment->patient->correo) {
            try {
                \Mail::send('emails.appointment-confirmation', [
                    'appointment' => $appointment,
                    'confirmationLink' => route('appointment.confirm', $appointment->confirmation_token),
                ], function ($message) use ($appointment) {
                    $message->to($appointment->patient->correo)
                        ->subject('Confirma tu cita');
                });
            } catch (\Exception $e) {
                // no interrumpir el flujo si falla el correo
            }
        }

        return response()->json(['ok' => true, 'event' => $this->toEvent($appointment)]);
    }

    /**
     * Arrastrar/soltar o redimensionar una cita en el calendario.
     */
    public function updateDates(Request $request, Appointment $appointment)
    {
        $request->validate([
            'appointment_date' => 'required|date',
            'end_date' => 'nullable|date|after:appointment_date',
        ]);

        $appointment->update([
            'appointment_date' => $request->appointment_date,
            'end_date' => $request->end_date,
        ]);

        return response()->json(['ok' => true, 'event' => $this->toEvent($appointment->fresh(['patient']))]);
    }

    /**
     * Editar los detalles completos de una cita desde el modal del calendario.
     */
    public function updateDetails(Request $request, Appointment $appointment)
    {
        $request->validate([
            'duration_minutes' => 'nullable|integer|min:5',
            'appointment_date' => 'required|date',
            'end_date' => 'nullable|date|after:appointment_date',
            'notes' => 'nullable|string|max:500',
            'nombre_temporal' => 'nullable|string|max:255',
            'telefono_temporal' => 'nullable|string|max:20',
        ]);

        $duration = $request->duration_minutes ?? $appointment->duration_minutes ?? 30;
        $start = Carbon::parse($request->appointment_date);
        $end = $request->end_date ? Carbon::parse($request->end_date) : $start->copy()->addMinutes($duration);

        $data = [
            'duration_minutes' => $duration,
            'appointment_date' => $start,
            'end_date' => $end,
            'notes' => $request->notes,
        ];

        if (! $appointment->patient_id) {
            $data['nombre_temporal'] = $request->nombre_temporal;
            $data['telefono_temporal'] = $request->telefono_temporal;
        }

        $appointment->update($data);

        return response()->json(['ok' => true, 'event' => $this->toEvent($appointment->fresh(['patient']))]);
    }

    /**
     * Cambiar el estado de la cita (Programada/Confirmada/En consulta/Finalizada/Cancelada/No asistió).
     */
    public function updateStatus(Request $request, Appointment $appointment)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(Appointment::$estados)),
        ]);

        $data = ['status' => $request->status];
        if ($request->status === 'confirmada' && ! $appointment->confirmed_at) {
            $data['confirmed_at'] = now();
        }

        $appointment->update($data);

        return response()->json(['ok' => true, 'event' => $this->toEvent($appointment->fresh(['patient']))]);
    }

    /**
     * Eliminar definitivamente una cita.
     */
    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return response()->json(['ok' => true]);
    }

    private function toEvent(Appointment $a): array
    {
        $start = $a->appointment_date;
        $end = $a->finCalculado();

        return [
            'id' => $a->id,
            'title' => $a->nombrePaciente(),
            'start' => $start->toIso8601String(),
            'end' => $end->toIso8601String(),
            'color' => $a->estadoColor(),
            'extendedProps' => [
                'status' => $a->status,
                'statusLabel' => $a->estadoLabel(),
                'patientId' => $a->patient_id,
                'patientName' => $a->nombrePaciente(),
                'expediente' => $a->patient->numero_expediente ?? null,
                'phone' => $a->telefonoPaciente(),
                'durationMinutes' => $a->duration_minutes ?? 30,
                'notes' => $a->notes,
                'isWalkIn' => $a->esPacienteNuevo(),
                'nombreTemporal' => $a->nombre_temporal,
                'telefonoTemporal' => $a->telefono_temporal,
            ],
        ];
    }

    // ══════════════════════════════════════════════════════════
    // Métodos anteriores — formulario simple /appointments/create,
    // confirmación por email, etc.
    // ══════════════════════════════════════════════════════════

    public function create()
    {
        $prePatient = null;
        if (request('patient')) {
            $prePatient = Patient::find(request('patient'));
        }

        return view('appointments.create', compact('prePatient'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id'       => 'required|exists:patients,id',
            'duration_minutes' => 'nullable|integer|min:5',
            'appointment_date' => 'required|date_format:Y-m-d\TH:i|after:now',
            'notes'            => 'nullable|string|max:500',
        ], [
            'patient_id.required'       => 'Debes seleccionar o registrar un paciente',
            'patient_id.exists'         => 'El paciente seleccionado no es válido',
            'appointment_date.required' => 'Debes seleccionar una fecha y hora',
            'appointment_date.date_format' => 'El formato de fecha no es válido',
            'appointment_date.after'    => 'La fecha debe ser posterior a la fecha actual',
            'notes.max'                 => 'Las notas no pueden exceder 500 caracteres',
        ]);

        $duration = $request->duration_minutes ?? 30;
        $appointmentDate = Carbon::createFromFormat('Y-m-d\TH:i', $request->appointment_date);
        $patient         = Patient::findOrFail($request->patient_id);

        if (!$this->isTimeAvailable($appointmentDate, $duration)) {
            return back()->withErrors(['appointment_date' => 'Este horario no está disponible, elige otro'])
                         ->withInput();
        }

        $appointment = Appointment::create([
            'user_id'            => auth()->id(),
            'patient_id'         => $patient->id,
            'duration_minutes'   => $duration,
            'appointment_date'   => $appointmentDate,
            'end_date'           => $appointmentDate->copy()->addMinutes($duration),
            'notes'              => $request->notes,
            'confirmation_token' => Str::random(32),
            'status'             => 'programada',
        ]);

        if ($patient->correo) {
            try {
                \Mail::send('emails.appointment-confirmation', [
                    'appointment'      => $appointment,
                    'confirmationLink' => route('appointment.confirm', $appointment->confirmation_token),
                ], function ($message) use ($patient) {
                    $message->to($patient->correo)
                            ->subject('Confirma tu cita');
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
        $appointment = Appointment::with(['patient', 'user'])
                                  ->findOrFail($id);

        if ($appointment->user_id !== auth()->id()) {
            abort(403);
        }

        return view('appointments.show', compact('appointment'));
    }

    public function edit($id)
    {
        $appointment = Appointment::with(['patient'])->findOrFail($id);

        if ($appointment->user_id !== auth()->id()) {
            abort(403);
        }

        if ($appointment->isConfirmed()) {
            return back()->withErrors(['error' => 'No puedes editar una cita ya confirmada']);
        }

        return view('appointments.edit', compact('appointment'));
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
            'duration_minutes' => 'nullable|integer|min:5',
            'appointment_date' => 'required|date_format:Y-m-d\TH:i|after:now',
            'notes'            => 'nullable|string|max:500',
        ], [
            'appointment_date.required'    => 'Debes seleccionar una fecha y hora',
            'appointment_date.date_format' => 'El formato de fecha no es válido',
            'appointment_date.after'       => 'La fecha debe ser posterior a la fecha actual',
            'notes.max'                    => 'Las notas no pueden exceder 500 caracteres',
        ]);

        $duration = $request->duration_minutes ?? $appointment->duration_minutes ?? 30;
        $appointmentDate = Carbon::createFromFormat('Y-m-d\TH:i', $request->appointment_date);

        if (!$this->isTimeAvailable($appointmentDate, $duration, $id)) {
            return back()->withErrors(['appointment_date' => 'Este horario no está disponible, elige otro'])
                         ->withInput();
        }

        $appointment->update([
            'duration_minutes' => $duration,
            'appointment_date' => $appointmentDate,
            'end_date'         => $appointmentDate->copy()->addMinutes($duration),
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

        $appointment->update(['status' => 'cancelada']);

        return back()->with('success', 'Cita cancelada correctamente');
    }

    public function confirm($token)
    {
        $appointment = Appointment::with(['user', 'patient'])
                                ->where('confirmation_token', $token)
                                ->first();

        if (! $appointment) {
            return view('appointments.confirm-invalid');
        }

        if ($appointment->isConfirmed()) {
            return view('appointments.already-confirmed');
        }

        if ($appointment->isCancelled()) {
            return view('appointments.confirm-invalid');
        }

        if ($appointment->tokenExpirado()) {
            return view('appointments.confirm-expired', compact('appointment'));
        }

        $appointment->update([
            'status'             => 'confirmada',
            'confirmed_at'       => now(),
            'confirmation_token' => null,
        ]);

        return view('appointments.confirmed', compact('appointment'));
    }

    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'duration_minutes' => 'nullable|integer|min:5',
            'date'       => 'required|date_format:Y-m-d',
        ]);

        $duration   = $request->duration_minutes ?? 30;
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

        while ($current->copy()->addMinutes($duration)->lte($end)) {
            if ($this->isTimeAvailable($current, $duration)) {
                $slots[] = [
                    'time'    => $current->format('H:i'),
                    'display' => $current->format('H:i'),
                ];
            }
            $current->addMinutes($duration);
        }

        return response()->json(['slots' => $slots]);
    }

    private function isTimeAvailable($dateTime, $duration, $excludeId = null)
    {
        $endTime = $dateTime->copy()->addMinutes($duration);

        $query = Appointment::where('status', '!=', 'cancelada')
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
