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
            <span class="badge badge-light-danger fw-bold fs-8 px-3 py-2 border-0 shadow-xs" id="liveAlertTime">
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
    <div class="card shadow-sm mb-6 border-0">
        {{-- Section Header dengan Filter Inline --}}
        <div class="card-header border-0 py-4 px-6 bg-white rounded-top border-bottom border-gray-200" style="min-height: auto;">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 w-100">
                {{-- Judul kiri --}}
                <div class="d-flex align-items-center gap-2">
                    <span class="bullet bullet-vertical h-20px bg-primary me-1"></span>
                    <i class="ki-duotone ki-element-11 fs-4 text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                    <span class="fw-bolder fs-6 text-gray-900">Ringkasan KPI</span>
                    <span class="badge badge-light-primary fs-8 fw-semibold ms-1">Seluruh Insiden</span>
                </div>
                {{-- Filter inline kanan dengan label deskriptif --}}
                <div class="d-flex flex-column align-items-start align-items-lg-end gap-1">
                    <span class="text-muted fw-bold fs-7 d-flex align-items-center gap-1">
                        <i class="ki-duotone ki-filter fs-7 text-gray-500"><span class="path1"></span><span class="path2"></span></i>
                        Saring Data Berdasarkan:
                    </span>
                    <form id="filterKeseluruhan" class="d-flex align-items-center gap-2 flex-wrap">
                        <div style="min-width: 105px;">
                            <select name="tahun" id="kpi_filter_tahun" class="form-select form-select-sm form-select-solid" data-control="select2" data-hide-search="true">
                                <option value="2026" data-icon="ki-duotone ki-calendar" {{ $tahun == 2026 ? 'selected' : '' }}>2026</option>
                                <option value="2025" data-icon="ki-duotone ki-calendar" {{ $tahun == 2025 ? 'selected' : '' }}>2025</option>
                                <option value="2024" data-icon="ki-duotone ki-calendar" {{ $tahun == 2024 ? 'selected' : '' }}>2024</option>
                            </select>
                        </div>
                        <div style="min-width: 135px;">
                            <select name="bulan" id="kpi_filter_bulan" class="form-select form-select-sm form-select-solid" data-control="select2" data-hide-search="true">
                                <option value="" data-icon="ki-duotone ki-calendar">-- Pilih Semua Bulan --</option>
                                @foreach(['1'=>'Jan','2'=>'Feb','3'=>'Mar','4'=>'Apr','5'=>'Mei','6'=>'Jun','7'=>'Jul','8'=>'Ags','9'=>'Sep','10'=>'Okt','11'=>'Nov','12'=>'Des'] as $num => $namaBulan)
                                    <option value="{{ $num }}" data-icon="ki-duotone ki-calendar" {{ (string)($bulan ?? '') === (string)$num ? 'selected' : '' }}>{{ $namaBulan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="min-width: 150px;">
                            <select name="zona_layanan" id="kpi_filter_zona_layanan" class="form-select form-select-sm form-select-solid" data-control="select2" data-hide-search="true">
                                <option value="" data-icon="ki-duotone ki-compass">-- Pilih Semua Zona --</option>
                                @foreach($zonaLayanans as $zona)
                                    <option value="{{ $zona }}" data-icon="ki-duotone ki-geolocation" {{ (string)($zonaLayanan ?? '') === $zona ? 'selected' : '' }}>{{ $zona }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="min-width: 180px;">
                            <select name="kecamatan_id" id="kpi_filter_kecamatan" class="form-select form-select-sm form-select-solid" data-control="select2">
                                <option value="" data-icon="ki-duotone ki-geolocation">-- Pilih Semua Kecamatan --</option>
                                @foreach($kecamatans as $kec)
                                    <option value="{{ $kec->id }}" data-zona="{{ $kec->zona_layanan }}" data-icon="ki-duotone ki-geolocation" {{ (string)($kecamatanId ?? '') === (string)$kec->id ? 'selected' : '' }}>{{ $kec->nama_kecamatan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" id="btnFilterKPI" class="btn btn-primary btn-sm px-3 shadow-none">
                            <i class="las la-search fs-6 me-1"></i>Filter
                        </button>
                        <button type="reset" class="btn btn-light btn-sm px-2 text-gray-600 shadow-none" title="Reset Filter">
                            <i class="las la-times fs-6"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- KPI Cards — Compact, Harmonious Pastel Theme & Proportional Grid --}}
        <div class="card-body p-6">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-5 g-4">
                {{-- Widget 1: Total Seluruh --}}
                <div class="col">
                    <div class="card card-flush bg-light-primary border border-gray-200 rounded-3 shadow-sm hover-elevate-up h-100">
                        <div class="card-body p-5 d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="fw-bold fs-7 text-gray-700 text-uppercase ls-1">Total Insiden</span>
                                <div class="symbol symbol-40px symbol-circle">
                                    <span class="symbol-label bg-white shadow-xs">
                                        <i class="ki-duotone ki-element-11 fs-3 text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                    </span>
                                </div>
                            </div>
                            <div>
                                <div class="d-flex align-items-baseline gap-1">
                                    <span class="fs-2hx fw-bolder text-gray-900 tracking-tight" id="kpiTotalSeluruh">{{ number_format($totalSeluruhKejadian ?? 0) }}</span>
                                    <span class="text-gray-500 fs-8 fw-semibold">kejadian</span>
                                </div>
                                <div class="text-muted fs-8 mt-1">Kebakaran + Rescue</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Widget 2: Total Kebakaran --}}
                <div class="col">
                    <div class="card card-flush bg-light-danger border border-gray-200 rounded-3 shadow-sm hover-elevate-up h-100">
                        <div class="card-body p-5 d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="fw-bold fs-7 text-danger text-uppercase ls-1">Kebakaran</span>
                                <div class="symbol symbol-40px symbol-circle">
                                    <span class="symbol-label bg-white shadow-xs">
                                        <i class="las la-fire fs-2 text-danger"></i>
                                    </span>
                                </div>
                            </div>
                            <div>
                                <div class="d-flex align-items-baseline gap-1">
                                    <span class="fs-2hx fw-bolder text-gray-900 tracking-tight" id="kpiTotalKebakaran">{{ number_format($totalKebakaran ?? 0) }}</span>
                                    <span class="text-gray-500 fs-8 fw-semibold">kejadian</span>
                                </div>
                                <div class="text-muted fs-8 mt-1">Insiden darurat api</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Widget 3: Total Karhutla --}}
                <div class="col">
                    <div class="card card-flush bg-light-warning border border-gray-200 rounded-3 shadow-sm hover-elevate-up h-100">
                        <div class="card-body p-5 d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="fw-bold fs-7 text-warning text-uppercase ls-1">Karhutla</span>
                                <div class="symbol symbol-40px symbol-circle">
                                    <span class="symbol-label bg-white shadow-xs">
                                        <i class="ki-duotone ki-tree fs-3 text-warning"><span class="path1"></span><span class="path2"></span></i>
                                    </span>
                                </div>
                            </div>
                            <div>
                                <div class="d-flex align-items-baseline gap-1">
                                    <span class="fs-2hx fw-bolder text-gray-900 tracking-tight" id="kpiTotalKarhutla">{{ number_format($totalKarhutla ?? 0) }}</span>
                                    <span class="text-gray-500 fs-8 fw-semibold">kejadian</span>
                                </div>
                                <div class="text-muted fs-8 mt-1">Kebakaran hutan &amp; lahan</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Widget 4: Total Rescue --}}
                <div class="col">
                    <div class="card card-flush border border-gray-200 rounded-3 shadow-sm hover-elevate-up h-100" style="background-color: #f8f5ff;">
                        <div class="card-body p-5 d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="fw-bold fs-7 text-uppercase ls-1" style="color: #7239ea;">Rescue</span>
                                <div class="symbol symbol-40px symbol-circle">
                                    <span class="symbol-label bg-white shadow-xs">
                                        <i class="ki-duotone ki-shield-tick fs-3" style="color: #7239ea;"><span class="path1"></span><span class="path2"></span></i>
                                    </span>
                                </div>
                            </div>
                            <div>
                                <div class="d-flex align-items-baseline gap-1">
                                    <span class="fs-2hx fw-bolder text-gray-900 tracking-tight" id="kpiTotalRescue">{{ number_format($totalRescue ?? 0) }}</span>
                                    <span class="text-gray-500 fs-8 fw-semibold">operasi</span>
                                </div>
                                <div class="text-muted fs-8 mt-1">Penyelamatan non-darurat</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Widget 5: Kecamatan Hotspot --}}
                <div class="col">
                    <div class="card card-flush bg-light-success border border-gray-200 rounded-3 shadow-sm hover-elevate-up h-100">
                        <div class="card-body p-5 d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="fw-bold fs-7 text-success text-uppercase ls-1">Hotspot Utama</span>
                                <div class="symbol symbol-40px symbol-circle">
                                    <span class="symbol-label bg-white shadow-xs">
                                        <i class="ki-duotone ki-geolocation fs-3 text-success"><span class="path1"></span><span class="path2"></span></i>
                                    </span>
                                </div>
                            </div>
                            <div>
                                <div class="d-flex align-items-baseline gap-1">
                                    <span class="fs-4 fw-bolder text-gray-900 lh-sm text-truncate d-block mw-100" id="kpiKecamatanHotspot">{{ $kecamatanTertinggi ? $kecamatanTertinggi->kecamatan : '-' }}</span>
                                </div>
                                <div class="text-muted fs-8 mt-1">Wilayah frekuensi tertinggi</div>
                            </div>
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
    <div class="card shadow-sm mb-6 border-0">
        <div class="card-header border-0 py-4 px-6 bg-white rounded-top border-bottom border-gray-200" style="min-height: auto; border-left: 4px solid #f1416c !important;">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 w-100">
                {{-- Judul kiri --}}
                <div class="d-flex align-items-center gap-2">
                    <span class="bullet bullet-vertical h-20px bg-danger me-1"></span>
                    <i class="las la-fire fs-4 text-danger"></i>
                    <span class="fw-bolder fs-6 text-gray-900">Data Insiden Kebakaran</span>
                    <span class="badge badge-light-danger fs-8 fw-semibold ms-1">Layanan Darurat</span>
                </div>
                {{-- Filter inline kanan dengan label deskriptif --}}
                <div class="d-flex flex-column align-items-start align-items-lg-end gap-1">
                    <span class="text-muted fw-bold fs-7 d-flex align-items-center gap-1">
                        <i class="ki-duotone ki-filter fs-7 text-gray-500"><span class="path1"></span><span class="path2"></span></i>
                        Saring Data Berdasarkan:
                    </span>
                    <form id="filterKebakaran" class="d-flex align-items-center gap-2 flex-wrap">
                        <div style="min-width: 105px;">
                            <select name="tahun" id="kebakaran_filter_tahun" class="form-select form-select-sm form-select-solid" data-control="select2" data-hide-search="true">
                                <option value="2026" data-icon="ki-duotone ki-calendar" {{ $tahun == 2026 ? 'selected' : '' }}>2026</option>
                                <option value="2025" data-icon="ki-duotone ki-calendar" {{ $tahun == 2025 ? 'selected' : '' }}>2025</option>
                                <option value="2024" data-icon="ki-duotone ki-calendar" {{ $tahun == 2024 ? 'selected' : '' }}>2024</option>
                            </select>
                        </div>
                        <div style="min-width: 135px;">
                            <select name="bulan" id="kebakaran_filter_bulan" class="form-select form-select-sm form-select-solid" data-control="select2" data-hide-search="true">
                                <option value="" data-icon="ki-duotone ki-calendar">-- Pilih Semua Bulan --</option>
                                @foreach(['1'=>'Jan','2'=>'Feb','3'=>'Mar','4'=>'Apr','5'=>'Mei','6'=>'Jun','7'=>'Jul','8'=>'Ags','9'=>'Sep','10'=>'Okt','11'=>'Nov','12'=>'Des'] as $num => $namaBulan)
                                    <option value="{{ $num }}" data-icon="ki-duotone ki-calendar" {{ (string)($bulan ?? '') === (string)$num ? 'selected' : '' }}>{{ $namaBulan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="min-width: 150px;">
                            <select name="zona_layanan" id="kebakaran_filter_zona_layanan" class="form-select form-select-sm form-select-solid" data-control="select2" data-hide-search="true">
                                <option value="" data-icon="ki-duotone ki-compass">-- Pilih Semua Zona --</option>
                                @foreach($zonaLayanans as $zona)
                                    <option value="{{ $zona }}" data-icon="ki-duotone ki-geolocation">{{ $zona }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="min-width: 180px;">
                            <select name="kecamatan_id" id="kebakaran_filter_kecamatan" class="form-select form-select-sm form-select-solid" data-control="select2">
                                <option value="" data-icon="ki-duotone ki-geolocation">-- Pilih Semua Kecamatan --</option>
                                @foreach($kecamatans as $kec)
                                    <option value="{{ $kec->id }}" data-zona="{{ $kec->zona_layanan }}" data-icon="ki-duotone ki-geolocation">{{ $kec->nama_kecamatan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="min-width: 250px; max-width: 350px; flex: 1 1 auto;">
                            <select name="kategori_objek_id" id="kebakaran_filter_kategori_objek" class="form-select form-select-sm form-select-solid" data-control="select2">
                                <option value="" data-icon="ki-duotone ki-fire">-- Pilih Semua Objek --</option>
                                @foreach($kategoriObjekKebakaran as $obj)
                                    <option value="{{ $obj->id }}"
                                        @if(str_contains(strtolower($obj->nama_kategori), 'tawon') || str_contains(strtolower($obj->nama_kategori), 'lebah')) data-icon="bi bi-bug"
                                        @elseif(str_contains(strtolower($obj->nama_kategori), 'ular') || str_contains(strtolower($obj->nama_kategori), 'kucing') || str_contains(strtolower($obj->nama_kategori), 'binatang') || str_contains(strtolower($obj->nama_kategori), 'hewan') || str_contains(strtolower($obj->nama_kategori), 'biawak') || str_contains(strtolower($obj->nama_kategori), 'monyet') || str_contains(strtolower($obj->nama_kategori), 'lutung') || str_contains(strtolower($obj->nama_kategori), 'ternak') || str_contains(strtolower($obj->nama_kategori), 'kandang')) data-icon="las la-paw"
                                        @elseif(str_contains(strtolower($obj->nama_kategori), 'pasar') || str_contains(strtolower($obj->nama_kategori), 'pertokoan') || str_contains(strtolower($obj->nama_kategori), 'toko') || str_contains(strtolower($obj->nama_kategori), 'kios')) data-icon="ki-duotone ki-shop"
                                        @elseif(str_contains(strtolower($obj->nama_kategori), 'sekolah') || str_contains(strtolower($obj->nama_kategori), 'kantor') || str_contains(strtolower($obj->nama_kategori), 'rs') || str_contains(strtolower($obj->nama_kategori), 'perkantoran')) data-icon="ki-duotone ki-bank"
                                        @elseif(str_contains(strtolower($obj->nama_kategori), 'rumah') || str_contains(strtolower($obj->nama_kategori), 'bangunan') || str_contains(strtolower($obj->nama_kategori), 'ruko') || str_contains(strtolower($obj->nama_kategori), 'gedung') || str_contains(strtolower($obj->nama_kategori), 'tinggal')) data-icon="ki-duotone ki-home-2"
                                        @elseif(str_contains(strtolower($obj->nama_kategori), 'listrik') || str_contains(strtolower($obj->nama_kategori), 'tower') || str_contains(strtolower($obj->nama_kategori), 'genset') || str_contains(strtolower($obj->nama_kategori), 'gardu')) data-icon="ki-duotone ki-electricity"
                                        @elseif(str_contains(strtolower($obj->nama_kategori), 'kendaraan') || str_contains(strtolower($obj->nama_kategori), 'mobil') || str_contains(strtolower($obj->nama_kategori), 'motor') || str_contains(strtolower($obj->nama_kategori), 'bus') || str_contains(strtolower($obj->nama_kategori), 'truk')) data-icon="ki-duotone ki-car"
                                        @elseif(str_contains(strtolower($obj->nama_kategori), 'lahan') || str_contains(strtolower($obj->nama_kategori), 'hutan') || str_contains(strtolower($obj->nama_kategori), 'alang') || str_contains(strtolower($obj->nama_kategori), 'kebun') || str_contains(strtolower($obj->nama_kategori), 'pohon') || str_contains(strtolower($obj->nama_kategori), 'perkebunan')) data-icon="ki-duotone ki-tree"
                                        @elseif(str_contains(strtolower($obj->nama_kategori), 'angin') || str_contains(strtolower($obj->nama_kategori), 'beliung')) data-icon="las la-wind"
                                        @elseif(str_contains(strtolower($obj->nama_kategori), 'pabrik') || str_contains(strtolower($obj->nama_kategori), 'gudang') || str_contains(strtolower($obj->nama_kategori), 'industri') || str_contains(strtolower($obj->nama_kategori), 'perindustrian')) data-icon="las la-industry"
                                        @elseif(str_contains(strtolower($obj->nama_kategori), 'sumur') || str_contains(strtolower($obj->nama_kategori), 'tenggelam') || str_contains(strtolower($obj->nama_kategori), 'tercebur') || str_contains(strtolower($obj->nama_kategori), 'air') || str_contains(strtolower($obj->nama_kategori), 'sungai')) data-icon="ki-duotone ki-drop"
                                        @elseif(str_contains(strtolower($obj->nama_kategori), 'orang') || str_contains(strtolower($obj->nama_kategori), 'hilang') || str_contains(strtolower($obj->nama_kategori), 'korban')) data-icon="ki-duotone ki-user"
                                        @elseif(str_contains(strtolower($obj->nama_kategori), 'cincin') || str_contains(strtolower($obj->nama_kategori), 'sakit') || str_contains(strtolower($obj->nama_kategori), 'luka') || str_contains(strtolower($obj->nama_kategori), 'medis') || str_contains(strtolower($obj->nama_kategori), 'kecelakaan')) data-icon="las la-band-aid"
                                        @elseif(str_contains(strtolower($obj->nama_kategori), 'api') || str_contains(strtolower($obj->nama_kategori), 'kebakaran')) data-icon="las la-fire"
                                        @elseif(str_contains(strtolower($obj->nama_kategori), 'lain')) data-icon="las la-ellipsis-h"
                                        @else data-icon="ki-duotone ki-abstract-26"
                                        @endif
                                        >{{ $obj->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="min-width: 250px; max-width: 340px; flex: 1 1 auto;">
                            <select name="kategori_penyebab_id" id="kebakaran_filter_kategori_penyebab" class="form-select form-select-sm form-select-solid" data-control="select2">
                                <option value="" data-icon="las la-fire">-- Pilih Semua Penyebab --</option>
                                @foreach($kategoriPenyebabs as $penyebab)
                                    <option value="{{ $penyebab->id }}"
                                        @if(str_contains(strtolower($penyebab->nama_penyebab), 'listrik') || str_contains(strtolower($penyebab->nama_penyebab), 'korslet') || str_contains(strtolower($penyebab->nama_penyebab), 'accu') || str_contains(strtolower($penyebab->nama_penyebab), 'baterai')) data-icon="ki-duotone ki-electricity"
                                        @elseif(str_contains(strtolower($penyebab->nama_penyebab), 'sampah')) data-icon="ki-duotone ki-trash"
                                        @elseif(str_contains(strtolower($penyebab->nama_penyebab), 'kompor') || str_contains(strtolower($penyebab->nama_penyebab), 'gas') || str_contains(strtolower($penyebab->nama_penyebab), 'hawu') || str_contains(strtolower($penyebab->nama_penyebab), 'tungku') || str_contains(strtolower($penyebab->nama_penyebab), 'lilin') || str_contains(strtolower($penyebab->nama_penyebab), 'korek') || str_contains(strtolower($penyebab->nama_penyebab), 'bakar') || str_contains(strtolower($penyebab->nama_penyebab), 'api')) data-icon="las la-fire"
                                        @elseif(str_contains(strtolower($penyebab->nama_penyebab), 'rokok')) data-icon="las la-smoking"
                                        @elseif(str_contains(strtolower($penyebab->nama_penyebab), 'petasan') || str_contains(strtolower($penyebab->nama_penyebab), 'bom') || str_contains(strtolower($penyebab->nama_penyebab), 'pengelasan') || str_contains(strtolower($penyebab->nama_penyebab), 'las')) data-icon="las la-bomb"
                                        @elseif(str_contains(strtolower($penyebab->nama_penyebab), 'tabrakan') || str_contains(strtolower($penyebab->nama_penyebab), 'kendaraan') || str_contains(strtolower($penyebab->nama_penyebab), 'rem')) data-icon="ki-duotone ki-car"
                                        @elseif(str_contains(strtolower($penyebab->nama_penyebab), 'belum') || str_contains(strtolower($penyebab->nama_penyebab), 'diketahui') || str_contains(strtolower($penyebab->nama_penyebab), 'selidik')) data-icon="las la-question-circle"
                                        @elseif(str_contains(strtolower($penyebab->nama_penyebab), 'lain')) data-icon="las la-ellipsis-h"
                                        @else data-icon="ki-duotone ki-abstract-26"
                                        @endif
                                        >{{ $penyebab->nama_penyebab }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" id="btnFilterKebakaran" class="btn btn-danger btn-sm px-3 shadow-none">
                            <i class="las la-search fs-6 me-1"></i>Filter
                        </button>
                        <button type="reset" class="btn btn-light btn-sm px-2 text-gray-600 shadow-none" title="Reset Filter">
                            <i class="las la-times fs-6"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="card-body px-6 py-5">
            {{-- Baris A: Tren Kebakaran Bulanan + Distribusi Objek --}}
            <div class="row g-5 mb-5">
                <div class="col-xl-8">
                    <div class="card card-flush shadow-none h-100 border border-gray-200 rounded-3">
                        <div class="card-header border-0 py-4 px-5" style="min-height: auto;">
                            <div class="card-title flex-column">
                                <span class="card-label fw-bolder fs-6 mb-0 text-gray-900">Tren Kebakaran Bulanan</span>
                                <span class="text-muted fw-normal fs-8">Jumlah insiden kebakaran per bulan</span>
                            </div>
                        </div>
                        <div class="card-body pt-1 pb-4 px-5">
                            <canvas id="chartTrenBulanan" style="width:100%; height:260px;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="card card-flush shadow-none h-100 border border-gray-200 rounded-3">
                        <div class="card-header border-0 py-4 px-5" style="min-height: auto;">
                            <div class="card-title flex-column">
                                <span class="card-label fw-bolder fs-6 mb-0 text-gray-900">Objek Kebakaran</span>
                                <span class="text-muted fw-normal fs-8">Distribusi jenis objek terbakar</span>
                            </div>
                        </div>
                        <div class="card-body pt-1 pb-4 px-5">
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
            <div class="row g-5">
                <div class="col-xl-6">
                    <div class="card card-flush shadow-none h-100 border border-gray-200 rounded-3">
                        <div class="card-header border-0 py-4 px-5" style="min-height: auto;">
                            <div class="card-title flex-column">
                                <span class="card-label fw-bolder fs-6 mb-0 text-gray-900">Sebaran Kebakaran per Kecamatan</span>
                                <span class="text-muted fw-normal fs-8">Jumlah kejadian per wilayah</span>
                            </div>
                        </div>
                        <div class="card-body pt-1 pb-4 px-5">
                            <canvas id="chartKecamatan" style="width:100%; height:250px;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="card card-flush shadow-none h-100 border border-gray-200 rounded-3">
                        <div class="card-header border-0 py-4 px-5" style="min-height: auto;">
                            <div class="card-title flex-column">
                                <span class="card-label fw-bolder fs-6 mb-0 text-gray-900">Dugaan Penyebab</span>
                                <span class="text-muted fw-normal fs-8">Distribusi dugaan penyebab kebakaran</span>
                            </div>
                        </div>
                        <div class="card-body pt-1 pb-4 px-5">
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
    <div class="card shadow-sm mb-6 border-0">
        <div class="card-header border-0 py-4 px-6 bg-white rounded-top border-bottom border-gray-200" style="min-height: auto; border-left: 4px solid #7239ea !important;">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 w-100">
                {{-- Judul kiri --}}
                <div class="d-flex align-items-center gap-2">
                    <span class="bullet bullet-vertical h-20px me-1" style="background-color: #7239ea !important;"></span>
                    <i class="ki-duotone ki-shield-tick fs-4" style="color: #7239ea !important;"><span class="path1"></span><span class="path2"></span></i>
                    <span class="fw-bolder fs-6 text-gray-900">Data Penyelamatan (Rescue)</span>
                    <span class="badge fs-8 fw-semibold ms-1" style="background-color: #f8f5ff; color: #7239ea; border: 1px solid rgba(114, 57, 234, 0.25);">Non-Darurat</span>
                </div>
                {{-- Filter inline kanan dengan label deskriptif --}}
                <div class="d-flex flex-column align-items-start align-items-lg-end gap-1">
                    <span class="text-muted fw-bold fs-7 d-flex align-items-center gap-1">
                        <i class="ki-duotone ki-filter fs-7 text-gray-500"><span class="path1"></span><span class="path2"></span></i>
                        Saring Data Berdasarkan:
                    </span>
                    <form id="filterRescue" class="d-flex align-items-center gap-2 flex-wrap">
                        <div style="min-width: 105px;">
                            <select name="tahun" id="rescue_filter_tahun" class="form-select form-select-sm form-select-solid" data-control="select2" data-hide-search="true">
                                <option value="2026" data-icon="ki-duotone ki-calendar" {{ $tahun == 2026 ? 'selected' : '' }}>2026</option>
                                <option value="2025" data-icon="ki-duotone ki-calendar" {{ $tahun == 2025 ? 'selected' : '' }}>2025</option>
                                <option value="2024" data-icon="ki-duotone ki-calendar" {{ $tahun == 2024 ? 'selected' : '' }}>2024</option>
                            </select>
                        </div>
                        <div style="min-width: 135px;">
                            <select name="bulan" id="rescue_filter_bulan" class="form-select form-select-sm form-select-solid" data-control="select2" data-hide-search="true">
                                <option value="" data-icon="ki-duotone ki-calendar">-- Pilih Semua Bulan --</option>
                                @foreach(['1'=>'Jan','2'=>'Feb','3'=>'Mar','4'=>'Apr','5'=>'Mei','6'=>'Jun','7'=>'Jul','8'=>'Ags','9'=>'Sep','10'=>'Okt','11'=>'Nov','12'=>'Des'] as $num => $namaBulan)
                                    <option value="{{ $num }}" data-icon="ki-duotone ki-calendar" {{ (string)($bulan ?? '') === (string)$num ? 'selected' : '' }}>{{ $namaBulan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="min-width: 150px;">
                            <select name="zona_layanan" id="rescue_filter_zona_layanan" class="form-select form-select-sm form-select-solid" data-control="select2" data-hide-search="true">
                                <option value="" data-icon="ki-duotone ki-compass">-- Pilih Semua Zona --</option>
                                @foreach($zonaLayanans as $zona)
                                    <option value="{{ $zona }}" data-icon="ki-duotone ki-geolocation">{{ $zona }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="min-width: 180px;">
                            <select name="kecamatan_id" id="rescue_filter_kecamatan" class="form-select form-select-sm form-select-solid" data-control="select2">
                                <option value="" data-icon="ki-duotone ki-geolocation">-- Pilih Semua Kecamatan --</option>
                                @foreach($kecamatans as $kec)
                                    <option value="{{ $kec->id }}" data-zona="{{ $kec->zona_layanan }}" data-icon="ki-duotone ki-geolocation">{{ $kec->nama_kecamatan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="min-width: 265px; max-width: 380px; flex: 1 1 auto;">
                            <select name="kategori_objek_id" id="rescue_filter_kategori_objek" class="form-select form-select-sm form-select-solid" data-control="select2">
                                <option value="" data-icon="las la-life-ring">-- Pilih Semua Objek --</option>
                                @foreach($kategoriObjekRescue as $obj)
                                    <option value="{{ $obj->id }}"
                                        @if(str_contains(strtolower($obj->nama_kategori), 'tawon') || str_contains(strtolower($obj->nama_kategori), 'lebah')) data-icon="bi bi-bug"
                                        @elseif(str_contains(strtolower($obj->nama_kategori), 'ular') || str_contains(strtolower($obj->nama_kategori), 'kucing') || str_contains(strtolower($obj->nama_kategori), 'binatang') || str_contains(strtolower($obj->nama_kategori), 'hewan') || str_contains(strtolower($obj->nama_kategori), 'biawak') || str_contains(strtolower($obj->nama_kategori), 'monyet') || str_contains(strtolower($obj->nama_kategori), 'lutung') || str_contains(strtolower($obj->nama_kategori), 'ternak') || str_contains(strtolower($obj->nama_kategori), 'kandang')) data-icon="las la-paw"
                                        @elseif(str_contains(strtolower($obj->nama_kategori), 'pasar') || str_contains(strtolower($obj->nama_kategori), 'pertokoan') || str_contains(strtolower($obj->nama_kategori), 'toko') || str_contains(strtolower($obj->nama_kategori), 'kios')) data-icon="ki-duotone ki-shop"
                                        @elseif(str_contains(strtolower($obj->nama_kategori), 'sekolah') || str_contains(strtolower($obj->nama_kategori), 'kantor') || str_contains(strtolower($obj->nama_kategori), 'rs') || str_contains(strtolower($obj->nama_kategori), 'perkantoran')) data-icon="ki-duotone ki-bank"
                                        @elseif(str_contains(strtolower($obj->nama_kategori), 'rumah') || str_contains(strtolower($obj->nama_kategori), 'bangunan') || str_contains(strtolower($obj->nama_kategori), 'ruko') || str_contains(strtolower($obj->nama_kategori), 'gedung') || str_contains(strtolower($obj->nama_kategori), 'tinggal')) data-icon="ki-duotone ki-home-2"
                                        @elseif(str_contains(strtolower($obj->nama_kategori), 'listrik') || str_contains(strtolower($obj->nama_kategori), 'tower') || str_contains(strtolower($obj->nama_kategori), 'genset') || str_contains(strtolower($obj->nama_kategori), 'gardu')) data-icon="ki-duotone ki-electricity"
                                        @elseif(str_contains(strtolower($obj->nama_kategori), 'kendaraan') || str_contains(strtolower($obj->nama_kategori), 'mobil') || str_contains(strtolower($obj->nama_kategori), 'motor') || str_contains(strtolower($obj->nama_kategori), 'bus') || str_contains(strtolower($obj->nama_kategori), 'truk')) data-icon="ki-duotone ki-car"
                                        @elseif(str_contains(strtolower($obj->nama_kategori), 'lahan') || str_contains(strtolower($obj->nama_kategori), 'hutan') || str_contains(strtolower($obj->nama_kategori), 'alang') || str_contains(strtolower($obj->nama_kategori), 'kebun') || str_contains(strtolower($obj->nama_kategori), 'pohon') || str_contains(strtolower($obj->nama_kategori), 'perkebunan')) data-icon="ki-duotone ki-tree"
                                        @elseif(str_contains(strtolower($obj->nama_kategori), 'angin') || str_contains(strtolower($obj->nama_kategori), 'beliung')) data-icon="las la-wind"
                                        @elseif(str_contains(strtolower($obj->nama_kategori), 'pabrik') || str_contains(strtolower($obj->nama_kategori), 'gudang') || str_contains(strtolower($obj->nama_kategori), 'industri') || str_contains(strtolower($obj->nama_kategori), 'perindustrian')) data-icon="las la-industry"
                                        @elseif(str_contains(strtolower($obj->nama_kategori), 'sumur') || str_contains(strtolower($obj->nama_kategori), 'tenggelam') || str_contains(strtolower($obj->nama_kategori), 'tercebur') || str_contains(strtolower($obj->nama_kategori), 'air') || str_contains(strtolower($obj->nama_kategori), 'sungai')) data-icon="ki-duotone ki-drop"
                                        @elseif(str_contains(strtolower($obj->nama_kategori), 'orang') || str_contains(strtolower($obj->nama_kategori), 'hilang') || str_contains(strtolower($obj->nama_kategori), 'korban')) data-icon="ki-duotone ki-user"
                                        @elseif(str_contains(strtolower($obj->nama_kategori), 'cincin') || str_contains(strtolower($obj->nama_kategori), 'sakit') || str_contains(strtolower($obj->nama_kategori), 'luka') || str_contains(strtolower($obj->nama_kategori), 'medis') || str_contains(strtolower($obj->nama_kategori), 'kecelakaan')) data-icon="las la-band-aid"
                                        @elseif(str_contains(strtolower($obj->nama_kategori), 'api') || str_contains(strtolower($obj->nama_kategori), 'kebakaran')) data-icon="las la-fire"
                                        @elseif(str_contains(strtolower($obj->nama_kategori), 'lain')) data-icon="las la-ellipsis-h"
                                        @else data-icon="ki-duotone ki-abstract-26"
                                        @endif
                                        >{{ $obj->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" id="btnFilterRescue" class="btn btn-sm px-3 shadow-none text-white" style="background-color: #7239ea;">
                            <i class="las la-search fs-6 me-1 text-white"></i>Filter
                        </button>
                        <button type="reset" class="btn btn-light btn-sm px-2 text-gray-600 shadow-none" title="Reset Filter">
                            <i class="las la-times fs-6"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="card-body px-6 py-5">
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
    // PALET WARNA: 13 warna unik & kontras
    // ===================================================================
    const PALETTE = [
        '#F1416C', // Metronic Danger  — Merah muda
        '#009EF7', // Metronic Primary — Biru cerah
        '#50CD89', // Metronic Success — Hijau
        '#FFC700', // Metronic Warning — Kuning emas
        '#7239EA', // Metronic Purple  — Ungu
        '#FF6B35', // Oranye terang
        '#00B8A9', // Teal/Cyan
        '#D63031', // Merah tua
        '#0984E3', // Biru cobalt
        '#6C5CE7', // Indigo/Lavender
        '#00CEC9', // Hijau tosca
        '#E17055', // Salmon
        '#74B9FF', // Biru muda
    ];

    // Palet khusus modul Penyelamatan (Rescue) diawali Ungu (#7239EA)
    const PALETTE_RESCUE = [
        '#7239EA', // Metronic Purple - Rescue Utama
        '#009EF7', // Biru cerah
        '#50CD89', // Hijau
        '#FFC700', // Kuning
        '#FF6B35', // Oranye
        '#00B8A9', // Teal
        '#6C5CE7', // Lavender
        '#F1416C', // Merah muda
        '#00CEC9', // Tosca
        '#0984E3', // Biru cobalt
        '#E17055', // Salmon
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

    // Helper: Membuat baris tabel + baris "Total" di tfoot dengan warna yang identik dengan chart
    function isiTabelDenganTotal(tbodyId, tfootId, labels, values, colors) {
        const tbody = document.getElementById(tbodyId);
        const tfoot = document.getElementById(tfootId);
        if (!tbody || !tfoot) return;
        const total = values.reduce((a, b) => a + b, 0);
        let html = '';
        labels.forEach((label, i) => {
            const persen = total > 0 ? ((values[i] / total) * 100).toFixed(1) : '0.0';
            const warna  = (colors && colors[i]) ? colors[i] : PALETTE[i % PALETTE.length];
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
                const colors = labels.map((_, i) => PALETTE[i % PALETTE.length]);
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
                            backgroundColor: colors,
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
                isiTabelDenganTotal('tabelObjekBody', 'tabelObjekFoot', labels, values, colors);
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
                const colors = labels.map((_, i) => PALETTE[i % PALETTE.length]);
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
                            backgroundColor: colors,
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
                isiTabelDenganTotal('tabelPenyebabBody', 'tabelPenyebabFoot', labels, values, colors);
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
                            borderColor: '#7239EA',
                            backgroundColor: 'rgba(114,57,234,0.10)',
                            borderWidth: 2.5,
                            pointRadius: 4,
                            pointBackgroundColor: '#7239EA',
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
                const colors = labels.map((_, i) => PALETTE_RESCUE[i % PALETTE_RESCUE.length]);
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
                            backgroundColor: colors,
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
                isiTabelDenganTotal('tabelRescueBody', 'tabelRescueFoot', labels, values, colors);
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
                            backgroundColor: 'rgba(114,57,234,0.85)',
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

    // Helper: Dependent Dropdown (Zona Layanan -> Kecamatan)
    function setupDependentZonaKecamatan(zonaSelectId, kecamatanSelectId) {
        if (!window.$) return;
        const $zona = $('#' + zonaSelectId);
        const $kec = $('#' + kecamatanSelectId);
        if (!$zona.length || !$kec.length) return;

        // Ambil dan simpan seluruh opsi kecamatan awal
        const originalOptions = [];
        $kec.find('option').each(function() {
            originalOptions.push({
                value: $(this).val(),
                text: $(this).text(),
                zona: $(this).attr('data-zona') || '',
                icon: $(this).attr('data-icon') || ''
            });
        });

        function filterOptions(resetValue) {
            const selectedZona = $zona.val() ? $zona.val().trim() : '';
            const currentKecVal = $kec.val();

            $kec.empty();

            let hasCurrentVal = false;
            originalOptions.forEach(function(opt) {
                // Selalu sertakan opsi placeholder "Pilih Semua Kecamatan" (value kosong)
                // Jika belum ada zona terpilih (Semua Zona), sertakan semua kecamatan (termasuk Luar Kabupaten)
                // Jika zona tertentu dipilih, HANYA sertakan kecamatan dalam zona tersebut
                if (!opt.value || !selectedZona || opt.zona === selectedZona) {
                    const $newOpt = $('<option>', {
                        value: opt.value,
                        text: opt.text
                    });
                    if (opt.icon) {
                        $newOpt.attr('data-icon', opt.icon);
                    }
                    if (opt.zona) {
                        $newOpt.attr('data-zona', opt.zona);
                    }
                    if (opt.value === currentKecVal && currentKecVal !== '') {
                        hasCurrentVal = true;
                    }
                    $kec.append($newOpt);
                }
            });

            // Sesuai aturan: ketika pengguna memilih suatu zona (interaktif), dropdown kecamatan otomatis di-reset ke "" (Semua Kecamatan)
            if (resetValue || !hasCurrentVal) {
                $kec.val('').trigger('change');
            } else {
                $kec.val(currentKecVal).trigger('change');
            }
        }

        $zona.on('change', function() {
            filterOptions(true);
        });

        // Jalankan saat inisialisasi jika zona sudah terisi dari query string
        if ($zona.val()) {
            filterOptions(false);
        }
    }

    // Inisialisasi dependent dropdown Zona -> Kecamatan
    setupDependentZonaKecamatan('kpi_filter_zona_layanan', 'kpi_filter_kecamatan');
    setupDependentZonaKecamatan('kebakaran_filter_zona_layanan', 'kebakaran_filter_kecamatan');
    setupDependentZonaKecamatan('rescue_filter_zona_layanan', 'rescue_filter_kecamatan');

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
            setTimeout(function() {
                if (window.$) {
                    $(formKeseluruhan).find('select').each(function() {
                        $(this).val('').trigger('change');
                    });
                }
                updateKPI();
            }, 50);
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
            setTimeout(function() {
                if (window.$) {
                    $(formKebakaran).find('select').each(function() {
                        $(this).val('').trigger('change');
                    });
                }
                updateChartKebakaran();
            }, 50);
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
            setTimeout(function() {
                if (window.$) {
                    $(formRescue).find('select').each(function() {
                        $(this).val('').trigger('change');
                    });
                }
                updateChartRescue();
            }, 50);
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
