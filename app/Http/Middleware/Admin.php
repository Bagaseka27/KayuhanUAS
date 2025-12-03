<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class Admin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect('/'); 
        }

        if (Auth::user()->role == 'admin') {
            return $next($request);
        }

        // Kalau bukan admin, redirect ke dashboard barista
        return redirect('/barista/dashboard');
    }
}