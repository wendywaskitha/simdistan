<?php

namespace App\Http\Controllers;

use App\Http\Requests\JenisPupukRequest;
use App\Services\JenisPupukService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class JenisPupukController extends Controller
{
    protected $jenisService;

    public function __construct(JenisPupukService $jenisService)
    {
        $this->jenisService = $jenisService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->jenisService->getAllJenis();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row) {
                    $editUrl = route('jenis-pupuks.edit', $row->id);
                    $deleteUrl = route('jenis-pupuks.destroy', $row->id);
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

        return view('master.jenis-pupuk.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('master.jenis-pupuk.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(JenisPupukRequest $request): RedirectResponse
    {
        $this->jenisService->createJenis($request->validated());

        return redirect()->route('jenis-pupuks.index')
            ->with('success', 'Data Jenis Pupuk berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $jenis = $this->jenisService->getJenisById($id);
        if (!$jenis) {
            abort(404);
        }

        return view('master.jenis-pupuk.edit', compact('jenis'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(JenisPupukRequest $request, int $id): RedirectResponse
    {
        $this->jenisService->updateJenis($id, $request->validated());

        return redirect()->route('jenis-pupuks.index')
            ->with('success', 'Data Jenis Pupuk berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->jenisService->deleteJenis($id);

        return redirect()->route('jenis-pupuks.index')
            ->with('success', 'Data Jenis Pupuk berhasil dihapus.');
    }
}
