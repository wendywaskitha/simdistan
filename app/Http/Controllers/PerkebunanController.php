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
use Yajra\DataTables\Facades\DataTables;

class PerkebunanController extends Controller
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
        
        $this->kategori = KategoriKomoditas::where('nama', 'Perkebunan')->first();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $activeKategoriId = $this->kategori->id;

        if ($request->ajax()) {
            $query = \App\Models\LaporanProduksi::with(['kategori', 'kecamatan', 'komoditas', 'satuan'])
                ->where('kategori_komoditas_id', $activeKategoriId);

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
                    $semesters = [
                        1 => 'Semester I (Jan-Jun)',
                        2 => 'Semester II (Jul-Des)'
                    ];
                    return $semesters[$row->bulan] ?? '-';
                })
                ->addColumn('satuan_nama', function($row) {
                    return $row->satuan->nama;
                })
                ->addColumn('action', function($row) {
                    $showUrl = route('perkebunan.show', $row->id);
                    $editUrl = route('perkebunan.edit', $row->id);
                    $deleteUrl = route('perkebunan.destroy', $row->id);
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
                ->rawColumns(['action'])
                ->make(true);
        }

        $kecamatans = $this->kecamatanService->getAllKecamatan()->pluck('nama', 'id')->toArray();
        $komoditas = $this->komoditasService->getAllKomoditas()
            ->where('kategori_komoditas_id', $activeKategoriId)
            ->pluck('nama', 'id')
            ->toArray();

        return view('produksi.perkebunan.index', [
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
        $kecamatans = $this->kecamatanService->getAllKecamatan()->pluck('nama', 'id')->toArray();
        $komoditasList = $this->komoditasService->getAllKomoditas()
            ->where('kategori_komoditas_id', $this->kategori->id);

        $satuans = $this->satuanService->getAllSatuan()->pluck('nama', 'id')->toArray();

        $semesters = [
            1 => 'Semester I (Jan-Jun)',
            2 => 'Semester II (Jul-Des)'
        ];

        return view('produksi.perkebunan.create', [
            'kategori' => $this->kategori,
            'kecamatans' => $kecamatans,
            'komoditasList' => $komoditasList,
            'satuans' => $satuans,
            'isTanamanPangan' => false,
            'months' => $semesters
        ]);
    }

    /**
     * AJAX: Ambil data periode sebelumnya untuk auto-fill form Perkebunan.
     */
    public function ajaxPrevData(Request $request): \Illuminate\Http\JsonResponse
    {
        $kecamatanId = (int) $request->input('kecamatan_id');
        $bulan       = (int) $request->input('bulan', 1);
        $tahun       = (int) $request->input('tahun', date('Y'));

        if (!$kecamatanId || !$bulan || !$tahun) {
            return response()->json([]);
        }

        $data = $this->laporanService->getPreviousPeriodData($kecamatanId, 'Perkebunan', $bulan, $tahun);
        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LaporanProduksiRequest $request): RedirectResponse
    {
        $this->laporanService->createLaporan($request->validated());

        return redirect()->route('perkebunan.index')
            ->with('success', 'Laporan Produksi Perkebunan berhasil ditambahkan.');
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

        $semesters = [
            1 => 'Semester I (Jan-Jun)',
            2 => 'Semester II (Jul-Des)'
        ];

        return view('produksi.perkebunan.show', [
            'laporan'   => $laporan,
            'kategori'  => $this->kategori,
            'semesters' => $semesters
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

        $kecamatans = $this->kecamatanService->getAllKecamatan()->pluck('nama', 'id')->toArray();
        $komoditas = $this->komoditasService->getAllKomoditas()
            ->where('kategori_komoditas_id', $this->kategori->id)
            ->pluck('nama', 'id')
            ->toArray();

        $satuans = $this->satuanService->getAllSatuan()->pluck('nama', 'id')->toArray();

        $semesters = [
            1 => 'Semester I (Jan-Jun)',
            2 => 'Semester II (Jul-Des)'
        ];

        return view('produksi.perkebunan.edit', [
            'laporan' => $laporan,
            'kategori' => $this->kategori,
            'kecamatans' => $kecamatans,
            'komoditas' => $komoditas,
            'satuans' => $satuans,
            'isTanamanPangan' => false,
            'months' => $semesters
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LaporanProduksiRequest $request, int $id): RedirectResponse
    {
        $this->laporanService->updateLaporan($id, $request->validated());

        return redirect()->route('perkebunan.index')
            ->with('success', 'Laporan Produksi Perkebunan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->laporanService->deleteLaporan($id);

        return redirect()->route('perkebunan.index')
            ->with('success', 'Laporan Produksi Perkebunan berhasil dihapus.');
    }
}
