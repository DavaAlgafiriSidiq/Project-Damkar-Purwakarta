{{-- Penyempurnaan kecil khusus area logo aside.
     Metronic v8.0.37 sudah menyediakan rule minimize untuk `.logo-default` (disembunyikan) dan
     `.logo-minimize` (ditampilkan), TETAPI tidak menyediakan rule dasar yang menyembunyikan
     `.logo-minimize` saat aside dalam keadaan lebar. Tanpa rule di bawah ini kedua ikon panah
     (`<<` dan `>>`) akan tampil bersamaan.
     Specificity rule ini (0,3,0) lebih rendah daripada rule minimize bawaan (0,4,0 dan 0,5,0)
     sehingga tidak mengganggu state minimize/minimize+hover. --}}
<style>
    .aside .aside-logo .logo-minimize {
        display: none;
    }
</style>


<div id="kt_aside" class="aside aside-dark aside-hoverable" data-kt-drawer="true" data-kt-drawer-name="aside" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_aside_mobile_toggle">
    
    {{-- begin::Brand --}}
    {{-- Brand aside memakai pasangan class bawaan Metronic 8:
           - `.logo-default`  : konten yang HANYA tampil saat aside lebar.
             Bundle sudah menyediakan rule-nya:
             [data-kt-aside-minimize=on] .aside:not(:hover) .aside-logo .logo-default{display:none}
           - `.logo-minimize` : konten yang HANYA tampil saat aside diminimize.
         PENTING: jangan menggabungkan `.logo-default` dengan utilitas display Metronic
         (`d-flex`, `d-block`, dst.) pada elemen yang SAMA, karena utilitas tersebut memakai
         `!important` sehingga rule `display:none` dari bundle kalah dan konten tidak ikut
         tersembunyi. Karena itu wrapper `.logo-default` dibiarkan bersih, sedangkan layout
         flex diletakkan pada <a> di dalamnya. --}}
    <div class="aside-logo flex-column-auto" id="kt_aside_logo">
        {{-- begin::Logo (tampil saat lebar, tersembunyi saat minimize) --}}
        <div class="logo-default">
            <a href="{{ route('analytics.dashboard') }}" class="d-flex align-items-center text-decoration-none">
                <span class="symbol symbol-35px me-3">
                    <span class="symbol-label bg-danger bg-opacity-20">
                        {{-- KeenIcons 2.0 tidak memiliki glyph api, memakai Line Awesome (ter-bundle di plugins.bundle.css) --}}
                        <i class="las la-fire fs-2 text-danger"></i>
                    </span>
                </span>
                <span class="d-flex flex-column">
                    <span class="fs-5 fw-bolder text-white ls-1">DAMKAR PWK</span>
                    <span class="text-gray-500 fs-9 fw-semibold">Purwakarta Tanggap</span>
                </span>
            </a>
        </div>
        {{-- end::Logo --}}

        {{-- begin::Aside toggler --}}
        {{-- Tombol toggle bawaan Metronic (KTToggle ada di scripts.bundle.js) menulis
             data-kt-aside-minimize="on" ke <body>; body sudah ber-class aside-fixed aside-enabled
             header-fixed sehingga CSS minimize bawaan aktif (.aside{width:75px},
             .wrapper{padding-left:75px}, .header{left:75px}) dan `.aside-logo` otomatis menjadi
             `justify-content:center` sehingga ikon panah berdiri di tengah.

             Dua ikon di dalam tombol bertukar sendiri lewat pasangan class logo-default/logo-minimize:
               - aside lebar    -> `<<` (ki-double-left-arrow, logo-default)  : klik untuk menutup
               - aside minimize -> `>>` (ki-double-right-arrow, logo-minimize): klik untuk membuka
             Saat minimize lalu di-hover (aside-hoverable melebar ke 265px) bundle otomatis
             menampilkan kembali logo-default dan menyembunyikan logo-minimize, jadi panah `<<`
             selalu selaras dengan kondisi visual aside.

             Ikon WAJIB tetap berada DI DALAM #kt_aside_toggle (jangan dipindah ke dalam <a> brand)
             supaya klik tetap berfungsi sebagai toggle, bukan navigasi ke dashboard.

             Catatan KeenIcons: rule `.aside.aside-dark .aside-toggle` bawaan hanya mewarnai SVG
             (svg [fill]), sedangkan ikon di sini font yang mewarisi `color` -> text-gray-600,
             hover btn-active-color-primary. --}}
        <div id="kt_aside_toggle" class="btn btn-icon w-auto px-0 text-gray-600 btn-active-color-primary aside-toggle" data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body" data-kt-toggle-name="aside-minimize">
            <i class="ki-duotone ki-double-left-arrow fs-1 logo-default"><span class="path1"></span><span class="path2"></span></i>
            <i class="ki-duotone ki-double-right-arrow fs-1 logo-minimize"><span class="path1"></span><span class="path2"></span></i>
        </div>
        {{-- end::Aside toggler --}}
    </div>
    {{-- end::Brand --}}

    {{-- Aside Menu Navigasi --}}
    <div class="aside-menu flex-column-fluid">
        <div class="hover-scroll-overlay-y my-5 my-lg-5" id="kt_aside_menu_wrapper" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_aside_logo, #kt_aside_footer" data-kt-scroll-wrappers="#kt_aside_menu" data-kt-scroll-offset="0">
            {{-- id tanpa '#' agar selector data-kt-scroll-wrappers="#kt_aside_menu" resolve dengan benar --}}
            <div class="menu menu-column menu-title-gray-800 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-500" id="kt_aside_menu" data-kt-menu="true" data-kt-menu-expand="false">
                
                {{-- SECTION 1: PUBLIK --}}
                <div class="menu-item">
                    <div class="menu-content pt-8 pb-2">
                        <span class="menu-section text-muted text-uppercase fs-8 ls-1">Menu Publik</span>
                    </div>
                </div>
                <div class="menu-item">
                    <a class="menu-link {{ request()->routeIs('analytics.dashboard') ? 'active' : '' }}" href="{{ route('analytics.dashboard') }}">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-element-11 fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                        </span>
                        <span class="menu-title">Dashboard Analitik</span>
                    </a>
                </div>

                {{-- SECTION 2: ADMINISTRATOR --}}
                @auth
                    @if(auth()->user()->role === 'admin')
                        @php
                            $draftCountBadge = \App\Models\KejadianKebakaran::where('status_verifikasi', 'draft')->count();
                        @endphp
                        <div class="menu-item">
                            <div class="menu-content pt-8 pb-2">
                                <span class="menu-section text-muted text-uppercase fs-8 ls-1">Administrator</span>
                            </div>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('admin.verifikasi.*') ? 'active' : '' }}" href="{{ route('admin.verifikasi.index') }}">
                                <span class="menu-icon">
                                    <i class="ki-duotone ki-shield-tick fs-2"><span class="path1"></span><span class="path2"></span></i>
                                </span>
                                <span class="menu-title">Verifikasi &amp; Data</span>
                                @if($draftCountBadge > 0)
                                    <span class="menu-badge ms-auto">
                                        <span class="badge badge-warning badge-sm fw-bolder">{{ $draftCountBadge }}</span>
                                    </span>
                                @endif
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('admin.laporan.matriks') ? 'active' : '' }}" href="{{ route('admin.laporan.matriks') }}">
                                <span class="menu-icon">
                                    <i class="ki-duotone ki-document fs-2"><span class="path1"></span><span class="path2"></span></i>
                                </span>
                                <span class="menu-title">Rekapitulasi Matriks</span>
                            </a>
                        </div>
                    @endif

                    {{-- SECTION 3: PETUGAS LAPANGAN --}}
                    @if(auth()->user()->role === 'petugas')
                        <div class="menu-item">
                            <div class="menu-content pt-8 pb-2">
                                <span class="menu-section text-muted text-uppercase fs-8 ls-1">Petugas Lapangan</span>
                            </div>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('petugas.kejadian.create') ? 'active' : '' }}" href="{{ route('petugas.kejadian.create') }}">
                                <span class="menu-icon">
                                    <i class="ki-duotone ki-notepad-edit fs-2"><span class="path1"></span><span class="path2"></span></i>
                                </span>
                                <span class="menu-title">Input Kejadian</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link {{ request()->routeIs('petugas.kejadian.index') ? 'active' : '' }}" href="{{ route('petugas.kejadian.index') }}">
                                <span class="menu-icon">
                                    <i class="ki-duotone ki-document fs-2"><span class="path1"></span><span class="path2"></span></i>
                                </span>
                                <span class="menu-title">Riwayat Laporan</span>
                            </a>
                        </div>
                    @endif
                @endauth

            </div>
        </div>
    </div>

    {{-- begin::Aside Footer --}}
    {{-- Wajib ada: dirujuk oleh data-kt-scroll-dependencies="#kt_aside_footer" pada menu wrapper.
         Tombol memakai .btn-custom yang sudah distyling khusus untuk .aside-dark
         (color:#b5b5c3; background:rgba(63,66,84,.35)). --}}
    <div class="aside-footer flex-column-auto pt-5 pb-7 px-6" id="kt_aside_footer">
        <a href="{{ route('analytics.dashboard') }}" class="btn btn-custom btn-primary w-100" data-bs-toggle="tooltip" data-bs-trigger="hover" title="Buka dashboard analitik publik">
            <span class="btn-label">Dashboard Analitik</span>
            <i class="ki-duotone ki-element-11 fs-2 ms-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
        </a>
    </div>
    {{-- end::Aside Footer --}}
</div>
