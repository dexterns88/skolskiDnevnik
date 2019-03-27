<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;

class roleParent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = JWTAuth::parseToken()->authenticate();

        if ($user->role !== 'parent' && $user->role !== 'admin') {
            return response()->json(['error' => 'You don\'t have access to this content'], 403);
        }
        return $next($request);
    }
}
