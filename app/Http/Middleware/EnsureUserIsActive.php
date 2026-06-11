<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && ! auth()->user()->is_active) {
            auth()->user()->tokens()->delete();

            return response()->json([
                'success' => false,
                'message' => 'Tu cuenta está desactivada. Contacta a secretaría para más información.',
            ], 403);
        }

        return $next($request);
    }
}
