<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller: AuthController
 *
 * Menangani seluruh proses autentikasi pengguna sistem Damkar:
 *   - Menampilkan form login
 *   - Memproses percobaan login dan redirect berdasarkan role
 *   - Logout dan invalidasi sesi
 *
 * Tidak ada fitur registrasi publik — akun dibuat langsung oleh superadmin
 * atau via UserSeeder.
 */
class AuthController extends Controller
{
    /**
     * Menampilkan halaman form login.
     *
     * Jika user sudah login, Laravel akan mengalihkan lewat middleware 'auth'
     * secara otomatis di rute-rute yang dilindungi.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Memproses percobaan login dari form.
     *
     * Alur:
     *   1. Validasi field email dan password (wajib diisi)
     *   2. Coba autentikasi dengan Auth::attempt()
     *   3. Jika berhasil: regenerasi sesi (cegah session fixation), lalu
     *      redirect berdasarkan role:
     *        - 'admin'   → halaman verifikasi data
     *        - 'petugas' → halaman daftar laporan
     *   4. Jika gagal: kembali ke form dengan pesan error
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // Validasi input: keduanya wajib diisi
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            // Regenerasi ID sesi untuk mencegah serangan Session Fixation
            $request->session()->regenerate();

            // Arahkan ke dashboard sesuai role pengguna
            if (Auth::user()->role === 'admin') {
                return redirect()->intended('admin/dashboard');
            } else {
                return redirect()->intended('petugas/dashboard');
            }
        }

        // Autentikasi gagal: kembalikan dengan pesan error generik (tidak membocorkan field mana yang salah)
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    /**
     * Memproses permintaan logout.
     *
     * Menginvalidasi sesi aktif dan meregenerasi CSRF token untuk keamanan.
     * Setelah logout, pengguna diarahkan ke halaman publik (dashboard analitik).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        // Invalidasi sesi untuk menghapus semua data sesi aktif
        $request->session()->invalidate();

        // Regenerasi token CSRF untuk mencegah penyalahgunaan token lama
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
