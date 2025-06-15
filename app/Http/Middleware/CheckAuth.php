<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Skip auth check for login routes
        if ($request->is('login') || $request->is('logout')) {
            return $next($request);
        }

        // Check if user is logged in
        if (!session()->has('user_email')) {
            return redirect('/login');
        }

        return $next($request);
    }
} 