<?php

namespace App\Http\Controllers;

use App\Http\Requests\JenisAlatRequest;
use App\Services\JenisAlatService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class JenisAlatController extends Controller
{
    protected $jenisAlatService;

    public function __construct(JenisAlatService $jenisAlatService)
    {
        $this->jenisAlatService = $jenisAlatService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->jenisAlatService->getAllJenisAlat();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $editUrl = route('jenis-alats.edit', $row->id);
                    $deleteUrl = route('jenis-alats.destroy', $row->id);
                    return '
                        <div class="d-flex gap-2">
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

        return view('master.jenis-alat.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('master.jenis-alat.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(JenisAlatRequest $request): RedirectResponse
    {
        $this->jenisAlatService->createJenisAlat($request->validated());

        return redirect()->route('jenis-alats.index')
            ->with('success', 'Data Jenis Alat berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $jenis = $this->jenisAlatService->getJenisAlatById($id);
        if (!$jenis) {
            abort(404);
        }

        return view('master.jenis-alat.edit', compact('jenis'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(JenisAlatRequest $request, int $id): RedirectResponse
    {
        $this->jenisAlatService->updateJenisAlat($id, $request->validated());

        return redirect()->route('jenis-alats.index')
            ->with('success', 'Data Jenis Alat berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->jenisAlatService->deleteJenisAlat($id);

        return redirect()->route('jenis-alats.index')
            ->with('success', 'Data Jenis Alat berhasil dihapus.');
    }
}
