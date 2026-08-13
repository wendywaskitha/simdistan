<?php

namespace App\Imports;

use App\Models\Petani;
use App\Models\KelompokTani;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Validation\ValidationException;

class PetaniImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading
{
    public function model(array $row)
    {
        $kelompokTaniNama = trim($row['kelompok_tani']);
        $kelompokTani = KelompokTani::where('nama', 'like', $kelompokTaniNama)->first();

        if (!$kelompokTani) {
            throw ValidationException::withMessages([
                'kelompok_tani' => ["Kelompok Tani dengan nama '{$kelompokTaniNama}' tidak ditemukan di database."]
            ]);
        }

        return new Petani([
            'nik'              => $row['nik'],
            'nama'             => $row['nama'],
            'kelompok_tani_id' => $kelompokTani->id,
            'telepon'          => $row['telepon'] ?? null,
            'alamat'           => $row['alamat'] ?? null,
            'luas_lahan'       => $row['luas_lahan'] ?? 0,
        ]);
    }

    public function rules(): array
    {
        return [
            'nik'           => ['required', 'numeric', 'digits:16', 'unique:petanis,nik'],
            'nama'          => ['required', 'string', 'max:255'],
            'kelompok_tani' => ['required', 'string'],
            'telepon'       => ['nullable'],
            'alamat'        => ['nullable'],
            'luas_lahan'    => ['nullable', 'numeric'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nik.required' => 'Kolom NIK wajib diisi.',
            'nik.digits'   => 'NIK harus berjumlah 16 digit.',
            'nik.numeric'  => 'NIK harus berupa angka.',
            'nik.unique'   => 'NIK :input sudah terdaftar sebelumnya.',
            'nama.required' => 'Kolom Nama wajib diisi.',
            'kelompok_tani.required' => 'Kolom Kelompok Tani wajib diisi.',
        ];
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
