@extends('layouts.app')

@section('content')
<div class="container-xxl">

    {{-- Header & Title --}}
    <div class="d-flex flex-wrap flex-stack pb-7">
        <div class="d-flex flex-column justify-content-center my-1">
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                Verifikasi & Manajemen Data Laporan
            </h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted">Administrator Damkar</li>
                <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
                <li class="breadcrumb-item text-dark">Antrean Validasi & Rekapitulasi</li>
            </ul>
        </div>
    </div>

    {{-- Flash Message Success --}}
    @if(session('success'))
        <div class="alert alert-dismissible bg-light-success border border-success d-flex flex-column flex-sm-row p-5 mb-7 shadow-xs">
            <i class="ki-duotone ki-check-circle fs-2hx text-success me-4 mb-5 mb-sm-0"><span class="path1"></span><span class="path2"></span></i>
            <div class="d-flex flex-column pe-0 pe-sm-10">
                <h5 class="mb-1 text-success">Operasi Berhasil!</h5>
                <span class="text-success fs-7">{{ session('success') }}</span>
            </div>
            <button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">
                <i class="ki-duotone ki-cross fs-1 text-success"><span class="path1"></span><span class="path2"></span></i>
            </button>
        </div>
    @endif

    {{-- Flash Message Error --}}
    @if(session('error'))
        <div class="alert alert-dismissible bg-light-danger border border-danger d-flex flex-column flex-sm-row p-5 mb-7 shadow-xs">
            <i class="ki-duotone ki-information-5 fs-2hx text-danger me-4 mb-5 mb-sm-0"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
            <div class="d-flex flex-column pe-0 pe-sm-10">
                <h5 class="mb-1 text-danger">Operasi Ditolak!</h5>
                <span class="text-danger fs-7">{{ session('error') }}</span>
            </div>
            <button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">
                <i class="ki-duotone ki-cross fs-1 text-danger"><span class="path1"></span><span class="path2"></span></i>
            </button>
        </div>
    @endif

    {{-- ── BARIS 1: KPI STATISTIK GLOBAL ── --}}
    {{-- Catatan: JANGAN pakai card-xl-stretch. Rule bawaan
         @media (min-width:1200px){ .card.card-xl-stretch{height:calc(100% - var(--bs-gutter-y))} }
         membuat kartu tertinggi lebih pendek dari konten aslinya sehingga teks deskripsi
         meluber keluar border putus-putus. h-100 = tinggi kolom (flex stretch) tanpa mengurangi gutter. --}}
    <div class="row g-5 g-xl-8 mb-7">
        {{-- Antrean Draft --}}
        <div class="col-xl-4">
            <a href="{{ route('admin.verifikasi.index', ['status' => 'draft']) }}" class="card h-100 bg-light-warning hoverable border border-warning border-dashed shadow-xs">
                <div class="card-body d-flex align-items-center justify-content-between py-5 px-6">
                    <div class="d-flex flex-column flex-grow-1 me-3">
                        <span class="text-warning fw-bold fs-7">Menunggu Verifikasi (Draft)</span>
                        <span class="text-dark fw-bolder fs-2hx mt-1">{{ number_format($countDraft) }}</span>
                        <span class="text-muted fs-8 mt-1">Laporan perlu validasi segera</span>
                    </div>
                    <div class="symbol symbol-50px symbol-circle flex-shrink-0">
                        <span class="symbol-label bg-white shadow-xs">
                            <i class="ki-duotone ki-time fs-2x text-warning"><span class="path1"></span><span class="path2"></span></i>
                        </span>
                    </div>
                </div>
            </a>
        </div>

        {{-- Terverifikasi --}}
        <div class="col-xl-4">
            <a href="{{ route('admin.verifikasi.index', ['status' => 'verified']) }}" class="card h-100 bg-light-success hoverable border border-success border-dashed shadow-xs">
                <div class="card-body d-flex align-items-center justify-content-between py-5 px-6">
                    <div class="d-flex flex-column flex-grow-1 me-3">
                        <span class="text-success fw-bold fs-7">Sudah Terverifikasi (Sah)</span>
                        <span class="text-dark fw-bolder fs-2hx mt-1">{{ number_format($countVerified) }}</span>
                        <span class="text-muted fs-8 mt-1">Aktif pada dashboard publik</span>
                    </div>
                    <div class="symbol symbol-50px symbol-circle flex-shrink-0">
                        <span class="symbol-label bg-white shadow-xs">
                            <i class="ki-duotone ki-verify fs-2x text-success"><span class="path1"></span><span class="path2"></span></i>
                        </span>
                    </div>
                </div>
            </a>
        </div>

        {{-- Total Seluruh Data --}}
        <div class="col-xl-4">
            <a href="{{ route('admin.verifikasi.index', ['status' => 'all']) }}" class="card h-100 bg-light-primary hoverable border border-primary border-dashed shadow-xs">
                <div class="card-body d-flex align-items-center justify-content-between py-5 px-6">
                    <div class="d-flex flex-column flex-grow-1 me-3">
                        <span class="text-primary fw-bold fs-7">Total Seluruh Database</span>
                        <span class="text-dark fw-bolder fs-2hx mt-1">{{ number_format($countTotal) }}</span>
                        <span class="text-muted fs-8 mt-1">Keseluruhan entri kejadian</span>
                    </div>
                    <div class="symbol symbol-50px symbol-circle flex-shrink-0">
                        <span class="symbol-label bg-white shadow-xs">
                            <i class="ki-duotone ki-folder fs-2x text-primary"><span class="path1"></span><span class="path2"></span></i>
                        </span>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- ── BARIS 2: BANNER REKAPITULASI HASIL FILTER AKTIF (EKSPLISIT) ── --}}
    <div class="card bg-light-info border border-info border-dashed mb-7 shadow-xs">
        <div class="card-body py-5 px-6">
            
            {{-- Header Info Parameter Filter Aktif --}}
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pb-3 mb-3 border-bottom border-info border-opacity-20">
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <i class="ki-duotone ki-filter-search fs-2 text-info me-1"><span class="path1"></span><span class="path2"></span></i>
                    <span class="fw-bold fs-6 text-dark me-2">Parameter Filter Aktif:</span>
                    
                    {{-- Badge Status --}}
                    <span class="badge badge-sm {{ $status === 'draft' ? 'badge-warning' : ($status === 'verified' ? 'badge-success' : 'badge-secondary') }} fw-semibold">
                        Status: {{ $status === 'draft' ? 'Draft (Menunggu)' : ($status === 'verified' ? 'Terverifikasi' : 'Semua Status') }}
                    </span>

                    {{-- Badge Layanan --}}
                    <span class="badge badge-sm badge-light-primary fw-semibold">
                        Layanan: {{ $jenisLayanan === 'darurat' ? 'Kebakaran' : ($jenisLayanan === 'non_darurat' ? 'Rescue' : 'Semua Layanan') }}
                    </span>

                    {{-- Badge Kecamatan --}}
                    @php
                        $selectedKec = $kecamatans->firstWhere('id', $kecamatanId);
                    @endphp
                    <span class="badge badge-sm badge-light-dark fw-semibold">
                        Wilayah: {{ $selectedKec ? 'Kec. ' . $selectedKec->nama_kecamatan : 'Semua Kecamatan' }}
                    </span>

                    {{-- Badge Waktu --}}
                    <span class="badge badge-sm badge-light-info fw-semibold">
                        Waktu: 
                        @if($rentang === 'hari_ini') Hari Ini
                        @elseif($rentang === 'minggu_ini') Minggu Ini
                        @elseif($rentang === 'bulan_ini') Bulan Ini
                        @elseif($tanggalDari && $tanggalSampai) {{ \Carbon\Carbon::parse($tanggalDari)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($tanggalSampai)->format('d/m/Y') }}
                        @elseif($tanggalDari) Sejak {{ \Carbon\Carbon::parse($tanggalDari)->format('d/m/Y') }}
                        @elseif($tanggalSampai) Hingga {{ \Carbon\Carbon::parse($tanggalSampai)->format('d/m/Y') }}
                        @else Seluruh Periode
                        @endif
                    </span>

                    {{-- Badge Search --}}
                    @if($search)
                        <span class="badge badge-sm badge-light-danger fw-semibold">
                            Cari: "{{ $search }}"
                        </span>
                    @endif
                </div>

                <div class="text-muted fs-8 fst-italic">
                    *Kalkulasi angka di bawah otomatis menyesuaikan parameter aktif di atas
                </div>
            </div>

            {{-- 4 Nilai Rekapitulasi Kuantitatif --}}
            <div class="row g-4 text-center text-sm-start">
                <div class="col-6 col-md-3">
                    <span class="text-muted fs-8 d-block fw-semibold">Jumlah Laporan Cocok:</span>
                    <span class="fw-bolder fs-4 text-dark">{{ number_format($filteredCount) }}</span>
                    <span class="text-muted fs-8 ms-1">Kejadian</span>
                </div>
                <div class="col-6 col-md-3 border-start-md border-gray-300 ps-md-6">
                    <span class="text-muted fs-8 d-block fw-semibold">Total Taksiran Kerugian:</span>
                    <span class="fw-bolder fs-4 text-danger">Rp {{ number_format($filteredKerugian, 0, ',', '.') }}</span>
                </div>
                <div class="col-6 col-md-3 border-start-md border-gray-300 ps-md-6">
                    <span class="text-muted fs-8 d-block fw-semibold">Total Aset Terselamatkan:</span>
                    <span class="fw-bolder fs-4 text-success">Rp {{ number_format($filteredTerselamatkan, 0, ',', '.') }}</span>
                </div>
                <div class="col-6 col-md-3 border-start-md border-gray-300 ps-md-6">
                    <span class="text-muted fs-8 d-block fw-semibold">Total Personel Lapangan:</span>
                    <span class="fw-bolder fs-4 text-primary">{{ number_format($filteredPersonel) }}</span>
                    <span class="text-muted fs-8 ms-1">Orang</span>
                </div>
            </div>

        </div>
    </div>

    {{-- ── BARIS 3: PANEL FILTER TERDISTRIBUSI & TABEL DATA ── --}}
    <div class="card card-flush shadow-sm">
        
        {{-- Card Header: Filter Interaktif Terdistribusi Merata --}}
        <div class="card-header border-0 pt-6">
            <div class="w-100">
                
                {{-- Baris Atas: Tabs Status & Search Box --}}
                <div class="d-flex flex-wrap align-items-center justify-content-between border-bottom pb-4 mb-4 gap-3">
                    
                    {{-- Status Tab Navs --}}
                    <ul class="nav nav-tabs nav-line-tabs fs-6 border-0 mb-0">
                        <li class="nav-item">
                            <a class="nav-link text-active-primary pb-2 {{ $status === 'draft' ? 'active fw-bolder text-primary' : 'text-gray-600' }}" 
                               href="{{ route('admin.verifikasi.index', array_merge(request()->except('status'), ['status' => 'draft'])) }}">
                                <i class="ki-duotone ki-time fs-5 me-1 text-warning"><span class="path1"></span><span class="path2"></span></i>
                                Perlu Verifikasi
                                @if($countDraft > 0)
                                    <span class="badge badge-light-warning ms-2 fw-bold">{{ $countDraft }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-active-primary pb-2 {{ $status === 'verified' ? 'active fw-bolder text-primary' : 'text-gray-600' }}" 
                               href="{{ route('admin.verifikasi.index', array_merge(request()->except('status'), ['status' => 'verified'])) }}">
                                <i class="ki-duotone ki-verify fs-5 me-1 text-success"><span class="path1"></span><span class="path2"></span></i>
                                Terverifikasi (Aktif)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-active-primary pb-2 {{ $status === 'all' ? 'active fw-bolder text-primary' : 'text-gray-600' }}" 
                               href="{{ route('admin.verifikasi.index', array_merge(request()->except('status'), ['status' => 'all'])) }}">
                                <i class="ki-duotone ki-element-11 fs-5 me-1 text-info"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                Semua Laporan
                            </a>
                        </li>
                    </ul>

                    {{-- Search Form --}}
                    <form action="{{ route('admin.verifikasi.index') }}" method="GET" class="d-flex align-items-center position-relative">
                        <input type="hidden" name="status" value="{{ $status }}" />
                        <input type="hidden" name="jenis_layanan" value="{{ $jenisLayanan }}" />
                        <input type="hidden" name="kecamatan_id" value="{{ $kecamatanId }}" />
                        <input type="hidden" name="rentang" value="{{ $rentang }}" />
                        <input type="hidden" name="tanggal_dari" value="{{ $tanggalDari }}" />
                        <input type="hidden" name="tanggal_sampai" value="{{ $tanggalSampai }}" />
                        
                        <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4 text-gray-500"><span class="path1"></span><span class="path2"></span></i>
                        <input type="text" name="search" class="form-control form-control-solid form-control-sm w-225px ps-12" placeholder="Cari objek / pelapor..." value="{{ $search }}" />
                    </form>

                </div>

                {{-- Baris Tengah: Shortcut Filter Waktu Cepat (Quick Pills) --}}
                <div class="d-flex flex-wrap align-items-center gap-2 mb-4">
                    <span class="fs-7 fw-bold text-gray-700 me-2 d-flex align-items-center">
                        <i class="ki-duotone ki-calendar-tick fs-5 text-primary me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span></i>
                        Filter Waktu Cepat:
                    </span>
                    <a href="{{ route('admin.verifikasi.index', array_merge(request()->except('rentang', 'tanggal_dari', 'tanggal_sampai'), ['rentang' => 'all'])) }}" 
                       class="btn btn-sm {{ $rentang === 'all' && !$tanggalDari ? 'btn-primary' : 'btn-light' }} py-2 px-3 fs-8">
                       Semua Waktu
                    </a>
                    <a href="{{ route('admin.verifikasi.index', array_merge(request()->except('rentang', 'tanggal_dari', 'tanggal_sampai'), ['rentang' => 'hari_ini'])) }}" 
                       class="btn btn-sm {{ $rentang === 'hari_ini' ? 'btn-primary' : 'btn-light' }} py-2 px-3 fs-8">
                       Hari Ini
                    </a>
                    <a href="{{ route('admin.verifikasi.index', array_merge(request()->except('rentang', 'tanggal_dari', 'tanggal_sampai'), ['rentang' => 'minggu_ini'])) }}" 
                       class="btn btn-sm {{ $rentang === 'minggu_ini' ? 'btn-primary' : 'btn-light' }} py-2 px-3 fs-8">
                       Minggu Ini
                    </a>
                    <a href="{{ route('admin.verifikasi.index', array_merge(request()->except('rentang', 'tanggal_dari', 'tanggal_sampai'), ['rentang' => 'bulan_ini'])) }}" 
                       class="btn btn-sm {{ $rentang === 'bulan_ini' ? 'btn-primary' : 'btn-light' }} py-2 px-3 fs-8">
                       Bulan Ini
                    </a>
                </div>

                {{-- Baris Bawah: Form Grid Filter Terdistribusi Merata --}}
                <form action="{{ route('admin.verifikasi.index') }}" method="GET" id="formFilterAdmin" class="bg-light p-4 rounded border border-gray-200">
                    <input type="hidden" name="status" value="{{ $status }}" />
                    <input type="hidden" name="search" value="{{ $search }}" />

                    <div class="row g-3 align-items-end">
                        
                        {{-- Col 1: Jenis Layanan --}}
                        <div class="col-md-3 col-lg-3">
                            <label class="fs-8 fw-bold text-gray-600 mb-1">Jenis Layanan:</label>
                            <select name="jenis_layanan" class="form-select form-select-solid form-select-sm" onchange="document.getElementById('formFilterAdmin').submit();">
                                <option value="">Semua Layanan</option>
                                <option value="darurat" {{ $jenisLayanan === 'darurat' ? 'selected' : '' }}>Kebakaran (Darurat)</option>
                                <option value="non_darurat" {{ $jenisLayanan === 'non_darurat' ? 'selected' : '' }}>Rescue (Non-Darurat)</option>
                            </select>
                        </div>

                        {{-- Col 2: Kecamatan --}}
                        <div class="col-md-3 col-lg-3">
                            <label class="fs-8 fw-bold text-gray-600 mb-1">Kecamatan Lokasi:</label>
                            <select name="kecamatan_id" class="form-select form-select-solid form-select-sm" onchange="document.getElementById('formFilterAdmin').submit();">
                                <option value="">Semua Kecamatan</option>
                                @foreach($kecamatans as $kec)
                                    <option value="{{ $kec->id }}" {{ $kecamatanId == $kec->id ? 'selected' : '' }}>Kec. {{ $kec->nama_kecamatan }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Col 3: Dari Tanggal --}}
                        <div class="col-md-2 col-lg-2">
                            <label class="fs-8 fw-bold text-gray-600 mb-1">Dari Tanggal:</label>
                            <input type="date" name="tanggal_dari" class="form-control form-control-solid form-control-sm" value="{{ $tanggalDari }}" />
                        </div>

                        {{-- Col 4: Sampai Tanggal --}}
                        <div class="col-md-2 col-lg-2">
                            <label class="fs-8 fw-bold text-gray-600 mb-1">Sampai Tanggal:</label>
                            <input type="date" name="tanggal_sampai" class="form-control form-control-solid form-control-sm" value="{{ $tanggalSampai }}" />
                        </div>

                        {{-- Col 5: Tombol Filter & Reset --}}
                        <div class="col-md-2 col-lg-2 d-flex gap-2">
                            <button type="submit" class="btn btn-sm btn-primary w-100 py-2 d-flex align-items-center justify-content-center">
                                <i class="ki-duotone ki-filter fs-4 me-1"><span class="path1"></span><span class="path2"></span></i>
                                <span>Terapkan</span>
                            </button>

                            @if($search || $jenisLayanan || $kecamatanId || $tanggalDari || $tanggalSampai || $rentang !== 'all')
                                <a href="{{ route('admin.verifikasi.index', ['status' => $status]) }}" class="btn btn-sm btn-light py-2 px-3 d-flex align-items-center text-muted" title="Reset Filter">
                                    <i class="ki-duotone ki-cross fs-4"><span class="path1"></span><span class="path2"></span></i>
                                </a>
                            @endif
                        </div>

                    </div>
                </form>

            </div>
        </div>

        {{-- Card Body: Tabel Data --}}
        <div class="card-body pt-3">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-4">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th class="w-10px pe-2">#ID</th>
                            <th class="min-w-140px">Waktu & Layanan</th>
                            <th class="min-w-130px">Lokasi / Wilayah</th>
                            <th class="min-w-160px">Objek & Dugaan Penyebab</th>
                            <th class="min-w-110px">Pelapor</th>
                            <th class="min-w-110px text-end">Kerugian (Rp)</th>
                            <th class="min-w-110px text-center">Status</th>
                            <th class="text-end min-w-160px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                        @forelse($laporans as $item)
                            <tr>
                                <td>
                                    <span class="text-gray-800 fw-bold">#{{ $item->id }}</span>
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
                                    <div class="d-flex flex-column">
                                        <span class="text-gray-800 fw-semibold">{{ $item->kategoriObjek->nama_kategori ?? '-' }}</span>
                                        <span class="text-muted fs-8">{{ $item->kategoriPenyebab->nama_penyebab ?? ($item->jenis_layanan === 'non_darurat' ? 'Penyelamatan/Rescue' : 'Belum Ditentukan') }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="text-gray-800 fw-semibold fs-7">{{ $item->pelapor->name ?? 'Petugas Lapangan' }}</span>
                                        @if($item->nama_pelapor)
                                            <span class="badge badge-light-warning text-warning fw-bold fs-8 mt-1">
                                                <i class="ki-duotone ki-user fs-8 me-1"><span class="path1"></span><span class="path2"></span></i>
                                                {{ $item->nama_pelapor }}
                                            </span>
                                        @else
                                            <span class="text-muted fs-8 fst-italic">— (data lama)</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-end">
                                    <span class="text-dark fw-bold">Rp {{ number_format($item->taksiran_kerugian, 0, ',', '.') }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex flex-column align-items-center gap-1">
                                        {{-- Badge Status Operasi Lapangan --}}
                                        @if ($item->status_operasi === 'dalam_penanganan')
                                            <span class="badge badge-light-warning text-warning fw-bolder px-2 py-1 fs-8 border border-warning border-dashed" title="Operasi masih aktif di lapangan">
                                                🟡 Penanganan
                                            </span>
                                        @else
                                            <span class="badge badge-light-success text-success fw-bold px-2 py-1 fs-8" title="Operasi telah selesai">
                                                🟢 Selesai
                                            </span>
                                        @endif

                                        {{-- Status Verifikasi Dokumen --}}
                                        @if ($item->status_verifikasi === 'verified')
                                            <span class="badge badge-light-success fw-semibold px-2 py-1 fs-9">
                                                Terverifikasi
                                            </span>
                                        @else
                                            <span class="badge badge-light-secondary text-muted fw-semibold px-2 py-1 fs-9">
                                                Draft
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        
                                        {{-- 1. Tombol Detail (Modal) --}}
                                        <button type="button" class="btn btn-icon btn-light-info btn-sm w-35px h-35px shadow-xs" title="Lihat Detail Lengkap" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $item->id }}">
                                            <i class="ki-duotone ki-eye fs-3 text-info"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                        </button>

                                        {{-- 2. Tombol Edit --}}
                                        <a href="{{ route('admin.verifikasi.edit', $item->id) }}" class="btn btn-icon btn-light-primary btn-sm w-35px h-35px shadow-xs" title="Edit & Koreksi Data">
                                            <i class="ki-duotone ki-pencil fs-3 text-primary"><span class="path1"></span><span class="path2"></span></i>
                                        </a>

                                        {{-- 3. Tombol Approve (Jika Draft) --}}
                                        @if($item->status_verifikasi === 'draft')
                                            <form action="{{ route('admin.verifikasi.approve', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin memverifikasi laporan #{{ $item->id }} ini? Data akan segera tayang di Dashboard Publik.');">
                                                @csrf
                                                <button type="submit" class="btn btn-icon btn-light-success btn-sm w-35px h-35px shadow-xs" title="Verifikasi / Setujui (Approve)">
                                                    <i class="ki-duotone ki-check fs-3 text-success"><span class="path1"></span><span class="path2"></span></i>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- 4. Tombol Hapus --}}
                                        <form action="{{ route('admin.verifikasi.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Peringatan: Apakah Anda yakin ingin menghapus data laporan ID #{{ $item->id }} ini secara permanen?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon btn-light-danger btn-sm w-35px h-35px shadow-xs" title="Hapus Laporan">
                                                <i class="ki-duotone ki-trash fs-3 text-danger"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                            </button>
                                        </form>

                                    </div>

                                    {{-- Modal Detail Laporan --}}
                                    <div class="modal fade" id="modalDetail{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered mw-650px">
                                            <div class="modal-content text-start shadow-lg">
                                                <div class="modal-header">
                                                    <h3 class="fw-bold d-flex align-items-center m-0">
                                                        <i class="ki-duotone ki-document fs-2 text-primary me-2"><span class="path1"></span><span class="path2"></span></i>
                                                        Detail Laporan Kejadian #{{ $item->id }}
                                                    </h3>
                                                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
                                                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                                                    </div>
                                                </div>
                                                <div class="modal-body py-5 px-lg-8">
                                                    <div class="row g-4 mb-4">
                                                        <div class="col-6">
                                                            <div class="text-muted fs-7">Jenis Layanan</div>
                                                            <div class="fw-bold fs-6 text-dark text-capitalize">{{ $item->jenis_layanan === 'darurat' ? 'Kebakaran (Darurat)' : 'Penyelamatan / Rescue (Non-Darurat)' }}</div>
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
                                                            <div class="fw-bold fs-6 text-dark">{{ $item->kategoriPenyebab->nama_penyebab ?? '-' }}</div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="text-muted fs-7">Personel Diterjunkan</div>
                                                            <div class="fw-bold fs-6 text-dark">{{ $item->jumlah_personel }} Orang</div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="text-muted fs-7">Taksiran Kerugian</div>
                                                            <div class="fw-bold fs-6 text-danger">Rp {{ number_format($item->taksiran_kerugian, 0, ',', '.') }}</div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="text-muted fs-7">Taksiran Nilai Terselamatkan</div>
                                                            <div class="fw-bold fs-6 text-success">Rp {{ number_format($item->taksiran_terselamatkan, 0, ',', '.') }}</div>
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
                                                            <div class="text-muted fs-8">Dilaporkan Oleh:</div>
                                                            <div class="fw-semibold fs-7 text-dark">{{ $item->pelapor->name ?? '-' }} ({{ $item->created_at->format('d/m/Y H:i') }})</div>
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
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Tutup</button>
                                                    @if($item->status_verifikasi === 'draft')
                                                        <form action="{{ route('admin.verifikasi.approve', $item->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success d-flex align-items-center">
                                                                <i class="ki-duotone ki-check fs-4 me-1"></i> Verifikasi Sekarang
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-10 text-muted">
                                    <i class="ki-duotone ki-information fs-3x text-muted mb-2 d-block"><span class="path1"></span><span class="path2"></span></i>
                                    Tidak ada data laporan ditemukan dengan kriteria filter saat ini.
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
