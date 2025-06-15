<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class Guest
{
    public function handle(Request $request, Closure $next)
    {
        if (Cache::has('user_email')) {
            return redirect()->route('home');
        }
        return $next($request);
    }
} 