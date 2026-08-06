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

        $totalPetani = DB::table('petanis')->count();
        $totalAlsintan = DB::table('alsintans')->whereNull('deleted_at')->count();
        $totalInfra = DB::table('infrastrukturs')->whereNull('deleted_at')->count();

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
            'komoditasDropdown'
        ));
    }
}
