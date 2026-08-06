<?php

namespace App\Http\Controllers;

use App\Http\Requests\SatuanRequest;
use App\Services\SatuanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class SatuanController extends Controller
{
    protected $satuanService;

    public function __construct(SatuanService $satuanService)
    {
        $this->satuanService = $satuanService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->satuanService->getAllSatuan();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row) {
                    $editUrl = route('satuans.edit', $row->id);
                    $deleteUrl = route('satuans.destroy', $row->id);
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

        return view('master.satuan.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('master.satuan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SatuanRequest $request): RedirectResponse
    {
        $this->satuanService->createSatuan($request->validated());

        return redirect()->route('satuans.index')
            ->with('success', 'Data Satuan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $satuan = $this->satuanService->getSatuanById($id);
        if (!$satuan) {
            abort(404);
        }

        return view('master.satuan.edit', compact('satuan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SatuanRequest $request, int $id): RedirectResponse
    {
        $this->satuanService->updateSatuan($id, $request->validated());

        return redirect()->route('satuans.index')
            ->with('success', 'Data Satuan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->satuanService->deleteSatuan($id);

        return redirect()->route('satuans.index')
            ->with('success', 'Data Satuan berhasil dihapus.');
    }
}
