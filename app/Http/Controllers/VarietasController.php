<?php

namespace App\Http\Controllers;

use App\Http\Requests\VarietasRequest;
use App\Services\VarietasService;
use App\Services\KomoditasService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class VarietasController extends Controller
{
    protected $varietasService;
    protected $komoditasService;

    public function __construct(VarietasService $varietasService, KomoditasService $komoditasService)
    {
        $this->varietasService = $varietasService;
        $this->komoditasService = $komoditasService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->varietasService->getAllVarietas();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('komoditas_nama', function($row) {
                    return $row->komoditas ? $row->komoditas->nama : '-';
                })
                ->addColumn('kategori_nama', function($row) {
                    return ($row->komoditas && $row->komoditas->kategori) ? $row->komoditas->kategori->nama : '-';
                })
                ->addColumn('action', function($row) {
                    $editUrl = route('varietas.edit', $row->id);
                    $deleteUrl = route('varietas.destroy', $row->id);
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

        return view('master.varietas.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $komoditas = $this->komoditasService->getAllKomoditas()->pluck('nama', 'id')->toArray();
        return view('master.varietas.create', compact('komoditas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VarietasRequest $request): RedirectResponse
    {
        $this->varietasService->createVarietas($request->validated());

        return redirect()->route('varietas.index')
            ->with('success', 'Data Varietas berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $varietas = $this->varietasService->getVarietasById($id);
        if (!$varietas) {
            abort(404);
        }

        $komoditas = $this->komoditasService->getAllKomoditas()->pluck('nama', 'id')->toArray();
        return view('master.varietas.edit', compact('varietas', 'komoditas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VarietasRequest $request, int $id): RedirectResponse
    {
        $this->varietasService->updateVarietas($id, $request->validated());

        return redirect()->route('varietas.index')
            ->with('success', 'Data Varietas berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->varietasService->deleteVarietas($id);

        return redirect()->route('varietas.index')
            ->with('success', 'Data Varietas berhasil dihapus.');
    }
}
