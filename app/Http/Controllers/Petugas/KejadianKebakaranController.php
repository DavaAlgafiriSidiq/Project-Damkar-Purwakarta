<?php

namespace App\Http\Controllers\Petugas;

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
 * Controller: KejadianKebakaranController (Role: Petugas Lapangan)
 * 
 * Bertanggung jawab menangani pembuatan laporan kejadian baru dan
 * menampilkan riwayat laporan dengan filter komprehensif.
 */
class KejadianKebakaranController extends Controller
{
    /**
     * Menampilkan riwayat laporan kejadian milik petugas yang sedang login
     * dilengkapi filter status, rentang tanggal (Hari Ini, Minggu Ini, Bulan Ini, Custom),
     * jenis layanan, dan pencarian kata kunci.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $userId = auth()->id();

        // Parameter filter
        $status       = $request->get('status', 'all');
        $rentang      = $request->get('rentang', 'all');
        $tanggalDari  = $request->get('tanggal_dari');
        $tanggalSampai= $request->get('tanggal_sampai');
        $jenisLayanan = $request->get('jenis_layanan');
        $search       = $request->get('search');

        // Query dasar: hanya laporan milik petugas yang login
        $query = KejadianKebakaran::with(['kecamatan.zonaLayanan', 'kategoriObjek', 'kategoriPenyebab'])
            ->where('dilaporkan_oleh', $userId);

        // Filter status verifikasi
        if ($status !== 'all' && in_array($status, ['draft', 'verified'])) {
            $query->where('status_verifikasi', $status);
        }

        // Filter jenis layanan
        if ($jenisLayanan && in_array($jenisLayanan, ['darurat', 'non_darurat'])) {
            $query->where('jenis_layanan', $jenisLayanan);
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
                  ->orWhereHas('kategoriObjek', function ($oq) use ($search) {
                      $oq->where('nama_kategori', 'like', "%{$search}%");
                  });
            });
        }

        // Hitung statistik untuk KPI card petugas
        $countDraft    = KejadianKebakaran::where('dilaporkan_oleh', $userId)->where('status_verifikasi', 'draft')->count();
        $countVerified = KejadianKebakaran::where('dilaporkan_oleh', $userId)->where('status_verifikasi', 'verified')->count();
        $countTotal    = KejadianKebakaran::where('dilaporkan_oleh', $userId)->count();

        // Paginate hasil
        $laporans = $query->latest('tanggal_waktu_kejadian')
            ->paginate(15)
            ->withQueryString();

        return view('petugas.index', compact(
            'laporans',
            'countDraft',
            'countVerified',
            'countTotal',
            'status',
            'rentang',
            'tanggalDari',
            'tanggalSampai',
            'jenisLayanan',
            'search'
        ));
    }

    /**
     * Menampilkan form pembuatan laporan kejadian baru.
     * Mengelompokkan kategori objek untuk kemudahan dinamisasi di antarmuka.
     *
     * @return \Illuminate\View\View
     */
    public function create(): View
    {
        $kecamatans       = Kecamatan::with('zonaLayanan')->orderBy('nama_kecamatan')->get();
        $kategoriObjek    = KategoriObjek::orderBy('nama_kategori')->get();
        $kategoriPenyebab = KategoriPenyebab::orderBy('nama_penyebab')->get();

        return view('petugas.create', compact('kecamatans', 'kategoriObjek', 'kategoriPenyebab'));
    }

