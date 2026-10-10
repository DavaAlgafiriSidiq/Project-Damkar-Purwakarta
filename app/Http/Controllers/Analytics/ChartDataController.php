<?php

namespace App\Http\Controllers\Analytics;

use App\Http\Controllers\Controller;
use App\Models\KejadianKebakaran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * Controller: ChartDataController
 *
 * Menyediakan endpoint JSON untuk seluruh chart pada Dashboard Analitik Publik.
 *
 * ATURAN BISNIS MUTLAK (wajib dipatuhi di SETIAP method):
 *   1. Selalu gunakan scope `verifiedOnly()` — TIDAK BOLEH ada query tanpa filter ini.
 *   2. Semua data diambil HANYA dari tabel transaksional `kejadian_kebakaran`.
 *      Tabel `stat_kebakaran_per_objek`, `stat_kebakaran_per_penyebab`,
 *      `stat_kebakaran_per_kecamatan`, dan `stat_rescue_per_objek` adalah
 *      tabel historis LEGACY dan TIDAK BOLEH digunakan di sini.
 *   3. Parameter `tahun` bersifat opsional dengan default tahun berjalan.
 *
 * Semua method mengembalikan JsonResponse dengan format konsisten:
 *   { labels: [...], datasets: [{ label: '...', data: [...] }] }
 */
