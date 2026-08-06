@extends('layouts.admin')

@section('title', 'PSP - Distribusi Pupuk')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Prasarana & Sarana (PSP)'],
    ['label' => 'Distribusi Pupuk']
]" />

<div class="card custom-card border-0 p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h5 class="fw-bold mb-1"><i class="bi bi-droplet-half text-success me-2"></i>Distribusi & Realisasi Pupuk Bersubsidi</h5>
            <p class="text-muted small mb-0">Pantau kuota, realisasi penebusan, serta sisa kuota pupuk bersubsidi Kabupaten Muna Barat.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('distribusi-pupuk.pengalihan.index') }}" class="btn btn-outline-success rounded-3 px-3 py-2">
                <i class="bi bi-arrow-left-right me-1"></i> Pengalihan Kuota
            </a>
            <a href="{{ route('distribusi-pupuk.input-bulanan') }}" class="btn btn-success rounded-3 px-3 py-2">
                <i class="bi bi-plus-circle me-1"></i> Input Laporan Bulanan
            </a>
        </div>
    </div>

    <!-- Filter Group -->
    <div class="row g-3 mb-4 bg-light rounded-3 p-3 border border-light-subtle">
        <div class="col-md-3">
            <label for="filterTahun" class="form-label fw-semibold text-secondary small">Tahun</label>
            <select id="filterTahun" class="form-select border-0 shadow-sm rounded-3">
                @foreach($years as $yr)
                    <option value="{{ $yr }}" {{ date('Y') == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label for="filterBulan" class="form-label fw-semibold text-secondary small">Bulan</label>
            <select id="filterBulan" class="form-select border-0 shadow-sm rounded-3">
                @foreach($months as $num => $nama)
                    <option value="{{ $num }}" {{ date('n') == $num ? 'selected' : '' }}>{{ $nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label for="filterJenisPupuk" class="form-label fw-semibold text-secondary small">Jenis Pupuk</label>
            <select id="filterJenisPupuk" class="form-select border-0 shadow-sm rounded-3">
                @foreach($jenisPupuks as $jp)
                    <option value="{{ $jp->id }}">{{ $jp->nama }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Nav Tabs Utama -->
    <ul class="nav nav-tabs nav-tabs-custom border-bottom mb-4" id="pupukTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold text-success border-0 border-bottom border-3 border-success" id="table-tab" data-bs-toggle="tab" data-bs-target="#table-pane" type="button" role="tab" aria-controls="table-pane" aria-selected="true">
                <i class="bi bi-table me-2"></i>Matriks Kecamatan
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold text-secondary border-0" id="chart-tab" data-bs-toggle="tab" data-bs-target="#chart-pane" type="button" role="tab" aria-controls="chart-pane" aria-selected="false">
                <i class="bi bi-bar-chart-line me-2"></i>Tren Visualisasi Grafik
            </button>
        </li>
    </ul>

    <div class="tab-content" id="pupukTabContent">
        <!--        // Tab Matriks -->
        <div class="tab-pane fade show active" id="table-pane" role="tabpanel" aria-labelledby="table-tab" tabindex="0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle text-center small" id="laporanTable" style="width:100%">
                    <thead class="table-light align-middle fw-bold">
                        <tr>
                            <th width="5%">No</th>
                            <th>Kecamatan</th>
                            <th>Kuota Awal <span class="unit-label">(Kg)</span></th>
                            <th>Penebusan <span class="unit-label">(Kg)</span></th>
                            <th>Pengalihan Masuk (+) <span class="unit-label">(Kg)</span></th>
                            <th>Pengalihan Keluar (-) <span class="unit-label">(Kg)</span></th>
                            <th>Sisa Kuota <span class="unit-label">(Kg)</span></th>
                            <th>Realisasi (%)</th>
                            <th>Status Realisasi</th>
                        </tr>
                    </thead>
                    <tbody id="matrixBody">
                        <tr>
                            <td colspan="9" class="text-center text-muted">Memuat data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Tabel Progres Kumulatif Tahunan Berjalan -->
            <div class="mt-4">
                <div class="d-flex align-items-center mb-3">
                    <span class="p-2 bg-success text-white rounded-3 me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="bi bi-calendar3"></i>
                    </span>
                    <h6 class="fw-bold text-success mb-0">Matriks Progres Kumulatif Tahunan Berjalan (s.d. Bulan Terpilih)</h6>
                </div>
                <div class="table-responsive border rounded-3 overflow-hidden">
                    <table class="table table-bordered table-hover align-middle text-center small mb-0" id="tahunanProgressTable" style="width:100%">
                        <thead class="table-light align-middle fw-bold">
                            <tr>
                                <th width="5%">No</th>
                                <th>Kecamatan</th>
                                <th>Kuota Tahunan <span class="unit-label">(Kg)</span></th>
                                <th>Penebusan Kumulatif <span class="unit-label">(Kg)</span></th>
                                <th>Pengalihan Masuk Kumulatif (+) <span class="unit-label">(Kg)</span></th>
                                <th>Pengalihan Keluar Kumulatif (-) <span class="unit-label">(Kg)</span></th>
                                <th>Sisa Kuota Tahunan <span class="unit-label">(Kg)</span></th>
                                <th>Realisasi Tahunan (%)</th>
                            </tr>
                        </thead>
                        <tbody id="tahunanProgressBody">
                            <tr>
                                <td colspan="8" class="text-center text-muted">Memuat data progres tahunan...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Panel Rekomendasi Relokasi -->
            <div class="mt-4 p-4 bg-light rounded-4 border border-light-subtle d-none" id="recommendation-panel">
                <div class="d-flex align-items-center mb-3">
                    <span class="p-2 bg-success text-white rounded-3 me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="bi bi-info-circle-fill"></i>
                    </span>
                    <h6 class="fw-bold text-success mb-0" id="recommendation-title">Rekomendasi Pengalihan / Relokasi Kuota Pupuk</h6>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-none bg-white p-3 h-100 rounded-3 border border-light-subtle">
                            <span class="fw-bold text-danger small mb-2 d-block" id="recommendation-source-title"><i class="bi bi-arrow-down-circle-fill me-1"></i>Kecamatan Sumber Relokasi (Penebusan &lt; 75%)</span>
                            <div class="text-secondary small" id="recommendation-source-list">
                                Memuat rekomendasi...
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-none bg-white p-3 h-100 rounded-3 border border-light-subtle">
                            <span class="fw-bold text-primary small mb-2 d-block" id="recommendation-target-title"><i class="bi bi-arrow-up-circle-fill me-1"></i>Kecamatan Target Relokasi (Penebusan &ge; 75%)</span>
                            <div class="text-secondary small" id="recommendation-target-list">
                                Memuat rekomendasi...
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-3 text-muted small bg-white p-3 rounded-3 border border-dashed border-success-subtle" id="recommendation-summary-text">
                    Pilih periode dan jenis pupuk untuk melihat analisis rekomendasi relokasi.
                </div>
            </div>
        </div>

        <!-- Tab Chart -->
        <div class="tab-pane fade" id="chart-pane" role="tabpanel" aria-labelledby="chart-tab" tabindex="0">
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label for="chartKecamatan" class="form-label fw-semibold text-secondary small">Filter Wilayah Kecamatan (Grafik)</label>
                    <select id="chartKecamatan" class="form-select rounded-3 shadow-sm border border-light-subtle">
                        <option value="">-- Semua Kecamatan --</option>
                        @foreach($kecamatans as $kec)
                            <option value="{{ $kec->id }}">{{ $kec->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Canvas Grafik Tahunan -->
            <div class="mb-4">
                <h6 class="fw-bold text-secondary mb-3"><i class="bi bi-calendar-event me-2"></i>Perbandingan Kuota vs Realisasi Tahunan (5 Tahun Terakhir)</h6>
                <div class="bg-white p-4 border border-light-subtle rounded-4 shadow-sm" style="position: relative; height: 320px;">
                    <canvas id="tahunanChart" style="width: 100%; height: 100%;"></canvas>
                </div>
            </div>

            <!-- Canvas Grafik Bulanan -->
            <div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-secondary mb-0"><i class="bi bi-calendar-range me-2"></i>Perkembangan Distribusi Bulanan</h6>
                    <div class="d-flex align-items-center gap-2">
                        <label for="chartTahunBulanan" class="small fw-semibold text-muted text-nowrap">Pilih Tahun:</label>
                        <select id="chartTahunBulanan" class="form-select form-select-sm rounded-3 border-light-subtle shadow-sm" style="width: 120px;">
                            @foreach($years as $yr)
                                <option value="{{ $yr }}" {{ date('Y') == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="bg-white p-4 border border-light-subtle rounded-4 shadow-sm" style="position: relative; height: 320px;">
                    <canvas id="bulananChart" style="width: 100%; height: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        // Toggle tab style
        $('#pupukTab button').on('click', function() {
            $('#pupukTab button').removeClass('text-success border-bottom border-3 border-success').addClass('text-secondary');
            $(this).addClass('text-success border-bottom border-3 border-success').removeClass('text-secondary');
        });

        // Load data table matrix
        const loadMatrixData = () => {
            const tahun = $('#filterTahun').val();
            const bulan = $('#filterBulan').val();
            const jenisPupukId = $('#filterJenisPupuk').val();

            $.ajax({
                url: "{{ route('distribusi-pupuk.ajax-laporan-data') }}",
                type: 'GET',
                data: {
                    tahun: tahun,
                    bulan: bulan,
                    jenis_pupuk_id: jenisPupukId
                },
                success: function(response) {
                    let html = '';
                    let totalKuota = 0, totalPenebusan = 0, totalMasuk = 0, totalKeluar = 0, totalSisa = 0;
                    const unitName = response.satuan_nama || 'Kg';
                    $('.unit-label').text('(' + unitName + ')');

                    if (response.data.length === 0) {
                        html = `<tr><td colspan="9" class="text-center text-muted">Tidak ada data untuk periode ini</td></tr>`;
                    } else {
                        response.data.forEach((row, index) => {
                            totalKuota += row.kuota;
                            totalPenebusan += row.penebusan;
                            totalMasuk += row.masuk;
                            totalKeluar += row.keluar;
                            totalSisa += row.sisa;

                            let badge = '';
                            if (row.persentase >= response.threshold) {
                                badge = `<span class="badge bg-success rounded-pill px-3 py-1">Tinggi (&ge;${response.threshold}%)</span>`;
                            } else if (row.persentase > 0) {
                                badge = `<span class="badge bg-warning text-dark rounded-pill px-3 py-1">Rendah (&lt;${response.threshold}%)</span>`;
                            } else {
                                badge = '<span class="badge bg-secondary rounded-pill px-3 py-1">Belum Ada</span>';
                            }

                            html += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td class="text-start fw-bold text-secondary">${row.nama}</td>
                                    <td class="text-end">${row.kuota.toLocaleString('id-ID', {minimumFractionDigits: 2})} ${unitName}</td>
                                    <td class="text-end">${row.penebusan.toLocaleString('id-ID', {minimumFractionDigits: 2})} ${unitName}</td>
                                    <td class="text-end text-success">+${row.masuk.toLocaleString('id-ID', {minimumFractionDigits: 2})} ${unitName}</td>
                                    <td class="text-end text-danger">-${row.keluar.toLocaleString('id-ID', {minimumFractionDigits: 2})} ${unitName}</td>
                                    <td class="text-end fw-bold">${row.sisa.toLocaleString('id-ID', {minimumFractionDigits: 2})} ${unitName}</td>
                                    <td class="fw-bold">${row.persentase}%</td>
                                    <td>${badge}</td>
                                </tr>
                            `;
                        });

                        const totalPercentage = totalKuota > 0 ? ((totalPenebusan / totalKuota) * 100).toFixed(2) : 0;
                        html += `
                            <tr class="table-warning fw-bold text-success">
                                <td colspan="2">TOTAL KABUPATEN</td>
                                <td class="text-end">${totalKuota.toLocaleString('id-ID', {minimumFractionDigits: 2})} ${unitName}</td>
                                <td class="text-end">${totalPenebusan.toLocaleString('id-ID', {minimumFractionDigits: 2})} ${unitName}</td>
                                <td class="text-end">${totalMasuk.toLocaleString('id-ID', {minimumFractionDigits: 2})} ${unitName}</td>
                                <td class="text-end">${totalKeluar.toLocaleString('id-ID', {minimumFractionDigits: 2})} ${unitName}</td>
                                <td class="text-end">${totalSisa.toLocaleString('id-ID', {minimumFractionDigits: 2})} ${unitName}</td>
                                <td>${totalPercentage}%</td>
                                <td></td>
                            </tr>
                        `;
                    }
                    $('#matrixBody').html(html);

                    // Generate annual progress table
                    let tahunanHtml = '';
                    let totKuotaTahun = 0, totPenebusanKum = 0, totMasukKum = 0, totKeluarKum = 0, totSisaTahun = 0;

                    if (response.data.length === 0) {
                        tahunanHtml = `<tr><td colspan="8" class="text-center text-muted">Tidak ada data progres tahunan untuk periode ini</td></tr>`;
                    } else {
                        response.data.forEach((row, index) => {
                            totKuotaTahun += row.kuota;
                            totPenebusanKum += row.penebusan_kumulatif;
                            totMasukKum += row.masuk_kumulatif;
                            totKeluarKum += row.keluar_kumulatif;
                            totSisaTahun += row.sisa_tahunan;

                            tahunanHtml += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td class="text-start fw-bold text-secondary">${row.nama}</td>
                                    <td class="text-end">${row.kuota.toLocaleString('id-ID', {minimumFractionDigits: 2})} ${unitName}</td>
                                    <td class="text-end text-success">${row.penebusan_kumulatif.toLocaleString('id-ID', {minimumFractionDigits: 2})} ${unitName}</td>
                                    <td class="text-end text-success">+${row.masuk_kumulatif.toLocaleString('id-ID', {minimumFractionDigits: 2})} ${unitName}</td>
                                    <td class="text-end text-danger">-${row.keluar_kumulatif.toLocaleString('id-ID', {minimumFractionDigits: 2})} ${unitName}</td>
                                    <td class="text-end fw-bold">${row.sisa_tahunan.toLocaleString('id-ID', {minimumFractionDigits: 2})} ${unitName}</td>
                                    <td class="fw-bold">${row.persentase_kumulatif}%</td>
                                </tr>
                            `;
                        });

                        const totPercentageKum = totKuotaTahun > 0 ? ((totPenebusanKum / totKuotaTahun) * 100).toFixed(2) : 0;
                        tahunanHtml += `
                            <tr class="table-warning fw-bold text-success">
                                <td colspan="2">TOTAL KABUPATEN</td>
                                <td class="text-end">${totKuotaTahun.toLocaleString('id-ID', {minimumFractionDigits: 2})} ${unitName}</td>
                                <td class="text-end">${totPenebusanKum.toLocaleString('id-ID', {minimumFractionDigits: 2})} ${unitName}</td>
                                <td class="text-end">${totMasukKum.toLocaleString('id-ID', {minimumFractionDigits: 2})} ${unitName}</td>
                                <td class="text-end">${totKeluarKum.toLocaleString('id-ID', {minimumFractionDigits: 2})} ${unitName}</td>
                                <td class="text-end">${totSisaTahun.toLocaleString('id-ID', {minimumFractionDigits: 2})} ${unitName}</td>
                                <td>${totPercentageKum}%</td>
                            </tr>
                        `;
                    }
                    $('#tahunanProgressBody').html(tahunanHtml);

                    // Generate recommendations based on the latest month with reports
                    $('#recommendation-title').html(`Rekomendasi Pengalihan / Relokasi Kuota Pupuk (Bulan Terakhir: ${response.latest_bulan_nama})`);
                    $('#recommendation-source-title').html(`<i class="bi bi-arrow-down-circle-fill me-1"></i>Kecamatan Sumber Relokasi (Penebusan &lt; ${response.threshold_latest}%)`);
                    $('#recommendation-target-title').html(`<i class="bi bi-arrow-up-circle-fill me-1"></i>Kecamatan Target Relokasi (Penebusan &ge; ${response.threshold_latest}%)`);

                    let sources = [];
                    let targets = [];

                    response.data.forEach(row => {
                        if (row.kuota > 0) {
                            if (row.persentase_latest < response.threshold_latest && row.sisa_latest > 0) {
                                sources.push(row);
                            } else if (row.persentase_latest >= response.threshold_latest) {
                                targets.push(row);
                            }
                        }
                    });

                    // Sort sources descending by sisa_latest (highest available first)
                    sources.sort((a, b) => b.sisa_latest - a.sisa_latest);
                    // Sort targets descending by percentage_latest (highest demand first)
                    targets.sort((a, b) => b.persentase_latest - a.persentase_latest);

                    let sourceHtml = '';
                    if (sources.length === 0) {
                        sourceHtml = `<p class="mb-0 text-muted">Tidak ada kecamatan dengan realisasi < ${response.threshold_latest}% yang memiliki sisa kuota.</p>`;
                    } else {
                        sourceHtml = '<ul class="mb-0 ps-3">';
                        sources.forEach(s => {
                            sourceHtml += `<li><strong>${s.nama}</strong>: Sisa ${s.sisa_latest.toLocaleString('id-ID', {maximumFractionDigits: 2})} ${unitName} (Realisasi: ${s.persentase_latest}%)</li>`;
                        });
                        sourceHtml += '</ul>';
                    }
                    $('#recommendation-source-list').html(sourceHtml);

                    let targetHtml = '';
                    if (targets.length === 0) {
                        targetHtml = `<p class="mb-0 text-muted">Tidak ada kecamatan dengan realisasi &ge; ${response.threshold_latest}%.</p>`;
                    } else {
                        targetHtml = '<ul class="mb-0 ps-3">';
                        targets.forEach(t => {
                            targetHtml += `<li><strong>${t.nama}</strong>: Realisasi ${t.persentase_latest}% (Disarankan menerima tambahan kuota)</li>`;
                        });
                        targetHtml += '</ul>';
                    }
                    $('#recommendation-target-list').html(targetHtml);

                    let summaryText = '';
                    if (sources.length > 0 && targets.length > 0) {
                        $('#recommendation-panel').removeClass('d-none');
                        summaryText = `<i class="bi bi-lightbulb-fill text-warning me-1"></i> Disarankan untuk mengalihkan sebagian kuota dari <strong>${sources[0].nama}</strong> (sisa kuota berjalan terbesar: ${sources[0].sisa_latest.toLocaleString('id-ID', {maximumFractionDigits: 2})} ${unitName}) ke <strong>${targets[0].nama}</strong> (realisasi tertinggi: ${targets[0].persentase_latest}%). <a href="{{ route('distribusi-pupuk.pengalihan.create') }}" class="text-success fw-bold text-decoration-none ms-1"><i class="bi bi-arrow-right-short"></i> Buat Form Pengalihan</a>`;
                    } else if (sources.length > 0) {
                        $('#recommendation-panel').removeClass('d-none');
                        summaryText = `<i class="bi bi-info-circle-fill text-secondary me-1"></i> Beberapa kecamatan memiliki realisasi rendah (&lt;75%) pada bulan ${response.latest_bulan_nama}, namun belum ada kecamatan dengan realisasi tinggi (&ge;75%) yang butuh relokasi pada periode tersebut.`;
                    } else {
                        $('#recommendation-panel').addClass('d-none');
                    }
                    $('#recommendation-summary-text').html(summaryText);
                }
            });
        };

        $('#filterTahun, #filterBulan, #filterJenisPupuk').on('change', loadMatrixData);
        loadMatrixData();

        // Chart.js integrations
        let tahunanChartInstance = null;
        let bulananChartInstance = null;

        const loadChartData = () => {
            const kecamatanId = $('#chartKecamatan').val();
            const jenisPupukId = $('#filterJenisPupuk').val();
            const tahunBulanan = $('#chartTahunBulanan').val();

            $.ajax({
                url: "{{ route('distribusi-pupuk.data-grafik') }}",
                type: 'GET',
                data: {
                    kecamatan_id: kecamatanId,
                    jenis_pupuk_id: jenisPupukId,
                    tahun_bulanan: tahunBulanan
                },
                success: function(response) {
                    const unitName = response.satuan_nama || 'Kg';

                    // Update Tahunan
                    const ctxTahunan = document.getElementById('tahunanChart').getContext('2d');
                    if (tahunanChartInstance) tahunanChartInstance.destroy();
                    tahunanChartInstance = new Chart(ctxTahunan, {
                        type: 'bar',
                        data: {
                            labels: response.years,
                            datasets: [
                                {
                                    label: 'Kuota (' + unitName + ')',
                                    data: response.kuota_tahunan,
                                    backgroundColor: 'rgba(16, 185, 129, 0.75)',
                                    borderColor: 'rgba(16, 185, 129, 1)',
                                    borderWidth: 1,
                                    borderRadius: 4
                                },
                                {
                                    label: 'Realisasi Penebusan (' + unitName + ')',
                                    data: response.penebusan_tahunan,
                                    backgroundColor: 'rgba(59, 130, 246, 0.75)',
                                    borderColor: 'rgba(59, 130, 246, 1)',
                                    borderWidth: 1,
                                    borderRadius: 4
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: { y: { beginAtZero: true } }
                        }
                    });

                    // Update Bulanan
                    const ctxBulanan = document.getElementById('bulananChart').getContext('2d');
                    if (bulananChartInstance) bulananChartInstance.destroy();
                    bulananChartInstance = new Chart(ctxBulanan, {
                        type: 'line',
                        data: {
                            labels: response.months,
                            datasets: [
                                {
                                    label: 'Kuota Tahunan (' + unitName + ')',
                                    data: response.kuota_bulanan,
                                    borderColor: '#10b981',
                                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                    fill: true,
                                    tension: 0.2
                                },
                                {
                                    label: 'Realisasi Penebusan (' + unitName + ')',
                                    data: response.penebusan_bulanan,
                                    borderColor: '#3b82f6',
                                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                    fill: true,
                                    tension: 0.2
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: { y: { beginAtZero: true } }
                        }
                    });
                }
            });
        };

        $('button[data-bs-target="#chart-pane"]').on('shown.bs.tab', loadChartData);
        $('#chartKecamatan, #filterJenisPupuk, #chartTahunBulanan').on('change', loadChartData);
    });
</script>
@endsection
