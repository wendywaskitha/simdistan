<?php

namespace App\Http\Controllers;

use App\Models\Kecamatan;
use App\Models\JenisPupuk;
use App\Models\TokoPupuk;
use App\Models\LaporanPupuk;
use App\Models\LaporanPupukDetail;
use App\Models\PengalihanPupuk;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DistribusiPupukController extends Controller
{
    /**
     * Display the main dashboard for fertilizer distribution.
     */
    public function index(): View
    {
        $years = [2022, 2023, 2024, 2025, 2026];
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $jenisPupuks = JenisPupuk::all();
        $kecamatans = Kecamatan::all();

        return view('produksi.distribusi-pupuk.index', compact('years', 'months', 'jenisPupuks', 'kecamatans'));
    }

    /**
     * Get summary matrix of distribution per kecamatan.
     */
    public function ajaxLaporanData(Request $request): \Illuminate\Http\JsonResponse
    {
        $tahun = $request->get('tahun', date('Y'));
        $bulan = $request->get('bulan', date('n'));
        $jenisPupukId = $request->get('jenis_pupuk_id');

        if (!$jenisPupukId) {
            $firstJenis = JenisPupuk::first();
            $jenisPupukId = $firstJenis ? $firstJenis->id : 0;
        }

        $kecamatans = Kecamatan::all();

        // Fetch annual quotas
        $annualQuotas = \App\Models\KuotaTahunanPupuk::where('tahun', $tahun)
            ->where('jenis_pupuk_id', $jenisPupukId)
            ->get();

        // Find the latest month with report inputs for this year & fertilizer type
        $latestReport = LaporanPupuk::where('tahun', $tahun)
            ->whereHas('details', function($q) use ($jenisPupukId) {
                $q->where('jenis_pupuk_id', $jenisPupukId)->where('penebusan', '>', 0);
            })
            ->orderBy('bulan', 'desc')
            ->first();
        
        $latestBulan = $latestReport ? $latestReport->bulan : $bulan;

        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $latestBulanNama = $monthNames[$latestBulan] ?? $monthNames[date('n')];

        // Fetch reports details for selected month
        $detailsBulanIni = LaporanPupukDetail::with(['laporan.satuan'])->whereHas('laporan', function($q) use ($tahun, $bulan) {
            $q->where('tahun', $tahun)->where('bulan', $bulan);
        })->where('jenis_pupuk_id', $jenisPupukId)->get();

        // Fetch cumulative reports details (selected month)
        $detailsKumulatif = LaporanPupukDetail::whereHas('laporan', function($q) use ($tahun, $bulan) {
            $q->where('tahun', $tahun)->where('bulan', '<=', $bulan);
        })->where('jenis_pupuk_id', $jenisPupukId)->get();

        // Fetch reports details for the entire year
        $detailsTahunan = LaporanPupukDetail::whereHas('laporan', function($q) use ($tahun) {
            $q->where('tahun', $tahun);
        })->where('jenis_pupuk_id', $jenisPupukId)->get();

        // Fetch reports details for the LATEST month
        $detailsBulanLatest = LaporanPupukDetail::whereHas('laporan', function($q) use ($tahun, $latestBulan) {
            $q->where('tahun', $tahun)->where('bulan', $latestBulan);
        })->where('jenis_pupuk_id', $jenisPupukId)->get();

        // Fetch cumulative reports details s.d. LATEST month
        $detailsKumulatifLatest = LaporanPupukDetail::whereHas('laporan', function($q) use ($tahun, $latestBulan) {
            $q->where('tahun', $tahun)->where('bulan', '<=', $latestBulan);
        })->where('jenis_pupuk_id', $jenisPupukId)->get();

        // Fetch reallocations for selected month
        $pengalihansBulanIni = PengalihanPupuk::where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->where('jenis_pupuk_id', $jenisPupukId)
            ->get();

        // Fetch cumulative reallocations s.d. selected month
        $pengalihansKumulatif = PengalihanPupuk::where('tahun', $tahun)
            ->where('bulan', '<=', $bulan)
            ->where('jenis_pupuk_id', $jenisPupukId)
            ->get();

        // Fetch cumulative reallocations s.d. LATEST month
        $pengalihansKumulatifLatest = PengalihanPupuk::where('tahun', $tahun)
            ->where('bulan', '<=', $latestBulan)
            ->where('jenis_pupuk_id', $jenisPupukId)
            ->get();

        // Fetch annual reallocations (entire year)
        $pengalihansTahunan = PengalihanPupuk::where('tahun', $tahun)
            ->where('jenis_pupuk_id', $jenisPupukId)
            ->get();

        $firstDetail = $detailsBulanIni->first();
        $satuanNama = ($firstDetail && $firstDetail->laporan && $firstDetail->laporan->satuan) 
            ? $firstDetail->laporan->satuan->nama 
            : 'Kg';

        $data = [];
        foreach ($kecamatans as $kecamatan) {
            $kuotaAwal = $annualQuotas->where('kecamatan_id', $kecamatan->id)->sum('jumlah');
            
            $penebusanBulanIni = $detailsBulanIni->where('kecamatan_id', $kecamatan->id)->sum('penebusan');
            $penebusanKumulatif = $detailsKumulatif->where('kecamatan_id', $kecamatan->id)->sum('penebusan');
            $penebusanTahunan = $detailsTahunan->where('kecamatan_id', $kecamatan->id)->sum('penebusan');

            $penebusanKumulatifLatest = $detailsKumulatifLatest->where('kecamatan_id', $kecamatan->id)->sum('penebusan');

            $masukBulanIni = $pengalihansBulanIni->where('kecamatan_tujuan_id', $kecamatan->id)->sum('jumlah');
            $keluarBulanIni = $pengalihansBulanIni->where('kecamatan_asal_id', $kecamatan->id)->sum('jumlah');

            $masukKumulatif = $pengalihansKumulatif->where('kecamatan_tujuan_id', $kecamatan->id)->sum('jumlah');
            $keluarKumulatif = $pengalihansKumulatif->where('kecamatan_asal_id', $kecamatan->id)->sum('jumlah');

            $masukKumulatifLatest = $pengalihansKumulatifLatest->where('kecamatan_tujuan_id', $kecamatan->id)->sum('jumlah');
            $keluarKumulatifLatest = $pengalihansKumulatifLatest->where('kecamatan_asal_id', $kecamatan->id)->sum('jumlah');

            $masukTahunan = $pengalihansTahunan->where('kecamatan_tujuan_id', $kecamatan->id)->sum('jumlah');
            $keluarTahunan = $pengalihansTahunan->where('kecamatan_asal_id', $kecamatan->id)->sum('jumlah');

            $kuotaBerjalan = ($bulan / 12) * $kuotaAwal;
            $kuotaBerjalanLatest = ($latestBulan / 12) * $kuotaAwal;

            $sisa = ($kuotaAwal - $penebusanKumulatif) + $masukKumulatif - $keluarKumulatif;
            $sisaLatest = ($kuotaAwal - $penebusanKumulatifLatest) + $masukKumulatifLatest - $keluarKumulatifLatest;
            $sisaTahunan = ($kuotaAwal - $penebusanTahunan) + $masukTahunan - $keluarTahunan;
            
            $persentase = $kuotaAwal > 0 ? ($penebusanKumulatif / $kuotaAwal) * 100 : 0;
            $persentaseLatest = $kuotaAwal > 0 ? ($penebusanKumulatifLatest / $kuotaAwal) * 100 : 0;

            $data[] = [
                'id' => $kecamatan->id,
                'nama' => $kecamatan->nama,
                'kuota' => doubleval($kuotaAwal),
                'penebusan' => doubleval($penebusanBulanIni),
                'masuk' => doubleval($masukBulanIni),
                'keluar' => doubleval($keluarBulanIni),
                'sisa' => doubleval($sisa),
                'persentase' => round($persentase, 2),
                'penebusan_kumulatif' => doubleval($penebusanTahunan),
                'masuk_kumulatif' => doubleval($masukTahunan),
                'keluar_kumulatif' => doubleval($keluarTahunan),
                'sisa_tahunan' => doubleval($sisaTahunan),
                'persentase_kumulatif' => $kuotaAwal > 0 ? round(($penebusanTahunan / $kuotaAwal) * 100, 2) : 0,
                // Latest inputs metrics for recommendations
                'sisa_latest' => doubleval($sisaLatest),
                'persentase_latest' => round($persentaseLatest, 2)
            ];
        }

        $threshold = 75 * ($bulan / 12);
        $thresholdLatest = 75 * ($latestBulan / 12);

        return response()->json([
            'data' => $data,
            'satuan_nama' => $satuanNama,
            'latest_bulan_nama' => $latestBulanNama,
            'threshold' => round($threshold, 2),
            'threshold_latest' => round($thresholdLatest, 2)
        ]);
    }

    /**
     * Show form to input monthly distributor report.
     */
    public function inputBulanan(): View
    {
        $years = [2022, 2023, 2024, 2025, 2026];
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $tokos = TokoPupuk::all();
        $jenisPupuks = JenisPupuk::all();
        $satuans = \App\Models\Satuan::all()->pluck('nama', 'id')->toArray();

        return view('produksi.distribusi-pupuk.input_bulanan', compact('years', 'months', 'tokos', 'jenisPupuks', 'satuans'));
    }

    /**
     * AJAX endpoint to fetch kecamatan served by a toko and any existing report data.
     */
    public function ajaxTokoKecamatan(Request $request): \Illuminate\Http\JsonResponse
    {
        $tokoId = $request->get('toko_pupuk_id');
        $tahun = $request->get('tahun');
        $bulan = $request->get('bulan');

        $toko = TokoPupuk::with('kecamatans')->find($tokoId);
        if (!$toko) {
            return response()->json(['kecamatans' => []]);
        }

        $laporan = LaporanPupuk::where('toko_pupuk_id', $tokoId)
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->first();

        $existingDetails = [];
        if ($laporan) {
            $details = LaporanPupukDetail::where('laporan_pupuk_id', $laporan->id)->get();
            foreach ($details as $d) {
                $existingDetails[$d->kecamatan_id][$d->jenis_pupuk_id] = [
                    'penebusan' => $d->penebusan
                ];
            }
        }

        $kecamatans = [];
        foreach ($toko->kecamatans as $kec) {
            $kecamatans[] = [
                'id' => $kec->id,
                'nama' => $kec->nama
            ];
        }

        return response()->json([
            'kecamatans' => $kecamatans,
            'existing_details' => $existingDetails,
            'satuan_id' => $laporan ? $laporan->satuan_id : ''
        ]);
    }

    /**
     * Store monthly report.
     */
    public function simpanBulanan(Request $request): RedirectResponse
    {
        $request->validate([
            'toko_pupuk_id' => ['required', 'exists:toko_pupuks,id'],
            'satuan_id' => ['required', 'exists:satuans,id'],
            'bulan' => ['required', 'integer', 'between:1,12'],
            'tahun' => ['required', 'integer', 'between:2020,2030'],
            'data' => ['required', 'array']
        ]);

        $tokoId = $request->toko_pupuk_id;
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        DB::transaction(function() use ($request, $tokoId, $bulan, $tahun) {
            $laporan = LaporanPupuk::updateOrCreate(
                [
                    'toko_pupuk_id' => $tokoId,
                    'bulan' => $bulan,
                    'tahun' => $tahun
                ],
                [
                    'satuan_id' => $request->satuan_id
                ]
            );

            // Clear old details
            $laporan->details()->delete();

            foreach ($request->data as $kecId => $jenisData) {
                foreach ($jenisData as $jenisId => $values) {
                    $penebusan = floatval($values['penebusan'] ?? 0);

                    if ($penebusan > 0) {
                        $laporan->details()->create([
                            'kecamatan_id' => $kecId,
                            'jenis_pupuk_id' => $jenisId,
                            'penebusan' => $penebusan
                        ]);
                    }
                }
            }
        });

        return redirect()->route('distribusi-pupuk.index')
            ->with('success', 'Laporan distribusi pupuk bulanan berhasil disimpan.');
    }

    /**
     * List all reallocations.
     */
    public function pengalihanList(): View
    {
        $pengalihans = PengalihanPupuk::with(['jenis', 'kecamatanAsal', 'kecamatanTujuan'])
            ->orderBy('id', 'desc')->get();
        return view('produksi.distribusi-pupuk.pengalihan', compact('pengalihans'));
    }

    /**
     * Show form to add reallocation.
     */
    public function pengalihanTambah(): View
    {
        $years = [2022, 2023, 2024, 2025, 2026];
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $jenisPupuks = JenisPupuk::all();

        return view('produksi.distribusi-pupuk.pengalihan_tambah', compact('years', 'months', 'jenisPupuks'));
    }

    /**
     * AJAX endpoint to load source and target subdistricts based on the 75% rule.
     */
    public function ajaxKecamatanPengalihan(Request $request): \Illuminate\Http\JsonResponse
    {
        $tahun = $request->get('tahun');
        $bulan = $request->get('bulan');
        $jenisId = $request->get('jenis_pupuk_id');

        $kecamatans = Kecamatan::all();

        // Fetch annual quotas
        $annualQuotas = \App\Models\KuotaTahunanPupuk::where('tahun', $tahun)
            ->where('jenis_pupuk_id', $jenisId)
            ->get();

        $detailsBulanIni = LaporanPupukDetail::whereHas('laporan', function($q) use ($tahun, $bulan) {
            $q->where('tahun', $tahun)->where('bulan', $bulan);
        })->where('jenis_pupuk_id', $jenisId)->get();

        $detailsKumulatif = LaporanPupukDetail::whereHas('laporan', function($q) use ($tahun, $bulan) {
            $q->where('tahun', $tahun)->where('bulan', '<=', $bulan);
        })->where('jenis_pupuk_id', $jenisId)->get();

        $pengalihansBulanIni = PengalihanPupuk::where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->where('jenis_pupuk_id', $jenisId)
            ->get();

        $pengalihansKumulatif = PengalihanPupuk::where('tahun', $tahun)
            ->where('bulan', '<=', $bulan)
            ->where('jenis_pupuk_id', $jenisId)
            ->get();

        $asal = [];
        $tujuan = [];

        foreach ($kecamatans as $kec) {
            $kuotaAwal = $annualQuotas->where('kecamatan_id', $kec->id)->sum('jumlah');
            $kuotaBerjalan = ($bulan / 12) * $kuotaAwal;

            $penebusanBulanIni = $detailsBulanIni->where('kecamatan_id', $kec->id)->sum('penebusan');
            $penebusanKumulatif = $detailsKumulatif->where('kecamatan_id', $kec->id)->sum('penebusan');

            $masukBulanIni = $pengalihansBulanIni->where('kecamatan_tujuan_id', $kec->id)->sum('jumlah');
            $keluarBulanIni = $pengalihansBulanIni->where('kecamatan_asal_id', $kec->id)->sum('jumlah');

            $masukKumulatif = $pengalihansKumulatif->where('kecamatan_tujuan_id', $kec->id)->sum('jumlah');
            $keluarKumulatif = $pengalihansKumulatif->where('kecamatan_asal_id', $kec->id)->sum('jumlah');

            $sisaTersedia = ($kuotaAwal - $penebusanKumulatif) - $keluarKumulatif + $masukKumulatif; 
            $persentase = $kuotaAwal > 0 ? ($penebusanKumulatif / $kuotaAwal) * 100 : 0;
            $threshold = 75 * ($bulan / 12);

            if ($kuotaAwal > 0) {
                if ($persentase < $threshold && $sisaTersedia > 0) {
                    $asal[] = [
                        'id' => $kec->id,
                        'nama' => $kec->nama,
                        'sisa' => doubleval($sisaTersedia),
                        'persentase' => round($persentase, 2)
                    ];
                }

                if ($persentase >= $threshold) {
                    $tujuan[] = [
                        'id' => $kec->id,
                        'nama' => $kec->nama,
                        'persentase' => round($persentase, 2)
                    ];
                }
            }
        }

        return response()->json([
            'asal' => $asal,
            'tujuan' => $tujuan
        ]);
    }

    /**
     * Save quota reallocation.
     */
    public function pengalihanSimpan(Request $request): RedirectResponse
    {
        $request->validate([
            'tahun' => ['required', 'integer'],
            'bulan' => ['required', 'integer', 'between:1,12'],
            'jenis_pupuk_id' => ['required', 'exists:jenis_pupuks,id'],
            'kecamatan_asal_id' => ['required', 'exists:kecamatans,id', 'different:kecamatan_tujuan_id'],
            'kecamatan_tujuan_id' => ['required', 'exists:kecamatans,id'],
            'jumlah' => ['required', 'numeric', 'min:0.01'],
            'nama_sk' => ['required', 'string', 'max:255'],
            'bukti_sk' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'keterangan' => ['nullable', 'string']
        ]);

        $tahun = $request->tahun;
        $bulan = $request->bulan;
        $jenisId = $request->jenis_pupuk_id;
        $asalId = $request->kecamatan_asal_id;
        $jumlah = floatval($request->jumlah);

        // Perform server side validation of sisa kuota and 75% rule
        $annualQuotas = \App\Models\KuotaTahunanPupuk::where('tahun', $tahun)
            ->where('jenis_pupuk_id', $jenisId)
            ->get();

        $detailsBulanIni = LaporanPupukDetail::whereHas('laporan', function($q) use ($tahun, $bulan) {
            $q->where('tahun', $tahun)->where('bulan', $bulan);
        })->where('jenis_pupuk_id', $jenisId)->get();

        $detailsKumulatif = LaporanPupukDetail::whereHas('laporan', function($q) use ($tahun, $bulan) {
            $q->where('tahun', $tahun)->where('bulan', '<=', $bulan);
        })->where('jenis_pupuk_id', $jenisId)->get();

        $pengalihansKumulatif = PengalihanPupuk::where('tahun', $tahun)
            ->where('bulan', '<=', $bulan)
            ->where('jenis_pupuk_id', $jenisId)
            ->get();

        $kuotaAwal = $annualQuotas->where('kecamatan_id', $asalId)->sum('jumlah');
        $threshold = 75 * ($bulan / 12);

        $penebusanBulanIni = $detailsBulanIni->where('kecamatan_id', $asalId)->sum('penebusan');
        $penebusanKumulatif = $detailsKumulatif->where('kecamatan_id', $asalId)->sum('penebusan');
        
        $keluarKumulatif = $pengalihansKumulatif->where('kecamatan_asal_id', $asalId)->sum('jumlah');
        $masukKumulatif = $pengalihansKumulatif->where('kecamatan_tujuan_id', $asalId)->sum('jumlah');

        $sisaTersedia = ($kuotaAwal - $penebusanKumulatif) - $keluarKumulatif + $masukKumulatif;
        $persentase = $kuotaAwal > 0 ? ($penebusanKumulatif / $kuotaAwal) * 100 : 0;

        if ($kuotaAwal <= 0 || $persentase >= $threshold) {
            return back()->withErrors(['kecamatan_asal_id' => 'Kecamatan asal harus memiliki tingkat penebusan di bawah 75% terhadap target berjalan'])->withInput();
        }

        if ($jumlah > $sisaTersedia) {
            return back()->withErrors(['jumlah' => 'Jumlah pengalihan melebihi sisa kuota tersedia (' . number_format($sisaTersedia, 2) . ' Kg)'])->withInput();
        }

        $filePath = null;
        if ($request->hasFile('bukti_sk')) {
            $file = $request->file('bukti_sk');
            $fileName = 'sk_relokasi_' . time() . '_' . rand(100, 999) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('uploads/sk_relokasi', $fileName, 'public');
        }

        $data = $request->all();
        $data['file_path'] = $filePath;

        PengalihanPupuk::create($data);

        return redirect()->route('distribusi-pupuk.pengalihan.index')
            ->with('success', 'Kuota pupuk berhasil dialihkan.');
    }

    /**
     * Get chart data for 5 years trends.
     */
    public function dataGrafik(Request $request): \Illuminate\Http\JsonResponse
    {
        $kecamatanId = $request->get('kecamatan_id');
        $jenisId = $request->get('jenis_pupuk_id');
        $tahunBulanan = intval($request->get('tahun_bulanan', date('Y')));

        $years = [2022, 2023, 2024, 2025, 2026];
        $kuotaTahun = [];
        $penebusanTahun = [];

        foreach ($years as $th) {
            $kuotaT = \App\Models\KuotaTahunanPupuk::where('tahun', $th);
            if ($kecamatanId) {
                $kuotaT->where('kecamatan_id', $kecamatanId);
            }
            if ($jenisId) {
                $kuotaT->where('jenis_pupuk_id', $jenisId);
            }
            $kuotaTahun[] = doubleval($kuotaT->sum('jumlah'));

            $query = LaporanPupukDetail::whereHas('laporan', function($q) use ($th) {
                $q->where('tahun', $th);
            });

            if ($kecamatanId) {
                $query->where('kecamatan_id', $kecamatanId);
            }
            if ($jenisId) {
                $query->where('jenis_pupuk_id', $jenisId);
            }

            $penebusanTahun[] = doubleval($query->sum('penebusan'));
        }

        // Monthly trends
        $monthNames = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agt", "Sep", "Okt", "Nov", "Des"];
        $kuotaBulan = [];
        $penebusanBulan = [];

        for ($m = 1; $m <= 12; $m++) {
            $kuotaT = \App\Models\KuotaTahunanPupuk::where('tahun', $tahunBulanan);
            if ($kecamatanId) {
                $kuotaT->where('kecamatan_id', $kecamatanId);
            }
            if ($jenisId) {
                $kuotaT->where('jenis_pupuk_id', $jenisId);
            }
            $kuotaBulan[] = doubleval($kuotaT->sum('jumlah'));

            $query = LaporanPupukDetail::whereHas('laporan', function($q) use ($tahunBulanan, $m) {
                $q->where('tahun', $tahunBulanan)->where('bulan', $m);
            });

            if ($kecamatanId) {
                $query->where('kecamatan_id', $kecamatanId);
            }
            if ($jenisId) {
                $query->where('jenis_pupuk_id', $jenisId);
            }

            $penebusanBulan[] = doubleval($query->sum('penebusan'));
        }

        $firstDetail = LaporanPupukDetail::with(['laporan.satuan'])->first();
        $satuanNama = ($firstDetail && $firstDetail->laporan && $firstDetail->laporan->satuan) 
            ? $firstDetail->laporan->satuan->nama 
            : 'Kg';

        return response()->json([
            'years' => $years,
            'kuota_tahunan' => $kuotaTahun,
            'penebusan_tahunan' => $penebusanTahun,
            'months' => $monthNames,
            'kuota_bulanan' => $kuotaBulan,
            'penebusan_bulanan' => $penebusanBulan,
            'satuan_nama' => $satuanNama,
        ]);
    }
}
