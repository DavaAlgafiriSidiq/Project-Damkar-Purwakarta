@extends('layouts.app')

@section('content')
<div class="container-xxl">

    {{-- Toolbar / Breadcrumb --}}
    <div class="d-flex flex-wrap flex-stack pb-7">
        <div class="d-flex flex-column justify-content-center my-1">
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                Koreksi & Verifikasi Data Laporan #{{ $laporan->id }}
            </h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('admin.verifikasi.index') }}" class="text-muted text-hover-primary">Admin Verifikasi</a>
                </li>
                <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
                <li class="breadcrumb-item text-dark">Koreksi Data Kejadian</li>
            </ul>
        </div>
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <a href="{{ route('admin.verifikasi.index') }}" class="btn btn-sm btn-light-primary d-flex align-items-center">
                <i class="ki-duotone ki-arrow-left fs-4 me-1"><span class="path1"></span><span class="path2"></span></i>
                <span>Kembali ke Antrean</span>
            </a>
        </div>
    </div>

    {{-- Alert Validasi Error --}}
    @if ($errors->any())
        <div class="alert alert-dismissible bg-light-danger border border-danger d-flex flex-column flex-sm-row p-5 mb-7">
            <i class="ki-duotone ki-cross-circle fs-2hx text-danger me-4 mb-5 mb-sm-0"><span class="path1"></span><span class="path2"></span></i>
            <div class="d-flex flex-column pe-0 pe-sm-10">
                <h5 class="mb-1 text-danger">Terdapat kesalahan isian:</h5>
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

    {{-- Form Edit Utama --}}
    <form action="{{ route('admin.verifikasi.update', $laporan->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-7">
            
            {{-- Kolom Kiri: Status, Klasifikasi & Lokasi --}}
            <div class="col-lg-7">
                <div class="card card-flush shadow-sm mb-7">
                    <div class="card-header pt-6">
                        <div class="card-title">
                            <i class="ki-duotone ki-file-sheet fs-2 text-primary me-2"><span class="path1"></span><span class="path2"></span></i>
                            <h3 class="fw-bold m-0 fs-5">Klasifikasi & Status Verifikasi</h3>
                        </div>
                    </div>
                    <div class="card-body pt-2">
                        
                        {{-- 1. Status Verifikasi --}}
                        <div class="mb-6 p-4 rounded bg-light border border-dashed">
                            <label class="required form-label fw-bold fs-6">Status Verifikasi Laporan</label>
                            <div class="d-flex gap-4">
                                <div class="form-check form-check-custom form-check-solid form-check-warning">
                                    <input class="form-check-input" type="radio" name="status_verifikasi" value="draft" id="statusDraft" {{ old('status_verifikasi', $laporan->status_verifikasi) === 'draft' ? 'checked' : '' }} />
                                    <label class="form-check-label fw-semibold text-dark fs-7" for="statusDraft">
                                        Draft (Menunggu Verifikasi)
                                    </label>
                                </div>
                                <div class="form-check form-check-custom form-check-solid form-check-success">
                                    <input class="form-check-input" type="radio" name="status_verifikasi" value="verified" id="statusVerified" {{ old('status_verifikasi', $laporan->status_verifikasi) === 'verified' ? 'checked' : '' }} />
                                    <label class="form-check-label fw-semibold text-dark fs-7" for="statusVerified">
                                        Terverifikasi (Aktif di Dashboard)
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- 2. Jenis Layanan --}}
                        <div class="mb-6">
                            <label class="required form-label fw-semibold fs-6">Jenis Layanan</label>
                            <select name="jenis_layanan" id="selectAdminJenisLayanan" class="form-select form-select-solid" required onchange="sinkronisasiAdminJenisLayanan()">
                                <option value="darurat" {{ old('jenis_layanan', $laporan->jenis_layanan) === 'darurat' ? 'selected' : '' }}>Kebakaran (Darurat)</option>
                                <option value="non_darurat" {{ old('jenis_layanan', $laporan->jenis_layanan) === 'non_darurat' ? 'selected' : '' }}>Penyelamatan / Rescue (Non-Darurat)</option>
                            </select>
                        </div>

                        {{-- 3. Tanggal & Waktu Kejadian --}}
                        <div class="mb-6">
                            <label class="required form-label fw-semibold fs-6">Tanggal & Waktu Kejadian</label>
                            <input type="datetime-local" name="tanggal_waktu_kejadian" class="form-control form-control-solid" value="{{ old('tanggal_waktu_kejadian', $laporan->tanggal_waktu_kejadian ? $laporan->tanggal_waktu_kejadian->format('Y-m-d\TH:i') : '') }}" required />
                        </div>

                        {{-- 4. Kecamatan Lokasi --}}
                        <div class="mb-6">
                            <label class="required form-label fw-semibold fs-6">Kecamatan Lokasi</label>
                            <select name="kecamatan_id" class="form-select form-select-solid" required>
                                <option value="">-- Pilih Kecamatan --</option>
                                @foreach ($kecamatans as $kec)
                                    <option value="{{ $kec->id }}" {{ old('kecamatan_id', $laporan->kecamatan_id) == $kec->id ? 'selected' : '' }}>
                                        Kec. {{ $kec->nama_kecamatan }} ({{ $kec->zonaLayanan->nama_pos ?? 'Pos Damkar' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 5. Kategori Objek (DINAMIS) --}}
                        <div class="mb-6">
                            <label class="required form-label fw-semibold fs-6" id="labelAdminKategoriObjek">Kategori Objek</label>
                            <select name="kategori_objek_id" id="selectAdminKategoriObjek" class="form-select form-select-solid" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($kategoriObjek as $obj)
                                    <option value="{{ $obj->id }}" 
                                            data-jenis="{{ $obj->jenis_layanan }}" 
                                            {{ old('kategori_objek_id', $laporan->kategori_objek_id) == $obj->id ? 'selected' : '' }}>
                                        {{ $obj->nama_kategori }} {{ $obj->is_karhutla ? '(Karhutla)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 6. Dugaan Penyebab --}}
                        <div class="mb-2" id="wrapperAdminPenyebab">
                            <label class="form-label fw-semibold fs-6">Dugaan Penyebab Kebakaran</label>
                            <select name="kategori_penyebab_id" id="selectAdminPenyebab" class="form-select form-select-solid">
                                <option value="">-- Pilih Dugaan Penyebab --</option>
                                @foreach ($kategoriPenyebab as $penyebab)
                                    <option value="{{ $penyebab->id }}" {{ old('kategori_penyebab_id', $laporan->kategori_penyebab_id) == $penyebab->id ? 'selected' : '' }}>
                                        {{ $penyebab->nama_penyebab }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="text-muted fs-8 mt-1">*Hanya berlaku untuk layanan Kebakaran.</div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Kolom Kanan: Operasional, Kerugian & Kronologi --}}
            <div class="col-lg-5">
                
                {{-- Operasional & Kerugian --}}
                <div class="card card-flush shadow-sm mb-7">
                    <div class="card-header pt-6">
                        <div class="card-title">
                            <i class="ki-duotone ki-shield-tick fs-2 text-success me-2"><span class="path1"></span><span class="path2"></span></i>
                            <h3 class="fw-bold m-0 fs-5">Operasional & Dampak</h3>
                        </div>
                    </div>
                    <div class="card-body pt-2">
                        
                        {{-- Jumlah Personel --}}
                        <div class="mb-5">
                            <label class="form-label fw-semibold fs-6">Jumlah Personel Diterjunkan</label>
                            <input type="number" name="jumlah_personel" class="form-control form-control-solid" value="{{ old('jumlah_personel', $laporan->jumlah_personel) }}" min="0" />
                        </div>

                        {{-- Taksiran Kerugian --}}
                        <div class="mb-5">
                            <label class="form-label fw-semibold fs-6">Taksiran Kerugian (Rp)</label>
                            <input type="number" name="taksiran_kerugian" class="form-control form-control-solid" value="{{ old('taksiran_kerugian', (int)$laporan->taksiran_kerugian) }}" min="0" step="100000" />
                        </div>

                        {{-- Taksiran Nilai Terselamatkan --}}
                        <div class="mb-5">
                            <label class="form-label fw-semibold fs-6">Nilai Terselamatkan (Rp)</label>
                            <input type="number" name="taksiran_terselamatkan" class="form-control form-control-solid" value="{{ old('taksiran_terselamatkan', (int)$laporan->taksiran_terselamatkan) }}" min="0" step="100000" />
                        </div>

                        {{-- Koordinat --}}
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold fs-7">Latitude</label>
                                <input type="text" name="latitude" class="form-control form-control-sm form-control-solid" value="{{ old('latitude', $laporan->latitude) }}" />
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold fs-7">Longitude</label>
                                <input type="text" name="longitude" class="form-control form-control-sm form-control-solid" value="{{ old('longitude', $laporan->longitude) }}" />
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Kronologi & Info Audit --}}
                <div class="card card-flush shadow-sm mb-7">
                    <div class="card-header pt-6">
                        <div class="card-title">
                            <i class="ki-duotone ki-note fs-2 text-warning me-2"><span class="path1"></span><span class="path2"></span></i>
                            <h3 class="fw-bold m-0 fs-5">Kronologi & Log Audit</h3>
                        </div>
                    </div>
                    <div class="card-body pt-2">
                        <div class="mb-4">
                            <textarea name="deskripsi" class="form-control form-control-solid" rows="4">{{ old('deskripsi', $laporan->deskripsi) }}</textarea>
                        </div>

                        <div class="p-3 bg-light rounded fs-8 text-muted mb-4 border border-dashed">
                            <div><strong>Pelapor Awal:</strong> {{ $laporan->pelapor->name ?? '-' }} ({{ $laporan->created_at->format('d/m/Y H:i') }})</div>
                            @if($laporan->diverifikasi_oleh)
                                <div><strong>Diverifikasi oleh:</strong> {{ $laporan->verifikator->name ?? '-' }} ({{ $laporan->diverifikasi_pada?->format('d/m/Y H:i') }})</div>
                            @endif
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="d-flex justify-content-end gap-3">
                            <a href="{{ route('admin.verifikasi.index') }}" class="btn btn-sm btn-light">Batal</a>
                            <button type="submit" class="btn btn-sm btn-primary d-flex align-items-center">
                                <i class="ki-duotone ki-check fs-3 me-1"><span class="path1"></span><span class="path2"></span></i>
                                <span>Simpan Perubahan</span>
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
 * Dinamisasi kategori objek pada form edit admin
 */
