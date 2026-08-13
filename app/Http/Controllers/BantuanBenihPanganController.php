<?php

namespace App\Http\Controllers;

use App\Models\BantuanBenihPangan;
use App\Models\KelompokTani;
use App\Models\Komoditas;
use App\Models\Varietas;
use App\Models\Petani;
use App\Models\BantuanBenihPanganDetail;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BantuanBenihPanganController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = BantuanBenihPangan::with(['kelompokTani', 'komoditas', 'varietas'])->select('bantuan_benih_pangans.*');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('kelompok_tani_nama', function ($row) {
                    return $row->kelompokTani ? $row->kelompokTani->nama : '-';
                })
                ->addColumn('komoditas_nama', function ($row) {
                    return $row->komoditas ? $row->komoditas->nama : '-';
                })
                ->addColumn('varietas_nama', function ($row) {
                    return $row->varietas ? $row->varietas->nama : '-';
                })
                ->editColumn('jumlah_bantuan', function ($row) {
                    return number_format($row->jumlah_bantuan, 0, ',', '.') . ' ' . $row->satuan;
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('bantuan-benih-pangan.edit', $row->id);
                    $deleteUrl = route('bantuan-benih-pangan.destroy', $row->id);
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

        return view('produksi.tanaman-pangan.bantuan.index');
    }

    public function create()
    {
        $kelompokTanis = KelompokTani::pluck('nama', 'id')->toArray();
        $komoditas = Komoditas::whereHas('kategori', function($q) {
            $q->where('nama', 'like', '%Pangan%');
        })->pluck('nama', 'id')->toArray();
        $varietas = Varietas::pluck('nama', 'id')->toArray();

        return view('produksi.tanaman-pangan.bantuan.create', compact('kelompokTanis', 'komoditas', 'varietas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelompok_tani_id' => 'required|exists:kelompok_tanis,id',
            'komoditas_id' => 'required|exists:komoditas,id',
            'varietas_id' => 'nullable|exists:varietas,id',
            'satuan' => 'required|string|max:50',
            'sumber_dana' => 'required|string|max:100',
            'tahun_bantuan' => 'required|integer|min:2000|max:' . date('Y'),
            'keterangan' => 'nullable|string',
            'petani_jumlah' => 'required|array',
            'petani_jumlah.*' => 'nullable|numeric|min:0',
        ]);

        $totalBantuan = 0;
        $detailsData = [];
        foreach ($request->get('petani_jumlah', []) as $petaniId => $jumlah) {
            if ($jumlah > 0) {
                $totalBantuan += $jumlah;
                $detailsData[] = [
                    'petani_id' => $petaniId,
                    'jumlah_bantuan' => $jumlah
                ];
            }
        }
        $validated['jumlah_bantuan'] = $totalBantuan;

        $bantuan = BantuanBenihPangan::create($validated);

        foreach ($detailsData as $detail) {
            $bantuan->details()->create($detail);
        }

        return redirect()->route('bantuan-benih-pangan.index')
            ->with('success', 'Data Bantuan Benih Pangan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $bantuan = BantuanBenihPangan::findOrFail($id);
        $kelompokTanis = KelompokTani::pluck('nama', 'id')->toArray();
        $komoditas = Komoditas::whereHas('kategori', function($q) {
            $q->where('nama', 'like', '%Pangan%');
        })->pluck('nama', 'id')->toArray();
        $varietas = Varietas::pluck('nama', 'id')->toArray();

        $petanis = Petani::where('kelompok_tani_id', $bantuan->kelompok_tani_id)->get(['id', 'nama', 'nik']);
        $existingDetails = $bantuan->details->pluck('jumlah_bantuan', 'petani_id')->toArray();

        return view('produksi.tanaman-pangan.bantuan.edit', compact('bantuan', 'kelompokTanis', 'komoditas', 'varietas', 'petanis', 'existingDetails'));
    }

    public function update(Request $request, $id)
    {
        $bantuan = BantuanBenihPangan::findOrFail($id);
        $validated = $request->validate([
            'kelompok_tani_id' => 'required|exists:kelompok_tanis,id',
            'komoditas_id' => 'required|exists:komoditas,id',
            'varietas_id' => 'nullable|exists:varietas,id',
            'satuan' => 'required|string|max:50',
            'sumber_dana' => 'required|string|max:100',
            'tahun_bantuan' => 'required|integer|min:2000|max:' . date('Y'),
            'keterangan' => 'nullable|string',
            'petani_jumlah' => 'required|array',
            'petani_jumlah.*' => 'nullable|numeric|min:0',
        ]);

        $totalBantuan = 0;
        $detailsData = [];
        foreach ($request->get('petani_jumlah', []) as $petaniId => $jumlah) {
            if ($jumlah > 0) {
                $totalBantuan += $jumlah;
                $detailsData[] = [
                    'petani_id' => $petaniId,
                    'jumlah_bantuan' => $jumlah
                ];
            }
        }
        $validated['jumlah_bantuan'] = $totalBantuan;

        $bantuan->update($validated);

        $bantuan->details()->delete();
        foreach ($detailsData as $detail) {
            $bantuan->details()->create($detail);
        }

        return redirect()->route('bantuan-benih-pangan.index')
            ->with('success', 'Data Bantuan Benih Pangan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $bantuan = BantuanBenihPangan::findOrFail($id);
        $bantuan->delete();

        return redirect()->route('bantuan-benih-pangan.index')
            ->with('success', 'Data Bantuan Benih Pangan berhasil dihapus.');
    }
}
