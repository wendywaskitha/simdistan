<?php

namespace App\Http\Controllers;

use App\Models\KategoriKomoditas;
use App\Models\Kecamatan;
use App\Models\LaporanProduksi;
use App\Models\Alsintan;
use App\Models\Infrastruktur;
use App\Models\LaporanPupuk;
use App\Models\LaporanPupukDetail;
use App\Models\JenisPupuk;
use App\Models\KuotaTahunanPupuk;
use App\Models\Penyuluh;
use App\Models\Gapoktan;
use App\Models\KelompokTani;
use App\Models\Petani;
use App\Models\Bpp;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanBpsExport;

class LaporanBpsController extends Controller
{
    public function index()
    {
        $currentYear = intval(date('Y'));
        $years = range($currentYear, $currentYear - 9);
        return view('laporan-bps.index', compact('years', 'currentYear'));
    }

    // ─── TANAMAN PANGAN ───────────────────────────────────────────────────────
    public function tanamanPangan(Request $request)
    {
        $tahun       = intval($request->get('tahun', date('Y')));
        $kecamatanId = $request->get('kecamatan_id');

        $kategori   = KategoriKomoditas::where('nama', 'Tanaman Pangan')->first();
        $kecamatans = Kecamatan::orderBy('nama')->get();

        $query = LaporanProduksi::with(['kecamatan', 'komoditas', 'satuan'])
            ->where('kategori_komoditas_id', $kategori->id)
            ->where('tahun', $tahun);
        if ($kecamatanId) $query->where('kecamatan_id', $kecamatanId);

        $laporans = $query->get();
        $grouped  = $this->groupPanganData($laporans);

        $currentYear = intval(date('Y'));
        $years = range($currentYear, $currentYear - 9);

        return view('laporan-bps.tanaman-pangan', compact('grouped', 'tahun', 'kecamatans', 'kecamatanId', 'years'));
    }

    public function tanamanPanganPdf(Request $request)
    {
        $tahun       = intval($request->get('tahun', date('Y')));
        $kecamatanId = $request->get('kecamatan_id');
        $kategori    = KategoriKomoditas::where('nama', 'Tanaman Pangan')->first();

        $query = LaporanProduksi::with(['kecamatan', 'komoditas'])
            ->where('kategori_komoditas_id', $kategori->id)
            ->where('tahun', $tahun);
        if ($kecamatanId) $query->where('kecamatan_id', $kecamatanId);

        $grouped = $this->groupPanganData($query->get());
        $kecamatanNama = $kecamatanId ? Kecamatan::find($kecamatanId)?->nama : null;
        $pdf = Pdf::loadView('laporan-bps.pdf.tanaman-pangan', compact('grouped', 'tahun', 'kecamatanNama'))
                  ->setPaper('a4', 'landscape');
        return $pdf->stream("laporan-tanaman-pangan-{$tahun}.pdf");
    }

    public function tanamanPanganExcel(Request $request)
    {
        $tahun       = intval($request->get('tahun', date('Y')));
        $kecamatanId = $request->get('kecamatan_id');
        $kategori    = KategoriKomoditas::where('nama', 'Tanaman Pangan')->first();

        $query = LaporanProduksi::with(['kecamatan', 'komoditas'])
            ->where('kategori_komoditas_id', $kategori->id)
            ->where('tahun', $tahun);
        if ($kecamatanId) $query->where('kecamatan_id', $kecamatanId);

        $grouped = $this->groupPanganData($query->get());
        return Excel::download(
            new LaporanBpsExport('tanaman-pangan', $grouped, $tahun),
            "laporan-bps-tanaman-pangan-{$tahun}.xlsx"
        );
    }

