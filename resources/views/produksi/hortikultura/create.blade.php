@extends('layouts.admin')

@section('title', 'Tambah Laporan Produksi - ' . $kategori->nama)

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Laporan Produksi', 'url' => route('hortikultura.index')],
    ['label' => 'Tambah Laporan ' . $kategori->nama]
]" />

{{-- Alert Error --}}
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4 shadow-sm" role="alert">
        <h6 class="fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i>Gagal Menyimpan Data:</h6>
        <ul class="mb-0 ps-3 small">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card custom-card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
    {{-- Header Banner --}}
    <div id="formBanner" class="px-4 py-3 d-flex align-items-center justify-content-between"
         style="background: linear-gradient(135deg, #1d4ed8, #3b82f6); transition: background 0.4s ease;">
        <div>
            <h5 class="fw-bold text-white mb-0">
                <i class="bi bi-clipboard2-data me-2"></i>
                Form Laporan Produksi Hortikultura
            </h5>
            <p class="text-white-50 small mb-0" id="bannerSubtitle">Pilih jenis form untuk melanjutkan pengisian data</p>
        </div>
        <span class="badge fs-6 px-3 py-2 rounded-pill" id="formBadge"
              style="background: rgba(255,255,255,0.2); color: #fff; letter-spacing: 1px;">SPH-SBS</span>
    </div>

    <div class="p-4">
        <form action="{{ route('hortikultura.store') }}" method="POST" id="produksiForm">
            @csrf
            <input type="hidden" name="kategori_komoditas_id" value="{{ $kategori->id }}">

            {{-- ── FILTER UTAMA ──────────────────────────────────────────────────── --}}
            <div class="row g-3 mb-4 bg-light rounded-3 p-3 border border-light-subtle align-items-end">
                {{-- Kecamatan --}}
                <div class="col-md-3">
                    <label for="kecamatan_id" class="form-label fw-semibold text-secondary small">Wilayah Kecamatan <span class="text-danger">*</span></label>
                    <select name="kecamatan_id" id="kecamatan_id" class="form-select border-0 shadow-sm rounded-3" required>
                        <option value="">-- Pilih Kecamatan --</option>
                        @foreach($kecamatans as $id => $nama)
                            <option value="{{ $id }}" {{ old('kecamatan_id') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Jenis Form / Form Type --}}
                <div class="col-md-2">
                    <label for="form_type_select" class="form-label fw-semibold text-secondary small">Jenis Formulir <span class="text-danger">*</span></label>
                    <select id="form_type_select" class="form-select border-0 shadow-sm rounded-3">
                        <option value="SPH-SBS">SPH-SBS (Sayuran/Buah Semusim — Bulanan)</option>
                        <option value="SPH-BST">SPH-BST (Buah/Sayuran Tahunan — Triwulanan)</option>
                        <option value="SPH-TBF">SPH-TBF (Tanaman Biofarmaka — Triwulanan)</option>
                    </select>
                    {{-- hidden field yang benar-benar dikirim per baris --}}
                </div>

                {{-- Periode / Bulan --}}
                <div class="col-md-2">
                    <label for="bulan" class="form-label fw-semibold text-secondary small">Periode <span class="text-danger">*</span></label>
                    <select name="bulan" id="bulan" class="form-select border-0 shadow-sm rounded-3" required>
                        {{-- Diisi JS --}}
                    </select>
                </div>

                {{-- Tahun --}}
                <div class="col-md-2">
                    <label for="tahun" class="form-label fw-semibold text-secondary small">Tahun <span class="text-danger">*</span></label>
                    <input type="number" name="tahun" id="tahun" class="form-control border-0 shadow-sm rounded-3"
                           value="{{ old('tahun', date('Y')) }}" min="2020" max="2050" required>
                </div>

                {{-- Satuan --}}
                <div class="col-md-2">
                    <label for="satuan_id" class="form-label fw-semibold text-secondary small">Satuan <span class="text-danger">*</span></label>
                    <select name="satuan_id" id="satuan_id" class="form-select border-0 shadow-sm rounded-3" required>
                        <option value="">-- Pilih --</option>
                        @foreach($satuans as $id => $nama)
                            <option value="{{ $id }}" {{ old('satuan_id') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Tombol Muat Data --}}
                <div class="col-md-1">
                    <label class="form-label fw-semibold text-secondary small d-block opacity-0">.</label>
                    <button type="button" id="btnLoadPrev" class="btn btn-outline-primary w-100 rounded-3 shadow-sm" title="Muat data periode sebelumnya">
                        <i class="bi bi-cloud-download"></i>
                    </button>
                </div>
            </div>

            {{-- ── TABEL SPH-SBS ──────────────────────────────────────────────────── --}}
            <div id="table-SBS" class="sph-table d-none">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge bg-primary px-3 py-2 rounded-pill fs-6">SPH-SBS</span>
                    <div>
                        <h6 class="fw-bold mb-0">Laporan Tanaman Sayuran &amp; Buah-buahan Semusim</h6>
                        <small class="text-muted">Periode Bulanan — Isian dalam desimal 2 angka di belakang koma (Ha/Kwintal)</small>
                    </div>
                </div>
                <div class="table-responsive" style="max-height: 520px; overflow-y: auto;">
                    <table class="table table-bordered table-hover align-middle mb-0" style="min-width: 1100px;">
                        <thead class="text-center small fw-bold" style="position: sticky; top: 0; z-index: 2; background: #eff6ff;">
                            <tr>
                                <th rowspan="2" style="min-width:160px; vertical-align: middle;">Nama Tanaman</th>
                                <th rowspan="2" style="min-width:110px; vertical-align: middle; background:#fef9c3;">(3) Luas Tanam Akhir Bulan Lalu (Ha)</th>
                                <th colspan="2" style="min-width:200px;">Luas Panen (Ha)</th>
                                <th rowspan="2" style="min-width:110px; vertical-align: middle;">(6) Luas Rusak / Puso (Ha)</th>
                                <th rowspan="2" style="min-width:120px; vertical-align: middle;">(7) Penanaman Baru / Tambah Tanam (Ha)</th>
                                <th rowspan="2" style="min-width:120px; vertical-align: middle; background:#fef9c3;">(8) Luas Tanam Akhir = (3)-(4)-(5)-(6)+(7)</th>
                                <th colspan="2" style="min-width:200px;">Produksi (Kwintal)</th>
                                <th rowspan="2" style="min-width:120px; vertical-align: middle;">(11) Rata-rata Harga Jual (Rp/Kg)</th>
                            </tr>
                            <tr>
                                <th style="min-width:100px;">(4) Habis / Dibongkar</th>
                                <th style="min-width:100px;">(5) Belum Habis</th>
                                <th style="min-width:100px;">(9) Dipanen / Dibongkar</th>
                                <th style="min-width:100px;">(10) Belum Habis</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach(($komoditasList['SPH-SBS'] ?? collect()) as $kom)
                            <tr>
                                <td class="fw-semibold text-secondary small">
                                    <i class="bi bi-circle-fill me-1 text-primary" style="font-size:6px;"></i>{{ $kom->nama }}
                                    <input type="hidden" name="komoditas[{{ $kom->id }}][jenis_periode]" value="{{ $kom->jenis_periode }}">
                                    <input type="hidden" name="komoditas[{{ $kom->id }}][form_type]" value="SPH-SBS">
                                </td>
                                {{-- (3) Auto-fill --}}
                                <td class="p-1" style="background: #fef9c3;">
                                    <input type="number" step="0.01" min="0"
                                           name="komoditas[{{ $kom->id }}][luas_tanam_akhir_bulan_lalu]"
                                           class="form-control form-control-sm text-end border-0 sbs-col3 prev-fill-sbs"
                                           data-komoditas-id="{{ $kom->id }}"
                                           data-field="luas_tanam_akhir_bulan_lalu"
                                           placeholder="0.00" style="background:transparent;">
                                </td>
                                {{-- (4) Luas Panen Habis --}}
                                <td class="p-1">
                                    <input type="number" step="0.01" min="0"
                                           name="komoditas[{{ $kom->id }}][luas_panen]"
                                           class="form-control form-control-sm text-end sbs-panen sbs-calc" data-komoditas-id="{{ $kom->id }}"
                                           placeholder="0.00">
                                </td>
                                {{-- (5) Luas Panen Belum Habis --}}
                                <td class="p-1">
                                    <input type="number" step="0.01" min="0"
                                           name="komoditas[{{ $kom->id }}][luas_panen_belum_habis]"
                                           class="form-control form-control-sm text-end sbs-panen-bh sbs-calc" data-komoditas-id="{{ $kom->id }}"
                                           placeholder="0.00">
                                </td>
                                {{-- (6) Luas Rusak --}}
                                <td class="p-1">
                                    <input type="number" step="0.01" min="0"
                                           name="komoditas[{{ $kom->id }}][luas_rusak]"
                                           class="form-control form-control-sm text-end sbs-rusak sbs-calc" data-komoditas-id="{{ $kom->id }}"
                                           placeholder="0.00">
                                </td>
                                {{-- (7) Penanaman Baru --}}
                                <td class="p-1">
                                    <input type="number" step="0.01" min="0"
                                           name="komoditas[{{ $kom->id }}][luas_tanam]"
                                           class="form-control form-control-sm text-end sbs-tanam sbs-calc" data-komoditas-id="{{ $kom->id }}"
                                           placeholder="0.00">
                                </td>
                                {{-- (8) Auto-kalkulasi --}}
                                <td class="p-1" style="background: #fef9c3;">
                                    <input type="number" step="0.01"
                                           name="komoditas[{{ $kom->id }}][luas_tanam_akhir]"
                                           class="form-control form-control-sm text-end fw-bold border-0 sbs-akhir"
                                           data-komoditas-id="{{ $kom->id }}"
                                           placeholder="0.00" readonly style="background:transparent; color:#854d0e;">
                                </td>
                                {{-- (9) Produksi --}}
                                <td class="p-1">
                                    <input type="number" step="0.01" min="0"
                                           name="komoditas[{{ $kom->id }}][produksi]"
                                           class="form-control form-control-sm text-end"
                                           placeholder="0.00">
                                </td>
                                {{-- (10) Produksi Belum Habis --}}
                                <td class="p-1">
                                    <input type="number" step="0.01" min="0"
                                           name="komoditas[{{ $kom->id }}][produksi_belum_habis]"
                                           class="form-control form-control-sm text-end"
                                           placeholder="0.00">
                                </td>
                                {{-- (11) Harga Jual --}}
                                <td class="p-1">
                                    <input type="number" step="0.01" min="0"
                                           name="komoditas[{{ $kom->id }}][harga_jual]"
                                           class="form-control form-control-sm text-end"
                                           placeholder="0">
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ── TABEL SPH-BST ──────────────────────────────────────────────────── --}}
            <div id="table-BST" class="sph-table d-none">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge bg-success px-3 py-2 rounded-pill fs-6">SPH-BST</span>
                    <div>
                        <h6 class="fw-bold mb-0">Laporan Tanaman Buah-buahan &amp; Sayuran Tahunan</h6>
                        <small class="text-muted">Periode Triwulanan — Isian dalam bilangan bulat (Pohon/Rumpun)</small>
                    </div>
                </div>
                <div class="table-responsive" style="max-height: 520px; overflow-y: auto;">
                    <table class="table table-bordered table-hover align-middle mb-0" style="min-width: 1200px;">
                        <thead class="text-center small fw-bold" style="position: sticky; top: 0; z-index: 2; background: #f0fdf4;">
                            <tr>
                                <th rowspan="2" style="min-width:160px; vertical-align: middle;">Nama Tanaman</th>
                                <th rowspan="2" style="min-width:130px; vertical-align: middle; background:#fef9c3;">(3) Jml. Tanaman Akhir Triwulan Lalu (Pohon/Rumpun)</th>
                                <th rowspan="2" style="min-width:110px; vertical-align: middle;">(4) Tanaman Dibongkar / Ditebang</th>
                                <th rowspan="2" style="min-width:100px; vertical-align: middle;">(5) Penanaman Baru</th>
                                <th rowspan="2" style="min-width:130px; vertical-align: middle; background:#fef9c3;">(6) Jml. Tanaman Akhir Triwulan = (3)-(4)+(5)</th>
                                <th colspan="3" style="min-width:330px;">Di Akhir Triwulan (Pohon/Rumpun)</th>
                                <th rowspan="2" style="min-width:110px; vertical-align: middle;">(10) Produksi (Kwintal)</th>
                                <th rowspan="2" style="min-width:120px; vertical-align: middle;">(11) Rata-rata Harga Jual (Rp/Kg)</th>
                            </tr>
                            <tr>
                                <th style="min-width:110px;">(7) Blm Menghasilkan</th>
                                <th style="min-width:110px;">(8) Menghasilkan</th>
                                <th style="min-width:110px;">(9) Tua / Rusak</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach(($komoditasList['SPH-BST'] ?? collect()) as $kom)
                            <tr>
                                <td class="fw-semibold text-secondary small">
                                    <i class="bi bi-circle-fill me-1 text-success" style="font-size:6px;"></i>{{ $kom->nama }}
                                    <input type="hidden" name="komoditas[{{ $kom->id }}][jenis_periode]" value="{{ $kom->jenis_periode }}">
                                    <input type="hidden" name="komoditas[{{ $kom->id }}][form_type]" value="SPH-BST">
                                </td>
                                {{-- (3) Auto-fill Triwulan Lalu --}}
                                <td class="p-1" style="background:#fef9c3;">
                                    <input type="number" min="0"
                                           name="komoditas[{{ $kom->id }}][jumlah_tanaman_akhir_triwulan_lalu]"
                                           class="form-control form-control-sm text-end border-0 bst-col3 prev-fill-bst"
                                           data-komoditas-id="{{ $kom->id }}"
                                           data-field="jumlah_tanaman_akhir_triwulan_lalu"
                                           placeholder="0" style="background:transparent;">
                                </td>
                                {{-- (4) Dibongkar --}}
                                <td class="p-1">
                                    <input type="number" min="0"
                                           name="komoditas[{{ $kom->id }}][tanaman_dibongkar]"
                                           class="form-control form-control-sm text-end bst-dibongkar bst-calc" data-komoditas-id="{{ $kom->id }}"
                                           placeholder="0">
                                </td>
                                {{-- (5) Tanam Baru --}}
                                <td class="p-1">
                                    <input type="number" min="0"
                                           name="komoditas[{{ $kom->id }}][tanaman_baru]"
                                           class="form-control form-control-sm text-end bst-baru bst-calc" data-komoditas-id="{{ $kom->id }}"
                                           placeholder="0">
                                </td>
                                {{-- (6) Auto-kalkulasi --}}
                                <td class="p-1" style="background:#fef9c3;">
                                    <input type="number"
                                           name="komoditas[{{ $kom->id }}][jumlah_tanaman_menghasilkan]"
                                           class="form-control form-control-sm text-end fw-bold border-0 bst-akhir"
                                           data-komoditas-id="{{ $kom->id }}"
                                           placeholder="0" readonly style="background:transparent; color:#14532d;">
                                </td>
                                {{-- (7) Belum Menghasilkan --}}
                                <td class="p-1">
                                    <input type="number" min="0"
                                           name="komoditas[{{ $kom->id }}][tanaman_tidak_menghasilkan]"
                                           class="form-control form-control-sm text-end"
                                           placeholder="0">
                                </td>
                                {{-- (8) Menghasilkan (luas_tanam direpurpose) --}}
                                <td class="p-1">
                                    <input type="number" min="0"
                                           name="komoditas[{{ $kom->id }}][luas_tanam]"
                                           class="form-control form-control-sm text-end"
                                           placeholder="0">
                                </td>
                                {{-- (9) Tua/Rusak --}}
                                <td class="p-1">
                                    <input type="number" min="0"
                                           name="komoditas[{{ $kom->id }}][tanaman_tus_rusak]"
                                           class="form-control form-control-sm text-end"
                                           placeholder="0">
                                </td>
                                {{-- (10) Produksi --}}
                                <td class="p-1">
                                    <input type="number" step="0.01" min="0"
                                           name="komoditas[{{ $kom->id }}][produksi]"
                                           class="form-control form-control-sm text-end"
                                           placeholder="0.00">
                                </td>
                                {{-- (11) Harga --}}
                                <td class="p-1">
                                    <input type="number" step="0.01" min="0"
                                           name="komoditas[{{ $kom->id }}][harga_jual]"
                                           class="form-control form-control-sm text-end"
                                           placeholder="0">
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ── TABEL SPH-TBF ──────────────────────────────────────────────────── --}}
            <div id="table-TBF" class="sph-table d-none">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge px-3 py-2 rounded-pill fs-6" style="background:#7c3aed; color:#fff;">SPH-TBF</span>
                    <div>
                        <h6 class="fw-bold mb-0">Laporan Tanaman Biofarmaka</h6>
                        <small class="text-muted">Periode Triwulanan — Isian dalam bilangan bulat (m²/Kg)</small>
                    </div>
                </div>
                <div class="table-responsive" style="max-height: 520px; overflow-y: auto;">
                    <table class="table table-bordered table-hover align-middle mb-0" style="min-width: 1100px;">
                        <thead class="text-center small fw-bold" style="position: sticky; top: 0; z-index: 2; background: #faf5ff;">
                            <tr>
                                <th rowspan="2" style="min-width:160px; vertical-align: middle;">Nama Tanaman</th>
                                <th rowspan="2" style="min-width:130px; vertical-align: middle; background:#fef9c3;">(3) Luas Tanam Akhir Triwulan Lalu (m²)</th>
                                <th colspan="2" style="min-width:220px;">Luas Panen (m²)</th>
                                <th rowspan="2" style="min-width:110px; vertical-align: middle;">(6) Luas Rusak / Puso (m²)</th>
                                <th rowspan="2" style="min-width:130px; vertical-align: middle;">(7) Penanaman Baru / Tambah Tanam (m²)</th>
                                <th rowspan="2" style="min-width:130px; vertical-align: middle; background:#fef9c3;">(8) Luas Tanam Akhir = (3)-(4)-(5)-(6)+(7)</th>
                                <th colspan="2" style="min-width:220px;">Produksi (Kg)</th>
                                <th rowspan="2" style="min-width:120px; vertical-align: middle;">(11) Rata-rata Harga Jual (Rp/Kg)</th>
                            </tr>
                            <tr>
                                <th style="min-width:110px;">(4) Habis / Dibongkar</th>
                                <th style="min-width:110px;">(5) Belum Habis</th>
                                <th style="min-width:110px;">(9) Dipanen</th>
                                <th style="min-width:110px;">(10) Belum Habis</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach(($komoditasList['SPH-TBF'] ?? collect()) as $kom)
                            <tr>
                                <td class="fw-semibold text-secondary small">
                                    <i class="bi bi-circle-fill me-1" style="font-size:6px; color:#7c3aed;"></i>{{ $kom->nama }}
                                    <input type="hidden" name="komoditas[{{ $kom->id }}][jenis_periode]" value="{{ $kom->jenis_periode }}">
                                    <input type="hidden" name="komoditas[{{ $kom->id }}][form_type]" value="SPH-TBF">
                                </td>
                                {{-- (3) Auto-fill --}}
                                <td class="p-1" style="background:#fef9c3;">
                                    <input type="number" step="0.01" min="0"
                                           name="komoditas[{{ $kom->id }}][luas_tanam_akhir_bulan_lalu]"
                                           class="form-control form-control-sm text-end border-0 tbf-col3 prev-fill-tbf"
                                           data-komoditas-id="{{ $kom->id }}"
                                           data-field="luas_tanam_akhir_bulan_lalu"
                                           placeholder="0.00" style="background:transparent;">
                                </td>
                                {{-- (4) Panen Habis --}}
                                <td class="p-1">
                                    <input type="number" step="0.01" min="0"
                                           name="komoditas[{{ $kom->id }}][luas_panen]"
                                           class="form-control form-control-sm text-end tbf-panen tbf-calc" data-komoditas-id="{{ $kom->id }}"
                                           placeholder="0.00">
                                </td>
                                {{-- (5) Panen Belum Habis --}}
                                <td class="p-1">
                                    <input type="number" step="0.01" min="0"
                                           name="komoditas[{{ $kom->id }}][luas_panen_belum_habis]"
                                           class="form-control form-control-sm text-end tbf-panen-bh tbf-calc" data-komoditas-id="{{ $kom->id }}"
                                           placeholder="0.00">
                                </td>
                                {{-- (6) Rusak --}}
                                <td class="p-1">
                                    <input type="number" step="0.01" min="0"
                                           name="komoditas[{{ $kom->id }}][luas_rusak]"
                                           class="form-control form-control-sm text-end tbf-rusak tbf-calc" data-komoditas-id="{{ $kom->id }}"
                                           placeholder="0.00">
                                </td>
                                {{-- (7) Tanam Baru --}}
                                <td class="p-1">
                                    <input type="number" step="0.01" min="0"
                                           name="komoditas[{{ $kom->id }}][luas_tanam]"
                                           class="form-control form-control-sm text-end tbf-tanam tbf-calc" data-komoditas-id="{{ $kom->id }}"
                                           placeholder="0.00">
                                </td>
                                {{-- (8) Auto-kalkulasi --}}
                                <td class="p-1" style="background:#fef9c3;">
                                    <input type="number" step="0.01"
                                           name="komoditas[{{ $kom->id }}][luas_tanam_akhir]"
                                           class="form-control form-control-sm text-end fw-bold border-0 tbf-akhir"
                                           data-komoditas-id="{{ $kom->id }}"
                                           placeholder="0.00" readonly style="background:transparent; color:#581c87;">
                                </td>
                                {{-- (9) Produksi Dipanen --}}
                                <td class="p-1">
                                    <input type="number" step="0.01" min="0"
                                           name="komoditas[{{ $kom->id }}][produksi]"
                                           class="form-control form-control-sm text-end"
                                           placeholder="0.00">
                                </td>
                                {{-- (10) Produksi Belum Habis --}}
                                <td class="p-1">
                                    <input type="number" step="0.01" min="0"
                                           name="komoditas[{{ $kom->id }}][produksi_belum_habis]"
                                           class="form-control form-control-sm text-end"
                                           placeholder="0.00">
                                </td>
                                {{-- (11) Harga --}}
                                <td class="p-1">
                                    <input type="number" step="0.01" min="0"
                                           name="komoditas[{{ $kom->id }}][harga_jual]"
                                           class="form-control form-control-sm text-end"
                                           placeholder="0">
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Keterangan kolom kalkulasi --}}
            <div class="d-flex align-items-center gap-3 mt-3 mb-4">
                <span class="d-flex align-items-center gap-1 small text-muted">
                    <span style="display:inline-block; width:14px; height:14px; background:#fef9c3; border:1px solid #d97706; border-radius:3px;"></span>
                    Kolom kuning = auto-fill / auto-hitung
                </span>
                <span class="d-flex align-items-center gap-1 small text-muted">
                    <i class="bi bi-cloud-download text-primary"></i>
                    Klik tombol <strong class="text-primary mx-1">Muat Data</strong> untuk mengisi otomatis kolom periode lalu dari database
                </span>
            </div>

            {{-- Action Buttons --}}
            <div class="d-flex gap-2 pt-3 border-top">
                <button type="submit" class="btn btn-success px-4 rounded-3 shadow-sm">
                    <i class="bi bi-check-circle me-2"></i>Simpan Laporan
                </button>
                <a href="{{ route('hortikultura.index') }}" class="btn btn-light px-4 rounded-3 text-secondary border">
                    <i class="bi bi-arrow-left me-2"></i>Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function () {

    // ── Konfigurasi tampilan per form type ─────────────────────────────────
    const formConfig = {
        'SPH-SBS': {
            banner: 'linear-gradient(135deg, #1d4ed8, #3b82f6)',
            badge: 'SPH-SBS',
            subtitle: 'Laporan Sayuran & Buah Semusim — Periode Bulanan',
            tableId: 'table-SBS',
            jenisInput: 'bulan',
        },
        'SPH-BST': {
            banner: 'linear-gradient(135deg, #15803d, #22c55e)',
            badge: 'SPH-BST',
            subtitle: 'Laporan Buah-buahan & Sayuran Tahunan — Periode Triwulanan',
            tableId: 'table-BST',
            jenisInput: 'triwulan',
        },
        'SPH-TBF': {
            banner: 'linear-gradient(135deg, #6d28d9, #a78bfa)',
            badge: 'SPH-TBF',
            subtitle: 'Laporan Tanaman Biofarmaka — Periode Triwulanan',
            tableId: 'table-TBF',
            jenisInput: 'triwulan',
        },
    };

    const monthOptions = {
        1:'Januari',2:'Februari',3:'Maret',4:'April',
        5:'Mei',6:'Juni',7:'Juli',8:'Agustus',
        9:'September',10:'Oktober',11:'November',12:'Desember'
    };
    const triwulanOptions = {
        1:'Triwulan I (Jan-Mar)',2:'Triwulan II (Apr-Jun)',
        3:'Triwulan III (Jul-Sep)',4:'Triwulan IV (Okt-Des)'
    };

    function switchFormType(ft) {
        const cfg = formConfig[ft];
        if (!cfg) return;

        // Banner
        $('#formBanner').css('background', cfg.banner);
        $('#formBadge').text(cfg.badge);
        $('#bannerSubtitle').text(cfg.subtitle);

        // Show/hide tabel
        $('.sph-table').addClass('d-none');
        $('#' + cfg.tableId).removeClass('d-none');

        // Populate bulan select
        const $bulan = $('#bulan');
        $bulan.empty();
        const opts = cfg.jenisInput === 'bulan' ? monthOptions : triwulanOptions;
        $.each(opts, function (val, text) {
            $bulan.append(new Option(text, val));
        });

        // Reset prev-fill inputs (clear only yellow cells)
        $('.prev-fill-sbs, .prev-fill-bst, .prev-fill-tbf').val('');
        recalcAll();
    }

    // ── Auto-kalkulasi SBS & TBF: (3)-(4)-(5)-(6)+(7) ─────────────────────
    function recalcSBS(komId) {
        const col3 = parseFloat($('.sbs-col3[data-komoditas-id="'+komId+'"]').val()) || 0;
        const col4 = parseFloat($('.sbs-panen[data-komoditas-id="'+komId+'"]').val()) || 0;
        const col5 = parseFloat($('.sbs-panen-bh[data-komoditas-id="'+komId+'"]').val()) || 0;
        const col6 = parseFloat($('.sbs-rusak[data-komoditas-id="'+komId+'"]').val()) || 0;
        const col7 = parseFloat($('.sbs-tanam[data-komoditas-id="'+komId+'"]').val()) || 0;
        const akhir = Math.max(0, col3 - col4 - col5 - col6 + col7);
        $('.sbs-akhir[data-komoditas-id="'+komId+'"]').val(akhir.toFixed(2));
    }
    function recalcBST(komId) {
        const col3 = parseInt($('.bst-col3[data-komoditas-id="'+komId+'"]').val()) || 0;
        const col4 = parseInt($('.bst-dibongkar[data-komoditas-id="'+komId+'"]').val()) || 0;
        const col5 = parseInt($('.bst-baru[data-komoditas-id="'+komId+'"]').val()) || 0;
        const akhir = Math.max(0, col3 - col4 + col5);
        $('.bst-akhir[data-komoditas-id="'+komId+'"]').val(akhir);
    }
    function recalcTBF(komId) {
        const col3 = parseFloat($('.tbf-col3[data-komoditas-id="'+komId+'"]').val()) || 0;
        const col4 = parseFloat($('.tbf-panen[data-komoditas-id="'+komId+'"]').val()) || 0;
        const col5 = parseFloat($('.tbf-panen-bh[data-komoditas-id="'+komId+'"]').val()) || 0;
        const col6 = parseFloat($('.tbf-rusak[data-komoditas-id="'+komId+'"]').val()) || 0;
        const col7 = parseFloat($('.tbf-tanam[data-komoditas-id="'+komId+'"]').val()) || 0;
        const akhir = Math.max(0, col3 - col4 - col5 - col6 + col7);
        $('.tbf-akhir[data-komoditas-id="'+komId+'"]').val(akhir.toFixed(2));
    }
    function recalcAll() {
        $('.sbs-calc').each(function () { recalcSBS($(this).data('komoditas-id')); });
        $('.bst-calc').each(function () { recalcBST($(this).data('komoditas-id')); });
        $('.tbf-calc').each(function () { recalcTBF($(this).data('komoditas-id')); });
    }

    // Delegasikan event input ke semua kolom kalkulasi
    $(document).on('input', '.sbs-calc, .sbs-col3', function () { recalcSBS($(this).data('komoditas-id')); });
    $(document).on('input', '.bst-calc, .bst-col3', function () { recalcBST($(this).data('komoditas-id')); });
    $(document).on('input', '.tbf-calc, .tbf-col3', function () { recalcTBF($(this).data('komoditas-id')); });

    // ── Auto-fill data periode sebelumnya via AJAX ──────────────────────────
    $('#btnLoadPrev').on('click', function () {
        const kecamatanId = $('#kecamatan_id').val();
        const bulan       = $('#bulan').val();
        const tahun       = $('#tahun').val();
        const ft          = $('#form_type_select').val();

        if (!kecamatanId || !bulan || !tahun) {
            alert('Pilih Kecamatan, Periode, dan Tahun terlebih dahulu!');
            return;
        }

        const $btn = $(this);
        $btn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i>');

        $.ajax({
            url: "{{ route('hortikultura.prev-data') }}",
            type: 'GET',
            data: { kecamatan_id: kecamatanId, form_type: ft, bulan: bulan, tahun: tahun },
            success: function (data) {
                let filled = 0;
                $.each(data, function (komId, fields) {
                    if (fields.luas_tanam_akhir_bulan_lalu !== undefined) {
                        $('.sbs-col3[data-komoditas-id="'+komId+'"]').val(fields.luas_tanam_akhir_bulan_lalu);
                        $('.tbf-col3[data-komoditas-id="'+komId+'"]').val(fields.luas_tanam_akhir_bulan_lalu);
                        filled++;
                    }
                    if (fields.jumlah_tanaman_akhir_triwulan_lalu !== undefined) {
                        $('.bst-col3[data-komoditas-id="'+komId+'"]').val(fields.jumlah_tanaman_akhir_triwulan_lalu);
                        filled++;
                    }
                });
                recalcAll();
                if (filled > 0) {
                    $btn.html('<i class="bi bi-check-circle text-success"></i>');
                    setTimeout(function() { $btn.html('<i class="bi bi-cloud-download"></i>'); }, 2000);
                } else {
                    $btn.html('<i class="bi bi-exclamation-circle text-warning"></i>');
                    setTimeout(function() { $btn.html('<i class="bi bi-cloud-download"></i>'); }, 2000);
                }
            },
            error: function () {
                alert('Gagal memuat data periode sebelumnya.');
                $btn.html('<i class="bi bi-cloud-download"></i>');
            },
            complete: function () {
                $btn.prop('disabled', false);
            }
        });
    });

    // ── Switch form type on dropdown change ────────────────────────────────
    $('#form_type_select').on('change', function () {
        switchFormType($(this).val());
    });

    // ── Init ────────────────────────────────────────────────────────────────
    switchFormType('SPH-SBS');
});
</script>

<style>
    .sph-table table th, .sph-table table td {
        font-size: 0.78rem;
        padding: 4px 5px !important;
        vertical-align: middle;
    }
    .sph-table table input.form-control-sm {
        font-size: 0.8rem;
        padding: 3px 6px;
    }
    .sph-table thead th {
        line-height: 1.3;
    }
    .sph-table::-webkit-scrollbar { height: 6px; }
    .sph-table::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
</style>
@endsection
