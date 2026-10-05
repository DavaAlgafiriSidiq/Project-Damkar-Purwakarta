<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Middleware: RoleMiddleware
 *
 * Mengecek apakah user yang sedang login memiliki hak akses (role)
 * yang sesuai dengan parameter yang diberikan pada rute.
 */
class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
    * @param  string  ...$roles  Role yang diizinkan (misal: 'admin', 'petugas')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Pastikan user sudah login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Cek apakah role user cocok dengan role yang disyaratkan
        if (!in_array(Auth::user()->role, $roles, true)) {
            abort(403, 'Akses ditolak. Role yang diizinkan: ' . implode(', ', $roles) . '.');
        }

        return $next($request);
    }
}