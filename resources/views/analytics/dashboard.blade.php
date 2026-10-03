@extends('layouts.app')

@section('content')
<div class="container-fluid py-5">
    
    {{-- =====================================================================
         HEADER & FILTER TAHUN
         Dropdown filter otomatis submit form saat tahun berubah
    ====================================================================== --}}
    <div class="d-flex flex-wrap flex-stack mb-6">
        <h3 class="fw-bolder my-2">
            Dashboard Analitik Publik 
            <span class="fs-6 text-gray-400 fw-bold ms-1">Statistik Terverifikasi Tahun {{ $tahun }}</span>
        </h3>
        
        <div class="d-flex align-items-center my-2">
            <form id="filterForm" action="{{ route('analytics.dashboard') }}" method="GET" class="d-flex align-items-center gap-3">
                <i class="ki-duotone ki-filter fs-2 text-gray-500"><span class="path1"></span><span class="path2"></span></i>
                <select name="tahun" class="form-select form-select-sm form-select-solid w-125px" onchange="document.getElementById('filterForm').submit();">
                    <option value="2026" {{ $tahun == 2026 ? 'selected' : '' }}>Tahun 2026</option>
                    <option value="2025" {{ $tahun == 2025 ? 'selected' : '' }}>Tahun 2025</option>
                    <option value="2024" {{ $tahun == 2024 ? 'selected' : '' }}>Tahun 2024</option>
                </select>
            </form>
        </div>
    </div>

    {{-- =====================================================================
         BARIS 1: KARTU STATISTIK RINGKASAN (5 KARTU INDIKATOR UTAMA)
         Menampilkan angka total yang dikalkulasi langsung oleh DashboardController
    ====================================================================== --}}
    <div class="row g-4 gx-xl-6 mb-7">
        {{-- Widget 1: Total Seluruh Kejadian (Kebakaran + Rescue) --}}
        <div class="col-xl col-md-4 col-sm-6">
            <div class="card card-flush bg-dark bg-opacity-5 border border-gray-300 shadow-sm hover-elevate-up h-100">
                <div class="card-body py-5 px-5">
                    <div class="d-flex flex-stack">
                        <span class="fw-bolder fs-5 text-dark">Total Seluruh</span>
                        <div class="symbol symbol-40px symbol-circle bg-white shadow-xs p-2">
                            <i class="ki-duotone ki-element-11 fs-2 text-dark">
                                <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span>
                            </i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="fs-2hx fw-bolder text-dark">{{ number_format($totalSeluruhKejadian ?? 0) }}</span>
                        <span class="text-muted fs-7 fw-semibold ms-1">insiden</span>
                    </div>
                    <div class="text-muted fs-8 mt-1">Kebakaran + Rescue {{ $tahun }}</div>
                </div>
            </div>
        </div>

        {{-- Widget 2: Total Kebakaran --}}
        <div class="col-xl col-md-4 col-sm-6">
            <div class="card card-flush bg-danger bg-opacity-10 border border-danger border-opacity-20 shadow-sm hover-elevate-up h-100">
                <div class="card-body py-5 px-5">
                    <div class="d-flex flex-stack">
                        <span class="fw-bolder fs-5 text-danger">Total Kebakaran</span>
                        <div class="symbol symbol-40px symbol-circle bg-white shadow-xs p-2">
                            <i class="las la-fire fs-2 text-danger"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="fs-2hx fw-bolder text-dark">{{ number_format($totalKebakaran ?? 0) }}</span>
                        <span class="text-muted fs-7 fw-semibold ms-1">kejadian</span>
                    </div>
                    <div class="text-muted fs-8 mt-1">Insiden kebakaran aktif</div>
                </div>
            </div>
        </div>

        {{-- Widget 3: Total Karhutla --}}
        <div class="col-xl col-md-4 col-sm-6">
            <div class="card card-flush bg-warning bg-opacity-10 border border-warning border-opacity-20 shadow-sm hover-elevate-up h-100">
                <div class="card-body py-5 px-5">
                    <div class="d-flex flex-stack">
                        <span class="fw-bolder fs-5 text-warning">Total Karhutla</span>
                        <div class="symbol symbol-40px symbol-circle bg-white shadow-xs p-2">
                            <i class="ki-duotone ki-tree fs-2 text-warning">
                                <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                            </i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="fs-2hx fw-bolder text-dark">{{ number_format($totalKarhutla ?? 0) }}</span>
                        <span class="text-muted fs-7 fw-semibold ms-1">kejadian</span>
                    </div>
                    <div class="text-muted fs-8 mt-1">Kebakaran hutan & lahan</div>
                </div>
            </div>
        </div>

        {{-- Widget 4: Total Rescue --}}
        <div class="col-xl col-md-6 col-sm-6">
            <div class="card card-flush bg-primary bg-opacity-10 border border-primary border-opacity-20 shadow-sm hover-elevate-up h-100">
                <div class="card-body py-5 px-5">
                    <div class="d-flex flex-stack">
                        <span class="fw-bolder fs-5 text-primary">Total Rescue</span>
                        <div class="symbol symbol-40px symbol-circle bg-white shadow-xs p-2">
                            <i class="ki-duotone ki-rescue fs-2 text-primary">
                                <span class="path1"></span><span class="path2"></span>
                            </i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="fs-2hx fw-bolder text-dark">{{ number_format($totalRescue ?? 0) }}</span>
                        <span class="text-muted fs-7 fw-semibold ms-1">operasi</span>
                    </div>
                    <div class="text-muted fs-8 mt-1">Penyelamatan non-darurat</div>
                </div>
            </div>
        </div>

        {{-- Widget 5: Kecamatan Hotspot --}}
        <div class="col-xl col-md-6 col-sm-12">
            <div class="card card-flush bg-info bg-opacity-10 border border-info border-opacity-20 shadow-sm hover-elevate-up h-100">
                <div class="card-body py-5 px-5">
                    <div class="d-flex flex-stack">
                        <span class="fw-bolder fs-5 text-info">Kecamatan Hotspot</span>
                        <div class="symbol symbol-40px symbol-circle bg-white shadow-xs p-2">
                            <i class="ki-duotone ki-geolocation fs-2 text-info">
                                <span class="path1"></span><span class="path2"></span>
                            </i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="fs-2 fw-bolder text-dark">{{ $kecamatanTertinggi ? $kecamatanTertinggi->kecamatan : '-' }}</span>
                    </div>
                    <div class="text-muted fs-8 mt-1">Wilayah frekuensi tertinggi</div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- =====================================================================
         BARIS 2: TREN BULANAN (Line Chart) + DISTRIBUSI OBJEK (Pie + Tabel)
    ====================================================================== --}}
    <div class="row g-5 gx-xl-10 mb-5 mb-xl-10">
        {{-- Tren Bulanan (Line Chart, lebar 8 kolom) --}}
        <div class="col-xl-8">
            <div class="card card-flush shadow-sm h-100">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">Tren Kejadian Bulanan ({{ $tahun }})</span>
                        <span class="text-muted fw-bold fs-7">Perbandingan Kebakaran vs Operasi Rescue</span>
                    </h3>
                </div>
                <div class="card-body pt-2">
                    <canvas id="chartTrenBulanan" style="width: 100%; height: 350px;"></canvas>
                </div>
            </div>
        </div>
        
        {{-- ============================================================
             Distribusi Objek Kebakaran (Pie Chart + Tabel Ringkasan)
             Tabel angka ditambahkan agar pengguna tidak harus menebak
             ukuran potongan diagram lingkaran
        ============================================================= --}}
        <div class="col-xl-4">
            <div class="card card-flush shadow-sm h-100">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">Objek Kebakaran</span>
                        <span class="text-muted fw-bold fs-7">Distribusi berdasarkan jenis objek terbakar</span>
                    </h3>
                </div>
                <div class="card-body pt-2">
                    {{-- Canvas chart pie --}}
                    <div class="d-flex justify-content-center">
                        <canvas id="chartObjek" style="max-height: 220px;"></canvas>
                    </div>

                    {{-- Tabel ringkasan angka kuantitatif (diisi oleh JS setelah fetch) --}}
                    <div class="mt-5">
                        <div class="separator separator-dashed mb-3"></div>
                        <table class="table table-row-dashed table-row-gray-300 gy-2 gs-0 mb-0">
                            <thead>
                                <tr class="fw-bolder text-muted fs-8 text-uppercase">
                                    <th>Objek</th>
                                    <th class="text-end">Jumlah</th>
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
    </div>
    
    {{-- =====================================================================
         BARIS 3: SEBARAN KECAMATAN (Bar) + PENYEBAB KEBAKARAN (Doughnut + Tabel)
    ====================================================================== --}}
    <div class="row g-5 gx-xl-10 mb-10">
        {{-- Sebaran per Kecamatan (Bar Chart, lebar 6 kolom) --}}
        <div class="col-xl-6">
            <div class="card card-flush shadow-sm h-100">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">Sebaran per Kecamatan</span>
                        <span class="text-muted fw-bold fs-7">Jumlah kejadian kebakaran per wilayah</span>
                    </h3>
                </div>
                <div class="card-body pt-2">
                    <canvas id="chartKecamatan" style="width: 100%; height: 320px;"></canvas>
                </div>
            </div>
        </div>

        {{-- ============================================================
             Dugaan Penyebab Kebakaran (Doughnut + Tabel Ringkasan)
             Tabel angka ditambahkan untuk kejelasan data kuantitatif
        ============================================================= --}}
        <div class="col-xl-6">
            <div class="card card-flush shadow-sm h-100">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bolder fs-3 mb-1">Dugaan Penyebab</span>
                        <span class="text-muted fw-bold fs-7">Distribusi dugaan penyebab kebakaran</span>
                    </h3>
                </div>
                <div class="card-body pt-2">
                    {{-- Canvas chart doughnut --}}
                    <div class="d-flex justify-content-center">
                        <canvas id="chartPenyebab" style="max-height: 220px;"></canvas>
                    </div>

                    {{-- Tabel ringkasan angka kuantitatif (diisi oleh JS setelah fetch) --}}
                    <div class="mt-5">
                        <div class="separator separator-dashed mb-3"></div>
                        <table class="table table-row-dashed table-row-gray-300 gy-2 gs-0 mb-0">
                            <thead>
                                <tr class="fw-bolder text-muted fs-8 text-uppercase">
                                    <th>Penyebab</th>
                                    <th class="text-end">Jumlah</th>
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

