<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KomoditasRequest extends FormRequest
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
        $id = $this->route('komodita'); // Laravel route resource model binding untuk komoditas

        return [
            'kategori_komoditas_id' => ['required', 'exists:kategori_komoditas,id'],
            'nama' => [
                'required',
                'string',
                'max:255',
                Rule::unique('komoditas')
                    ->where(fn ($query) => $query->whereNull('deleted_at')->where('kategori_komoditas_id', $this->kategori_komoditas_id))
                    ->ignore($id)
            ],
            'durasi_panen_bulan' => ['nullable', 'integer', 'min:1', 'max:24'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'kategori_komoditas_id' => 'Kategori Komoditas',
            'nama' => 'Nama Komoditas',
            'durasi_panen_bulan' => 'Durasi Panen (Bulan)',
        ];
    }
}
