<?php

namespace App\Http\Controllers;

use App\Http\Requests\DesaRequest;
use App\Services\DesaService;
use App\Services\KecamatanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class DesaController extends Controller
{
    protected $desaService;
    protected $kecamatanService;

    public function __construct(DesaService $desaService, KecamatanService $kecamatanService)
    {
        $this->desaService = $desaService;
        $this->kecamatanService = $kecamatanService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->desaService->getAllDesa();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('kecamatan_nama', function($row) {
                    return $row->kecamatan->nama;
                })
                ->addColumn('action', function($row) {
                    $editUrl = route('desas.edit', $row->id);
                    $deleteUrl = route('desas.destroy', $row->id);
                    return '
                        <div class="d-flex gap-2">
                            <a href="'.$editUrl.'" class="btn btn-sm btn-info text-white"><i class="bi bi-pencil-square"></i></a>
                            <form action="'.$deleteUrl.'" method="POST" class="d-inline">
                                '.csrf_field().'
                                '.method_field('DELETE').'
                                <button type="submit" class="btn btn-sm btn-danger btn-delete-trigger"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('master.desa.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $kecamatans = $this->kecamatanService->getAllKecamatan()->pluck('nama', 'id')->toArray();
        return view('master.desa.create', compact('kecamatans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DesaRequest $request): RedirectResponse
    {
        $this->desaService->createDesa($request->validated());

        return redirect()->route('desas.index')
            ->with('success', 'Data Desa berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $desa = $this->desaService->getDesaById($id);
        if (!$desa) {
            abort(404);
        }

        $kecamatans = $this->kecamatanService->getAllKecamatan()->pluck('nama', 'id')->toArray();
        return view('master.desa.edit', compact('desa', 'kecamatans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DesaRequest $request, int $id): RedirectResponse
    {
        $this->desaService->updateDesa($id, $request->validated());

        return redirect()->route('desas.index')
            ->with('success', 'Data Desa berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->desaService->deleteDesa($id);

        return redirect()->route('desas.index')
            ->with('success', 'Data Desa berhasil dihapus.');
    }
}
