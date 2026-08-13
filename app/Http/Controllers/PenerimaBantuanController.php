<?php

namespace App\Http\Controllers;

use App\Models\Alsintan;
use App\Models\Infrastruktur;
use App\Models\BantuanBenihPangan;
use App\Models\BantuanBibitHorti;
use App\Models\BantuanBibitPerkebunan;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PenerimaBantuanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sumberDanaAlsintan = Alsintan::whereNotNull('sumber_dana')->distinct()->pluck('sumber_dana')->toArray();
        $sumberDanaInfra = Infrastruktur::whereNotNull('sumber_dana')->distinct()->pluck('sumber_dana')->toArray();
        $sumberDanaPangan = BantuanBenihPangan::whereNotNull('sumber_dana')->distinct()->pluck('sumber_dana')->toArray();
        $sumberDanaHorti = BantuanBibitHorti::whereNotNull('sumber_dana')->distinct()->pluck('sumber_dana')->toArray();
        $sumberDanaPerkebunan = BantuanBibitPerkebunan::whereNotNull('sumber_dana')->distinct()->pluck('sumber_dana')->toArray();
        
        $sumberDana = array_unique(array_merge($sumberDanaAlsintan, $sumberDanaInfra, $sumberDanaPangan, $sumberDanaHorti, $sumberDanaPerkebunan));
        sort($sumberDana);

        $tahunAlsintan = Alsintan::whereNotNull('tahun_bantuan')->distinct()->pluck('tahun_bantuan')->toArray();
        $tahunInfra = Infrastruktur::whereNotNull('tahun_anggaran')->distinct()->pluck('tahun_anggaran')->toArray();
        $tahunPangan = BantuanBenihPangan::whereNotNull('tahun_bantuan')->distinct()->pluck('tahun_bantuan')->toArray();
        $tahunHorti = BantuanBibitHorti::whereNotNull('tahun_bantuan')->distinct()->pluck('tahun_bantuan')->toArray();
        $tahunPerkebunan = BantuanBibitPerkebunan::whereNotNull('tahun_bantuan')->distinct()->pluck('tahun_bantuan')->toArray();
        
        $tahuns = array_unique(array_merge($tahunAlsintan, $tahunInfra, $tahunPangan, $tahunHorti, $tahunPerkebunan));
        rsort($tahuns);

        return view('penyuluhan.penerima-bantuan.index', compact('sumberDana', 'tahuns'));
    }

    /**
     * Get data for DataTables dynamically.
     */
    public function getData(Request $request)
    {
        $type = $request->get('type', 'alsintan');
        $sumberDanaFilter = $request->get('sumber_dana');
        $tahunFilter = $request->get('tahun');

        if ($type === 'alsintan') {
            $data = Alsintan::with(['kelompokTani.desa.kecamatan', 'jenisAlat'])->select('alsintans.*');
            if ($sumberDanaFilter) {
                $data->where('sumber_dana', $sumberDanaFilter);
            }
            if ($tahunFilter) {
                $data->where('tahun_bantuan', $tahunFilter);
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('penerima', function ($row) {
                    return $row->kelompokTani ? $row->kelompokTani->nama : 'Umum';
                })
                ->addColumn('kecamatan_desa', function ($row) {
                    if ($row->kelompokTani && $row->kelompokTani->desa) {
                        return $row->kelompokTani->desa->nama . ' (' . ($row->kelompokTani->desa->kecamatan ? $row->kelompokTani->desa->kecamatan->nama : '-') . ')';
                    }
                    return '-';
                })
                ->addColumn('nama_bantuan', function ($row) {
                    $alat = $row->jenisAlat ? $row->jenisAlat->nama : 'Alat';
                    return $alat . ' - ' . $row->nama . ' (' . $row->merek . ')';
                })
                ->addColumn('tahun', function ($row) {
                    return $row->tahun_bantuan;
                })
                ->addColumn('detail', function ($row) {
                    return 'Kondisi: ' . $row->kondisi . ', Sumber Dana: ' . $row->sumber_dana;
                })
                ->make(true);
        } else if ($type === 'infrastruktur') {
            $data = Infrastruktur::with(['kelompokTani.desa.kecamatan', 'desa', 'kecamatan'])->select('infrastrukturs.*');
            if ($sumberDanaFilter) {
                $data->where('sumber_dana', $sumberDanaFilter);
            }
            if ($tahunFilter) {
                $data->where('tahun_anggaran', $tahunFilter);
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('penerima', function ($row) {
                    return $row->kelompokTani ? $row->kelompokTani->nama : 'Umum (Non-Kelompok)';
                })
                ->addColumn('kecamatan_desa', function ($row) {
                    $desa = $row->desa ? $row->desa->nama : '-';
                    $kec = $row->kecamatan ? $row->kecamatan->nama : '-';
                    return $desa . ' (' . $kec . ')';
                })
                ->addColumn('nama_bantuan', function ($row) {
                    return $row->jenis_infrastruktur . ' - ' . $row->nama_proyek;
                })
                ->addColumn('tahun', function ($row) {
                    return $row->tahun_anggaran;
                })
                ->addColumn('detail', function ($row) {
                    return 'Volume: ' . $row->volume . ' ' . $row->satuan . ', Anggaran: Rp ' . number_format($row->nilai_anggaran, 0, ',', '.') . ', Status: ' . $row->status;
                })
                ->make(true);
        } else if ($type === 'benih_pangan') {
            $data = BantuanBenihPangan::with(['kelompokTani.desa.kecamatan', 'komoditas', 'varietas'])->select('bantuan_benih_pangans.*');
            if ($sumberDanaFilter) {
                $data->where('sumber_dana', $sumberDanaFilter);
            }
            if ($tahunFilter) {
                $data->where('tahun_bantuan', $tahunFilter);
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('penerima', function ($row) {
                    return $row->kelompokTani ? $row->kelompokTani->nama : 'Umum';
                })
                ->addColumn('kecamatan_desa', function ($row) {
                    if ($row->kelompokTani && $row->kelompokTani->desa) {
                        return $row->kelompokTani->desa->nama . ' (' . ($row->kelompokTani->desa->kecamatan ? $row->kelompokTani->desa->kecamatan->nama : '-') . ')';
                    }
                    return '-';
                })
                ->addColumn('nama_bantuan', function ($row) {
                    $komo = $row->komoditas ? $row->komoditas->nama : 'Benih';
                    $var = $row->varietas ? ' (Var. ' . $row->varietas->nama . ')' : '';
                    return 'Benih ' . $komo . $var;
                })
                ->addColumn('tahun', function ($row) {
                    return $row->tahun_bantuan;
                })
                ->addColumn('detail', function ($row) {
                    return 'Jumlah: ' . $row->jumlah_bantuan . ' ' . $row->satuan . ', Sumber Dana: ' . $row->sumber_dana . ($row->keterangan ? ' (' . $row->keterangan . ')' : '');
                })
                ->make(true);
        } else if ($type === 'bibit_horti') {
            $data = BantuanBibitHorti::with(['kelompokTani.desa.kecamatan', 'komoditas'])->select('bantuan_bibit_hortis.*');
            if ($sumberDanaFilter) {
                $data->where('sumber_dana', $sumberDanaFilter);
            }
            if ($tahunFilter) {
                $data->where('tahun_bantuan', $tahunFilter);
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('penerima', function ($row) {
                    return $row->kelompokTani ? $row->kelompokTani->nama : 'Umum';
                })
                ->addColumn('kecamatan_desa', function ($row) {
                    if ($row->kelompokTani && $row->kelompokTani->desa) {
                        return $row->kelompokTani->desa->nama . ' (' . ($row->kelompokTani->desa->kecamatan ? $row->kelompokTani->desa->kecamatan->nama : '-') . ')';
                    }
                    return '-';
                })
                ->addColumn('nama_bantuan', function ($row) {
                    return 'Bibit ' . ($row->komoditas ? $row->komoditas->nama : 'Hortikultura');
                })
                ->addColumn('tahun', function ($row) {
                    return $row->tahun_bantuan;
                })
                ->addColumn('detail', function ($row) {
                    return 'Jumlah: ' . $row->jumlah_bantuan . ' ' . $row->satuan . ', Sumber Dana: ' . $row->sumber_dana . ($row->keterangan ? ' (' . $row->keterangan . ')' : '');
                })
                ->make(true);
        } else {
            $data = BantuanBibitPerkebunan::with(['kelompokTani.desa.kecamatan', 'komoditas'])->select('bantuan_bibit_perkebunans.*');
            if ($sumberDanaFilter) {
                $data->where('sumber_dana', $sumberDanaFilter);
            }
            if ($tahunFilter) {
                $data->where('tahun_bantuan', $tahunFilter);
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('penerima', function ($row) {
                    return $row->kelompokTani ? $row->kelompokTani->nama : 'Umum';
                })
                ->addColumn('kecamatan_desa', function ($row) {
                    if ($row->kelompokTani && $row->kelompokTani->desa) {
                        return $row->kelompokTani->desa->nama . ' (' . ($row->kelompokTani->desa->kecamatan ? $row->kelompokTani->desa->kecamatan->nama : '-') . ')';
                    }
                    return '-';
                })
                ->addColumn('nama_bantuan', function ($row) {
                    return 'Bibit ' . ($row->komoditas ? $row->komoditas->nama : 'Perkebunan');
                })
                ->addColumn('tahun', function ($row) {
                    return $row->tahun_bantuan;
                })
                ->addColumn('detail', function ($row) {
                    return 'Jumlah: ' . $row->jumlah_bantuan . ' ' . $row->satuan . ', Sumber Dana: ' . $row->sumber_dana . ($row->keterangan ? ' (' . $row->keterangan . ')' : '');
                })
                ->make(true);
        }
    }
}