    private function groupPanganData($laporans)
    {
        return $laporans->groupBy('kecamatan_id')->map(function ($rows) {
            $kecamatan   = $rows->first()->kecamatan;
            $byKomoditas = $rows->groupBy('komoditas_id')->map(function ($rk) {
                return [
                    'komoditas'     => $rk->first()->komoditas,
                    'luas_lahan'    => $rk->sum('luas_lahan'),
                    'luas_tanam'    => $rk->sum('luas_tanam'),
                    'luas_panen'    => $rk->sum('luas_panen'),
                    'produksi'      => $rk->sum('produksi'),
                    'produktivitas' => $rk->sum('luas_panen') > 0
                        ? ($rk->sum('produksi') / $rk->sum('luas_panen')) : 0,
                ];
            })->values();
            return [
                'kecamatan'      => $kecamatan,
                'rows'           => $byKomoditas,
                'total_lahan'    => $byKomoditas->sum('luas_lahan'),
                'total_tanam'    => $byKomoditas->sum('luas_tanam'),
                'total_panen'    => $byKomoditas->sum('luas_panen'),
                'total_produksi' => $byKomoditas->sum('produksi'),
            ];
        })->values();
    }

    // ─── HORTIKULTURA ─────────────────────────────────────────────────────────
    public function hortikultura(Request $request)
    {
        $tahun       = intval($request->get('tahun', date('Y')));
        $kecamatanId = $request->get('kecamatan_id');
        $formType    = $request->get('form_type');

        $kategoriIds = KategoriKomoditas::where('nama', 'LIKE', '%Hortikultura%')->pluck('id')->toArray();
        $kecamatans = Kecamatan::orderBy('nama')->get();

        $query = LaporanProduksi::with(['kecamatan', 'komoditas', 'satuan'])
            ->whereIn('kategori_komoditas_id', $kategoriIds)
            ->where('tahun', $tahun);
        if ($kecamatanId) $query->where('kecamatan_id', $kecamatanId);
        if ($formType)    $query->where('form_type', $formType);
        $laporans = $query->orderBy('kecamatan_id')->orderBy('komoditas_id')->get();

        $currentYear = intval(date('Y'));
        $years     = range($currentYear, $currentYear - 9);
        $formTypes = ['SPH-SBS' => 'SPH-SBS (Sayuran/Buah Semusim)', 'SPH-BST' => 'SPH-BST (Buah Tahunan)', 'SPH-TBF' => 'SPH-TBF (Biofarmaka)'];

        return view('laporan-bps.hortikultura', compact('laporans', 'tahun', 'kecamatans', 'kecamatanId', 'formType', 'years', 'formTypes'));
    }

    public function hortikulturaPdf(Request $request)
    {
        $tahun       = intval($request->get('tahun', date('Y')));
        $kecamatanId = $request->get('kecamatan_id');
        $formType    = $request->get('form_type');
        $kategoriIds = KategoriKomoditas::where('nama', 'LIKE', '%Hortikultura%')->pluck('id')->toArray();

        $query = LaporanProduksi::with(['kecamatan', 'komoditas', 'satuan'])
            ->whereIn('kategori_komoditas_id', $kategoriIds)
            ->where('tahun', $tahun);
        if ($kecamatanId) $query->where('kecamatan_id', $kecamatanId);
        if ($formType)    $query->where('form_type', $formType);
        $laporans = $query->orderBy('kecamatan_id')->orderBy('komoditas_id')->get();

        $pdf = Pdf::loadView('laporan-bps.pdf.hortikultura', compact('laporans', 'tahun', 'formType'))
                  ->setPaper('a4', 'landscape');
        return $pdf->stream("laporan-bps-hortikultura-{$tahun}.pdf");
    }

    public function hortikulturaExcel(Request $request)
    {
        $tahun       = intval($request->get('tahun', date('Y')));
        $kecamatanId = $request->get('kecamatan_id');
        $formType    = $request->get('form_type');
        $kategoriIds = KategoriKomoditas::where('nama', 'LIKE', '%Hortikultura%')->pluck('id')->toArray();

        $query = LaporanProduksi::with(['kecamatan', 'komoditas', 'satuan'])
            ->whereIn('kategori_komoditas_id', $kategoriIds)
            ->where('tahun', $tahun);
        if ($kecamatanId) $query->where('kecamatan_id', $kecamatanId);
        if ($formType)    $query->where('form_type', $formType);
        $laporans = $query->orderBy('kecamatan_id')->orderBy('komoditas_id')->get();

        return Excel::download(
            new LaporanBpsExport('hortikultura', $laporans, $tahun),
            "laporan-bps-hortikultura-{$tahun}.xlsx"
        );
    }

