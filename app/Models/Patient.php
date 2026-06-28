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

    /**
     * Genera el siguiente número de expediente automáticamente
     */
    public static function generarNumeroExpediente(): string
    {
        $ultimo = self::orderBy('id', 'desc')->first();
        $numero = $ultimo ? (int) substr($ultimo->numero_expediente, 4) + 1 : 1;
        return 'EXP-' . str_pad($numero, 4, '0', STR_PAD_LEFT);
    }
}
