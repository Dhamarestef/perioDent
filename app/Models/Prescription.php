<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Prescription extends Model
{
    protected $fillable = [
        'patient_id',
        'user_id',
        'fecha_emision',
        'alergias',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(PrescriptionItem::class)->orderBy('orden');
    }

    public function fechaEmisionTexto(): string
    {
        return Str::upper($this->fecha_emision->translatedFormat('d \d\e F \d\e Y'));
    }

    public function alergiasTexto(): string
    {
        return $this->alergias ? $this->alergias : 'Interrogadas y negadas.';
    }
}
