<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KejadianKebakaran;
use App\Models\Kecamatan;
use App\Models\KategoriObjek;
use App\Models\KategoriPenyebab;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller: LaporanController (Admin)
 *
 * Menghasilkan rekapitulasi data tahunan dalam bentuk tabel matriks bulanan (Jan - Des)
 * bergaya format lembar kerja Excel Dinas Pemadam Kebakaran Purwakarta.
 *
 * Menerapkan:
 *   - Scope verifiedOnly() secara mutlak (hanya data sah/terverifikasi).
 *   - Pemisahan fisik murni antara Operasi Kebakaran dan Operasi Penyelamatan (Rescue).
 *   - Filter Zona Layanan baku (WMK Pusat, WMK UPTD 1, WMK UPTD 2, WMK UPTD 3, Luar Daerah).
 */
class LaporanController extends Controller
{
    /**
     * Menampilkan halaman matriks rekapitulasi tahunan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function rekapMatriks(Request $request): View
    {
        // Parameter Filter
        $tahun       = (int) $request->input('tahun', now()->year);
        $zonaLayanan = $request->input('zona_layanan'); // null atau nama zona baku

        // Ambil daftar tahun unik yang tersedia di database
        $tahunList = KejadianKebakaran::selectRaw('YEAR(tanggal_waktu_kejadian) as th')
            ->whereNotNull('tanggal_waktu_kejadian')
            ->distinct()
            ->orderByDesc('th')
            ->pluck('th')
            ->toArray();

        if (empty($tahunList)) {
            $tahunList = [now()->year];
        } elseif (!in_array(now()->year, $tahunList)) {
            array_unshift($tahunList, now()->year);
        }

        // Daftar zona layanan baku sesuai aturan 8.10 di ai-context.md
        $zonaList = [
            'WMK Pusat',
            'WMK UPTD 1',
            'WMK UPTD 2',
            'WMK UPTD 3',
            'Luar Daerah',
        ];

        // Daftar label bulan 1 - 12
        $namaBulan = [
            1  => 'Jan',
            2  => 'Feb',
            3  => 'Mar',
            4  => 'Apr',
            5  => 'Mei',
            6  => 'Jun',
            7  => 'Jul',
            8  => 'Ags',
            9  => 'Sep',
            10 => 'Okt',
            11 => 'Nov',
            12 => 'Des',
        ];

        // Base Query untuk filter tahun & zona layanan terpilih
        $baseQuery = KejadianKebakaran::verifiedOnly()
            ->whereYear('tanggal_waktu_kejadian', $tahun)
            ->when($zonaLayanan, function ($query, $zona) {
                $query->whereHas('kecamatan', fn($k) => $k->where('zona_layanan', $zona));
            });

        // ─────────────────────────────────────────────────────────────────────
        // 1. MATRIKS 1: DISTRIBUSI WILAYAH KECAMATAN (SEMUA KEJADIAN)
        // ─────────────────────────────────────────────────────────────────────
        $kecamatans = Kecamatan::when($zonaLayanan, fn($q) => $q->where('zona_layanan', $zonaLayanan))
            ->orderBy('id')
            ->get();

        $rawKecamatan = (clone $baseQuery)
            ->whereNotNull('kecamatan_id')
            ->selectRaw('kecamatan_id, MONTH(tanggal_waktu_kejadian) as bulan, COUNT(*) as total')
            ->groupBy('kecamatan_id', 'bulan')
            ->get();

        $matrixKecamatan = [];
        $totalBulanKecamatan = array_fill(1, 12, 0);
        $grandTotalKecamatan = 0;

        foreach ($kecamatans as $kec) {
            $matrixKecamatan[$kec->id] = [
                'id'           => $kec->id,
                'nama'         => $kec->nama_kecamatan,
                'zona_layanan' => $kec->zona_layanan ?? 'Umum',
                'bulan'        => array_fill(1, 12, 0),
                'total'        => 0,
            ];
        }

        foreach ($rawKecamatan as $item) {
            $kId = (int) $item->kecamatan_id;
            $bln = (int) $item->bulan;
            $cnt = (int) $item->total;

            if (isset($matrixKecamatan[$kId])) {
                $matrixKecamatan[$kId]['bulan'][$bln] = $cnt;
                $matrixKecamatan[$kId]['total'] += $cnt;
                $totalBulanKecamatan[$bln] += $cnt;
                $grandTotalKecamatan += $cnt;
            }
        }

        // ─────────────────────────────────────────────────────────────────────
        // 2. MATRIKS 2: OPERASI PEMADAMAN KEBAKARAN (MURNI DARURAT)
        //    (a) Berdasarkan Objek Kebakaran (ID 1-9: Bangunan, Hutan & Lahan)
        //    (b) Berdasarkan Dugaan Penyebab Api (11 Kategori)
        // ─────────────────────────────────────────────────────────────────────
        $queryKebakaran = (clone $baseQuery)->where('jenis_layanan', 'darurat');

        // 2.A Objek Kebakaran
        $objekKebakaranList = KategoriObjek::whereIn('id', range(1, 9))->orderBy('id')->get();
        $rawObjekKebakaran = (clone $queryKebakaran)
            ->whereNotNull('kategori_objek_id')
            ->selectRaw('kategori_objek_id, MONTH(tanggal_waktu_kejadian) as bulan, COUNT(*) as total')
            ->groupBy('kategori_objek_id', 'bulan')
            ->get();

        $matrixObjekKebakaran = [];
        $totalBulanObjekKebakaran = array_fill(1, 12, 0);
        $grandTotalObjekKebakaran = 0;

        foreach ($objekKebakaranList as $obj) {
            $tipeLabel = $obj->is_karhutla || str_contains(strtolower($obj->nama_kategori), 'lahan') ? 'Hutan & Lahan' : 'Bangunan';
            $tipeBadge = $tipeLabel === 'Hutan & Lahan' ? 'badge-light-danger' : 'badge-light-warning';

            $matrixObjekKebakaran[$obj->id] = [
                'id'               => $obj->id,
                'nama'             => $obj->nama_kategori,
                'tipe_label'       => $tipeLabel,
                'tipe_badge_class' => $tipeBadge,
                'bulan'            => array_fill(1, 12, 0),
                'total'            => 0,
            ];
        }

        foreach ($rawObjekKebakaran as $item) {
            $oId = (int) $item->kategori_objek_id;
            $bln = (int) $item->bulan;
            $cnt = (int) $item->total;

            if (isset($matrixObjekKebakaran[$oId])) {
                $matrixObjekKebakaran[$oId]['bulan'][$bln] = $cnt;
                $matrixObjekKebakaran[$oId]['total'] += $cnt;
                $totalBulanObjekKebakaran[$bln] += $cnt;
                $grandTotalObjekKebakaran += $cnt;
            }
        }

        // 2.B Dugaan Penyebab Api
        $kategoriPenyebabs = KategoriPenyebab::orderBy('id')->get();
        $rawPenyebab = (clone $queryKebakaran)
            ->whereNotNull('kategori_penyebab_id')
            ->selectRaw('kategori_penyebab_id, MONTH(tanggal_waktu_kejadian) as bulan, COUNT(*) as total')
            ->groupBy('kategori_penyebab_id', 'bulan')
            ->get();

        $matrixPenyebab = [];
        $totalBulanPenyebab = array_fill(1, 12, 0);
        $grandTotalPenyebab = 0;

        foreach ($kategoriPenyebabs as $penyebab) {
            $matrixPenyebab[$penyebab->id] = [
                'id'    => $penyebab->id,
                'nama'  => $penyebab->nama_penyebab,
                'bulan' => array_fill(1, 12, 0),
                'total' => 0,
            ];
        }

        foreach ($rawPenyebab as $item) {
            $pId = (int) $item->kategori_penyebab_id;
            $bln = (int) $item->bulan;
            $cnt = (int) $item->total;

            if (isset($matrixPenyebab[$pId])) {
                $matrixPenyebab[$pId]['bulan'][$bln] = $cnt;
                $matrixPenyebab[$pId]['total'] += $cnt;
                $totalBulanPenyebab[$bln] += $cnt;
                $grandTotalPenyebab += $cnt;
            }
        }

        // ─────────────────────────────────────────────────────────────────────
        // 3. MATRIKS 3: OPERASI PENYELAMATAN / RESCUE (MURNI NON-DARURAT)
        //    Berdasarkan Jenis Objek Penyelamatan (ID 10-22)
        // ─────────────────────────────────────────────────────────────────────
        $queryRescue = (clone $baseQuery)->where('jenis_layanan', 'non_darurat');

        $objekRescueList = KategoriObjek::whereIn('id', range(10, 22))->orderBy('id')->get();
        $rawRescue = (clone $queryRescue)
            ->whereNotNull('kategori_objek_id')
            ->selectRaw('kategori_objek_id, MONTH(tanggal_waktu_kejadian) as bulan, COUNT(*) as total')
            ->groupBy('kategori_objek_id', 'bulan')
            ->get();

        $matrixRescue = [];
        $totalBulanRescue = array_fill(1, 12, 0);
        $grandTotalRescue = 0;

        foreach ($objekRescueList as $obj) {
            $isAnimal = in_array($obj->id, [10, 11, 12, 13, 14, 15]);
            $tipeLabel = $isAnimal ? 'Penyelamatan Hewan' : 'Evakuasi Khusus';
            $tipeBadge = $isAnimal ? 'badge-light-info' : 'badge-light-success';

            $matrixRescue[$obj->id] = [
                'id'               => $obj->id,
                'nama'             => $obj->nama_kategori,
                'tipe_label'       => $tipeLabel,
                'tipe_badge_class' => $tipeBadge,
                'bulan'            => array_fill(1, 12, 0),
                'total'            => 0,
            ];
        }

        foreach ($rawRescue as $item) {
            $rId = (int) $item->kategori_objek_id;
            $bln = (int) $item->bulan;
            $cnt = (int) $item->total;

            if (isset($matrixRescue[$rId])) {
                $matrixRescue[$rId]['bulan'][$bln] = $cnt;
                $matrixRescue[$rId]['total'] += $cnt;
                $totalBulanRescue[$bln] += $cnt;
                $grandTotalRescue += $cnt;
            }
        }

        // Ringkasan KPI untuk header halaman (dipengaruhi filter tahun & zona layanan)
        $summary = [
            'total_kejadian'  => (clone $baseQuery)->count(),
            'total_kebakaran' => (clone $queryKebakaran)->count(),
            'total_rescue'    => (clone $queryRescue)->count(),
        ];

        return view('admin.laporan.matriks', compact(
            'tahun',
            'tahunList',
            'zonaLayanan',
            'zonaList',
            'namaBulan',
            // Matriks Wilayah
            'matrixKecamatan',
            'totalBulanKecamatan',
            'grandTotalKecamatan',
            // Matriks Kebakaran
            'matrixObjekKebakaran',
            'totalBulanObjekKebakaran',
            'grandTotalObjekKebakaran',
            'matrixPenyebab',
            'totalBulanPenyebab',
            'grandTotalPenyebab',
            // Matriks Rescue
            'matrixRescue',
            'totalBulanRescue',
            'grandTotalRescue',
            // KPI Summary
            'summary'
        ));
    }
}
