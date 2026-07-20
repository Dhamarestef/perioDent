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
        'service_id',
        'appointment_date',
        'end_date',
        'status',
        'notes',
        'confirmation_token',
        'confirmed_at',
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
        'end_date'         => 'datetime',
        'confirmed_at'     => 'datetime',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
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

    // ── Alias de compatibilidad con vistas existentes (show/edit/patients.show) ──
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
}
