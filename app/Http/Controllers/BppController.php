<?php

namespace App\Http\Controllers;

use App\Http\Requests\BppRequest;
use App\Services\BppService;
use App\Services\KecamatanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class BppController extends Controller
{
    protected $bppService;
    protected $kecamatanService;

    public function __construct(BppService $bppService, KecamatanService $kecamatanService)
    {
        $this->bppService = $bppService;
        $this->kecamatanService = $kecamatanService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->bppService->getAllBpp();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('kecamatan_nama', function($row) {
                    return $row->kecamatan->nama;
                })
                ->addColumn('action', function($row) {
                    $editUrl = route('bpps.edit', $row->id);
                    $deleteUrl = route('bpps.destroy', $row->id);
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

        return view('master.bpp.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $kecamatans = $this->kecamatanService->getAllKecamatan()->pluck('nama', 'id')->toArray();
        return view('master.bpp.create', compact('kecamatans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BppRequest $request): RedirectResponse
    {
        $this->bppService->createBpp($request->validated());

        return redirect()->route('bpps.index')
            ->with('success', 'Data BPP berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $bpp = $this->bppService->getBppById($id);
        if (!$bpp) {
            abort(404);
        }

        $kecamatans = $this->kecamatanService->getAllKecamatan()->pluck('nama', 'id')->toArray();
        return view('master.bpp.edit', compact('bpp', 'kecamatans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BppRequest $request, int $id): RedirectResponse
    {
        $this->bppService->updateBpp($id, $request->validated());

        return redirect()->route('bpps.index')
            ->with('success', 'Data BPP berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->bppService->deleteBpp($id);

        return redirect()->route('bpps.index')
            ->with('success', 'Data BPP berhasil dihapus.');
    }
}
