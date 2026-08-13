<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class PetaniTemplateExport implements FromArray, WithHeadings, WithTitle
{
    public function array(): array
    {
        return [
            [
                '7402123456789001',
                'Budi Santoso',
                'Mekar Sari',
                '081234567890',
                'Dusun I RT 01',
                '1.5'
            ],
            [
                '7402123456789002',
                'Siti Aminah',
                'Mekar Sari',
                '085211223344',
                'Dusun II RT 03',
                '0.75'
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'NIK',
            'Nama',
            'Kelompok Tani',
            'Telepon',
            'Alamat',
            'Luas Lahan'
        ];
    }

    public function title(): string
    {
        return 'Template Import Petani';
    }
}