{{-- CDN Chart.js (versi stabil) --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {

    const tahun = '{{ $tahun }}';

    // ===================================================================
    // PALET WARNA: 13 warna unik & kontras — tidak ada yang mirip/ganda
    // Dipilih berdasarkan standar aksesibilitas WCAG & teori warna kontras
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

    // ===================================================================
    // Helper: Membuat baris tabel + baris "Total" di tfoot
    // tbodyId  : id elemen <tbody>
    // tfootId  : id elemen <tfoot>
    // labels   : array nama kategori
    // values   : array angka kuantitatif
    // ===================================================================
    function isiTabelDenganTotal(tbodyId, tfootId, labels, values) {
        const tbody = document.getElementById(tbodyId);
        const tfoot = document.getElementById(tfootId);
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

        // Baris "Total" di footer tabel
        tfoot.innerHTML = `
            <tr class="border-top border-top-dashed">
                <td class="fw-bolder text-dark fs-7 pt-3">Total</td>
                <td class="text-end fw-bolder text-dark fs-7 pt-3">${total.toLocaleString()}</td>
                <td class="text-end fw-bolder text-primary fs-7 pt-3">100%</td>
            </tr>`;
    }

    // ===================================================================
    // 1. TREN BULANAN — Line Chart Komparasi Kebakaran vs Rescue
    // ===================================================================
    Promise.all([
        fetch(`/api/analytics/tren-kebakaran-bulanan?tahun=${tahun}`).then(r => r.json()),
        fetch(`/api/analytics/tren-rescue-bulanan?tahun=${tahun}`).then(r => r.json())
    ]).then(([dataKebakaran, dataRescue]) => {
        new Chart(document.getElementById('chartTrenBulanan').getContext('2d'), {
            type: 'line',
            data: {
                labels: dataKebakaran.labels,
                datasets: [
                    {
                        label: 'Kebakaran',
                        data: dataKebakaran.datasets[0].data,
                        borderColor: '#F1416C',
                        backgroundColor: 'rgba(241,65,108,0.07)',
                        borderWidth: 2.5, pointRadius: 4,
                        pointBackgroundColor: '#F1416C',
                        tension: 0.4, fill: true
                    },
                    {
                        label: 'Rescue',
                        data: dataRescue.datasets[0].data,
                        borderColor: '#009EF7',
                        backgroundColor: 'rgba(0,158,247,0.07)',
                        borderWidth: 2.5, pointRadius: 4,
                        pointBackgroundColor: '#009EF7',
                        tension: 0.4, fill: true
                    }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
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
    });

    // ===================================================================
    // 2. DISTRIBUSI OBJEK KEBAKARAN — Pie Chart + Tabel + Baris Total
    // ===================================================================
    fetch(`/api/analytics/distribusi-objek-kebakaran?tahun=${tahun}`)
        .then(r => r.json())
        .then(data => {
            const labels = Array.isArray(data.labels)             ? data.labels             : [];
            const values = Array.isArray(data.datasets?.[0]?.data)? data.datasets[0].data   : [];

            new Chart(document.getElementById('chartObjek').getContext('2d'), {
                type: 'pie',
                data: {
                    labels,
                    datasets: [{
                        data: values,
                        backgroundColor: labels.map((_, i) => PALETTE[i % PALETTE.length]),
                        borderWidth: 2, borderColor: '#fff'
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
                                    return ` ${ctx.label}: ${ctx.parsed} (${((ctx.parsed/tot)*100).toFixed(1)}%)`;
                                }
                            }
                        }
                    }
                }
            });
            isiTabelDenganTotal('tabelObjekBody', 'tabelObjekFoot', labels, values);
        });

    // ===================================================================
    // 3. SEBARAN PER KECAMATAN — Horizontal Bar Chart
    // ===================================================================
    fetch(`/api/analytics/sebaran-per-kecamatan?tahun=${tahun}`)
        .then(r => r.json())
        .then(data => {
            new Chart(document.getElementById('chartKecamatan').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Jumlah Kebakaran',
                        data: data.datasets[0].data,
                        backgroundColor: 'rgba(0,158,247,0.80)',
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { beginAtZero: true, grid: { color: '#F5F5F5' } },
                        y: { grid: { display: false }, ticks: { font: { size: 11 } } }
                    }
                }
            });
        });

    // ===================================================================
    // 4. DUGAAN PENYEBAB — Doughnut Chart + Tabel + Baris Total
    // ===================================================================
    fetch(`/api/analytics/distribusi-penyebab-kebakaran?tahun=${tahun}`)
        .then(r => r.json())
        .then(data => {
            const labels = Array.isArray(data.labels)              ? data.labels             : [];
            const values = Array.isArray(data.datasets?.[0]?.data) ? data.datasets[0].data  : [];

            new Chart(document.getElementById('chartPenyebab').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels,
                    datasets: [{
                        data: values,
                        backgroundColor: labels.map((_, i) => PALETTE[i % PALETTE.length]),
                        borderWidth: 2, borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true, cutout: '62%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: ctx => {
                                    const tot = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                    return ` ${ctx.label}: ${ctx.parsed} (${((ctx.parsed/tot)*100).toFixed(1)}%)`;
                                }
                            }
                        }
                    }
                }
            });
            isiTabelDenganTotal('tabelPenyebabBody', 'tabelPenyebabFoot', labels, values);
        });
});
</script>
@endsection
