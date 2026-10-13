<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Treatment extends Model
{
    use SoftDeletes;

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
