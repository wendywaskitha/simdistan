<?php

namespace App\Services;

use App\Repositories\LaporanProduksiRepositoryInterface;
use App\Models\LaporanProduksi;
use App\Models\KategoriKomoditas;
use Illuminate\Support\Facades\DB;

class LaporanProduksiService
{
    protected $laporanRepository;

    public function __construct(LaporanProduksiRepositoryInterface $laporanRepository)
    {
        $this->laporanRepository = $laporanRepository;
    }

    public function getAllLaporan()
    {
        return $this->laporanRepository->allWithRelations();
    }

    public function getLaporanByCategory(int $kategoriId)
    {
        return $this->laporanRepository->getByCategory($kategoriId);
    }

    public function getLaporanById(int $id): ?LaporanProduksi
    {
        return $this->laporanRepository->find($id);
    }

    /**
     * Create multiple reports from bulk input.
     */
    public function createLaporan(array $data): void
    {
        DB::transaction(function() use ($data) {
            $kategoriId = $data['kategori_komoditas_id'];
            $isTanamanPangan = $this->checkIsTanamanPangan($kategoriId);

            foreach ($data['komoditas'] as $komoditasId => $komoditasData) {
                // Skip jika data kosong (tanam, panen, produksi semuanya 0 atau kosong)
                $hasData = false;
                if ($isTanamanPangan && isset($komoditasData['mingguans'])) {
                    foreach ($komoditasData['mingguans'] as $m) {
                        if (($m['luas_tanam'] ?? 0) > 0 || ($m['luas_panen'] ?? 0) > 0 || ($m['produksi'] ?? 0) > 0) {
                            $hasData = true;
                            break;
                        }
                    }
                } else {
                    if (
                        ($komoditasData['luas_tanam'] ?? 0) > 0 || 
                        ($komoditasData['luas_panen'] ?? 0) > 0 || 
                        ($komoditasData['produksi'] ?? 0) > 0 ||
                        ($komoditasData['luas_rusak'] ?? 0) > 0 ||
                        ($komoditasData['jumlah_tanaman_menghasilkan'] ?? 0) > 0
                    ) {
                        $hasData = true;
                    }
                }

                if (!$hasData) {
                    continue; // Skip komoditas yang tidak diisi data sama sekali
                }

                $totalTanam = 0;
                $totalPanen = 0;
                $totalProduksi = 0;
                $totalLahan = 0;
                $luasRusak = 0;
                $jumlahTanaman = 0;

                if ($isTanamanPangan && isset($komoditasData['mingguans'])) {
                    foreach ($komoditasData['mingguans'] as $m) {
                        $totalTanam += floatval($m['luas_tanam'] ?? 0);
                        $totalPanen += floatval($m['luas_panen'] ?? 0);
                        $totalProduksi += floatval($m['produksi'] ?? 0);
                    }
                } else {
                    $totalTanam = floatval($komoditasData['luas_tanam'] ?? 0);
                    $totalPanen = floatval($komoditasData['luas_panen'] ?? 0);
                    $totalProduksi = floatval($komoditasData['produksi'] ?? 0);
                    $luasRusak = floatval($komoditasData['luas_rusak'] ?? 0);
                    $jumlahTanaman = intval($komoditasData['jumlah_tanaman_menghasilkan'] ?? 0);
                }

                $produktivitas = $totalPanen > 0 ? ($totalProduksi / $totalPanen) : 0;

                // Gunakan updateOrCreate untuk menghindari duplikasi kombinasi unique
                $laporan = LaporanProduksi::updateOrCreate(
                    [
                        'kecamatan_id' => $data['kecamatan_id'],
                        'komoditas_id' => $komoditasId,
                        'bulan'        => $data['bulan'],
                        'tahun'        => $data['tahun'],
                    ],
                    [
                        'kategori_komoditas_id' => $kategoriId,
                        'satuan_id'             => $data['satuan_id'],
                        'luas_tanam'            => $totalTanam,
                        'luas_panen'            => $totalPanen,
                        'luas_rusak'            => $luasRusak,
                        'jumlah_tanaman_menghasilkan' => $jumlahTanaman,
                        'jenis_periode'         => $komoditasData['jenis_periode'] ?? null,
                        'form_type'             => $komoditasData['form_type'] ?? null,
                        'produktivitas'         => $produktivitas,
                        'produksi'              => $totalProduksi,
                        // SPH-SBS & SPH-TBF fields
                        'luas_tanam_akhir_bulan_lalu' => floatval($komoditasData['luas_tanam_akhir_bulan_lalu'] ?? 0),
                        'luas_panen_belum_habis'      => floatval($komoditasData['luas_panen_belum_habis'] ?? 0),
                        'luas_tanam_akhir'            => floatval($komoditasData['luas_tanam_akhir'] ?? 0),
                        'produksi_belum_habis'        => floatval($komoditasData['produksi_belum_habis'] ?? 0),
                        'harga_jual'                  => floatval($komoditasData['harga_jual'] ?? 0),
                        // SPH-BST fields (pohon/rumpun)
                        'jumlah_tanaman_akhir_triwulan_lalu' => intval($komoditasData['jumlah_tanaman_akhir_triwulan_lalu'] ?? 0),
                        'tanaman_dibongkar'           => intval($komoditasData['tanaman_dibongkar'] ?? 0),
                        'tanaman_baru'                => intval($komoditasData['tanaman_baru'] ?? 0),
                        'tanaman_tidak_menghasilkan'  => intval($komoditasData['tanaman_tidak_menghasilkan'] ?? 0),
                        'tanaman_tus_rusak'           => intval($komoditasData['tanaman_tus_rusak'] ?? 0),
                        // Perkebunan fields
                        'luas_akhir_tahun_lalu'       => floatval($komoditasData['luas_akhir_tahun_lalu'] ?? 0),
                        'tanam_ulang'                 => floatval($komoditasData['tanam_ulang'] ?? 0),
                        'tanam_baru'                  => floatval($komoditasData['tanam_baru'] ?? 0),
                        'pengurangan'                 => floatval($komoditasData['pengurangan'] ?? 0),
                        'luas_jumlah'                 => floatval($komoditasData['luas_jumlah'] ?? 0),
                        'tbm'                         => floatval($komoditasData['tbm'] ?? 0),
                        'tm'                          => floatval($komoditasData['tm'] ?? 0),
                        'ttm'                         => floatval($komoditasData['ttm'] ?? 0),
                        'produksi_akhir_tahun_lalu'   => floatval($komoditasData['produksi_akhir_tahun_lalu'] ?? 0),
                        'wujud_produksi'              => $komoditasData['wujud_produksi'] ?? null,
                        'jumlah_petani_pemilik'       => intval($komoditasData['jumlah_petani_pemilik'] ?? 0),
                        'jumlah_petani_bmu'           => intval($komoditasData['jumlah_petani_bmu'] ?? 0),
                        'keterangan_selisih_panen'    => $komoditasData['keterangan_selisih_panen'] ?? null,
                    ]
                );

                // Update detail mingguan jika Tanaman Pangan
                if ($isTanamanPangan && isset($komoditasData['mingguans'])) {
                    $laporan->mingguans()->delete();
                    foreach ($komoditasData['mingguans'] as $index => $m) {
                        $laporan->mingguans()->create([
                            'minggu_ke' => $index + 1,
                            'luas_tanam' => floatval($m['luas_tanam'] ?? 0),
                            'luas_panen' => floatval($m['luas_panen'] ?? 0),
                            'produktivitas' => floatval($m['produktivitas'] ?? 0),
                            'produksi' => floatval($m['produksi'] ?? 0),
                        ]);
                    }
                }
            }
        });
    }