    // ─── PERKEBUNAN ───────────────────────────────────────────────────────────
    public function perkebunan(Request $request)
    {
        $tahun       = intval($request->get('tahun', date('Y')));
        $kecamatanId = $request->get('kecamatan_id');
        $bulan       = $request->get('bulan');

        $kategori   = KategoriKomoditas::where('nama', 'Perkebunan')->first();
        $kecamatans = Kecamatan::orderBy('nama')->get();

        $query = LaporanProduksi::with(['kecamatan', 'komoditas', 'satuan'])
            ->where('kategori_komoditas_id', $kategori->id)
            ->where('tahun', $tahun);
        if ($kecamatanId) $query->where('kecamatan_id', $kecamatanId);
        if ($bulan)       $query->where('bulan', $bulan);
        $laporans = $query->orderBy('kecamatan_id')->orderBy('komoditas_id')->get();

        $currentYear = intval(date('Y'));
        $years     = range($currentYear, $currentYear - 9);
        $semesters = [1 => 'Semester I (Jan-Jun)', 2 => 'Semester II (Jul-Des)'];

        return view('laporan-bps.perkebunan', compact('laporans', 'tahun', 'kecamatans', 'kecamatanId', 'bulan', 'years', 'semesters'));
    }

    public function perkebunanPdf(Request $request)
    {
        $tahun       = intval($request->get('tahun', date('Y')));
        $kecamatanId = $request->get('kecamatan_id');
        $bulan       = $request->get('bulan');
        $kategori    = KategoriKomoditas::where('nama', 'Perkebunan')->first();

        $query = LaporanProduksi::with(['kecamatan', 'komoditas', 'satuan'])
            ->where('kategori_komoditas_id', $kategori->id)
            ->where('tahun', $tahun);
        if ($kecamatanId) $query->where('kecamatan_id', $kecamatanId);
        if ($bulan)       $query->where('bulan', $bulan);
        $laporans  = $query->orderBy('kecamatan_id')->orderBy('komoditas_id')->get();
        $semesters = [1 => 'Semester I (Jan-Jun)', 2 => 'Semester II (Jul-Des)'];

        $pdf = Pdf::loadView('laporan-bps.pdf.perkebunan', compact('laporans', 'tahun', 'semesters', 'bulan'))
                  ->setPaper('a4', 'landscape');
        return $pdf->stream("laporan-bps-perkebunan-{$tahun}.pdf");
    }

    public function perkebunanExcel(Request $request)
    {
        $tahun       = intval($request->get('tahun', date('Y')));
        $kecamatanId = $request->get('kecamatan_id');
        $bulan       = $request->get('bulan');
        $kategori    = KategoriKomoditas::where('nama', 'Perkebunan')->first();

        $query = LaporanProduksi::with(['kecamatan', 'komoditas', 'satuan'])
            ->where('kategori_komoditas_id', $kategori->id)
            ->where('tahun', $tahun);
        if ($kecamatanId) $query->where('kecamatan_id', $kecamatanId);
        if ($bulan)       $query->where('bulan', $bulan);
        $laporans = $query->orderBy('kecamatan_id')->orderBy('komoditas_id')->get();

        return Excel::download(
            new LaporanBpsExport('perkebunan', $laporans, $tahun),
            "laporan-bps-perkebunan-{$tahun}.xlsx"
        );
    }

