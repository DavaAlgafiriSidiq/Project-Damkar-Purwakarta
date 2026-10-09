<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi & Analitik Kebakaran - Dinas Pemadam Kebakaran Purwakarta</title>
    <!-- CSS Bawaan Metronic -->
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <!-- KeenIcons 2.0 (Duotone Icon Set) - wajib dimuat setelah style.bundle.css -->
    <link href="{{ asset('assets/css/keenicons.bundle.css') }}" rel="stylesheet" type="text/css" />

    {{-- Modern Select2 & Corporate Theme Custom Styling --}}
    <style>
    .select2-container .select2-selection--single {
        height: calc(1.5em + 1.1rem + 2px) !important;
        display: flex !important;
        align-items: center !important;
        background-color: #F5F8FA !important;
        border: 1px solid #E4E6EF !important;
        border-radius: 0.475rem !important;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out !important;
    }
    .select2-container--bootstrap5 .select2-selection--single:focus,
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #009EF7 !important;
        background-color: #FFFFFF !important;
        box-shadow: 0 0 0 0.2rem rgba(0, 158, 247, 0.12) !important;
    }
    .select2-container--bootstrap5 .select2-selection--single .select2-selection__rendered,
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #3F4254 !important;
        font-weight: 600 !important;
        font-size: 0.825rem !important;
        padding-left: 0.65rem !important;
        padding-right: 1.75rem !important;
        line-height: normal !important;
        display: flex !important;
        align-items: center !important;
    }
    .select2-container .select2-selection--single .select2-selection__arrow {
        height: 100% !important;
        top: 0 !important;
        right: 8px !important;
    }
    .select2-dropdown {
        border: 1px solid #EFF2F5 !important;
        box-shadow: 0 8px 24px 0 rgba(0, 0, 0, 0.12) !important;
        border-radius: 0.475rem !important;
        font-size: 0.825rem !important;
        z-index: 1055 !important;
        background-color: #FFFFFF !important;
        min-width: 100% !important;
        width: max-content !important;
        max-width: min(520px, 92vw) !important;
    }
    .select2-results__option {
        padding: 8px 12px !important;
        font-weight: 500 !important;
        white-space: nowrap !important;
        overflow: visible !important;
        text-overflow: clip !important;
        order: 2 !important;
    }
    .select2-results__option .select2-option-label {
        white-space: nowrap !important;
        overflow: visible !important;
        text-overflow: clip !important;
    }
    .select2-results__options {
        display: flex !important;
        flex-direction: column !important;
        max-height: 280px !important;
        overflow-y: auto !important;
    }

    /* Select2 Normal Selectable Options Styling (Solid & Prominent) */
    .select2-results__option:not([aria-disabled="true"]):not(.is-disabled) {
        opacity: 1 !important;
        color: #252F4A !important;
        cursor: pointer !important;
    }
    .select2-results__option:not([aria-disabled="true"]):not(.is-disabled) i,
    .select2-results__option:not([aria-disabled="true"]):not(.is-disabled) .ki-duotone,
    .select2-results__option:not([aria-disabled="true"]):not(.is-disabled) .las,
    .select2-results__option:not([aria-disabled="true"]):not(.is-disabled) .bi {
        opacity: 1 !important;
        color: #4B5675 !important;
    }
    .select2-results__option:not([aria-disabled="true"]):not(.is-disabled) .select2-option-label {
        opacity: 1 !important;
        color: #252F4A !important;
    }

    /* Hover & Highlighted State for Selectable Options (Primary Blue Indicator) */
    .select2-results__option--highlighted:not([aria-disabled="true"]):not(.is-disabled) {
        background-color: #F1FAFF !important;
        color: #009EF7 !important;
    }
    .select2-results__option--highlighted:not([aria-disabled="true"]):not(.is-disabled) i,
    .select2-results__option--highlighted:not([aria-disabled="true"]):not(.is-disabled) .ki-duotone,
    .select2-results__option--highlighted:not([aria-disabled="true"]):not(.is-disabled) .las,
    .select2-results__option--highlighted:not([aria-disabled="true"]):not(.is-disabled) .bi,
    .select2-results__option--highlighted:not([aria-disabled="true"]):not(.is-disabled) .select2-option-label {
        color: #009EF7 !important;
        opacity: 1 !important;
    }
    .select2-results__option--highlighted:not([aria-disabled="true"]):not(.is-disabled) .bullet {
        background-color: #009EF7 !important;
    }

    /* Selected Active State */
    .select2-results__option[aria-selected="true"]:not([aria-disabled="true"]):not(.is-disabled) {
        background-color: #EBF8FF !important;
        color: #009EF7 !important;
        font-weight: 600 !important;
    }
    .select2-results__option[aria-selected="true"]:not([aria-disabled="true"]):not(.is-disabled) i,
    .select2-results__option[aria-selected="true"]:not([aria-disabled="true"]):not(.is-disabled) .ki-duotone,
    .select2-results__option[aria-selected="true"]:not([aria-disabled="true"]):not(.is-disabled) .las,
    .select2-results__option[aria-selected="true"]:not([aria-disabled="true"]):not(.is-disabled) .bi,
    .select2-results__option[aria-selected="true"]:not([aria-disabled="true"]):not(.is-disabled) .select2-option-label {
        color: #009EF7 !important;
        opacity: 1 !important;
    }

    /* Opsi pertama / Placeholder selalu di urutan teratas */
    .select2-results__option:first-child {
        order: 1 !important;
    }

    .select2-search--dropdown .select2-search__field {
        border: 1px solid #E4E6EF !important;
        border-radius: 0.35rem !important;
        padding: 6px 10px !important;
        font-size: 0.825rem !important;
    }
    .select2-search--dropdown .select2-search__field:focus {
        border-color: #009EF7 !important;
        outline: none !important;
    }
    /* Metronic OptGroup & Category Header Styling in Select2 */
    .select2-results__group {
        font-size: 0.75rem !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        color: #7E8299 !important;
        background-color: #F1F4F8 !important;
        padding: 6px 12px !important;
        font-weight: 700 !important;
        border-left: 3px solid #009EF7 !important;
        cursor: default !important;
        order: 99 !important;
    }
    /* Select2 Disabled Options Styling (Grey Area Indicator dipaksa ke bawah) */
    .select2-results__option[aria-disabled="true"],
    .select2-results__option.is-disabled {
        order: 99 !important;
        background-color: #F8F9FA !important;
        color: #A1A5B7 !important;
        cursor: not-allowed !important;
        font-weight: 500 !important;
        opacity: 0.55 !important;
        pointer-events: none !important;
        border-top: 1px solid #EFF2F5 !important;
        border-bottom: 1px solid #EFF2F5 !important;
        padding-top: 6px !important;
        padding-bottom: 6px !important;
    }
    .select2-results__option[aria-disabled="true"] i,
    .select2-results__option[aria-disabled="true"] .ki-duotone,
    .select2-results__option[aria-disabled="true"] .las,
    .select2-results__option[aria-disabled="true"] .bi {
        opacity: 0.35 !important;
        color: #A1A5B7 !important;
    }
    .select2-results__option[aria-disabled="true"] .select2-option-label {
        color: #A1A5B7 !important;
    }
    .select2-selection--single .select2-selection__rendered i,
    .select2-selection--single .select2-selection__rendered .ki-duotone,
    .select2-selection--single .select2-selection__rendered .las,
    .select2-selection--single .select2-selection__rendered .bi {
        opacity: 1 !important;
        color: #4B5675 !important;
    }
    select option:disabled {
        background-color: #F5F8FA !important;
        color: #A1A5B7 !important;
        font-weight: bold !important;
    }
    select optgroup {
        font-weight: bold !important;
        color: #5E6278 !important;
        background-color: #F1F4F8 !important;
    }
    .select2-container--disabled .select2-selection--single {
        background-color: #F5F8FA !important;
        border-color: #EFF2F5 !important;
        cursor: not-allowed !important;
        opacity: 0.7 !important;
    }
    .select2-container--disabled .select2-selection--single .select2-selection__rendered {
        color: #A1A5B7 !important;
    }
    </style>
