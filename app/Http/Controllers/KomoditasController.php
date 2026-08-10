<?php

namespace App\Http\Controllers;

use App\Http\Requests\KomoditasRequest;
use App\Services\KomoditasService;
use App\Services\KategoriKomoditasService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class KomoditasController extends Controller
{
    protected $komoditasService;
    protected $kategoriService;

    public function __construct(KomoditasService $komoditasService, KategoriKomoditasService $kategoriService)
    {
        $this->komoditasService = $komoditasService;
        $this->kategoriService = $kategoriService;
    }

    private function getAllowedCategoryIds()
    {
        $user = auth()->user();
        if ($user->hasRole('Super Admin')) {
            return null; // All
        }
        if ($user->hasRole('Tanaman Pangan')) {
            return \App\Models\KategoriKomoditas::where('nama', 'Tanaman Pangan')->pluck('id')->toArray();
        }
        if ($user->hasRole('Hortikultura')) {
            return \App\Models\KategoriKomoditas::where('nama', 'LIKE', '%Hortikultura%')->pluck('id')->toArray();
        }
        if ($user->hasRole('Perkebunan')) {
            return \App\Models\KategoriKomoditas::where('nama', 'Perkebunan')->pluck('id')->toArray();
        }
        return []; // No access
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $allowedCategoryIds = $this->getAllowedCategoryIds();

        if ($request->ajax()) {
            $query = \App\Models\Komoditas::with('kategori');
            
            if ($allowedCategoryIds !== null) {
                $query->whereIn('kategori_komoditas_id', $allowedCategoryIds);
            }
            
            $kategoriId = $request->get('kategori_id');
            if ($kategoriId) {
                if ($allowedCategoryIds === null || in_array($kategoriId, $allowedCategoryIds)) {
                    $query->where('kategori_komoditas_id', $kategoriId);
                } else {
                    $query->whereRaw('1 = 0');
                }
            }

            $data = $query->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('kategori_nama', function($row) {
                    return $row->kategori->nama;
                })
                ->addColumn('durasi_formatted', function($row) {
                    return ($row->durasi_panen_bulan ?? 4) . ' Bulan';
                })
                ->addColumn('action', function($row) {
                    $editUrl = route('komoditas.edit', $row->id);
                    $deleteUrl = route('komoditas.destroy', $row->id);
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

        if ($allowedCategoryIds !== null) {
            $kategoris = \App\Models\KategoriKomoditas::whereIn('id', $allowedCategoryIds)->get();
        } else {
            $kategoris = $this->kategoriService->getAllKategori();
        }
        return view('master.komoditas.index', compact('kategoris'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $allowedCategoryIds = $this->getAllowedCategoryIds();
        
        if ($allowedCategoryIds !== null) {
            $kategoris = \App\Models\KategoriKomoditas::whereIn('id', $allowedCategoryIds)->pluck('nama', 'id')->toArray();
        } else {
            $kategoris = $this->kategoriService->getAllKategori()->pluck('nama', 'id')->toArray();
        }
        return view('master.komoditas.create', compact('kategoris'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(KomoditasRequest $request): RedirectResponse
    {
        $this->komoditasService->createKomoditas($request->validated());

        return redirect()->route('komoditas.index')
            ->with('success', 'Data Komoditas berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $komoditas = $this->komoditasService->getKomoditasById($id);
        if (!$komoditas) {
            abort(404);
        }

        $allowedCategoryIds = $this->getAllowedCategoryIds();
        if ($allowedCategoryIds !== null && !in_array($komoditas->kategori_komoditas_id, $allowedCategoryIds)) {
            abort(403, 'Unauthorized action.');
        }

        if ($allowedCategoryIds !== null) {
            $kategoris = \App\Models\KategoriKomoditas::whereIn('id', $allowedCategoryIds)->pluck('nama', 'id')->toArray();
        } else {
            $kategoris = $this->kategoriService->getAllKategori()->pluck('nama', 'id')->toArray();
        }
        return view('master.komoditas.edit', compact('komoditas', 'kategoris'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(KomoditasRequest $request, int $id): RedirectResponse
    {
        $komoditas = $this->komoditasService->getKomoditasById($id);
        if (!$komoditas) {
            abort(404);
        }

        $allowedCategoryIds = $this->getAllowedCategoryIds();
        if ($allowedCategoryIds !== null && !in_array($komoditas->kategori_komoditas_id, $allowedCategoryIds)) {
            abort(403, 'Unauthorized action.');
        }

        $this->komoditasService->updateKomoditas($id, $request->validated());

        return redirect()->route('komoditas.index')
            ->with('success', 'Data Komoditas berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $komoditas = $this->komoditasService->getKomoditasById($id);
        if (!$komoditas) {
            abort(404);
        }

        $allowedCategoryIds = $this->getAllowedCategoryIds();
        if ($allowedCategoryIds !== null && !in_array($komoditas->kategori_komoditas_id, $allowedCategoryIds)) {
            abort(403, 'Unauthorized action.');
        }

        $this->komoditasService->deleteKomoditas($id);

        return redirect()->route('komoditas.index')
            ->with('success', 'Data Komoditas berhasil dihapus.');
    }
}
