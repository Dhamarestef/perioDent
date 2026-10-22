<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class LogReceptionistActivity
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $user = $request->user();
        $route = $request->route();
        $name = $route?->getName();

        if (! $user || ! $user->isReceptionist() || ! $name) {
            return $response;
        }

        $tracked = ! $request->isMethodSafe()
            || in_array($name, config('permissions.logged_get_routes', []), true);

        if (! $tracked || $response->getStatusCode() >= 400) {
            return $response;
        }

        // Si fue un redirect por error de validación, no hubo acción real.
        if ($response->isRedirection() && $request->hasSession() && $request->session()->has('errors')) {
            return $response;
        }

        $type = $id = null;
        foreach ($route->parameters() as $key => $value) {
            if ($value instanceof Model) {
                $type = class_basename($value);
                $id = $value->getKey();
                break;
            }
            if (is_numeric($value)) {
                $type = (string) $key;
                $id = (int) $value;
                break;
            }
        }

        ActivityLog::record($user, $name, null, $type, $id);

        return $response;
    }
}
