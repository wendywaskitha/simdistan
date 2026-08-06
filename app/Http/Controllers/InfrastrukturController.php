<?php

namespace App\Http\Controllers;

use App\Http\Requests\InfrastrukturRequest;
use App\Http\Requests\InfrastrukturLaporanRequest;
use App\Services\InfrastrukturService;
use App\Services\KecamatanService;
use App\Services\DesaService;
use App\Services\KelompokTaniService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class InfrastrukturController extends Controller
{
    protected $infrastrukturService;
    protected $kecamatanService;
    protected $desaService;
    protected $kelompokTaniService;

    public function __construct(
        InfrastrukturService $infrastrukturService,
        KecamatanService $kecamatanService,
        DesaService $desaService,
        KelompokTaniService $kelompokTaniService
    ) {
        $this->infrastrukturService = $infrastrukturService;
        $this->kecamatanService = $kecamatanService;
        $this->desaService = $desaService;
        $this->kelompokTaniService = $kelompokTaniService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = \App\Models\Infrastruktur::with(['kecamatan', 'desa', 'kelompokTani']);

            if ($request->filled('kecamatan_id')) {
                $query->where('kecamatan_id', $request->kecamatan_id);
            }

            if ($request->filled('desa_id')) {
                $query->where('desa_id', $request->desa_id);
            }

            if ($request->filled('jenis_infrastruktur')) {
                $query->where('jenis_infrastruktur', $request->jenis_infrastruktur);
            }

            $data = $query->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('kecamatan_nama', function ($row) {
                    return $row->kecamatan ? $row->kecamatan->nama : '-';
                })
                ->addColumn('desa_nama', function ($row) {
                    return $row->desa ? $row->desa->nama : '-';
                })
                ->addColumn('kelompok_tani_nama', function ($row) {
                    return $row->kelompokTani ? $row->kelompokTani->nama : 'Umum (Non-Kelompok)';
                })
                ->addColumn('nilai_anggaran_format', function ($row) {
                    return 'Rp ' . number_format($row->nilai_anggaran, 0, ',', '.');
                })
                ->addColumn('volume_format', function ($row) {
                    return number_format($row->volume, 0, ',', '.') . ' ' . $row->satuan;
                })
                ->addColumn('action', function ($row) {
                    $showUrl = route('infrastrukturs.show', $row->id);
                    $editUrl = route('infrastrukturs.edit', $row->id);
                    $deleteUrl = route('infrastrukturs.destroy', $row->id);
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

        $kecamatans = $this->kecamatanService->getAllKecamatan();
        $jenisOptions = [
            'Jaringan Irigasi Tersier' => 'Jaringan Irigasi Tersier',
            'Embung' => 'Embung',
            'Jalan Usaha Tani' => 'Jalan Usaha Tani',
            'Sumur Bor' => 'Sumur Bor',
            'Dam Parit' => 'Dam Parit',
            'Long Storage' => 'Long Storage',
            'Pompa Air' => 'Pompa Air'
        ];

        return view('psp.infrastruktur.index', compact('kecamatans', 'jenisOptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $kecamatans = $this->kecamatanService->getAllKecamatan();
        $kelompokTanis = $this->kelompokTaniService->getAllKelompokTani();

        $jenisOptions = [
            'Jaringan Irigasi Tersier' => 'Jaringan Irigasi Tersier',
            'Embung' => 'Embung',
            'Jalan Usaha Tani' => 'Jalan Usaha Tani',
            'Sumur Bor' => 'Sumur Bor',
            'Dam Parit' => 'Dam Parit',
            'Long Storage' => 'Long Storage',
            'Pompa Air' => 'Pompa Air'
        ];

        $sumberDanaOptions = [
            'APBD' => 'APBD Kabupaten/Kota',
            'APBD Provinsi' => 'APBD Provinsi',
            'APBN' => 'APBN Pusat',
            'DAK' => 'DAK (Dana Alokasi Khusus)',
            'DAK Penugasan' => 'DAK Penugasan',
            'BANPER' => 'Bantuan Pemerintah',
            'MANDIRI' => 'Swadaya Mandiri'
        ];

        $statusOptions = [
            'Rencana' => 'Perencanaan / Rencana',
            'Konstruksi' => 'Dalam Pembangunan / Konstruksi',
            'Selesai' => 'Selesai Pembangunan',
            'Rusak' => 'Mengalami Kerusakan'
        ];

        return view('psp.infrastruktur.create', compact('kecamatans', 'kelompokTanis', 'jenisOptions', 'sumberDanaOptions', 'statusOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InfrastrukturRequest $request): RedirectResponse
    {
        $this->infrastrukturService->createInfrastruktur($request->validated(), $request->file('kml_file'));

        return redirect()->route('infrastrukturs.index')
            ->with('success', 'Data Infrastruktur & Irigasi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): View
    {
        $infrastruktur = $this->infrastrukturService->getInfrastrukturById($id);
        if (!$infrastruktur) {
            abort(404);
        }

        return view('psp.infrastruktur.show', compact('infrastruktur'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $infrastruktur = $this->infrastrukturService->getInfrastrukturById($id);
        if (!$infrastruktur) {
            abort(404);
        }

        $kecamatans = $this->kecamatanService->getAllKecamatan();
        $kelompokTanis = $this->kelompokTaniService->getAllKelompokTani();

        // Get Desas in current Kecamatan
        $desas = \App\Models\Desa::where('kecamatan_id', $infrastruktur->kecamatan_id)->get();

        $jenisOptions = [
            'Jaringan Irigasi Tersier' => 'Jaringan Irigasi Tersier',
            'Embung' => 'Embung',
            'Jalan Usaha Tani' => 'Jalan Usaha Tani',
            'Sumur Bor' => 'Sumur Bor',
            'Dam Parit' => 'Dam Parit',
            'Long Storage' => 'Long Storage',
            'Pompa Air' => 'Pompa Air'
        ];

        $sumberDanaOptions = [
            'APBD' => 'APBD Kabupaten/Kota',
            'APBD Provinsi' => 'APBD Provinsi',
            'APBN' => 'APBN Pusat',
            'DAK' => 'DAK (Dana Alokasi Khusus)',
            'DAK Penugasan' => 'DAK Penugasan',
            'BANPER' => 'Bantuan Pemerintah',
            'MANDIRI' => 'Swadaya Mandiri'
        ];

        $statusOptions = [
            'Rencana' => 'Perencanaan / Rencana',
            'Konstruksi' => 'Dalam Pembangunan / Konstruksi',
            'Selesai' => 'Selesai Pembangunan',
            'Rusak' => 'Mengalami Kerusakan'
        ];

        return view('psp.infrastruktur.edit', compact('infrastruktur', 'kecamatans', 'desas', 'kelompokTanis', 'jenisOptions', 'sumberDanaOptions', 'statusOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(InfrastrukturRequest $request, int $id): RedirectResponse
    {
        $this->infrastrukturService->updateInfrastruktur($id, $request->validated(), $request->file('kml_file'));

        return redirect()->route('infrastrukturs.index')
            ->with('success', 'Data Infrastruktur & Irigasi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->infrastrukturService->deleteInfrastruktur($id);

        return redirect()->route('infrastrukturs.index')
            ->with('success', 'Data Infrastruktur & Irigasi berhasil dihapus.');
    }

    /**
     * Add a condition report via AJAX/Modal.
     */
    public function storeLaporan(InfrastrukturLaporanRequest $request, int $id): RedirectResponse
    {
        $this->infrastrukturService->tambahLaporanKondisi($id, $request->validated());

        return redirect()->route('infrastrukturs.show', $id)
            ->with('success', 'Laporan Kondisi & Pemeliharaan berhasil disimpan.');
    }

    /**
     * Helper AJAX endpoint to get desas by kecamatan.
     */
    public function getDesasByKecamatan(?int $kecamatanId = null): JsonResponse
    {
        if (!$kecamatanId) {
            return response()->json([]);
        }
        $desas = \App\Models\Desa::where('kecamatan_id', $kecamatanId)->get(['id', 'nama']);
        return response()->json($desas);
    }

    /**
     * Helper AJAX endpoint to get all infrastructure projects with coordinates.
     */
    public function getMapLocations(Request $request): JsonResponse
    {
        $query = \App\Models\Infrastruktur::with(['kecamatan', 'desa', 'kelompokTani'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');

        // Apply same filters if requested
        if ($request->filled('kecamatan_id')) {
            $query->where('kecamatan_id', $request->kecamatan_id);
        }
        if ($request->filled('desa_id')) {
            $query->where('desa_id', $request->desa_id);
        }
        if ($request->filled('jenis_infrastruktur')) {
            $query->where('jenis_infrastruktur', $request->jenis_infrastruktur);
        }

        $locations = $query->get();
        return response()->json($locations);
    }
}
