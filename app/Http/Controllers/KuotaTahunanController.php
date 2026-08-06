<?php

namespace App\Http\Controllers;

use App\Services\KuotaTahunanService;
use App\Services\KecamatanService;
use App\Models\JenisPupuk;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class KuotaTahunanController extends Controller
{
    protected $kuotaService;
    protected $kecamatanService;

    public function __construct(KuotaTahunanService $kuotaService, KecamatanService $kecamatanService)
    {
        $this->kuotaService = $kuotaService;
        $this->kecamatanService = $kecamatanService;
    }

    /**
     * Show the page to manage annual quota settings.
     */
    public function index(Request $request): View
    {
        $years = [2022, 2023, 2024, 2025, 2026];
        $selectedYear = intval($request->get('tahun', date('Y')));

        $kecamatans = $this->kecamatanService->getAllKecamatan();
        $jenisPupuks = JenisPupuk::all();

        // Fetch existing quota allocations
        $quotas = $this->kuotaService->getKuotaByTahun($selectedYear);
        
        $mappedQuotas = [];
        foreach ($quotas as $q) {
            $mappedQuotas[$q->kecamatan_id][$q->jenis_pupuk_id] = $q->jumlah;
        }

        $dokumen = \App\Models\DokumenAlokasiTahunan::where('tahun', $selectedYear)->first();

        return view('master.kuota-tahunan.index', compact('years', 'selectedYear', 'kecamatans', 'jenisPupuks', 'mappedQuotas', 'dokumen'));
    }

    /**
     * AJAX endpoint to fetch annual quotas for a specific year.
     */
    public function ajaxKuotaData(Request $request): \Illuminate\Http\JsonResponse
    {
        $tahun = intval($request->get('tahun', date('Y')));
        $quotas = $this->kuotaService->getKuotaByTahun($tahun);

        $mapped = [];
        foreach ($quotas as $q) {
            $mapped[$q->kecamatan_id][$q->jenis_pupuk_id] = doubleval($q->jumlah);
        }

        $dokumen = \App\Models\DokumenAlokasiTahunan::where('tahun', $tahun)->first();
        $fileUrl = $dokumen ? asset('storage/' . $dokumen->file_path) : null;
        $fileName = $dokumen ? basename($dokumen->file_path) : null;

        return response()->json([
            'quotas' => $mapped,
            'file_url' => $fileUrl,
            'file_name' => $fileName
        ]);
    }

    /**
     * Store/update the annual quota settings.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'tahun' => ['required', 'integer', 'between:2020,2030'],
            'data' => ['required', 'array'],
            'bukti_sk' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048']
        ]);

        $this->kuotaService->simpanKuota(intval($request->tahun), $request->data);

        if ($request->hasFile('bukti_sk')) {
            $file = $request->file('bukti_sk');
            $fileName = 'sk_alokasi_' . $request->tahun . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads/sk_alokasi', $fileName, 'public');

            \App\Models\DokumenAlokasiTahunan::updateOrCreate(
                ['tahun' => intval($request->tahun)],
                ['file_path' => $path]
            );
        }

        return redirect()->route('kuota-tahunan.index', ['tahun' => $request->tahun])
            ->with('success', 'Konfigurasi Kuota Tahunan dan SK Alokasi berhasil disimpan.');
    }
}
