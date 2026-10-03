@extends('layouts.app')

@section('content')
<div class="container-xxl">

    {{-- Toolbar / Header --}}
    <div class="d-flex flex-wrap flex-stack pb-7">
        <div class="d-flex flex-column justify-content-center my-1">
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                Riwayat & Manajemen Laporan Saya
            </h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted">Petugas Lapangan</li>
                <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
                <li class="breadcrumb-item text-dark">Daftar Input Kejadian</li>
            </ul>
        </div>
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <a href="{{ route('petugas.kejadian.create') }}" class="btn btn-sm btn-primary d-flex align-items-center">
                <i class="ki-duotone ki-plus fs-3 me-1"><span class="path1"></span><span class="path2"></span></i>
                <span>Input Laporan Baru</span>
            </a>
        </div>
    </div>

    {{-- Flash Message Success --}}
    @if(session('success'))
        <div class="alert alert-dismissible bg-light-success border border-success d-flex flex-column flex-sm-row p-5 mb-7">
            <i class="ki-duotone ki-check-circle fs-2hx text-success me-4 mb-5 mb-sm-0"><span class="path1"></span><span class="path2"></span></i>
            <div class="d-flex flex-column pe-0 pe-sm-10">
                <h5 class="mb-1 text-success">Berhasil!</h5>
                <span class="text-success fs-7">{{ session('success') }}</span>
            </div>
            <button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">
                <i class="ki-duotone ki-cross fs-1 text-success"><span class="path1"></span><span class="path2"></span></i>
            </button>
        </div>
    @endif

    {{-- ── BARIS 1: KPI STATISTIK RINGKAS PETUGAS ── --}}
    {{-- Catatan: JANGAN pakai card-xl-stretch. Rule bawaan
         @media (min-width:1200px){ .card.card-xl-stretch{height:calc(100% - var(--bs-gutter-y))} }
         membuat kartu tertinggi lebih pendek dari konten aslinya sehingga teks meluber
         keluar border putus-putus. h-100 = tinggi kolom (flex stretch) tanpa mengurangi gutter. --}}
    <div class="row g-5 g-xl-8 mb-7">
        {{-- Total Draft --}}
        <div class="col-md-4">
            <a href="{{ route('petugas.kejadian.index', ['status' => 'draft']) }}" class="card h-100 bg-light-warning hoverable border border-warning border-dashed">
                <div class="card-body d-flex align-items-center justify-content-between py-5 px-6">
                    <div class="d-flex flex-column flex-grow-1 me-3">
                        <span class="text-warning fw-bold fs-7">Laporan Draft (Menunggu)</span>
                        <span class="text-dark fw-bolder fs-2hx mt-1">{{ number_format($countDraft) }}</span>
                    </div>
                    <div class="symbol symbol-45px symbol-circle flex-shrink-0">
                        <span class="symbol-label bg-white shadow-sm">
                            <i class="ki-duotone ki-time fs-2 text-warning"><span class="path1"></span><span class="path2"></span></i>
                        </span>
                    </div>
                </div>
            </a>
        </div>

        {{-- Total Terverifikasi --}}
        <div class="col-md-4">
            <a href="{{ route('petugas.kejadian.index', ['status' => 'verified']) }}" class="card h-100 bg-light-success hoverable border border-success border-dashed">
                <div class="card-body d-flex align-items-center justify-content-between py-5 px-6">
                    <div class="d-flex flex-column flex-grow-1 me-3">
                        <span class="text-success fw-bold fs-7">Laporan Terverifikasi (Sah)</span>
                        <span class="text-dark fw-bolder fs-2hx mt-1">{{ number_format($countVerified) }}</span>
                    </div>
                    <div class="symbol symbol-45px symbol-circle flex-shrink-0">
                        <span class="symbol-label bg-white shadow-sm">
                            <i class="ki-duotone ki-verify fs-2 text-success"><span class="path1"></span><span class="path2"></span></i>
                        </span>
                    </div>
                </div>
            </a>
        </div>

        {{-- Total Seluruh Input --}}
        <div class="col-md-4">
            <a href="{{ route('petugas.kejadian.index', ['status' => 'all']) }}" class="card h-100 bg-light-primary hoverable border border-primary border-dashed">
                <div class="card-body d-flex align-items-center justify-content-between py-5 px-6">
                    <div class="d-flex flex-column flex-grow-1 me-3">
                        <span class="text-primary fw-bold fs-7">Total Laporan Saya</span>
                        <span class="text-dark fw-bolder fs-2hx mt-1">{{ number_format($countTotal) }}</span>
                    </div>
                    <div class="symbol symbol-45px symbol-circle flex-shrink-0">
                        <span class="symbol-label bg-white shadow-sm">
                            <i class="ki-duotone ki-folder fs-2 text-primary"><span class="path1"></span><span class="path2"></span></i>
                        </span>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- ── BARIS 2: FILTER TOOLBAR & TABEL RIWAYAT ── --}}
    <div class="card card-flush shadow-sm">
        
        {{-- Card Header: Filter Interaktif --}}
        <div class="card-header border-0 pt-6">
            <div class="card-title w-100 mb-0">
                <form action="{{ route('petugas.kejadian.index') }}" method="GET" class="w-100" id="formFilterPetugas">
                    
                    {{-- Baris Filter Atas: Quick Pills + Search --}}
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                        
                        {{-- Quick Range Pills --}}
                        <div class="d-flex align-items-center gap-2">
                            <span class="fs-7 fw-bold text-gray-700 me-1">Waktu:</span>
                            <a href="{{ route('petugas.kejadian.index', array_merge(request()->except('rentang', 'tanggal_dari', 'tanggal_sampai'), ['rentang' => 'all'])) }}" 
                               class="btn btn-sm {{ $rentang === 'all' && !$tanggalDari ? 'btn-primary' : 'btn-light' }} py-2 px-3 fs-8">
                               Semua
                            </a>
                            <a href="{{ route('petugas.kejadian.index', array_merge(request()->except('rentang', 'tanggal_dari', 'tanggal_sampai'), ['rentang' => 'hari_ini'])) }}" 
                               class="btn btn-sm {{ $rentang === 'hari_ini' ? 'btn-primary' : 'btn-light' }} py-2 px-3 fs-8">
                               Hari Ini
                            </a>
                            <a href="{{ route('petugas.kejadian.index', array_merge(request()->except('rentang', 'tanggal_dari', 'tanggal_sampai'), ['rentang' => 'minggu_ini'])) }}" 
                               class="btn btn-sm {{ $rentang === 'minggu_ini' ? 'btn-primary' : 'btn-light' }} py-2 px-3 fs-8">
                               Minggu Ini
                            </a>
                            <a href="{{ route('petugas.kejadian.index', array_merge(request()->except('rentang', 'tanggal_dari', 'tanggal_sampai'), ['rentang' => 'bulan_ini'])) }}" 
                               class="btn btn-sm {{ $rentang === 'bulan_ini' ? 'btn-primary' : 'btn-light' }} py-2 px-3 fs-8">
                               Bulan Ini
                            </a>
                        </div>

                        {{-- Search Box --}}
                        <div class="d-flex align-items-center position-relative">
                            <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4 text-gray-500"><span class="path1"></span><span class="path2"></span></i>
                            <input type="text" name="search" class="form-control form-control-solid form-control-sm w-225px ps-12" placeholder="Cari objek / deskripsi..." value="{{ $search }}" />
                        </div>

                    </div>

                    {{-- Baris Filter Bawah: Rentang Tanggal Custom & Dropdowns --}}
                    <div class="d-flex flex-wrap align-items-center gap-3 pt-3 border-top border-gray-200">
                        
                        {{-- Status Select --}}
                        <div class="d-flex align-items-center gap-2">
                            <span class="fs-8 text-muted fw-semibold">Status:</span>
                            <select name="status" class="form-select form-select-solid form-select-sm w-150px" onchange="document.getElementById('formFilterPetugas').submit();">
                                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua Status</option>
                                <option value="draft" {{ $status === 'draft' ? 'selected' : '' }}>Draft (Menunggu)</option>
                                <option value="verified" {{ $status === 'verified' ? 'selected' : '' }}>Terverifikasi</option>
                            </select>
                        </div>

                        {{-- Jenis Layanan Select --}}
                        <div class="d-flex align-items-center gap-2">
                            <span class="fs-8 text-muted fw-semibold">Layanan:</span>
                            <select name="jenis_layanan" class="form-select form-select-solid form-select-sm w-150px" onchange="document.getElementById('formFilterPetugas').submit();">
                                <option value="">Semua Layanan</option>
                                <option value="darurat" {{ $jenisLayanan === 'darurat' ? 'selected' : '' }}>Kebakaran</option>
                                <option value="non_darurat" {{ $jenisLayanan === 'non_darurat' ? 'selected' : '' }}>Rescue</option>
                            </select>
                        </div>

                        {{-- Rentang Tanggal Custom --}}
                        <div class="d-flex align-items-center gap-2">
                            <span class="fs-8 text-muted fw-semibold">Dari:</span>
                            <input type="date" name="tanggal_dari" class="form-control form-control-solid form-control-sm w-140px" value="{{ $tanggalDari }}" />
                            <span class="fs-8 text-muted fw-semibold">Sampai:</span>
                            <input type="date" name="tanggal_sampai" class="form-control form-control-solid form-control-sm w-140px" value="{{ $tanggalSampai }}" />
                        </div>

                        {{-- Tombol Terapkan & Reset --}}
                        <button type="submit" class="btn btn-sm btn-primary py-2 px-4 d-flex align-items-center">
                            <i class="ki-duotone ki-filter fs-4 me-1"><span class="path1"></span><span class="path2"></span></i>
                            <span>Terapkan</span>
                        </button>

                        @if($search || $status !== 'all' || $rentang !== 'all' || $tanggalDari || $tanggalSampai || $jenisLayanan)
                            <a href="{{ route('petugas.kejadian.index') }}" class="btn btn-sm btn-light py-2 px-3 d-flex align-items-center text-muted">
                                <i class="ki-duotone ki-cross fs-4 me-1"><span class="path1"></span><span class="path2"></span></i>
                                <span>Reset Filter</span>
                            </a>
                        @endif

                    </div>

                </form>
            </div>
        </div>

        {{-- Card Body: Tabel Riwayat --}}
        <div class="card-body pt-4">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-4">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th class="w-10px pe-2">#ID</th>
                            <th class="min-w-140px">Waktu & Layanan</th>
                            <th class="min-w-130px">Wilayah Kecamatan</th>
                            <th class="min-w-160px">Objek / Penyelamatan</th>
                            <th class="min-w-130px">Dugaan Penyebab</th>
                            <th class="min-w-110px text-end">Taksiran Kerugian</th>
                            <th class="min-w-110px text-center">Status Laporan</th>
                            <th class="text-end min-w-100px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                        @forelse ($laporans as $item)
                            <tr>
                                <td>
                                    <span class="text-gray-700 fw-bold">#{{ $item->id }}</span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="text-dark fw-bold fs-7">{{ $item->tanggal_waktu_kejadian->format('d M Y, H:i') }}</span>
                                        @if($item->jenis_layanan === 'darurat')
                                            <span class="badge badge-light-danger w-90px mt-1"><i class="las la-fire fs-8 me-1 text-danger"></i> Kebakaran</span>
                                        @else
                                            <span class="badge badge-light-primary w-90px mt-1"><i class="ki-duotone ki-rescue fs-8 me-1 text-primary"></i> Rescue</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="text-dark fw-bold">Kec. {{ $item->kecamatan->nama_kecamatan ?? '-' }}</span>
                                    <div class="text-muted fs-8">{{ $item->kecamatan->zonaLayanan->nama_pos ?? 'Pos Damkar' }}</div>
                                </td>
                                <td>
                                    <span class="text-gray-800 fw-semibold">{{ $item->kategoriObjek->nama_kategori ?? '-' }}</span>
                                    @if($item->kategoriObjek?->is_karhutla)
                                        <span class="badge badge-light-warning fs-9 ms-1">Karhutla</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-gray-700">{{ $item->kategoriPenyebab->nama_penyebab ?? ($item->jenis_layanan === 'non_darurat' ? 'Penyelamatan/Rescue' : 'Belum Ditentukan') }}</span>
                                </td>
                                <td class="text-end">
                                    <span class="text-dark fw-bold">Rp {{ number_format($item->taksiran_kerugian, 0, ',', '.') }}</span>
                                </td>
                                <td class="text-center">
                                    @if ($item->status_verifikasi === 'verified')
                                        <span class="badge badge-light-success fw-bold px-3 py-2">
                                            <i class="ki-duotone ki-verify fs-7 me-1 text-success"><span class="path1"></span><span class="path2"></span></i>
                                            Terverifikasi
                                        </span>
                                    @else
                                        <span class="badge badge-light-warning fw-bold px-3 py-2">
                                            <i class="ki-duotone ki-time fs-7 me-1 text-warning"><span class="path1"></span><span class="path2"></span></i>
                                            Draft (Menunggu)
                                        </span>
                                    @endif
                                </td>

                                {{-- Kolom Aksi: hanya Detail (read-only).
                                     Petugas tidak diberi akses Edit/Hapus karena route petugas.kejadian
                                     hanya menyediakan index, create, dan store. Koreksi data sepenuhnya
                                     menjadi kewenangan Admin verifikator. --}}
                                <td class="text-end">
                                    <div class="d-flex justify-content-end">
                                        <button type="button" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm w-35px h-35px shadow-xs"
                                                title="Lihat Detail Laporan" data-bs-toggle="modal" data-bs-target="#modalDetailPetugas{{ $item->id }}">
                                            <i class="ki-duotone ki-eye fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                        </button>
                                    </div>

                                    {{-- Modal Detail Laporan (Read-Only untuk Petugas) --}}
                                    <div class="modal fade" id="modalDetailPetugas{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered mw-650px">
                                            <div class="modal-content text-start shadow-lg">
                                                <div class="modal-header">
                                                    <h3 class="fw-bold d-flex align-items-center m-0">
                                                        <i class="ki-duotone ki-document fs-2 text-primary me-2"><span class="path1"></span><span class="path2"></span></i>
                                                        Detail Laporan #{{ $item->id }}
                                                    </h3>
                                                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
                                                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                                                    </div>
                                                </div>
                                                <div class="modal-body py-5 px-lg-8">
                                                    <div class="row g-4 mb-4">
                                                        <div class="col-6">
                                                            <div class="text-muted fs-7">Jenis Layanan</div>
                                                            <div class="fw-bold fs-6 text-dark">{{ $item->jenis_layanan === 'darurat' ? 'Kebakaran (Darurat)' : 'Penyelamatan / Rescue (Non-Darurat)' }}</div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="text-muted fs-7">Waktu Kejadian</div>
                                                            <div class="fw-bold fs-6 text-dark">{{ $item->tanggal_waktu_kejadian->format('d F Y, H:i') }} WIB</div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="text-muted fs-7">Kecamatan & Pos Wilayah</div>
                                                            <div class="fw-bold fs-6 text-dark">Kec. {{ $item->kecamatan->nama_kecamatan ?? '-' }} ({{ $item->kecamatan->zonaLayanan->nama_pos ?? '-' }})</div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="text-muted fs-7">Kategori Objek / Target</div>
                                                            <div class="fw-bold fs-6 text-dark">{{ $item->kategoriObjek->nama_kategori ?? '-' }}</div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="text-muted fs-7">Dugaan Penyebab</div>
                                                            <div class="fw-bold fs-6 text-dark">{{ $item->kategoriPenyebab->nama_penyebab ?? ($item->jenis_layanan === 'non_darurat' ? 'Penyelamatan/Rescue' : 'Belum Ditentukan') }}</div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="text-muted fs-7">Personel Diterjunkan</div>
                                                            <div class="fw-bold fs-6 text-dark">{{ $item->jumlah_personel ?? 0 }} Orang</div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="text-muted fs-7">Taksiran Kerugian</div>
                                                            <div class="fw-bold fs-6 text-danger">Rp {{ number_format($item->taksiran_kerugian ?? 0, 0, ',', '.') }}</div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="text-muted fs-7">Taksiran Nilai Terselamatkan</div>
                                                            <div class="fw-bold fs-6 text-success">Rp {{ number_format($item->taksiran_terselamatkan ?? 0, 0, ',', '.') }}</div>
                                                        </div>
                                                    </div>

                                                    <div class="separator separator-dashed my-4"></div>

                                                    <div class="mb-4">
                                                        <div class="text-muted fs-7 mb-1 fw-bold">Kronologi / Catatan Lapangan:</div>
                                                        <div class="p-3 bg-light rounded text-gray-700 fs-7">
                                                            {{ $item->deskripsi ?: 'Tidak ada catatan khusus yang dilampirkan.' }}
                                                        </div>
                                                    </div>

                                                    <div class="row g-4 pt-2 border-top border-gray-200">
                                                        <div class="col-6">
                                                            <div class="text-muted fs-8">Dilaporkan Pada:</div>
                                                            <div class="fw-semibold fs-7 text-dark">{{ $item->created_at->format('d/m/Y H:i') }}</div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="text-muted fs-8">Status & Verifikator:</div>
                                                            <div class="fw-semibold fs-7 text-dark">
                                                                {{ $item->verifikator->name ?? 'Menunggu Verifikasi Admin' }}
                                                                @if($item->diverifikasi_pada)
                                                                    ({{ $item->diverifikasi_pada->format('d/m/Y H:i') }})
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>

                                                    @if($item->status_verifikasi === 'draft')
                                                        <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-4 mt-5">
                                                            <i class="ki-duotone ki-information fs-2tx text-warning me-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                                            <div class="fw-semibold fs-7 text-gray-700">
                                                                Laporan ini masih berstatus <strong>Draft</strong>. Perbaikan atau pembatalan data hanya dapat dilakukan oleh Admin verifikator.
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Tutup</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-10 text-muted">
                                    <i class="ki-duotone ki-information fs-3x text-muted mb-2 d-block"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                    Tidak ada laporan ditemukan dengan kriteria filter yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ── CARD FOOTER PAGINATION (TERSTRUKTUR RAPI) ── --}}
            <div class="card-footer d-flex flex-stack flex-wrap py-4 px-0 border-top border-gray-200">
                <div class="fs-7 fw-semibold text-gray-700">
                    Menampilkan <span class="fw-bolder text-dark">{{ $laporans->firstItem() ?? 0 }}</span> sampai <span class="fw-bolder text-dark">{{ $laporans->lastItem() ?? 0 }}</span> dari total <span class="fw-bolder text-dark">{{ $laporans->total() }}</span> laporan
                </div>
                <div>
                    {{ $laporans->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
