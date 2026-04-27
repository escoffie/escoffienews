<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = env('ADMIN_TOKEN');
        
        if ($request->header('Authorization') !== $token) {
            return response()->json(['message' => 'Unauthorized. Invalid admin token.'], 401);
        }

        return $next($request);
    }
}
