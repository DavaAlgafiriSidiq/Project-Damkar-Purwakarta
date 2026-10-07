@extends('layouts.app')

@section('content')
<style>
@keyframes liveAlertPulse {
    0% {
        box-shadow: 0 0 0 0 rgba(241, 65, 108, 0.7);
        transform: scale(1);
    }
    50% {
        box-shadow: 0 0 25px 6px rgba(241, 65, 108, 0.4);
        transform: scale(1.005);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(241, 65, 108, 0.7);
        transform: scale(1);
    }
}
.live-alert-pulse {
    animation: liveAlertPulse 2.2s infinite ease-in-out;
    background: linear-gradient(90deg, #fff5f8 0%, #ffe2e5 100%);
    border: 2px solid #f1416c !important;
}
@keyframes iconFlash {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.8; transform: scale(1.15); }
}
.alert-icon-pulse {
    animation: iconFlash 1.4s infinite ease-in-out;
}
</style>

<div class="container-fluid py-4">

    {{-- =====================================================================
         BANNER LIVE ALERT (Notifikasi Real-Time Kejadian Aktif)
    ====================================================================== --}}
    <div class="alert alert-danger d-none align-items-center p-4 p-lg-5 mb-6 rounded-3 shadow-lg live-alert-pulse" id="liveAlertBanner" role="alert" style="display: none;">
        <div class="d-flex align-items-center justify-content-center bg-danger text-white rounded-circle p-3 me-4 flex-shrink-0 shadow alert-icon-pulse">
            <i class="ki-duotone ki-notification-on fs-1 text-white"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
        </div>
        <div class="d-flex flex-column flex-grow-1">
            <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                <span class="badge badge-danger text-white fw-bolder px-2 py-1 fs-8 text-uppercase">SIAGA AKTIF</span>
                <span class="text-danger fw-bolder fs-5" id="liveAlertText">
                    ⚠️ INFO DARURAT: Sedang terjadi penanganan insiden di Wilayah Purwakarta!
                </span>
            </div>
            <div class="text-gray-800 fs-7" id="liveAlertSubtext">
                Petugas Pemadam Kebakaran dan Penyelamatan Kabupaten Purwakarta sedang dalam proses penanganan di lokasi.
            </div>
        </div>
        <div class="ms-auto ps-3 flex-shrink-0 d-none d-md-block">
            <span class="badge badge-light-danger fw-bold fs-8 px-3 py-2 border border-danger border-dashed" id="liveAlertTime">
                Waktu Kejadian
            </span>
        </div>
    </div>

    {{-- =====================================================================
         HEADER DASHBOARD (Compact)
    ====================================================================== --}}
    <div class="d-flex align-items-center justify-content-between mb-5">
        <div>
            <h3 class="fw-bolder mb-0">
                Dashboard Analitik Publik
            </h3>
            <span class="text-muted fs-7">Statistik Terverifikasi Â· Kabupaten Purwakarta</span>
        </div>
        <span class="badge badge-light-primary fs-8 fw-bold px-3 py-2">
            <i class="ki-duotone ki-shield-tick fs-7 me-1"><span class="path1"></span><span class="path2"></span></i>
            Data Terverifikasi
        </span>
    </div>

    {{-- =====================================================================
         SEKSI 1: RINGKASAN KPI
         Filter inline di dalam card-header, KPI cards di bawahnya
    ====================================================================== --}}
    <div class="card shadow-sm mb-4 border-0">
        {{-- Section Header dengan Filter Inline --}}
        <div class="card-header border-0 py-3 px-5 bg-light rounded-top" style="min-height: auto;">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 w-100">
                {{-- Judul kiri --}}
                <div class="d-flex align-items-center gap-2">
                    <i class="ki-duotone ki-element-11 fs-4 text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                    <span class="fw-bolder fs-6 text-dark">Ringkasan KPI</span>
                    <span class="badge badge-light fs-9 text-muted fw-semibold">Seluruh Insiden</span>
                </div>
                {{-- Filter inline kanan --}}
                <form id="filterKeseluruhan" class="d-flex align-items-center gap-2 flex-wrap">
                    <select name="tahun" id="kpi_filter_tahun" class="form-select form-select-sm" style="width:80px;">
                        <option value="2026" {{ $tahun == 2026 ? 'selected' : '' }}>2026</option>
                        <option value="2025" {{ $tahun == 2025 ? 'selected' : '' }}>2025</option>
                        <option value="2024" {{ $tahun == 2024 ? 'selected' : '' }}>2024</option>
                    </select>
                    <select name="bulan" id="kpi_filter_bulan" class="form-select form-select-sm" style="width:110px;">
                        <option value="">Semua Bulan</option>
                        @foreach(['1'=>'Jan','2'=>'Feb','3'=>'Mar','4'=>'Apr','5'=>'Mei','6'=>'Jun','7'=>'Jul','8'=>'Ags','9'=>'Sep','10'=>'Okt','11'=>'Nov','12'=>'Des'] as $num => $namaBulan)
                            <option value="{{ $num }}" {{ (string)($bulan ?? '') === (string)$num ? 'selected' : '' }}>{{ $namaBulan }}</option>
                        @endforeach
                    </select>
                    <select name="kecamatan_id" id="kpi_filter_kecamatan" class="form-select form-select-sm" style="width:145px;">
                        <option value="">Semua Kecamatan</option>
                        @foreach($kecamatans as $kec)
                            <option value="{{ $kec->id }}" {{ (string)($kecamatanId ?? '') === (string)$kec->id ? 'selected' : '' }}>{{ $kec->nama_kecamatan }}</option>
                        @endforeach
                    </select>
                    <select name="kategori_objek_id" id="kpi_filter_kategori_objek" class="form-select form-select-sm" style="width:130px;">
                        <option value="">Semua Objek</option>
                        @foreach($kategoriObjeks as $obj)
                            <option value="{{ $obj->id }}" {{ (string)($kategoriObjekId ?? '') === (string)$obj->id ? 'selected' : '' }}>{{ $obj->nama_kategori }}</option>
                        @endforeach
                    </select>
                    <select name="kategori_penyebab_id" id="kpi_filter_kategori_penyebab" class="form-select form-select-sm" style="width:135px;">
                        <option value="">Semua Penyebab</option>
                        @foreach($kategoriPenyebabs as $penyebab)
                            <option value="{{ $penyebab->id }}" {{ (string)($kategoriPenyebabId ?? '') === (string)$penyebab->id ? 'selected' : '' }}>{{ $penyebab->nama_penyebab }}</option>
                        @endforeach
                    </select>
                    <button type="submit" id="btnFilterKPI" class="btn btn-primary btn-sm px-3">
                        <i class="las la-search fs-7 me-1"></i>Filter
                    </button>
                    <button type="reset" class="btn btn-light btn-sm px-2">
                        <i class="las la-times fs-7"></i>
                    </button>
                </form>
            </div>
        </div>

        {{-- KPI Cards â€” Compact --}}
        <div class="card-body px-5 py-4">
            <div class="row g-3">
                {{-- Widget 1: Total Seluruh --}}
                <div class="col-xl col-md-4 col-sm-6">
                    <div class="card card-flush bg-dark bg-opacity-5 border border-gray-200 shadow-xs hover-elevate-up h-100 rounded-2">
                        <div class="card-body px-4 py-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold fs-7 text-gray-600">Total Seluruh</span>
                                <div class="symbol symbol-30px symbol-circle bg-white shadow-xs p-1">
                                    <i class="ki-duotone ki-element-11 fs-4 text-dark"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                </div>
                            </div>
                            <div class="d-flex align-items-baseline gap-1">
                                <span class="fs-2 fw-bolder text-dark" id="kpiTotalSeluruh">{{ number_format($totalSeluruhKejadian ?? 0) }}</span>
                                <span class="text-muted fs-8 fw-semibold">insiden</span>
                            </div>
                            <div class="text-muted fs-9 mt-1">Kebakaran + Rescue</div>
                        </div>
                    </div>
                </div>

                {{-- Widget 2: Total Kebakaran --}}
                <div class="col-xl col-md-4 col-sm-6">
                    <div class="card card-flush bg-danger bg-opacity-10 border border-danger border-opacity-25 shadow-xs hover-elevate-up h-100 rounded-2">
                        <div class="card-body px-4 py-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold fs-7 text-danger">Total Kebakaran</span>
                                <div class="symbol symbol-30px symbol-circle bg-white shadow-xs p-1">
                                    <i class="las la-fire fs-4 text-danger"></i>
                                </div>
                            </div>
                            <div class="d-flex align-items-baseline gap-1">
                                <span class="fs-2 fw-bolder text-dark" id="kpiTotalKebakaran">{{ number_format($totalKebakaran ?? 0) }}</span>
                                <span class="text-muted fs-8 fw-semibold">kejadian</span>
                            </div>
                            <div class="text-muted fs-9 mt-1">Insiden kebakaran aktif</div>
                        </div>
                    </div>
                </div>

                {{-- Widget 3: Total Karhutla --}}
                <div class="col-xl col-md-4 col-sm-6">
                    <div class="card card-flush bg-warning bg-opacity-10 border border-warning border-opacity-25 shadow-xs hover-elevate-up h-100 rounded-2">
                        <div class="card-body px-4 py-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold fs-7 text-warning">Total Karhutla</span>
                                <div class="symbol symbol-30px symbol-circle bg-white shadow-xs p-1">
                                    <i class="ki-duotone ki-tree fs-4 text-warning"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                </div>
                            </div>
                            <div class="d-flex align-items-baseline gap-1">
                                <span class="fs-2 fw-bolder text-dark" id="kpiTotalKarhutla">{{ number_format($totalKarhutla ?? 0) }}</span>
                                <span class="text-muted fs-8 fw-semibold">kejadian</span>
                            </div>
                            <div class="text-muted fs-9 mt-1">Kebakaran hutan &amp; lahan</div>
                        </div>
                    </div>
                </div>

                {{-- Widget 4: Total Rescue --}}
                <div class="col-xl col-md-6 col-sm-6">
                    <div class="card card-flush bg-primary bg-opacity-10 border border-primary border-opacity-25 shadow-xs hover-elevate-up h-100 rounded-2">
                        <div class="card-body px-4 py-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold fs-7 text-primary">Total Rescue</span>
                                <div class="symbol symbol-30px symbol-circle bg-white shadow-xs p-1">
                                    <i class="ki-duotone ki-rescue fs-4 text-primary"><span class="path1"></span><span class="path2"></span></i>
                                </div>
                            </div>
                            <div class="d-flex align-items-baseline gap-1">
                                <span class="fs-2 fw-bolder text-dark" id="kpiTotalRescue">{{ number_format($totalRescue ?? 0) }}</span>
                                <span class="text-muted fs-8 fw-semibold">operasi</span>
                            </div>
                            <div class="text-muted fs-9 mt-1">Penyelamatan non-darurat</div>
                        </div>
                    </div>
                </div>

                {{-- Widget 5: Kecamatan Hotspot --}}
                <div class="col-xl col-md-6 col-sm-12">
                    <div class="card card-flush bg-info bg-opacity-10 border border-info border-opacity-25 shadow-xs hover-elevate-up h-100 rounded-2">
                        <div class="card-body px-4 py-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold fs-7 text-info">Kecamatan Hotspot</span>
                                <div class="symbol symbol-30px symbol-circle bg-white shadow-xs p-1">
                                    <i class="ki-duotone ki-geolocation fs-4 text-info"><span class="path1"></span><span class="path2"></span></i>
                                </div>
                            </div>
                            <div class="d-flex align-items-baseline gap-1">
                                <span class="fs-5 fw-bolder text-dark lh-sm" id="kpiKecamatanHotspot">{{ $kecamatanTertinggi ? $kecamatanTertinggi->kecamatan : '-' }}</span>
                            </div>
                            <div class="text-muted fs-9 mt-1">Wilayah frekuensi tertinggi</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- =====================================================================
         SEKSI 2: DATA INSIDEN KEBAKARAN
         Filter inline di card-header, charts di card-body
    ====================================================================== --}}
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-header border-0 py-3 px-5 rounded-top" style="min-height: auto; background: linear-gradient(90deg, #fff5f5 0%, #fff 100%); border-left: 4px solid #f1416c !important;">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 w-100">
                {{-- Judul kiri --}}
                <div class="d-flex align-items-center gap-2">
                    <i class="las la-fire fs-4 text-danger"></i>
                    <span class="fw-bolder fs-6 text-danger">Data Insiden Kebakaran</span>
                    <span class="badge badge-light-danger fs-9 fw-bold">Darurat</span>
                </div>
                {{-- Filter inline kanan --}}
                <form id="filterKebakaran" class="d-flex align-items-center gap-2 flex-wrap">
                    <select name="tahun" id="kebakaran_filter_tahun" class="form-select form-select-sm" style="width:80px;">
                        <option value="2026" {{ $tahun == 2026 ? 'selected' : '' }}>2026</option>
                        <option value="2025" {{ $tahun == 2025 ? 'selected' : '' }}>2025</option>
                        <option value="2024" {{ $tahun == 2024 ? 'selected' : '' }}>2024</option>
                    </select>
                    <select name="bulan" id="kebakaran_filter_bulan" class="form-select form-select-sm" style="width:110px;">
                        <option value="">Semua Bulan</option>
                        @foreach(['1'=>'Jan','2'=>'Feb','3'=>'Mar','4'=>'Apr','5'=>'Mei','6'=>'Jun','7'=>'Jul','8'=>'Ags','9'=>'Sep','10'=>'Okt','11'=>'Nov','12'=>'Des'] as $num => $namaBulan)
                            <option value="{{ $num }}" {{ (string)($bulan ?? '') === (string)$num ? 'selected' : '' }}>{{ $namaBulan }}</option>
                        @endforeach
                    </select>
                    <select name="kecamatan_id" id="kebakaran_filter_kecamatan" class="form-select form-select-sm" style="width:145px;">
                        <option value="">Semua Kecamatan</option>
                        @foreach($kecamatans as $kec)
                            <option value="{{ $kec->id }}" {{ (string)($kecamatanId ?? '') === (string)$kec->id ? 'selected' : '' }}>{{ $kec->nama_kecamatan }}</option>
                        @endforeach
                    </select>
                    <select name="kategori_objek_id" id="kebakaran_filter_kategori_objek" class="form-select form-select-sm" style="width:130px;">
                        <option value="">Semua Objek</option>
                        @foreach($kategoriObjeks as $obj)
                            <option value="{{ $obj->id }}" {{ (string)($kategoriObjekId ?? '') === (string)$obj->id ? 'selected' : '' }}>{{ $obj->nama_kategori }}</option>
                        @endforeach
                    </select>
                    <select name="kategori_penyebab_id" id="kebakaran_filter_kategori_penyebab" class="form-select form-select-sm" style="width:135px;">
                        <option value="">Semua Penyebab</option>
                        @foreach($kategoriPenyebabs as $penyebab)
                            <option value="{{ $penyebab->id }}" {{ (string)($kategoriPenyebabId ?? '') === (string)$penyebab->id ? 'selected' : '' }}>{{ $penyebab->nama_penyebab }}</option>
                        @endforeach
                    </select>
                    <button type="submit" id="btnFilterKebakaran" class="btn btn-danger btn-sm px-3">
                        <i class="las la-search fs-7 me-1"></i>Filter
                    </button>
                    <button type="reset" class="btn btn-light btn-sm px-2">
                        <i class="las la-times fs-7"></i>
                    </button>
                </form>
            </div>
        </div>

        <div class="card-body px-5 py-4">
            {{-- Baris A: Tren Kebakaran Bulanan + Distribusi Objek --}}
            <div class="row g-4 mb-4">
                <div class="col-xl-8">
                    <div class="card card-flush shadow-xs h-100 border border-gray-200">
                        <div class="card-header border-0 py-3 px-4" style="min-height: auto;">
                            <div class="card-title flex-column">
                                <span class="card-label fw-bolder fs-6 mb-0">Tren Kebakaran Bulanan</span>
                                <span class="text-muted fw-semibold fs-8">Jumlah insiden kebakaran per bulan</span>
                            </div>
                        </div>
                        <div class="card-body pt-1 pb-4 px-4">
                            <canvas id="chartTrenBulanan" style="width:100%; height:260px;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="card card-flush shadow-xs h-100 border border-gray-200">
                        <div class="card-header border-0 py-3 px-4" style="min-height: auto;">
                            <div class="card-title flex-column">
                                <span class="card-label fw-bolder fs-6 mb-0">Objek Kebakaran</span>
                                <span class="text-muted fw-semibold fs-8">Distribusi jenis objek terbakar</span>
                            </div>
                        </div>
                        <div class="card-body pt-1 pb-4 px-4">
                            <div class="d-flex justify-content-center">
                                <canvas id="chartObjek" style="max-height:190px;"></canvas>
                            </div>
                            <div class="separator separator-dashed my-3"></div>
                            <table class="table table-row-dashed table-row-gray-200 gy-1 gs-0 mb-0 fs-8">
                                <thead>
                                    <tr class="fw-bolder text-muted text-uppercase">
                                        <th>Objek</th>
                                        <th class="text-end">Jml</th>
                                        <th class="text-end">%</th>
                                    </tr>
                                </thead>
                                <tbody id="tabelObjekBody">
                                    <tr><td colspan="3" class="text-center text-muted py-2">Memuat data...</td></tr>
                                </tbody>
                                <tfoot id="tabelObjekFoot"></tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Baris B: Sebaran Kecamatan + Dugaan Penyebab --}}
            <div class="row g-4">
                <div class="col-xl-6">
                    <div class="card card-flush shadow-xs h-100 border border-gray-200">
                        <div class="card-header border-0 py-3 px-4" style="min-height: auto;">
                            <div class="card-title flex-column">
                                <span class="card-label fw-bolder fs-6 mb-0">Sebaran Kebakaran per Kecamatan</span>
                                <span class="text-muted fw-semibold fs-8">Jumlah kejadian per wilayah</span>
                            </div>
                        </div>
                        <div class="card-body pt-1 pb-4 px-4">
                            <canvas id="chartKecamatan" style="width:100%; height:250px;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="card card-flush shadow-xs h-100 border border-gray-200">
                        <div class="card-header border-0 py-3 px-4" style="min-height: auto;">
                            <div class="card-title flex-column">
                                <span class="card-label fw-bolder fs-6 mb-0">Dugaan Penyebab</span>
                                <span class="text-muted fw-semibold fs-8">Distribusi dugaan penyebab kebakaran</span>
                            </div>
                        </div>
                        <div class="card-body pt-1 pb-4 px-4">
                            <div class="d-flex justify-content-center">
                                <canvas id="chartPenyebab" style="max-height:190px;"></canvas>
                            </div>
                            <div class="separator separator-dashed my-3"></div>
                            <table class="table table-row-dashed table-row-gray-200 gy-1 gs-0 mb-0 fs-8">
                                <thead>
                                    <tr class="fw-bolder text-muted text-uppercase">
                                        <th>Penyebab</th>
                                        <th class="text-end">Jml</th>
                                        <th class="text-end">%</th>
                                    </tr>
                                </thead>
                                <tbody id="tabelPenyebabBody">
                                    <tr><td colspan="3" class="text-center text-muted py-2">Memuat data...</td></tr>
                                </tbody>
                                <tfoot id="tabelPenyebabFoot"></tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- =====================================================================
         SEKSI 3: DATA OPERASI PENYELAMATAN (RESCUE)
         Filter inline di card-header, charts di card-body
    ====================================================================== --}}
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-header border-0 py-3 px-5 rounded-top" style="min-height: auto; background: linear-gradient(90deg, #f0f8ff 0%, #fff 100%); border-left: 4px solid #009ef7 !important;">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 w-100">
                {{-- Judul kiri --}}
                <div class="d-flex align-items-center gap-2">
                    <i class="ki-duotone ki-rescue fs-4 text-primary"><span class="path1"></span><span class="path2"></span></i>
                    <span class="fw-bolder fs-6 text-primary">Data Penyelamatan (Rescue)</span>
                    <span class="badge badge-light-primary fs-9 fw-bold">Non-Darurat</span>
                </div>
                {{-- Filter inline kanan --}}
                <form id="filterRescue" class="d-flex align-items-center gap-2 flex-wrap">
                    <select name="tahun" id="rescue_filter_tahun" class="form-select form-select-sm" style="width:80px;">
                        <option value="2026" {{ $tahun == 2026 ? 'selected' : '' }}>2026</option>
                        <option value="2025" {{ $tahun == 2025 ? 'selected' : '' }}>2025</option>
                        <option value="2024" {{ $tahun == 2024 ? 'selected' : '' }}>2024</option>
                    </select>
                    <select name="bulan" id="rescue_filter_bulan" class="form-select form-select-sm" style="width:110px;">
                        <option value="">Semua Bulan</option>
                        @foreach(['1'=>'Jan','2'=>'Feb','3'=>'Mar','4'=>'Apr','5'=>'Mei','6'=>'Jun','7'=>'Jul','8'=>'Ags','9'=>'Sep','10'=>'Okt','11'=>'Nov','12'=>'Des'] as $num => $namaBulan)
                            <option value="{{ $num }}" {{ (string)($bulan ?? '') === (string)$num ? 'selected' : '' }}>{{ $namaBulan }}</option>
                        @endforeach
                    </select>
                    <select name="kecamatan_id" id="rescue_filter_kecamatan" class="form-select form-select-sm" style="width:145px;">
                        <option value="">Semua Kecamatan</option>
                        @foreach($kecamatans as $kec)
                            <option value="{{ $kec->id }}" {{ (string)($kecamatanId ?? '') === (string)$kec->id ? 'selected' : '' }}>{{ $kec->nama_kecamatan }}</option>
                        @endforeach
                    </select>
                    <select name="kategori_objek_id" id="rescue_filter_kategori_objek" class="form-select form-select-sm" style="width:145px;">
                        <option value="">Semua Objek / Kasus</option>
                        @foreach($kategoriObjeks as $obj)
                            <option value="{{ $obj->id }}" {{ (string)($kategoriObjekId ?? '') === (string)$obj->id ? 'selected' : '' }}>{{ $obj->nama_kategori }}</option>
                        @endforeach
                    </select>
                    <button type="submit" id="btnFilterRescue" class="btn btn-primary btn-sm px-3">
                        <i class="las la-search fs-7 me-1"></i>Filter
                    </button>
                    <button type="reset" class="btn btn-light btn-sm px-2">
                        <i class="las la-times fs-7"></i>
                    </button>
                </form>
            </div>
        </div>

        <div class="card-body px-5 py-4">
            {{-- Baris A: Tren Rescue Bulanan + Distribusi Jenis Kasus --}}
            <div class="row g-4 mb-4">
                <div class="col-xl-8">
                    <div class="card card-flush shadow-xs h-100 border border-gray-200">
                        <div class="card-header border-0 py-3 px-4" style="min-height: auto;">
                            <div class="card-title flex-column">
                                <span class="card-label fw-bolder fs-6 mb-0">Tren Rescue Bulanan</span>
                                <span class="text-muted fw-semibold fs-8">Jumlah operasi penyelamatan per bulan</span>
                            </div>
                        </div>
                        <div class="card-body pt-1 pb-4 px-4">
                            <canvas id="chartTrenRescue" style="width:100%; height:260px;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="card card-flush shadow-xs h-100 border border-gray-200">
                        <div class="card-header border-0 py-3 px-4" style="min-height: auto;">
                            <div class="card-title flex-column">
                                <span class="card-label fw-bolder fs-6 mb-0">Jenis Kasus Rescue</span>
                                <span class="text-muted fw-semibold fs-8">Distribusi kategori penyelamatan</span>
                            </div>
                        </div>
                        <div class="card-body pt-1 pb-4 px-4">
                            <div class="d-flex justify-content-center">
                                <canvas id="chartJenisRescue" style="max-height:190px;"></canvas>
                            </div>
                            <div class="separator separator-dashed my-3"></div>
                            <table class="table table-row-dashed table-row-gray-200 gy-1 gs-0 mb-0 fs-8">
                                <thead>
                                    <tr class="fw-bolder text-muted text-uppercase">
                                        <th>Kategori Kasus</th>
                                        <th class="text-end">Jml</th>
                                        <th class="text-end">%</th>
                                    </tr>
                                </thead>
                                <tbody id="tabelRescueBody">
                                    <tr><td colspan="3" class="text-center text-muted py-2">Memuat data...</td></tr>
                                </tbody>
                                <tfoot id="tabelRescueFoot"></tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Baris B: Sebaran Rescue per Kecamatan (full width) --}}
            <div class="row g-4">
                <div class="col-12">
                    <div class="card card-flush shadow-xs border border-gray-200">
                        <div class="card-header border-0 py-3 px-4" style="min-height: auto;">
                            <div class="card-title flex-column">
                                <span class="card-label fw-bolder fs-6 mb-0">Sebaran Rescue per Kecamatan</span>
                                <span class="text-muted fw-semibold fs-8">Jumlah operasi penyelamatan per wilayah</span>
                            </div>
                        </div>
                        <div class="card-body pt-1 pb-4 px-4">
                            <canvas id="chartKecamatanRescue" style="width:100%; height:250px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {

    // ===================================================================
    // PALET WARNA: 13 warna unik & kontras â€” tidak ada yang mirip/ganda
    // ===================================================================
    const PALETTE = [
        '#F1416C', // Metronic Danger  â€” Merah muda
        '#009EF7', // Metronic Primary â€” Biru cerah
        '#50CD89', // Metronic Success â€” Hijau
        '#FFC700', // Metronic Warning â€” Kuning emas
        '#7239EA', // Metronic Purple  â€” Ungu
        '#FF6B35', // Oranye terang
        '#00B8A9', // Teal/Cyan
        '#D63031', // Merah tua
        '#0984E3', // Biru cobalt
        '#6C5CE7', // Indigo/Lavender
        '#00CEC9', // Hijau tosca
        '#E17055', // Salmon
        '#74B9FF', // Biru muda
    ];

    // Variabel instance chart untuk reload tanpa memory leak / duplicate canvas
    let chartTrenBulananInstance     = null;
    let chartObjekInstance           = null;
    let chartKecamatanInstance       = null;
    let chartPenyebabInstance        = null;
    let chartTrenRescueInstance      = null;
    let chartJenisRescueInstance     = null;
    let chartKecamatanRescueInstance = null;

    // Helper: Mendapatkan query string dari Form Filter berdasarkan ID
    function getQueryParams(formId) {
        const form = document.getElementById(formId);
        if (!form) return '';
        const formData = new FormData(form);
        const params = new URLSearchParams();
        for (const [key, value] of formData.entries()) {
            if (value !== '' && value !== null && value !== undefined) {
                params.append(key, value);
            }
        }
        return params.toString();
    }

    // Helper: Membuat baris tabel + baris "Total" di tfoot
    function isiTabelDenganTotal(tbodyId, tfootId, labels, values) {
        const tbody = document.getElementById(tbodyId);
        const tfoot = document.getElementById(tfootId);
        if (!tbody || !tfoot) return;
        const total = values.reduce((a, b) => a + b, 0);
        let html = '';
        labels.forEach((label, i) => {
            const persen = total > 0 ? ((values[i] / total) * 100).toFixed(1) : '0.0';
            const warna  = PALETTE[i % PALETTE.length];
            html += `
                <tr>
                    <td>
                        <span class="bullet bullet-dot me-2" style="background-color:${warna}; width:10px; height:10px; display:inline-block; border-radius:50%;"></span>
                        <span class="text-gray-700 fw-semibold fs-7">${label}</span>
                    </td>
                    <td class="text-end fw-bolder text-dark fs-7">${values[i].toLocaleString()}</td>
                    <td class="text-end fw-semibold text-muted fs-7">${persen}%</td>
                </tr>`;
        });
        tbody.innerHTML = html || '<tr><td colspan="3" class="text-center text-muted py-3">Tidak ada data</td></tr>';

        tfoot.innerHTML = `
            <tr class="border-top border-top-dashed">
                <td class="fw-bolder text-dark fs-7 pt-3">Total</td>
                <td class="text-end fw-bolder text-dark fs-7 pt-3">${total.toLocaleString()}</td>
                <td class="text-end fw-bolder text-primary fs-7 pt-3">${total > 0 ? '100%' : '0%'}</td>
            </tr>`;
    }

    // ===================================================================
    // 1. SEKSI 1: RINGKASAN KPI (updateKPI)
    // ===================================================================
    function updateKPI() {
        const query = getQueryParams('filterKeseluruhan');
        fetch(`/api/analytics/ringkasan-statistik?${query}`)
            .then(r => r.json())
            .then(data => {
                const elTotal = document.getElementById('kpiTotalSeluruh');
                if (elTotal) elTotal.innerText = Number(data.total_kejadian || 0).toLocaleString();

                const elKebakaran = document.getElementById('kpiTotalKebakaran');
                if (elKebakaran) elKebakaran.innerText = Number(data.total_kebakaran || 0).toLocaleString();

                const elKarhutla = document.getElementById('kpiTotalKarhutla');
                if (elKarhutla) elKarhutla.innerText = Number(data.total_karhutla || 0).toLocaleString();

                const elRescue = document.getElementById('kpiTotalRescue');
                if (elRescue) elRescue.innerText = Number(data.total_rescue || 0).toLocaleString();

                const elHotspot = document.getElementById('kpiKecamatanHotspot');
                if (elHotspot) elHotspot.innerText = data.kecamatan_hotspot || '-';
            })
            .catch(err => console.error('Error fetching ringkasan statistik:', err));
    }

    // ===================================================================
    // 2. SEKSI 2: DATA INSIDEN KEBAKARAN (updateChartKebakaran)
    // ===================================================================
    function updateChartKebakaran() {
        const query = getQueryParams('filterKebakaran');

        // a. Tren Kebakaran Bulanan (Line Chart)
        fetch(`/api/analytics/tren-kebakaran-bulanan?${query}`)
            .then(r => r.json())
            .then(data => {
                const ctx = document.getElementById('chartTrenBulanan');
                if (!ctx) return;
                if (chartTrenBulananInstance) {
                    chartTrenBulananInstance.destroy();
                }
                chartTrenBulananInstance = new Chart(ctx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'Jumlah Kebakaran',
                                data: data.datasets[0].data,
                                borderColor: '#F1416C',
                                backgroundColor: 'rgba(241,65,108,0.1)',
                                borderWidth: 2.5,
                                pointRadius: 4,
                                pointBackgroundColor: '#F1416C',
                                tension: 0.4,
                                fill: true
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'top' },
                            tooltip: { mode: 'index', intersect: false }
                        },
                        scales: {
                            y: { beginAtZero: true, grid: { color: '#F5F5F5' } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            })
            .catch(err => console.error('Error fetching tren kebakaran bulanan:', err));

        // b. Distribusi Objek Kebakaran (Pie Chart + Tabel)
        fetch(`/api/analytics/distribusi-objek-kebakaran?${query}`)
            .then(r => r.json())
            .then(data => {
                const labels = Array.isArray(data.labels) ? data.labels : [];
                const values = Array.isArray(data.datasets?.[0]?.data) ? data.datasets[0].data : [];
                const ctx = document.getElementById('chartObjek');
                if (!ctx) return;

                if (chartObjekInstance) {
                    chartObjekInstance.destroy();
                }
                chartObjekInstance = new Chart(ctx.getContext('2d'), {
                    type: 'pie',
                    data: {
                        labels,
                        datasets: [{
                            data: values,
                            backgroundColor: labels.map((_, i) => PALETTE[i % PALETTE.length]),
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: ctx => {
                                        const tot = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                        return ` ${ctx.label}: ${ctx.parsed} (${tot > 0 ? ((ctx.parsed/tot)*100).toFixed(1) : 0}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
                isiTabelDenganTotal('tabelObjekBody', 'tabelObjekFoot', labels, values);
            })
            .catch(err => console.error('Error fetching distribusi objek kebakaran:', err));

        // c. Sebaran Kebakaran per Kecamatan (Bar Chart)
        fetch(`/api/analytics/sebaran-per-kecamatan?${query}`)
            .then(r => r.json())
            .then(data => {
                const ctx = document.getElementById('chartKecamatan');
                if (!ctx) return;

                if (chartKecamatanInstance) {
                    chartKecamatanInstance.destroy();
                }
                chartKecamatanInstance = new Chart(ctx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Jumlah Kebakaran',
                            data: data.datasets[0].data,
                            backgroundColor: 'rgba(241,65,108,0.80)',
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { beginAtZero: true, grid: { color: '#F5F5F5' } },
                            y: { grid: { display: false }, ticks: { font: { size: 11 } } }
                        }
                    }
                });
            })
            .catch(err => console.error('Error fetching sebaran kebakaran per kecamatan:', err));

        // d. Dugaan Penyebab Kebakaran (Doughnut Chart + Tabel)
        fetch(`/api/analytics/distribusi-penyebab-kebakaran?${query}`)
            .then(r => r.json())
            .then(data => {
                const labels = Array.isArray(data.labels) ? data.labels : [];
                const values = Array.isArray(data.datasets?.[0]?.data) ? data.datasets[0].data : [];
                const ctx = document.getElementById('chartPenyebab');
                if (!ctx) return;

                if (chartPenyebabInstance) {
                    chartPenyebabInstance.destroy();
                }
                chartPenyebabInstance = new Chart(ctx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels,
                        datasets: [{
                            data: values,
                            backgroundColor: labels.map((_, i) => PALETTE[i % PALETTE.length]),
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        cutout: '62%',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: ctx => {
                                        const tot = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                        return ` ${ctx.label}: ${ctx.parsed} (${tot > 0 ? ((ctx.parsed/tot)*100).toFixed(1) : 0}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
                isiTabelDenganTotal('tabelPenyebabBody', 'tabelPenyebabFoot', labels, values);
            })
            .catch(err => console.error('Error fetching dugaan penyebab kebakaran:', err));
    }

    // ===================================================================
    // 3. SEKSI 3: DATA OPERASI PENYELAMATAN (updateChartRescue)
    // ===================================================================
    function updateChartRescue() {
        const query = getQueryParams('filterRescue');

        // a. Tren Rescue Bulanan (Line Chart)
        fetch(`/api/analytics/tren-rescue-bulanan?${query}`)
            .then(r => r.json())
            .then(data => {
                const ctx = document.getElementById('chartTrenRescue');
                if (!ctx) return;

                if (chartTrenRescueInstance) {
                    chartTrenRescueInstance.destroy();
                }
                chartTrenRescueInstance = new Chart(ctx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Jumlah Operasi Rescue',
                            data: data.datasets[0].data,
                            borderColor: '#009EF7',
                            backgroundColor: 'rgba(0,158,247,0.08)',
                            borderWidth: 2.5,
                            pointRadius: 4,
                            pointBackgroundColor: '#009EF7',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'top' },
                            tooltip: { mode: 'index', intersect: false }
                        },
                        scales: {
                            y: { beginAtZero: true, grid: { color: '#F5F5F5' } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            })
            .catch(err => console.error('Error fetching tren rescue bulanan:', err));

        // b. Distribusi Jenis Kasus Rescue (Pie Chart + Tabel)
        fetch(`/api/analytics/distribusi-jenis-rescue?${query}`)
            .then(r => r.json())
            .then(data => {
                const labels = Array.isArray(data.labels) ? data.labels : [];
                const values = Array.isArray(data.datasets?.[0]?.data) ? data.datasets[0].data : [];
                const ctx = document.getElementById('chartJenisRescue');
                if (!ctx) return;

                if (chartJenisRescueInstance) {
                    chartJenisRescueInstance.destroy();
                }
                chartJenisRescueInstance = new Chart(ctx.getContext('2d'), {
                    type: 'pie',
                    data: {
                        labels,
                        datasets: [{
                            data: values,
                            backgroundColor: labels.map((_, i) => PALETTE[(i + 4) % PALETTE.length]),
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: ctx => {
                                        const tot = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                        return ` ${ctx.label}: ${ctx.parsed} (${tot > 0 ? ((ctx.parsed/tot)*100).toFixed(1) : 0}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
                isiTabelDenganTotal('tabelRescueBody', 'tabelRescueFoot', labels, values);
            })
            .catch(err => console.error('Error fetching jenis rescue:', err));

        // c. Sebaran Rescue per Kecamatan (Bar Chart)
        fetch(`/api/analytics/sebaran-rescue-per-kecamatan?${query}`)
            .then(r => r.json())
            .then(data => {
                const ctx = document.getElementById('chartKecamatanRescue');
                if (!ctx) return;

                if (chartKecamatanRescueInstance) {
                    chartKecamatanRescueInstance.destroy();
                }
                chartKecamatanRescueInstance = new Chart(ctx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Jumlah Operasi Rescue',
                            data: data.datasets[0].data,
                            backgroundColor: 'rgba(0,158,247,0.80)',
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { beginAtZero: true, grid: { color: '#F5F5F5' } },
                            y: { grid: { display: false }, ticks: { font: { size: 11 } } }
                        }
                    }
                });
            })
            .catch(err => console.error('Error fetching sebaran rescue per kecamatan:', err));
    }

    // ===================================================================
    // 4. EVENT LISTENERS FORM FILTER
    // ===================================================================
    // Seksi 1: Ringkasan KPI
    const formKeseluruhan = document.getElementById('filterKeseluruhan');
    if (formKeseluruhan) {
        formKeseluruhan.addEventListener('submit', function(e) {
            e.preventDefault();
            updateKPI();
        });
        formKeseluruhan.addEventListener('reset', function() {
            setTimeout(updateKPI, 0);
        });
    }

    // Seksi 2: Data Insiden Kebakaran
    const formKebakaran = document.getElementById('filterKebakaran');
    if (formKebakaran) {
        formKebakaran.addEventListener('submit', function(e) {
            e.preventDefault();
            updateChartKebakaran();
        });
        formKebakaran.addEventListener('reset', function() {
            setTimeout(updateChartKebakaran, 0);
        });
    }

    // Seksi 3: Data Penyelamatan (Rescue)
    const formRescue = document.getElementById('filterRescue');
    if (formRescue) {
        formRescue.addEventListener('submit', function(e) {
            e.preventDefault();
            updateChartRescue();
        });
        formRescue.addEventListener('reset', function() {
            setTimeout(updateChartRescue, 0);
        });
    }

    // Inisialisasi awal saat halaman dibuka
    updateKPI();
    updateChartKebakaran();
    updateChartRescue();

    // ===================================================================
    // 5. LIVE ALERT POLLING (Real-Time 30 Detik)
    // ===================================================================
    function checkLiveAlert() {
        fetch('/api/analytics/live-alert')
            .then(res => res.json())
            .then(data => {
                const banner = document.getElementById('liveAlertBanner');
                const alertText = document.getElementById('liveAlertText');
                const alertTime = document.getElementById('liveAlertTime');

                if (!banner) return;

                if (data.is_active) {
                    if (alertText) {
                        alertText.innerText = `⚠️ INFO DARURAT: Sedang terjadi penanganan insiden ${data.jenis_layanan} di Kecamatan ${data.kecamatan}!`;
                    }
                    if (alertTime && data.waktu) {
                        alertTime.innerText = `Waktu: ${data.waktu}`;
                    }
                    banner.style.display = 'flex';
                    banner.classList.remove('d-none');
                    banner.classList.add('d-flex');
                } else {
                    banner.style.display = 'none';
                    banner.classList.remove('d-flex');
                    banner.classList.add('d-none');
                }
            })
            .catch(err => console.error('Error fetching live-alert:', err));
    }

    // Jalankan segera saat page load dan ulangi setiap 30 detik
    checkLiveAlert();
    setInterval(checkLiveAlert, 30000);
});
</script>
@endsection
