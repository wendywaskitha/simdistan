<?php

namespace App\Http\Controllers;

use App\Http\Requests\PetaniRequest;
use App\Services\PetaniService;
use App\Services\KelompokTaniService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class PetaniController extends Controller
{
    protected $petaniService;
    protected $kelompokTaniService;

    public function __construct(PetaniService $petaniService, KelompokTaniService $kelompokTaniService)
    {
        $this->petaniService = $petaniService;
        $this->kelompokTaniService = $kelompokTaniService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->petaniService->getAllPetani();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('kelompok_tani_nama', function($row) {
                    return $row->kelompokTani->nama;
                })
                ->addColumn('desa_nama', function($row) {
                    return $row->kelompokTani->desa->nama;
                })
                ->editColumn('luas_lahan', function($row) {
                    return number_format($row->luas_lahan, 2) . ' Ha';
                })
                ->addColumn('ktp_status', function($row) {
                    return $row->ktp 
                        ? '<a href="'.asset('storage/'.$row->ktp).'" target="_blank" class="text-success"><i class="bi bi-patch-check-fill fs-5"></i></a>' 
                        : '<span class="text-danger">—</span>';
                })
                ->addColumn('action', function($row) {
                    $editUrl = route('petanis.edit', $row->id);
                    $deleteUrl = route('petanis.destroy', $row->id);
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
                ->rawColumns(['ktp_status', 'action'])
                ->make(true);
        }

        return view('master.petani.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $kelompokTanis = $this->kelompokTaniService->getAllKelompokTani()->pluck('nama', 'id')->toArray();
        return view('master.petani.create', compact('kelompokTanis'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PetaniRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('ktp')) {
            $data['ktp'] = $request->file('ktp')->store('ktp', 'public');
        }
        $this->petaniService->createPetani($data);

        return redirect()->route('petanis.index')
            ->with('success', 'Data Petani berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $petani = $this->petaniService->getPetaniById($id);
        if (!$petani) {
            abort(404);
        }

        $kelompokTanis = $this->kelompokTaniService->getAllKelompokTani()->pluck('nama', 'id')->toArray();
        return view('master.petani.edit', compact('petani', 'kelompokTanis'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PetaniRequest $request, int $id): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('ktp')) {
            // Delete old file if exists
            $petani = $this->petaniService->getPetaniById($id);
            if ($petani && $petani->ktp) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($petani->ktp);
            }
            $data['ktp'] = $request->file('ktp')->store('ktp', 'public');
        }
        $this->petaniService->updatePetani($id, $data);

        return redirect()->route('petanis.index')
            ->with('success', 'Data Petani berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->petaniService->deletePetani($id);

        return redirect()->route('petanis.index')
            ->with('success', 'Data Petani berhasil dihapus.');
    }
}
