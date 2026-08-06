<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LaporanPemanfaatanRequest extends FormRequest
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
        $fiveYearsAgo = date('Y-m-d', strtotime('-5 years'));
        $today = date('Y-m-d');

        return [
            'tanggal' => 'required|date|after_or_equal:' . $fiveYearsAgo . '|before_or_equal:' . $today,
            'luas_lahan' => 'required|numeric|min:0.01',
            'waktu_pengerjaan' => 'required|integer|min:1',
            'biaya_pengolahan' => 'required|numeric|min:0',
            'hour_meter' => 'required|numeric|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'tanggal.required' => 'Tanggal pengerjaan harus diisi.',
            'tanggal.date' => 'Format tanggal tidak valid.',
            'tanggal.after_or_equal' => 'Tanggal tidak boleh lebih dari 5 tahun yang lalu.',
            'tanggal.before_or_equal' => 'Tanggal tidak boleh di masa depan.',
            'luas_lahan.required' => 'Luas lahan harus diisi.',
            'luas_lahan.numeric' => 'Luas lahan harus berupa angka.',
            'luas_lahan.min' => 'Luas lahan minimal 0.01 hektar.',
            'waktu_pengerjaan.required' => 'Waktu pengerjaan harus diisi.',
            'waktu_pengerjaan.integer' => 'Waktu pengerjaan harus berupa bilangan bulat.',
            'waktu_pengerjaan.min' => 'Waktu pengerjaan minimal 1 jam.',
            'biaya_pengolahan.required' => 'Biaya pengolahan harus diisi.',
            'biaya_pengolahan.numeric' => 'Biaya pengolahan harus berupa angka.',
            'hour_meter.required' => 'Hour Meter harus diisi.',
            'hour_meter.numeric' => 'Hour Meter harus berupa angka.',
        ];
    }
}
