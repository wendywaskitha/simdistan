<?php

namespace App\Http\Controllers;

use App\Http\Requests\GapoktanRequest;
use App\Services\GapoktanService;
use App\Services\KecamatanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class GapoktanController extends Controller
{
    protected $gapoktanService;
    protected $kecamatanService;

    public function __construct(GapoktanService $gapoktanService, KecamatanService $kecamatanService)
    {
        $this->gapoktanService = $gapoktanService;
        $this->kecamatanService = $kecamatanService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->gapoktanService->getAllGapoktan();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('kecamatan_nama', function($row) {
                    return $row->kecamatan->nama;
                })
                ->addColumn('action', function($row) {
                    $editUrl = route('gapoktans.edit', $row->id);
                    $deleteUrl = route('gapoktans.destroy', $row->id);
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

        return view('master.gapoktan.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $kecamatans = $this->kecamatanService->getAllKecamatan()->pluck('nama', 'id')->toArray();
        return view('master.gapoktan.create', compact('kecamatans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GapoktanRequest $request): RedirectResponse
    {
        $this->gapoktanService->createGapoktan($request->validated());

        return redirect()->route('gapoktans.index')
            ->with('success', 'Data Gapoktan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $gapoktan = $this->gapoktanService->getGapoktanById($id);
        if (!$gapoktan) {
            abort(404);
        }

        $kecamatans = $this->kecamatanService->getAllKecamatan()->pluck('nama', 'id')->toArray();
        return view('master.gapoktan.edit', compact('gapoktan', 'kecamatans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GapoktanRequest $request, int $id): RedirectResponse
    {
        $this->gapoktanService->updateGapoktan($id, $request->validated());

        return redirect()->route('gapoktans.index')
            ->with('success', 'Data Gapoktan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->gapoktanService->deleteGapoktan($id);

        return redirect()->route('gapoktans.index')
            ->with('success', 'Data Gapoktan berhasil dihapus.');
    }
}
