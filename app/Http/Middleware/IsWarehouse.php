<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsWarehouse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user() && auth()->user()->type == 'Warehouse' || auth()->user()->is_admin == '1' || auth()->user()->type == 'Admin') {
            return $next($request);
        }
        abort(403, 'Unauthorized action.');
    }
}
