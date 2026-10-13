<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Medication extends Model
{
    use SoftDeletes;

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
