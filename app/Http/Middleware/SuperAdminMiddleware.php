<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Hubi user login yahay
        if (!auth()->check()) {
            abort(403, 'Unauthorized');
        }

        // Hubi inuu leeyahay role super_admin
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403, 'Only Super Admin allowed.');
        }

        return $next($request);
    }
}