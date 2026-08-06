<?php

namespace App\Http\Controllers;

use App\Http\Requests\BidangRequest;
use App\Services\BidangService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class BidangController extends Controller
{
    protected $bidangService;

    public function __construct(BidangService $bidangService)
    {
        $this->bidangService = $bidangService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->bidangService->getAllBidang();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row) {
                    $editUrl = route('bidangs.edit', $row->id);
                    $deleteUrl = route('bidangs.destroy', $row->id);
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

        return view('master.bidang.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('master.bidang.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BidangRequest $request): RedirectResponse
    {
        $this->bidangService->createBidang($request->validated());

        return redirect()->route('bidangs.index')
            ->with('success', 'Data Bidang berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $bidang = $this->bidangService->getBidangById($id);
        if (!$bidang) {
            abort(404);
        }

        return view('master.bidang.edit', compact('bidang'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BidangRequest $request, int $id): RedirectResponse
    {
        $this->bidangService->updateBidang($id, $request->validated());

        return redirect()->route('bidangs.index')
            ->with('success', 'Data Bidang berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->bidangService->deleteBidang($id);

        return redirect()->route('bidangs.index')
            ->with('success', 'Data Bidang berhasil dihapus.');
    }
}
