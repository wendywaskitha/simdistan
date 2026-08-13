<?php

namespace App\Http\Controllers;

use App\Models\LaporanProduksi;
use App\Models\KategoriKomoditas;
use App\Models\Kecamatan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StatistikController extends Controller
{
    /**
     * Tampilkan halaman utama Statistik
     */
    public function index(): View
    {
        $kecamatans = Kecamatan::all();
        // Tahun terbaru yang ada data — fallback ke tahun sekarang
        $tahunDefault = LaporanProduksi::max('tahun') ?? intval(date('Y'));
        return view('statistik.index', compact('kecamatans', 'tahunDefault'));
    }

    /**
     * API: Ambil data statistik Produksi & Luas untuk Tanaman Pangan, Hortikultura, Perkebunan
     */
    public function dataProduksi(Request $request): JsonResponse
    {
        $tahun = $request->input('tahun', date('Y'));
        $kecamatanId = $request->input('kecamatan_id');

        $kategoriPangan = KategoriKomoditas::where('nama', 'LIKE', '%Pangan%')->first();
        $kategoriHorti = KategoriKomoditas::where('nama', 'LIKE', '%Hortikultura%')->first();
        $kategoriBun = KategoriKomoditas::where('nama', 'LIKE', '%Perkebunan%')->first();

        // 1. Pangan Detail Data
        $panganData = [];
        if ($kategoriPangan) {
            // Stats utama per komoditas
            $panganStats = DB::table('laporan_produksis')
                ->join('komoditas', 'laporan_produksis.komoditas_id', '=', 'komoditas.id')
                ->select(
                    'komoditas.nama as komoditas_nama',
                    DB::raw('SUM(laporan_produksis.luas_tanam) as total_luas_tanam'),
                    DB::raw('SUM(laporan_produksis.luas_panen) as total_luas_panen'),
                    DB::raw('SUM(laporan_produksis.luas_rusak) as total_luas_rusak'),
                    DB::raw('SUM(laporan_produksis.produksi) as total_produksi')
                )
                ->where('laporan_produksis.kategori_komoditas_id', $kategoriPangan->id)
                ->where('laporan_produksis.tahun', $tahun)
                ->whereNull('laporan_produksis.deleted_at');
            
            if ($kecamatanId) {
                $panganStats->where('laporan_produksis.kecamatan_id', $kecamatanId);
            }
            $panganData['stats'] = $panganStats->groupBy('komoditas.nama')->get();

            // Bulanan tren
            $panganBulanan = DB::table('laporan_produksis')
                ->select(
                    'bulan',
                    DB::raw('SUM(luas_tanam) as total_tanam'),
                    DB::raw('SUM(luas_panen) as total_panen'),
                    DB::raw('SUM(produksi) as total_produksi')
                )
                ->where('kategori_komoditas_id', $kategoriPangan->id)
                ->where('tahun', $tahun)
                ->whereNull('deleted_at');
            if ($kecamatanId) {
                $panganBulanan->where('kecamatan_id', $kecamatanId);
            }
            $panganData['bulanan'] = $panganBulanan->groupBy('bulan')->orderBy('bulan')->get();

            // Target vs Realisasi Tanam per Komoditas
            $targetQuery = DB::table('target_tanams')
                ->join('komoditas', 'target_tanams.komoditas_id', '=', 'komoditas.id')
                ->select('komoditas.nama as komoditas_nama', DB::raw('SUM(target_tanams.target) as total_target'))
                ->where('target_tanams.tahun', $tahun);
            if ($kecamatanId) {
                $targetQuery->where('target_tanams.kecamatan_id', $kecamatanId);
            }
            $panganData['target'] = $targetQuery->groupBy('komoditas.nama')->get();
        }

        $data = [
            'pangan' => $panganData,
            'horti'  => $this->getKategoriStats($kategoriHorti?->id, $tahun, $kecamatanId),
            'bun'    => $this->getKategoriStats($kategoriBun?->id, $tahun, $kecamatanId),
        ];

        return response()->json($data);
    }

    /**
     * API: Ambil data Alsintan
     */
    public function dataAlsintan(): JsonResponse
    {
        $stats = DB::table('alsintans')
            ->join('jenis_alats', 'alsintans.jenis_alat_id', '=', 'jenis_alats.id')
            ->select('jenis_alats.nama as jenis', DB::raw('COUNT(alsintans.id) as total'))
            ->whereNull('alsintans.deleted_at')
            ->groupBy('jenis_alats.nama')
            ->get();

        return response()->json($stats);
    }

    /**
     * API: Ambil data Infrastruktur
     */
    public function dataInfrastruktur(): JsonResponse
    {
        $stats = DB::table('infrastrukturs')
            ->select('jenis_infrastruktur as jenis', DB::raw('count(*) as total'))
            ->whereNull('deleted_at')
            ->groupBy('jenis_infrastruktur')
            ->get();

        return response()->json($stats);
    }

    /**
     * API: Ambil data Pupuk
     */
    public function dataPupuk(Request $request): JsonResponse
    {
        $tahun = $request->input('tahun', date('Y'));

        $stats = DB::table('laporan_pupuk_details')
            ->join('laporan_pupuks', 'laporan_pupuk_details.laporan_pupuk_id', '=', 'laporan_pupuks.id')
            ->join('jenis_pupuks', 'laporan_pupuk_details.jenis_pupuk_id', '=', 'jenis_pupuks.id')
            ->select('jenis_pupuks.nama as jenis', DB::raw('SUM(laporan_pupuk_details.penebusan) as total_penyaluran'))
            ->where('laporan_pupuks.tahun', $tahun)
            ->groupBy('jenis_pupuks.nama')
            ->get();

        return response()->json($stats);
    }

    private function getKategoriStats(?int $kategoriId, int $tahun, ?int $kecamatanId)
    {
        if (!$kategoriId) return [];

        $query = DB::table('laporan_produksis')
            ->join('komoditas', 'laporan_produksis.komoditas_id', '=', 'komoditas.id')
            ->select(
                'komoditas.nama as komoditas_nama',
                DB::raw('SUM(laporan_produksis.luas_panen) as total_luas_panen'),
                DB::raw('SUM(laporan_produksis.produksi) as total_produksi')
            )
            ->where('laporan_produksis.kategori_komoditas_id', $kategoriId)
            ->where('laporan_produksis.tahun', $tahun)
            ->whereNull('laporan_produksis.deleted_at');

        if ($kecamatanId) {
            $query->where('laporan_produksis.kecamatan_id', $kecamatanId);
        }

        return $query->groupBy('komoditas.nama')->get();
    }
}
