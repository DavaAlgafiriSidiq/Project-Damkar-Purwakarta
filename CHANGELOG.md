# Changelog

Semua perubahan penting pada proyek Sistem Pencatatan & Dashboard Analitik Damkar Purwakarta dicatat dalam berkas ini. Format berkas ini mengacu pada standar hierarki berbasis Bahasa Indonesia.

## [Unreleased] - 2026-10-09

### Perbaikan (Fixed)
- **Sinkronisasi Ikon Dropdown & Koreksi Mapping Kategori ([dashboard.blade.php](file:///c:/xampp/htdocs/Project%20Damkar/resources/views/analytics/dashboard.blade.php), [create.blade.php](file:///c:/xampp/htdocs/Project%20Damkar/resources/views/petugas/create.blade.php), [edit.blade.php](file:///c:/xampp/htdocs/Project%20Damkar/resources/views/petugas/edit.blade.php), [admin/verifikasi/edit.blade.php](file:///c:/xampp/htdocs/Project%20Damkar/resources/views/admin/verifikasi/edit.blade.php)):**
  - Menyamakan logika pemetaan `data-icon` pada perulangan Kategori Objek dan Dugaan Penyebab di Dashboard Analitik secara 100% identik dengan form Input Kejadian dan Verifikasi Admin.
  - Mengeliminasi ikon checkmark generik (`ki-shield-tick`) pada opsi dan fallback dropdown, menggantinya dengan ikon semantik presisi: `ki-duotone ki-electricity` (listrik, tower, genset), `las la-paw` (semua hewan & peternakan), `ki-duotone ki-home-2` (bangunan/rumah), `ki-duotone ki-shop` (pasar/toko), `ki-duotone ki-bank` (sekolah/kantor/RS), `las la-wind` (angin puting beliung), dan `las la-life-ring` (penyelamatan/rescue).
  - Memperbaiki bug ikon hilang pada penyebab kompor tradisional/hawu dengan mengganti class invalid `las la-burn` menjadi `las la-fire` yang valid di LineAwesome.
- **Perbaikan Visual Ikon & Kontras Select2 ([layouts/app.blade.php](file:///c:/xampp/htdocs/Project%20Damkar/resources/views/layouts/app.blade.php)):**
  - Memastikan aturan CSS peredupan opacity dan warna pudar HANYA berlaku pada elemen dengan atribut `[aria-disabled="true"]` atau class `.is-disabled`.
  - Mengunci kontras solid untuk seluruh opsi normal yang dapat dipilih (`opacity: 1 !important; color: #4B5675 !important;` pada ikon dan `#252F4A !important;` pada teks label), menghapus class `text-gray-600` yang membuat tampilan opsi tampak redup/disabled.
  - Mempertahankan dan menegaskan efek hover/highlight: latar belakang biru terang (`#F1FAFF`) serta perubahan warna teks dan ikon menjadi biru primer (`#009EF7`) saat disorot kursor untuk memperjelas status interaktif (*selectable*).
- **Penataan Urutan Dropdown & Pemisahan Disabled ([dashboard.blade.php](file:///c:/xampp/htdocs/Project%20Damkar/resources/views/analytics/dashboard.blade.php), [layouts/app.blade.php](file:///c:/xampp/htdocs/Project%20Damkar/resources/views/layouts/app.blade.php), form input):**
  - Memastikan opsi `-- Pilih Semua Objek --` dan `-- Pilih Semua Penyebab --` (`<option value="">`) selalu dirender dan berada di urutan teratas (indeks 1) pada setiap dropdown filter Dashboard Analitik.
  - Memaksa opsi nonaktif (`disabled`) dan judul pemisah kategori turun ke urutan paling bawah dropdown (DOM reordering dan CSS `order: 99`).
  - Menerapkan pengurutan alfabetis dengan opsi "Lain-lain" / "Lainnya" selalu berada di paling akhir via scope `orderWithLainLast()`.
- **Interaktivitas Dependen Zona & Kecamatan ([dashboard.blade.php](file:///c:/xampp/htdocs/Project%20Damkar/resources/views/analytics/dashboard.blade.php)):**
  - Menerapkan logika dependen zona &rarr; kecamatan: memilih zona tertentu otomatis menyembunyikan kecamatan di luar zona tersebut dan me-reset pilihan kecamatan.
  - Mengurutkan kecamatan berdasarkan zona layanan baku (WMK Pusat, UPTD 1-3, dan Luar Kabupaten) via scope `orderByZona()`.
- **Cakupan Filter Zona Layanan Matriks ([LaporanController.php](file:///c:/xampp/htdocs/Project%20Damkar/app/Http/Controllers/Admin/LaporanController.php)):**
  - Menjaga integritas Tabel 1 (Distribusi Wilayah) agar selalu menampilkan 18 kecamatan lengkap tanpa terpotong oleh filter zona layanan (format baku dinas).
  - Menerapkan filter zona layanan secara ketat membatasi data pada Tabel 2 (Pemadaman Kebakaran) dan Tabel 3 (Penyelamatan/Rescue).

### Perubahan (Changed)
- **Modernisasi Dropdown (Select2 dengan Ikon Dinamis):**
  - Mengganti seluruh form `<select>` bawaan browser pada modul Dashboard Analitik, Rekapitulasi Matriks, Riwayat Petugas, serta Form Input & Verifikasi Kejadian dengan komponen *Select2* (Metronic 8).
  - Menyeragamkan seluruh ikon filter waktu (tahun & bulan) menggunakan ikon kalender bawaan template Metronic (`ki-duotone ki-calendar`).
  - Menambahkan teks label deskriptif `Saring Data Berdasarkan:` disertai ikon filter di atas baris kontrol filter pada ketiga seksi Dashboard Analitik.
- **Harmonisasi Desain Kartu KPI & Ringkasan (Soft / Pastel Theme):**
  - Menghilangkan gaya border putus-putus (`border-dashed`) dengan warna neon mencolok pada seluruh kartu ringkasan KPI.
  - Mengganti kartu KPI menjadi card pastel lembut tanpa border (`border-0`), bayangan halus (`shadow-xs`), dan sudut melengkung modern (`rounded-3`).
- **Restrukturisasi Hierarki & Layout Dashboard:**
  - Mengoptimasi grid sistem 5 kolom seimbang di layar desktop (`row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-5 g-4`).
  - Merapikan padding dan margin chart agar padat (*compact*), teratur, dan profesional.

---

## [Unreleased] - 2026-10-08

### Perbaikan & Refaktor (Fixed & Refactored)
- **Penghapusan Hardcode Kategori (`LaporanController.php`):**
  - Menghapus ketergantungan `whereIn('id', range(1, 9))` dan `whereIn('id', range(10, 22))` pada agregasi matriks rekapitulasi.
  - Mengubah query menjadi dinamis menggunakan kolom `jenis_layanan`:
    - Objek Kebakaran: `KategoriObjek::where('jenis_layanan', 'kebakaran')->orderBy('id')->get()`
    - Objek Penyelamatan: `KategoriObjek::where('jenis_layanan', 'rescue')->orderBy('id')->get()`
    - Penentuan sub-tipe Penyelamatan Hewan vs Evakuasi Khusus dilakukan secara dinamis menggunakan pencocokan kata kunci nama kategori, bukan array ID statis.
- **Proteksi Integritas Data Terverifikasi (`VerifikasiKejadianController.php`):**
  - Menambahkan guard pada method `destroy(int $id)` untuk menolak penghapusan laporan yang sudah berstatus `verified` (`status_verifikasi === 'verified'`).
  - Mengembalikan flash message error `Data terverifikasi tidak dapat dihapus. Silakan batalkan verifikasi terlebih dahulu jika ada kesalahan fatal.`.
  - Menambahkan container `@if(session('error'))` pada [admin/verifikasi/index.blade.php](file:///c:/xampp/htdocs/Project%20Damkar/resources/views/admin/verifikasi/index.blade.php) dengan styling Metronic 8 `alert-danger`.
- **Koreksi Dokumentasi Model (`User.php`):**
  - Memperbaiki docblock pada [User.php](file:///c:/xampp/htdocs/Project%20Damkar/app/Models/User.php) yang sebelumnya kontradiktif mengenai `$fillable` role, menegaskan bahwa `role` sengaja dikeluarkan dari `$fillable` demi mencegah Mass Assignment Vulnerability.
- **Pembersihan File Sampah / Demo:**
  - Menghapus `resources/views/layouts/partials/header.blade.php` (sisa demo Metronic yang tidak digunakan).
  - Menghapus `resources/views/welcome.blade.php` (sisa template bawaan Laravel).

### Penambahan (Added)
- **Fitur Ekspor Rekapitulasi Matriks ke File Excel (.xlsx):**
  - **Integrasi Library:** Mengintegrasikan `maatwebsite/excel` (v4.0.3) dengan `phpoffice/phpspreadsheet` (v5.10.0) untuk kompilasi lembar kerja spreadsheet berformat native `.xlsx`.
  - **Arsitektur Multi-Sheet Export:**
    - Membuat class induk [RekapMatriksExport.php](file:///c:/xampp/htdocs/Project%20Damkar/app/Exports/RekapMatriksExport.php) yang mengimplementasikan `Maatwebsite\Excel\Concerns\Export` dan `Maatwebsite\Excel\Concerns\WithMultipleSheets`.
    - Membuat 3 sheet dedicated:
      1. [RekapWilayahSheet.php](file:///c:/xampp/htdocs/Project%20Damkar/app/Exports/Sheets/RekapWilayahSheet.php) &rarr; Sheet *1. Wilayah Kecamatan* ([wilayah.blade.php](file:///c:/xampp/htdocs/Project%20Damkar/resources/views/admin/laporan/excel/wilayah.blade.php))
      2. [RekapKebakaranSheet.php](file:///c:/xampp/htdocs/Project%20Damkar/app/Exports/Sheets/RekapKebakaranSheet.php) &rarr; Sheet *2. Pemadaman Kebakaran* ([kebakaran.blade.php](file:///c:/xampp/htdocs/Project%20Damkar/resources/views/admin/laporan/excel/kebakaran.blade.php))
      3. [RekapRescueSheet.php](file:///c:/xampp/htdocs/Project%20Damkar/app/Exports/Sheets/RekapRescueSheet.php) &rarr; Sheet *3. Operasi Penyelamatan* ([rescue.blade.php](file:///c:/xampp/htdocs/Project%20Damkar/resources/views/admin/laporan/excel/rescue.blade.php))
    - Masing-masing sheet dilengkapi header dinas resmi, styling warna tematik, border tabel standar, lebar kolom otomatis, dan footer total akumulasi.
  - **Backend Controller & Routing:**
    - Menambahkan method `exportExcel(Request $request)` dan refaktorisasi `getMatriksData(int $tahun, ?string $zonaLayanan)` di [Admin\LaporanController.php](file:///c:/xampp/htdocs/Project%20Damkar/app/Http/Controllers/Admin/LaporanController.php).
    - Menghasilkan nama file rapi secara dinamis: `Rekap_Kejadian_Damkar_{tahun}.xlsx` (atau `Rekap_Kejadian_Damkar_{tahun}_{zona_slug}.xlsx` jika difilter per zona).
    - Mendaftarkan rute `GET /admin/laporan/matriks/export` dengan nama `admin.laporan.matriks.export` di [routes/web.php](file:///c:/xampp/htdocs/Project%20Damkar/routes/web.php).
  - **Frontend Binding:**
    - Memperbarui tombol aksi pada [admin/laporan/matriks.blade.php](file:///c:/xampp/htdocs/Project%20Damkar/resources/views/admin/laporan/matriks.blade.php) menjadi tombol unduhan yang meneruskan query parameters aktif (`?tahun=...&zona_layanan=...`).
- **Restrukturisasi Matriks Rekapitulasi Tahunan (Format Excel Dinas Damkar):**
  - **Pemisahan Operasi Kebakaran vs Penyelamatan:** Memisahkan data murni agar tidak ada kerancuan objek non-api pada penyebab kebakaran:
    1. *Tab 1: Distribusi Wilayah (Kecamatan):* Mengagregasi seluruh kejadian (Kebakaran + Rescue) per kecamatan per bulan (1-12) dan total tahunan.
    2. *Tab 2: Operasi Pemadaman Kebakaran (Murni Darurat):* Memuat 2 tabel vertikal — Tabel 2.A (Objek Kebakaran) dan Tabel 2.B (Dugaan Penyebab Api).
    3. *Tab 3: Operasi Penyelamatan / Rescue (Murni Non-Darurat):* Memuat 1 tabel berdasarkan jenis kasus penyelamatan (Penyelamatan Hewan & Evakuasi Khusus).
  - **Database Migration & Seeder Zona Layanan (WMK):**
    - Migration `2026_10_08_033938_add_zona_layanan_to_kecamatan_table.php` menambahkan kolom `zona_layanan` pada tabel `kecamatan`.
    - Memperbarui [KecamatanSeeder.php](file:///c:/xampp/htdocs/Project%20Damkar/database/seeders/KecamatanSeeder.php) untuk memetakan 18 kecamatan ke 5 zona operasional (`WMK Pusat`, `WMK UPTD 1`, `WMK UPTD 2`, `WMK UPTD 3`, dan `Luar Daerah`).
  - **Filter Zona Layanan Dinamis:**
    - Menambahkan dropdown filter "Zona Layanan" di header tabel matriks berdampingan dengan filter tahun.
- **Akuntabilitas Akun Bersama (`nama_pelapor`):**
  - Menambahkan kolom `nama_pelapor` pada tabel `kejadian_kebakaran` via migration `2026_10_08_000001_add_nama_pelapor_to_kejadian_kebakaran_table.php`.
  - Validasi wajib isi pada controller petugas dan admin, serta input form dengan border aksen peringatan akun bersama.
- **Soft Deletes & Audit Log Otomatis:**
  - Menambahkan kolom `deleted_at` via migration `2026_10_08_000002_add_soft_deletes_to_kejadian_kebakaran_table.php`.
  - Membuat sistem audit log otomatis (`audit_logs`) via migration `2026_10_08_000003_create_audit_logs_table.php` dan lifecycle hooks Eloquent Observers.
- **Data Master Wilayah "Luar Kabupaten":**
  - Menambahkan entri `'Luar Kabupaten' => 'PUSAT'` untuk menampung bantuan mutual aid di luar wilayah Purwakarta.

### Keamanan & Proteksi Sistem (Security & Hardening)
- **Model Security ($fillable & Mass Assignment Guard):**
  - Menghapus kolom `role` dari `$fillable` model [User.php](file:///c:/xampp/htdocs/Project%20Damkar/app/Models/User.php).
  - Menghapus `status_verifikasi`, `diverifikasi_oleh`, dan `diverifikasi_pada` dari `$fillable` model [KejadianKebakaran.php](file:///c:/xampp/htdocs/Project%20Damkar/app/Models/KejadianKebakaran.php).
  - Mengunci pengisian status verifikasi di level server (Petugas selalu `draft`, Admin via method verifikasi eksplisit).
- **Access Guard Petugas:**
  - Melindungi method `edit()`, `update()`, dan `destroy()` dengan guard `abort(403)` jika laporan telah diverifikasi.
- **Privacy Audit Endpoint Publik:**
  - Memastikan seluruh endpoint analitik publik di [ChartDataController.php](file:///c:/xampp/htdocs/Project%20Damkar/app/Http/Controllers/Analytics/ChartDataController.php) hanya mengembalikan data agregat tanpa mengekspos identitas perorangan atau rincian korban.

---

## [Unreleased] - 2026-10-07

### Penambahan (Added)
- **Pemisahan Status Operasi Lapangan vs Status Verifikasi Dokumen:**
  - Migration `2026_10_07_054127_add_status_operasi_to_kejadian_kebakaran_table.php` menambahkan kolom `status_operasi` dan `tanggal_waktu_selesai`.
  - Penambahan input dropdown status operasi dan input datetime-local waktu selesai pada form input Petugas dan Admin.
  - Refaktor banner Live Alert publik: hanya aktif jika terdapat insiden berstatus `dalam_penanganan`.
- **Pencatatan Data Korban & Terdampak:**
  - Migration `2026_10_07_051940_add_korban_fields_to_kejadian_kebakaran_table.php` menambahkan 5 kolom integer korban: `korban_meninggal`, `korban_luka_berat`, `korban_luka_ringan`, `kk_terdampak`, dan `jiwa_terdampak`.
  - Penambahan card "Data Korban & Dampak Sosial" pada form input Petugas dan Admin.
- **Notifikasi Real-Time Publik (Live Alert):**
  - Endpoint publik `/api/analytics/live-alert` dan banner interaktif pulsing di bagian atas dashboard dengan auto-polling berkala.
- **Pemisahan Global Filter menjadi 3 Seksi Independen:**
  - Seksi 1 (Ringkasan KPI), Seksi 2 (Data Insiden Kebakaran), dan Seksi 3 (Data Penyelamatan/Rescue) beroperasi secara terpisah tanpa saling memicu reload yang tidak diinginkan.
- **Visualisasi Komprehensif Data Rescue:**
  - Penambahan grafik tren bulanan rescue, distribusi kasus pie chart, dan sebaran per kecamatan via endpoint analitik dedicated.

### Perubahan (Changed)
- **Refaktor UI Dashboard Analitik ("Compact & Proportional"):**
  - Memindahkan kontrol form filter inline ke dalam header card masing-masing seksi.
  - Penyesuaian ukuran tipografi metrik KPI, padding kartu, serta proporsi canvas grafik agar lebih padat dan responsif.
  - Penambahan aksen garis warna tematik pada header kartu: merah muda untuk Kebakaran dan ungu/biru untuk Rescue.
- **Penyelarasan Query Analitik:**
  - Seluruh endpoint di `ChartDataController` mendukung parameter filter dinamis menggunakan method Eloquent `->when()` dengan pembatasan ketat scope `KejadianKebakaran::verifiedOnly()`.
