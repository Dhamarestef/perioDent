<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
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

    public static function generarNumeroExpediente(): string
    {
        $ultimo = self::orderBy('id', 'desc')->first();
        $numero = $ultimo ? (int) substr($ultimo->numero_expediente, 4) + 1 : 1;
        return 'EXP-' . str_pad($numero, 4, '0', STR_PAD_LEFT);
    }

    public function expedienteIncompleto(): bool
    {
        return $this->domicilio_actual === 'Por completar'
            || $this->lugar_nacimiento === 'Por completar'
            || $this->edad === 0;
    }
}
