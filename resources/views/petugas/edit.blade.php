@extends('layouts.app')

@section('content')
<div class="container-xxl">

    {{-- Toolbar / Breadcrumb --}}
    <div class="d-flex flex-wrap flex-stack pb-7">
        <div class="d-flex flex-column justify-content-center my-1">
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                Edit Laporan Kejadian Lapangan #{{ $laporan->id }}
            </h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('petugas.kejadian.index') }}" class="text-muted text-hover-primary">Petugas Lapangan</a>
                </li>
                <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
                <li class="breadcrumb-item text-dark">Edit Laporan #{{ $laporan->id }}</li>
            </ul>
        </div>
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <a href="{{ route('petugas.kejadian.index') }}" class="btn btn-sm btn-light-primary d-flex align-items-center">
                <i class="ki-duotone ki-arrow-left fs-4 me-1"><span class="path1"></span><span class="path2"></span></i>
                <span>Kembali ke Riwayat</span>
            </a>
        </div>
    </div>

    {{-- Alert Validasi Error --}}
    @if ($errors->any())
        <div class="alert alert-dismissible bg-light-danger border border-danger d-flex flex-column flex-sm-row p-5 mb-7">
            <i class="ki-duotone ki-cross-circle fs-2hx text-danger me-4 mb-5 mb-sm-0"><span class="path1"></span><span class="path2"></span></i>
            <div class="d-flex flex-column pe-0 pe-sm-10">
                <h5 class="mb-1 text-danger">Terdapat kesalahan isian form:</h5>
                <ul class="mb-0 ps-4 text-danger fs-7">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">
                <i class="ki-duotone ki-cross fs-1 text-danger"><span class="path1"></span><span class="path2"></span></i>
            </button>
        </div>
    @endif

    {{-- Form Edit Kejadian --}}
    <form action="{{ route('petugas.kejadian.update', $laporan->id) }}" method="POST" id="formEditKejadian">
        @csrf
        @method('PUT')

        <div class="row g-7">
            
            {{-- Kolom Kiri: Klasifikasi, Lokasi & Korban --}}
            <div class="col-lg-7">
                <div class="card card-flush shadow-sm mb-7">
                    <div class="card-header pt-6">
                        <div class="card-title">
                            <i class="ki-duotone ki-file-sheet fs-2 text-primary me-2"><span class="path1"></span><span class="path2"></span></i>
                            <h3 class="fw-bold m-0 fs-5">Klasifikasi & Lokasi Kejadian</h3>
                        </div>
                    </div>
                    <div class="card-body pt-2">
                        
                        {{-- 1. Jenis Layanan (Radio Box Interaktif) --}}
                        <div class="mb-6">
                            <label class="required form-label fw-semibold fs-6">Jenis Layanan Operasi</label>
                            <div class="row g-4">
                                <div class="col-6">
                                    <label class="btn btn-outline btn-outline-dashed btn-active-light-danger d-flex flex-stack text-start p-4 w-100 {{ old('jenis_layanan', $laporan->jenis_layanan) === 'darurat' ? 'active' : '' }}" id="labelLayananDarurat">
                                        <div class="d-flex align-items-center me-2">
                                            <div class="form-check form-check-custom form-check-solid form-check-danger me-3">
                                                <input class="form-check-input" type="radio" name="jenis_layanan" value="darurat" id="layananDarurat" {{ old('jenis_layanan', $laporan->jenis_layanan) === 'darurat' ? 'checked' : '' }} onchange="sinkronisasiJenisLayanan()" />
                                            </div>
                                            <div class="flex-grow-1">
                                                <h4 class="d-flex align-items-center fs-6 fw-bold mb-0 text-danger">
                                                    <i class="las la-fire fs-3 me-2 text-danger"></i>
                                                    Kebakaran
                                                </h4>
                                                <div class="text-muted fs-8">Insiden Darurat Kebakaran</div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <div class="col-6">
                                    <label class="btn btn-outline btn-outline-dashed btn-active-light-primary d-flex flex-stack text-start p-4 w-100 {{ old('jenis_layanan', $laporan->jenis_layanan) === 'non_darurat' ? 'active' : '' }}" id="labelLayananRescue">
                                        <div class="d-flex align-items-center me-2">
                                            <div class="form-check form-check-custom form-check-solid form-check-primary me-3">
                                                <input class="form-check-input" type="radio" name="jenis_layanan" value="non_darurat" id="layananRescue" {{ old('jenis_layanan', $laporan->jenis_layanan) === 'non_darurat' ? 'checked' : '' }} onchange="sinkronisasiJenisLayanan()" />
                                            </div>
                                            <div class="flex-grow-1">
                                                <h4 class="d-flex align-items-center fs-6 fw-bold mb-0 text-primary">
                                                    <i class="ki-duotone ki-rescue fs-3 me-2 text-primary"><span class="path1"></span><span class="path2"></span></i>
                                                    Rescue
                                                </h4>
                                                <div class="text-muted fs-8">Evakuasi & Penyelamatan</div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- 2. Waktu Kejadian & Status Operasi Lapangan --}}
                        <div class="row g-4 mb-6">
                            <div class="col-md-6">
                                <label class="required form-label fw-semibold fs-6">Tanggal & Waktu Mulai</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="ki-duotone ki-calendar fs-2 text-primary"><span class="path1"></span><span class="path2"></span></i></span>
                                    <input type="datetime-local" name="tanggal_waktu_kejadian" class="form-control form-control-solid @error('tanggal_waktu_kejadian') is-invalid @enderror" value="{{ old('tanggal_waktu_kejadian', $laporan->tanggal_waktu_kejadian ? \Carbon\Carbon::parse($laporan->tanggal_waktu_kejadian)->format('Y-m-d\TH:i') : '') }}" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="required form-label fw-semibold fs-6">Status Operasi Lapangan</label>
                                <select name="status_operasi" id="selectStatusOperasi" class="form-select form-select-solid @error('status_operasi') is-invalid @enderror" required onchange="handleStatusOperasiChange()">
                                    <option value="dalam_penanganan" {{ old('status_operasi', $laporan->status_operasi ?? 'dalam_penanganan') === 'dalam_penanganan' ? 'selected' : '' }}>
                                        🟡 Sedang Dalam Penanganan
                                    </option>
                                    <option value="selesai" {{ old('status_operasi', $laporan->status_operasi ?? 'dalam_penanganan') === 'selesai' ? 'selected' : '' }}>
                                        🟢 Selesai (Padam / Tertangani)
                                    </option>
                                </select>
                            </div>
                        </div>

                        {{-- Waktu Penanganan Selesai --}}
                        <div class="mb-6" id="wrapperWaktuSelesai" style="{{ old('status_operasi', $laporan->status_operasi ?? 'dalam_penanganan') === 'selesai' ? '' : 'display: none;' }}">
                            <label class="form-label fw-semibold fs-6 text-success" id="labelWaktuSelesai">Waktu Penanganan Selesai</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light-success text-success border-0"><i class="ki-duotone ki-check-circle fs-2 text-success"><span class="path1"></span><span class="path2"></span></i></span>
                                <input type="datetime-local" name="tanggal_waktu_selesai" id="inputWaktuSelesai" class="form-control form-control-solid @error('tanggal_waktu_selesai') is-invalid @enderror" value="{{ old('tanggal_waktu_selesai', $laporan->tanggal_waktu_selesai ? \Carbon\Carbon::parse($laporan->tanggal_waktu_selesai)->format('Y-m-d\TH:i') : '') }}" />
                            </div>
                            <div class="text-muted fs-8 mt-1">Wajib diisi saat status operasi telah dinyatakan Selesai.</div>
                        </div>

                        {{-- 3. Kecamatan Lokasi --}}
                        <div class="mb-6">
                            <label class="required form-label fw-semibold fs-6">Kecamatan Lokasi Kejadian</label>
                            <select name="kecamatan_id" class="form-select form-select-solid @error('kecamatan_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Kecamatan di Purwakarta --</option>
                                @foreach ($kecamatans as $kec)
                                    <option value="{{ $kec->id }}" {{ old('kecamatan_id', $laporan->kecamatan_id) == $kec->id ? 'selected' : '' }}>
                                        Kec. {{ $kec->nama_kecamatan }} ({{ $kec->zonaLayanan->nama_pos ?? 'Zona Pos' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 4. Kategori Objek --}}
                        <div class="mb-6">
                            <label class="required form-label fw-semibold fs-6" id="labelKategoriObjek">Kategori Objek Terbakar</label>
                            <select name="kategori_objek_id" id="selectKategoriObjek" class="form-select form-select-solid @error('kategori_objek_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($kategoriObjek as $obj)
                                    <option value="{{ $obj->id }}" 
                                            data-jenis="{{ $obj->jenis_layanan }}" 
                                            {{ old('kategori_objek_id', $laporan->kategori_objek_id) == $obj->id ? 'selected' : '' }}>
                                        {{ $obj->nama_kategori }} {{ $obj->is_karhutla ? '(Karhutla)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="text-muted fs-8 mt-1" id="kategoriHelperText">Pilihan kategori otomatis disesuaikan dengan jenis layanan di atas.</div>
                        </div>

                        {{-- 5. Dugaan Penyebab (Khusus Kebakaran) --}}
                        <div class="mb-2" id="wrapperPenyebab">
                            <label class="form-label fw-semibold fs-6">Dugaan Penyebab Kebakaran</label>
                            <select name="kategori_penyebab_id" id="selectPenyebab" class="form-select form-select-solid @error('kategori_penyebab_id') is-invalid @enderror">
                                <option value="">-- Pilih Dugaan Penyebab --</option>
                                @foreach ($kategoriPenyebab as $penyebab)
                                    <option value="{{ $penyebab->id }}" {{ old('kategori_penyebab_id', $laporan->kategori_penyebab_id) == $penyebab->id ? 'selected' : '' }}>
                                        {{ $penyebab->nama_penyebab }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="text-muted fs-8 mt-1" id="penyebabHelperText">*Wajib untuk insiden kebakaran; dinonaktifkan otomatis untuk operasi penyelamatan (rescue).</div>
                        </div>

                    </div>
                </div>

                {{-- Card Data Korban & Dampak Sosial --}}
                <div class="card card-flush shadow-sm mb-7">
                    <div class="card-header pt-6">
                        <div class="card-title">
                            <i class="ki-duotone ki-heart-circle fs-2 text-danger me-2"><span class="path1"></span><span class="path2"></span></i>
                            <h3 class="fw-bold m-0 fs-5">Data Korban & Dampak Sosial</h3>
                        </div>
                    </div>
                    <div class="card-body pt-2">
                        <div class="text-muted fs-7 mb-5">
                            Pencatatan rincian korban manusia dan warga terdampak di lokasi insiden. Masukkan angka 0 jika nihil.
                        </div>

                        {{-- Baris 1: Korban Jiwa & Cedera --}}
                        <div class="row g-4 mb-5">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold fs-7 text-gray-700">Luka Ringan</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light-warning text-warning border-0 fw-bold">
                                        <i class="ki-duotone ki-bandage fs-5 text-warning"><span class="path1"></span><span class="path2"></span></i>
                                    </span>
                                    <input type="number" name="korban_luka_ringan" class="form-control form-control-solid @error('korban_luka_ringan') is-invalid @enderror" value="{{ old('korban_luka_ringan', $laporan->korban_luka_ringan ?? 0) }}" min="0" placeholder="0" />
                                    <span class="input-group-text bg-light border-0 fs-8 text-gray-600">Jiwa</span>
                                </div>
                                @error('korban_luka_ringan')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold fs-7 text-gray-700">Luka Berat</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light-danger text-danger border-0 fw-bold">
                                        <i class="ki-duotone ki-cross-circle fs-5 text-danger"><span class="path1"></span><span class="path2"></span></i>
                                    </span>
                                    <input type="number" name="korban_luka_berat" class="form-control form-control-solid @error('korban_luka_berat') is-invalid @enderror" value="{{ old('korban_luka_berat', $laporan->korban_luka_berat ?? 0) }}" min="0" placeholder="0" />
                                    <span class="input-group-text bg-light border-0 fs-8 text-gray-600">Jiwa</span>
                                </div>
                                @error('korban_luka_berat')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold fs-7 text-danger">Meninggal</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-danger text-white border-0 fw-bold">
                                        <i class="ki-duotone ki-user-cross fs-5 text-white"><span class="path1"></span><span class="path2"></span></i>
                                    </span>
                                    <input type="number" name="korban_meninggal" class="form-control form-control-solid text-danger fw-bold @error('korban_meninggal') is-invalid @enderror" value="{{ old('korban_meninggal', $laporan->korban_meninggal ?? 0) }}" min="0" placeholder="0" />
                                    <span class="input-group-text bg-light border-0 fs-8 text-gray-600">Jiwa</span>
                                </div>
                                @error('korban_meninggal')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Baris 2: Populasi Terdampak --}}
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold fs-7 text-gray-700">KK Terdampak</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light-primary text-primary border-0 fw-bold">
                                        <i class="ki-duotone ki-home fs-5 text-primary"><span class="path1"></span><span class="path2"></span></i>
                                    </span>
                                    <input type="number" name="kk_terdampak" class="form-control form-control-solid @error('kk_terdampak') is-invalid @enderror" value="{{ old('kk_terdampak', $laporan->kk_terdampak ?? 0) }}" min="0" placeholder="0" />
                                    <span class="input-group-text bg-light border-0 fs-8 text-gray-600">KK</span>
                                </div>
                                @error('kk_terdampak')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold fs-7 text-gray-700">Total Jiwa Terdampak</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light-info text-info border-0 fw-bold">
                                        <i class="ki-duotone ki-people fs-5 text-info"><span class="path1"></span><span class="path2"></span></i>
                                    </span>
                                    <input type="number" name="jiwa_terdampak" class="form-control form-control-solid @error('jiwa_terdampak') is-invalid @enderror" value="{{ old('jiwa_terdampak', $laporan->jiwa_terdampak ?? 0) }}" min="0" placeholder="0" />
                                    <span class="input-group-text bg-light border-0 fs-8 text-gray-600">Jiwa</span>
                                </div>
                                @error('jiwa_terdampak')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Kolom Kanan: Operasional, Kerugian & Deskripsi --}}
            <div class="col-lg-5">
                
                {{-- Card Operasional & Estimasi Dampak --}}
                <div class="card card-flush shadow-sm mb-7">
                    <div class="card-header pt-6">
                        <div class="card-title">
                            <i class="ki-duotone ki-shield-tick fs-2 text-success me-2"><span class="path1"></span><span class="path2"></span></i>
                            <h3 class="fw-bold m-0 fs-5">Operasional & Estimasi Dampak</h3>
                        </div>
                    </div>
                    <div class="card-body pt-2">
                        
                        {{-- Jumlah Personel --}}
                        <div class="mb-5">
                            <label class="form-label fw-semibold fs-6">Jumlah Personel Diterjunkan</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="ki-duotone ki-profile-circle fs-2 text-gray-600"><span class="path1"></span><span class="path2"></span></i></span>
                                <input type="number" name="jumlah_personel" class="form-control form-control-solid" value="{{ old('jumlah_personel', $laporan->jumlah_personel) }}" min="0" placeholder="0" />
                                <span class="input-group-text bg-light border-0">Orang</span>
                            </div>
                        </div>

                        {{-- Taksiran Kerugian --}}
                        <div class="mb-5">
                            <label class="form-label fw-semibold fs-6">Taksiran Kerugian Material</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0 fw-bold text-gray-700">Rp</span>
                                <input type="number" name="taksiran_kerugian" class="form-control form-control-solid" value="{{ old('taksiran_kerugian', (int)$laporan->taksiran_kerugian) }}" min="0" step="100000" placeholder="0" />
                            </div>
                            <div class="text-muted fs-8 mt-1">Estimasi kerugian materiil akibat kejadian (Rp)</div>
                        </div>

                        {{-- Taksiran Nilai Terselamatkan --}}
                        <div class="mb-5">
                            <label class="form-label fw-semibold fs-6">Taksiran Nilai Terselamatkan</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0 fw-bold text-gray-700">Rp</span>
                                <input type="number" name="taksiran_terselamatkan" class="form-control form-control-solid" value="{{ old('taksiran_terselamatkan', (int)$laporan->taksiran_terselamatkan) }}" min="0" step="100000" placeholder="0" />
                            </div>
                            <div class="text-muted fs-8 mt-1">Estimasi aset yang berhasil diselamatkan</div>
                        </div>

                        {{-- Koordinat GPS Opsional --}}
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold fs-7">Latitude (Opsional)</label>
                                <input type="text" name="latitude" class="form-control form-control-sm form-control-solid" value="{{ old('latitude', $laporan->latitude) }}" placeholder="-6.556" />
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold fs-7">Longitude (Opsional)</label>
                                <input type="text" name="longitude" class="form-control form-control-sm form-control-solid" value="{{ old('longitude', $laporan->longitude) }}" placeholder="107.442" />
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Card Deskripsi & Kronologi --}}
                <div class="card card-flush shadow-sm mb-7">
                    <div class="card-header pt-6">
                        <div class="card-title">
                            <i class="ki-duotone ki-note fs-2 text-warning me-2"><span class="path1"></span><span class="path2"></span></i>
                            <h3 class="fw-bold m-0 fs-5">Kronologi & Catatan Lapangan</h3>
                        </div>
                    </div>
                    <div class="card-body pt-2">
                        <div class="mb-4">
                            <textarea name="deskripsi" class="form-control form-control-solid" rows="4" placeholder="Tuliskan kronologi singkat, alamat lengkap/patokan kejadian, kendala di lapangan, atau catatan penting lainnya...">{{ old('deskripsi', $laporan->deskripsi) }}</textarea>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="d-flex justify-content-end gap-3">
                            <a href="{{ route('petugas.kejadian.index') }}" class="btn btn-sm btn-light">Batal</a>
                            <button type="submit" class="btn btn-sm btn-primary d-flex align-items-center" id="btnSubmit">
                                <i class="ki-duotone ki-check fs-3 me-1"><span class="path1"></span><span class="path2"></span></i>
                                <span>Perbarui Laporan</span>
                            </button>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </form>