    // ─── PSP ──────────────────────────────────────────────────────────────────
    public function psp(Request $request)
    {
        $tahun       = intval($request->get('tahun', date('Y')));
        $tab         = $request->get('tab', 'alsintan');
        $kecamatanId = $request->get('kecamatan_id');

        $kecamatans  = Kecamatan::orderBy('nama')->get();
        $currentYear = intval(date('Y'));
        $years       = range($currentYear, $currentYear - 9);

        [$alsintans, $infrastrukturs, $pupukData, $jenisPupuks, $pemanfaatanLaporans, $infrastrukturLaporans, $realokasiAlsintans, $realokasiPupuks] = $this->buildPspData($tab, $tahun, $kecamatanId);

        return view('laporan-bps.psp', compact(
            'tab', 'tahun', 'kecamatans', 'kecamatanId', 'years',
            'alsintans', 'infrastrukturs', 'pupukData', 'jenisPupuks', 'pemanfaatanLaporans', 'infrastrukturLaporans', 'realokasiAlsintans', 'realokasiPupuks'
        ));
    }

    public function pspPdf(Request $request)
    {
        $tahun       = intval($request->get('tahun', date('Y')));
        $tab         = $request->get('tab', 'alsintan');
        $kecamatanId = $request->get('kecamatan_id');

        [$alsintans, $infrastrukturs, $pupukData, $jenisPupuks, $pemanfaatanLaporans, $infrastrukturLaporans, $realokasiAlsintans, $realokasiPupuks] = $this->buildPspData($tab, $tahun, $kecamatanId);

        $pdf = Pdf::loadView("laporan-bps.pdf.psp-{$tab}", compact(
            'alsintans', 'infrastrukturs', 'pupukData', 'jenisPupuks', 'pemanfaatanLaporans', 'infrastrukturLaporans', 'realokasiAlsintans', 'realokasiPupuks', 'tahun'
        ))->setPaper('a4', 'landscape');

        return $pdf->stream("laporan-bps-psp-{$tab}-{$tahun}.pdf");
    }

    public function pspExcel(Request $request)
    {
        $tahun       = intval($request->get('tahun', date('Y')));
        $tab         = $request->get('tab', 'alsintan');
        $kecamatanId = $request->get('kecamatan_id');

        [$alsintans, $infrastrukturs, $pupukData, $jenisPupuks, $pemanfaatanLaporans, $infrastrukturLaporans, $realokasiAlsintans, $realokasiPupuks] = $this->buildPspData($tab, $tahun, $kecamatanId);
        $data = match ($tab) {
            'alsintan'      => $alsintans,
            'infrastruktur' => $infrastrukturs,
            'pupuk'         => $pupukData,
            'pemanfaatan'   => $pemanfaatanLaporans,
            'laporan-infrastruktur' => $infrastrukturLaporans,
            'realokasi-alsintan'    => $realokasiAlsintans,
            'realokasi-pupuk'       => $realokasiPupuks,
            default         => collect(),
        };

        return Excel::download(
            new LaporanBpsExport("psp-{$tab}", $data, $tahun),
            "laporan-bps-psp-{$tab}-{$tahun}.xlsx"
        );
    }

