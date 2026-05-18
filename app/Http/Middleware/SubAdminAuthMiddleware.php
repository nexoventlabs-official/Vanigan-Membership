<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SubAdminAuthMiddleware
{
    /**
     * Handle an incoming request for the Sub-Admin panel.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session('sub_admin_logged_in')) {
            return redirect()->route('sub_admin.login')->with('error', 'Please login first');
        }

        return $next($request);
    }
}
