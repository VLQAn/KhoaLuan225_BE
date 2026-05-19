<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle request
     */
    public function handle(
        Request $request,
        Closure $next,
        string $role
    ): Response {

        $user = Auth::user();

        if (
            !$user ||
            !$user->hasRole($role)
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        return $next($request);
    }
}
