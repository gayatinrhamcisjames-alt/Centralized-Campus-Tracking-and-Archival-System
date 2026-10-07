<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(
        Request $request,
        Closure $next,
        string ...$roles
    ): Response {
        if (! auth()->check() || ! in_array(auth()->user()->role, $roles, true)) {
            abort(403, 'Access Denied. You are not authorized to access this page.');
        }

        return $next($request);
    }
}
