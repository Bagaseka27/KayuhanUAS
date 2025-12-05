<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class Barista
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect('/'); 
        }

        if (Auth::user()->role == 'Barista') {
            return $next($request);
        }

        // Kalau bukan barista, redirect ke dashboard admin
        return redirect('/dashboard');
    }
}