function sinkronisasiAdminJenisLayanan() {
    const jenisLayanan = document.getElementById('selectAdminJenisLayanan').value;
    const targetJenis = (jenisLayanan === 'darurat') ? 'kebakaran' : 'rescue';

    const labelKategori = document.getElementById('labelAdminKategoriObjek');
    labelKategori.innerText = (jenisLayanan === 'darurat') ? 'Kategori Objek Terbakar' : 'Jenis Operasi Penyelamatan (Rescue)';

    const selectObjek = document.getElementById('selectAdminKategoriObjek');
    const options = selectObjek.querySelectorAll('option');

    options.forEach(opt => {
        if (!opt.value) return;
        const optJenis = opt.getAttribute('data-jenis');
        if (optJenis === targetJenis) {
            opt.style.display = '';
            opt.disabled = false;
        } else {
            opt.style.display = 'none';
            opt.disabled = true;
        }
    });

    const wrapperPenyebab = document.getElementById('wrapperAdminPenyebab');
    const selectPenyebab  = document.getElementById('selectAdminPenyebab');

    if (jenisLayanan === 'darurat') {
        wrapperPenyebab.style.opacity = '1';
        selectPenyebab.disabled = false;
    } else {
        wrapperPenyebab.style.opacity = '0.4';
        selectPenyebab.disabled = true;
        selectPenyebab.value = '';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    sinkronisasiAdminJenisLayanan();
});
</script>
@endsection
