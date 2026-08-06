<?php

namespace App\Http\Controllers;

use App\Http\Requests\KecamatanRequest;
use App\Services\KecamatanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class KecamatanController extends Controller
{
    protected $kecamatanService;

    public function __construct(KecamatanService $kecamatanService)
    {
        $this->kecamatanService = $kecamatanService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->kecamatanService->getAllKecamatan();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row) {
                    $editUrl = route('kecamatans.edit', $row->id);
                    $deleteUrl = route('kecamatans.destroy', $row->id);
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

        return view('master.kecamatan.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('master.kecamatan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(KecamatanRequest $request): RedirectResponse
    {
        $this->kecamatanService->createKecamatan($request->validated());

        return redirect()->route('kecamatans.index')
            ->with('success', 'Data Kecamatan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $kecamatan = $this->kecamatanService->getKecamatanById($id);
        if (!$kecamatan) {
            abort(404);
        }

        return view('master.kecamatan.edit', compact('kecamatan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(KecamatanRequest $request, int $id): RedirectResponse
    {
        $this->kecamatanService->updateKecamatan($id, $request->validated());

        return redirect()->route('kecamatans.index')
            ->with('success', 'Data Kecamatan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->kecamatanService->deleteKecamatan($id);

        return redirect()->route('kecamatans.index')
            ->with('success', 'Data Kecamatan berhasil dihapus.');
    }
}
