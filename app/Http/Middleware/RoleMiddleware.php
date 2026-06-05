<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        if ($user->role !== $role) {
            $dashboardRoute = match ($user->role) {
                'admin' => 'admin.dashboard',
                'guru' => 'guru.dashboard',
                'kepala_sekolah' => 'kepsek.dashboard',
                'wali_murid' => 'wali.dashboard',
                default => null,
            };

            if ($dashboardRoute) {
                return redirect()->route($dashboardRoute)->with('error', 'Anda tidak memiliki hak akses ke halaman tersebut.');
            }

            auth()->logout();
            return redirect()->route('login')->with('error', 'Hak akses tidak valid.');
        }

        return $next($request);
    }
}