</div>

<script>
/**
 * Logika Dinamisasi Kategori Objek & Dugaan Penyebab
 */
function sinkronisasiJenisLayanan() {
    const isDarurat = document.getElementById('layananDarurat').checked;
    const targetJenis = isDarurat ? 'kebakaran' : 'rescue';

    const labelDarurat = document.getElementById('labelLayananDarurat');
    const labelRescue  = document.getElementById('labelLayananRescue');
    if (isDarurat) {
        labelDarurat.classList.add('active');
        labelRescue.classList.remove('active');
    } else {
        labelRescue.classList.add('active');
        labelDarurat.classList.remove('active');
    }

    const labelKategori = document.getElementById('labelKategoriObjek');
    labelKategori.innerText = isDarurat ? 'Kategori Objek Terbakar' : 'Jenis Operasi Penyelamatan (Rescue)';

    const selectObjek = document.getElementById('selectKategoriObjek');
    const options = selectObjek.querySelectorAll('option');
    let selectedStillValid = false;

    options.forEach(opt => {
        if (!opt.value) return;
        const optJenis = opt.getAttribute('data-jenis');
        if (optJenis === targetJenis) {
            opt.style.display = '';
            opt.disabled = false;
            if (opt.selected) selectedStillValid = true;
        } else {
            opt.style.display = 'none';
            opt.disabled = true;
        }
    });

    if (!selectedStillValid && selectObjek.value) {
        selectObjek.value = '';
    }

    const wrapperPenyebab = document.getElementById('wrapperPenyebab');
    const selectPenyebab  = document.getElementById('selectPenyebab');

    if (isDarurat) {
        wrapperPenyebab.style.opacity = '1';
        selectPenyebab.disabled = false;
    } else {
        wrapperPenyebab.style.opacity = '0.4';
        selectPenyebab.disabled = true;
        selectPenyebab.value = '';
    }
}

/**
 * Logika Sinkronisasi Status Operasi & Waktu Penanganan Selesai
 */
function handleStatusOperasiChange() {
    const statusSelect = document.getElementById('selectStatusOperasi');
    const wrapper = document.getElementById('wrapperWaktuSelesai');
    const input = document.getElementById('inputWaktuSelesai');
    if (!statusSelect || !wrapper || !input) return;

    if (statusSelect.value === 'selesai') {
        wrapper.style.display = 'block';
        input.disabled = false;
        input.required = true;
        if (!input.value) {
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            input.value = `${year}-${month}-${day}T${hours}:${minutes}`;
        }
    } else {
        wrapper.style.display = 'none';
        input.disabled = true;
        input.required = false;
        input.value = '';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    sinkronisasiJenisLayanan();
    handleStatusOperasiChange();
});
</script>
@endsection
