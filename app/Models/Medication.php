<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    protected $fillable = [
        'nombre',
        'categoria',
        'indicaciones',
        'principio_activo',
        'presentacion',
        'dosis_habitual',
    ];

    public static $categorias = [
        'Analgésicos',
        'Antibióticos',
    ];
}
