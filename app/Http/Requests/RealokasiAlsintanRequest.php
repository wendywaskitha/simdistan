<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RealokasiAlsintanRequest extends FormRequest
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
        $fiveYearsAgo = date('Y-m-d', strtotime('-5 years'));
        $today = date('Y-m-d');

        return [
            'kelompok_tani_tujuan_id' => 'required|exists:kelompok_tanis,id|different:kelompok_tani_asal_id',
            'nama_ketua_tujuan' => 'nullable|string|max:255',
            'tanggal_realokasi' => 'required|date|after_or_equal:' . $fiveYearsAgo . '|before_or_equal:' . $today,
            'keterangan' => 'nullable|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'kelompok_tani_tujuan_id.required' => 'Kelompok tani tujuan harus dipilih.',
            'kelompok_tani_tujuan_id.exists' => 'Kelompok tani tujuan tidak valid.',
            'kelompok_tani_tujuan_id.different' => 'Kelompok tani tujuan harus berbeda dengan kelompok tani asal.',
            'tanggal_realokasi.required' => 'Tanggal realokasi harus diisi.',
            'tanggal_realokasi.date' => 'Format tanggal tidak valid.',
            'tanggal_realokasi.after_or_equal' => 'Tanggal tidak boleh lebih dari 5 tahun yang lalu.',
            'tanggal_realokasi.before_or_equal' => 'Tanggal tidak boleh di masa depan.',
        ];
    }
}
