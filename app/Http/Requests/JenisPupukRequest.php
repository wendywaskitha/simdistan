<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JenisPupukRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string']
        ];
    }

    public function attributes(): array
    {
        return [
            'nama' => 'Nama Jenis Pupuk',
            'deskripsi' => 'Deskripsi'
        ];
    }
}
