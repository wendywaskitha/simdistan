<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AlsintanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $currentYear = (int) date('Y');
        $startYear = $currentYear - 4; // Terakhir 5 tahun termasuk tahun sekarang (misal 2022, 2023, 2024, 2025, 2026)

        return [
            'kelompok_tani_id' => 'required|exists:kelompok_tanis,id',
            'nama_ketua' => 'nullable|string|max:255',
            'nama_operator' => 'nullable|string|max:255',
            'no_hp_operator' => 'nullable|string|max:50',
            'jenis_alat_id' => 'required|exists:jenis_alats,id',
            'nama_alat' => 'required|string|max:255',
            'merek' => 'nullable|string|max:255',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'nomor_rangka' => 'nullable|string|max:255',
            'nomor_mesin' => 'nullable|string|max:255',
            'sumber_dana' => 'required|in:APBD,APBN,DAK,BANPER,MANDIRI',
            'harga' => 'required|numeric|min:0',
            'tahun_bantuan' => 'required|integer|between:' . $startYear . ',' . $currentYear,
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        $currentYear = (int) date('Y');
        $startYear = $currentYear - 4;

        return [
            'kelompok_tani_id.required' => 'Kelompok tani harus dipilih.',
            'kelompok_tani_id.exists' => 'Kelompok tani tidak valid.',
            'jenis_alat_id.required' => 'Jenis alat harus dipilih.',
            'jenis_alat_id.exists' => 'Jenis alat tidak valid.',
            'nama_alat.required' => 'Nama alat harus diisi.',
            'kondisi.required' => 'Kondisi alat harus dipilih.',
            'kondisi.in' => 'Kondisi alat tidak valid.',
            'sumber_dana.required' => 'Sumber dana harus dipilih.',
            'sumber_dana.in' => 'Sumber dana tidak valid.',
            'harga.required' => 'Harga alat harus diisi.',
            'harga.numeric' => 'Harga harus berupa angka.',
            'tahun_bantuan.required' => 'Tahun bantuan harus diisi.',
            'tahun_bantuan.between' => 'Tahun bantuan harus dalam rentang 5 tahun terakhir (' . $startYear . ' - ' . $currentYear . ').',
        ];
    }
}
