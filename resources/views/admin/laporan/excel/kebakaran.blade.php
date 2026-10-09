<table>
    <thead>
        <tr>
            <th colspan="16" style="font-size: 14pt; font-weight: bold; text-align: center;">
                DINAS PEMADAM KEBAKARAN DAN PENYELAMATAN KABUPATEN PURWAKARTA
            </th>
        </tr>
        <tr>
            <th colspan="16" style="font-size: 12pt; font-weight: bold; text-align: center;">
                REKAPITULASI OPERASI PEMADAMAN KEBAKARAN TAHUN {{ $tahun }}
            </th>
        </tr>
        <tr>
            <th colspan="16" style="font-size: 10pt; text-align: center; color: #555555;">
                Zona Layanan: {{ $zonaLayanan ? $zonaLayanan : 'Seluruh Wilayah' }} &middot; Tanggal Cetak: {{ now()->translatedFormat('d F Y, H:i') }}
            </th>
        </tr>
        <tr>
            <th colspan="16"></th>
        </tr>

        {{-- TABEL 2.A: OBJEK KEBAKARAN --}}
        <tr>
            <th colspan="16" style="font-size: 11pt; font-weight: bold; background-color: #F8CBAD; color: #833C0C; text-align: left; padding: 4px;">
                TABEL 2.A: REKAPITULASI BERDASARKAN KATEGORI OBJEK KEBAKARAN
            </th>
        </tr>
        <tr style="background-color: #C00000; color: #FFFFFF; font-weight: bold; text-align: center;">
            <th style="border: 1px solid #000000; text-align: center; width: 5;">No</th>
            <th style="border: 1px solid #000000; text-align: left; width: 28;">Kategori Objek Kebakaran</th>
            <th style="border: 1px solid #000000; text-align: center; width: 16;">Klasifikasi</th>
            @foreach($namaBulan as $mNum => $mLabel)
                <th style="border: 1px solid #000000; text-align: center; width: 7;">{{ $mLabel }}</th>
            @endforeach
            <th style="border: 1px solid #000000; text-align: center; width: 12; background-color: #800000; color: #FFFFFF;">TOTAL</th>
        </tr>
    </thead>
    <tbody>
        @php $noObjK = 1; @endphp
        @foreach($matrixObjekKebakaran as $objId => $row)
            <tr>
                <td style="border: 1px solid #000000; text-align: center;">{{ $noObjK++ }}</td>
                <td style="border: 1px solid #000000; text-align: left; font-weight: bold;">{{ $row['nama'] }}</td>
                <td style="border: 1px solid #000000; text-align: center;">{{ $row['tipe_label'] }}</td>
                @foreach($namaBulan as $mNum => $mLabel)
                    @php $val = $row['bulan'][$mNum]; @endphp
                    <td style="border: 1px solid #000000; text-align: center;">
                        {{ $val > 0 ? $val : 0 }}
                    </td>
                @endforeach
                <td style="border: 1px solid #000000; text-align: center; font-weight: bold; background-color: #FCE4D6;">
                    {{ $row['total'] }}
                </td>
            </tr>
        @endforeach
        <tr style="background-color: #F8CBAD; font-weight: bold; text-align: center;">
            <td colspan="3" style="border: 1px solid #000000; text-align: right; font-weight: bold;">TOTAL KEBAKARAN (BULAN):</td>
            @foreach($namaBulan as $mNum => $mLabel)
                <td style="border: 1px solid #000000; text-align: center; font-weight: bold;">
                    {{ $totalBulanObjekKebakaran[$mNum] }}
                </td>
            @endforeach
            <td style="border: 1px solid #000000; text-align: center; font-weight: bold; background-color: #800000; color: #FFFFFF;">
                {{ $grandTotalObjekKebakaran }}
            </td>
        </tr>

        {{-- PEMISAH ANTAR TABEL --}}
        <tr>
            <td colspan="16"></td>
        </tr>
        <tr>
            <td colspan="16"></td>
        </tr>

        {{-- TABEL 2.B: DUGAAN PENYEBAB API --}}
        <tr>
            <th colspan="16" style="font-size: 11pt; font-weight: bold; background-color: #FFF2CC; color: #7F6000; text-align: left; padding: 4px;">
                TABEL 2.B: REKAPITULASI BERDASARKAN DUGAAN PENYEBAB API
            </th>
        </tr>
        <tr style="background-color: #ED7D31; color: #FFFFFF; font-weight: bold; text-align: center;">
            <th style="border: 1px solid #000000; text-align: center; width: 5;">No</th>
            <th style="border: 1px solid #000000; text-align: left; width: 28;">Dugaan Penyebab Api</th>
            <th style="border: 1px solid #000000; text-align: center; width: 16;">Klasifikasi</th>
            @foreach($namaBulan as $mNum => $mLabel)
                <th style="border: 1px solid #000000; text-align: center; width: 7;">{{ $mLabel }}</th>
            @endforeach
            <th style="border: 1px solid #000000; text-align: center; width: 12; background-color: #C65911; color: #FFFFFF;">TOTAL</th>
        </tr>
        @php $noPenyebab = 1; @endphp
        @foreach($matrixPenyebab as $pId => $row)
            <tr>
                <td style="border: 1px solid #000000; text-align: center;">{{ $noPenyebab++ }}</td>
                <td style="border: 1px solid #000000; text-align: left; font-weight: bold;">{{ $row['nama'] }}</td>
                <td style="border: 1px solid #000000; text-align: center;">Penyebab Api</td>
                @foreach($namaBulan as $mNum => $mLabel)
                    @php $val = $row['bulan'][$mNum]; @endphp
                    <td style="border: 1px solid #000000; text-align: center;">
                        {{ $val > 0 ? $val : 0 }}
                    </td>
                @endforeach
                <td style="border: 1px solid #000000; text-align: center; font-weight: bold; background-color: #FFF2CC;">
                    {{ $row['total'] }}
                </td>
            </tr>
        @endforeach
        <tr style="background-color: #FCE4D6; font-weight: bold; text-align: center;">
            <td colspan="3" style="border: 1px solid #000000; text-align: right; font-weight: bold;">TOTAL PENYEBAB API (BULAN):</td>
            @foreach($namaBulan as $mNum => $mLabel)
                <td style="border: 1px solid #000000; text-align: center; font-weight: bold;">
                    {{ $totalBulanPenyebab[$mNum] }}
                </td>
            @endforeach
            <td style="border: 1px solid #000000; text-align: center; font-weight: bold; background-color: #C65911; color: #FFFFFF;">
                {{ $grandTotalPenyebab }}
            </td>
        </tr>
    </tbody>
</table>
