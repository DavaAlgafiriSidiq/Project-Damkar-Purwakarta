@extends('layouts.app')

@section('content')
<div class="container-xxl">

    {{-- Header & Title --}}
    <div class="d-flex flex-wrap flex-stack pb-6">
        <div class="d-flex flex-column justify-content-center my-1">
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                <span class="d-flex align-items-center">
                    <i class="ki-duotone ki-document fs-2x text-primary me-3"><span class="path1"></span><span class="path2"></span></i>
                    Tabel Matriks Rekapitulasi Tahunan
                </span>
            </h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted">Administrator Damkar</li>
                <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
                <li class="breadcrumb-item text-muted">Laporan &amp; Rekapitulasi</li>
                <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
                <li class="breadcrumb-item text-dark">Matriks Bulanan Excel (Tahun {{ $tahun }}{{ $zonaLayanan ? ' &middot; ' . $zonaLayanan : '' }})</li>
            </ul>
        </div>

        {{-- Action Buttons (Ekspor Excel & Print) --}}
        <div class="d-flex align-items-center gap-2 gap-lg-3 my-1">
            <a href="{{ route('admin.laporan.matriks.export', array_filter(['tahun' => $tahun, 'zona_layanan' => $zonaLayanan])) }}" class="btn btn-sm btn-success border-0 shadow-xs d-flex align-items-center">
                <i class="ki-duotone ki-file-down fs-4 me-2"><span class="path1"></span><span class="path2"></span></i>
                Ekspor Excel (.xlsx)
            </a>
            <button type="button" class="btn btn-sm btn-light-primary border-0 shadow-xs d-flex align-items-center" onclick="window.print()">
                <i class="ki-duotone ki-printer fs-4 me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                Cetak Lembar Kerja
            </button>
        </div>
    </div>

    {{-- ── BARIS 1: KPI RINGKASAN TAHUN BERJALAN & ZONA AKTIF ── --}}
    <div class="row g-5 g-xl-6 mb-7">
        {{-- Total Kejadian Terverifikasi --}}
        <div class="col-sm-6 col-xl-4">
            <div class="card h-100 bg-light-primary border border-gray-200 shadow-sm rounded-3">
                <div class="card-body d-flex align-items-center justify-content-between py-4 px-6">
                    <div class="d-flex flex-column flex-grow-1 me-3">
                        <span class="text-primary fw-bold fs-7 text-uppercase ls-1">Total Insiden Terverifikasi</span>
                        <span class="text-gray-900 fw-bolder fs-2x mt-1 tracking-tight">{{ number_format($summary['total_kejadian']) }}</span>
                        <span class="text-muted fs-8 mt-1">Akumulasi {{ $zonaLayanan ? 'Zona ' . $zonaLayanan : 'Seluruh Wilayah' }} ({{ $tahun }})</span>
                    </div>
                    <div class="symbol symbol-45px symbol-circle flex-shrink-0">
                        <span class="symbol-label bg-white shadow-xs">
                            <i class="ki-duotone ki-document fs-1 text-primary"><span class="path1"></span><span class="path2"></span></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kebakaran (Darurat) --}}
        <div class="col-sm-6 col-xl-4">
            <div class="card h-100 bg-light-danger border border-gray-200 shadow-sm rounded-3">
                <div class="card-body d-flex align-items-center justify-content-between py-4 px-6">
                    <div class="d-flex flex-column flex-grow-1 me-3">
                        <span class="text-danger fw-bold fs-7 text-uppercase ls-1">Operasi Pemadaman Kebakaran</span>
                        <span class="text-gray-900 fw-bolder fs-2x mt-1 tracking-tight">{{ number_format($summary['total_kebakaran']) }}</span>
                        <span class="text-muted fs-8 mt-1">Layanan Darurat Pemadaman Api</span>
                    </div>
                    <div class="symbol symbol-45px symbol-circle flex-shrink-0">
                        <span class="symbol-label bg-white shadow-xs">
                            <i class="ki-duotone ki-fire fs-1 text-danger"><span class="path1"></span><span class="path2"></span></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Penyelamatan / Rescue (Non-Darurat - Tema Ungu) --}}
        <div class="col-sm-12 col-xl-4">
            <div class="card h-100 border border-gray-200 shadow-sm rounded-3" style="background-color: #f8f5ff !important;">
                <div class="card-body d-flex align-items-center justify-content-between py-4 px-6">
                    <div class="d-flex flex-column flex-grow-1 me-3">
                        <span class="fw-bold fs-7 text-uppercase ls-1" style="color: #7239ea !important;">Operasi Penyelamatan (Rescue)</span>
                        <span class="text-gray-900 fw-bolder fs-2x mt-1 tracking-tight">{{ number_format($summary['total_rescue']) }}</span>
                        <span class="text-muted fs-8 mt-1">Penyelamatan Hewan &amp; Evakuasi Khusus</span>
                    </div>
                    <div class="symbol symbol-45px symbol-circle flex-shrink-0">
                        <span class="symbol-label bg-white shadow-xs">
                            <i class="ki-duotone ki-shield-tick fs-1" style="color: #7239ea !important;"><span class="path1"></span><span class="path2"></span></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── BARIS 2: CARD UTAMA TABEL MATRIKS METRONIC 8 ── --}}
    <div class="card shadow-sm border border-gray-200 mb-8">
        {{-- Card Header: Nav Tabs Sejajar Rapi & Form Filter Tahun + Zona --}}
        <div class="card-header card-header-stretch border-bottom border-gray-200">
            {{-- Nav Tabs Container (Flexbox Horizontal Sejajar Rapi) --}}
            <div class="card-title m-0">
                <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-6 fw-bold flex-nowrap" role="tablist">
                    {{-- Tab 1: Distribusi Wilayah --}}
                    <li class="nav-item d-flex align-items-center" role="presentation">
                        <a class="nav-link text-active-primary active d-inline-flex align-items-center py-4 px-4" data-bs-toggle="tab" href="#tab_wilayah" role="tab">
                            <i class="ki-duotone ki-geolocation fs-4 me-2 text-primary"><span class="path1"></span><span class="path2"></span></i>
                            <span>1. Distribusi Wilayah (Kecamatan)</span>
                            <span class="badge badge-light-primary ms-2 fs-8 fw-semibold">{{ count($matrixKecamatan) }} Wilayah</span>
                        </a>
                    </li>

                    {{-- Tab 2: Pemadaman Kebakaran --}}
                    <li class="nav-item d-flex align-items-center" role="presentation">
                        <a class="nav-link text-active-danger d-inline-flex align-items-center py-4 px-4" data-bs-toggle="tab" href="#tab_kebakaran" role="tab">
                            <i class="ki-duotone ki-fire fs-4 me-2 text-danger"><span class="path1"></span><span class="path2"></span></i>
                            <span>2. Operasi Pemadaman Kebakaran</span>
                            <span class="badge badge-light-danger ms-2 fs-8 fw-semibold">{{ number_format($summary['total_kebakaran']) }} Insiden</span>
                        </a>
                    </li>

                    {{-- Tab 3: Penyelamatan (Rescue - Tema Ungu) --}}
                    <li class="nav-item d-flex align-items-center" role="presentation">
                        <a class="nav-link tab-rescue-link d-inline-flex align-items-center py-4 px-4" data-bs-toggle="tab" href="#tab_rescue" role="tab">
                            <i class="ki-duotone ki-shield-tick fs-4 me-2" style="color: #7239ea;"><span class="path1"></span><span class="path2"></span></i>
                            <span style="color: #7239ea;">3. Operasi Penyelamatan (Rescue)</span>
                            <span class="badge ms-2 fs-8 fw-semibold" style="background-color: rgba(114, 57, 234, 0.1); color: #7239ea;">{{ number_format($summary['total_rescue']) }} Kasus</span>
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Toolbar: Filter Tahun & Filter Zona Layanan --}}
            <div class="card-toolbar m-0 py-2">
                <form id="formFilterMatriks" action="{{ route('admin.laporan.matriks') }}" method="GET" class="d-flex align-items-center gap-3">
                    {{-- Filter Tahun --}}
                    <div class="d-flex align-items-center gap-2">
                        <label for="selectTahun" class="form-label fs-7 fw-bold text-gray-700 text-nowrap mb-0">
                            <i class="ki-duotone ki-calendar fs-6 me-1"><span class="path1"></span><span class="path2"></span></i>
                            Tahun:
                        </label>
                        <div style="min-width: 110px; width: 115px;">
                            <select name="tahun" id="selectTahun" class="form-select form-select-sm form-select-solid fw-bold" data-control="select2" data-hide-search="true">
                                @foreach($tahunList as $th)
                                    <option value="{{ $th }}" data-icon="ki-duotone ki-calendar" {{ (int)$tahun === (int)$th ? 'selected' : '' }}>{{ $th }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Filter Zona Layanan (WMK / UPTD) --}}
                    <div class="d-flex align-items-center gap-2">
                        <label for="selectZona" class="form-label fs-7 fw-bold text-gray-700 text-nowrap mb-0">
                            <i class="ki-duotone ki-compass fs-6 me-1"><span class="path1"></span><span class="path2"></span></i>
                            Zona:
                        </label>
                        <div style="min-width: 200px; width: 215px;">
                            <select name="zona_layanan" id="selectZona" class="form-select form-select-sm form-select-solid fw-bold" data-control="select2" data-hide-search="true">
                                <option value="" data-icon="ki-duotone ki-compass">Semua Zona Layanan</option>
                                @foreach($zonaList as $zona)
                                    <option value="{{ $zona }}" data-icon="ki-duotone ki-geolocation" {{ $zonaLayanan === $zona ? 'selected' : '' }}>{{ $zona }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @if($zonaLayanan)
                        <a href="{{ route('admin.laporan.matriks', ['tahun' => $tahun]) }}" id="btnResetZona" class="btn btn-sm btn-icon btn-light-danger" title="Reset Filter Zona">
                            <i class="ki-duotone ki-cross fs-4"><span class="path1"></span><span class="path2"></span></i>
                        </a>
                    @endif
                </form>
            </div>
        </div>

        {{-- Card Body: Tab Contents --}}
        <div class="card-body p-6">
            <div class="tab-content" id="matrixTabContent">

                {{-- ══════════════════════════════════════════════════════════════
                     TAB 1: DISTRIBUSI WILAYAH (KECAMATAN) — SEMUA KEJADIAN
                     ══════════════════════════════════════════════════════════════ --}}
                <div class="tab-pane fade show active" id="tab_wilayah" role="tabpanel">
                    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                        <div>
                            <h4 class="text-gray-800 fw-bold mb-1">
                                Rekapitulasi Seluruh Kejadian Berdasarkan Wilayah Kecamatan (Tahun {{ $tahun }})
                            </h4>
                            <span class="text-muted fs-7">
                                Menggabungkan seluruh insiden kebakaran dan penyelamatan/rescue. {{ $zonaLayanan ? 'Menampilkan khusus wilayah ' . $zonaLayanan . '.' : 'Mencakup seluruh kecamatan Purwakarta & wilayah perbantuan luar daerah.' }} Data hanya mencakup status <span class="badge badge-light-success fs-9 py-0">Verified</span>.
                            </span>
                        </div>
                        <span class="badge badge-light-primary fw-bold py-2 px-3">
                            Format Baku Excel &middot; Rekapitulasi Wilayah
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle gs-3 gy-2 fs-7 border border-gray-300 excel-matrix-table">
                            <thead class="bg-light-primary text-gray-800 fw-bold border-bottom-2 border-primary">
                                <tr class="text-center align-middle">
                                    <th style="width: 40px;" class="text-center">No</th>
                                    <th style="min-width: 180px;" class="text-start">Kecamatan / Wilayah</th>
                                    <th style="width: 120px;" class="text-center">Zona Layanan (WMK)</th>
                                    @foreach($namaBulan as $mNum => $mLabel)
                                        <th style="min-width: 42px;" class="text-center px-1">{{ $mLabel }}</th>
                                    @endforeach
                                    <th style="min-width: 70px;" class="text-center bg-primary text-white fw-bolder">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $noKec = 1; @endphp
                                @forelse($matrixKecamatan as $kecId => $row)
                                    @php
                                        $isLuarKab = ($row['nama'] === 'Luar Kabupaten' || $row['zona_layanan'] === 'Luar Daerah');
                                    @endphp
                                    <tr class="{{ $isLuarKab ? 'bg-light-warning' : '' }}">
                                        <td class="text-center text-muted fw-semibold">{{ $noKec++ }}</td>
                                        <td class="fw-semibold text-gray-800 text-start">
                                            {{ $row['nama'] }}
                                            @if($isLuarKab)
                                                <span class="badge badge-warning text-dark fs-9 py-0 px-2 ms-1" title="Perbantuan Antar Wilayah (Mutual Aid)">Perbantuan</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($row['zona_layanan'] === 'WMK Pusat')
                                                <span class="badge badge-light-primary fs-9 py-0 px-2">WMK Pusat</span>
                                            @elseif($row['zona_layanan'] === 'WMK UPTD 1')
                                                <span class="badge badge-light-success fs-9 py-0 px-2">WMK UPTD 1</span>
                                            @elseif($row['zona_layanan'] === 'WMK UPTD 2')
                                                <span class="badge badge-light-info fs-9 py-0 px-2">WMK UPTD 2</span>
                                            @elseif($row['zona_layanan'] === 'WMK UPTD 3')
                                                <span class="badge badge-light-warning fs-9 py-0 px-2">WMK UPTD 3</span>
                                            @elseif($row['zona_layanan'] === 'Luar Daerah')
                                                <span class="badge badge-light-danger fs-9 py-0 px-2">Luar Daerah</span>
                                            @else
                                                <span class="badge badge-light-secondary fs-9 py-0 px-2">{{ $row['zona_layanan'] }}</span>
                                            @endif
                                        </td>
                                        @foreach($namaBulan as $mNum => $mLabel)
                                            @php $val = $row['bulan'][$mNum]; @endphp
                                            <td class="text-center {{ $val > 0 ? 'fw-bold text-gray-900 bg-light-primary-subtle' : 'text-muted' }}">
                                                {{ $val > 0 ? $val : '-' }}
                                            </td>
                                        @endforeach
                                        <td class="text-center fw-bolder bg-light-info text-primary fs-6">
                                            {{ $row['total'] > 0 ? number_format($row['total']) : '0' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="16" class="text-center py-5 text-muted">Tidak ada data kecamatan pada filter zona ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-gray-100 fw-bolder text-gray-800 border-top-2 border-dark">
                                <tr class="text-center align-middle fs-7">
                                    <td colspan="3" class="text-end pe-4 fw-bolder text-uppercase">TOTAL KESELURUHAN (BULAN):</td>
                                    @foreach($namaBulan as $mNum => $mLabel)
                                        @php $totBln = $totalBulanKecamatan[$mNum]; @endphp
                                        <td class="text-center fw-bolder {{ $totBln > 0 ? 'text-primary' : 'text-muted' }}">
                                            {{ $totBln > 0 ? number_format($totBln) : '0' }}
                                        </td>
                                    @endforeach
                                    <td class="text-center fw-bolder bg-primary text-white fs-6">
                                        {{ number_format($grandTotalKecamatan) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                {{-- ══════════════════════════════════════════════════════════════
                     TAB 2: OPERASI PEMADAMAN KEBAKARAN (MURNI KEBAKARAN)
                     Berisi 2 Tabel Vertikal:
                       (a) Berdasarkan Objek Kebakaran
                       (b) Berdasarkan Dugaan Penyebab Api
                     ══════════════════════════════════════════════════════════════ --}}
                <div class="tab-pane fade" id="tab_kebakaran" role="tabpanel">

                    {{-- TABEL 2.A: BERDASARKAN OBJEK KEBAKARAN --}}
                    <div class="mb-8">
                        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                            <div>
                                <h4 class="text-gray-800 fw-bold mb-1">
                                    <i class="ki-duotone ki-home fs-4 text-danger me-1"><span class="path1"></span><span class="path2"></span></i>
                                    Tabel 2.A &middot; Rekapitulasi Berdasarkan Objek Kebakaran (Tahun {{ $tahun }})
                                </h4>
                                <span class="text-muted fs-7">Murni insiden pemadaman kebakaran (bangunan tempat tinggal, industri, sarana umum, serta hutan &amp; lahan).</span>
                            </div>
                            <span class="badge badge-light-danger fw-bold py-2 px-3">
                                Objek Bangunan &amp; Karhutla
                            </span>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle gs-3 gy-2 fs-7 border border-gray-300 excel-matrix-table">
                                <thead class="bg-light-danger text-gray-800 fw-bold border-bottom-2 border-danger">
                                    <tr class="text-center align-middle">
                                        <th style="width: 40px;" class="text-center">No</th>
                                        <th style="min-width: 220px;" class="text-start">Kategori Objek Kebakaran</th>
                                        <th style="min-width: 130px;" class="text-center">Klasifikasi</th>
                                        @foreach($namaBulan as $mNum => $mLabel)
                                            <th style="min-width: 42px;" class="text-center px-1">{{ $mLabel }}</th>
                                        @endforeach
                                        <th style="min-width: 70px;" class="text-center bg-danger text-white fw-bolder">TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $noObjK = 1; @endphp
                                    @forelse($matrixObjekKebakaran as $objId => $row)
                                        <tr>
                                            <td class="text-center text-muted fw-semibold">{{ $noObjK++ }}</td>
                                            <td class="fw-semibold text-gray-800 text-start">
                                                {{ $row['nama'] }}
                                            </td>
                                            <td class="text-center">
                                                <span class="badge {{ $row['tipe_badge_class'] }} fs-9 py-0 px-2">
                                                    {{ $row['tipe_label'] }}
                                                </span>
                                            </td>
                                            @foreach($namaBulan as $mNum => $mLabel)
                                                @php $val = $row['bulan'][$mNum]; @endphp
                                                <td class="text-center {{ $val > 0 ? 'fw-bold text-gray-900 bg-light-danger-subtle' : 'text-muted' }}">
                                                    {{ $val > 0 ? $val : '-' }}
                                                </td>
                                            @endforeach
                                            <td class="text-center fw-bolder bg-light-danger text-danger fs-6">
                                                {{ $row['total'] > 0 ? number_format($row['total']) : '0' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ 3 + count($namaBulan) + 1 }}" class="text-center text-muted py-5">
                                                Tidak ada data kategori objek kebakaran untuk filter yang dipilih.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="bg-gray-100 fw-bolder text-gray-800 border-top-2 border-dark">
                                    <tr class="text-center align-middle fs-7">
                                        <td colspan="3" class="text-end pe-4 fw-bolder text-uppercase">TOTAL KEBAKARAN (BULAN):</td>
                                        @foreach($namaBulan as $mNum => $mLabel)
                                            @php $totBln = $totalBulanObjekKebakaran[$mNum]; @endphp
                                            <td class="text-center fw-bolder {{ $totBln > 0 ? 'text-danger' : 'text-muted' }}">
                                                {{ $totBln > 0 ? number_format($totBln) : '0' }}
                                            </td>
                                        @endforeach
                                        <td class="text-center fw-bolder bg-danger text-white fs-6">
                                            {{ number_format($grandTotalObjekKebakaran) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="separator separator-dashed my-8"></div>

                    {{-- TABEL 2.B: BERDASARKAN DUGAAN PENYEBAB API --}}
                    <div>
                        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                            <div>
                                <h4 class="text-gray-800 fw-bold mb-1">
                                    <i class="ki-duotone ki-fire fs-4 text-warning me-1"><span class="path1"></span><span class="path2"></span></i>
                                    Tabel 2.B &middot; Rekapitulasi Berdasarkan Dugaan Penyebab Kebakaran (Tahun {{ $tahun }})
                                </h4>
                                <span class="text-muted fs-7">Faktor asal mula timbulnya api pada kejadian kebakaran (korsleting, gas, pembakaran sampah, dll).</span>
                            </div>
                            <span class="badge badge-light-warning fw-bold py-2 px-3">
                                Analisis Faktor Penyebab Api
                            </span>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle gs-3 gy-2 fs-7 border border-gray-300 excel-matrix-table">
                                <thead class="bg-light-warning text-gray-800 fw-bold border-bottom-2 border-warning">
                                    <tr class="text-center align-middle">
                                        <th style="width: 40px;" class="text-center">No</th>
                                        <th style="min-width: 250px;" class="text-start">Kategori / Dugaan Penyebab Api</th>
                                        <th style="width: 120px;" class="text-center">Klasifikasi</th>
                                        @foreach($namaBulan as $mNum => $mLabel)
                                            <th style="min-width: 42px;" class="text-center px-1">{{ $mLabel }}</th>
                                        @endforeach
                                        <th style="min-width: 70px;" class="text-center bg-warning text-dark fw-bolder">TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $noPenyebab = 1; @endphp
                                    @forelse($matrixPenyebab as $pId => $row)
                                        <tr>
                                            <td class="text-center text-muted fw-semibold">{{ $noPenyebab++ }}</td>
                                            <td class="fw-semibold text-gray-800 text-start">
                                                {{ $row['nama'] }}
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-light-danger fs-9 py-0 px-2">Penyebab Api</span>
                                            </td>
                                            @foreach($namaBulan as $mNum => $mLabel)
                                                @php $val = $row['bulan'][$mNum]; @endphp
                                                <td class="text-center {{ $val > 0 ? 'fw-bold text-gray-900 bg-light-warning-subtle' : 'text-muted' }}">
                                                    {{ $val > 0 ? $val : '-' }}
                                                </td>
                                            @endforeach
                                            <td class="text-center fw-bolder bg-light-warning text-dark fs-6">
                                                {{ $row['total'] > 0 ? number_format($row['total']) : '0' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ 3 + count($namaBulan) + 1 }}" class="text-center text-muted py-5">
                                                Tidak ada data faktor penyebab api untuk filter yang dipilih.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="bg-gray-100 fw-bolder text-gray-800 border-top-2 border-dark">
                                    <tr class="text-center align-middle fs-7">
                                        <td colspan="3" class="text-end pe-4 fw-bolder text-uppercase">TOTAL PENYEBAB API (BULAN):</td>
                                        @foreach($namaBulan as $mNum => $mLabel)
                                            @php $totBln = $totalBulanPenyebab[$mNum]; @endphp
                                            <td class="text-center fw-bolder {{ $totBln > 0 ? 'text-dark' : 'text-muted' }}">
                                                {{ $totBln > 0 ? number_format($totBln) : '0' }}
                                            </td>
                                        @endforeach
                                        <td class="text-center fw-bolder bg-warning text-dark fs-6">
                                            {{ number_format($grandTotalPenyebab) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                </div>

                {{-- ══════════════════════════════════════════════════════════════
                     TAB 3: OPERASI PENYELAMATAN (RESCUE) — MURNI NON-DARURAT
                     Berisi 1 Tabel: Berdasarkan Jenis Evakuasi & Penyelamatan
                     ══════════════════════════════════════════════════════════════ --}}
                <div class="tab-pane fade" id="tab_rescue" role="tabpanel">
                    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                        <div>
                            <h4 class="text-gray-800 fw-bold mb-1">
                                <i class="ki-duotone ki-shield-tick fs-4 text-info me-1"><span class="path1"></span><span class="path2"></span></i>
                                Rekapitulasi Operasi Penyelamatan (Rescue) Berdasarkan Jenis Kasus (Tahun {{ $tahun }})
                            </h4>
                            <span class="text-muted fs-7">
                                Murni layanan penyelamatan non-darurat kebakaran. Terdiri dari operasi penyelamatan hewan membahayakan/terjebak dan evakuasi kedaruratan khusus.
                            </span>
                        </div>
                        <span class="badge badge-light-info fw-bold py-2 px-3">
                            Hewan Liar &amp; Evakuasi Khusus
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle gs-3 gy-2 fs-7 border border-gray-300 excel-matrix-table">
                            <thead class="bg-light-info text-gray-800 fw-bold border-bottom-2 border-info">
                                <tr class="text-center align-middle">
                                    <th style="width: 40px;" class="text-center">No</th>
                                    <th style="min-width: 250px;" class="text-start">Jenis Evakuasi &amp; Penyelamatan</th>
                                    <th style="min-width: 160px;" class="text-center">Klasifikasi</th>
                                    @foreach($namaBulan as $mNum => $mLabel)
                                        <th style="min-width: 42px;" class="text-center px-1">{{ $mLabel }}</th>
                                    @endforeach
                                    <th style="min-width: 70px;" class="text-center bg-info text-white fw-bolder">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $noRescue = 1; @endphp
                                @forelse($matrixRescue as $rId => $row)
                                    <tr>
                                        <td class="text-center text-muted fw-semibold">{{ $noRescue++ }}</td>
                                        <td class="fw-semibold text-gray-800 text-start">
                                            {{ $row['nama'] }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $row['tipe_badge_class'] }} fs-9 py-0 px-2">
                                                {{ $row['tipe_label'] }}
                                            </span>
                                        </td>
                                        @foreach($namaBulan as $mNum => $mLabel)
                                            @php $val = $row['bulan'][$mNum]; @endphp
                                            <td class="text-center {{ $val > 0 ? 'fw-bold text-gray-900 bg-light-info-subtle' : 'text-muted' }}">
                                                {{ $val > 0 ? $val : '-' }}
                                            </td>
                                        @endforeach
                                        <td class="text-center fw-bolder bg-light-info text-info fs-6">
                                            {{ $row['total'] > 0 ? number_format($row['total']) : '0' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ 3 + count($namaBulan) + 1 }}" class="text-center text-muted py-5">
                                            Tidak ada data operasi penyelamatan (rescue) untuk filter yang dipilih.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-gray-100 fw-bolder text-gray-800 border-top-2 border-dark">
                                <tr class="text-center align-middle fs-7">
                                    <td colspan="3" class="text-end pe-4 fw-bolder text-uppercase">TOTAL RESCUE (BULAN):</td>
                                    @foreach($namaBulan as $mNum => $mLabel)
                                        @php $totBln = $totalBulanRescue[$mNum]; @endphp
                                        <td class="text-center fw-bolder {{ $totBln > 0 ? 'text-info' : 'text-muted' }}">
                                            {{ $totBln > 0 ? number_format($totBln) : '0' }}
                                        </td>
                                    @endforeach
                                    <td class="text-center fw-bolder bg-info text-white fs-6">
                                        {{ number_format($grandTotalRescue) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            </div>
        </div>

        {{-- Card Footer --}}
        <div class="card-footer py-4 d-flex flex-wrap align-items-center justify-content-between text-muted fs-8">
            <div>
                <i class="ki-duotone ki-information-5 fs-6 me-1 text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                Seluruh nilai bersumber dari laporan yang telah diverifikasi (status <strong>verified</strong>) di database Dinas Pemadam Kebakaran Purwakarta.
                @if($zonaLayanan)
                    &middot; Filter Aktif: Zona <strong>{{ $zonaLayanan }}</strong>
                @endif
            </div>
            <div>
                Tahun Rekapitulasi: <strong>{{ $tahun }}</strong> &middot; Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }}
            </div>
        </div>
    </div>

</div>

{{-- Custom CSS untuk Tampilan Excel / Print Styling --}}
@push('styles')
<style>
.excel-matrix-table {
    border-collapse: collapse;
}
.excel-matrix-table th, .excel-matrix-table td {
    border: 1px solid #dcdfe3 !important;
}
.excel-matrix-table tbody tr:hover {
    background-color: #f8f9fa !important;
}
.bg-light-primary-subtle {
    background-color: #f1faff !important;
}
.bg-light-danger-subtle {
    background-color: #fff5f8 !important;
}
.bg-light-warning-subtle {
    background-color: #fff8dd !important;
}
.bg-light-info-subtle {
    background-color: #f1faff !important;
}

@media print {
    .btn, .breadcrumb, .nav-line-tabs, #selectTahun, #selectZona, .select2-container, form {
        display: none !important;
    }
    .card {
        box-shadow: none !important;
        border: none !important;
    }
    .tab-pane {
        display: block !important;
        opacity: 1 !important;
        page-break-after: always;
    }
    .excel-matrix-table th, .excel-matrix-table td {
        font-size: 8pt !important;
        padding: 2px 4px !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var formFilter = document.getElementById('formFilterMatriks');
    var btnReset = document.getElementById('btnResetZona');

    function getActiveTabHash() {
        var activeTabLink = document.querySelector('.nav-line-tabs .nav-link.active');
        return activeTabLink ? activeTabLink.getAttribute('href') : (window.location.hash || '#tab_wilayah');
    }

    function updateFormAndResetAction(hash) {
        if (!hash) hash = '#tab_wilayah';
        if (formFilter) {
            formFilter.action = formFilter.action.split('#')[0] + hash;
        }
        if (btnReset) {
            btnReset.href = btnReset.href.split('#')[0] + hash;
        }
    }

    // 1. STATE RETENTION: Pulihkan Tab dari URL Hash saat halaman dimuat ulang
    var initialHash = window.location.hash;
    if (initialHash && document.querySelector('.nav-line-tabs .nav-link[href="' + initialHash + '"]')) {
        var targetTabLink = document.querySelector('.nav-line-tabs .nav-link[href="' + initialHash + '"]');
        if (window.bootstrap && bootstrap.Tab) {
            var tabInstance = bootstrap.Tab.getOrCreateInstance(targetTabLink);
            tabInstance.show();
        } else if (window.$) {
            $(targetTabLink).tab('show');
        }
    }

    // 2. Listener: Simpan hash ke URL & form action saat pengguna berpindah tab
    var tabLinks = document.querySelectorAll('.nav-line-tabs .nav-link[data-bs-toggle="tab"]');
    tabLinks.forEach(function(tabLink) {
        tabLink.addEventListener('shown.bs.tab', function(e) {
            var currentHash = e.target.getAttribute('href');
            if (currentHash && history.replaceState) {
                history.replaceState(null, null, currentHash);
            }
            updateFormAndResetAction(currentHash);
        });
    });

    // Inisialisasi awal form action & tombol reset
    updateFormAndResetAction(getActiveTabHash());

    // 3. Filter Submit & Change: Pastikan hash selalu dipertahankan saat filter Zona/Tahun dikirim
    if (formFilter) {
        formFilter.addEventListener('submit', function(e) {
            e.preventDefault();
            var activeHash = getActiveTabHash();
            var url = new URL(formFilter.action, window.location.origin);
            var formData = new FormData(formFilter);
            for (var pair of formData.entries()) {
                if (pair[1] !== '') {
                    url.searchParams.set(pair[0], pair[1]);
                } else {
                    url.searchParams.delete(pair[0]);
                }
            }
            url.hash = activeHash;
            window.location.href = url.toString();
        });
    }

    if (btnReset) {
        btnReset.addEventListener('click', function(e) {
            e.preventDefault();
            var activeHash = getActiveTabHash();
            var url = new URL(btnReset.href, window.location.origin);
            url.hash = activeHash;
            window.location.href = url.toString();
        });
    }

    if (window.$) {
        $('#selectTahun, #selectZona').on('change', function() {
            if (formFilter) {
                $(formFilter).trigger('submit');
            }
        });
    }
});
</script>
@endpush
@endsection
