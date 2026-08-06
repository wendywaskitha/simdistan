<?php

namespace App\Http\Controllers;

use App\Http\Requests\LaporanProduksiRequest;
use App\Services\LaporanProduksiService;
use App\Services\KecamatanService;
use App\Services\KomoditasService;
use App\Services\SatuanService;
use App\Services\KategoriKomoditasService;
use App\Models\KategoriKomoditas;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class HortikulturaController extends Controller
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
        
        $this->kategori = KategoriKomoditas::where('nama', 'LIKE', '%Hortikultura%')->first();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $kategoriIds = KategoriKomoditas::where('nama', 'LIKE', '%Hortikultura%')->pluck('id')->toArray();
        $activeKategoriId = $this->kategori ? $this->kategori->id : null;

        if ($request->ajax()) {
            $query = \App\Models\LaporanProduksi::with(['kategori', 'kecamatan', 'komoditas', 'satuan'])
                ->whereIn('kategori_komoditas_id', $kategoriIds);

            if ($request->filled('kecamatan_id')) {
                $query->where('kecamatan_id', $request->kecamatan_id);
            }

            if ($request->filled('komoditas_id')) {
                $query->where('komoditas_id', $request->komoditas_id);
            }

            $data = $query->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('kecamatan_nama', function($row) {
                    return $row->kecamatan->nama;
                })
                ->addColumn('komoditas_nama', function($row) {
                    return $row->komoditas->nama;
                })
                ->addColumn('bulan_nama', function($row) {
                    if ($row->jenis_periode === 'Triwulanan') {
                        $triwulans = [
                            1 => 'Triwulan I (Jan-Mar)',
                            2 => 'Triwulan II (Apr-Jun)',
                            3 => 'Triwulan III (Jul-Sep)',
                            4 => 'Triwulan IV (Okt-Des)'
                        ];
                        return $triwulans[$row->bulan] ?? '-';
                    }
                    $months = [
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                    ];
                    return $months[$row->bulan] ?? '-';
                })
                ->addColumn('form_badge', function($row) {
                    $badge = $row->form_type ?? '-';
                    $class = 'bg-secondary';
                    if ($badge === 'SPH-SBS') $class = 'bg-primary text-white';
                    elseif ($badge === 'SPH-BST') $class = 'bg-warning text-dark';
                    elseif ($badge === 'SPH-TBF') $class = 'bg-info text-white';
                    return '<span class="badge ' . $class . ' px-2 py-1 rounded-pill">' . $badge . '</span>';
                })
                ->addColumn('tanam_atau_pohon', function($row) {
                    if ($row->form_type === 'SPH-BST') {
                        return number_format($row->jumlah_tanaman_menghasilkan ?? 0, 0, ',', '.') . ' pohon';
                    } elseif ($row->form_type === 'SPH-TBF') {
                        return number_format($row->luas_tanam_akhir ?? 0, 2, ',', '.') . ' m²';
                    }
                    return number_format($row->luas_tanam_akhir ?? 0, 2, ',', '.') . ' Ha';
                })
                ->addColumn('panen_formatted', function($row) {
                    if ($row->form_type === 'SPH-TBF') {
                        return number_format($row->luas_panen ?? 0, 2, ',', '.') . ' m²';
                    }
                    return number_format($row->luas_panen ?? 0, 2, ',', '.') . ' Ha';
                })
                ->addColumn('produksi_formatted', function($row) {
                    if ($row->form_type === 'SPH-TBF') {
                        return number_format($row->produksi ?? 0, 2, ',', '.') . ' Kg';
                    }
                    return number_format($row->produksi ?? 0, 2, ',', '.') . ' Kw';
                })
                ->addColumn('satuan_nama', function($row) {
                    return $row->satuan->nama;
                })
                ->addColumn('action', function($row) {
                    $showUrl = route('hortikultura.show', $row->id);
                    $editUrl = route('hortikultura.edit', $row->id);
                    $deleteUrl = route('hortikultura.destroy', $row->id);
                    return '
                        <div class="d-flex gap-2">
                            <a href="'.$showUrl.'" class="btn btn-sm btn-light border" title="Detail"><i class="bi bi-eye"></i></a>
                            <a href="'.$editUrl.'" class="btn btn-sm btn-info text-white" title="Edit"><i class="bi bi-pencil-square"></i></a>
                            <form action="'.$deleteUrl.'" method="POST" class="d-inline form-delete">
                                '.csrf_field().'
                                '.method_field('DELETE').'
                                <button type="button" class="btn btn-sm btn-danger btn-delete-trigger" title="Hapus"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    ';
                })
                ->rawColumns(['action', 'form_badge'])
                ->make(true);
        }

        $kecamatans = $this->kecamatanService->getAllKecamatan()->pluck('nama', 'id')->toArray();
        $komoditas = $this->komoditasService->getAllKomoditas()
            ->whereIn('kategori_komoditas_id', $kategoriIds)
            ->pluck('nama', 'id')
            ->toArray();

        return view('produksi.hortikultura.index', [
            'activeKategoriId' => $activeKategoriId,
            'kategori' => $this->kategori,
            'kecamatans' => $kecamatans,
            'komoditas' => $komoditas
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $kategoriIds = KategoriKomoditas::where('nama', 'LIKE', '%Hortikultura%')->pluck('id')->toArray();
        $kecamatans  = $this->kecamatanService->getAllKecamatan()->pluck('nama', 'id')->toArray();
        $satuans     = $this->satuanService->getAllSatuan()->pluck('nama', 'id')->toArray();

        // Kelompokkan komoditas per form_type agar view bisa render tabel terpisah
        $komoditasList = $this->komoditasService->getAllKomoditas()
            ->whereIn('kategori_komoditas_id', $kategoriIds)
            ->groupBy('form_type');

        return view('produksi.hortikultura.create', [
            'kategori'      => $this->kategori,
            'kecamatans'    => $kecamatans,
            'komoditasList' => $komoditasList,
            'satuans'       => $satuans,
        ]);
    }

    /**
     * AJAX: Ambil data periode sebelumnya untuk auto-fill form (SBS/BST/TBF).
     */
    public function ajaxPrevData(Request $request): JsonResponse
    {
        $kecamatanId = (int) $request->input('kecamatan_id');
        $formType    = $request->input('form_type', 'SPH-SBS');
        $bulan       = (int) $request->input('bulan', 1);
        $tahun       = (int) $request->input('tahun', date('Y'));

        if (!$kecamatanId || !$bulan || !$tahun) {
            return response()->json([]);
        }

        $data = $this->laporanService->getPreviousPeriodData($kecamatanId, $formType, $bulan, $tahun);
        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LaporanProduksiRequest $request): RedirectResponse
    {
        $this->laporanService->createLaporan($request->validated());

        return redirect()->route('hortikultura.index')
            ->with('success', 'Laporan Produksi Hortikultura berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): View
    {
        $laporan = $this->laporanService->getLaporanById($id);
        if (!$laporan) {
            abort(404);
        }

        if ($laporan->jenis_periode === 'Triwulanan') {
            $periods = [
                1 => 'Triwulan I (Jan-Mar)',
                2 => 'Triwulan II (Apr-Jun)',
                3 => 'Triwulan III (Jul-Sep)',
                4 => 'Triwulan IV (Okt-Des)'
            ];
        } else {
            $periods = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
        }

        return view('produksi.hortikultura.show', [
            'laporan'  => $laporan,
            'kategori' => $this->kategori,
            'periods'  => $periods
        ]);
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

        $kategoriIds = KategoriKomoditas::where('nama', 'LIKE', '%Hortikultura%')->pluck('id')->toArray();
        $kecamatans = $this->kecamatanService->getAllKecamatan()->pluck('nama', 'id')->toArray();
        $komoditas = $this->komoditasService->getAllKomoditas()
            ->whereIn('kategori_komoditas_id', $kategoriIds)
            ->pluck('nama', 'id')
            ->toArray();

        $satuans = $this->satuanService->getAllSatuan()->pluck('nama', 'id')->toArray();

        if ($laporan->jenis_periode === 'Triwulanan') {
            $months = [
                1 => 'Triwulan I (Jan-Mar)',
                2 => 'Triwulan II (Apr-Jun)',
                3 => 'Triwulan III (Jul-Sep)',
                4 => 'Triwulan IV (Okt-Des)'
            ];
        } else {
            $months = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
        }

        return view('produksi.hortikultura.edit', [
            'laporan' => $laporan,
            'kategori' => $this->kategori,
            'kecamatans' => $kecamatans,
            'komoditas' => $komoditas,
            'satuans' => $satuans,
            'isTanamanPangan' => false,
            'months' => $months
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LaporanProduksiRequest $request, int $id): RedirectResponse
    {
        $this->laporanService->updateLaporan($id, $request->validated());

        return redirect()->route('hortikultura.index')
            ->with('success', 'Laporan Produksi Hortikultura berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->laporanService->deleteLaporan($id);

        return redirect()->route('hortikultura.index')
            ->with('success', 'Laporan Produksi Hortikultura berhasil dihapus.');
    }
}
