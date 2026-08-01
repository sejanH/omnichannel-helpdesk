<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscriptionIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && !$user->hasActiveSubscription()) {
            // Allow access to billing, profile, and logout pages so client can manage payment
            if ($request->routeIs('billing.*') || $request->routeIs('profile.*') || $request->routeIs('logout')) {
                return $next($request);
            }

            return redirect()->route('billing.index')->with('warning', 'Your subscription has expired or payment failed. Please update your payment method to restore workspace access.');
        }

        return $next($request);
    }
}
