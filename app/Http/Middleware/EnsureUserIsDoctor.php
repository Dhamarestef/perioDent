<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserIsDoctor
{
    public function handle(Request $request, Closure $next)
    {
        abort_unless($request->user()?->isDoctor(), 403, 'Esta sección es solo para el doctor.');

        return $next($request);
    }
}
