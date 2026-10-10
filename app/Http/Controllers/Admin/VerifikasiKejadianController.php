<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KejadianKebakaran;
use App\Models\Kecamatan;
use App\Models\KategoriObjek;
use App\Models\KategoriPenyebab;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;

/**
 * Controller: VerifikasiKejadianController (Role: Administrator / Humas)
 * 
 * Bertanggung jawab memvalidasi laporan draft dari petugas lapangan,
 * melakukan koreksi/edit data kejadian, approval verifikasi, penghapusan,
 * serta menghitung rekapitulasi data berdasarkan filter aktif.
 */
class VerifikasiKejadianController extends Controller
{
    /**
     * Menampilkan daftar seluruh laporan kejadian dengan filter status, kecamatan, jenis layanan,
     * rentang tanggal, dan kata kunci. Menghitung KPI global sekaligus rekapitulasi data terfilter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        // 1. KPI Global (Seluruh Data di Sistem)
        $countDraft    = KejadianKebakaran::where('status_verifikasi', 'draft')->count();
        $countVerified = KejadianKebakaran::where('status_verifikasi', 'verified')->count();
        $countTotal    = KejadianKebakaran::count();

        // 2. Ambil Parameter Filter
        $status        = $request->get('status', 'draft'); // Default 'draft' agar fokus ke antrean
        $jenisLayanan  = $request->get('jenis_layanan');
        $kecamatanId   = $request->get('kecamatan_id');
        $rentang       = $request->get('rentang', 'all');
        $tanggalDari   = $request->get('tanggal_dari');
        $tanggalSampai = $request->get('tanggal_sampai');
        $search        = $request->get('search');

        // 3. Bangun Query
        $query = KejadianKebakaran::with([
            'kecamatan.zonaLayanan',
            'kategoriObjek',
            'kategoriPenyebab',
            'pelapor',
            'verifikator'
        ]);

        // Filter status
        if ($status !== 'all' && in_array($status, ['draft', 'verified'])) {
            $query->where('status_verifikasi', $status);
        }

        // Filter jenis layanan
        if ($jenisLayanan && in_array($jenisLayanan, ['darurat', 'non_darurat'])) {
            $query->where('jenis_layanan', $jenisLayanan);
        }

        // Filter kecamatan
        if ($kecamatanId) {
            $query->where('kecamatan_id', $kecamatanId);
        }

        // Filter rentang tanggal
        if ($rentang === 'hari_ini') {
            $query->whereDate('tanggal_waktu_kejadian', Carbon::today());
        } elseif ($rentang === 'minggu_ini') {
            $query->whereBetween('tanggal_waktu_kejadian', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($rentang === 'bulan_ini') {
            $query->whereMonth('tanggal_waktu_kejadian', Carbon::now()->month)
                  ->whereYear('tanggal_waktu_kejadian', Carbon::now()->year);
        } elseif ($tanggalDari && $tanggalSampai) {
            $query->whereBetween('tanggal_waktu_kejadian', [
                Carbon::parse($tanggalDari)->startOfDay(),
                Carbon::parse($tanggalSampai)->endOfDay()
            ]);
        } elseif ($tanggalDari) {
            $query->whereDate('tanggal_waktu_kejadian', '>=', Carbon::parse($tanggalDari));
        } elseif ($tanggalSampai) {
            $query->whereDate('tanggal_waktu_kejadian', '<=', Carbon::parse($tanggalSampai));
        }

        // Pencarian kata kunci
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('deskripsi', 'like', "%{$search}%")
                  ->orWhereHas('kecamatan', function ($kq) use ($search) {
                      $kq->where('nama_kecamatan', 'like', "%{$search}%");
                  })
                  ->orWhereHas('pelapor', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('kategoriObjek', function ($oq) use ($search) {
                      $oq->where('nama_kategori', 'like', "%{$search}%");
                  });
            });
        }

        // 4. Hitung Rekapitulasi Cerdas Hasil Filter Aktif (untuk kebutuhan laporan resmi dinas)
        $filteredQuery = clone $query;
        $filteredCount         = $filteredQuery->count();
        $filteredKerugian      = (clone $filteredQuery)->sum('taksiran_kerugian');
        $filteredTerselamatkan = (clone $filteredQuery)->sum('taksiran_terselamatkan');
        $filteredPersonel      = (clone $filteredQuery)->sum('jumlah_personel');

        // 5. Paginate hasil laporan
        $laporans = $query->orderBy('status_verifikasi', 'asc')
                          ->latest('tanggal_waktu_kejadian')
                          ->paginate(15)
                          ->withQueryString();

        $kecamatans = Kecamatan::orderByZona()->get();

        return view('admin.verifikasi.index', compact(
            'laporans',
            'countDraft',
            'countVerified',
            'countTotal',
            'filteredCount',
            'filteredKerugian',
            'filteredTerselamatkan',
            'filteredPersonel',
            'status',
            'jenisLayanan',
            'kecamatanId',
            'rentang',
            'tanggalDari',
            'tanggalSampai',
            'search',
            'kecamatans'
        ));
    }

    /**
     * Menampilkan form edit untuk melakukan koreksi data kejadian.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(int $id): View
    {
        $laporan = KejadianKebakaran::with(['kecamatan', 'kategoriObjek', 'kategoriPenyebab', 'pelapor'])
            ->findOrFail($id);

        $kecamatans       = Kecamatan::with('zonaLayanan')->orderByZona()->get();
        $kategoriObjek    = KategoriObjek::orderWithLainLast('nama_kategori')->get();
        $kategoriPenyebab = KategoriPenyebab::orderWithLainLast('nama_penyebab')->get();

        return view('admin.verifikasi.edit', compact('laporan', 'kecamatans', 'kategoriObjek', 'kategoriPenyebab'));
    }

    /**
     * Memperbarui data kejadian di database setelah diperbaiki oleh admin.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $laporan = KejadianKebakaran::findOrFail($id);

        $validated = $request->validate([
            'jenis_layanan'          => ['required', 'in:darurat,non_darurat'],
            'tanggal_waktu_kejadian' => ['required', 'date'],
            'kecamatan_id'           => ['required', 'exists:kecamatan,id'],
            'kategori_objek_id'      => ['required', 'exists:kategori_objek,id'],
            'kategori_penyebab_id'   => ['nullable', 'exists:kategori_penyebab,id'],
            'latitude'               => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'              => ['nullable', 'numeric', 'between:-180,180'],
            'jumlah_personel'        => ['nullable', 'integer', 'min:0'],
            'taksiran_kerugian'      => ['nullable', 'numeric', 'min:0'],
            'taksiran_terselamatkan' => ['nullable', 'numeric', 'min:0'],
            'korban_meninggal'       => ['nullable', 'integer', 'min:0'],
            'korban_luka_berat'      => ['nullable', 'integer', 'min:0'],
            'korban_luka_ringan'     => ['nullable', 'integer', 'min:0'],
            'kk_terdampak'           => ['nullable', 'integer', 'min:0'],
            'jiwa_terdampak'         => ['nullable', 'integer', 'min:0'],
            'status_operasi'         => ['required', 'in:dalam_penanganan,selesai'],
            'tanggal_waktu_selesai'  => ['nullable', 'required_if:status_operasi,selesai', 'date'],
            'deskripsi'              => ['nullable', 'string', 'max:2000'],
            'status_verifikasi'      => ['required', 'in:draft,verified'],
        ]);

        $diverifikasiOleh = $laporan->diverifikasi_oleh;
        $diverifikasiPada = $laporan->diverifikasi_pada;

        if ($validated['status_verifikasi'] === 'verified' && $laporan->status_verifikasi !== 'verified') {
            $diverifikasiOleh = auth()->id();
            $diverifikasiPada = now();
        }

        $statusOperasi = $validated['status_operasi'] ?? 'dalam_penanganan';
        $waktuSelesai = $statusOperasi === 'selesai' ? ($validated['tanggal_waktu_selesai'] ?? null) : null;

        // Update kolom yang ada di $fillable via mass assignment
        $laporan->update([
            'jenis_layanan'          => $validated['jenis_layanan'],
            'tanggal_waktu_kejadian' => $validated['tanggal_waktu_kejadian'],
            'kecamatan_id'           => $validated['kecamatan_id'],
            'kategori_objek_id'      => $validated['kategori_objek_id'],
            'kategori_penyebab_id'   => $validated['jenis_layanan'] === 'non_darurat' ? null : ($validated['kategori_penyebab_id'] ?? null),
            'latitude'               => $validated['latitude'] ?? null,
            'longitude'              => $validated['longitude'] ?? null,
            'jumlah_personel'        => $validated['jumlah_personel'] ?? 0,
            'taksiran_kerugian'      => $validated['taksiran_kerugian'] ?? 0,
            'taksiran_terselamatkan' => $validated['taksiran_terselamatkan'] ?? 0,
            'korban_meninggal'       => $validated['korban_meninggal'] ?? 0,
            'korban_luka_berat'      => $validated['korban_luka_berat'] ?? 0,
            'korban_luka_ringan'     => $validated['korban_luka_ringan'] ?? 0,
            'kk_terdampak'           => $validated['kk_terdampak'] ?? 0,
            'jiwa_terdampak'         => $validated['jiwa_terdampak'] ?? 0,
            'status_operasi'         => $statusOperasi,
            'tanggal_waktu_selesai'  => $waktuSelesai,
            'deskripsi'              => $validated['deskripsi'] ?? null,
        ]);

        // Kolom verifikasi di-set langsung (tidak ada di $fillable) — admin only
        $laporan->status_verifikasi = $validated['status_verifikasi'];
        $laporan->diverifikasi_oleh = $diverifikasiOleh;
        $laporan->diverifikasi_pada = $diverifikasiPada;
        $laporan->save();

        return redirect()->route('admin.verifikasi.index')
            ->with('success', "Laporan ID #{$laporan->id} berhasil diperbarui.");
    }

    /**
     * Memverifikasi (Approve) laporan draft sehingga sah dan masuk ke statistik publik.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(int $id): RedirectResponse
    {
        $laporan = KejadianKebakaran::findOrFail($id);

        $laporan->status_verifikasi = 'verified';
        $laporan->diverifikasi_oleh = auth()->id();
        $laporan->diverifikasi_pada = now();
        $laporan->save();

        return redirect()->back()
            ->with('success', "Laporan ID #{$laporan->id} berhasil diverifikasi (Approved) dan kini aktif pada dashboard publik.");
    }

    /**
     * Menghapus laporan kejadian yang keliru atau tidak valid.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $laporan = KejadianKebakaran::findOrFail($id);

        // Guard Integritas Data: Mencegah penghapusan jika laporan sudah berstatus verified
        if ($laporan->status_verifikasi === 'verified') {
            return redirect()->back()
                ->with('error', 'Data terverifikasi tidak dapat dihapus. Silakan batalkan verifikasi terlebih dahulu jika ada kesalahan fatal.');
        }

        $idHapus = $laporan->id;
        $laporan->delete();

        return redirect()->back()
            ->with('success', "Laporan ID #{$idHapus} berhasil dihapus dari sistem.");
    }
}