    /**
     * Menyimpan data laporan kejadian baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_pelapor'           => ['required', 'string', 'max:100'],
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
        ], [
            'jenis_layanan.required'          => 'Jenis layanan wajib dipilih.',
            'tanggal_waktu_kejadian.required' => 'Tanggal dan waktu kejadian wajib diisi.',
            'kecamatan_id.required'           => 'Kecamatan wajib dipilih.',
            'kategori_objek_id.required'      => 'Kategori objek wajib dipilih.',
            'status_operasi.required'         => 'Status operasi wajib dipilih.',
            'tanggal_waktu_selesai.required_if' => 'Waktu penanganan selesai wajib diisi saat status operasi Selesai.',
            'korban_meninggal.integer'        => 'Jumlah korban meninggal harus berupa angka.',
            'korban_luka_berat.integer'       => 'Jumlah korban luka berat harus berupa angka.',
            'korban_luka_ringan.integer'      => 'Jumlah korban luka ringan harus berupa angka.',
            'kk_terdampak.integer'            => 'Jumlah KK terdampak harus berupa angka.',
            'jiwa_terdampak.integer'          => 'Jumlah jiwa terdampak harus berupa angka.',
        ]);

        $statusOperasi = $validated['status_operasi'] ?? 'dalam_penanganan';
        $waktuSelesai = $statusOperasi === 'selesai' ? ($validated['tanggal_waktu_selesai'] ?? null) : null;

        // Bangun instance baru — kolom yang TIDAK ada di $fillable di-set langsung
        // ke properti model (bukan via mass assignment) untuk memastikan server
        // memaksa nilai yang benar secara hardcode.
        $laporan = new KejadianKebakaran();
        $laporan->fill([
            'nama_pelapor'           => $validated['nama_pelapor'],
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
            'deskripsi'              => $validated['deskripsi'] ?? null,
            'status_operasi'         => $statusOperasi,
            'tanggal_waktu_selesai'  => $waktuSelesai,
            'dilaporkan_oleh'        => auth()->id(),
        ]);
        // Server memaksa nilai ini — tidak boleh datang dari request
        $laporan->status_verifikasi = 'draft';
        $laporan->diverifikasi_oleh = null;
        $laporan->diverifikasi_pada = null;
        $laporan->save();

        return redirect()->route('petugas.kejadian.index')
            ->with('success', 'Laporan kejadian berhasil disimpan sebagai Draft dan menunggu verifikasi Admin.');
    }

    /**
     * Menampilkan form edit laporan kejadian (jika masih draft).
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit(int $id): View
    {
        $laporan = KejadianKebakaran::where('dilaporkan_oleh', auth()->id())
            ->findOrFail($id);

        // Guard: petugas tidak boleh mengedit laporan yang sudah diverifikasi
        if ($laporan->status_verifikasi !== 'draft') {
            abort(403, 'Akses ditolak: Laporan sudah diverifikasi.');
        }

        $kecamatans       = Kecamatan::with('zonaLayanan')->orderBy('nama_kecamatan')->get();
        $kategoriObjek    = KategoriObjek::orderBy('nama_kategori')->get();
        $kategoriPenyebab = KategoriPenyebab::orderBy('nama_penyebab')->get();

        return view('petugas.edit', compact('laporan', 'kecamatans', 'kategoriObjek', 'kategoriPenyebab'));
    }

    /**
     * Memperbarui laporan kejadian oleh Petugas pelapor.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $laporan = KejadianKebakaran::where('dilaporkan_oleh', auth()->id())
            ->findOrFail($id);

        // Guard: petugas tidak boleh mengubah laporan yang sudah diverifikasi
        if ($laporan->status_verifikasi !== 'draft') {
            abort(403, 'Akses ditolak: Laporan sudah diverifikasi.');
        }

        $validated = $request->validate([
            'nama_pelapor'           => ['required', 'string', 'max:100'],
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
        ], [
            'jenis_layanan.required'          => 'Jenis layanan wajib dipilih.',
            'tanggal_waktu_kejadian.required' => 'Tanggal dan waktu kejadian wajib diisi.',
            'kecamatan_id.required'           => 'Kecamatan wajib dipilih.',
            'kategori_objek_id.required'      => 'Kategori objek wajib dipilih.',
            'status_operasi.required'         => 'Status operasi wajib dipilih.',
            'tanggal_waktu_selesai.required_if' => 'Waktu penanganan selesai wajib diisi saat status operasi Selesai.',
            'nama_pelapor.required'           => 'Nama pelapor / Danru wajib diisi.',
            'korban_meninggal.integer'        => 'Jumlah korban meninggal harus berupa angka.',
            'korban_luka_berat.integer'       => 'Jumlah korban luka berat harus berupa angka.',
            'korban_luka_ringan.integer'      => 'Jumlah korban luka ringan harus berupa angka.',
            'kk_terdampak.integer'            => 'Jumlah KK terdampak harus berupa angka.',
            'jiwa_terdampak.integer'          => 'Jumlah jiwa terdampak harus berupa angka.',
        ]);

        $statusOperasi = $validated['status_operasi'] ?? 'dalam_penanganan';
        $waktuSelesai = $statusOperasi === 'selesai' ? ($validated['tanggal_waktu_selesai'] ?? null) : null;

        $laporan->update([
            'nama_pelapor'           => $validated['nama_pelapor'],
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

        return redirect()->route('petugas.kejadian.index')
            ->with('success', "Laporan ID #{$laporan->id} berhasil diperbarui.");
    }

    /**
     * Menghapus laporan kejadian milik petugas.
     * Hanya laporan berstatus 'draft' yang boleh dihapus.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $laporan = KejadianKebakaran::where('dilaporkan_oleh', auth()->id())
            ->findOrFail($id);

        // Guard: petugas tidak boleh menghapus laporan yang sudah diverifikasi
        if ($laporan->status_verifikasi !== 'draft') {
            abort(403, 'Akses ditolak: Laporan sudah diverifikasi.');
        }

        $idHapus = $laporan->id;
        $laporan->delete();

        return redirect()->route('petugas.kejadian.index')
            ->with('success', "Laporan ID #{$idHapus} berhasil dihapus.");
    }
}
