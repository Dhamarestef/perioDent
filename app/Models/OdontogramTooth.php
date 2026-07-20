<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OdontogramTooth extends Model
{
    protected $fillable = [
        'patient_id',
        'tooth_number',
        'estado',
    ];

    public static $estados = [
        'normal',
        'ausente',
        'pendiente',
        'finalizado',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
