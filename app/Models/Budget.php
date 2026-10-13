<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Budget extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'patient_id',
        'user_id',
        'fecha',
        'diagnostico',
        'descuento_porcentaje',
        'promocion_bienvenida',
    ];

    protected $casts = [
        'fecha' => 'date',
        'descuento_porcentaje' => 'decimal:2',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class)->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(BudgetItem::class)->orderBy('orden');
    }

    /** Suma de los conceptos con costo definido (excluye "Por cotizar"). */
    public function subtotal(): float
    {
        return (float) $this->items->sum(fn ($item) => $item->costoGlobal() ?? 0);
    }

    public function tieneDescuento(): bool
    {
        return ! is_null($this->descuento_porcentaje) && (float) $this->descuento_porcentaje > 0;
    }

    public function descuentoMonto(): float
    {
        if (! $this->tieneDescuento()) {
            return 0;
        }

        return round($this->subtotal() * ((float) $this->descuento_porcentaje / 100), 2);
    }

    public function total(): float
    {
        return round($this->subtotal() - $this->descuentoMonto(), 2);
    }

    public function tieneConceptosPorCotizar(): bool
    {
        return $this->items->contains(fn ($item) => is_null($item->costo_unitario));
    }

    public function fechaTexto(): string
    {
        return Str::upper($this->fecha->translatedFormat('d \d\e F \d\e Y'));
    }
}
