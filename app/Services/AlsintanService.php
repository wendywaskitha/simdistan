<?php

namespace App\Services;

use App\Repositories\AlsintanRepositoryInterface;
use App\Repositories\LaporanPemanfaatanAlsintanRepositoryInterface;
use App\Repositories\RealokasiAlsintanRepositoryInterface;
use App\Models\Alsintan;
use App\Models\LaporanPemanfaatanAlsintan;
use App\Models\RealokasiAlsintan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class AlsintanService
{
    protected $alsintanRepository;
    protected $laporanRepository;
    protected $realokasiRepository;

    public function __construct(
        AlsintanRepositoryInterface $alsintanRepository,
        LaporanPemanfaatanAlsintanRepositoryInterface $laporanRepository,
        RealokasiAlsintanRepositoryInterface $realokasiRepository
    ) {
        $this->alsintanRepository = $alsintanRepository;
        $this->laporanRepository = $laporanRepository;
        $this->realokasiRepository = $realokasiRepository;
    }

    /**
     * Get all Alsintan with Kelompok Tani relationship.
     */
    public function getAllAlsintan(): Collection
    {
        return $this->alsintanRepository->all();
    }

    /**
     * Get an Alsintan by its ID.
     */
    public function getAlsintanById(int $id): ?Alsintan
    {
        return $this->alsintanRepository->find($id);
    }

    /**
     * Create a new Alsintan record.
     */
    public function createAlsintan(array $data): Alsintan
    {
        return $this->alsintanRepository->create($data);
    }

    /**
     * Update an existing Alsintan.
     */
    public function updateAlsintan(int $id, array $data): bool
    {
        return $this->alsintanRepository->update($id, $data);
    }

    /**
     * Delete (soft-delete) an Alsintan.
     */
    public function deleteAlsintan(int $id): bool
    {
        return $this->alsintanRepository->delete($id);
    }

    /**
     * Add a utilization report to an Alsintan.
     */
    public function tambahLaporanPemanfaatan(int $alsintanId, array $data): LaporanPemanfaatanAlsintan
    {
        $data['alsintan_id'] = $alsintanId;
        return $this->laporanRepository->create($data);
    }

    /**
     * Reallocate an Alsintan to a new Kelompok Tani.
     */
    public function realokasiAlsintan(int $alsintanId, array $data): bool
    {
        return DB::transaction(function () use ($alsintanId, $data) {
            $alsintan = $this->getAlsintanById($alsintanId);
            if (!$alsintan) {
                return false;
            }

            // Record old kelompok tani
            $oldKelompokTaniId = $alsintan->kelompok_tani_id;
            $newKelompokTaniId = $data['kelompok_tani_tujuan_id'];

            // Save to realokasi table
            $this->realokasiRepository->create([
                'alsintan_id' => $alsintanId,
                'kelompok_tani_asal_id' => $oldKelompokTaniId,
                'kelompok_tani_tujuan_id' => $newKelompokTaniId,
                'tanggal_realokasi' => $data['tanggal_realokasi'],
                'keterangan' => $data['keterangan'] ?? null,
            ]);

            // Update Alsintan's current kelompok tani and ketua
            return $alsintan->update([
                'kelompok_tani_id' => $newKelompokTaniId,
                'nama_ketua' => $data['nama_ketua_tujuan'] ?? $alsintan->nama_ketua,
            ]);
        });
    }

    /**
     * Get Laporan Pemanfaatan by ID.
     */
    public function getLaporanById(int $laporanId): ?LaporanPemanfaatanAlsintan
    {
        return $this->laporanRepository->find($laporanId);
    }

    /**
     * Update Laporan Pemanfaatan by ID.
     */
    public function updateLaporan(int $laporanId, array $data): bool
    {
        return $this->laporanRepository->update($laporanId, $data);
    }

    /**
     * Delete Laporan Pemanfaatan by ID.
     */
    public function deleteLaporan(int $laporanId): bool
    {
        return $this->laporanRepository->delete($laporanId);
    }
}
