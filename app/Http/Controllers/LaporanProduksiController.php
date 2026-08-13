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

class LaporanProduksiController extends Controller
{
    protected $laporanService;
    protected $kecamatanService;
    protected $komoditasService;
    protected $satuanService;
    protected $kategoriService;

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
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $kategoris = $this->kategoriService->getAllKategori();
        
        // Ambil kategori pertama secara default jika tidak ada filter kategori_id
        $activeKategoriId = $request->get('kategori_id', $kategoris->first()?->id);

        if ($request->ajax()) {
            $kecamatanId = $request->get('kecamatan_id');
            $kategoriId = intval($activeKategoriId);

            if (!$kecamatanId) {
                return DataTables::of([])->make(true);
            }

            // Ambil semua komoditas dalam kategori ini
            $komoditasList = \App\Models\Komoditas::where('kategori_komoditas_id', $kategoriId)->get();

            // Ambil seluruh laporan produksi di kecamatan ini untuk kategori ini
            $laporans = \App\Models\LaporanProduksi::where('kecamatan_id', $kecamatanId)
                ->where('kategori_komoditas_id', $kategoriId)
                ->whereBetween('tahun', [2022, 2026])
                ->get();

            $data = [];
            foreach ($komoditasList as $komoditas) {
                $row = [
                    'komoditas_id' => $komoditas->id,
                    'komoditas_nama' => $komoditas->nama,
                ];

                for ($tahun = 2022; $tahun <= 2026; $tahun++) {
                    $laporanTahun = $laporans->where('komoditas_id', $komoditas->id)->where('tahun', $tahun);
                    
                    $row['tanam_' . $tahun] = $laporanTahun->sum('luas_tanam');
                    $row['panen_' . $tahun] = $laporanTahun->sum('luas_panen');
                    $row['produksi_' . $tahun] = $laporanTahun->sum('produksi');
                    $row['produktivitas_' . $tahun] = $laporanTahun->avg('produktivitas') ?: 0;
                }

                $data[] = $row;
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row) {
                    $kelolaUrl = route('laporan-produksis.kelola', [
                        'komoditas_id' => $row['komoditas_id']
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
        $komoditas = $this->komoditasService->getAllKomoditas()
            ->where('kategori_komoditas_id', $activeKategoriId)
            ->pluck('nama', 'id')
            ->toArray();

        return view('produksi.index', compact('kategoris', 'activeKategoriId', 'kecamatans', 'komoditas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $kategoriId = $request->get('kategori_id');
        $kategori = KategoriKomoditas::find($kategoriId);
        if (!$kategori) {
            abort(404, 'Kategori Komoditas tidak ditemukan.');
        }

        $kecamatans = $this->kecamatanService->getAllKecamatan()->pluck('nama', 'id')->toArray();
        
        // Ambil komoditas sebagai model list untuk looping di form
        $komoditasList = $this->komoditasService->getAllKomoditas()
            ->where('kategori_komoditas_id', $kategoriId);

        $satuans = $this->satuanService->getAllSatuan()->pluck('nama', 'id')->toArray();
        $isTanamanPangan = strtolower($kategori->nama) === 'tanaman pangan';

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return view('produksi.create', compact('kategori', 'kecamatans', 'komoditasList', 'satuans', 'isTanamanPangan', 'months'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LaporanProduksiRequest $request): RedirectResponse
    {
        $this->laporanService->createLaporan($request->validated());

        return redirect()->route('laporan-produksis.index', ['kategori_id' => $request->kategori_komoditas_id])
            ->with('success', 'Laporan Produksi berhasil ditambahkan.');
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

        $kategori = $laporan->kategori;
        $kecamatans = $this->kecamatanService->getAllKecamatan()->pluck('nama', 'id')->toArray();
        
        $komoditas = $this->komoditasService->getAllKomoditas()
            ->where('kategori_komoditas_id', $kategori->id)
            ->pluck('nama', 'id')
            ->toArray();

        $satuans = $this->satuanService->getAllSatuan()->pluck('nama', 'id')->toArray();
        $isTanamanPangan = strtolower($kategori->nama) === 'tanaman pangan';

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        // Format rincian mingguan jika Tanaman Pangan
        $mingguans = [];
        if ($isTanamanPangan) {
            $mingguans = $laporan->mingguans->sortBy('minggu_ke')->values()->toArray();
        }

        return view('produksi.edit', compact('laporan', 'kategori', 'kecamatans', 'komoditas', 'satuans', 'isTanamanPangan', 'months', 'mingguans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LaporanProduksiRequest $request, int $id): RedirectResponse
    {
        $this->laporanService->updateLaporan($id, $request->validated());

        $laporan = $this->laporanService->getLaporanById($id);

        return redirect()->route('laporan-produksis.index', ['kategori_id' => $laporan->kategori_komoditas_id])
            ->with('success', 'Laporan Produksi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $laporan = $this->laporanService->getLaporanById($id);
        $kategoriId = $laporan ? $laporan->kategori_komoditas_id : null;

        $this->laporanService->deleteLaporan($id);

        return redirect()->route('laporan-produksis.index', ['kategori_id' => $kategoriId])
            ->with('success', 'Laporan Produksi berhasil dihapus.');
    }

    /**
     * Display monthly matrix of kecamatan for a selected commodity and year.
     */
    public function kelola(Request $request): View
    {
        $komoditasId = $request->get('komoditas_id');
        $komoditas = \App\Models\Komoditas::findOrFail($komoditasId);
        $kategori = $komoditas->kategori;

        $tahun = $request->get('tahun', date('Y'));
        $years = [2022, 2023, 2024, 2025, 2026];

        $kecamatans = $this->kecamatanService->getAllKecamatan();
        $laporans = \App\Models\LaporanProduksi::where('komoditas_id', $komoditasId)
            ->where('tahun', $tahun)
            ->get();

        return view('produksi.kelola', compact('komoditas', 'kategori', 'tahun', 'years', 'kecamatans', 'laporans'));
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
        $kategori = $komoditas->kategori;

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

        return view('produksi.input_mingguan', compact('kecamatan', 'komoditas', 'kategori', 'tahun', 'bulan', 'laporan', 'satuans', 'months', 'mingguans'));
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
                    'produksi' => floatval($m['produksi'] ?? 0),
                ]);
            }
        });

        return redirect()->route('laporan-produksis.kelola', [
            'komoditas_id' => $komoditasId,
            'tahun' => $tahun
        ])->with('success', 'Laporan Mingguan berhasil disimpan.');
    }
}