    /**
     * Update a single report record.
     */
    public function updateLaporan(int $id, array $data): bool
    {
        return DB::transaction(function() use ($id, $data) {
            $laporan = LaporanProduksi::find($id);
            if (!$laporan) return false;

            $isTanamanPangan = $this->checkIsTanamanPangan($data['kategori_komoditas_id']);

            // Di form edit single, key komoditas dikirim berupa array berisi satu item komoditas_id
            $komoditasId = $laporan->komoditas_id;
            $komoditasData = $data['komoditas'][$komoditasId] ?? [];

            $totalTanam = 0;
            $totalPanen = 0;
            $totalProduksi = 0;
            $totalLahan = 0;

            if ($isTanamanPangan && isset($komoditasData['mingguans'])) {
                foreach ($komoditasData['mingguans'] as $m) {
                    $totalTanam += floatval($m['luas_tanam'] ?? 0);
                    $totalPanen += floatval($m['luas_panen'] ?? 0);
                    $totalProduksi += floatval($m['produksi'] ?? 0);
                }
            } else {
                $totalTanam = floatval($komoditasData['luas_tanam'] ?? 0);
                $totalPanen = floatval($komoditasData['luas_panen'] ?? 0);
                $totalProduksi = floatval($komoditasData['produksi'] ?? 0);
            }

            $produktivitas = $totalPanen > 0 ? ($totalProduksi / $totalPanen) : 0;

            $updated = $laporan->update([
                'kecamatan_id'   => $data['kecamatan_id'],
                'satuan_id'      => $data['satuan_id'],
                'bulan'          => $data['bulan'],
                'tahun'          => $data['tahun'],
                'luas_tanam'     => $totalTanam,
                'luas_panen'     => $totalPanen,
                'luas_rusak'     => floatval($komoditasData['luas_rusak'] ?? 0),
                'jumlah_tanaman_menghasilkan' => intval($komoditasData['jumlah_tanaman_menghasilkan'] ?? 0),
                'jenis_periode'  => $komoditasData['jenis_periode'] ?? null,
                'form_type'      => $komoditasData['form_type'] ?? null,
                'produktivitas'  => $produktivitas,
                'produksi'       => $totalProduksi,
                // SPH-SBS & SPH-TBF fields
                'luas_tanam_akhir_bulan_lalu' => floatval($komoditasData['luas_tanam_akhir_bulan_lalu'] ?? 0),
                'luas_panen_belum_habis'      => floatval($komoditasData['luas_panen_belum_habis'] ?? 0),
                'luas_tanam_akhir'            => floatval($komoditasData['luas_tanam_akhir'] ?? 0),
                'produksi_belum_habis'        => floatval($komoditasData['produksi_belum_habis'] ?? 0),
                'harga_jual'                  => floatval($komoditasData['harga_jual'] ?? 0),
                // SPH-BST fields
                'jumlah_tanaman_akhir_triwulan_lalu' => intval($komoditasData['jumlah_tanaman_akhir_triwulan_lalu'] ?? 0),
                'tanaman_dibongkar'           => intval($komoditasData['tanaman_dibongkar'] ?? 0),
                'tanaman_baru'                => intval($komoditasData['tanaman_baru'] ?? 0),
                'tanaman_tidak_menghasilkan'  => intval($komoditasData['tanaman_tidak_menghasilkan'] ?? 0),
                'tanaman_tus_rusak'           => intval($komoditasData['tanaman_tus_rusak'] ?? 0),
                // Perkebunan fields
                'luas_akhir_tahun_lalu'       => floatval($komoditasData['luas_akhir_tahun_lalu'] ?? 0),
                'tanam_ulang'                 => floatval($komoditasData['tanam_ulang'] ?? 0),
                'tanam_baru'                  => floatval($komoditasData['tanam_baru'] ?? 0),
                'pengurangan'                 => floatval($komoditasData['pengurangan'] ?? 0),
                'luas_jumlah'                 => floatval($komoditasData['luas_jumlah'] ?? 0),
                'tbm'                         => floatval($komoditasData['tbm'] ?? 0),
                'tm'                          => floatval($komoditasData['tm'] ?? 0),
                'ttm'                         => floatval($komoditasData['ttm'] ?? 0),
                'produksi_akhir_tahun_lalu'   => floatval($komoditasData['produksi_akhir_tahun_lalu'] ?? 0),
                'wujud_produksi'              => $komoditasData['wujud_produksi'] ?? null,
                'jumlah_petani_pemilik'       => intval($komoditasData['jumlah_petani_pemilik'] ?? 0),
                'jumlah_petani_bmu'           => intval($komoditasData['jumlah_petani_bmu'] ?? 0),
                'keterangan_selisih_panen'    => $komoditasData['keterangan_selisih_panen'] ?? null,
            ]);

            if ($updated && $isTanamanPangan && isset($komoditasData['mingguans'])) {
                $laporan->mingguans()->delete();
                foreach ($komoditasData['mingguans'] as $index => $m) {
                    $laporan->mingguans()->create([
                        'minggu_ke' => $index + 1,
                        'luas_tanam' => floatval($m['luas_tanam'] ?? 0),
                        'luas_panen' => floatval($m['luas_panen'] ?? 0),
                        'produktivitas' => floatval($m['produktivitas'] ?? 0),
                        'produksi' => floatval($m['produksi'] ?? 0),
                    ]);
                }
            }

            return $updated;
        });
    }

