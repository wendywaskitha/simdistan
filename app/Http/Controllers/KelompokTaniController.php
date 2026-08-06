<?php

namespace App\Http\Controllers;

use App\Http\Requests\KelompokTaniRequest;
use App\Services\KelompokTaniService;
use App\Services\DesaService;
use App\Services\GapoktanService;
use App\Models\KelompokTani;
use App\Models\Petani;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class KelompokTaniController extends Controller
{
    protected $kelompokTaniService;
    protected $desaService;
    protected $gapoktanService;

    public function __construct(
        KelompokTaniService $kelompokTaniService,
        DesaService $desaService,
        GapoktanService $gapoktanService
    ) {
        $this->kelompokTaniService = $kelompokTaniService;
        $this->desaService = $desaService;
        $this->gapoktanService = $gapoktanService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->kelompokTaniService->getAllKelompokTani();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('desa_nama', function($row) {
                    return $row->desa->nama;
                })
                ->addColumn('gapoktan_nama', function($row) {
                    return $row->gapoktan ? $row->gapoktan->nama : '-';
                })
                ->addColumn('sk_status', function($row) {
                    return $row->sk_pembentukan 
                        ? '<a href="'.asset('storage/'.$row->sk_pembentukan).'" target="_blank" class="text-success"><i class="bi bi-patch-check-fill fs-5"></i></a>' 
                        : '<span class="text-danger">—</span>';
                })
                ->addColumn('ba_status', function($row) {
                    return $row->berita_acara 
                        ? '<a href="'.asset('storage/'.$row->berita_acara).'" target="_blank" class="text-success"><i class="bi bi-patch-check-fill fs-5"></i></a>' 
                        : '<span class="text-danger">—</span>';
                })
                ->addColumn('action', function($row) {
                    $editUrl   = route('kelompok-tanis.edit', $row->id);
                    $kelolaUrl = route('kelompok-tanis.kelola-anggota', $row->id);
                    $deleteUrl = route('kelompok-tanis.destroy', $row->id);
                    
                    return '
                        <div class="d-flex gap-2">
                            <a href="'.$kelolaUrl.'" class="btn btn-sm btn-success" title="Kelola Anggota"><i class="bi bi-people-fill"></i></a>
                            <a href="'.$editUrl.'" class="btn btn-sm btn-info text-white" title="Edit"><i class="bi bi-pencil-square"></i></a>
                            <form action="'.$deleteUrl.'" method="POST" class="d-inline">
                                '.csrf_field().'
                                '.method_field('DELETE').'
                                <button type="submit" class="btn btn-sm btn-danger btn-delete-trigger"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    ';
                })
                ->rawColumns(['sk_status', 'ba_status', 'action'])
                ->make(true);
        }

        return view('master.kelompok-tani.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $desas = $this->desaService->getAllDesa()->pluck('nama', 'id')->toArray();
        $gapoktans = $this->gapoktanService->getAllGapoktan()->pluck('nama', 'id')->toArray();
        return view('master.kelompok-tani.create', compact('desas', 'gapoktans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(KelompokTaniRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('sk_pembentukan')) {
            $data['sk_pembentukan'] = $request->file('sk_pembentukan')->store('kelompok_tani/sk', 'public');
        }
        if ($request->hasFile('berita_acara')) {
            $data['berita_acara'] = $request->file('berita_acara')->store('kelompok_tani/berita_acara', 'public');
        }
        $this->kelompokTaniService->createKelompokTani($data);

        return redirect()->route('kelompok-tanis.index')
            ->with('success', 'Data Kelompok Tani berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $kelompokTani = $this->kelompokTaniService->getKelompokTaniById($id);
        if (!$kelompokTani) {
            abort(404);
        }

        $desas = $this->desaService->getAllDesa()->pluck('nama', 'id')->toArray();
        $gapoktans = $this->gapoktanService->getAllGapoktan()->pluck('nama', 'id')->toArray();
        return view('master.kelompok-tani.edit', compact('kelompokTani', 'desas', 'gapoktans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(KelompokTaniRequest $request, int $id): RedirectResponse
    {
        $data = $request->validated();
        $record = $this->kelompokTaniService->getKelompokTaniById($id);

        if ($request->hasFile('sk_pembentukan')) {
            if ($record && $record->sk_pembentukan) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($record->sk_pembentukan);
            }
            $data['sk_pembentukan'] = $request->file('sk_pembentukan')->store('kelompok_tani/sk', 'public');
        }
        if ($request->hasFile('berita_acara')) {
            if ($record && $record->berita_acara) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($record->berita_acara);
            }
            $data['berita_acara'] = $request->file('berita_acara')->store('kelompok_tani/berita_acara', 'public');
        }

        $this->kelompokTaniService->updateKelompokTani($id, $data);

        return redirect()->route('kelompok-tanis.index')
            ->with('success', 'Data Kelompok Tani berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->kelompokTaniService->deleteKelompokTani($id);

        return redirect()->route('kelompok-tanis.index')
            ->with('success', 'Data Kelompok Tani berhasil dihapus.');
    }

    // ─── Kelola Anggota ───────────────────────────────────────────────────────

    /**
     * Halaman kelola petani anggota kelompok tani.
     */
    public function kelolaAnggota(int $id, Request $request): View
    {
        $kelompokTani = KelompokTani::with(['desa.kecamatan', 'gapoktan'])->findOrFail($id);

        // Anggota saat ini (paginated)
        $petanis = Petani::with(['kelompokTani.desa.kecamatan'])
            ->where('kelompok_tani_id', $id)
            ->orderBy('nama')
            ->paginate(25)
            ->withQueryString();

        // Petani belum memiliki kelompok tani sama sekali (untuk di-attach)
        $availablePetanis = Petani::whereNull('kelompok_tani_id')
            ->orderBy('nama')
            ->get();

        return view('master.kelompok-tani.kelola-anggota', compact(
            'kelompokTani', 'petanis', 'availablePetanis'
        ));
    }

    /**
     * Attach petani terpilih ke kelompok tani ini.
     */
    public function attachAnggota(int $id, Request $request): RedirectResponse
    {
        $request->validate([
            'petani_ids'   => ['required', 'array', 'min:1'],
            'petani_ids.*' => ['exists:petanis,id'],
        ]);

        Petani::whereIn('id', $request->petani_ids)->update(['kelompok_tani_id' => $id]);

        return redirect()->route('kelompok-tanis.kelola-anggota', $id)
            ->with('success', count($request->petani_ids) . ' petani berhasil ditambahkan sebagai anggota.');
    }

    /**
     * Buat petani baru dan langsung tambahkan ke kelompok ini.
     */
    public function createNewAnggota(int $id, Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama'    => ['required', 'string', 'max:255'],
            'nik'     => ['nullable', 'string', 'max:16'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'alamat'  => ['nullable', 'string', 'max:500'],
            'luas_lahan' => ['nullable', 'numeric', 'min:0'],
            'ktp'     => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        if ($request->hasFile('ktp')) {
            $data['ktp'] = $request->file('ktp')->store('ktp', 'public');
        }

        $data['kelompok_tani_id'] = $id;
        Petani::create($data);

        return redirect()->route('kelompok-tanis.kelola-anggota', $id)
            ->with('success', 'Petani baru berhasil dibuat dan ditambahkan sebagai anggota.');
    }

    /**
     * Lepaskan petani dari kelompok tani (set kelompok_tani_id = null).
     */
    public function removeAnggota(int $kelompokTaniId, int $petaniId): RedirectResponse
    {
        $petani = Petani::where('id', $petaniId)
            ->where('kelompok_tani_id', $kelompokTaniId)
            ->firstOrFail();

        $petani->update(['kelompok_tani_id' => null]);

        return redirect()->route('kelompok-tanis.kelola-anggota', $kelompokTaniId)
            ->with('success', 'Petani berhasil dilepaskan dari kelompok tani.');
    }

    /**
     * Jadikan petani anggota ini sebagai Ketua Kelompok Tani.
     */
    public function setKetua(int $kelompokTaniId, int $petaniId): RedirectResponse
    {
        $kelompokTani = KelompokTani::findOrFail($kelompokTaniId);
        $petani = Petani::where('id', $petaniId)
            ->where('kelompok_tani_id', $kelompokTaniId)
            ->firstOrFail();

        $kelompokTani->update([
            'ketua_petani_id' => $petani->id,
            'ketua'           => $petani->nama
        ]);

        return redirect()->route('kelompok-tanis.kelola-anggota', $kelompokTaniId)
            ->with('success', $petani->nama . ' berhasil ditetapkan sebagai Ketua Kelompok Tani.');
    }
}

