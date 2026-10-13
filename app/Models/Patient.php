<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'numero_expediente',
        'fecha_registro',
        'nombre',
        'edad',
        'sexo',
        'escolaridad',
        'estado_civil',
        'lugar_nacimiento',
        'fecha_nacimiento',
        'domicilio_actual',
        'telefono',
        'lugar_residencia',
        'correo',
        'alergias',
    ];

    protected $casts = [
        'fecha_registro'   => 'date',
        'fecha_nacimiento' => 'date',
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function odontogramTeeth()
    {
        return $this->hasMany(OdontogramTooth::class);
    }

    public function odontogramTreatments()
    {
        return $this->hasMany(OdontogramTreatment::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }

    public static function generarNumeroExpediente(): string
    {
        // withTrashed(): un paciente oculto sigue ocupando su número (columna unique)
        $ultimo = self::withTrashed()->orderBy('id', 'desc')->first();
        $numero = $ultimo ? (int) substr($ultimo->numero_expediente, 4) + 1 : 1;
        return 'EXP-' . str_pad($numero, 4, '0', STR_PAD_LEFT);
    }

    public function expedienteIncompleto(): bool
    {
        return $this->domicilio_actual === 'Por completar'
            || $this->lugar_nacimiento === 'Por completar'
            || $this->edad === 0;
    }

    public function alergiasParaReceta(): string
    {
        return $this->alergias ? $this->alergias : 'Interrogadas y negadas.';
    }
}
