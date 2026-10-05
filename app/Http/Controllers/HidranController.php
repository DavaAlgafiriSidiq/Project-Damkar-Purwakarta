<?php

namespace App\Http\Controllers;

use App\Models\Hidran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HidranController extends Controller
{
    /**
     * Tampilkan halaman peta hidran.
     */
    public function index(): View
    {
        return view('hidran.map');
    }

    /**
     * Endpoint JSON yang dipanggil Leaflet lewat fetch() di frontend.
     */
    public function apiIndex(): JsonResponse
    {
        return response()->json(Hidran::all());
    }

    /**
     * Terima lokasi petugas (lat, lng), kembalikan seluruh hidran
     * terurut dari yang terdekat, lengkap dengan jarak dalam km.
     */
    public function nearest(Request $request): JsonResponse
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $lat = (float) $request->query('lat');
        $lng = (float) $request->query('lng');

        $hidrans = Hidran::all()
            ->map(function (Hidran $h) use ($lat, $lng) {
                $h->jarak_km = $this->haversine($lat, $lng, $h->lat, $h->lng);
                return $h;
            })
            ->sortBy('jarak_km')
            ->values();

        return response()->json($hidrans);
    }

    /**
     * Jarak garis lurus antara dua titik koordinat (rumus Haversine), dalam km.
     */
    private function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadiusKm = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadiusKm * $c, 3);
    }
}