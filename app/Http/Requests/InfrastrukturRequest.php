<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InfrastrukturRequest extends FormRequest
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
            'nama_proyek' => 'required|string|max:255',
            'jenis_infrastruktur' => 'required|string|max:255',
            'kelompok_tani_id' => 'nullable|exists:kelompok_tanis,id',
            'kecamatan_id' => 'required|exists:kecamatans,id',
            'desa_id' => 'required|exists:desas,id',
            'volume' => 'required|numeric|min:0',
            'satuan' => 'required|string|max:50',
            'nilai_anggaran' => 'required|numeric|min:0',
            'sumber_dana' => 'required|string|max:100',
            'tahun_anggaran' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'status_pembangunan' => 'required|in:Rencana,Konstruksi,Selesai,Rusak',
            'latitude' => 'nullable|string|max:50',
            'longitude' => 'nullable|string|max:50',
            'kml_file' => 'nullable|file|max:5120',
            'keterangan' => 'nullable|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nama_proyek.required' => 'Nama proyek/kegiatan harus diisi.',
            'jenis_infrastruktur.required' => 'Jenis infrastruktur harus diisi.',
            'kecamatan_id.required' => 'Kecamatan lokasi harus dipilih.',
            'kecamatan_id.exists' => 'Kecamatan tidak valid.',
            'desa_id.required' => 'Desa lokasi harus dipilih.',
            'desa_id.exists' => 'Desa tidak valid.',
            'volume.required' => 'Volume/dimensi harus diisi.',
            'volume.numeric' => 'Volume harus berupa angka.',
            'satuan.required' => 'Satuan harus diisi.',
            'nilai_anggaran.required' => 'Nilai anggaran harus diisi.',
            'nilai_anggaran.numeric' => 'Nilai anggaran harus berupa angka.',
            'sumber_dana.required' => 'Sumber dana harus dipilih/diisi.',
            'tahun_anggaran.required' => 'Tahun anggaran harus diisi.',
            'status_pembangunan.required' => 'Status pembangunan harus dipilih.',
        ];
    }
}
