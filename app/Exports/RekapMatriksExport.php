<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/**
 * Class Export: RekapMatriksExport
 *
 * Meng-export seluruh lembar kerja matriks tahunan ke dalam 1 file Excel multi-sheet (.xlsx).
 * Masing-masing sheet mewakili satu kategori data operasional:
 *   1. Sheet 1: Distribusi Wilayah Kecamatan (Semua Insiden)
 *   2. Sheet 2: Operasi Pemadaman Kebakaran (Objek & Dugaan Penyebab Api)
 *   3. Sheet 3: Operasi Penyelamatan (Rescue)
 */
class RekapMatriksExport implements Export, WithMultipleSheets
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        return [
            new Sheets\RekapWilayahSheet($this->data),
            new Sheets\RekapKebakaranSheet($this->data),
            new Sheets\RekapRescueSheet($this->data),
        ];
    }
}
