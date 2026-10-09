<?php

namespace App\Http\Controllers\Analytics;

use App\Http\Controllers\Controller;
use App\Models\KejadianKebakaran;
use App\Models\Kecamatan;
use App\Models\KategoriObjek;
use App\Models\KategoriPenyebab;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $tahun              = (int) $request->input('tahun', 2026);
        $bulan              = $request->input('bulan');
        $kecamatanId        = $request->input('kecamatan_id');
        $kategoriObjekId    = $request->input('kategori_objek_id');
        $kategoriPenyebabId = $request->input('kategori_penyebab_id');
        $zonaLayanan        = $request->input('zona_layanan');

        // Master data untuk opsi filter
        $kecamatans             = Kecamatan::orderBy('nama_kecamatan')->get();
        $kategoriObjeks         = KategoriObjek::orderBy('nama_kategori')->get();
        $kategoriObjekKebakaran = KategoriObjek::where('jenis_layanan', 'kebakaran')->orderBy('nama_kategori')->get();
        $kategoriObjekRescue    = KategoriObjek::where('jenis_layanan', 'rescue')->orderBy('nama_kategori')->get();
        $kategoriPenyebabs      = KategoriPenyebab::orderBy('nama_penyebab')->get();

        // Daftar zona layanan baku (WMK)
        $zonaLayanans = [
            'WMK Pusat',
            'WMK UPTD 1',
            'WMK UPTD 2',
            'WMK UPTD 3',
            'Luar Daerah',
        ];

        // Query dasar KPI dengan filter aktif
        $baseQuery = KejadianKebakaran::verifiedOnly()
            ->whereYear('tanggal_waktu_kejadian', $tahun)
            ->when($bulan, fn($q) => $q->whereMonth('tanggal_waktu_kejadian', $bulan))
            ->when($kecamatanId, fn($q) => $q->where('kecamatan_id', $kecamatanId))
            ->when($kategoriObjekId, fn($q) => $q->where('kategori_objek_id', $kategoriObjekId))
            ->when($kategoriPenyebabId, fn($q) => $q->where('kategori_penyebab_id', $kategoriPenyebabId))
            ->when($zonaLayanan && $zonaLayanan !== 'Semua', function($q) use ($zonaLayanan) {
                $q->whereHas('kecamatan', fn($kq) => $kq->where('zona_layanan', $zonaLayanan));
            });

        $totalKebakaran = (clone $baseQuery)
            ->where('jenis_layanan', 'darurat')
            ->count();

        $totalKarhutla = (clone $baseQuery)
            ->where('jenis_layanan', 'darurat')
            ->whereHas('kategoriObjek', function($q) {
                $q->where('is_karhutla', true)->orWhere('nama_kategori', 'like', '%Lahan%');
            })
            ->count();

        $totalRescue = (clone $baseQuery)
            ->where('jenis_layanan', 'non_darurat')
            ->count();

        // Hotspot
        $hotspot = (clone $baseQuery)
            ->where('jenis_layanan', 'darurat')
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
            'bulan',
            'kecamatanId',
            'kategoriObjekId',
            'kategoriPenyebabId',
            'zonaLayanan',
            'zonaLayanans',
            'kecamatans',
            'kategoriObjeks',
            'kategoriObjekKebakaran',
            'kategoriObjekRescue',
            'kategoriPenyebabs',
            'totalSeluruhKejadian',
            'totalKebakaran',
            'totalKarhutla',
            'totalRescue',
            'kecamatanTertinggi'
        ));
    }
}