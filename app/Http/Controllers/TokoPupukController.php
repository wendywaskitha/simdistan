<?php

namespace App\Http\Controllers;

use App\Http\Requests\TokoPupukRequest;
use App\Services\TokoPupukService;
use App\Services\KecamatanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class TokoPupukController extends Controller
{
    protected $tokoService;
    protected $kecamatanService;

    public function __construct(TokoPupukService $tokoService, KecamatanService $kecamatanService)
    {
        $this->tokoService = $tokoService;
        $this->kecamatanService = $kecamatanService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->tokoService->getAllToko();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('kecamatan_list', function($row) {
                    return $row->kecamatans->pluck('nama')->implode(', ');
                })
                ->addColumn('action', function($row) {
                    $editUrl = route('toko-pupuks.edit', $row->id);
                    $deleteUrl = route('toko-pupuks.destroy', $row->id);
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

        return view('master.toko-pupuk.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $kecamatans = $this->kecamatanService->getAllKecamatan()->pluck('nama', 'id')->toArray();
        return view('master.toko-pupuk.create', compact('kecamatans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TokoPupukRequest $request): RedirectResponse
    {
        $this->tokoService->createToko($request->validated());

        return redirect()->route('toko-pupuks.index')
            ->with('success', 'Data Toko Pupuk berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $toko = $this->tokoService->getTokoById($id);
        if (!$toko) {
            abort(404);
        }
        $kecamatans = $this->kecamatanService->getAllKecamatan()->pluck('nama', 'id')->toArray();
        $selectedKecamatans = $toko->kecamatans->pluck('id')->toArray();

        return view('master.toko-pupuk.edit', compact('toko', 'kecamatans', 'selectedKecamatans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TokoPupukRequest $request, int $id): RedirectResponse
    {
        $this->tokoService->updateToko($id, $request->validated());

        return redirect()->route('toko-pupuks.index')
            ->with('success', 'Data Toko Pupuk berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->tokoService->deleteToko($id);

        return redirect()->route('toko-pupuks.index')
            ->with('success', 'Data Toko Pupuk berhasil dihapus.');
    }
}
