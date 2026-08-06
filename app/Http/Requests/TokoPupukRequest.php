<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TokoPupukRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255'],
            'pemilik' => ['nullable', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'kecamatan_ids' => ['required', 'array'],
            'kecamatan_ids.*' => ['exists:kecamatans,id']
        ];
    }

    public function attributes(): array
    {
        return [
            'nama' => 'Nama Toko/Distributor',
            'pemilik' => 'Nama Pemilik',
            'alamat' => 'Alamat',
            'telepon' => 'Nomor Telepon',
            'kecamatan_ids' => 'Kecamatan diampu',
        ];
    }
}
