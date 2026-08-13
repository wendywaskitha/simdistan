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

        $alsintanId = $this->route('id');
        if (!$alsintanId && $this->route('laporanId')) {
            $laporan = \App\Models\LaporanPemanfaatanAlsintan::find($this->route('laporanId'));
            $alsintanId = $laporan ? $laporan->alsintan_id : null;
        }

        $alsintan = \App\Models\Alsintan::with('jenisAlat')->find($alsintanId);
        $isTractorOrCombine = false;
        if ($alsintan && $alsintan->jenisAlat) {
            $isTractorOrCombine = in_array(strtolower($alsintan->jenisAlat->nama), [
                'traktor roda 2',
                'traktor roda 4',
                'combine harvester'
            ]);
        }

        $rules = [
            'luas_lahan' => 'required|numeric|min:0.01',
            'waktu_pengerjaan' => 'required|integer|min:1',
            'biaya_pengolahan' => 'required|numeric|min:0',
            'hour_meter' => 'nullable|numeric|min:0',
            'foto_dokumentasi' => $this->isMethod('post') ? 'required|image|mimes:jpeg,png,jpg,webp|max:2048' : 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];

        if ($isTractorOrCombine) {
            $rules['tanggal'] = 'required|date|after_or_equal:' . $fiveYearsAgo . '|before_or_equal:' . $today;
            $rules['hour_meter_awal'] = 'required|numeric|min:0';
            $rules['hour_meter_akhir'] = 'required|numeric|min:0|gte:hour_meter_awal';
            $rules['foto_hm_awal'] = $this->isMethod('post') ? 'required|image|mimes:jpeg,png,jpg,webp|max:2048' : 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048';
            $rules['foto_hm_akhir'] = $this->isMethod('post') ? 'required|image|mimes:jpeg,png,jpg,webp|max:2048' : 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048';
            $rules['tanggal_mulai'] = 'nullable';
            $rules['tanggal_selesai'] = 'nullable';
        } else {
            $rules['tanggal'] = 'nullable';
            $rules['hour_meter_awal'] = 'nullable';
            $rules['hour_meter_akhir'] = 'nullable';
            $rules['foto_hm_awal'] = 'nullable';
            $rules['foto_hm_akhir'] = 'nullable';
            $rules['tanggal_mulai'] = 'required|date';
            $rules['tanggal_selesai'] = 'required|date|after_or_equal:tanggal_mulai';
        }

        return $rules;
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
            'hour_meter_awal.required' => 'Hour Meter Awal harus diisi.',
            'hour_meter_awal.numeric' => 'Hour Meter Awal harus berupa angka.',
            'hour_meter_akhir.required' => 'Hour Meter Akhir harus diisi.',
            'hour_meter_akhir.numeric' => 'Hour Meter Akhir harus berupa angka.',
            'hour_meter_akhir.gte' => 'Hour Meter Akhir harus lebih besar atau sama dengan Hour Meter Awal.',
            'foto_hm_awal.required' => 'Foto Hour Meter Awal harus diunggah.',
            'foto_hm_awal.image' => 'Foto Hour Meter Awal harus berupa gambar.',
            'foto_hm_awal.max' => 'Foto Hour Meter Awal tidak boleh lebih dari 2MB.',
            'foto_hm_akhir.required' => 'Foto Hour Meter Akhir harus diunggah.',
            'foto_hm_akhir.image' => 'Foto Hour Meter Akhir harus berupa gambar.',
            'foto_hm_akhir.max' => 'Foto Hour Meter Akhir tidak boleh lebih dari 2MB.',
            'foto_dokumentasi.required' => 'Foto Dokumentasi Kerja harus diunggah.',
            'foto_dokumentasi.image' => 'Foto Dokumentasi Kerja harus berupa gambar.',
            'foto_dokumentasi.max' => 'Foto Dokumentasi Kerja tidak boleh lebih dari 2MB.',
            'tanggal_mulai.required' => 'Tanggal mulai pemanfaatan harus diisi.',
            'tanggal_mulai.date' => 'Format tanggal mulai pemanfaatan tidak valid.',
            'tanggal_selesai.required' => 'Tanggal selesai pemanfaatan harus diisi.',
            'tanggal_selesai.date' => 'Format tanggal selesai pemanfaatan tidak valid.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
        ];
    }
}
