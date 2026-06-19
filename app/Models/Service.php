<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'duration',
        'buffer_time',
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
