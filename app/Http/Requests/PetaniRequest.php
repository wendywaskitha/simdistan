<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PetaniRequest extends FormRequest
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
        $id = $this->route('petani');

        return [
            'kelompok_tani_id' => ['required', 'exists:kelompok_tanis,id'],
            'nama' => ['required', 'string', 'max:255'],
            'nik' => [
                'required',
                'string',
                'size:16',
                'unique:petanis,nik,' . ($id ? $id : 'NULL') . ',id,deleted_at,NULL'
            ],
            'telepon' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'ktp' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'luas_lahan' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'kelompok_tani_id' => 'Kelompok Tani',
            'nama' => 'Nama Petani',
            'nik' => 'NIK',
            'telepon' => 'Nomor Telepon',
            'alamat' => 'Alamat Lengkap',
            'luas_lahan' => 'Luas Lahan',
        ];
    }
}
