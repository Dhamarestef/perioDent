<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetItem extends Model
{
    protected $fillable = [
        'budget_id',
        'treatment_id',
        'concepto',
        'cantidad',
        'costo_unitario',
    ];

    protected $casts = [
        'costo_unitario' => 'decimal:2',
    ];

    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }

    public function treatment()
    {
        return $this->belongsTo(Treatment::class);
    }

    public function costoGlobal(): ?float
    {
        if (is_null($this->costo_unitario)) {
            return null;
        }

        return round($this->cantidad * (float) $this->costo_unitario, 2);
    }

    public function costoUnitarioFormateado(): string
    {
        return is_null($this->costo_unitario)
            ? 'Por cotizar'
            : '$' . number_format((float) $this->costo_unitario, 2);
    }

    public function costoGlobalFormateado(): string
    {
        $g = $this->costoGlobal();

        return is_null($g) ? 'Por cotizar' : '$' . number_format($g, 2);
    }
}
