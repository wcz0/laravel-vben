<?php

namespace App\Http\Middleware;

use Casbin\Exceptions\CasbinException;
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
        if (Enforcer::enforce($request->user('admin')->id, '', '/'.$request->path())) {
            return $next($request);
        }
        throw new CasbinException();
    }
}
