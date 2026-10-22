<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Gate explícito para las secciones exclusivas del doctor.
        Gate::define('doctor', fn (User $user) => $user->isDoctor());

        // El doctor siempre pasa cualquier Gate (hereda permisos futuros).
        // La recepcionista solo pasa si el ability es un permiso del catálogo
        // y lo tiene asignado. Si no es un permiso del catálogo, se deja
        // decidir a otros Gates/Policies (null).
        Gate::before(function (User $user, string $ability) {
            if ($user->isDoctor()) {
                return true;
            }

            if (in_array($ability, User::allPermissionKeys(), true)) {
                return $user->hasPermission($ability) ?: null;
            }

            return null;
        });
    }
}
