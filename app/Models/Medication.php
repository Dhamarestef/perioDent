<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    protected $fillable = [
        'nombre',
        'categoria',
        'indicaciones',
    ];

    public static $categorias = [
        'Analgésicos',
        'Antibióticos',
    ];
}
