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
     * @param  string  $role  Role yang diizinkan (misal: 'admin')
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Pastikan user sudah login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Cek apakah role user cocok dengan role yang disyaratkan
        if (Auth::user()->role !== $role) {
            // Jika tidak cocok, arahkan ke dashboard publik atau tampilkan 403
            abort(403, 'Akses Ditolak. Anda tidak memiliki izin (role: ' . $role . ') untuk halaman ini.');
        }

        return $next($request);
    }
}