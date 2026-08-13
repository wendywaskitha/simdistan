@extends('layouts.admin')

@section('title', 'Laporan Produksi - Tanaman Pangan')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Laporan Produksi'],
    ['label' => 'Tanaman Pangan']
]" />

<div class="card custom-card border-0 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1">Laporan Produksi Tanaman Pangan</h5>
            <p class="text-muted small mb-0">Kelola dan pantau statistik luas lahan serta hasil panen komoditas per tahun.</p>
        </div>
    </div>



    <!-- Nav Tabs Utama -->
    <ul class="nav nav-tabs nav-tabs-custom border-bottom mb-4" id="produksiTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold text-success border-0 border-bottom border-3 border-success" id="lahan-baku-tab" data-bs-toggle="tab" data-bs-target="#lahan-baku-pane" type="button" role="tab" aria-controls="lahan-baku-pane" aria-selected="true">
                <i class="bi bi-geo-alt-fill me-2"></i>Luas Lahan Baku
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold text-secondary border-0" id="target-tanam-tab" data-bs-toggle="tab" data-bs-target="#target-tanam-pane" type="button" role="tab" aria-controls="target-tanam-pane" aria-selected="false">
                <i class="bi bi-bullseye me-2"></i>Target Tanam
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold text-secondary border-0" id="table-tab" data-bs-toggle="tab" data-bs-target="#table-pane" type="button" role="tab" aria-controls="table-pane" aria-selected="false">
                <i class="bi bi-table me-2"></i>Tabel Matriks
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold text-secondary border-0" id="rekap-ltt-tab" data-bs-toggle="tab" data-bs-target="#rekap-ltt-pane" type="button" role="tab" aria-controls="rekap-ltt-pane" aria-selected="false">
                <i class="bi bi-file-earmark-bar-graph me-2"></i>Rekap LTT
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold text-secondary border-0" id="chart-tab" data-bs-toggle="tab" data-bs-target="#chart-pane" type="button" role="tab" aria-controls="chart-pane" aria-selected="false">
                <i class="bi bi-bar-chart-line me-2"></i>Visualisasi Grafik
            </button>
        </li>
    </ul>

    <div class="tab-content" id="produksiTabContent">
        <!-- Pane Luas Lahan Baku -->
        <div class="tab-pane fade show active" id="lahan-baku-pane" role="tabpanel" aria-labelledby="lahan-baku-tab" tabindex="0">
            <form action="{{ route('tanaman-pangan.simpan-lahan-baku') }}" method="POST" id="lahanBakuForm">
                @csrf
                <div class="row align-items-center mb-3 g-3">
                    <div class="col-md-3">
                        <label for="lahanBakuTahun" class="form-label fw-semibold text-secondary small">Pilih Tahun Lahan Baku</label>
                        <select name="tahun" id="lahanBakuTahun" class="form-select border-light shadow-sm rounded-3" onchange="changeLahanBakuFilter()">
                            @foreach($years as $tahun)
                                <option value="{{ $tahun }}">{{ $tahun }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="lahanBakuKomoditas" class="form-label fw-semibold text-secondary small">Pilih Komoditas Lahan Baku</label>
                        <select name="komoditas_id" id="lahanBakuKomoditas" class="form-select border-light shadow-sm rounded-3" onchange="changeLahanBakuFilter()">
                            @foreach($komoditasList as $kom)
                                <option value="{{ $kom->id }}">{{ $kom->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="table-responsive mb-4 border rounded-3 overflow-hidden">
                    <table class="table table-bordered align-middle text-center mb-0">
                        <thead class="table-light small fw-bold">
                            <tr>
                                <th width="10%">No</th>
                                <th>Kecamatan</th>
                                <th width="40%">Luas Lahan Baku (Hektar / Ha)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kecamatanListObj as $index => $kec)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="text-start fw-semibold text-secondary">{{ $kec->nama }}</td>
                                    <td>
                                        <div class="input-group">
                                            <input type="number" step="0.01" min="0" 
                                                   name="lahan[{{ $kec->id }}]" 
                                                   id="lahan_input_{{ $kec->id }}"
                                                   class="form-control text-end lahan-baku-input" 
                                                   value="0.00" required>
                                            <span class="input-group-text bg-light border-light-subtle">Ha</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <button type="submit" class="btn btn-success px-4 rounded-3 shadow-sm">
                    <i class="bi bi-save-fill me-2"></i> Simpan Luas Lahan Baku
                </button>
            </form>
        </div>

        <!-- Pane Target Tanam Bulanan -->
        <div class="tab-pane fade" id="target-tanam-pane" role="tabpanel" aria-labelledby="target-tanam-tab" tabindex="0">
            <form action="{{ route('tanaman-pangan.simpan-target-tanam') }}" method="POST" id="targetTanamForm">
                @csrf
                <div class="row align-items-center mb-3 g-3">
                    <div class="col-md-3">
                        <label for="targetTanamTahun" class="form-label fw-semibold text-secondary small">Pilih Tahun Target</label>
                        <select name="tahun" id="targetTanamTahun" class="form-select border-light shadow-sm rounded-3" onchange="changeTargetTanamFilter()">
                            @foreach($years as $tahun)
                                <option value="{{ $tahun }}">{{ $tahun }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="targetTanamKomoditas" class="form-label fw-semibold text-secondary small">Pilih Komoditas Target</label>
                        <select name="komoditas_id" id="targetTanamKomoditas" class="form-select border-light shadow-sm rounded-3" onchange="changeTargetTanamFilter()">
                            @foreach($komoditasList as $kom)
                                <option value="{{ $kom->id }}">{{ $kom->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="table-responsive mb-4 border rounded-3 overflow-hidden">
                    <table class="table table-bordered align-middle text-center mb-0 small" id="targetTanamTable">
                        <thead class="table-light fw-bold">
                            <tr>
                                <th width="3%">No</th>
                                <th width="15%" class="text-start">Nama Kecamatan</th>
                                <th>Jan</th>
                                <th>Feb</th>
                                <th>Mar</th>
                                <th>Apr</th>
                                <th>Mei</th>
                                <th>Jun</th>
                                <th>Jul</th>
                                <th>Agt</th>
                                <th>Sep</th>
                                <th>Okt</th>
                                <th>Nov</th>
                                <th>Des</th>
                                <th width="8%">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kecamatanListObj as $index => $kec)
                                <tr class="target-kec-row" data-kec-id="{{ $kec->id }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td class="text-start fw-semibold text-secondary">{{ $kec->nama }}</td>
                                    @for($m = 1; $m <= 12; $m++)
                                        <td>
                                            <input type="number" step="0.01" min="0" 
                                                   name="target[{{ $kec->id }}][{{ $m }}]" 
                                                   id="target_input_{{ $kec->id }}_{{ $m }}"
                                                   class="form-control form-control-sm text-end target-month-input border-0 bg-transparent shadow-none" 
                                                   placeholder="0" value="0.00" required style="min-width: 60px;">
                                        </td>
                                    @endfor
                                    <td>
                                        <input type="text" id="target_total_{{ $kec->id }}" 
                                               class="form-control form-control-sm text-end fw-bold border-0 bg-transparent shadow-none" 
                                               value="0.00" readonly>
                                    </td>
                                </tr>
                            @endforeach
                            <!-- Baris Total Kumulatif -->
                            <tr class="table-warning fw-bold text-dark text-center">
                                <td></td>
                                <td class="text-start">Total</td>
                                @for($m = 1; $m <= 12; $m++)
                                    <td id="target_col_total_{{ $m }}">0.00</td>
                                @endfor
                                <td id="target_grand_total">0.00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <button type="submit" class="btn btn-success px-4 rounded-3 shadow-sm">
                    <i class="bi bi-save-fill me-2"></i> Simpan Target Tanam
                </button>
            </form>
        </div>

        <!-- Pane Rekap LTT (Read-only) -->
        <div class="tab-pane fade" id="rekap-ltt-pane" role="tabpanel" aria-labelledby="rekap-ltt-tab" tabindex="0">
            <div class="row align-items-center mb-3 g-3">
                <div class="col-md-3">
                    <label for="rekapLttTahun" class="form-label fw-semibold text-secondary small">Pilih Tahun Rekap</label>
                    <select id="rekapLttTahun" class="form-select border-light shadow-sm rounded-3" onchange="changeRekapLttFilter()">
                        @foreach($years as $tahun)
                            <option value="{{ $tahun }}">{{ $tahun }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="rekapLttKomoditas" class="form-label fw-semibold text-secondary small">Pilih Komoditas Rekap</label>
                    <select id="rekapLttKomoditas" class="form-select border-light shadow-sm rounded-3" onchange="changeRekapLttFilter()">
                        @foreach($komoditasList as $kom)
                            <option value="{{ $kom->id }}">{{ $kom->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <a href="#" id="btnCetakRekapLtt" target="_blank" class="btn btn-primary rounded-3 shadow-sm w-100 py-2">
                        <i class="bi bi-printer-fill me-2"></i> Cetak Rekap LTT
                    </a>
                </div>
            </div>

            <!-- 1. TABEL TARGET TANAM -->
            <div class="mb-5">
                <div class="d-flex align-items-center mb-2">
                    <span class="badge bg-success me-2">&nbsp;</span>
                    <h6 class="fw-bold mb-0 text-dark">TARGET TANAM</h6>
                </div>
                <div class="table-responsive border rounded-3 overflow-hidden">
                    <table class="table table-bordered align-middle text-center mb-0 small" id="rekapTargetTable">
                        <thead class="table-light fw-bold">
                            <tr>
                                <th width="3%">No</th>
                                <th width="15%" class="text-start">Nama Kecamatan</th>
                                <th>Jan</th>
                                <th>Feb</th>
                                <th>Mar</th>
                                <th>Apr</th>
                                <th>Mei</th>
                                <th>Jun</th>
                                <th>Jul</th>
                                <th>Agt</th>
                                <th>Sep</th>
                                <th>Okt</th>
                                <th>Nov</th>
                                <th>Des</th>
                                <th width="8%">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kecamatanListObj as $index => $kec)
                                <tr class="rekap-row" data-kec-id="{{ $kec->id }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td class="text-start fw-semibold text-secondary">{{ $kec->nama }}</td>
                                    @for($m = 1; $m <= 12; $m++)
                                        <td id="rekap_target_{{ $kec->id }}_{{ $m }}">0.00</td>
                                    @endfor
                                    <td class="fw-bold text-dark" id="rekap_target_total_{{ $kec->id }}">0.00</td>
                                </tr>
                            @endforeach
                            <!-- Baris Total Kumulatif -->
                            <tr class="table-warning fw-bold text-dark text-center">
                                <td></td>
                                <td class="text-start">Total</td>
                                @for($m = 1; $m <= 12; $m++)
                                    <td id="rekap_target_col_{{ $m }}">0.00</td>
                                @endfor
                                <td id="rekap_target_grand">0.00</td>
                            </tr>
                            <tr class="table-success fw-bold text-dark text-center">
                                <td></td>
                                <td class="text-start">Triwulan</td>
                                <td colspan="3" id="rekap_target_tw_1">TW1: 0.00</td>
                                <td colspan="3" id="rekap_target_tw_2">TW2: 0.00</td>
                                <td colspan="3" id="rekap_target_tw_3">TW3: 0.00</td>
                                <td colspan="3" id="rekap_target_tw_4">TW4: 0.00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 2. TABEL LUAS TANAM -->
            <div class="mb-5">
                <div class="d-flex align-items-center mb-2">
                    <span class="badge bg-warning me-2">&nbsp;</span>
                    <h6 class="fw-bold mb-0 text-dark">LUAS TANAM (REALISASI)</h6>
                </div>
                <div class="table-responsive border rounded-3 overflow-hidden">
                    <table class="table table-bordered align-middle text-center mb-0 small" id="rekapTanamTable">
                        <thead class="table-light fw-bold">
                            <tr>
                                <th width="3%">No</th>
                                <th width="15%" class="text-start">Nama Kecamatan</th>
                                <th>Jan</th>
                                <th>Feb</th>
                                <th>Mar</th>
                                <th>Apr</th>
                                <th>Mei</th>
                                <th>Jun</th>
                                <th>Jul</th>
                                <th>Agt</th>
                                <th>Sep</th>
                                <th>Okt</th>
                                <th>Nov</th>
                                <th>Des</th>
                                <th width="8%">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kecamatanListObj as $index => $kec)
                                <tr class="rekap-row" data-kec-id="{{ $kec->id }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td class="text-start fw-semibold text-secondary">{{ $kec->nama }}</td>
                                    @for($m = 1; $m <= 12; $m++)
                                        <td id="rekap_tanam_{{ $kec->id }}_{{ $m }}">0.00</td>
                                    @endfor
                                    <td class="fw-bold text-dark" id="rekap_tanam_total_{{ $kec->id }}">0.00</td>
                                </tr>
                            @endforeach
                            <!-- Baris Total Kumulatif -->
                            <tr class="table-warning fw-bold text-dark text-center">
                                <td></td>
                                <td class="text-start">Total</td>
                                @for($m = 1; $m <= 12; $m++)
                                    <td id="rekap_tanam_col_{{ $m }}">0.00</td>
                                @endfor
                                <td id="rekap_tanam_grand">0.00</td>
                            </tr>
                            <tr class="table-success fw-bold text-dark text-center">
                                <td></td>
                                <td class="text-start">Triwulan</td>
                                <td colspan="3" id="rekap_tanam_tw_1">TW1: 0.00</td>
                                <td colspan="3" id="rekap_tanam_tw_2">TW2: 0.00</td>
                                <td colspan="3" id="rekap_tanam_tw_3">TW3: 0.00</td>
                                <td colspan="3" id="rekap_tanam_tw_4">TW4: 0.00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 3. TABEL LUAS PANEN -->
            <div class="mb-4">
                <div class="d-flex align-items-center mb-2">
                    <span class="badge bg-danger me-2">&nbsp;</span>
                    <h6 class="fw-bold mb-0 text-dark">LUAS PANEN (REALISASI)</h6>
                </div>
                <div class="table-responsive border rounded-3 overflow-hidden">
                    <table class="table table-bordered align-middle text-center mb-0 small" id="rekapPanenTable">
                        <thead class="table-light fw-bold">
                            <tr>
                                <th width="3%">No</th>
                                <th width="15%" class="text-start">Nama Kecamatan</th>
                                <th>Jan</th>
                                <th>Feb</th>
                                <th>Mar</th>
                                <th>Apr</th>
                                <th>Mei</th>
                                <th>Jun</th>
                                <th>Jul</th>
                                <th>Agt</th>
                                <th>Sep</th>
                                <th>Okt</th>
                                <th>Nov</th>
                                <th>Des</th>
                                <th width="8%">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kecamatanListObj as $index => $kec)
                                <tr class="rekap-row" data-kec-id="{{ $kec->id }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td class="text-start fw-semibold text-secondary">{{ $kec->nama }}</td>
                                    @for($m = 1; $m <= 12; $m++)
                                        <td id="rekap_panen_{{ $kec->id }}_{{ $m }}">0.00</td>
                                    @endfor
                                    <td class="fw-bold text-dark" id="rekap_panen_total_{{ $kec->id }}">0.00</td>
                                </tr>
                            @endforeach
                            <!-- Baris Total Kumulatif -->
                            <tr class="table-warning fw-bold text-dark text-center">
                                <td></td>
                                <td class="text-start">Total</td>
                                @for($m = 1; $m <= 12; $m++)
                                    <td id="rekap_panen_col_{{ $m }}">0.00</td>
                                @endfor
                                <td id="rekap_panen_grand">0.00</td>
                            </tr>
                            <tr class="table-success fw-bold text-dark text-center">
                                <td></td>
                                <td class="text-start">Triwulan</td>
                                <td colspan="3" id="rekap_panen_tw_1">TW1: 0.00</td>
                                <td colspan="3" id="rekap_panen_tw_2">TW2: 0.00</td>
                                <td colspan="3" id="rekap_panen_tw_3">TW3: 0.00</td>
                                <td colspan="3" id="rekap_panen_tw_4">TW4: 0.00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pane Tabel Matriks -->
        <div class="tab-pane fade" id="table-pane" role="tabpanel" aria-labelledby="table-tab" tabindex="0">
            <!-- Filter Kecamatan Tabel Matriks -->
            <div class="row g-3 mb-4 bg-light rounded-3 p-3 border border-light-subtle">
                <div class="col-md-4">
                    <label for="filterKecamatan" class="form-label fw-semibold text-secondary small"><i class="bi bi-geo-alt me-1"></i>Wilayah Kerja Kecamatan</label>
                    <select id="filterKecamatan" class="form-select form-select-sm border-0 shadow-sm rounded-3">
                        <option value="" selected>-- Semua Kecamatan --</option>
                        @foreach($kecamatans as $id => $nama)
                            <option value="{{ $id }}">{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle" id="laporanTable" style="width:100%">
                    <thead class="table-light align-middle text-center small fw-bold">
                        <tr>
                            <th rowspan="2" width="4%">No</th>
                            <th rowspan="2" width="12%">Komoditas</th>
                            @foreach($years as $tahun)
                                <th colspan="5" class="border-bottom">{{ $tahun }}</th>
                            @endforeach
                            <th rowspan="2" width="6%">Aksi</th>
                        </tr>
                        <tr class="small text-muted">
                            @foreach($years as $tahun)
                                <th>Lahan</th>
                                <th>Tanam</th>
                                <th>Panen</th>
                                <th>Prod.</th>
                                <th>Pdv.</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <!-- Pane Visualisasi Grafik -->
        <div class="tab-pane fade" id="chart-pane" role="tabpanel" aria-labelledby="chart-tab" tabindex="0">
            <!-- Panel Filter Terpadu -->
            <div class="row g-3 mb-4 bg-light rounded-3 p-3 border border-light-subtle">
                <div class="col-md-3">
                    <label for="chartKecamatan" class="form-label fw-semibold text-secondary small"><i class="bi bi-geo-alt me-1"></i>Wilayah Kecamatan</label>
                    <select id="chartKecamatan" class="form-select form-select-sm border-0 shadow-sm rounded-3">
                        <option value="" selected>-- Semua Kecamatan --</option>
                        @foreach($kecamatans as $id => $nama)
                            <option value="{{ $id }}">{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="chartKomoditas" class="form-label fw-semibold text-secondary small"><i class="bi bi-flower1 me-1"></i>Komoditas</label>
                    <select id="chartKomoditas" class="form-select form-select-sm border-0 shadow-sm rounded-3">
                        @foreach($komoditasList as $k)
                            <option value="{{ $k->id }}">{{ $k->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="chartIndikator" class="form-label fw-semibold text-secondary small"><i class="bi bi-bar-chart me-1"></i>Indikator Grafik</label>
                    <select id="chartIndikator" class="form-select form-select-sm border-0 shadow-sm rounded-3">
                        <option value="produksi" selected>Hasil Produksi</option>
                        <option value="luas_tanam">Luas Tanam (Ha)</option>
                        <option value="luas_panen">Luas Panen (Ha)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="chartTahunBulanan" class="form-label fw-semibold text-secondary small"><i class="bi bi-calendar3 me-1"></i>Tahun (Grafik Bulanan)</label>
                    <select id="chartTahunBulanan" class="form-select form-select-sm border-0 shadow-sm rounded-3">
                        @foreach($years as $tahun)
                            <option value="{{ $tahun }}" {{ $tahun == end($years) ? 'selected' : '' }}>{{ $tahun }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Canvas Grafik Tahunan -->
            <div class="mb-4">
                <h6 class="fw-bold text-secondary mb-3"><i class="bi bi-calendar-event me-2"></i>Perkembangan Produksi Tahunan ({{ $years[0] }}-{{ end($years) }})</h6>
                <div class="bg-white p-4 border border-light-subtle rounded-4 shadow-sm" style="position: relative; height: 320px;">
                    <canvas id="komoditasChart" style="width: 100%; height: 100%;"></canvas>
                </div>
            </div>

            <!-- Canvas Grafik Bulanan -->
            <div class="mb-5">
                <h6 class="fw-bold text-secondary mb-3"><i class="bi bi-calendar-range me-2"></i>Perkembangan Produksi Bulanan (Jan-Des)</h6>
                <div class="bg-white p-4 border border-light-subtle rounded-4 shadow-sm" style="position: relative; height: 320px;">
                    <canvas id="bulananChart" style="width: 100%; height: 100%;"></canvas>
                </div>
            </div>

            <!-- Canvas Grafik Target vs Realisasi Tanam Bulanan -->
            <div class="mt-5">
                <h6 class="fw-bold text-secondary mb-3"><i class="bi bi-bar-chart-fill me-2"></i>Komparasi Target vs Realisasi Luas Tanam Bulanan</h6>
                <div class="bg-white p-4 border border-light-subtle rounded-4 shadow-sm" style="position: relative; height: 320px;">
                    <canvas id="targetVsRealisasiChart" style="width: 100%; height: 100%;"></canvas>
                </div>
            </div>

            <!-- Canvas Grafik Target vs Realisasi Tanam Tahunan -->
            <div class="mt-5">
                <h6 class="fw-bold text-secondary mb-3"><i class="bi bi-bar-chart-fill me-2"></i>Komparasi Target vs Realisasi Luas Tanam Tahunan (5 Tahun Terakhir)</h6>
                <div class="bg-white p-4 border border-light-subtle rounded-4 shadow-sm" style="position: relative; height: 320px;">
                    <canvas id="targetVsRealisasiTahunanChart" style="width: 100%; height: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<!-- Import Chart.js via CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        // 1. DataTables Init
        const table = $('#laporanTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('tanaman-pangan.index') }}",
                data: function(d) {
                    d.kecamatan_id = $('#filterKecamatan').val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'komoditas_nama', name: 'komoditas_nama'},
                
                @foreach($years as $tahun)
                    {data: 'lahan_{{ $tahun }}', name: 'lahan_{{ $tahun }}', searchable: false, render: function(data){ return parseFloat(data).toLocaleString('id-ID') + ' Ha'; }},
                    {data: 'tanam_{{ $tahun }}', name: 'tanam_{{ $tahun }}', searchable: false, render: function(data){ return parseFloat(data).toLocaleString('id-ID') + ' Ha'; }},
                    {data: 'panen_{{ $tahun }}', name: 'panen_{{ $tahun }}', searchable: false, render: function(data){ return parseFloat(data).toLocaleString('id-ID') + ' Ha'; }},
                    {data: 'produksi_{{ $tahun }}', name: 'produksi_{{ $tahun }}', searchable: false, render: function(data){ return parseFloat(data).toLocaleString('id-ID'); }},
                    {data: 'produktivitas_{{ $tahun }}', name: 'produktivitas_{{ $tahun }}', searchable: false, render: function(data){ return parseFloat(data).toFixed(2).toLocaleString('id-ID'); }},
                @endforeach
                
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            }
        });

        // Trigger redraw on filter change
        $('#filterKecamatan').on('change', function() {
            table.draw();
            loadChartData(); // Juga perbarui grafik saat kecamatan diganti
        });



        // 2. Tab Custom Styling Toggle
        $('#produksiTab button').on('click', function() {
            $('#produksiTab button').removeClass('text-success border-bottom border-3 border-success').addClass('text-secondary');
            $(this).addClass('text-success border-bottom border-3 border-success').removeClass('text-secondary');
        });

        // 3. Chart.js Init
        let komoditasChart = null;
        let bulananChart = null;
        let targetVsRealisasiChart = null;
        let targetVsRealisasiTahunanChart = null;

        const loadChartData = () => {
            const komoditasId = $('#chartKomoditas').val();
            const indicator = $('#chartIndikator').val();
            const kecamatanId = $('#chartKecamatan').val();
            const tahunBulanan = $('#chartTahunBulanan').val();

            if (!komoditasId) return;

            $.ajax({
                url: "{{ route('tanaman-pangan.data-grafik') }}",
                type: 'GET',
                data: {
                    komoditas_id: komoditasId,
                    kecamatan_id: kecamatanId,
                    tahun_bulanan: tahunBulanan
                },
                success: function(response) {
                    // Update Grafik Tahunan
                    const ctxTahunan = document.getElementById('komoditasChart').getContext('2d');
                    let dataValuesTahun = [];
                    let labelIndicator = '';

                    if (indicator === 'luas_lahan') {
                        dataValuesTahun = response.luas_lahan;
                        labelIndicator = 'Luas Lahan (Ha)';
                    } else if (indicator === 'luas_tanam') {
                        dataValuesTahun = response.luas_tanam;
                        labelIndicator = 'Luas Tanam (Ha)';
                    } else if (indicator === 'luas_panen') {
                        dataValuesTahun = response.luas_panen;
                        labelIndicator = 'Luas Panen (Ha)';
                    } else if (indicator === 'produksi') {
                        dataValuesTahun = response.produksi;
                        labelIndicator = 'Hasil Produksi';
                    }

                    if (komoditasChart) {
                        komoditasChart.destroy();
                    }

                    komoditasChart = new Chart(ctxTahunan, {
                        type: 'bar',
                        data: {
                            labels: response.years,
                            datasets: [{
                                label: labelIndicator + ' (Tahunan)',
                                data: dataValuesTahun,
                                backgroundColor: 'rgba(16, 185, 129, 0.75)',
                                borderColor: 'rgba(16, 185, 129, 1)',
                                borderWidth: 1,
                                borderRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top'
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return value.toLocaleString('id-ID');
                                        }
                                    }
                                }
                            }
                        }
                    });

                    // Update Grafik Bulanan
                    const ctxBulanan = document.getElementById('bulananChart').getContext('2d');
                    let dataValuesBulan = [];

                    if (indicator === 'luas_lahan') {
                        dataValuesBulan = response.bulanan_lahan;
                    } else if (indicator === 'luas_tanam') {
                        dataValuesBulan = response.bulanan_tanam;
                    } else if (indicator === 'luas_panen') {
                        dataValuesBulan = response.bulanan_panen;
                    } else if (indicator === 'produksi') {
                        dataValuesBulan = response.bulanan_produksi;
                    }

                    if (bulananChart) {
                        bulananChart.destroy();
                    }

                    bulananChart = new Chart(ctxBulanan, {
                        type: 'bar',
                        data: {
                            labels: response.months,
                            datasets: [{
                                label: labelIndicator + ' (Bulanan - ' + tahunBulanan + ')',
                                data: dataValuesBulan,
                                backgroundColor: 'rgba(59, 130, 246, 0.75)',
                                borderColor: 'rgba(59, 130, 246, 1)',
                                borderWidth: 1,
                                borderRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top'
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return value.toLocaleString('id-ID');
                                        }
                                    }
                                }
                            }
                        }
                    });

                    // Update Grafik Komparasi Target vs Realisasi Tanam Bulanan
                    const ctxKomparasi = document.getElementById('targetVsRealisasiChart').getContext('2d');

                    if (targetVsRealisasiChart) {
                        targetVsRealisasiChart.destroy();
                    }

                    targetVsRealisasiChart = new Chart(ctxKomparasi, {
                        type: 'bar',
                        data: {
                            labels: response.months,
                            datasets: [
                                {
                                    label: 'Target Tanam (Ha)',
                                    data: response.bulanan_target,
                                    backgroundColor: 'rgba(16, 185, 129, 0.75)',
                                    borderColor: 'rgba(16, 185, 129, 1)',
                                    borderWidth: 1,
                                    borderRadius: 6
                                },
                                {
                                    label: 'Realisasi Tanam (Ha)',
                                    data: response.bulanan_tanam,
                                    backgroundColor: 'rgba(59, 130, 246, 0.75)',
                                    borderColor: 'rgba(59, 130, 246, 1)',
                                    borderWidth: 1,
                                    borderRadius: 6
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top'
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return value.toLocaleString('id-ID') + ' Ha';
                                        }
                                    }
                                }
                            }
                        }
                    });

                    // Update Grafik Komparasi Target vs Realisasi Tanam Tahunan
                    const ctxKomparasiTahunan = document.getElementById('targetVsRealisasiTahunanChart').getContext('2d');

                    if (targetVsRealisasiTahunanChart) {
                        targetVsRealisasiTahunanChart.destroy();
                    }

                    targetVsRealisasiTahunanChart = new Chart(ctxKomparasiTahunan, {
                        type: 'bar',
                        data: {
                            labels: response.years,
                            datasets: [
                                {
                                    label: 'Target Tanam Tahunan (Ha)',
                                    data: response.target_tahunan,
                                    backgroundColor: 'rgba(16, 185, 129, 0.75)',
                                    borderColor: 'rgba(16, 185, 129, 1)',
                                    borderWidth: 1,
                                    borderRadius: 6
                                },
                                {
                                    label: 'Realisasi Tanam Tahunan (Ha)',
                                    data: response.luas_tanam,
                                    backgroundColor: 'rgba(59, 130, 246, 0.75)',
                                    borderColor: 'rgba(59, 130, 246, 1)',
                                    borderWidth: 1,
                                    borderRadius: 6
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top'
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return value.toLocaleString('id-ID') + ' Ha';
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            });
        };

        // Load data on click tab chart or change filters
        $('button[data-bs-target="#chart-pane"]').on('shown.bs.tab', loadChartData);
        $('#chartKomoditas, #chartIndikator, #chartTahunBulanan, #chartKecamatan').on('change', loadChartData);
    });

    // 4. Luas Lahan Baku Map logic
    const lahanBakuMap = @json($lahanBakuMap);

    const changeLahanBakuFilter = () => {
        const tahun = $('#lahanBakuTahun').val();
        const komoditasId = $('#lahanBakuKomoditas').val();
        
        const dataTahun = lahanBakuMap[tahun] || {};
        const dataKomoditas = dataTahun[komoditasId] || {};
        
        // Reset/isi input form berdasarkan kecamatan
        $('.lahan-baku-input').each(function() {
            const idAttr = $(this).attr('id');
            const kecId = idAttr.replace('lahan_input_', '');
            const val = dataKomoditas[kecId] || 0.00;
            $(this).val(val.toFixed(2));
        });
    };

    // 5. Target Tanam Map & Realtime Calculation
    const targetTanamMap = @json($targetTanamMap);

    const calculateTargetTotals = () => {
        let colTotals = Array(13).fill(0);
        let grandTotal = 0;

        $('.target-kec-row').each(function() {
            const kecId = $(this).attr('data-kec-id');
            let rowTotal = 0;

            for (let m = 1; m <= 12; m++) {
                const val = parseFloat($(`#target_input_${kecId}_${m}`).val()) || 0;
                rowTotal += val;
                colTotals[m] += val;
            }

            $(`#target_total_${kecId}`).val(rowTotal.toFixed(2));
            grandTotal += rowTotal;
        });

        // Set total column
        for (let m = 1; m <= 12; m++) {
            $(`#target_col_total_${m}`).text(colTotals[m].toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        }
        $(`#target_grand_total`).text(grandTotal.toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    };

    const changeTargetTanamFilter = () => {
        const tahun = $('#targetTanamTahun').val();
        const komoditasId = $('#targetTanamKomoditas').val();

        const dataTahun = targetTanamMap[tahun] || {};
        const dataKomoditas = dataTahun[komoditasId] || {};

        $('.target-kec-row').each(function() {
            const kecId = $(this).attr('data-kec-id');
            const dataKec = dataKomoditas[kecId] || {};

            for (let m = 1; m <= 12; m++) {
                const val = dataKec[m] || 0.00;
                $(`#target_input_${kecId}_${m}`).val(val.toFixed(2));
            }
        });

        calculateTargetTotals();
    };

    $(document).on('input', '.target-month-input', calculateTargetTotals);

    // 6. Rekap LTT Map & Realtime Calculations
    const realisasiTanamMap = @json($realisasiTanamMap);
    const realisasiPanenMap = @json($realisasiPanenMap);

    const calculateRekapTable = (tableId, mapData, prefixId) => {
        const tahun = $('#rekapLttTahun').val();
        const komoditasId = $('#rekapLttKomoditas').val();

        const dataTahun = mapData[tahun] || {};
        const dataKomoditas = dataTahun[komoditasId] || {};

        let colTotals = Array(13).fill(0);
        let grandTotal = 0;

        $(`#${tableId} .rekap-row`).each(function() {
            const kecId = $(this).attr('data-kec-id');
            const dataKec = dataKomoditas[kecId] || {};
            let rowTotal = 0;

            for (let m = 1; m <= 12; m++) {
                const val = parseFloat(dataKec[m]) || 0;
                rowTotal += val;
                colTotals[m] += val;
                
                $(`#rekap_${prefixId}_${kecId}_${m}`).text(val > 0 ? val.toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '-');
            }

            $(`#rekap_${prefixId}_total_${kecId}`).text(rowTotal > 0 ? rowTotal.toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0.00');
            grandTotal += rowTotal;
        });

        // Set total columns
        for (let m = 1; m <= 12; m++) {
            $(`#rekap_${prefixId}_col_${m}`).text(colTotals[m] > 0 ? colTotals[m].toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0.00');
        }
        $(`#rekap_${prefixId}_grand`).text(grandTotal > 0 ? grandTotal.toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0.00');

        // Triwulan calculations
        const tw1 = colTotals[1] + colTotals[2] + colTotals[3];
        const tw2 = colTotals[4] + colTotals[5] + colTotals[6];
        const tw3 = colTotals[7] + colTotals[8] + colTotals[9];
        const tw4 = colTotals[10] + colTotals[11] + colTotals[12];

        $(`#rekap_${prefixId}_tw_1`).text(`TW1: ${tw1.toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`);
        $(`#rekap_${prefixId}_tw_2`).text(`TW2: ${tw2.toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`);
        $(`#rekap_${prefixId}_tw_3`).text(`TW3: ${tw3.toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`);
        $(`#rekap_${prefixId}_tw_4`).text(`TW4: ${tw4.toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`);
    };

    const changeRekapLttFilter = () => {
        const tahun = $('#rekapLttTahun').val();
        const komoditasId = $('#rekapLttKomoditas').val();
        
        // Update URL Cetak
        const cetakUrl = `{{ route('tanaman-pangan.cetak-rekap-ltt') }}?tahun=${tahun}&komoditas_id=${komoditasId}`;
        $('#btnCetakRekapLtt').attr('href', cetakUrl);

        calculateRekapTable('rekapTargetTable', targetTanamMap, 'target');
        calculateRekapTable('rekapTanamTable', realisasiTanamMap, 'tanam');
        calculateRekapTable('rekapPanenTable', realisasiPanenMap, 'panen');
    };

    // Trigger load awal untuk lahan baku, target tanam, & rekap LTT
    $(document).ready(function() {
        changeLahanBakuFilter();
        changeTargetTanamFilter();
        changeRekapLttFilter();
    });
</script>
<style>
    #laporanTable th, #laporanTable td {
        padding: 6px 4px !important;
        font-size: 0.82rem !important;
        white-space: nowrap;
    }
    #targetTanamTable th, #targetTanamTable td,
    #rekapTargetTable th, #rekapTargetTable td,
    #rekapTanamTable th, #rekapTanamTable td,
    #rekapPanenTable th, #rekapPanenTable td {
        vertical-align: middle !important;
        padding: 4px 4px !important;
    }
    .nav-tabs-custom .nav-link {
        transition: all 0.3s ease;
    }
    .nav-tabs-custom .nav-link.active {
        background: transparent !important;
    }
</style>
@endsection


