<?php

namespace App\Http\Controllers;

use App\Http\Requests\LaporanProduksiRequest;
use App\Services\LaporanProduksiService;
use App\Services\KecamatanService;
use App\Services\KomoditasService;
use App\Services\SatuanService;
use App\Services\KategoriKomoditasService;
use App\Models\KategoriKomoditas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class TanamanPanganController extends Controller
{
    protected $laporanService;
    protected $kecamatanService;
    protected $komoditasService;
    protected $satuanService;
    protected $kategoriService;
    protected $kategori;

    public function __construct(
        LaporanProduksiService $laporanService,
        KecamatanService $kecamatanService,
        KomoditasService $komoditasService,
        SatuanService $satuanService,
        KategoriKomoditasService $kategoriService
    ) {
        $this->laporanService = $laporanService;
        $this->kecamatanService = $kecamatanService;
        $this->komoditasService = $komoditasService;
        $this->satuanService = $satuanService;
        $this->kategoriService = $kategoriService;
        
        $this->kategori = KategoriKomoditas::where('nama', 'Tanaman Pangan')->first();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $activeKategoriId = $this->kategori->id;
        $currentYear = intval(date('Y'));
        $years = range($currentYear - 4, $currentYear);

        if ($request->ajax()) {
            $kecamatanId = $request->get('kecamatan_id');

            $komoditasList = \App\Models\Komoditas::where('kategori_komoditas_id', $activeKategoriId)->get();
            
            $query = \App\Models\LaporanProduksi::where('kategori_komoditas_id', $activeKategoriId)
                ->whereBetween('tahun', [$years[0], end($years)]);

            if ($kecamatanId) {
                $query->where('kecamatan_id', $kecamatanId);
            }

            $laporans = $query->get();

            $data = [];
            foreach ($komoditasList as $komoditas) {
                $row = [
                    'komoditas_id' => $komoditas->id,
                    'komoditas_nama' => $komoditas->nama,
                ];

                foreach ($years as $tahun) {
                    $laporanTahun = $laporans->where('komoditas_id', $komoditas->id)->where('tahun', $tahun);
                    
                    $row['tanam_' . $tahun] = $laporanTahun->sum('luas_tanam');
                    $row['panen_' . $tahun] = $laporanTahun->sum('luas_panen');
                    $row['produksi_' . $tahun] = $laporanTahun->sum('produksi');
                    $row['produktivitas_' . $tahun] = $laporanTahun->avg('produktivitas') ?: 0;
                    $row['lahan_' . $tahun] = $laporanTahun->sum('luas_lahan');
                }

                $data[] = $row;
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row) use ($kecamatanId) {
                    if (!$kecamatanId) {
                        return '
                            <button class="btn btn-sm btn-secondary rounded-3 px-3" disabled title="Pilih Kecamatan untuk mengelola data">
                                <i class="bi bi-pencil-square me-1"></i> Kelola
                            </button>
                        ';
                    }
                    $kelolaUrl = route('tanaman-pangan.kelola', [
                        'komoditas_id' => $row['komoditas_id'],
                        'kecamatan_id' => $kecamatanId
                    ]);
                    return '
                        <a href="'.$kelolaUrl.'" class="btn btn-sm btn-success rounded-3 px-3">
                            <i class="bi bi-pencil-square me-1"></i> Kelola
                        </a>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $kecamatans = $this->kecamatanService->getAllKecamatan()->pluck('nama', 'id')->toArray();
        $komoditasList = \App\Models\Komoditas::where('kategori_komoditas_id', $activeKategoriId)->get();
        return view('produksi.tanaman-pangan.index', [
            'activeKategoriId' => $activeKategoriId,
            'kategori' => $this->kategori,
            'kecamatans' => $kecamatans,
            'komoditasList' => $komoditasList,
            'years' => $years
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $kecamatans = $this->kecamatanService->getAllKecamatan()->pluck('nama', 'id')->toArray();
        $komoditasList = $this->komoditasService->getAllKomoditas()
            ->where('kategori_komoditas_id', $this->kategori->id);

        $satuans = $this->satuanService->getAllSatuan()->pluck('nama', 'id')->toArray();

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return view('produksi.create', [
            'kategori' => $this->kategori,
            'kecamatans' => $kecamatans,
            'komoditasList' => $komoditasList,
            'satuans' => $satuans,
            'isTanamanPangan' => true,
            'months' => $months
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LaporanProduksiRequest $request): RedirectResponse
    {
        $this->laporanService->createLaporan($request->validated());

        return redirect()->route('tanaman-pangan.index')
            ->with('success', 'Laporan Produksi Tanaman Pangan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $laporan = $this->laporanService->getLaporanById($id);
        if (!$laporan) {
            abort(404);
        }

        $kecamatans = $this->kecamatanService->getAllKecamatan()->pluck('nama', 'id')->toArray();
        $komoditas = $this->komoditasService->getAllKomoditas()
            ->where('kategori_komoditas_id', $this->kategori->id)
            ->pluck('nama', 'id')
            ->toArray();

        $satuans = $this->satuanService->getAllSatuan()->pluck('nama', 'id')->toArray();

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $mingguans = $laporan->mingguans->sortBy('minggu_ke')->values()->toArray();

        return view('produksi.edit', [
            'laporan' => $laporan,
            'kategori' => $this->kategori,
            'kecamatans' => $kecamatans,
            'komoditas' => $komoditas,
            'satuans' => $satuans,
            'isTanamanPangan' => true,
            'months' => $months,
            'mingguans' => $mingguans
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LaporanProduksiRequest $request, int $id): RedirectResponse
    {
        $this->laporanService->updateLaporan($id, $request->validated());

        return redirect()->route('tanaman-pangan.index')
            ->with('success', 'Laporan Produksi Tanaman Pangan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->laporanService->deleteLaporan($id);

        return redirect()->route('tanaman-pangan.index')
            ->with('success', 'Laporan Produksi Tanaman Pangan berhasil dihapus.');
    }

    /**
     * Display monthly matrix of kecamatan for a selected commodity and year.
     */
    public function kelola(Request $request): View
    {
        $komoditasId = $request->get('komoditas_id');
        $komoditas = \App\Models\Komoditas::findOrFail($komoditasId);

        $tahun = $request->get('tahun', date('Y'));
        $currentYear = intval(date('Y'));
        $years = range($currentYear - 4, $currentYear);

        $kecamatans = $this->kecamatanService->getAllKecamatan();
        $laporans = \App\Models\LaporanProduksi::where('komoditas_id', $komoditasId)
            ->where('tahun', $tahun)
            ->get();

        return view('produksi.tanaman-pangan.kelola', [
            'komoditas' => $komoditas,
            'kategori' => $this->kategori,
            'tahun' => $tahun,
            'years' => $years,
            'kecamatans' => $kecamatans,
            'laporans' => $laporans
        ]);
    }

    /**
     * Show form to input/edit weekly data for a selected kecamatan, month, year.
     */
    public function inputMingguan(Request $request): View
    {
        $kecamatanId = $request->get('kecamatan_id');
        $komoditasId = $request->get('komoditas_id');
        $tahun = $request->get('tahun');
        $bulan = $request->get('bulan', date('n'));

        $kecamatan = \App\Models\Kecamatan::findOrFail($kecamatanId);
        $komoditas = \App\Models\Komoditas::findOrFail($komoditasId);

        $laporan = \App\Models\LaporanProduksi::where('kecamatan_id', $kecamatanId)
            ->where('komoditas_id', $komoditasId)
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->first();

        $satuans = $this->satuanService->getAllSatuan()->pluck('nama', 'id')->toArray();

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $mingguans = [];
        if ($laporan) {
            $mingguans = $laporan->mingguans->sortBy('minggu_ke')->values()->toArray();
        }

        return view('produksi.tanaman-pangan.input_mingguan', [
            'kecamatan' => $kecamatan,
            'komoditas' => $komoditas,
            'kategori' => $this->kategori,
            'tahun' => $tahun,
            'bulan' => $bulan,
            'laporan' => $laporan,
            'satuans' => $satuans,
            'months' => $months,
            'mingguans' => $mingguans
        ]);
    }

    /**
     * Save weekly production reports.
     */
    public function simpanMingguan(Request $request): RedirectResponse
    {
        $request->validate([
            'kecamatan_id' => ['required', 'exists:kecamatans,id'],
            'komoditas_id' => ['required', 'exists:komoditas,id'],
            'tahun' => ['required', 'integer'],
            'bulan' => ['required', 'integer', 'between:1,12'],
            'satuan_id' => ['required', 'exists:satuans,id'],
            'mingguans' => ['required', 'array', 'size:4'],
            'mingguans.*.luas_tanam' => ['required', 'numeric', 'min:0'],
            'mingguans.*.luas_panen' => ['required', 'numeric', 'min:0'],
            'mingguans.*.produktivitas' => ['required', 'numeric', 'min:0'],
            'mingguans.*.produksi' => ['required', 'numeric', 'min:0'],
            'mingguans.*.luas_lahan' => ['required', 'numeric', 'min:0'],
        ]);

        $kecamatanId = $request->kecamatan_id;
        $komoditasId = $request->komoditas_id;
        $tahun = $request->tahun;
        $bulan = $request->bulan;

        $komoditas = \App\Models\Komoditas::findOrFail($komoditasId);

        // Hitung total inputan dari request mingguan
        $totalTanam = 0;
        $totalPanen = 0;
        foreach ($request->mingguans as $m) {
            $totalTanam += floatval($m['luas_tanam'] ?? 0);
            $totalPanen += floatval($m['luas_panen'] ?? 0);
        }

        // Dapatkan data laporan lama jika sedang diedit/simpan ulang
        $existingLaporan = \App\Models\LaporanProduksi::where('kecamatan_id', $kecamatanId)
            ->where('komoditas_id', $komoditasId)
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->first();

        // Validasi batasan panen (Alternatif A)
        $result = $this->laporanService->calculateMaxHarvestArea(
            (int) $kecamatanId,
            (int) $komoditasId,
            (int) $bulan,
            (int) $tahun,
            $totalTanam,
            $existingLaporan ? $existingLaporan->id : null
        );

        if ($result['is_tanaman_pangan'] && $totalPanen > $result['max_panen']) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'total_luas_panen' => "Total Luas Panen ({$totalPanen} Ha) tidak boleh melebihi Luas Tanam pada {$result['durasi']} bulan sebelumnya (Maksimal: " . number_format($result['max_panen'], 2) . " Ha)."
                ]);
        }

        DB::transaction(function() use ($request, $kecamatanId, $komoditasId, $tahun, $bulan, $komoditas, $totalTanam, $totalPanen) {
            $totalProduksi = 0;
            $totalLahan = 0;

            foreach ($request->mingguans as $m) {
                $totalProduksi += floatval($m['produksi'] ?? 0);
                $totalLahan += floatval($m['luas_lahan'] ?? 0);
            }

            $produktivitas = $totalPanen > 0 ? ($totalProduksi / $totalPanen) : 0;

            $laporan = \App\Models\LaporanProduksi::updateOrCreate(
                [
                    'kecamatan_id' => $kecamatanId,
                    'komoditas_id' => $komoditasId,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                ],
                [
                    'kategori_komoditas_id' => $komoditas->kategori_komoditas_id,
                    'satuan_id' => $request->satuan_id,
                    'luas_tanam' => $totalTanam,
                    'luas_panen' => $totalPanen,
                    'produktivitas' => $produktivitas,
                    'produksi' => $totalProduksi,
                    'luas_lahan' => $totalLahan,
                ]
            );

            $laporan->mingguans()->delete();
            foreach ($request->mingguans as $index => $m) {
                $laporan->mingguans()->create([
                    'minggu_ke' => $index + 1,
                    'luas_tanam' => floatval($m['luas_tanam'] ?? 0),
                    'luas_panen' => floatval($m['luas_panen'] ?? 0),
                    'produktivitas' => floatval($m['produktivitas'] ?? 0),
                    'produksi' => floatval($m['produksi'] ?? 0),
                    'luas_lahan' => floatval($m['luas_lahan'] ?? 0),
                ]);
            }
        });

        return redirect()->route('tanaman-pangan.kelola', [
            'komoditas_id' => $komoditasId,
            'tahun' => $tahun
        ])->with('success', 'Laporan Mingguan berhasil disimpan.');
    }

    /**
     * Get annual and monthly time-series data for chart visualization.
     */
    public function dataGrafik(Request $request): \Illuminate\Http\JsonResponse
    {
        $komoditasId = $request->get('komoditas_id');
        $kecamatanId = $request->get('kecamatan_id');
        $tahunBulanan = intval($request->get('tahun_bulanan', date('Y')));
        $kategoriId = $this->kategori->id;

        $currentYear = intval(date('Y'));
        $years = range($currentYear - 4, $currentYear);

        // Query Laporan Tahunan
        $queryTahun = \App\Models\LaporanProduksi::where('kategori_komoditas_id', $kategoriId)
            ->where('komoditas_id', $komoditasId)
            ->whereBetween('tahun', [$years[0], end($years)]);

        if ($kecamatanId) {
            $queryTahun->where('kecamatan_id', $kecamatanId);
        }
        $laporansTahun = $queryTahun->get();

        $tanamTahun = [];
        $panenTahun = [];
        $produksiTahun = [];
        $lahanTahun = [];

        foreach ($years as $tahun) {
            $lT = $laporansTahun->where('tahun', $tahun);
            $tanamTahun[] = doubleval($lT->sum('luas_tanam'));
            $panenTahun[] = doubleval($lT->sum('luas_panen'));
            $produksiTahun[] = doubleval($lT->sum('produksi'));
            $lahanTahun[] = doubleval($lT->sum('luas_lahan'));
        }

        // Query Laporan Bulanan (1-12) untuk tahun terpilih
        $queryBulan = \App\Models\LaporanProduksi::where('kategori_komoditas_id', $kategoriId)
            ->where('komoditas_id', $komoditasId)
            ->where('tahun', $tahunBulanan);

        if ($kecamatanId) {
            $queryBulan->where('kecamatan_id', $kecamatanId);
        }
        $laporansBulan = $queryBulan->get();

        $months = range(1, 12);
        $monthNames = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agt", "Sep", "Okt", "Nov", "Des"];
        $tanamBulan = [];
        $panenBulan = [];
        $produksiBulan = [];
        $lahanBulan = [];

        foreach ($months as $m) {
            $lB = $laporansBulan->where('bulan', $m);
            $tanamBulan[] = doubleval($lB->sum('luas_tanam'));
            $panenBulan[] = doubleval($lB->sum('luas_panen'));
            $produksiBulan[] = doubleval($lB->sum('produksi'));
            $lahanBulan[] = doubleval($lB->sum('luas_lahan'));
        }

        return response()->json([
            'years' => $years,
            'luas_tanam' => $tanamTahun,
            'luas_panen' => $panenTahun,
            'produksi' => $produksiTahun,
            'luas_lahan' => $lahanTahun,
            'months' => $monthNames,
            'bulanan_tanam' => $tanamBulan,
            'bulanan_panen' => $panenBulan,
            'bulanan_produksi' => $produksiBulan,
            'bulanan_lahan' => $lahanBulan,
        ]);
    }
}
