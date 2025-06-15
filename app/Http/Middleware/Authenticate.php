<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Authenticate
{
    public function handle(Request $request, Closure $next)
    {
        // Get the current path
        $path = $request->path();
        
        // Always allow access to login and logout routes
        if (in_array($path, ['login', 'logout'])) {
            return $next($request);
        }

        // For all other routes, check authentication
        if (!session('authenticated')) {
            return redirect('/login');
        }

        // If authenticated, proceed with the request
        return $next($request);
    }
} 