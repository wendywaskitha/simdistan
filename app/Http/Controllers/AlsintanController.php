<?php

namespace App\Http\Controllers;

use App\Http\Requests\AlsintanRequest;
use App\Http\Requests\LaporanPemanfaatanRequest;
use App\Http\Requests\RealokasiAlsintanRequest;
use App\Services\AlsintanService;
use App\Services\KelompokTaniService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

use App\Services\JenisAlatService;

class AlsintanController extends Controller
{
    protected $alsintanService;
    protected $kelompokTaniService;
    protected $jenisAlatService;

    public function __construct(
        AlsintanService $alsintanService, 
        KelompokTaniService $kelompokTaniService,
        JenisAlatService $jenisAlatService
    ) {
        $this->alsintanService = $alsintanService;
        $this->kelompokTaniService = $kelompokTaniService;
        $this->jenisAlatService = $jenisAlatService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->alsintanService->getAllAlsintan();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('kelompok_tani_nama', function ($row) {
                    return $row->kelompokTani ? $row->kelompokTani->nama : '-';
                })
                ->addColumn('jenis_alat_nama', function ($row) {
                    return $row->jenisAlat ? $row->jenisAlat->nama : '-';
                })
                ->addColumn('action', function ($row) {
                    $showUrl = route('alsintans.show', $row->id);
                    $editUrl = route('alsintans.edit', $row->id);
                    $deleteUrl = route('alsintans.destroy', $row->id);
                    return '
                        <div class="d-flex gap-2">
                            <a href="' . $showUrl . '" class="btn btn-sm btn-primary"><i class="bi bi-eye"></i> Detail</a>
                            <a href="' . $editUrl . '" class="btn btn-sm btn-info text-white"><i class="bi bi-pencil-square"></i></a>
                            <form action="' . $deleteUrl . '" method="POST" class="d-inline">
                                ' . csrf_field() . '
                                ' . method_field('DELETE') . '
                                <button type="submit" class="btn btn-sm btn-danger btn-delete-trigger"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('psp.alsintan.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $kelompokTanis = $this->kelompokTaniService->getAllKelompokTani();
        $jenisAlats = $this->jenisAlatService->getAllJenisAlat();
        return view('psp.alsintan.create', compact('kelompokTanis', 'jenisAlats'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AlsintanRequest $request): RedirectResponse
    {
        // Get the chairman name from the selected kelompok tani if not manually input
        $data = $request->validated();
        if (empty($data['nama_ketua'])) {
            $kelompokTani = $this->kelompokTaniService->getKelompokTaniById($data['kelompok_tani_id']);
            $data['nama_ketua'] = $kelompokTani ? $kelompokTani->ketua : null;
        }

        $this->alsintanService->createAlsintan($data);

        return redirect()->route('alsintans.index')
            ->with('success', 'Data Penerima Bantuan Alsintan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): View
    {
        $alsintan = $this->alsintanService->getAlsintanById($id);
        if (!$alsintan) {
            abort(404);
        }

        return view('psp.alsintan.show', compact('alsintan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $alsintan = $this->alsintanService->getAlsintanById($id);
        if (!$alsintan) {
            abort(404);
        }

        $kelompokTanis = $this->kelompokTaniService->getAllKelompokTani();
        $jenisAlats = $this->jenisAlatService->getAllJenisAlat();
        return view('psp.alsintan.edit', compact('alsintan', 'kelompokTanis', 'jenisAlats'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AlsintanRequest $request, int $id): RedirectResponse
    {
        $data = $request->validated();
        if (empty($data['nama_ketua'])) {
            $kelompokTani = $this->kelompokTaniService->getKelompokTaniById($data['kelompok_tani_id']);
            $data['nama_ketua'] = $kelompokTani ? $kelompokTani->ketua : null;
        }

        $this->alsintanService->updateAlsintan($id, $data);

        return redirect()->route('alsintans.index')
            ->with('success', 'Data Penerima Bantuan Alsintan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->alsintanService->deleteAlsintan($id);

        return redirect()->route('alsintans.index')
            ->with('success', 'Data Penerima Bantuan Alsintan berhasil dihapus.');
    }

    /**
     * Store a newly created utilization report in storage.
     */
    public function storeLaporan(LaporanPemanfaatanRequest $request, int $id): RedirectResponse
    {
        $this->alsintanService->tambahLaporanPemanfaatan($id, $request->validated());

        return redirect()->route('alsintans.show', $id)
            ->with('success', 'Laporan Pemanfaatan Alsintan berhasil ditambahkan.');
    }

    /**
     * Show the form for reallocating the specified resource.
     */
    public function realokasiForm(int $id): View
    {
        $alsintan = $this->alsintanService->getAlsintanById($id);
        if (!$alsintan) {
            abort(404);
        }

        $kelompokTanis = $this->kelompokTaniService->getAllKelompokTani();
        return view('psp.alsintan.realokasi', compact('alsintan', 'kelompokTanis'));
    }

    /**
     * Store a newly created reallocation in storage.
     */
    public function realokasiStore(RealokasiAlsintanRequest $request, int $id): RedirectResponse
    {
        $data = $request->validated();
        
        // Fetch new kelompok tani chairman if empty
        if (empty($data['nama_ketua_tujuan'])) {
            $kelompokTaniTujuan = $this->kelompokTaniService->getKelompokTaniById($data['kelompok_tani_tujuan_id']);
            $data['nama_ketua_tujuan'] = $kelompokTaniTujuan ? $kelompokTaniTujuan->ketua : null;
        }

        $success = $this->alsintanService->realokasiAlsintan($id, $data);

        if (!$success) {
            return redirect()->back()
                ->with('error', 'Gagal memproses realokasi alsintan.');
        }

        return redirect()->route('alsintans.show', $id)
            ->with('success', 'Alsintan berhasil direalokasikan ke kelompok tani tujuan.');
    }
}
