<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSubscription
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Skip for super admin if they are on central domain
        if (auth()->check() && auth()->user()->hasRole('super_admin')) {
            return $next($request);
        }

        // 2. Skip for impersonation and expired routes
        if ($request->routeIs('subscription.expired') || str_contains($request->path(), 'tenancy/impersonate')) {
            return $next($request);
        }

        // Only check if we are in a tenant context
        if (tenant()) {
            $isActive = tenant('is_active');
            $endsAt = tenant('subscription_ends_at');

            if (!$isActive || ($endsAt && \Carbon\Carbon::parse($endsAt)->isPast())) {
                
                // If it's a web request, redirect to expired page
                if (!$request->expectsJson()) {
                     return redirect()->route('subscription.expired');
                }

                return response()->json(['error' => 'Subscription expired'], 402);
            }
        }

        return $next($request);
    }
}