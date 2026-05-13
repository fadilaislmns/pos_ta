<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
//use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        // cek user login
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // load role (PENTING!)
        $user->load('role');

        // cek role ada atau tidak
        if (!$user->role) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak memiliki role'
            ], 403);
        }

        // cek apakah role sesuai
        $userRole = strtolower(str_replace(' ', '_', $user->role->role_name));

        $allowedRoles = array_map(function ($role) {
            return strtolower(str_replace(' ', '_', $role));
        }, $roles);

        if (!in_array($userRole, $allowedRoles)) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak'
            ], 403);
        }

        return $next($request);
    }
}
