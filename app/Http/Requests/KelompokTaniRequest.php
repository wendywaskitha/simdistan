<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KelompokTaniRequest extends FormRequest
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
        $id = $this->route('kelompok_tani');

        return [
            'desa_id' => ['required', 'exists:desas,id'],
            'gapoktan_id' => ['nullable', 'exists:gapoktans,id'],
            'nama' => [
                'required',
                'string',
                'max:255',
                Rule::unique('kelompok_tanis')
                    ->where(fn ($query) => $query->whereNull('deleted_at')->where('desa_id', $this->desa_id))
                    ->ignore($id)
            ],
            'ketua' => ['nullable', 'string', 'max:255'],
            'sk_pembentukan' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:2048'],
            'berita_acara' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:2048'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'desa_id' => 'Desa',
            'gapoktan_id' => 'Gapoktan',
            'nama' => 'Nama Kelompok Tani',
            'ketua' => 'Ketua Kelompok Tani',
        ];
    }
}
