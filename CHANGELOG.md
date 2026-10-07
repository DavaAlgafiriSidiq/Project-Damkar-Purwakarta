# Changelog

Semua perubahan penting pada proyek Sistem Pencatatan & Dashboard Analitik Damkar Purwakarta dicatat dalam berkas ini.

## [Unreleased] - 2026-10-07

### Added
- **Pemisahan Status Operasi Lapangan vs Status Verifikasi Dokumen:**
  - **Database Migration:** Migration `2026_10_07_054127_add_status_operasi_to_kejadian_kebakaran_table.php` menambahkan kolom `status_operasi` (enum: `dalam_penanganan`, `selesai`, default: `dalam_penanganan`) dan `tanggal_waktu_selesai` (`datetime`, nullable).
  - **Model Eloquent:** Menambahkan `status_operasi` dan `tanggal_waktu_selesai` ke `$fillable` dan `$casts` di [KejadianKebakaran.php](file:///c:/xampp/htdocs/Project%20Damkar/app/Models/KejadianKebakaran.php).
  - **Controller Petugas & Admin:**
    - Pada [KejadianKebakaranController.php](file:///c:/xampp/htdocs/Project%20Damkar/app/Http/Controllers/Petugas/KejadianKebakaranController.php) (`store`, `update`) dan [VerifikasiKejadianController.php](file:///c:/xampp/htdocs/Project%20Damkar/app/Http/Controllers/Admin/VerifikasiKejadianController.php) (`update`), memvalidasi `status_operasi` dan `tanggal_waktu_selesai` (wajib diisi apabila status operasi dipilih `selesai`).
  - **Refaktor Live Alert:**
    - Method `liveAlert()` di [ChartDataController.php](file:///c:/xampp/htdocs/Project%20Damkar/app/Http/Controllers/Analytics/ChartDataController.php) kini dipicu secara mutlak oleh kondisi `status_operasi = 'dalam_penanganan'` dengan safety fallback maksimal 12 jam terakhir dari waktu kejadian. Saat operasi diubah menjadi `selesai`, banner Live Alert di dashboard publik otomatis dinonaktifkan (`is_active: false`).
  - **Frontend Metronic 8:**
    - Menambahkan input dropdown **Status Operasi Lapangan** dan input datetime-local **Waktu Penanganan Selesai** pada form input Petugas (`create.blade.php`, `edit.blade.php`) dan Admin (`edit.blade.php`).
    - Dilengkapi logika JavaScript: jika status dipilih "Dalam Penanganan", input Waktu Selesai dinonaktifkan/dikosongkan. Jika "Selesai", input Waktu Selesai otomatis diaktifkan dan wajib diisi.
    - Menambahkan badge status operasi di tabel daftar laporan Petugas (`petugas/index.blade.php`) dan Admin (`admin/verifikasi/index.blade.php`) — badge kuning `🟡 Penanganan` untuk insiden aktif dan badge hijau `🟢 Selesai` untuk operasi yang telah ditutup.

- **Pencatatan Data Korban & Terdampak (Modul Petugas & Database):**
  - **Database Migration:** Migration `2026_10_07_051940_add_korban_fields_to_kejadian_kebakaran_table.php` menambahkan 5 kolom integer (`nullable`, default 0) pada tabel `kejadian_kebakaran`: `korban_meninggal`, `korban_luka_berat`, `korban_luka_ringan`, `kk_terdampak`, dan `jiwa_terdampak`.
  - **Model Eloquent:** Menambahkan ke-5 kolom tersebut ke dalam `$fillable` dan `$casts` (tipe `integer`) pada model `KejadianKebakaran`.
  - **Controller Petugas:** Memperbarui method `store` dan menambahkan method `edit` serta `update` pada `KejadianKebakaranController` untuk memvalidasi input numerik (`integer`, `min:0`) dan menyimpannya ke database.
  - **Controller Admin:** Menambahkan validasi dan penyimpanan 5 kolom korban pada `VerifikasiKejadianController@update`.
  - **Frontend Metronic 8 Form Input:**
    - Menambahkan card "Data Korban & Dampak Sosial" pada `resources/views/petugas/create.blade.php` dan `resources/views/petugas/edit.blade.php` dengan grid rapi (Luka Ringan, Luka Berat, Meninggal dalam satu baris, serta KK Terdampak dan Jiwa Terdampak pada baris berikutnya).
    - Memperbarui `resources/views/petugas/index.blade.php` dengan tombol Edit untuk laporan berstatus Draft serta menampilkan rincian korban pada modal detail laporan.
- **Fitur Live Alert (Notifikasi Real-Time Publik):**
  - **Backend Endpoint:** Method `liveAlert()` pada `ChartDataController` mendeteksi 1 kejadian terbaru dari `kejadian_kebakaran` dengan syarat: `status_verifikasi = 'draft'` dan `tanggal_waktu_kejadian >= now()->subHours(3)`. Mengembalikan JSON `{"is_active": true, "jenis_layanan": "...", "kecamatan": "...", "waktu": "..."}` jika ada, atau `{"is_active": false}` jika nihil.
  - **Route Publik:** Mendaftarkan route GET `/api/analytics/live-alert` (tanpa auth middleware) pada `routes/web.php`.
  - **Banner Interaktif Metronic 8:** Menambahkan banner alert beranimasi pulsing (`#liveAlertBanner`) di bagian paling atas `resources/views/analytics/dashboard.blade.php` yang melakukan polling berkala setiap 30 detik via `setInterval()` dan menampilkan teks alert darurat secara otomatis saat ada kejadian aktif.

### Changed
- **Refaktor UI Dashboard Analitik — "Compact & Proportional" (Frontend Only):**
  - **Filter Forms dipadatkan & dipindah inline:**
    - Ketiga form filter (`#filterKeseluruhan`, `#filterKebakaran`, `#filterRescue`) dipindahkan langsung ke dalam `card-header` masing-masing seksinya.
    - Layout form menggunakan `d-flex align-items-center justify-content-between` — judul seksi di kiri, barisan dropdown filter di kanan.
    - Semua label filter dihapus (dianggap intuitif dari konteks placeholder dropdown); dropdown menggunakan `form-select-sm` dengan lebar fixed (`style="width:Xpx"`) agar tidak melebar tak terkontrol.
    - Tombol Submit diberi ikon `<i class="las la-search">` dan tombol Reset menjadi ikon `✕` minimalis.
  - **KPI Cards dipadatkan:**
    - Padding kartu diubah dari `py-5 px-5` → `px-4 py-3`.
    - Tipografi angka KPI diturunkan dari `fs-2hx` → `fs-2`.
    - Ikon badge dari `symbol-40px` → `symbol-30px`.
    - Subteks keterangan menggunakan `fs-9` (lebih kecil satu level).
  - **Pemisah visual seksi:**
    - Setiap seksi (KPI, Kebakaran, Rescue) dikemas dalam satu kartu utama (`card shadow-sm mb-4 border-0`).
    - Section header Kebakaran diberi aksen `border-left: 4px solid #f1416c` + gradient merah muda tipis.
    - Section header Rescue diberi aksen `border-left: 4px solid #009ef7` + gradient biru muda tipis.
    - Pemisah visual antar-chart dalam seksi menggunakan `separator separator-dashed my-3`.
  - **Canvas tinggi dikurangi:**
    - Line/bar chart besar: dari `350px` → `260px`.
    - Bar chart kecamatan: dari `320px` → `250px`.
    - Pie/Doughnut chart: dari `max-height: 220px` → `max-height: 190px`.
  - **Header dashboard direfaktor:**
    - Judul halaman menggunakan layout flexbox `d-flex justify-content-between` — teks judul di kiri, badge "Data Terverifikasi" di kanan.
    - Subtitel dengan warna `text-muted fs-7` di bawah judul utama.

### Added
- **Pemisahan Global Filter menjadi 3 Filter Seksi Independen:**
  - **Seksi 1 (Ringkasan KPI):** Form `<form id="filterKeseluruhan">` khusus untuk kartu KPI dengan ID elemen:
    - `#kpiTotalSeluruh`: Total Kejadian (Kebakaran + Rescue).
    - `#kpiTotalKebakaran`: Total Insiden Kebakaran.
    - `#kpiTotalKarhutla`: Total Kebakaran Hutan & Lahan.
    - `#kpiTotalRescue`: Total Operasi Rescue.
    - `#kpiKecamatanHotspot`: Wilayah Kecamatan Frekuensi Tertinggi.
  - **Seksi 2 (Data Insiden Kebakaran):** Form `<form id="filterKebakaran">` independen di atas visualisasi kebakaran (`#chartTrenBulanan`, `#chartObjek`, `#chartKecamatan`, `#chartPenyebab`) dengan input: Tahun, Bulan, Kecamatan, Kategori Objek, dan Dugaan Penyebab.
  - **Seksi 3 (Data Penyelamatan / Rescue):** Form `<form id="filterRescue">` independen di atas visualisasi rescue (`#chartTrenRescue`, `#chartJenisRescue`, `#chartKecamatanRescue`) dengan input: Tahun, Bulan, Kecamatan, dan Kategori Objek/Kasus.
  - **Refaktor JavaScript Fetch API:**
    - Memecah fungsi monolithic `loadAllCharts()` menjadi 3 fungsi modular independen: `updateKPI()`, `updateChartKebakaran()`, dan `updateChartRescue()`.
    - Masing-masing fungsi di-trigger oleh event listener `submit` dengan `e.preventDefault()` serta event listener `reset` form masing-masing seksi.
    - Mengubah filter pada Seksi Kebakaran **TIDAK** memicu reload data pada Seksi KPI atau Seksi Rescue, begitu pun sebaliknya.
- **Penyelarasan Endpoint Ringkasan KPI (`ChartDataController@ringkasanStatistik`):**
  - Menambahkan kalkulasi `total_karhutla` dan `kecamatan_hotspot` ke respon JSON agar konsisten dengan kartu KPI di frontend.
- **Global Filter Dashboard Analitik Publik:**
  - Ditambahkan form filter HTML fungsional pada tampilan [resources/views/analytics/dashboard.blade.php](file:///c:/xampp/htdocs/Project%20Damkar/resources/views/analytics/dashboard.blade.php) dengan parameter:
    - Tahun (`tahun`)
    - Bulan (`bulan`)
    - Kecamatan (`kecamatan_id`)
    - Kategori Objek (`kategori_objek_id`)
    - Dugaan Penyebab (`kategori_penyebab_id`)
  - Form dilengkapi tombol **Filter** (reload chart & KPI via AJAX/fetch tanpa reload halaman penuh) serta tombol **Reset**.
  - Dropdown master data pada form filter disuplai dari database melalui controller:
    - `Kecamatan::orderBy('nama_kecamatan')->get()`
    - `KategoriObjek::orderBy('nama_kategori')->get()`
    - `KategoriPenyebab::orderBy('nama_penyebab')->get()`
  - Integrasi JavaScript Chart.js untuk me-load seluruh chart secara dinamis dengan query string filter aktif (`/api/analytics/*?tahun=...&bulan=...&kecamatan_id=...`).
  - Penanganan lifecycle Chart.js instance (`.destroy()`) untuk mencegah kebocoran memori (memory leak) dan duplikasi canvas chart saat filter diperbarui.
- **Sesi Visualisasi Komprehensif Data Rescue (Penyelamatan):**
  - Ditambahkan endpoint baru `sebaranRescuePerKecamatan()` di `ChartDataController` dan route `/api/analytics/sebaran-rescue-per-kecamatan` di `routes/web.php`.
  - Ditambahkan section khusus **Data Penyelamatan (Rescue)** di [resources/views/analytics/dashboard.blade.php](file:///c:/xampp/htdocs/Project%20Damkar/resources/views/analytics/dashboard.blade.php) sejajar dengan Sesi Kebakaran:
    - `<canvas id="chartTrenRescue">`: Line chart tren bulanan operasi rescue.
    - `<canvas id="chartJenisRescue">`: Pie chart distribusi jenis/kategori kasus rescue beserta tabel ringkasan.
    - `<canvas id="chartKecamatanRescue">`: Bar chart sebaran operasi rescue di setiap wilayah kecamatan.
  - Seluruh visualisasi data Rescue terhubung penuh ke Global Filter (`tahun`, `bulan`, `kecamatan_id`, `kategori_objek_id`, `kategori_penyebab_id`) via `loadAllCharts()` dengan siklus `.destroy()` Chart.js yang aman.

### Changed
- **Pembaruan Query Controller Analitik (`ChartDataController`):**
  - Seluruh method endpoint analitik diperbarui agar mendukung parameter filter global menggunakan method Eloquent `->when()`:
    - `trenKebakaranBulanan()`
    - `trenRescueBulanan()`
    - `distribusiObjekKebakaran()`
    - `distribusiPenyebabKebakaran()`
    - `sebaranPerKecamatan()`
    - `distribusiJenisRescue()`
    - `trenKomparasiBulanan()`
    - `ringkasanStatistik()`
  - Seluruh query tetap mematuhi aturan arsitektur data: wajib menggunakan scope `KejadianKebakaran::verifiedOnly()`.
- **Pembaruan `DashboardController@index`:**
  - Menghubungkan pengambilan master data (`Kecamatan`, `KategoriObjek`, `KategoriPenyebab`) ke view.
  - Memperbarui kalkulasi KPI ringkasan pada load awal agar sinkron dengan parameter filter yang dikirimkan via query URL.
