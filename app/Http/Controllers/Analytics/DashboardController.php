<?php

namespace App\Http\Controllers\Analytics;

use App\Http\Controllers\Controller;
use App\Models\KejadianKebakaran;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $tahun = (int) $request->input('tahun', 2026);

        $totalKebakaran = KejadianKebakaran::verifiedOnly()
            ->where('jenis_layanan', 'darurat')
            ->whereYear('tanggal_waktu_kejadian', $tahun)
            ->count();

        $totalKarhutla = KejadianKebakaran::verifiedOnly()
            ->where('jenis_layanan', 'darurat')
            ->whereHas('kategoriObjek', function($q) {
                $q->where('is_karhutla', true)->orWhere('nama_kategori', 'like', '%Lahan%');
            })
            ->whereYear('tanggal_waktu_kejadian', $tahun)
            ->count();

        $totalRescue = KejadianKebakaran::verifiedOnly()
            ->where('jenis_layanan', 'non_darurat')
            ->whereYear('tanggal_waktu_kejadian', $tahun)
            ->count();

        // Hotspot
        $hotspot = KejadianKebakaran::verifiedOnly()
            ->where('jenis_layanan', 'darurat')
            ->whereYear('tanggal_waktu_kejadian', $tahun)
            ->selectRaw('kecamatan_id, count(*) as total')
            ->groupBy('kecamatan_id')
            ->orderByDesc('total')
            ->with('kecamatan')
            ->first();
            
        $kecamatanTertinggi = null;
        if ($hotspot && $hotspot->kecamatan) {
            $kecamatanTertinggi = new \stdClass();
            $kecamatanTertinggi->kecamatan = $hotspot->kecamatan->nama_kecamatan;
        }

        // Total Seluruh Kejadian (Kebakaran + Rescue)
        $totalSeluruhKejadian = $totalKebakaran + $totalRescue;

        return view('analytics.dashboard', compact(
            'tahun',
            'totalSeluruhKejadian',
            'totalKebakaran',
            'totalKarhutla',
            'totalRescue',
            'kecamatanTertinggi'
        ));
    }
}