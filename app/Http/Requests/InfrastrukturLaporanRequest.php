<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InfrastrukturLaporanRequest extends FormRequest
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
        return [
            'tanggal_laporan' => 'required|date',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'progres_fisik' => 'required|numeric|min:0|max:100',
            'keterangan' => 'nullable|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'tanggal_laporan.required' => 'Tanggal laporan harus diisi.',
            'tanggal_laporan.date' => 'Format tanggal tidak valid.',
            'kondisi.required' => 'Kondisi infrastruktur harus dipilih.',
            'kondisi.in' => 'Kondisi tidak valid.',
            'progres_fisik.required' => 'Progres fisik harus diisi.',
            'progres_fisik.numeric' => 'Progres fisik harus berupa angka.',
            'progres_fisik.min' => 'Progres fisik minimal 0%.',
            'progres_fisik.max' => 'Progres fisik maksimal 100%.',
        ];
    }
}
