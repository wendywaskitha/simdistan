<?php

namespace App\Http\Controllers;

use App\Models\KategoriKomoditas;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Deteksi role Kepala Dinas
        if (auth()->user()->hasRole('Kepala Dinas')) {
            return $this->renderKadinDashboard();
        }

        // Hitung data riil dari database untuk operator/admin
        $totalPoktan = DB::table('kelompok_tanis')->count();
        $totalPenyuluh = DB::table('penyuluhs')->count();
        $totalPetani = DB::table('petanis')->count();
        $totalKomoditas = DB::table('komoditas')->count();
        $totalAlsintan = DB::table('alsintans')->whereNull('deleted_at')->count();
        $totalLuasPanen = DB::table('laporan_produksis')->whereNull('deleted_at')->sum('luas_panen');

        // Peta Ringkasan Produksi Terkini (5 laporan terbaru)
        $laporanTerbaru = DB::table('laporan_produksis')
            ->join('kecamatans', 'laporan_produksis.kecamatan_id', '=', 'kecamatans.id')
            ->join('komoditas', 'laporan_produksis.komoditas_id', '=', 'komoditas.id')
            ->select('laporan_produksis.*', 'kecamatans.nama as kecamatan_nama', 'komoditas.nama as komoditas_nama')
            ->whereNull('laporan_produksis.deleted_at')
            ->orderBy('laporan_produksis.created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalPoktan',
            'totalPenyuluh',
            'totalPetani',
            'totalKomoditas',
            'totalAlsintan',
            'totalLuasPanen',
            'laporanTerbaru'
        ));
    }

    /**
     * Endpoint AJAX untuk mendapatkan tren produksi per komoditas selama 5 tahun terakhir
     */
    public function getKomoditasTrend(\Illuminate\Http\Request $request): JsonResponse
    {
        $komoditasId = $request->get('komoditas_id');
        $currentYear = (int) date('Y');
        $yearsList = range($currentYear - 4, $currentYear);

        $trendData = [];
        foreach ($yearsList as $yr) {
            $query = DB::table('laporan_produksis')
                ->where('tahun', $yr)
                ->whereNull('deleted_at');
            
            if ($komoditasId) {
                $query->where('komoditas_id', $komoditasId);
            }

            $trendData[] = (float) $query->sum('produksi');
        }

        return response()->json([
            'years' => $yearsList,
            'data'  => $trendData
        ]);
    }

    /**
     * Render Dashboard Eksekutif Kadin (Light Theme Monitoring Detail)
     */
    private function renderKadinDashboard(): View
    {
        // 1. Data produksi komoditas pangan, hortikultura, perkebunan
        $idPangan = KategoriKomoditas::where('nama', 'LIKE', '%Pangan%')->first()?->id;
        $idHorti = KategoriKomoditas::where('nama', 'LIKE', '%Hortikultura%')->first()?->id;
        $idBun = KategoriKomoditas::where('nama', 'LIKE', '%Perkebunan%')->first()?->id;

        $panganProduksi = DB::table('laporan_produksis')->where('kategori_komoditas_id', $idPangan)->whereNull('deleted_at')->sum('produksi');
        $hortiProduksi = DB::table('laporan_produksis')->where('kategori_komoditas_id', $idHorti)->whereNull('deleted_at')->sum('produksi');
        $bunProduksi = DB::table('laporan_produksis')->where('kategori_komoditas_id', $idBun)->whereNull('deleted_at')->sum('produksi');

        $panganLuas = DB::table('laporan_produksis')->where('kategori_komoditas_id', $idPangan)->whereNull('deleted_at')->sum('luas_panen');
        $hortiLuas = DB::table('laporan_produksis')->where('kategori_komoditas_id', $idHorti)->whereNull('deleted_at')->sum('luas_panen');
        $bunLuas = DB::table('laporan_produksis')->where('kategori_komoditas_id', $idBun)->whereNull('deleted_at')->sum('luas_panen');

        // Daftar komoditas untuk dropdown filter
        $komoditasDropdown = DB::table('komoditas')->orderBy('nama')->get();

        // Detail list komoditas pangan & horti terperinci
        $detailKomoditas = DB::table('laporan_produksis')
            ->join('komoditas', 'laporan_produksis.komoditas_id', '=', 'komoditas.id')
            ->join('kategori_komoditas', 'laporan_produksis.kategori_komoditas_id', '=', 'kategori_komoditas.id')
            ->select('komoditas.nama as komoditas_nama', 'kategori_komoditas.nama as kategori_nama', DB::raw('SUM(laporan_produksis.luas_panen) as total_luas'), DB::raw('SUM(laporan_produksis.produksi) as total_produksi'))
            ->whereNull('laporan_produksis.deleted_at')
            ->groupBy('komoditas.nama', 'kategori_komoditas.nama')
            ->orderBy('total_produksi', 'desc')
            ->get();

        // 2. Monitoring Alsintan berdasarkan kondisi
        $alsintanKondisi = DB::table('alsintans')
            ->select('kondisi', DB::raw('count(*) as total'))
            ->whereNull('deleted_at')
            ->groupBy('kondisi')
            ->get();

        // 3. Realisasi Pupuk Bersubsidi
        $pupukRealisasi = DB::table('laporan_pupuk_details')
            ->join('kecamatans', 'laporan_pupuk_details.kecamatan_id', '=', 'kecamatans.id')
            ->join('jenis_pupuks', 'laporan_pupuk_details.jenis_pupuk_id', '=', 'jenis_pupuks.id')
            ->select('kecamatans.nama as kecamatan_nama', 'jenis_pupuks.nama as jenis_pupuk', DB::raw('SUM(laporan_pupuk_details.penebusan) as total_penebusan'))
            ->groupBy('kecamatans.nama', 'jenis_pupuks.nama')
            ->orderBy('kecamatans.nama')
            ->get();

        // 4. Daftar Infrastruktur Irigasi Terbangun
        $listInfrastruktur = DB::table('infrastrukturs')
            ->join('kecamatans', 'infrastrukturs.kecamatan_id', '=', 'kecamatans.id')
            ->select('infrastrukturs.*', 'kecamatans.nama as kecamatan_nama')
            ->whereNull('infrastrukturs.deleted_at')
            ->orderBy('infrastrukturs.created_at', 'desc')
            ->limit(5)
            ->get();

        // 5. Tren Fluktuasi Produksi per 5 Tahun terakhir (Default Semua)
        $currentYear = (int) date('Y');
        $yearsList = range($currentYear - 4, $currentYear);

        $panganTrend = [];
        $hortiTrend = [];
        $bunTrend = [];

        foreach ($yearsList as $yr) {
            $panganTrend[] = (float) DB::table('laporan_produksis')
                ->where('kategori_komoditas_id', $idPangan)
                ->where('tahun', $yr)
                ->whereNull('deleted_at')
                ->sum('produksi');

            $hortiTrend[] = (float) DB::table('laporan_produksis')
                ->where('kategori_komoditas_id', $idHorti)
                ->where('tahun', $yr)
                ->whereNull('deleted_at')
                ->sum('produksi');

            $bunTrend[] = (float) DB::table('laporan_produksis')
                ->where('kategori_komoditas_id', $idBun)
                ->where('tahun', $yr)
                ->whereNull('deleted_at')
                ->sum('produksi');
        }

        $rincianKategoriKomoditas = [];
        $categories = DB::table('kategori_komoditas')->get();
        foreach ($categories as $cat) {
            $catTrend = [];
            foreach ($yearsList as $yr) {
                $catTrend[] = (float) DB::table('laporan_produksis')
                    ->where('kategori_komoditas_id', $cat->id)
                    ->where('tahun', $yr)
                    ->whereNull('deleted_at')
                    ->sum('produksi');
            }

            $suffix = '';
            if (stripos($cat->nama, 'pangan') !== false) {
                $suffix = ' (Ton)';
            } elseif (stripos($cat->nama, 'horti') !== false) {
                $suffix = ' (Kw/Kg)';
            } elseif (stripos($cat->nama, 'kebun') !== false) {
                $suffix = ' (Kg)';
            }

            $komoditasList = DB::table('komoditas')
                ->where('kategori_komoditas_id', $cat->id)
                ->whereNull('deleted_at')
                ->get();

            $komoditasData = [];
            foreach ($komoditasList as $kom) {
                $komTrend = [];
                foreach ($yearsList as $yr) {
                    $komTrend[] = (float) DB::table('laporan_produksis')
                        ->where('komoditas_id', $kom->id)
                        ->where('tahun', $yr)
                        ->whereNull('deleted_at')
                        ->sum('produksi');
                }
                $komoditasData[] = [
                    'nama' => $kom->nama,
                    'trend' => $komTrend
                ];
            }

            $rincianKategoriKomoditas[] = [
                'id' => $cat->id,
                'nama' => $cat->nama . $suffix,
                'trend' => $catTrend,
                'komoditas' => $komoditasData
            ];
        }

        $totalPetani = DB::table('petanis')->count();
        $totalAlsintan = DB::table('alsintans')->whereNull('deleted_at')->count();
        $totalInfra = DB::table('infrastrukturs')->whereNull('deleted_at')->count();
        $listKecamatan = DB::table('kecamatans')->orderBy('nama')->get();

        return view('dashboard_kadin', compact(
            'panganProduksi',
            'hortiProduksi',
            'bunProduksi',
            'panganLuas',
            'hortiLuas',
            'bunLuas',
            'detailKomoditas',
            'alsintanKondisi',
            'pupukRealisasi',
            'listInfrastruktur',
            'yearsList',
            'panganTrend',
            'hortiTrend',
            'bunTrend',
            'totalPetani',
            'totalAlsintan',
            'totalInfra',
            'komoditasDropdown',
            'listKecamatan',
            'rincianKategoriKomoditas'
        ));
    }

    /**
     * AJAX endpoint to get regional detail by kecamatan_id (Bupati monitoring view).
     */
    public function getRegionalDetail(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        $kecId = $request->get('kecamatan_id');
        $tahun = $request->get('tahun');

        if (!$kecId) {
            return response()->json(['error' => 'Kecamatan ID is required'], 400);
        }

        // 1. Luas Panen & Produksi Terkini
        $queryLuas = DB::table('laporan_produksis')->whereNull('deleted_at');
        $queryProd = DB::table('laporan_produksis')->whereNull('deleted_at');
        
        if ($kecId !== 'all') {
            $queryLuas->where('kecamatan_id', $kecId);
            $queryProd->where('kecamatan_id', $kecId);
        }
        if ($tahun) {
            $queryLuas->where('tahun', $tahun);
            $queryProd->where('tahun', $tahun);
        }
        $totalLuasPanen = $queryLuas->sum('luas_panen');
        $totalProduksi = $queryProd->sum('produksi');

        // 2. Bantuan Alsintan
        $queryAlsintan = DB::table('alsintans')
            ->join('kelompok_tanis', 'alsintans.kelompok_tani_id', '=', 'kelompok_tanis.id')
            ->join('desas', 'kelompok_tanis.desa_id', '=', 'desas.id')
            ->leftJoin('jenis_alats', 'alsintans.jenis_alat_id', '=', 'jenis_alats.id')
            ->select('alsintans.*', 'desas.nama as desa_nama', 'kelompok_tanis.nama as poktan_nama', 'jenis_alats.nama as jenis_alat_nama')
            ->whereNull('alsintans.deleted_at');
            
        if ($kecId !== 'all') {
            $queryAlsintan->where('desas.kecamatan_id', $kecId);
        }
        if ($tahun) {
            $queryAlsintan->where('alsintans.tahun_bantuan', $tahun);
        }
        $alsintans = $queryAlsintan->get();

        // 3. Infrastruktur & Irigasi
        $queryInfra = DB::table('infrastrukturs')
            ->leftJoin('desas', 'infrastrukturs.desa_id', '=', 'desas.id')
            ->leftJoin('kelompok_tanis', 'infrastrukturs.kelompok_tani_id', '=', 'kelompok_tanis.id')
            ->select('infrastrukturs.*', 'desas.nama as desa_nama', 'kelompok_tanis.nama as poktan_nama')
            ->whereNull('infrastrukturs.deleted_at');
            
        if ($kecId !== 'all') {
            $queryInfra->where('infrastrukturs.kecamatan_id', $kecId);
        }
        if ($tahun) {
            $queryInfra->where('infrastrukturs.tahun_anggaran', $tahun);
        }
        $infrastrukturs = $queryInfra->get();

        // 4. Bantuan Benih Pangan
        $queryBenih = DB::table('bantuan_benih_pangans')
            ->join('kelompok_tanis', 'bantuan_benih_pangans.kelompok_tani_id', '=', 'kelompok_tanis.id')
            ->join('desas', 'kelompok_tanis.desa_id', '=', 'desas.id')
            ->join('komoditas', 'bantuan_benih_pangans.komoditas_id', '=', 'komoditas.id')
            ->leftJoin('varietas', 'bantuan_benih_pangans.varietas_id', '=', 'varietas.id')
            ->select('bantuan_benih_pangans.*', 'desas.nama as desa_nama', 'kelompok_tanis.nama as poktan_nama', 'komoditas.nama as komoditas_nama', 'varietas.nama as varietas_nama');
            
        if ($kecId !== 'all') {
            $queryBenih->where('desas.kecamatan_id', $kecId);
        }
        if ($tahun) {
            $queryBenih->where('bantuan_benih_pangans.tahun_bantuan', $tahun);
        }
        $benihPangans = $queryBenih->get();

        // 5. Bantuan Bibit Horti
        $queryBibitHorti = DB::table('bantuan_bibit_hortis')
            ->join('kelompok_tanis', 'bantuan_bibit_hortis.kelompok_tani_id', '=', 'kelompok_tanis.id')
            ->join('desas', 'kelompok_tanis.desa_id', '=', 'desas.id')
            ->join('komoditas', 'bantuan_bibit_hortis.komoditas_id', '=', 'komoditas.id')
            ->select('bantuan_bibit_hortis.*', 'desas.nama as desa_nama', 'kelompok_tanis.nama as poktan_nama', 'komoditas.nama as komoditas_nama');
            
        if ($kecId !== 'all') {
            $queryBibitHorti->where('desas.kecamatan_id', $kecId);
        }
        if ($tahun) {
            $queryBibitHorti->where('bantuan_bibit_hortis.tahun_bantuan', $tahun);
        }
        $bibitHortis = $queryBibitHorti->get();

        // 6. Bantuan Bibit Perkebunan
        $queryBibitBun = DB::table('bantuan_bibit_perkebunans')
            ->join('kelompok_tanis', 'bantuan_bibit_perkebunans.kelompok_tani_id', '=', 'kelompok_tanis.id')
            ->join('desas', 'kelompok_tanis.desa_id', '=', 'desas.id')
            ->join('komoditas', 'bantuan_bibit_perkebunans.komoditas_id', '=', 'komoditas.id')
            ->select('bantuan_bibit_perkebunans.*', 'desas.nama as desa_nama', 'kelompok_tanis.nama as poktan_nama', 'komoditas.nama as komoditas_nama');
            
        if ($kecId !== 'all') {
            $queryBibitBun->where('desas.kecamatan_id', $kecId);
        }
        if ($tahun) {
            $queryBibitBun->where('bantuan_bibit_perkebunans.tahun_bantuan', $tahun);
        }
        $bibitPerkebunans = $queryBibitBun->get();

        // 1b. Breakdown per Komoditas
        $queryBreakdown = DB::table('laporan_produksis')
            ->join('komoditas', 'laporan_produksis.komoditas_id', '=', 'komoditas.id')
            ->join('kategori_komoditas', 'laporan_produksis.kategori_komoditas_id', '=', 'kategori_komoditas.id')
            ->select(
                'komoditas.nama as komoditas_nama',
                'kategori_komoditas.nama as kategori_nama',
                DB::raw('SUM(laporan_produksis.luas_panen) as total_luas'),
                DB::raw('SUM(laporan_produksis.produksi) as total_produksi')
            )
            ->whereNull('laporan_produksis.deleted_at');
            
        if ($kecId !== 'all') {
            $queryBreakdown->where('laporan_produksis.kecamatan_id', $kecId);
        }
        if ($tahun) {
            $queryBreakdown->where('laporan_produksis.tahun', $tahun);
        }
        $productionBreakdown = $queryBreakdown->groupBy('komoditas.nama', 'kategori_komoditas.nama')
            ->orderBy('total_produksi', 'desc')
            ->get();

        return response()->json([
            'luas_panen' => (float)$totalLuasPanen,
            'produksi' => (float)$totalProduksi,
            'production_breakdown' => $productionBreakdown,
            'alsintans' => $alsintans,
            'infrastrukturs' => $infrastrukturs,
            'benih_pangans' => $benihPangans,
            'bibit_hortis' => $bibitHortis,
            'bibit_perkebunans' => $bibitPerkebunans
        ]);
    }
}
