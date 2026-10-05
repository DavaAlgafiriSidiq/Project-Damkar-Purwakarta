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
    @stack('styles')
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
    @stack('scripts')
</body>
</html>