    public function deleteLaporan(int $id): bool
    {
        return $this->laporanRepository->delete($id);
    }

    /**
     * Ambil data periode sebelumnya untuk auto-fill kolom "Akhir Periode Lalu".
     * Mendukung SBS (bulanan) dan BST/TBF (triwulanan).
     *
     * @param int    $kecamatanId
     * @param string $formType    'SPH-SBS' | 'SPH-BST' | 'SPH-TBF'
     * @param int    $bulan       Bulan saat ini (1-12 untuk SBS, 1-4 triwulan untuk BST/TBF)
     * @param int    $tahun
     * @return array  komoditas_id => data fields
     */
    public function getPreviousPeriodData(int $kecamatanId, string $formType, int $bulan, int $tahun): array
    {
        // Hitung periode sebelumnya
        if ($formType === 'SPH-SBS') {
            // Bulanan: bulan sebelumnya
            $prevBulan = $bulan - 1;
            $prevTahun = $tahun;
            if ($prevBulan < 1) { $prevBulan = 12; $prevTahun--; }
        } elseif ($formType === 'Perkebunan') {
            // Perkebunan Semesteran: semester sebelumnya
            $prevBulan = $bulan - 1;
            $prevTahun = $tahun;
            if ($prevBulan < 1) { $prevBulan = 2; $prevTahun--; }
        } else {
            // Triwulanan: triwulan sebelumnya
            $prevBulan = $bulan - 1;
            $prevTahun = $tahun;
            if ($prevBulan < 1) { $prevBulan = 4; $prevTahun--; }
        }

        $laporans = LaporanProduksi::where('kecamatan_id', $kecamatanId)
            ->where('form_type', $formType)
            ->where('bulan', $prevBulan)
            ->where('tahun', $prevTahun)
            ->get();

        $result = [];
        foreach ($laporans as $lap) {
            if ($formType === 'SPH-BST') {
                $result[$lap->komoditas_id] = [
                    'jumlah_tanaman_akhir_triwulan_lalu' => ($lap->jumlah_tanaman_akhir_triwulan_lalu ?? 0)
                        - ($lap->tanaman_dibongkar ?? 0)
                        + ($lap->tanaman_baru ?? 0),
                ];
            } elseif ($formType === 'Perkebunan') {
                // Untuk Perkebunan: kembalikan luas jumlah mutasi (luas_jumlah)
                $result[$lap->komoditas_id] = [
                    'luas_akhir_tahun_lalu' => $lap->luas_jumlah ?? 0,
                ];
            } else {
                $result[$lap->komoditas_id] = [
                    'luas_tanam_akhir_bulan_lalu' => $lap->luas_tanam_akhir ?? 0,
                ];
            }
        }

        return $result;
    }

