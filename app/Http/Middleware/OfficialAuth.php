<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class OfficialAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->session()->has('official_team_id')) {
            return redirect('/official/login');
        }

        return $next($request);
    }
}
