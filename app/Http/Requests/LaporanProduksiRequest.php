<?php

namespace App\Http\Requests;

use App\Models\KategoriKomoditas;
use Illuminate\Foundation\Http\FormRequest;

class LaporanProduksiRequest extends FormRequest
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
        $id = $this->route('laporan_produksi') ?? $this->route('tanaman_pangan') ?? $this->route('hortikultura') ?? $this->route('perkebunan');

        $rules = [
            'kategori_komoditas_id' => ['required', 'exists:kategori_komoditas,id'],
            'kecamatan_id' => ['required', 'exists:kecamatans,id'],
            'bulan' => ['required', 'integer', 'between:1,12'],
            'tahun' => ['required', 'integer', 'min:2020', 'max:2050'],
            'satuan_id' => ['required', 'exists:satuans,id'],
            'komoditas' => ['required', 'array'],
        ];

        $kategori = KategoriKomoditas::find($this->kategori_komoditas_id);
        $isTanamanPangan = $kategori && strtolower($kategori->nama) === 'tanaman pangan';

        if ($isTanamanPangan) {
            $rules['komoditas.*.mingguans'] = ['required', 'array', 'size:4'];
            $rules['komoditas.*.mingguans.*.luas_tanam'] = ['required', 'numeric', 'min:0'];
            $rules['komoditas.*.mingguans.*.luas_panen'] = ['required', 'numeric', 'min:0'];
            $rules['komoditas.*.mingguans.*.produktivitas'] = ['required', 'numeric', 'min:0'];
            $rules['komoditas.*.mingguans.*.produksi'] = ['required', 'numeric', 'min:0'];
            $rules['komoditas.*.mingguans.*.luas_lahan'] = ['required', 'numeric', 'min:0'];
        } else {
            $rules['komoditas.*.luas_tanam'] = ['nullable', 'numeric', 'min:0'];
            $rules['komoditas.*.luas_rusak']  = ['nullable', 'numeric', 'min:0'];
            $rules['komoditas.*.jumlah_tanaman_menghasilkan'] = ['nullable', 'integer', 'min:0'];
            $rules['komoditas.*.jenis_periode'] = ['nullable', 'string'];
            $rules['komoditas.*.form_type']     = ['nullable', 'string'];
            // SPH-SBS & SPH-TBF fields
            $rules['komoditas.*.luas_tanam_akhir_bulan_lalu'] = ['nullable', 'numeric', 'min:0'];
            $rules['komoditas.*.luas_panen_belum_habis']      = ['nullable', 'numeric', 'min:0'];
            $rules['komoditas.*.luas_tanam_akhir']            = ['nullable', 'numeric', 'min:0'];
            $rules['komoditas.*.produksi_belum_habis']        = ['nullable', 'numeric', 'min:0'];
            $rules['komoditas.*.harga_jual']                  = ['nullable', 'numeric', 'min:0'];
            // SPH-BST fields
            $rules['komoditas.*.jumlah_tanaman_akhir_triwulan_lalu'] = ['nullable', 'integer', 'min:0'];
            $rules['komoditas.*.tanaman_dibongkar']           = ['nullable', 'integer', 'min:0'];
            $rules['komoditas.*.tanaman_baru']                = ['nullable', 'integer', 'min:0'];
            $rules['komoditas.*.tanaman_tidak_menghasilkan']  = ['nullable', 'integer', 'min:0'];
            $rules['komoditas.*.tanaman_tus_rusak']           = ['nullable', 'integer', 'min:0'];
            // Perkebunan fields
            $rules['komoditas.*.luas_akhir_tahun_lalu']       = ['nullable', 'numeric', 'min:0'];
            $rules['komoditas.*.tanam_ulang']                 = ['nullable', 'numeric', 'min:0'];
            $rules['komoditas.*.tanam_baru']                  = ['nullable', 'numeric', 'min:0'];
            $rules['komoditas.*.pengurangan']                 = ['nullable', 'numeric', 'min:0'];
            $rules['komoditas.*.luas_jumlah']                 = ['nullable', 'numeric', 'min:0'];
            $rules['komoditas.*.tbm']                         = ['nullable', 'numeric', 'min:0'];
            $rules['komoditas.*.tm']                          = ['nullable', 'numeric', 'min:0'];
            $rules['komoditas.*.ttm']                         = ['nullable', 'numeric', 'min:0'];
            $rules['komoditas.*.produksi_akhir_tahun_lalu']   = ['nullable', 'numeric', 'min:0'];
            $rules['komoditas.*.wujud_produksi']              = ['nullable', 'string'];
            $rules['komoditas.*.jumlah_petani_pemilik']       = ['nullable', 'integer', 'min:0'];
            $rules['komoditas.*.jumlah_petani_bmu']           = ['nullable', 'integer', 'min:0'];

            foreach ($this->input('komoditas', []) as $key => $komoditasVal) {
                $hasInput = !empty($komoditasVal['luas_tanam']) ||
                             !empty($komoditasVal['luas_panen']) ||
                             !empty($komoditasVal['produksi']) ||
                             !empty($komoditasVal['tbm']) ||
                             !empty($komoditasVal['tm']) ||
                             !empty($komoditasVal['luas_akhir_tahun_lalu']) ||
                             !empty($komoditasVal['tanam_baru']);

                if ($hasInput) {
                    $rules["komoditas.{$key}.luas_panen"] = ['nullable', 'numeric', 'min:0'];
                    $rules["komoditas.{$key}.produksi"]   = ['nullable', 'numeric', 'min:0'];
                } else {
                    $rules["komoditas.{$key}.luas_panen"] = ['nullable', 'numeric', 'min:0'];
                    $rules["komoditas.{$key}.produksi"]   = ['nullable', 'numeric', 'min:0'];
                }
            }
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'kategori_komoditas_id' => 'Kategori Komoditas',
            'kecamatan_id' => 'Kecamatan',
            'bulan' => 'Bulan',
            'tahun' => 'Tahun',
            'satuan_id' => 'Satuan Ukur',
            'komoditas' => 'Data Komoditas',
            'komoditas.*.luas_tanam' => 'Luas Tanam',
            'komoditas.*.luas_panen' => 'Luas Panen',
            'komoditas.*.produksi' => 'Hasil Produksi',
            'komoditas.*.mingguans.*.luas_tanam' => 'Luas Tanam Mingguan',
            'komoditas.*.mingguans.*.luas_panen' => 'Luas Panen Mingguan',
            'komoditas.*.mingguans.*.produksi' => 'Hasil Produksi Mingguan',
            'komoditas.*.mingguans.*.luas_lahan' => 'Luas Lahan Mingguan',
        ];
    }

    /**
     * Configure the validator instance to check harvest limits based on durasi_panen_bulan.
     * Berlaku untuk semua kategori: Tanaman Pangan, Hortikultura, Perkebunan, dll.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $kecamatanId = $this->kecamatan_id;
            $bulan       = $this->bulan;
            $tahun       = $this->tahun;
            $komoditasData = $this->komoditas;

            if (!$kecamatanId || !$bulan || !$tahun || !is_array($komoditasData)) {
                return;
            }

            $service   = app(\App\Services\LaporanProduksiService::class);
            $laporanId = $this->route('laporan_produksi') ?? $this->route('tanaman_pangan') ?? $this->route('hortikultura') ?? $this->route('perkebunan');

            foreach ($komoditasData as $komoditasId => $data) {
                // Hitung total luas tanam & panen – mendukung struktur mingguan (Tanaman Pangan)
                // maupun struktur langsung (Hortikultura / Perkebunan)
                $incomingTanam = 0;
                $incomingPanen = 0;

                if (isset($data['mingguans']) && is_array($data['mingguans'])) {
                    // Struktur mingguan
                    foreach ($data['mingguans'] as $m) {
                        $incomingTanam += floatval($m['luas_tanam'] ?? 0);
                        $incomingPanen += floatval($m['luas_panen'] ?? 0);
                    }
                } else {
                    // Struktur langsung (hortikultura / perkebunan)
                    $incomingTanam = floatval($data['luas_tanam'] ?? 0);
                    $incomingPanen = floatval($data['luas_panen'] ?? 0);
                }

                // Jika baris ini tidak diisi sama sekali, lewati
                if ($incomingTanam == 0 && $incomingPanen == 0) {
                    continue;
                }

                $result = $service->calculateMaxHarvestArea(
                    (int) $kecamatanId,
                    (int) $komoditasId,
                    (int) $bulan,
                    (int) $tahun,
                    $incomingTanam,
                    $laporanId ? (int) $laporanId : null
                );

                // has_duration_limit = true berarti komoditas ini punya durasi panen yang ditetapkan
                if (!empty($result['has_duration_limit']) && $incomingPanen > $result['max_panen']) {
                    $validator->errors()->add(
                        "komoditas.{$komoditasId}.luas_panen",
                        "Luas Panen untuk komoditas {$result['komoditas_nama']} tidak boleh melebihi Luas Tanam pada {$result['durasi']} bulan sebelumnya (Maks: " . number_format($result['max_panen'], 2) . " Ha, input: " . number_format($incomingPanen, 2) . " Ha)."
                    );
                }
            }
        });
    }
}
