<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OdontogramTreatment extends Model
{
    protected $fillable = [
        'patient_id',
        'tooth_number',
        'treatment_id',
        'observaciones',
        'finalizado',
        'fecha_realizacion',
    ];

    protected $casts = [
        'finalizado' => 'boolean',
        'fecha_realizacion' => 'date',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function treatment()
    {
        return $this->belongsTo(Treatment::class);
    }
}
