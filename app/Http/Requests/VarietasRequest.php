<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VarietasRequest extends FormRequest
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
        $id = $this->route('varieta'); // Route model binding Laravel default untuk varietas

        return [
            'komoditas_id' => ['required', 'exists:komoditas,id'],
            'nama' => [
                'required',
                'string',
                'max:255',
                Rule::unique('varietas')
                    ->where(fn ($query) => $query->whereNull('deleted_at')->where('komoditas_id', $this->komoditas_id))
                    ->ignore($id)
            ],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'komoditas_id' => 'Komoditas',
            'nama' => 'Nama Varietas',
        ];
    }
}