class ChartDataController extends Controller
{
    /**
     * Tren kebakaran bulanan (kejadian darurat) sepanjang satu tahun.
     *
     * Endpoint: GET /api/analytics/tren-kebakaran-bulanan?tahun=YYYY
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function trenKebakaranBulanan(Request $request): JsonResponse
    {
        $tahun = (int) $request->input('tahun', now()->year);

        // Ambil HANYA data kebakaran (darurat) yang sudah terverifikasi pada tahun tersebut
        $data = KejadianKebakaran::verifiedOnly()
            ->where('jenis_layanan', 'darurat')
            ->whereYear('tanggal_waktu_kejadian', $tahun)
            ->when($request->filled('bulan'), fn($q) => $q->whereMonth('tanggal_waktu_kejadian', $request->input('bulan')))
            ->when($request->filled('kecamatan_id'), fn($q) => $q->where('kecamatan_id', $request->input('kecamatan_id')))
            ->when($request->filled('kategori_objek_id'), fn($q) => $q->where('kategori_objek_id', $request->input('kategori_objek_id')))
            ->when($request->filled('kategori_penyebab_id'), fn($q) => $q->where('kategori_penyebab_id', $request->input('kategori_penyebab_id')))
            ->when($request->filled('zona_layanan') && $request->input('zona_layanan') !== 'Semua', function ($q) use ($request) {
                $q->whereHas('kecamatan', fn($kq) => $kq->where('zona_layanan', $request->input('zona_layanan')));
            })
            ->get()
            ->groupBy(function ($item) {
                // Kelompokkan berdasarkan nomor bulan (1-12)
                return Carbon::parse($item->tanggal_waktu_kejadian)->format('n');
            });

        $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        $totals = array_fill(0, 12, 0);

        foreach ($data as $bulan => $items) {
            $totals[$bulan - 1] = count($items);
        }

        return response()->json([
            'labels'   => $labels,
            'datasets' => [
                [
                    'label' => 'Jumlah Kebakaran',
                    'data'  => $totals,
                ],
            ],
        ]);
    }

    /**
     * Tren operasi rescue bulanan (kejadian non_darurat) sepanjang satu tahun.
     *
     * Endpoint: GET /api/analytics/tren-rescue-bulanan?tahun=YYYY
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function trenRescueBulanan(Request $request): JsonResponse
    {
        $tahun = (int) $request->input('tahun', now()->year);

        $data = KejadianKebakaran::verifiedOnly()
            ->where('jenis_layanan', 'non_darurat')
            ->whereYear('tanggal_waktu_kejadian', $tahun)
            ->when($request->filled('bulan'), fn($q) => $q->whereMonth('tanggal_waktu_kejadian', $request->input('bulan')))
            ->when($request->filled('kecamatan_id'), fn($q) => $q->where('kecamatan_id', $request->input('kecamatan_id')))
            ->when($request->filled('kategori_objek_id'), fn($q) => $q->where('kategori_objek_id', $request->input('kategori_objek_id')))
            ->when($request->filled('kategori_penyebab_id'), fn($q) => $q->where('kategori_penyebab_id', $request->input('kategori_penyebab_id')))
            ->when($request->filled('zona_layanan') && $request->input('zona_layanan') !== 'Semua', function ($q) use ($request) {
                $q->whereHas('kecamatan', fn($kq) => $kq->where('zona_layanan', $request->input('zona_layanan')));
            })
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->tanggal_waktu_kejadian)->format('n');
            });

        $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        $totals = array_fill(0, 12, 0);

        foreach ($data as $bulan => $items) {
            $totals[$bulan - 1] = count($items);
        }

        return response()->json([
            'labels'   => $labels,
            'datasets' => [['label' => 'Jumlah Operasi Rescue', 'data' => $totals]],
        ]);
    }

    /**
     * Distribusi kebakaran berdasarkan jenis objek yang terbakar.
     *
     * Endpoint: GET /api/analytics/distribusi-objek-kebakaran?tahun=YYYY
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function distribusiObjekKebakaran(Request $request): JsonResponse
    {
        $tahun = (int) $request->input('tahun', now()->year);

        $data = KejadianKebakaran::with('kategoriObjek')
            ->verifiedOnly()
            ->where('jenis_layanan', 'darurat')
            ->whereHas('kategoriObjek', fn($q) => $q->where('jenis_layanan', 'kebakaran'))
            ->whereYear('tanggal_waktu_kejadian', $tahun)
            ->when($request->filled('bulan'), fn($q) => $q->whereMonth('tanggal_waktu_kejadian', $request->input('bulan')))
            ->when($request->filled('kecamatan_id'), fn($q) => $q->where('kecamatan_id', $request->input('kecamatan_id')))
            ->when($request->filled('kategori_objek_id'), fn($q) => $q->where('kategori_objek_id', $request->input('kategori_objek_id')))
            ->when($request->filled('kategori_penyebab_id'), fn($q) => $q->where('kategori_penyebab_id', $request->input('kategori_penyebab_id')))
            ->when($request->filled('zona_layanan') && $request->input('zona_layanan') !== 'Semua', function ($q) use ($request) {
                $q->whereHas('kecamatan', fn($kq) => $kq->where('zona_layanan', $request->input('zona_layanan')));
            })
            ->get()
            ->groupBy(function ($item) {
                return $item->kategoriObjek ? $item->kategoriObjek->nama_kategori : 'Lain-lain';
            })
            ->map(function ($group) { return count($group); })
            ->sortDesc();

        // Paksa 'Lain-lain' / 'Lainnya' berada di urutan paling akhir
        $lainKeys = $data->keys()->filter(fn($k) => str_contains(strtolower($k), 'lain'));
        foreach ($lainKeys as $lk) {
            $val = $data->pull($lk);
            $data->put($lk, $val);
        }

        return response()->json([
            'labels'   => $data->keys(),
            'datasets' => [['label' => 'Jumlah Kejadian', 'data' => $data->values()]],
        ]);
    }

    /**
     * Distribusi kebakaran berdasarkan dugaan penyebab.
     *
     * Endpoint: GET /api/analytics/distribusi-penyebab-kebakaran?tahun=YYYY
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function distribusiPenyebabKebakaran(Request $request): JsonResponse
    {
        $tahun = (int) $request->input('tahun', now()->year);

        $data = KejadianKebakaran::with('kategoriPenyebab')
            ->verifiedOnly()
            ->where('jenis_layanan', 'darurat')
            ->whereYear('tanggal_waktu_kejadian', $tahun)
            ->when($request->filled('bulan'), fn($q) => $q->whereMonth('tanggal_waktu_kejadian', $request->input('bulan')))
            ->when($request->filled('kecamatan_id'), fn($q) => $q->where('kecamatan_id', $request->input('kecamatan_id')))
            ->when($request->filled('kategori_objek_id'), fn($q) => $q->where('kategori_objek_id', $request->input('kategori_objek_id')))
            ->when($request->filled('kategori_penyebab_id'), fn($q) => $q->where('kategori_penyebab_id', $request->input('kategori_penyebab_id')))
            ->when($request->filled('zona_layanan') && $request->input('zona_layanan') !== 'Semua', function ($q) use ($request) {
                $q->whereHas('kecamatan', fn($kq) => $kq->where('zona_layanan', $request->input('zona_layanan')));
            })
            ->get()
            ->groupBy(function ($item) {
                return $item->kategoriPenyebab ? $item->kategoriPenyebab->nama_penyebab : 'Belum diketahui';
            })
            ->map(function ($group) { return count($group); })
            ->sortDesc();

        // Paksa 'Lain-lain' / 'Lainnya' berada di urutan paling akhir
        $lainKeys = $data->keys()->filter(fn($k) => str_contains(strtolower($k), 'lain'));
        foreach ($lainKeys as $lk) {
            $val = $data->pull($lk);
            $data->put($lk, $val);
        }

        return response()->json([
            'labels'   => $data->keys(),
            'datasets' => [['label' => 'Jumlah Kejadian', 'data' => $data->values()]],
        ]);
    }

    /**
     * Sebaran kejadian kebakaran berdasarkan kecamatan.
     *
     * Endpoint: GET /api/analytics/sebaran-per-kecamatan?tahun=YYYY
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sebaranPerKecamatan(Request $request): JsonResponse
    {
        $tahun = (int) $request->input('tahun', now()->year);

        $data = KejadianKebakaran::with('kecamatan')
            ->verifiedOnly()
            ->where('jenis_layanan', 'darurat')
            ->whereYear('tanggal_waktu_kejadian', $tahun)
            ->when($request->filled('bulan'), fn($q) => $q->whereMonth('tanggal_waktu_kejadian', $request->input('bulan')))
            ->when($request->filled('kecamatan_id'), fn($q) => $q->where('kecamatan_id', $request->input('kecamatan_id')))
            ->when($request->filled('kategori_objek_id'), fn($q) => $q->where('kategori_objek_id', $request->input('kategori_objek_id')))
            ->when($request->filled('kategori_penyebab_id'), fn($q) => $q->where('kategori_penyebab_id', $request->input('kategori_penyebab_id')))
            ->when($request->filled('zona_layanan') && $request->input('zona_layanan') !== 'Semua', function ($q) use ($request) {
                $q->whereHas('kecamatan', fn($kq) => $kq->where('zona_layanan', $request->input('zona_layanan')));
            })
            ->get()
            ->groupBy(function ($item) {
                return $item->kecamatan ? $item->kecamatan->nama_kecamatan : 'Tidak Diketahui';
            })
            ->map(function ($group) { return count($group); })
            ->sortDesc();

        return response()->json([
            'labels'   => $data->keys(),
            'datasets' => [['label' => 'Jumlah Kebakaran', 'data' => $data->values()]],
        ]);
    }

    /**
     * Distribusi operasi rescue berdasarkan jenis kasus penyelamatan.
     *
     * Endpoint: GET /api/analytics/distribusi-jenis-rescue?tahun=YYYY
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function distribusiJenisRescue(Request $request): JsonResponse
    {
        $tahun = (int) $request->input('tahun', now()->year);

        $data = KejadianKebakaran::with('kategoriObjek')
            ->verifiedOnly()
            ->where('jenis_layanan', 'non_darurat')
            ->whereHas('kategoriObjek', fn($q) => $q->where('jenis_layanan', 'rescue'))
            ->whereYear('tanggal_waktu_kejadian', $tahun)
            ->when($request->filled('bulan'), fn($q) => $q->whereMonth('tanggal_waktu_kejadian', $request->input('bulan')))
            ->when($request->filled('kecamatan_id'), fn($q) => $q->where('kecamatan_id', $request->input('kecamatan_id')))
            ->when($request->filled('kategori_objek_id'), fn($q) => $q->where('kategori_objek_id', $request->input('kategori_objek_id')))
            ->when($request->filled('kategori_penyebab_id'), fn($q) => $q->where('kategori_penyebab_id', $request->input('kategori_penyebab_id')))
            ->when($request->filled('zona_layanan') && $request->input('zona_layanan') !== 'Semua', function ($q) use ($request) {
                $q->whereHas('kecamatan', fn($kq) => $kq->where('zona_layanan', $request->input('zona_layanan')));
            })
            ->get()
            ->groupBy(function ($item) {
                return $item->kategoriObjek ? $item->kategoriObjek->nama_kategori : 'Lain-lain';
            })
            ->map(function ($group) { return count($group); })
            ->sortDesc();

        // Paksa 'Lain-lain' / 'Lainnya' berada di urutan paling akhir
        $lainKeys = $data->keys()->filter(fn($k) => str_contains(strtolower($k), 'lain'));
        foreach ($lainKeys as $lk) {
            $val = $data->pull($lk);
            $data->put($lk, $val);
        }

        return response()->json([
            'labels'   => $data->keys(),
            'datasets' => [['label' => 'Jumlah Rescue', 'data' => $data->values()]],
        ]);
    }

    /**
     * Sebaran kejadian operasi rescue berdasarkan kecamatan.
     *
     * Endpoint: GET /api/analytics/sebaran-rescue-per-kecamatan?tahun=YYYY
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sebaranRescuePerKecamatan(Request $request): JsonResponse
    {
        $tahun = (int) $request->input('tahun', now()->year);

        $data = KejadianKebakaran::with('kecamatan')
            ->verifiedOnly()
            ->where('jenis_layanan', 'non_darurat')
            ->whereYear('tanggal_waktu_kejadian', $tahun)
            ->when($request->filled('bulan'), fn($q) => $q->whereMonth('tanggal_waktu_kejadian', $request->input('bulan')))
            ->when($request->filled('kecamatan_id'), fn($q) => $q->where('kecamatan_id', $request->input('kecamatan_id')))
            ->when($request->filled('kategori_objek_id'), fn($q) => $q->where('kategori_objek_id', $request->input('kategori_objek_id')))
            ->when($request->filled('kategori_penyebab_id'), fn($q) => $q->where('kategori_penyebab_id', $request->input('kategori_penyebab_id')))
            ->when($request->filled('zona_layanan') && $request->input('zona_layanan') !== 'Semua', function ($q) use ($request) {
                $q->whereHas('kecamatan', fn($kq) => $kq->where('zona_layanan', $request->input('zona_layanan')));
            })
            ->get()
            ->groupBy(function ($item) {
                return $item->kecamatan ? $item->kecamatan->nama_kecamatan : 'Tidak Diketahui';
            })
            ->map(function ($group) { return count($group); })
            ->sortDesc();

        return response()->json([
            'labels'   => $data->keys(),
            'datasets' => [['label' => 'Jumlah Operasi Rescue', 'data' => $data->values()]],
        ]);
    }

    /**
     * Tren komparasi bulanan antara kebakaran dan rescue dalam satu chart.
     *
     * Endpoint: GET /api/analytics/tren-komparasi-bulanan?tahun=YYYY
     *
     * Diimplementasikan dengan query langsung ke tabel transaksional.
     * TIDAK menggunakan tabel stat_* lama.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function trenKomparasiBulanan(Request $request): JsonResponse
    {
        $tahun = (int) $request->input('tahun', now()->year);

        $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];

        // Ambil semua kejadian terverifikasi tahun tersebut, kelompokkan per bulan + jenis
        $semua = KejadianKebakaran::verifiedOnly()
            ->whereYear('tanggal_waktu_kejadian', $tahun)
            ->when($request->filled('bulan'), fn($q) => $q->whereMonth('tanggal_waktu_kejadian', $request->input('bulan')))
            ->when($request->filled('kecamatan_id'), fn($q) => $q->where('kecamatan_id', $request->input('kecamatan_id')))
            ->when($request->filled('kategori_objek_id'), fn($q) => $q->where('kategori_objek_id', $request->input('kategori_objek_id')))
            ->when($request->filled('kategori_penyebab_id'), fn($q) => $q->where('kategori_penyebab_id', $request->input('kategori_penyebab_id')))
            ->when($request->filled('zona_layanan') && $request->input('zona_layanan') !== 'Semua', function ($q) use ($request) {
                $q->whereHas('kecamatan', fn($kq) => $kq->where('zona_layanan', $request->input('zona_layanan')));
            })
            ->get();

        $kebakaran = array_fill(0, 12, 0);
        $rescue    = array_fill(0, 12, 0);

        foreach ($semua as $item) {
            $bulanIndex = Carbon::parse($item->tanggal_waktu_kejadian)->month - 1;
            if ($item->jenis_layanan === 'darurat') {
                $kebakaran[$bulanIndex]++;
            } else {
                $rescue[$bulanIndex]++;
            }
        }

        return response()->json([
            'labels'   => $labels,
            'datasets' => [
                [
                    'label' => 'Kebakaran',
                    'data'  => $kebakaran,
                ],
                [
                    'label' => 'Rescue',
                    'data'  => $rescue,
                ],
            ],
        ]);
    }

    /**
     * Ringkasan statistik global untuk tahun tertentu.
     *
     * Endpoint: GET /api/analytics/ringkasan-statistik?tahun=YYYY
     *
     * Mengembalikan angka-angka KPI utama:
     *   - total_kebakaran    : total insiden kebakaran (darurat)
     *   - total_rescue       : total operasi rescue (non_darurat)
     *   - total_kejadian     : gabungan keduanya
     *   - total_kerugian     : estimasi total kerugian (rupiah)
     *   - total_terselamatkan: estimasi total aset terselamatkan (rupiah)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ringkasanStatistik(Request $request): JsonResponse
    {
        $tahun = (int) $request->input('tahun', now()->year);

        // Query dasar: hanya data verified pada tahun yang diminta beserta filter aktif (Waktu & Lokasi)
        $baseQuery = KejadianKebakaran::verifiedOnly()
            ->whereYear('tanggal_waktu_kejadian', $tahun)
            ->when($request->filled('bulan'), fn($q) => $q->whereMonth('tanggal_waktu_kejadian', $request->input('bulan')))
            ->when($request->filled('kecamatan_id'), fn($q) => $q->where('kecamatan_id', $request->input('kecamatan_id')))
            ->when($request->filled('zona_layanan') && $request->input('zona_layanan') !== 'Semua', function ($q) use ($request) {
                $q->whereHas('kecamatan', fn($kq) => $kq->where('zona_layanan', $request->input('zona_layanan')));
            });

        $totalKebakaran     = (clone $baseQuery)->where('jenis_layanan', 'darurat')->count();
        $totalRescue        = (clone $baseQuery)->where('jenis_layanan', 'non_darurat')->count();
        $totalKerugian      = (clone $baseQuery)->sum('taksiran_kerugian');
        $totalTerselamatkan = (clone $baseQuery)->sum('taksiran_terselamatkan');

        $totalKarhutla = (clone $baseQuery)
            ->where('jenis_layanan', 'darurat')
            ->whereHas('kategoriObjek', function ($q) {
                $q->where('is_karhutla', true)->orWhere('nama_kategori', 'like', '%Lahan%');
            })
            ->count();

        $hotspot = (clone $baseQuery)
            ->where('jenis_layanan', 'darurat')
            ->selectRaw('kecamatan_id, count(*) as total')
            ->groupBy('kecamatan_id')
            ->orderByDesc('total')
            ->with('kecamatan')
            ->first();

        $kecamatanHotspot = ($hotspot && $hotspot->kecamatan) ? $hotspot->kecamatan->nama_kecamatan : '-';

        return response()->json([
            'tahun'               => $tahun,
            'total_kebakaran'     => $totalKebakaran,
            'total_karhutla'      => $totalKarhutla,
            'total_rescue'        => $totalRescue,
            'total_kejadian'      => $totalKebakaran + $totalRescue,
            'total_kerugian'      => $totalKerugian,
            'total_terselamatkan' => $totalTerselamatkan,
            'kecamatan_hotspot'   => $kecamatanHotspot,
        ]);
    }

    /**
     * Endpoint Live Alert: Mendeteksi insiden aktif yang sedang dalam penanganan di lapangan.
     * Syarat: status_operasi = 'dalam_penanganan' & tanggal_waktu_kejadian dalam 12 jam terakhir (safety fallback).
     *
     * Endpoint: GET /api/analytics/live-alert
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function liveAlert(): JsonResponse
    {
        $batasWaktu = now()->subHours(12);

        $kejadian = KejadianKebakaran::where('status_operasi', 'dalam_penanganan')
            ->where('tanggal_waktu_kejadian', '>=', $batasWaktu)
            ->with('kecamatan')
            ->latest('tanggal_waktu_kejadian')
            ->first();

        if ($kejadian) {
            return response()->json([
                'is_active'      => true,
                'jenis_layanan'  => $kejadian->jenis_layanan,
                'kecamatan'      => $kejadian->kecamatan->nama_kecamatan ?? 'Purwakarta',
                'waktu'          => Carbon::parse($kejadian->tanggal_waktu_kejadian)->format('Y-m-d H:i'),
                'status_operasi' => $kejadian->status_operasi,
            ]);
        }

        return response()->json([
            'is_active' => false,
        ]);
    }
}
