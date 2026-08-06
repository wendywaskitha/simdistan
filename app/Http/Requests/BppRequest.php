<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BppRequest extends FormRequest
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
        $id = $this->route('bpp');

        return [
            'kecamatan_id' => ['required', 'exists:kecamatans,id'],
            'nama' => [
                'required',
                'string',
                'max:255',
                Rule::unique('bpps')
                    ->where(fn ($query) => $query->whereNull('deleted_at')->where('kecamatan_id', $this->kecamatan_id))
                    ->ignore($id)
            ],
            'alamat' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'kecamatan_id' => 'Kecamatan',
            'nama' => 'Nama BPP',
            'alamat' => 'Alamat BPP',
        ];
    }
}
