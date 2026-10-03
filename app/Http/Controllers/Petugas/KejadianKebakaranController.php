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
            'deskripsi'              => ['nullable', 'string', 'max:2000'],
        ], [
            'jenis_layanan.required'          => 'Jenis layanan wajib dipilih.',
            'tanggal_waktu_kejadian.required' => 'Tanggal dan waktu kejadian wajib diisi.',
            'kecamatan_id.required'           => 'Kecamatan wajib dipilih.',
            'kategori_objek_id.required'      => 'Kategori objek wajib dipilih.',
        ]);

        KejadianKebakaran::create([
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
            'deskripsi'              => $validated['deskripsi'] ?? null,
            'status_verifikasi'      => 'draft',
            'dilaporkan_oleh'        => auth()->id(),
            'diverifikasi_oleh'      => null,
            'diverifikasi_pada'      => null,
        ]);

        return redirect()->route('petugas.kejadian.index')
            ->with('success', 'Laporan kejadian berhasil disimpan sebagai Draft dan menunggu verifikasi Admin.');
    }
}
