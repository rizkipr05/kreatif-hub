<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperadminOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->isSuperadmin()) {
            abort(403, 'Akses terbatas untuk Superadmin / Owner.');
        }

        return $next($request);
    }
}

