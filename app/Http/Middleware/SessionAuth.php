<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SessionAuth
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
        // Define routes that do not require authentication
        $publicRoutes = [
            'login',
            'login/attempt', // Assuming your form submission goes here
            'logout' // Allow logout regardless of auth status
        ];

        // Get the current path, ensuring it's relative and clean
        $path = $request->path();

        // Check if the current path is one of the public routes
        if (in_array($path, $publicRoutes)) {
            return $next($request);
        }

        // For all other routes, check authentication
        if (!session('authenticated')) {
            // Redirect to login page if not authenticated
            return redirect('/login');
        }

        // If authenticated, proceed with the request
        return $next($request);
    }
}