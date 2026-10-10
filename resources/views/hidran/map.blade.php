@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css">
<style>
  #map { height: min(65vh, 680px); min-height: 400px; width: 100%; }
  #legend { display: flex; flex-wrap: wrap; gap: 16px; font-size: 12.5px; }
  #legend span { display: inline-flex; align-items: center; gap: 6px; }
  .dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; }
  .dot-optimal { background: #0f6e56; }
  .dot-rusak { background: #a32d2d; }
  .leaflet-popup-content b { display: block; margin-bottom: 2px; }
  .status-optimal { color: #0f6e56; font-weight: 600; }
  .status-rusak { color: #a32d2d; font-weight: 600; }
  #state { font-size: 13px; }
  #result { display: none; }
  @media (max-width: 575.98px) {
    #map { min-height: 360px; height: 55vh; }
  }
</style>
@endpush

@section('content')
<div class="container-fluid py-5">
  <div class="d-flex flex-wrap flex-stack mb-6">
    <div>
      <h3 class="fw-bolder text-gray-900 mb-1">Peta Hidran</h3>
      <div class="text-muted fs-7">Lokasi dan kondisi hidran Damkar Purwakarta</div>
    </div>
    <button id="btn-nearest" class="btn btn-primary d-flex align-items-center mt-3 mt-sm-0">
      <i class="ki-duotone ki-geolocation fs-3 me-2"><span class="path1"></span><span class="path2"></span></i>
      Cari hidran terdekat
    </button>
  </div>

  <div class="card card-flush border border-gray-300 shadow-sm">
    <div class="card-header align-items-center py-4">
      <div class="card-title">
        <h4 class="fw-bold text-gray-800 mb-0">Sebaran Hidran</h4>
      </div>
      <div class="card-toolbar">
        <div id="legend" class="text-gray-600">
          <span><i class="dot dot-optimal"></i> Optimal</span>
          <span><i class="dot dot-rusak"></i> Rusak</span>
        </div>
      </div>
    </div>
    <div class="card-body pt-0">
      <div id="map" class="rounded"></div>
      <div id="result" class="alert alert-light-primary border border-primary border-dashed mt-4 mb-0"></div>
      <div id="state" class="text-muted mt-3"></div>
    </div>
  </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>
<script>
  var map = L.map('map').setView([-6.5569, 107.4470], 14);

  L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors',
    maxZoom: 19
  }).addTo(map);

  var stateEl = document.getElementById('state');

  function markerIcon(status) {
    var color = status === 'optimal' ? '#0f6e56' : '#a32d2d';
    return L.divIcon({
      className: '',
      html: '<div style="width:16px;height:16px;border-radius:50%;background:' + color + ';border:2px solid #fff;box-shadow:0 0 2px rgba(0,0,0,.4)"></div>',
      iconSize: [16, 16],
      iconAnchor: [8, 8]
    });
  }

  fetch('{{ route('hidran.data') }}')
    .then(function (res) {
      if (!res.ok) throw new Error('Gagal memuat data (' + res.status + ')');
      return res.json();
    })
    .then(function (data) {
      if (!data.length) {
        stateEl.textContent = 'Belum ada data hidran.';
        return;
      }
      data.forEach(function (h) {
        var jenisLabel = h.jenis === 'perusahaan' ? 'Perusahaan' : 'Plat merah';
        var statusLabel = h.status === 'optimal' ? 'Optimal' : 'Rusak';
        var popupHtml =
          '<b>' + h.nama + '</b>' +
          (h.alamat ? h.alamat + '<br>' : '') +
          'Jenis: ' + jenisLabel + '<br>' +
          'Status: <span class="status-' + h.status + '">' + statusLabel + '</span>' +
          (h.catatan ? '<br><span style="color:#6b7280">' + h.catatan + '</span>' : '');

        L.marker([h.lat, h.lng], { icon: markerIcon(h.status) }).addTo(map).bindPopup(popupHtml);
      });
    })
    .catch(function (err) {
      stateEl.textContent = 'Gagal memuat data hidran: ' + err.message;
    });

  // ---- Cari hidran terdekat + rute tercepat ----
  var btnNearest = document.getElementById('btn-nearest');
  var resultEl = document.getElementById('result');
  var userMarker = null;
  var routeLine = null;

  btnNearest.addEventListener('click', function () {
    if (!navigator.geolocation) {
      resultEl.style.display = 'block';
      resultEl.textContent = 'Browser ini tidak mendukung Geolocation.';
      return;
    }

    btnNearest.disabled = true;
    btnNearest.textContent = 'Mencari lokasi...';

    navigator.geolocation.getCurrentPosition(
      function (pos) {
        var lat = pos.coords.latitude;
        var lng = pos.coords.longitude;
        cariHidranTerdekat(lat, lng);
      },
      function () {
        btnNearest.disabled = false;
        btnNearest.textContent = 'Cari hidran terdekat';
        resultEl.style.display = 'block';
        resultEl.textContent = 'Gagal mengambil lokasi. Pastikan izin lokasi diaktifkan.';
      }
    );
  });

  function cariHidranTerdekat(lat, lng) {
    btnNearest.textContent = 'Menghitung hidran terdekat...';

    // 1. Tandai lokasi petugas di peta
    if (userMarker) map.removeLayer(userMarker);
    userMarker = L.marker([lat, lng], {
      icon: L.divIcon({
        className: '',
        html: '<div style="width:14px;height:14px;border-radius:50%;background:#1e2761;border:2px solid #fff;box-shadow:0 0 3px rgba(0,0,0,.5)"></div>',
        iconSize: [14, 14],
        iconAnchor: [7, 7]
      })
    }).addTo(map).bindPopup('Lokasi kamu').openPopup();

    // 2. Minta daftar hidran terurut jarak (Haversine, dihitung di backend Laravel)
    fetch('{{ route('hidran.nearest') }}?lat=' + encodeURIComponent(lat) + '&lng=' + encodeURIComponent(lng))
      .then(function (res) {
        if (!res.ok) throw new Error('Gagal menghitung hidran terdekat');
        return res.json();
      })
      .then(function (list) {
        if (!list.length) throw new Error('Belum ada data hidran');
        var terdekat = list[0];
        ambilRute(lat, lng, terdekat);
      })
      .catch(function (err) {
        btnNearest.disabled = false;
        btnNearest.textContent = 'Cari hidran terdekat';
        resultEl.style.display = 'block';
        resultEl.textContent = err.message;
      });
  }

  function ambilRute(lat, lng, hidran) {
    btnNearest.textContent = 'Mengambil rute...';

    // 3. Panggil OSRM untuk rute jalan sesungguhnya (bukan garis lurus)
    var url = 'https://router.project-osrm.org/route/v1/driving/'
      + lng + ',' + lat + ';' + hidran.lng + ',' + hidran.lat
      + '?overview=full&geometries=geojson';

    fetch(url)
      .then(function (res) { return res.json(); })
      .then(function (data) {
        btnNearest.disabled = false;
        btnNearest.textContent = 'Cari hidran terdekat';

        if (!data.routes || !data.routes.length) {
          throw new Error('Rute tidak ditemukan');
        }

        var route = data.routes[0];
        var coords = route.geometry.coordinates.map(function (c) { return [c[1], c[0]]; });

        if (routeLine) map.removeLayer(routeLine);
        routeLine = L.polyline(coords, { color: '#c1272d', weight: 4 }).addTo(map);
        map.fitBounds(routeLine.getBounds(), { padding: [30, 30] });

        var jarakKm = (route.distance / 1000).toFixed(2);
        var menit = Math.round(route.duration / 60);

        resultEl.style.display = 'block';
        resultEl.innerHTML =
          'Hidran terdekat: <b>' + hidran.nama + '</b><br>' +
          'Jarak tempuh: <b>' + jarakKm + ' km</b> · Estimasi waktu: <b>' + menit + ' menit</b>';
      })
      .catch(function (err) {
        btnNearest.disabled = false;
        btnNearest.textContent = 'Cari hidran terdekat';
        resultEl.style.display = 'block';
        resultEl.textContent = 'Gagal mengambil rute: ' + err.message;
      });
  }
</script>
@endpush
@endsection