<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PenyuluhRequest extends FormRequest
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
        $id = $this->route('penyuluh');

        return [
            'nama' => ['required', 'string', 'max:255'],
            'nip' => [
                'nullable',
                'string',
                'max:255',
                'unique:penyuluhs,nip,' . ($id ? $id : 'NULL') . ',id,deleted_at,NULL'
            ],
            'telepon' => ['nullable', 'string', 'max:20'],
            'bpp_id' => ['required', 'exists:bpps,id'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'nama' => 'Nama Penyuluh',
            'nip' => 'NIP',
            'telepon' => 'Nomor Telepon',
            'bpp_id' => 'Kantor BPP',
        ];
    }
}
