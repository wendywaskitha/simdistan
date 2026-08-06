<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KategoriKomoditasRequest extends FormRequest
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
        $id = $this->route('kategori_komodita'); // Nama model binding otomatis jamak/singular dari Laravel resource routing

        return [
            'nama' => [
                'required',
                'string',
                'max:255',
                'unique:kategori_komoditas,nama,' . ($id ? $id : 'NULL') . ',id,deleted_at,NULL'
            ],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'nama' => 'Nama Kategori',
        ];
    }
}
