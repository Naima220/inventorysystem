<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventTenantAccessToCentralRoutes
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $centralDomains = config('tenancy.central_domains', []);

        // Allow access only if the host is in central_domains
        if (!in_array($request->getHost(), $centralDomains)) {
            abort(404);
        }

        return $next($request);
    }
}
