<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;

abstract class Controller
{
    /**
     * ¿Puede el usuario autenticado operar sobre este registro?
     * - Doctor: siempre.
     * - Recepcionista: solo si el registro lo creó ella (user_id).
     *
     * Si algún día cambias a "la recepcionista ve todo", cambia el
     * return de la rama recepcionista por `true`.
     */
    protected function canManageRecord(?Model $record): bool
    {
        $user = auth()->user();

        if (! $user || ! $record) {
            return false;
        }

        if ($user->isDoctor()) {
            return true;
        }

        return (int) $record->user_id === (int) $user->id;
    }
}