    private function buildPspData(string $tab, int $tahun, ?int $kecamatanId): array
    {
        $alsintans = $infrastrukturs = $pupukData = $jenisPupuks = $pemanfaatanLaporans = $infrastrukturLaporans = $realokasiAlsintans = $realokasiPupuks = collect();

        if ($tab === 'alsintan') {
            $q = Alsintan::with(['jenisAlat', 'kelompokTani.desa.kecamatan'])->where('tahun_bantuan', $tahun);
            if ($kecamatanId) {
                $q->whereHas('kelompokTani.desa.kecamatan', fn($q2) => $q2->where('id', $kecamatanId));
            }
            $alsintans = $q->get();

        } elseif ($tab === 'pemanfaatan') {
            $q = \App\Models\LaporanPemanfaatanAlsintan::with(['alsintan.jenisAlat', 'alsintan.kelompokTani.desa.kecamatan'])
                ->whereYear('tanggal', $tahun);
            if ($kecamatanId) {
                $q->whereHas('alsintan.kelompokTani.desa.kecamatan', fn($q2) => $q2->where('id', $kecamatanId));
            }
            $pemanfaatanLaporans = $q->get();
        } elseif ($tab === 'infrastruktur') {
            $q = Infrastruktur::with(['kecamatan', 'desa', 'kelompokTani'])->where('tahun_anggaran', $tahun);
            if ($kecamatanId) $q->where('kecamatan_id', $kecamatanId);
            $infrastrukturs = $q->get();

        } elseif ($tab === 'laporan-infrastruktur') {
            $q = \App\Models\InfrastrukturLaporan::whereHas('infrastruktur')
                ->with(['infrastruktur.kecamatan', 'infrastruktur.desa'])
                ->whereYear('tanggal_laporan', $tahun);
            if ($kecamatanId) {
                $q->whereHas('infrastruktur', fn($q2) => $q2->where('kecamatan_id', $kecamatanId));
            }
            $infrastrukturLaporans = $q->get();

        } elseif ($tab === 'realokasi-alsintan') {
            $q = \App\Models\RealokasiAlsintan::with(['alsintan.jenisAlat', 'kelompokTaniAsal.desa.kecamatan', 'kelompokTaniTujuan.desa.kecamatan'])
                ->whereYear('tanggal_realokasi', $tahun);
            if ($kecamatanId) {
                $q->where(function($sub) use ($kecamatanId) {
                    $sub->whereHas('kelompokTaniAsal.desa.kecamatan', fn($q2) => $q2->where('id', $kecamatanId))
                       ->orWhereHas('kelompokTaniTujuan.desa.kecamatan', fn($q2) => $q2->where('id', $kecamatanId));
                });
            }
            $realokasiAlsintans = $q->get();

        } elseif ($tab === 'realokasi-pupuk') {
            $q = \App\Models\PengalihanPupuk::with(['jenis', 'kecamatanAsal', 'kecamatanTujuan'])
                ->where('tahun', $tahun);
            if ($kecamatanId) {
                $q->where(function($sub) use ($kecamatanId) {
                    $sub->where('kecamatan_asal_id', $kecamatanId)
                       ->orWhere('kecamatan_tujuan_id', $kecamatanId);
                });
            }
            $realokasiPupuks = $q->get();

        } elseif ($tab === 'pupuk') {
            $jenisPupuks   = JenisPupuk::orderBy('nama')->get();
            $kecamatanList = $kecamatanId
                ? Kecamatan::where('id', $kecamatanId)->get()
                : Kecamatan::orderBy('nama')->get();

            $kuotas = KuotaTahunanPupuk::where('tahun', $tahun)
                ->when($kecamatanId, fn($q) => $q->where('kecamatan_id', $kecamatanId))
                ->get()->groupBy(fn($k) => $k->kecamatan_id . '|' . $k->jenis_pupuk_id);

            $realizations = LaporanPupukDetail::with(['laporan'])
                ->whereHas('laporan', fn($q) => $q->where('tahun', $tahun))
                ->when($kecamatanId, fn($q) => $q->where('kecamatan_id', $kecamatanId))
                ->get()->groupBy(fn($d) => $d->kecamatan_id . '|' . $d->jenis_pupuk_id);

            $rows = [];
            foreach ($kecamatanList as $kec) {
                foreach ($jenisPupuks as $jenis) {
                    $key   = $kec->id . '|' . $jenis->id;
                    $kuota = $kuotas->get($key)?->first()?->jumlah ?? 0;
                    $real  = $realizations->get($key)?->sum('penebusan') ?? 0;
                    $rows[] = [
                        'kecamatan' => $kec,
                        'jenis'     => $jenis,
                        'kuota'     => $kuota,
                        'realisasi' => $real,
                        'selisih'   => $kuota - $real,
                    ];
                }
            }
            $pupukData = collect($rows);
        }

        return [$alsintans, $infrastrukturs, $pupukData, $jenisPupuks, $pemanfaatanLaporans, $infrastrukturLaporans, $realokasiAlsintans, $realokasiPupuks];
    }

