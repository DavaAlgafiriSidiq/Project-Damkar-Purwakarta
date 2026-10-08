<table>
    <thead>
        <tr>
            <th colspan="16" style="font-size: 14pt; font-weight: bold; text-align: center;">
                DINAS PEMADAM KEBAKARAN DAN PENYELAMATAN KABUPATEN PURWAKARTA
            </th>
        </tr>
        <tr>
            <th colspan="16" style="font-size: 12pt; font-weight: bold; text-align: center;">
                REKAPITULASI KEJADIAN BERDASARKAN WILAYAH KECAMATAN TAHUN {{ $tahun }}
            </th>
        </tr>
        <tr>
            <th colspan="16" style="font-size: 10pt; text-align: center; color: #555555;">
                Zona Layanan: {{ $zonaLayanan ? $zonaLayanan : 'Seluruh Wilayah Kabupaten Purwakarta' }} &middot; Tanggal Cetak: {{ now()->translatedFormat('d F Y, H:i') }}
            </th>
        </tr>
        <tr>
            <th colspan="16"></th>
        </tr>
        <tr style="background-color: #2E75B6; color: #FFFFFF; font-weight: bold; text-align: center;">
            <th style="border: 1px solid #000000; text-align: center; width: 5;">No</th>
            <th style="border: 1px solid #000000; text-align: left; width: 25;">Kecamatan / Wilayah</th>
            <th style="border: 1px solid #000000; text-align: center; width: 15;">Zona Layanan</th>
            @foreach($namaBulan as $mNum => $mLabel)
                <th style="border: 1px solid #000000; text-align: center; width: 7;">{{ $mLabel }}</th>
            @endforeach
            <th style="border: 1px solid #000000; text-align: center; width: 12; background-color: #1F4E79; color: #FFFFFF;">TOTAL</th>
        </tr>
    </thead>
    <tbody>
        @php $noKec = 1; @endphp
        @foreach($matrixKecamatan as $kecId => $row)
            <tr>
                <td style="border: 1px solid #000000; text-align: center;">{{ $noKec++ }}</td>
                <td style="border: 1px solid #000000; text-align: left; font-weight: bold;">
                    {{ $row['nama'] }} {{ $row['nama'] === 'Luar Kabupaten' ? '(Perbantuan / Mutual Aid)' : '' }}
                </td>
                <td style="border: 1px solid #000000; text-align: center;">{{ $row['zona_layanan'] }}</td>
                @foreach($namaBulan as $mNum => $mLabel)
                    @php $val = $row['bulan'][$mNum]; @endphp
                    <td style="border: 1px solid #000000; text-align: center;">
                        {{ $val > 0 ? $val : 0 }}
                    </td>
                @endforeach
                <td style="border: 1px solid #000000; text-align: center; font-weight: bold; background-color: #D9E1F2;">
                    {{ $row['total'] }}
                </td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr style="background-color: #BDD7EE; font-weight: bold; text-align: center;">
            <td colspan="3" style="border: 1px solid #000000; text-align: right; font-weight: bold;">TOTAL KESELURUHAN (BULAN):</td>
            @foreach($namaBulan as $mNum => $mLabel)
                <td style="border: 1px solid #000000; text-align: center; font-weight: bold;">
                    {{ $totalBulanKecamatan[$mNum] }}
                </td>
            @endforeach
            <td style="border: 1px solid #000000; text-align: center; font-weight: bold; background-color: #1F4E79; color: #FFFFFF;">
                {{ $grandTotalKecamatan }}
            </td>
        </tr>
    </tfoot>
</table>
