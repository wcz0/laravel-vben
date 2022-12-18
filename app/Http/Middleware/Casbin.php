<?php

namespace App\Http\Middleware;

use App\Exceptions\AuthException;
use App\Exceptions\CasbinException;
use Closure;
use Illuminate\Http\Request;
use Lauthz\Facades\Enforcer;

class Casbin
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
        if (Enforcer::enforce((string)$request->user('admin')->id, '', substr($request->path(), 5))) {
            return $next($request);
        }
        throw new CasbinException();
    }
}
