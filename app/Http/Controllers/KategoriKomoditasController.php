<?php

namespace App\Http\Controllers;

use App\Http\Requests\KategoriKomoditasRequest;
use App\Services\KategoriKomoditasService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class KategoriKomoditasController extends Controller
{
    protected $kategoriService;

    public function __construct(KategoriKomoditasService $kategoriService)
    {
        $this->kategoriService = $kategoriService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->kategoriService->getAllKategori();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row) {
                    $editUrl = route('kategori-komoditas.edit', $row->id);
                    $deleteUrl = route('kategori-komoditas.destroy', $row->id);
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

        return view('master.kategori-komoditas.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('master.kategori-komoditas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(KategoriKomoditasRequest $request): RedirectResponse
    {
        $this->kategoriService->createKategori($request->validated());

        return redirect()->route('kategori-komoditas.index')
            ->with('success', 'Kategori Komoditas berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $kategori = $this->kategoriService->getKategoriById($id);
        if (!$kategori) {
            abort(404);
        }

        return view('master.kategori-komoditas.edit', compact('kategori'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(KategoriKomoditasRequest $request, int $id): RedirectResponse
    {
        $this->kategoriService->updateKategori($id, $request->validated());

        return redirect()->route('kategori-komoditas.index')
            ->with('success', 'Kategori Komoditas berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->kategoriService->deleteKategori($id);

        return redirect()->route('kategori-komoditas.index')
            ->with('success', 'Kategori Komoditas berhasil dihapus.');
    }
}
