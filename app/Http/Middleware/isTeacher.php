<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class isTeacher
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        if (!$request->user() || $request->user()->role()->name == "docente"
            || $request->user()->role()->name == "jefe de profesor") {
            return $next($request);
        }
        return response('No tienes permisos suficientes para realizar esta acción', 403);
    }
}
