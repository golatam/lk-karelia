<?php

namespace App\Http\Middleware;

use Closure;

class IsActive
{
    public function handle($request, Closure $next)
    {
        if (!auth()->user()->is_active) {

            auth()->logout();

            return to_route('login');
        }

        return $next($request);
    }
}
