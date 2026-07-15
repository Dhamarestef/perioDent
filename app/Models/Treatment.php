<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Treatment extends Model
{
    protected $fillable = [
        'abreviacion',
        'nombre',
        'costo',
        'categoria',
    ];

    public static $categorias = [
        'General',
        'Endodoncia',
        'Cirugia y Periodoncia',
    ];

    public function getCostoFormateadoAttribute(): string
    {
        return $this->costo
            ? '$' . number_format($this->costo, 2)
            : 'Por cotizar';
    }
}
