<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class KelompokTaniTemplateExport implements FromArray, WithHeadings, WithTitle
{
    public function array(): array
    {
        return [
            [
                'Mekar Sari',
                'Barangka',
                'Sawerigadi',
                'Gapoktan Tani Mandiri',
                'H. Ruslan'
            ],
            [
                'Tunas Harapan',
                'Barangka',
                'Lombe',
                '',
                'Supardi'
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'Nama Kelompok Tani',
            'Kecamatan',
            'Desa',
            'Gapoktan',
            'Ketua'
        ];
    }

    public function title(): string
    {
        return 'Template Import Kelompok Tani';
    }
}
