<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Please log in as a customer to purchase a plan.',
                ], 401);
            }

            return redirect('/login')->with('error', 'Please log in to continue.');
        }

        $user = Auth::user();

        if (! in_array($user->role, $roles, true)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Only customer accounts can purchase a plan.',
                ], 403);
            }

            abort(403, 'Unauthorized access. You do not have permission to view this page.');
        }

        return $next($request);
    }
}
