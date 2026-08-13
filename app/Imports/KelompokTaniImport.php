<?php

namespace App\Imports;

use App\Models\KelompokTani;
use App\Models\Desa;
use App\Models\Kecamatan;
use App\Models\Gapoktan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Validation\ValidationException;

class KelompokTaniImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading
{
    public function model(array $row)
    {
        $kecamatanNama = trim($row['kecamatan']);
        $desaNama = trim($row['desa']);
        
        $kecamatan = Kecamatan::where('nama', 'like', $kecamatanNama)->first();
        if (!$kecamatan) {
            throw ValidationException::withMessages([
                'kecamatan' => ["Kecamatan dengan nama '{$kecamatanNama}' tidak ditemukan."]
            ]);
        }

        $desa = Desa::where('nama', 'like', $desaNama)
            ->where('kecamatan_id', $kecamatan->id)
            ->first();

        if (!$desa) {
            throw ValidationException::withMessages([
                'desa' => ["Desa dengan nama '{$desaNama}' di Kecamatan '{$kecamatanNama}' tidak ditemukan."]
            ]);
        }

        // Check unique constraint: nama kelompok tani di desa yang sama
        $namaKelompok = trim($row['nama_kelompok_tani']);
        $exists = KelompokTani::where('nama', 'like', $namaKelompok)
            ->where('desa_id', $desa->id)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'nama_kelompok_tani' => ["Kelompok Tani dengan nama '{$namaKelompok}' sudah terdaftar di Desa '{$desaNama}'."]
            ]);
        }

        $gapoktanId = null;
        if (!empty($row['gapoktan'])) {
            $gapoktanNama = trim($row['gapoktan']);
            $gapoktan = Gapoktan::where('nama', 'like', $gapoktanNama)->first();
            if ($gapoktan) {
                $gapoktanId = $gapoktan->id;
            }
        }

        return new KelompokTani([
            'nama'        => $namaKelompok,
            'desa_id'     => $desa->id,
            'gapoktan_id' => $gapoktanId,
            'ketua'       => $row['ketua'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nama_kelompok_tani' => ['required', 'string', 'max:255'],
            'kecamatan'          => ['required', 'string'],
            'desa'               => ['required', 'string'],
            'gapoktan'           => ['nullable', 'string'],
            'ketua'              => ['nullable', 'string', 'max:255'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama_kelompok_tani.required' => 'Kolom Nama Kelompok Tani wajib diisi.',
            'kecamatan.required'          => 'Kolom Kecamatan wajib diisi.',
            'desa.required'               => 'Kolom Desa wajib diisi.',
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
