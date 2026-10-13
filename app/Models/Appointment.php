<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'user_id',
        'patient_id',
        'nombre_temporal',
        'telefono_temporal',
        'duration_minutes',
        'appointment_date',
        'end_date',
        'status',
        'notes',
        'confirmation_token',
        'confirmed_at',
        'email_reminder_sent_at',
        'email_status',
    ];

    protected $casts = [
        'duration_minutes'        => 'integer',
        'appointment_date'        => 'datetime',
        'end_date'                => 'datetime',
        'confirmed_at'            => 'datetime',
        'email_reminder_sent_at'  => 'datetime',
    ];

    /**
     * Catálogo de estados de la cita con su etiqueta y color para el calendario.
     */
    public static array $estados = [
        'programada'  => ['label' => 'Programada',   'color' => '#6c757d'],
        'confirmada'  => ['label' => 'Confirmada',   'color' => '#28a745'],
        'en_consulta' => ['label' => 'En consulta',  'color' => '#17a2b8'],
        'finalizada'  => ['label' => 'Finalizada',   'color' => '#667eea'],
        'cancelada'   => ['label' => 'Cancelada',    'color' => '#dc3545'],
        'no_asistio'  => ['label' => 'No asistió',   'color' => '#f0a500'],
    ];

    /**
     * Catálogo de estados de envío del correo, para el panel admin.
     */
    public static array $emailEstados = [
        'pendiente' => ['label' => 'Aún no enviado',  'color' => '#6c757d'],
        'enviado'   => ['label' => 'Enviado',         'color' => '#28a745'],
        'error'     => ['label' => 'Error al enviar', 'color' => '#dc3545'],
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class)->withTrashed();
    }

    public function estadoLabel(): string
    {
        return self::$estados[$this->status]['label'] ?? ucfirst($this->status);
    }

    public function estadoColor(): string
    {
        return self::$estados[$this->status]['color'] ?? '#999999';
    }

    public function nombrePaciente(): string
    {
        return $this->patient->nombre ?? $this->nombre_temporal ?? 'Paciente';
    }

    public function telefonoPaciente(): ?string
    {
        return $this->patient->telefono ?? $this->telefono_temporal ?? null;
    }

    public function esPacienteNuevo(): bool
    {
        return is_null($this->patient_id);
    }

    /**
     * Texto legible de la duración de la cita, p. ej. "30 min".
     */
    public function duracionTexto(): string
    {
        return ($this->duration_minutes ?? 30) . ' min';
    }

    /**
     * Fecha/hora de finalización calculada, por si end_date no estuviera guardado.
     */
    public function finCalculado()
    {
        return $this->end_date ?: $this->appointment_date->copy()->addMinutes((int) ($this->duration_minutes ?? 30));
    }

    /**
     * El enlace de confirmación deja de ser válido 2 horas antes de la cita.
     */
    public function tokenExpirado(): bool
    {
        if (! $this->confirmation_token) {
            return true;
        }

        return now()->greaterThanOrEqualTo($this->appointment_date->copy()->subHours(2));
    }

    public function emailEstadoLabel(): string
    {
        return self::$emailEstados[$this->email_status ?? 'pendiente']['label'] ?? 'Desconocido';
    }

    public function emailEstadoColor(): string
    {
        return self::$emailEstados[$this->email_status ?? 'pendiente']['color'] ?? '#999999';
    }

    // ── Alias de compatibilidad con vistas existentes ──
    public function isPending(): bool
    {
        return $this->status === 'programada';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmada';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelada';
    }

    public function isEnConsulta(): bool
    {
        return $this->status === 'en_consulta';
    }

    public function isFinalizada(): bool
    {
        return $this->status === 'finalizada';
    }

    public function isNoAsistio(): bool
    {
        return $this->status === 'no_asistio';
    }

    public function duracion(): int
    {
        return (int) ($this->duration_minutes ?? 30);
    }
}
