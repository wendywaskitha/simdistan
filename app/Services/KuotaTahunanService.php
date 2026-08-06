<?php

namespace App\Services;

use App\Repositories\KuotaTahunanRepositoryInterface;
use App\Models\KuotaTahunanPupuk;
use Illuminate\Database\Eloquent\Collection;

class KuotaTahunanService
{
    protected $kuotaRepository;

    public function __construct(KuotaTahunanRepositoryInterface $kuotaRepository)
    {
        $this->kuotaRepository = $kuotaRepository;
    }

    public function getKuotaByTahun(int $tahun): Collection
    {
        return KuotaTahunanPupuk::where('tahun', $tahun)->get();
    }

    public function simpanKuota(int $tahun, array $data): void
    {
        foreach ($data as $kecamatanId => $jenisData) {
            foreach ($jenisData as $jenisPupukId => $jumlah) {
                $jumlahFloat = floatval($jumlah ?? 0);

                if ($jumlahFloat >= 0) {
                    KuotaTahunanPupuk::updateOrCreate(
                        [
                            'tahun' => $tahun,
                            'kecamatan_id' => $kecamatanId,
                            'jenis_pupuk_id' => $jenisPupukId
                        ],
                        [
                            'jumlah' => $jumlahFloat
                        ]
                    );
                }
            }
        }
    }
}