</head>
<body id="kt_body" class="header-fixed header-tablet-and-mobile-fixed toolbar-enabled toolbar-fixed aside-enabled aside-fixed" style="--kt-toolbar-height:55px;--kt-toolbar-height-tablet-and-mobile:55px">
    
    <!-- Wrapper Utama -->
    <div class="d-flex flex-column flex-root">
        <div class="page d-flex flex-row flex-column-fluid">
            
            <!-- Sidebar / Aside Navigasi -->
            @include('layouts.partials._sidebar')

            <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
                
                <!-- Topbar Header -->
                <div id="kt_header" class="header align-items-stretch shadow-xs" style="background-color: #ffffff; border-bottom: 1px solid #EFF2F5;">
                    <div class="container-fluid d-flex align-items-stretch justify-content-between">
                        
                        <!-- Toggle Sidebar Mobile & Brand Title -->
                        <div class="d-flex align-items-center d-lg-none ms-n2 me-2" title="Show aside menu">
                            <div class="btn btn-icon btn-active-color-primary w-35px h-35px" id="kt_aside_mobile_toggle">
                                <i class="ki-duotone ki-abstract-14 fs-1"><span class="path1"></span><span class="path2"></span></i>
                            </div>
                        </div>

                        <!-- Header Left: Status Badge & App Title -->
                        <div class="d-flex align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge badge-light-danger fw-bold fs-8 px-3 py-2 d-none d-sm-inline-flex">
                                    <i class="ki-duotone ki-shield-tick fs-7 me-1 text-danger"><span class="path1"></span><span class="path2"></span></i>
                                    DAMKAR PURWAKARTA
                                </span>
                                <span class="text-gray-500 fs-7 d-none d-md-inline">Sistem Pencatatan & Analitik Terpadu</span>
                            </div>
                        </div>

                        <!-- Header Right: User Profile Dropdown / Login Button -->
                        <div class="d-flex align-items-center flex-shrink-0">
                            @auth
                                <!-- Metronic User Dropdown Menu -->
                                <div class="d-flex align-items-center ms-1 ms-lg-3" id="kt_header_user_menu_toggle">
                                    
                                    <!-- User Avatar Trigger Button -->
                                    <div class="d-flex align-items-center cursor-pointer symbol symbol-35px symbol-md-40px" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                                        <div class="symbol-label fs-5 fw-bolder {{ auth()->user()->role === 'admin' ? 'bg-light-danger text-danger' : 'bg-light-primary text-primary' }}">
                                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                        </div>
                                    </div>

                                    <!-- Dropdown Menu Content -->
                                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px shadow-lg" data-kt-menu="true">
                                        
                                        <!-- Header Dropdown: User Info -->
                                        <div class="menu-item px-3">
                                            <div class="menu-content d-flex align-items-center px-3">
                                                <!-- Avatar -->
                                                <div class="symbol symbol-45px me-4">
                                                    <div class="symbol-label fs-4 fw-bolder {{ auth()->user()->role === 'admin' ? 'bg-light-danger text-danger' : 'bg-light-primary text-primary' }}">
                                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                                    </div>
                                                </div>
                                                <!-- Name & Role Badge -->
                                                <div class="d-flex flex-column">
                                                    <div class="fw-bold d-flex align-items-center fs-6 text-dark">
                                                        {{ auth()->user()->name }}
                                                    </div>
                                                    <div class="mt-1">
                                                        @if(auth()->user()->role === 'admin')
                                                            <span class="badge badge-light-danger fw-bold fs-9 px-2 py-1">Administrator</span>
                                                        @else
                                                            <span class="badge badge-light-primary fw-bold fs-9 px-2 py-1">Petugas Lapangan</span>
                                                        @endif
                                                    </div>
                                                    <span class="text-muted fs-8 mt-1">{{ auth()->user()->email }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="separator my-2"></div>

                                        <!-- Quick Navigation Links -->
                                        <div class="menu-item px-5">
                                            <a href="{{ route('analytics.dashboard') }}" class="menu-link px-5">
                                                <i class="ki-duotone ki-chart-simple-2 fs-5 me-2 text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                                Dashboard Analitik
                                            </a>
                                        </div>

                                        @if(auth()->user()->role === 'admin')
                                            <div class="menu-item px-5">
                                                <a href="{{ route('admin.verifikasi.index') }}" class="menu-link px-5">
                                                    <i class="ki-duotone ki-shield-tick fs-5 me-2 text-success"><span class="path1"></span><span class="path2"></span></i>
                                                    Verifikasi Data
                                                </a>
                                            </div>
                                            <div class="menu-item px-5">
                                                <a href="{{ route('admin.laporan.matriks') }}" class="menu-link px-5">
                                                    <i class="ki-duotone ki-document fs-5 me-2 text-primary"><span class="path1"></span><span class="path2"></span></i>
                                                    Rekapitulasi Matriks
                                                </a>
                                            </div>
                                        @elseif(auth()->user()->role === 'petugas')
                                            <div class="menu-item px-5">
                                                <a href="{{ route('petugas.kejadian.create') }}" class="menu-link px-5">
                                                    <i class="ki-duotone ki-notepad-edit fs-5 me-2 text-primary"><span class="path1"></span><span class="path2"></span></i>
                                                    Input Kejadian Baru
                                                </a>
                                            </div>
                                            <div class="menu-item px-5">
                                                <a href="{{ route('petugas.kejadian.index') }}" class="menu-link px-5">
                                                    <i class="ki-duotone ki-document fs-5 me-2 text-info"><span class="path1"></span><span class="path2"></span></i>
                                                    Riwayat Laporan Saya
                                                </a>
                                            </div>
                                        @endif

                                        <div class="separator my-2"></div>

                                        <!-- Logout Action -->
                                        <div class="menu-item px-5">
                                            <form method="POST" action="{{ route('logout') }}" id="formLogoutHeader">
                                                @csrf
                                                <button type="submit" class="btn btn-link text-danger w-100 text-start menu-link px-5 py-2">
                                                    <i class="ki-duotone ki-entrance-left fs-5 me-2 text-danger"><span class="path1"></span><span class="path2"></span></i>
                                                    Keluar (Logout)
                                                </button>
                                            </form>
                                        </div>

                                    </div>
                                </div>
                            @endauth

                            @guest
                                <div class="d-flex align-items-center ms-1 ms-lg-3">
                                    <a href="{{ route('login') }}" class="btn btn-sm btn-primary d-flex align-items-center">
                                        <i class="ki-duotone ki-entrance-right fs-4 me-1"><span class="path1"></span><span class="path2"></span></i>
                                        <span>Masuk Sistem</span>
                                    </a>
                                </div>
                            @endguest
                        </div>

                    </div>
                </div>

                <!-- Konten Utama Halaman -->
                <div class="content d-flex flex-column flex-column-fluid py-6" id="kt_content">
                    @yield('content')
                </div>

            </div>
        </div>
    </div>

    <!-- JS Bawaan Metronic -->
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
    <script>
    // Global Select2 Custom Formatter with Dynamic Icons & Comprehensive Fallbacks
    function formatSelect2OptionWithIcon(item) {
        if (!item.text || item.text.trim() === '' || item.loading) {
            return item.text;
        }
        var $el = item.element ? $(item.element) : null;
        var icon = $el ? ($el.data('icon') || $el.attr('data-icon') || '') : '';
        var text = (item.text || '').toLowerCase().trim();

        // Opsi kategori induk yang dinonaktifkan (disabled / judul pemisah)
        var isDisabled = item.disabled || ($el && ($el.prop('disabled') || $el.is(':disabled')));
        if (isDisabled) {
            return $(`<span class="d-flex align-items-center w-100 text-muted fw-normal py-1 px-1" style="cursor: not-allowed; opacity: 0.6;"><span class="bullet bullet-dot bg-gray-400 me-2"></span><span class="select2-option-label text-muted fs-8">${item.text}</span></span>`);
        }

        // Jika opsi placeholder tanpa ID dan tidak memiliki icon khusus serta diawali 'pilih '
        if (!item.id && !icon && (text.indexOf('pilih ') === 0 || text === '')) {
            return item.text;
        }

        // Normalisasi dan mapping kompatibilitas icon
        if (icon) {
            if (icon.indexOf('calendar') !== -1) {
                icon = 'ki-duotone ki-calendar';
            } else if (icon.indexOf('ki-rescue') !== -1) {
                icon = 'las la-life-ring';
            } else if (icon.indexOf('la-burn') !== -1) {
                icon = 'las la-fire';
            } else if (icon.indexOf('ki-bug') !== -1 || icon.indexOf('fa-bug') !== -1) {
                icon = 'bi bi-bug';
            } else if (icon.indexOf('ki-factory') !== -1 || icon.indexOf('fa-industry') !== -1) {
                icon = 'las la-industry';
            } else if (icon.indexOf('fa-cow') !== -1 || icon.indexOf('ki-cow') !== -1) {
                icon = 'las la-paw';
            } else if (icon.indexOf('ki-bandage') !== -1) {
                icon = 'las la-band-aid';
            } else if (icon.indexOf('ki-home') !== -1 && icon.indexOf('ki-home-2') === -1) {
                icon = 'ki-duotone ki-home-2';
            } else if (icon.indexOf('ki-outline') !== -1) {
                icon = icon.replace('ki-outline', 'ki-duotone');
            }
        }

        // Fallback cerdas jika data-icon tidak ditentukan eksplisit di elemen
        if (!icon) {
            var $parentSelect = $el ? $el.closest('select') : null;
            var selectName = ($parentSelect ? ($parentSelect.attr('name') || '') : '').toLowerCase();
            var selectId = ($parentSelect ? ($parentSelect.attr('id') || '') : '').toLowerCase();

            // 1. Deteksi Waktu (Tahun & Bulan) -> Ikon Kalender
            var isYear = /^(19|20)\d{2}$/.test(text) || text.indexOf('tahun') !== -1 || selectName === 'tahun' || selectId.indexOf('tahun') !== -1;
            var isMonth = /^(jan|feb|mar|apr|mei|jun|jul|ags|agu|sep|okt|oct|nov|des|dec)/i.test(text) || text.indexOf('bulan') !== -1 || selectName === 'bulan' || selectId.indexOf('bulan') !== -1;
            var isDate = text.indexOf('tanggal') !== -1 || text.indexOf('periode') !== -1 || selectName.indexOf('tanggal') !== -1;

            if (isYear || isMonth || isDate) {
                icon = 'ki-duotone ki-calendar';
            } else if (text.indexOf('tawon') !== -1 || text.indexOf('lebah') !== -1) {
                icon = 'bi bi-bug';
            } else if (text.indexOf('ular') !== -1 || text.indexOf('monyet') !== -1 || text.indexOf('biawak') !== -1 || text.indexOf('kucing') !== -1 || text.indexOf('binatang') !== -1 || text.indexOf('hewan') !== -1 || text.indexOf('ternak') !== -1 || text.indexOf('kandang') !== -1 || text.indexOf('sapi') !== -1 || text.indexOf('kambing') !== -1) {
                icon = 'las la-paw';
            } else if (text.indexOf('rumah') !== -1 || text.indexOf('tinggal') !== -1 || text.indexOf('ruko') !== -1 || text.indexOf('gedung') !== -1 || text.indexOf('bangunan') !== -1) {
                icon = 'ki-duotone ki-home-2';
            } else if (text.indexOf('pasar') !== -1 || text.indexOf('toko') !== -1 || text.indexOf('kios') !== -1) {
                icon = 'ki-duotone ki-shop';
            } else if (text.indexOf('sekolah') !== -1 || text.indexOf('kantor') !== -1 || text.indexOf('rs') !== -1 || text.indexOf('perkantoran') !== -1) {
                icon = 'ki-duotone ki-bank';
            } else if (text.indexOf('kendaraan') !== -1 || text.indexOf('mobil') !== -1 || text.indexOf('motor') !== -1 || text.indexOf('bus') !== -1 || text.indexOf('truk') !== -1 || text.indexOf('tabrakan') !== -1) {
                icon = 'ki-duotone ki-car';
            } else if (text.indexOf('kebun') !== -1 || text.indexOf('hutan') !== -1 || text.indexOf('lahan') !== -1 || text.indexOf('pohon') !== -1 || text.indexOf('perkebunan') !== -1) {
                icon = 'ki-duotone ki-tree';
            } else if (text.indexOf('angin') !== -1 || text.indexOf('beliung') !== -1) {
                icon = 'las la-wind';
            } else if (text.indexOf('sumur') !== -1 || text.indexOf('tenggelam') !== -1 || text.indexOf('sungai') !== -1 || text.indexOf('danau') !== -1 || text.indexOf('air') !== -1) {
                icon = 'ki-duotone ki-drop';
            } else if (text.indexOf('orang') !== -1 || text.indexOf('hilang') !== -1 || text.indexOf('korban') !== -1) {
                icon = 'ki-duotone ki-user';
            } else if (text.indexOf('cincin') !== -1 || text.indexOf('sakit') !== -1 || text.indexOf('luka') !== -1 || text.indexOf('medis') !== -1 || text.indexOf('kecelakaan') !== -1) {
                icon = 'las la-band-aid';
            } else if (text.indexOf('listrik') !== -1 || text.indexOf('tower') !== -1 || text.indexOf('genset') !== -1 || text.indexOf('korslet') !== -1 || text.indexOf('accu') !== -1 || text.indexOf('baterai') !== -1 || text.indexOf('gardu') !== -1) {
                icon = 'ki-duotone ki-electricity';
            } else if (text.indexOf('sampah') !== -1) {
                icon = 'ki-duotone ki-trash';
            } else if (text.indexOf('gas') !== -1 || text.indexOf('kompor') !== -1 || text.indexOf('lilin') !== -1 || text.indexOf('hawu') !== -1 || text.indexOf('tungku') !== -1 || text.indexOf('bakar') !== -1 || text.indexOf('api') !== -1 || text.indexOf('korek') !== -1) {
                icon = 'las la-fire';
            } else if (text.indexOf('rokok') !== -1) {
                icon = 'las la-smoking';
            } else if (text.indexOf('petasan') !== -1 || text.indexOf('bom') !== -1 || text.indexOf('pengelasan') !== -1) {
                icon = 'las la-bomb';
            } else if (text.indexOf('industri') !== -1 || text.indexOf('pabrik') !== -1 || text.indexOf('gudang') !== -1) {
                icon = 'las la-industry';
            } else if (text.indexOf('kecamatan') !== -1 || text.indexOf('wmk') !== -1 || text.indexOf('zona') !== -1) {
                icon = 'ki-duotone ki-geolocation';
            } else if (text.indexOf('objek') !== -1) {
                icon = (selectName.indexOf('rescue') !== -1 || selectId.indexOf('rescue') !== -1) ? 'las la-life-ring' : 'ki-duotone ki-fire';
            } else if (text.indexOf('penyebab') !== -1) {
                icon = 'las la-fire';
            } else if (text.indexOf('rescue') !== -1) {
                icon = 'las la-life-ring';
            } else if (text.indexOf('belum') !== -1 || text.indexOf('diketahui') !== -1) {
                icon = 'las la-question-circle';
            } else if (text.indexOf('lain') !== -1 || text.indexOf('etc') !== -1 || text.indexOf('dll') !== -1) {
                icon = 'las la-ellipsis-h';
            } else {
                icon = 'ki-duotone ki-abstract-26';
            }
        }

        var isDuotone = icon.indexOf('ki-duotone') !== -1;
        var paths = isDuotone ? '<span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span>' : '';
        var colorClass = icon.indexOf('text-') !== -1 ? '' : '';
        return $(`<span class="d-inline-flex align-items-center"><i class="${icon} ${colorClass} fs-5 me-2 flex-shrink-0">${paths}</i><span class="select2-option-label">${item.text}</span></span>`);
    }

    // Inisialisasi Select2 cerdas dengan dukungan ikon
    window.initEnhancedSelect2 = function(scope) {
        if (typeof $.fn.select2 === 'undefined') return;
        var container = scope || document;
        $(container).find('[data-control="select2"]').each(function() {
            var $select = $(this);
            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }
            var hideSearch = $select.data('hide-search') === true || $select.data('hide-search') === 'true';
            var placeholder = $select.data('placeholder');

            var opts = {
                dropdownAutoWidth: true,
                width: '100%',
                minimumResultsForSearch: hideSearch ? Infinity : 0,
                templateResult: formatSelect2OptionWithIcon,
                templateSelection: formatSelect2OptionWithIcon
            };

            // Hanya set placeholder jika data-placeholder ditentukan secara eksplisit
            if (placeholder && String(placeholder).trim() !== '') {
                opts.placeholder = placeholder;
            }

            $select.select2(opts);
        });
    };

    $(document).ready(function() {
        window.initEnhancedSelect2();
    });
    </script>
    @stack('scripts')
</body>
</html>