<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permissionKey): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (!$user->canAccess($permissionKey)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses Ditolak: Anda tidak memiliki izin untuk fitur ini.'
                ], 403);
            }

            return redirect()->route('dashboard')->with('error', 'Akses Ditolak: Anda tidak memiliki izin untuk mengakses fitur tersebut.');
        }

        return $next($request);
    }
}
