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

    <!-- Filter Kecamatan -->
    <div class="row g-3 mb-4 bg-light rounded-3 p-3 border border-light-subtle">
        <div class="col-md-4">
            <label for="filterKecamatan" class="form-label fw-semibold text-secondary small">Wilayah Kerja Kecamatan</label>
            <select id="filterKecamatan" class="form-select border-0 shadow-sm rounded-3">
                <option value="" selected>-- Semua Kecamatan --</option>
                @foreach($kecamatans as $id => $nama)
                    <option value="{{ $id }}">{{ $nama }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Nav Tabs Utama -->
    <ul class="nav nav-tabs nav-tabs-custom border-bottom mb-4" id="produksiTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold text-success border-0 border-bottom border-3 border-success" id="table-tab" data-bs-toggle="tab" data-bs-target="#table-pane" type="button" role="tab" aria-controls="table-pane" aria-selected="true">
                <i class="bi bi-table me-2"></i>Tabel Matriks
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold text-secondary border-0" id="chart-tab" data-bs-toggle="tab" data-bs-target="#chart-pane" type="button" role="tab" aria-controls="chart-pane" aria-selected="false">
                <i class="bi bi-bar-chart-line me-2"></i>Visualisasi Grafik
            </button>
        </li>
    </ul>

    <div class="tab-content" id="produksiTabContent">
        <!-- Pane Tabel Matriks -->
        <div class="tab-pane fade show active" id="table-pane" role="tabpanel" aria-labelledby="table-tab" tabindex="0">
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
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label for="chartKomoditas" class="form-label fw-semibold text-secondary small">Pilih Komoditas</label>
                    <select id="chartKomoditas" class="form-select rounded-3 shadow-sm border border-light-subtle">
                        @foreach($komoditasList as $k)
                            <option value="{{ $k->id }}">{{ $k->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="chartIndikator" class="form-label fw-semibold text-secondary small">Pilih Indikator</label>
                    <select id="chartIndikator" class="form-select rounded-3 shadow-sm border border-light-subtle">
                        <option value="luas_lahan">Luas Lahan (Ha)</option>
                        <option value="luas_tanam">Luas Tanam (Ha)</option>
                        <option value="luas_panen">Luas Panen (Ha)</option>
                        <option value="produksi">Hasil Produksi</option>
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

            <!-- Canvas Grafik Bulanan dengan Filter Tahun -->
            <div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-secondary mb-0"><i class="bi bi-calendar-range me-2"></i>Perkembangan Produksi Bulanan (Jan-Des)</h6>
                    <div class="d-flex align-items-center gap-2">
                        <label for="chartTahunBulanan" class="small fw-semibold text-muted text-nowrap">Pilih Tahun Grafik Bulanan:</label>
                        <select id="chartTahunBulanan" class="form-select form-select-sm rounded-3 border-light-subtle shadow-sm" style="width: 120px;">
                            @foreach($years as $tahun)
                                <option value="{{ $tahun }}" {{ $tahun == end($years) ? 'selected' : '' }}>{{ $tahun }}</option>
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

        const loadChartData = () => {
            const komoditasId = $('#chartKomoditas').val();
            const indicator = $('#chartIndikator').val();
            const kecamatanId = $('#filterKecamatan').val();
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
                }
            });
        };

        // Load data on click tab chart or change filters
        $('button[data-bs-target="#chart-pane"]').on('shown.bs.tab', loadChartData);
        $('#chartKomoditas, #chartIndikator, #chartTahunBulanan').on('change', loadChartData);
    });
</script>
<style>
    #laporanTable th, #laporanTable td {
        padding: 6px 4px !important;
        font-size: 0.82rem !important;
        white-space: nowrap;
    }
    .nav-tabs-custom .nav-link {
        transition: all 0.3s ease;
    }
    .nav-tabs-custom .nav-link.active {
        background: transparent !important;
    }
</style>
@endsection


