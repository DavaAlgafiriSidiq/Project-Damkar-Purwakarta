@extends('layouts.app')

@section('content')
<div class="container-xxl">

    {{-- Toolbar / Breadcrumb --}}
    <div class="d-flex flex-wrap flex-stack pb-7">
        <div class="d-flex flex-column justify-content-center my-1">
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                Input Laporan Kejadian Lapangan
            </h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('petugas.kejadian.index') }}" class="text-muted text-hover-primary">Petugas Lapangan</a>
                </li>
                <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
                <li class="breadcrumb-item text-dark">Form Input Baru</li>
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

    {{-- Form Input Kejadian --}}
    <form action="{{ route('petugas.kejadian.store') }}" method="POST" id="formInputKejadian">
        @csrf

        <div class="row g-7">
            
            {{-- Kolom Kiri: Klasifikasi & Lokasi --}}
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
                                    <label class="btn btn-outline btn-outline-dashed btn-active-light-danger d-flex flex-stack text-start p-4 w-100 {{ old('jenis_layanan', 'darurat') === 'darurat' ? 'active' : '' }}" id="labelLayananDarurat">
                                        <div class="d-flex align-items-center me-2">
                                            <div class="form-check form-check-custom form-check-solid form-check-danger me-3">
                                                <input class="form-check-input" type="radio" name="jenis_layanan" value="darurat" id="layananDarurat" {{ old('jenis_layanan', 'darurat') === 'darurat' ? 'checked' : '' }} onchange="sinkronisasiJenisLayanan()" />
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
                                    <label class="btn btn-outline btn-outline-dashed btn-active-light-primary d-flex flex-stack text-start p-4 w-100 {{ old('jenis_layanan') === 'non_darurat' ? 'active' : '' }}" id="labelLayananRescue">
                                        <div class="d-flex align-items-center me-2">
                                            <div class="form-check form-check-custom form-check-solid form-check-primary me-3">
                                                <input class="form-check-input" type="radio" name="jenis_layanan" value="non_darurat" id="layananRescue" {{ old('jenis_layanan') === 'non_darurat' ? 'checked' : '' }} onchange="sinkronisasiJenisLayanan()" />
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

                        {{-- 2. Tanggal & Waktu Kejadian --}}
                        <div class="mb-6">
                            <label class="required form-label fw-semibold fs-6">Tanggal & Waktu Kejadian</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="ki-duotone ki-calendar fs-2 text-primary"><span class="path1"></span><span class="path2"></span></i></span>
                                <input type="datetime-local" name="tanggal_waktu_kejadian" class="form-control form-control-solid @error('tanggal_waktu_kejadian') is-invalid @enderror" value="{{ old('tanggal_waktu_kejadian', now()->format('Y-m-d\TH:i')) }}" required />
                            </div>
                        </div>

                        {{-- 3. Kecamatan Lokasi --}}
                        <div class="mb-6">
                            <label class="required form-label fw-semibold fs-6">Kecamatan Lokasi Kejadian</label>
                            <select name="kecamatan_id" class="form-select form-select-solid @error('kecamatan_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Kecamatan di Purwakarta --</option>
                                @foreach ($kecamatans as $kec)
                                    <option value="{{ $kec->id }}" {{ old('kecamatan_id') == $kec->id ? 'selected' : '' }}>
                                        Kec. {{ $kec->nama_kecamatan }} ({{ $kec->zonaLayanan->nama_pos ?? 'Zona Pos' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 4. Kategori Objek (DINAMIS SINKRON DENGAN JENIS LAYANAN) --}}
                        <div class="mb-6">
                            <label class="required form-label fw-semibold fs-6" id="labelKategoriObjek">Kategori Objek Terbakar</label>
                            <select name="kategori_objek_id" id="selectKategoriObjek" class="form-select form-select-solid @error('kategori_objek_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($kategoriObjek as $obj)
                                    <option value="{{ $obj->id }}" 
                                            data-jenis="{{ $obj->jenis_layanan }}" 
                                            {{ old('kategori_objek_id') == $obj->id ? 'selected' : '' }}>
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
                                    <option value="{{ $penyebab->id }}" {{ old('kategori_penyebab_id') == $penyebab->id ? 'selected' : '' }}>
                                        {{ $penyebab->nama_penyebab }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="text-muted fs-8 mt-1" id="penyebabHelperText">*Wajib untuk insiden kebakaran; dinonaktifkan otomatis untuk operasi penyelamatan (rescue).</div>
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
                                <input type="number" name="jumlah_personel" class="form-control form-control-solid" value="{{ old('jumlah_personel', 6) }}" min="0" placeholder="0" />
                                <span class="input-group-text bg-light border-0">Orang</span>
                            </div>
                        </div>

                        {{-- Taksiran Kerugian --}}
                        <div class="mb-5">
                            <label class="form-label fw-semibold fs-6">Taksiran Kerugian Material</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0 fw-bold text-gray-700">Rp</span>
                                <input type="number" name="taksiran_kerugian" class="form-control form-control-solid" value="{{ old('taksiran_kerugian', 0) }}" min="0" step="100000" placeholder="0" />
                            </div>
                            <div class="text-muted fs-8 mt-1">Estimasi kerugian materiil akibat kejadian (Rp)</div>
                        </div>

                        {{-- Taksiran Nilai Terselamatkan --}}
                        <div class="mb-5">
                            <label class="form-label fw-semibold fs-6">Taksiran Nilai Terselamatkan</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0 fw-bold text-gray-700">Rp</span>
                                <input type="number" name="taksiran_terselamatkan" class="form-control form-control-solid" value="{{ old('taksiran_terselamatkan', 0) }}" min="0" step="100000" placeholder="0" />
                            </div>
                            <div class="text-muted fs-8 mt-1">Estimasi aset yang berhasil diselamatkan</div>
                        </div>

                        {{-- Koordinat GPS Opsional --}}
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold fs-7">Latitude (Opsional)</label>
                                <input type="text" name="latitude" class="form-control form-control-sm form-control-solid" value="{{ old('latitude') }}" placeholder="-6.556" />
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold fs-7">Longitude (Opsional)</label>
                                <input type="text" name="longitude" class="form-control form-control-sm form-control-solid" value="{{ old('longitude') }}" placeholder="107.442" />
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
                            <textarea name="deskripsi" class="form-control form-control-solid" rows="4" placeholder="Tuliskan kronologi singkat, alamat lengkap/patokan kejadian, kendala di lapangan, atau catatan penting lainnya...">{{ old('deskripsi') }}</textarea>
                        </div>

                        <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-4 mb-5">
                            <i class="ki-duotone ki-information fs-2tx text-warning me-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                            <div class="d-flex flex-stack flex-grow-1">
                                <div class="fw-semibold">
                                    <div class="fs-7 text-gray-700">Laporan otomatis disimpan sebagai <strong>Draft</strong> dan masuk antrean verifikasi Admin Damkar sebelum tayang di dashboard publik.</div>
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="d-flex justify-content-end gap-3">
                            <a href="{{ route('petugas.kejadian.index') }}" class="btn btn-sm btn-light">Batal</a>
                            <button type="submit" class="btn btn-sm btn-primary d-flex align-items-center" id="btnSubmit">
                                <i class="ki-duotone ki-check fs-3 me-1"><span class="path1"></span><span class="path2"></span></i>
                                <span>Simpan Laporan (Draft)</span>
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
 * Menyesuaikan opsi kategori objek berdasarkan jenis layanan yang dipilih.
 */
function sinkronisasiJenisLayanan() {
    const isDarurat = document.getElementById('layananDarurat').checked;
    const targetJenis = isDarurat ? 'kebakaran' : 'rescue';

    // Update active visual state pada radio buttons
    const labelDarurat = document.getElementById('labelLayananDarurat');
    const labelRescue  = document.getElementById('labelLayananRescue');
    if (isDarurat) {
        labelDarurat.classList.add('active');
        labelRescue.classList.remove('active');
    } else {
        labelRescue.classList.add('active');
        labelDarurat.classList.remove('active');
    }

    // Ubah label judul kategori objek
    const labelKategori = document.getElementById('labelKategoriObjek');
    labelKategori.innerText = isDarurat ? 'Kategori Objek Terbakar' : 'Jenis Operasi Penyelamatan (Rescue)';

    // Filter dropdown Kategori Objek
    const selectObjek = document.getElementById('selectKategoriObjek');
    const options = selectObjek.querySelectorAll('option');
    let selectedStillValid = false;

    options.forEach(opt => {
        if (!opt.value) return; // Lewati opsi placeholder
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

    // Jika opsi yang sebelumnya dipilih tidak cocok dengan jenis layanan baru, reset ke placeholder
    if (!selectedStillValid && selectObjek.value) {
        selectObjek.value = '';
    }

    // Toggle Dugaan Penyebab (hanya relevan untuk Kebakaran)
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

document.addEventListener('DOMContentLoaded', function() {
    sinkronisasiJenisLayanan();
});
</script>
@endsection
