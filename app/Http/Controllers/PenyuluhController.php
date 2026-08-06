<?php

namespace App\Http\Controllers;

use App\Http\Requests\PenyuluhRequest;
use App\Services\PenyuluhService;
use App\Services\BppService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class PenyuluhController extends Controller
{
    protected $penyuluhService;
    protected $bppService;

    public function __construct(PenyuluhService $penyuluhService, BppService $bppService)
    {
        $this->penyuluhService = $penyuluhService;
        $this->bppService = $bppService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->penyuluhService->getAllPenyuluh();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('bpp_nama', function($row) {
                    return $row->bpp->nama;
                })
                ->addColumn('action', function($row) {
                    $editUrl = route('penyuluhs.edit', $row->id);
                    $deleteUrl = route('penyuluhs.destroy', $row->id);
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

        return view('master.penyuluh.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $bpps = $this->bppService->getAllBpp()->pluck('nama', 'id')->toArray();
        return view('master.penyuluh.create', compact('bpps'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PenyuluhRequest $request): RedirectResponse
    {
        $this->penyuluhService->createPenyuluh($request->validated());

        return redirect()->route('penyuluhs.index')
            ->with('success', 'Data Penyuluh berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $penyuluh = $this->penyuluhService->getPenyuluhById($id);
        if (!$penyuluh) {
            abort(404);
        }

        $bpps = $this->bppService->getAllBpp()->pluck('nama', 'id')->toArray();
        return view('master.penyuluh.edit', compact('penyuluh', 'bpps'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PenyuluhRequest $request, int $id): RedirectResponse
    {
        $this->penyuluhService->updatePenyuluh($id, $request->validated());

        return redirect()->route('penyuluhs.index')
            ->with('success', 'Data Penyuluh berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->penyuluhService->deletePenyuluh($id);

        return redirect()->route('penyuluhs.index')
            ->with('success', 'Data Penyuluh berhasil dihapus.');
    }
}