    // ─── PENYULUHAN ───────────────────────────────────────────────────────────
    public function penyuluhan(Request $request)
    {
        $tab         = $request->get('tab', 'penyuluh');
        $kecamatanId = $request->get('kecamatan_id');
        $kecamatans  = Kecamatan::orderBy('nama')->get();

        [$penyuluhs, $gapoktans, $kelompokTanis, $petanis, $bpps] = $this->buildPenyuluhanData($tab, $kecamatanId, $request);

        return view('laporan-bps.penyuluhan', compact(
            'tab', 'kecamatans', 'kecamatanId',
            'penyuluhs', 'gapoktans', 'kelompokTanis', 'petanis', 'bpps'
        ));
    }

    public function penyuluhanPdf(Request $request)
    {
        $tab         = $request->get('tab', 'penyuluh');
        $kecamatanId = $request->get('kecamatan_id');

        [$penyuluhs, $gapoktans, $kelompokTanis, $petanis, $bpps] = $this->buildPenyuluhanData($tab, $kecamatanId);

        $pdf = Pdf::loadView("laporan-bps.pdf.penyuluhan-{$tab}", compact(
            'penyuluhs', 'gapoktans', 'kelompokTanis', 'petanis', 'bpps'
        ))->setPaper('a4', 'landscape');

        return $pdf->stream("laporan-bps-penyuluhan-{$tab}.pdf");
    }

    public function penyuluhanExcel(Request $request)
    {
        $tab         = $request->get('tab', 'penyuluh');
        $kecamatanId = $request->get('kecamatan_id');

        [$penyuluhs, $gapoktans, $kelompokTanis, $petanis, $bpps] = $this->buildPenyuluhanData($tab, $kecamatanId);
        $data = match ($tab) {
            'penyuluh'    => $penyuluhs,
            'gapoktan'    => $gapoktans,
            'kelompoktani'=> $kelompokTanis,
            'petani'      => $petanis,
            'bpp'         => $bpps,
            default       => collect(),
        };

        return Excel::download(
            new \App\Exports\LaporanBpsExport("penyuluhan-{$tab}", $data, intval(date('Y'))),
            "laporan-bps-penyuluhan-{$tab}.xlsx"
        );
    }

    private function buildPenyuluhanData(string $tab, ?int $kecamatanId, ?Request $request = null): array
    {
        $penyuluhs = $gapoktans = $kelompokTanis = $petanis = $bpps = collect();

        if ($tab === 'penyuluh') {
            $q = Penyuluh::with('bpp.kecamatan');
            if ($kecamatanId) $q->whereHas('bpp.kecamatan', fn($q2) => $q2->where('id', $kecamatanId));
            $penyuluhs = $q->orderBy('nama')->get();

        } elseif ($tab === 'gapoktan') {
            $q = Gapoktan::with('kecamatan');
            if ($kecamatanId) $q->where('kecamatan_id', $kecamatanId);
            $gapoktans = $q->withCount('kelompokTanis')->orderBy('nama')->get();

        } elseif ($tab === 'kelompoktani') {
            $q = KelompokTani::with(['desa.kecamatan', 'gapoktan'])->withCount('petanis');
            if ($kecamatanId) $q->whereHas('desa.kecamatan', fn($q2) => $q2->where('id', $kecamatanId));
            $kelompokTanis = $q->orderBy('nama')->get();

        } elseif ($tab === 'petani') {
            $q = Petani::with(['kelompokTani.desa.kecamatan', 'kelompokTani.gapoktan']);
            if ($kecamatanId) $q->whereHas('kelompokTani.desa.kecamatan', fn($q2) => $q2->where('id', $kecamatanId));
            $petanis = $q->orderBy('nama')->paginate(50)->withQueryString();

        } elseif ($tab === 'bpp') {
            $q = Bpp::with('kecamatan')->withCount(['kecamatan']);
            if ($kecamatanId) $q->where('kecamatan_id', $kecamatanId);
            $bpps = $q->orderBy('nama')->get();
        }

        return [$penyuluhs, $gapoktans, $kelompokTanis, $petanis, $bpps];
    }
}

