<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class SendAppointmentEmailReminders extends Command
{
    protected $signature = 'appointments:send-email-reminders';

    protected $description = 'Envía por email un recordatorio (con botón de confirmar) a los pacientes con cita mañana';

    public function handle(): int
    {
        $tomorrowStart = now()->addDay()->startOfDay();
        $tomorrowEnd = now()->addDay()->endOfDay();

        $appointments = Appointment::with(['patient'])
            ->whereBetween('appointment_date', [$tomorrowStart, $tomorrowEnd])
            ->whereIn('status', ['programada', 'confirmada'])
            ->whereNull('email_reminder_sent_at')
            ->get();

        $enviados = 0;
        $omitidos = 0;

        foreach ($appointments as $appointment) {
            $correo = $appointment->patient->correo ?? null;

            if (! $correo) {
                $omitidos++;
                continue;
            }

            try {
                Mail::send('emails.appointment-reminder', [
                    'appointment' => $appointment,
                    'confirmationLink' => $appointment->confirmation_token
                        ? route('appointment.confirm', $appointment->confirmation_token)
                        : route('dashboard'),
                    'cancelLink' => URL::signedRoute('appointment.cancel-public', ['appointment' => $appointment->id]),
                ], function ($message) use ($correo) {
                    $message->to($correo)
                        ->subject('Recordatorio: tu cita es mañana');
                });

                $appointment->update([
                    'email_reminder_sent_at' => now(),
                    'email_status'           => 'enviado',
                ]);
                $enviados++;
            } catch (\Exception $e) {
                $appointment->update(['email_status' => 'error']);
                $this->error("Error al enviar recordatorio a la cita #{$appointment->id}: {$e->getMessage()}");
                $omitidos++;
            }
        }

        $this->info("Recordatorios por email enviados: {$enviados}. Omitidos (sin correo o error): {$omitidos}.");

        return self::SUCCESS;
    }
}
