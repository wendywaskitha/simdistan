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
use Barryvdh\DomPDF\Facade\Pdf;

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

            // Load data luas lahan baku
            $lahanBakuQuery = \App\Models\LuasLahanBaku::whereBetween('tahun', [$years[0], end($years)]);
            if ($kecamatanId) {
                $lahanBakuQuery->where('kecamatan_id', $kecamatanId);
            }
            $lahanBakus = $lahanBakuQuery->get();

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
                    
                    // Lahan diambil dari data baku pertahun spesifik per KOMODITAS juga
                    $row['lahan_' . $tahun] = $lahanBakus->where('komoditas_id', (int) $komoditas->id)->where('tahun', (int) $tahun)->sum('luas_lahan');
                }

                $data[] = $row;
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row) {
                    $kelolaUrl = route('tanaman-pangan.kelola', [
                        'komoditas_id' => $row['komoditas_id'],
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

        $kecamatanListObj = \App\Models\Kecamatan::all();
        $kecamatans = $kecamatanListObj->pluck('nama', 'id')->toArray();
        $komoditasList = \App\Models\Komoditas::where('kategori_komoditas_id', $activeKategoriId)->get();

        // Ambil data luas lahan baku pertahun & komoditas untuk ditampilkan di form tab Luas Lahan Baku
        $lahanBakuMap = [];
        foreach ($years as $tahun) {
            foreach ($komoditasList as $kom) {
                foreach ($kecamatanListObj as $kec) {
                    $lahan = \App\Models\LuasLahanBaku::where('kecamatan_id', $kec->id)
                        ->where('komoditas_id', $kom->id)
                        ->where('tahun', $tahun)
                        ->first();
                    $lahanBakuMap[$tahun][$kom->id][$kec->id] = $lahan ? floatval($lahan->luas_lahan) : 0.00;
                }
            }
        }

        // Ambil data target tanam bulanan untuk ditampilkan di form tab Target Tanam
        $targetTanamMap = [];
        foreach ($years as $tahun) {
            foreach ($komoditasList as $kom) {
                foreach ($kecamatanListObj as $kec) {
                    for ($m = 1; $m <= 12; $m++) {
                        $targetObj = \App\Models\TargetTanam::where('kecamatan_id', $kec->id)
                            ->where('komoditas_id', $kom->id)
                            ->where('tahun', $tahun)
                            ->where('bulan', $m)
                            ->first();
                        $targetTanamMap[$tahun][$kom->id][$kec->id][$m] = $targetObj ? floatval($targetObj->target) : 0.00;
                    }
                }
            }
        }

        // Ambil realisasi luas tanam dan luas panen bulanan dari laporan_produksis
        $realisasiTanamMap = [];
        $realisasiPanenMap = [];
        foreach ($years as $tahun) {
            foreach ($komoditasList as $kom) {
                foreach ($kecamatanListObj as $kec) {
                    for ($m = 1; $m <= 12; $m++) {
                        $realisasi = \App\Models\LaporanProduksi::where('kecamatan_id', $kec->id)
                            ->where('komoditas_id', $kom->id)
                            ->where('tahun', $tahun)
                            ->where('bulan', $m)
                            ->first();
                        $realisasiTanamMap[$tahun][$kom->id][$kec->id][$m] = $realisasi ? floatval($realisasi->luas_tanam) : 0.00;
                        $realisasiPanenMap[$tahun][$kom->id][$kec->id][$m] = $realisasi ? floatval($realisasi->luas_panen) : 0.00;
                    }
                }
            }
        }

        return view('produksi.tanaman-pangan.index', [
            'activeKategoriId' => $activeKategoriId,
            'kategori' => $this->kategori,
            'kecamatans' => $kecamatans,
            'kecamatanListObj' => $kecamatanListObj,
            'komoditasList' => $komoditasList,
            'years' => $years,
            'lahanBakuMap' => $lahanBakuMap,
            'targetTanamMap' => $targetTanamMap,
            'realisasiTanamMap' => $realisasiTanamMap,
            'realisasiPanenMap' => $realisasiPanenMap
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

        $resultMax = $this->laporanService->calculateMaxHarvestArea(
            (int) $laporan->kecamatan_id,
            (int) $laporan->komoditas_id,
            (int) $laporan->bulan,
            (int) $laporan->tahun,
            0,
            $laporan->id
        );
        $maxPanen = $resultMax['max_panen'] ?? 0.00;

        return view('produksi.edit', [
            'laporan' => $laporan,
            'kategori' => $this->kategori,
            'kecamatans' => $kecamatans,
            'komoditas' => $komoditas,
            'satuans' => $satuans,
            'isTanamanPangan' => true,
            'months' => $months,
            'mingguans' => $mingguans,
            'maxPanen' => $maxPanen
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

        // Siapkan precalculated max_panen untuk setiap laporan/bulan
        foreach ($laporans as $lap) {
            $resultMax = $this->laporanService->calculateMaxHarvestArea(
                (int) $lap->kecamatan_id,
                (int) $lap->komoditas_id,
                (int) $lap->bulan,
                (int) $lap->tahun,
                0,
                $lap->id
            );
            $lap->max_panen = $resultMax['max_panen'] ?? 0.00;
        }

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

        $resultMax = $this->laporanService->calculateMaxHarvestArea(
            (int) $kecamatanId,
            (int) $komoditasId,
            (int) $bulan,
            (int) $tahun,
            0, // incomingLuasTanam tidak berpengaruh untuk max_panen historis
            $laporan ? $laporan->id : null
        );
        $maxPanen = $resultMax['max_panen'] ?? 0.00;

        return view('produksi.tanaman-pangan.input_mingguan', [
            'kecamatan' => $kecamatan,
            'komoditas' => $komoditas,
            'kategori' => $this->kategori,
            'tahun' => $tahun,
            'bulan' => $bulan,
            'laporan' => $laporan,
            'satuans' => $satuans,
            'months' => $months,
            'mingguans' => $mingguans,
            'maxPanen' => $maxPanen
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
            'keterangan_selisih_panen' => ['nullable', 'string'],
            'mingguans' => ['required', 'array', 'size:4'],
            'mingguans.*.luas_tanam' => ['required', 'numeric', 'min:0'],
            'mingguans.*.luas_panen' => ['required', 'numeric', 'min:0'],
            'mingguans.*.produktivitas' => ['required', 'numeric', 'min:0'],
            'mingguans.*.produksi' => ['required', 'numeric', 'min:0'],
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

        if ($result['is_tanaman_pangan'] && $totalPanen > $result['max_panen'] && empty(trim($request->keterangan_selisih_panen ?? ''))) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'keterangan_selisih_panen' => "Keterangan alasan selisih wajib diisi karena total Luas Panen ({$totalPanen} Ha) melebihi Luas Tanam pada {$result['durasi']} bulan sebelumnya (Maksimal: " . number_format($result['max_panen'], 2) . " Ha)."
                ]);
        }

        $keteranganSelisih = $request->keterangan_selisih_panen;
        if ($result['is_tanaman_pangan'] && $totalPanen <= $result['max_panen']) {
            $keteranganSelisih = null;
        }

        DB::transaction(function() use ($request, $kecamatanId, $komoditasId, $tahun, $bulan, $komoditas, $totalTanam, $totalPanen, $keteranganSelisih) {
            $totalProduksi = 0;

            foreach ($request->mingguans as $m) {
                $totalProduksi += floatval($m['produksi'] ?? 0);
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
                    'keterangan_selisih_panen' => $keteranganSelisih,
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

        // Ambil data luas lahan baku pertahun untuk menggantikan akumulasi dinamis
        $lahanBakus = \App\Models\LuasLahanBaku::whereBetween('tahun', [$years[0], end($years)]);
        if ($kecamatanId) {
            $lahanBakus->where('kecamatan_id', $kecamatanId);
        }
        $lahanBakus = $lahanBakus->get();

        $lahanTahun = [];
        foreach ($years as $tahun) {
            $lahanTahun[] = doubleval($lahanBakus->where('tahun', $tahun)->sum('luas_lahan'));
        }

        // Untuk bulanan, luas lahan baku tetap sama di setiap bulan karena data pertahun
        $lahanBakuTahunTerpilih = doubleval($lahanBakus->where('tahun', $tahunBulanan)->sum('luas_lahan'));
        $lahanBulan = array_fill(0, 12, $lahanBakuTahunTerpilih);

        // Ambil target bulanan kabupaten
        $targetsQuery = \App\Models\TargetTanam::where('tahun', $tahunBulanan)->where('komoditas_id', $komoditasId);
        if ($kecamatanId) {
            $targetsQuery->where('kecamatan_id', $kecamatanId);
        }
        $targetsObj = $targetsQuery->get();

        $targetBulan = [];
        foreach ($months as $m) {
            $targetBulan[] = doubleval($targetsObj->where('bulan', $m)->sum('target'));
        }

        // Ambil target tahunan (5 tahun terakhir)
        $targetTahun = [];
        foreach ($years as $tahun) {
            $tQuery = \App\Models\TargetTanam::where('tahun', $tahun)->where('komoditas_id', $komoditasId);
            if ($kecamatanId) {
                $tQuery->where('kecamatan_id', $kecamatanId);
            }
            $targetTahun[] = doubleval($tQuery->sum('target'));
        }

        return response()->json([
            'years' => $years,
            'luas_tanam' => $tanamTahun,
            'luas_panen' => $panenTahun,
            'produksi' => $produksiTahun,
            'luas_lahan' => $lahanTahun,
            'target_tahunan' => $targetTahun,
            'months' => $monthNames,
            'bulanan_tanam' => $tanamBulan,
            'bulanan_panen' => $panenBulan,
            'bulanan_produksi' => $produksiBulan,
            'bulanan_lahan' => $lahanBulan,
            'bulanan_target' => $targetBulan
        ]);
    }

    /**
     * Simpan data luas lahan baku pertahun per kecamatan.
     */
    public function simpanLahanBaku(Request $request): RedirectResponse
    {
        $request->validate([
            'tahun' => ['required', 'integer'],
            'komoditas_id' => ['required', 'exists:komoditas,id'],
            'lahan' => ['required', 'array'],
            'lahan.*' => ['required', 'numeric', 'min:0'],
        ]);

        $tahun = $request->tahun;
        $komoditasId = $request->komoditas_id;

        DB::transaction(function() use ($request, $tahun, $komoditasId) {
            foreach ($request->lahan as $kecamatanId => $luasLahan) {
                \App\Models\LuasLahanBaku::updateOrCreate(
                    [
                        'kecamatan_id' => $kecamatanId,
                        'komoditas_id' => $komoditasId,
                        'tahun' => $tahun,
                    ],
                    [
                        'luas_lahan' => floatval($luasLahan),
                    ]
                );
            }
        });

        return redirect()->back()->with('success', 'Data Luas Lahan Baku berhasil diperbarui.');
    }

    /**
     * Simpan data target tanam bulanan per kecamatan.
     */
    public function simpanTargetTanam(Request $request): RedirectResponse
    {
        $request->validate([
            'tahun' => ['required', 'integer'],
            'komoditas_id' => ['required', 'exists:komoditas,id'],
            'target' => ['required', 'array'],
        ]);

        $tahun = $request->tahun;
        $komoditasId = $request->komoditas_id;

        DB::transaction(function() use ($request, $tahun, $komoditasId) {
            foreach ($request->target as $kecamatanId => $months) {
                if (is_array($months)) {
                    foreach ($months as $bulan => $targetVal) {
                        \App\Models\TargetTanam::updateOrCreate(
                            [
                                'kecamatan_id' => $kecamatanId,
                                'komoditas_id' => $komoditasId,
                                'tahun' => $tahun,
                                'bulan' => (int) $bulan,
                            ],
                            [
                                'target' => floatval($targetVal),
                            ]
                        );
                    }
                }
            }
        });

        return redirect()->back()->with('success', 'Data Target Tanam bulanan berhasil diperbarui.');
    }

    /**
     * Cetak Laporan Rekap LTT ke Print / PDF View
     */
    public function cetakRekapLtt(Request $request)
    {
        $request->validate([
            'tahun' => ['required', 'integer'],
            'komoditas_id' => ['required', 'exists:komoditas,id'],
        ]);

        $tahun = (int) $request->tahun;
        $komoditasId = (int) $request->komoditas_id;

        $komoditas = \App\Models\Komoditas::findOrFail($komoditasId);
        $kecamatanListObj = \App\Models\Kecamatan::all();

        // 1. Target Tanam
        $targetMap = [];
        for ($m = 1; $m <= 12; $m++) {
            foreach ($kecamatanListObj as $kec) {
                $targetObj = \App\Models\TargetTanam::where('kecamatan_id', $kec->id)
                    ->where('komoditas_id', $komoditasId)
                    ->where('tahun', $tahun)
                    ->where('bulan', $m)
                    ->first();
                $targetMap[$kec->id][$m] = $targetObj ? floatval($targetObj->target) : 0.00;
            }
        }

        // 2. Realisasi Luas Tanam & Luas Panen
        $tanamMap = [];
        $panenMap = [];
        for ($m = 1; $m <= 12; $m++) {
            foreach ($kecamatanListObj as $kec) {
                $realisasi = \App\Models\LaporanProduksi::where('kecamatan_id', $kec->id)
                    ->where('komoditas_id', $komoditasId)
                    ->where('tahun', $tahun)
                    ->where('bulan', $m)
                    ->first();
                $tanamMap[$kec->id][$m] = $realisasi ? floatval($realisasi->luas_tanam) : 0.00;
                $panenMap[$kec->id][$m] = $realisasi ? floatval($realisasi->luas_panen) : 0.00;
            }
        }

        $pdf = Pdf::loadView('produksi.tanaman-pangan.cetak_rekap_ltt', [
            'tahun' => $tahun,
            'komoditas' => $komoditas,
            'kecamatanListObj' => $kecamatanListObj,
            'targetMap' => $targetMap,
            'tanamMap' => $tanamMap,
            'panenMap' => $panenMap
        ]);

        $pdf->setPaper('a4', 'landscape');
        
        return $pdf->stream('Rekap_LTT_' . str_replace(' ', '_', $komoditas->nama) . '_' . $tahun . '.pdf');
    }
}