    private function checkIsTanamanPangan(int $kategoriId): bool
    {
        $kategori = KategoriKomoditas::find($kategoriId);
        return $kategori && strtolower($kategori->nama) === 'tanaman pangan';
    }

    /**
     * Calculate maximum allowed luas_panen for a commodity in a kecamatan based on durasi_panen_bulan.
     * Berlaku untuk semua kategori komoditas (Tanaman Pangan, Hortikultura, Perkebunan, dll).
     */
    public function calculateMaxHarvestArea(int $kecamatanId, int $komoditasId, int $bulan, int $tahun, float $incomingLuasTanam, ?int $excludeLaporanId = null): array
    {
        $komoditas = \App\Models\Komoditas::find($komoditasId);
        if (!$komoditas) {
            return ['has_duration_limit' => false, 'max_panen' => null];
        }

        // Jika komoditas tidak memiliki durasi panen yang ditetapkan, lewati
        $durasi = $komoditas->durasi_panen_bulan ?? null;
        if (!$durasi || $durasi <= 0) {
            return ['has_duration_limit' => false, 'max_panen' => null];
        }

        // Hitung bulan dan tahun target (M - durasi)
        $targetBulan = $bulan - $durasi;
        $targetTahun = $tahun;
        while ($targetBulan < 1) {
            $targetBulan += 12;
            $targetTahun--;
        }

        // Cari laporan produksi di kecamatan ini untuk komoditas ini pada bulan target
        $query = LaporanProduksi::where('kecamatan_id', $kecamatanId)
            ->where('komoditas_id', $komoditasId)
            ->where('bulan', $targetBulan)
            ->where('tahun', $targetTahun);

        if ($excludeLaporanId) {
            $query->where('id', '!=', $excludeLaporanId);
        }

        $targetLaporan = $query->first();

        // Batas maksimal panen adalah luas tanam pada bulan target tersebut
        $maxPanen = $targetLaporan ? floatval($targetLaporan->luas_tanam) : 0.00;

        return [
            'has_duration_limit' => true,
            'is_tanaman_pangan' => true, // backward compat
            'max_panen' => $maxPanen,
            'durasi' => $durasi,
            'komoditas_nama' => $komoditas->nama
        ];
    }
}
