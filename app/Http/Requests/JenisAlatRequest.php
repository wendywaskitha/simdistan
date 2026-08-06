<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JenisAlatRequest extends FormRequest
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
            'nama' => 'required|string|max:255|unique:jenis_alats,nama,' . $this->route('jenis_alat'),
            'deskripsi' => 'nullable|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nama.required' => 'Nama jenis alat harus diisi.',
            'nama.unique' => 'Nama jenis alat sudah terdaftar.',
        ];
    }
}
