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
        $user = auth()->user('admin');
        $routes = $request->segments();
        $route  = '';
        foreach ($routes as $key => $value) {
            if ($key < 2) {
                continue;
            }
            if ($key == count($routes) - 1 && (int) $value) {
                $value = '{id}';
            }
            $route = $route . '/' . $value;
        }
        $act    = strtolower($request->method());
        if (Enforcer::enforce((string)$user->id, $route, $act )) {
            return $next($request);
        }
        throw new CasbinException();
    }
}
