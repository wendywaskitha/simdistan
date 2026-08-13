@extends('layouts.admin')

@section('title', 'Statistik & Grafik Analisis')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Statistik & Grafik']
]" />

<div class="card custom-card border-0 shadow-sm p-4 mb-4" style="border-radius:16px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-1"><i class="bi bi-graph-up-arrow me-2 text-primary"></i>Statistik &amp; Grafik Analisis</h5>
            <p class="text-muted small mb-0">Visualisasi statistik data produksi komoditas pertanian, alat mesin pertanian, infrastruktur, dan pupuk bersubsidi.</p>
        </div>
    </div>

    {{-- Filter Global --}}
    <div class="row g-3 bg-light rounded-3 p-3 border border-light-subtle align-items-end mb-4">
        <div class="col-md-4">
            <label for="filterKecamatan" class="form-label fw-semibold text-secondary small">Filter Wilayah Kecamatan</label>
            <select id="filterKecamatan" class="form-select border-0 shadow-sm rounded-3">
                <option value="">-- Semua Kecamatan --</option>
                @foreach($kecamatans as $kec)
                    <option value="{{ $kec->id }}">{{ $kec->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label for="filterTahun" class="form-label fw-semibold text-secondary small">Tahun Analisis</label>
            <input type="number" id="filterTahun" class="form-control border-0 shadow-sm rounded-3" value="{{ $tahunDefault }}" min="2020" max="2050">
        </div>
        <div class="col-md-2">
            <button type="button" id="btnFilter" class="btn btn-primary w-100 rounded-3 shadow-sm">
                <i class="bi bi-filter me-1"></i> Terapkan
            </button>
        </div>
    </div>

    {{-- Nav Tabs --}}
    <ul class="nav nav-pills nav-fill gap-2 p-1 bg-light rounded-3 mb-4" id="statsTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-3" id="pangan-tab" data-bs-toggle="tab" data-bs-target="#pangan-pane" type="button" role="tab"><i class="bi bi-egg me-2"></i>Tanaman Pangan</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-3" id="horti-tab" data-bs-toggle="tab" data-bs-target="#horti-pane" type="button" role="tab"><i class="bi bi-flower1 me-2"></i>Hortikultura</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-3" id="bun-tab" data-bs-toggle="tab" data-bs-target="#bun-pane" type="button" role="tab"><i class="bi bi-tree me-2"></i>Perkebunan</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-3" id="alsintan-tab" data-bs-toggle="tab" data-bs-target="#alsintan-pane" type="button" role="tab"><i class="bi bi-cpu me-2"></i>Alsintan</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-3" id="infra-tab" data-bs-toggle="tab" data-bs-target="#infra-pane" type="button" role="tab"><i class="bi bi-activity me-2"></i>Infrastruktur</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-3" id="pupuk-tab" data-bs-toggle="tab" data-bs-target="#pupuk-pane" type="button" role="tab"><i class="bi bi-bag me-2"></i>Pupuk</button>
        </li>
    </ul>

    {{-- Tab Content --}}
    <div class="tab-content" id="statsTabContent">
        {{-- Tanaman Pangan --}}
        <div class="tab-pane fade show active" id="pangan-pane" role="tabpanel">
            <div class="row g-4">
                <!-- Row 1: Grafik Utama -->
                <div class="col-md-6">
                    <div class="card border border-light-subtle rounded-3 p-3 bg-white shadow-sm">
                        <h6 class="fw-bold mb-3 text-secondary"><i class="bi bi-calendar-check me-2"></i>Komparasi Luas Tanam vs Luas Panen (Ha)</h6>
                        <div style="height: 320px;"><canvas id="chartPanganTanamPanen"></canvas></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border border-light-subtle rounded-3 p-3 bg-white shadow-sm">
                        <h6 class="fw-bold mb-3 text-secondary"><i class="bi bi-graph-up me-2"></i>Tren Produksi &amp; Luas Panen Bulanan</h6>
                        <div style="height: 320px;"><canvas id="chartPanganTrenBulanan"></canvas></div>
                    </div>
                </div>

                <!-- Row 2: Distribusi & Target -->
                <div class="col-md-6">
                    <div class="card border border-light-subtle rounded-3 p-3 bg-white shadow-sm">
                        <h6 class="fw-bold mb-3 text-secondary"><i class="bi bi-pie-chart me-2"></i>Persentase Kontribusi Produksi per Komoditas</h6>
                        <div style="height: 320px;"><canvas id="chartPanganDonutProduksi"></canvas></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border border-light-subtle rounded-3 p-3 bg-white shadow-sm">
                        <h6 class="fw-bold mb-3 text-secondary"><i class="bi bi-bullseye me-2"></i>Realisasi Luas Tanam vs Target Tanam (Ha)</h6>
                        <div style="height: 320px;"><canvas id="chartPanganTargetTanam"></canvas></div>
                    </div>
                </div>

                <!-- Row 3: Kerusakan Lahan & Produktivitas -->
                <div class="col-md-6">
                    <div class="card border border-light-subtle rounded-3 p-3 bg-white shadow-sm">
                        <h6 class="fw-bold mb-3 text-secondary"><i class="bi bi-x-circle me-2"></i>Luas Lahan Rusak/Puso per Komoditas (Ha)</h6>
                        <div style="height: 320px;"><canvas id="chartPanganLahanRusak"></canvas></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border border-light-subtle rounded-3 p-3 bg-white shadow-sm">
                        <h6 class="fw-bold mb-3 text-secondary"><i class="bi bi-activity me-2"></i>Grafik Total Produksi Tanaman Pangan (Ton)</h6>
                        <div style="height: 320px;"><canvas id="chartPanganProduksi"></canvas></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Hortikultura --}}
        <div class="tab-pane fade" id="horti-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card border border-light-subtle rounded-3 p-3 bg-white shadow-sm">
                        <h6 class="fw-bold mb-3 text-secondary">Grafik Luas Panen / Areal Hortikultura</h6>
                        <div style="height: 320px;"><canvas id="chartHortiLuas"></canvas></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border border-light-subtle rounded-3 p-3 bg-white shadow-sm">
                        <h6 class="fw-bold mb-3 text-secondary">Grafik Total Produksi Hortikultura</h6>
                        <div style="height: 320px;"><canvas id="chartHortiProduksi"></canvas></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Perkebunan --}}
        <div class="tab-pane fade" id="bun-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card border border-light-subtle rounded-3 p-3 bg-white shadow-sm">
                        <h6 class="fw-bold mb-3 text-secondary">Grafik Luas Areal Perkebunan Rakyat (Ha)</h6>
                        <div style="height: 320px;"><canvas id="chartBunLuas"></canvas></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border border-light-subtle rounded-3 p-3 bg-white shadow-sm">
                        <h6 class="fw-bold mb-3 text-secondary">Grafik Hasil Produksi Perkebunan (Kg)</h6>
                        <div style="height: 320px;"><canvas id="chartBunProduksi"></canvas></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Alsintan --}}
        <div class="tab-pane fade" id="alsintan-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-md-8 mx-auto">
                    <div class="card border border-light-subtle rounded-3 p-3 bg-white shadow-sm">
                        <h6 class="fw-bold mb-3 text-secondary">Grafik Penyebaran Jumlah Unit Alsintan Berdasarkan Jenis Alat</h6>
                        <div style="height: 350px;"><canvas id="chartAlsintan"></canvas></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Infrastruktur --}}
        <div class="tab-pane fade" id="infra-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-md-8 mx-auto">
                    <div class="card border border-light-subtle rounded-3 p-3 bg-white shadow-sm">
                        <h6 class="fw-bold mb-3 text-secondary">Grafik Distribusi Jenis Bangunan Infrastruktur &amp; Irigasi</h6>
                        <div style="height: 350px;"><canvas id="chartInfra"></canvas></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pupuk --}}
        <div class="tab-pane fade" id="pupuk-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-md-8 mx-auto">
                    <div class="card border border-light-subtle rounded-3 p-3 bg-white shadow-sm">
                        <h6 class="fw-bold mb-3 text-secondary">Grafik Penyaluran Pupuk Bersubsidi (Ton)</h6>
                        <div style="height: 350px;"><canvas id="chartPupuk"></canvas></div>
                    </div>
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
    // Objek instansi chart global untuk mempermudah reset/update
    let charts = {};

    function initChart(canvasId, type, labels, data, label, color) {
        if (charts[canvasId]) {
            charts[canvasId].destroy();
        }
        const ctx = document.getElementById(canvasId).getContext('2d');
        
        let datasets = [];
        if (Array.isArray(data) && data.length > 0 && typeof data[0] === 'object' && data[0].datasets) {
            // Jika payload data berupa custom dataset terstruktur
            datasets = data[0].datasets;
        } else {
            // Mode dataset tunggal biasa
            datasets = [{
                label: label,
                data: data,
                backgroundColor: color,
                borderColor: typeof color === 'string' ? color.replace('0.6', '1') : color,
                borderWidth: 1.5,
                borderRadius: type === 'bar' ? 6 : 0
            }];
        }

        charts[canvasId] = new Chart(ctx, {
            type: type,
            data: {
                labels: labels,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: type === 'pie' || type === 'doughnut' || datasets.length > 1 }
                },
                scales: (type === 'bar' || type === 'line') ? {
                    y: { beginAtZero: true }
                } : {}
            }
        });
    }

    function loadProduksiStats() {
        const kecId = $('#filterKecamatan').val();
        const tahun = $('#filterTahun').val();

        $.ajax({
            url: "{{ route('statistik.data-produksi') }}",
            type: 'GET',
            data: { kecamatan_id: kecId, tahun: tahun },
            success: function(res) {
                // 1. DATA PANGAN (Detail lengkap)
                const pangan = res.pangan.stats || [];
                const panganLabels = pangan.map(item => item.komoditas_nama);
                const panganTanam = pangan.map(item => parseFloat(item.total_luas_tanam || 0));
                const panganPanen = pangan.map(item => parseFloat(item.total_luas_panen || 0));
                const panganRusak = pangan.map(item => parseFloat(item.total_luas_rusak || 0));
                const panganProd = pangan.map(item => parseFloat(item.total_produksi || 0));

                // A. Grafik Komparasi Luas Tanam vs Luas Panen (Bar Grouped)
                const tanamPanenDataset = [{
                    datasets: [
                        {
                            label: 'Luas Tanam (Ha)',
                            data: panganTanam,
                            backgroundColor: 'rgba(54, 162, 235, 0.6)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1.5,
                            borderRadius: 6
                        },
                        {
                            label: 'Luas Panen (Ha)',
                            data: panganPanen,
                            backgroundColor: 'rgba(75, 192, 192, 0.6)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1.5,
                            borderRadius: 6
                        }
                    ]
                }];
                initChart('chartPanganTanamPanen', 'bar', panganLabels, tanamPanenDataset);

                // B. Grafik Tren Bulanan (Jan - Des) (Line Chart Multi Axis)
                const bulanan = res.pangan.bulanan || [];
                const blnLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                const blnTanam = Array(12).fill(0);
                const blnPanen = Array(12).fill(0);
                const blnProd = Array(12).fill(0);
                
                bulanan.forEach(item => {
                    const idx = parseInt(item.bulan) - 1;
                    if (idx >= 0 && idx < 12) {
                        blnTanam[idx] = parseFloat(item.total_tanam || 0);
                        blnPanen[idx] = parseFloat(item.total_panen || 0);
                        blnProd[idx] = parseFloat(item.total_produksi || 0);
                    }
                });

                const trenBulananDataset = [{
                    datasets: [
                        {
                            label: 'Realisasi Produksi (Ton)',
                            data: blnProd,
                            type: 'line',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.3
                        },
                        {
                            label: 'Luas Panen (Ha)',
                            data: blnPanen,
                            type: 'bar',
                            backgroundColor: 'rgba(153, 102, 255, 0.6)',
                            borderColor: 'rgba(153, 102, 255, 1)',
                            borderWidth: 1.5,
                            borderRadius: 6
                        }
                    ]
                }];
                initChart('chartPanganTrenBulanan', 'bar', blnLabels, trenBulananDataset);

                // C. Donut Chart Kontribusi Produksi
                const donutColors = [
                    'rgba(255, 99, 132, 0.6)',
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(255, 206, 86, 0.6)',
                    'rgba(75, 192, 192, 0.6)',
                    'rgba(153, 102, 255, 0.6)',
                    'rgba(255, 159, 64, 0.6)'
                ];
                initChart('chartPanganDonutProduksi', 'doughnut', panganLabels, panganProd, 'Produksi (Ton)', donutColors);

                // D. Target vs Realisasi Tanam
                const targets = res.pangan.target || [];
                const targetValues = panganLabels.map(lbl => {
                    const match = targets.find(t => t.komoditas_nama === lbl);
                    return match ? parseFloat(match.total_target || 0) : 0;
                });

                const targetDataset = [{
                    datasets: [
                        {
                            label: 'Target Tanam (Ha)',
                            data: targetValues,
                            backgroundColor: 'rgba(255, 206, 86, 0.6)',
                            borderColor: 'rgba(255, 206, 86, 1)',
                            borderWidth: 1.5,
                            borderRadius: 6
                        },
                        {
                            label: 'Realisasi Tanam (Ha)',
                            data: panganTanam,
                            backgroundColor: 'rgba(75, 192, 192, 0.6)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1.5,
                            borderRadius: 6
                        }
                    ]
                }];
                initChart('chartPanganTargetTanam', 'bar', panganLabels, targetDataset);

                // E. Luas Lahan Rusak
                initChart('chartPanganLahanRusak', 'bar', panganLabels, panganRusak, 'Lahan Rusak/Puso (Ha)', 'rgba(220, 53, 69, 0.6)');

                // F. Total Produksi
                initChart('chartPanganProduksi', 'bar', panganLabels, panganProd, 'Total Produksi (Ton)', 'rgba(40, 167, 69, 0.6)');


                // 2. DATA HORTI (Tetap)
                const hortiLabels = res.horti.map(item => item.komoditas_nama);
                const hortiLuas = res.horti.map(item => parseFloat(item.total_luas_panen || 0));
                const hortiProd = res.horti.map(item => parseFloat(item.total_produksi || 0));

                initChart('chartHortiLuas', 'bar', hortiLabels, hortiLuas, 'Luas Panen (Ha)', 'rgba(236, 72, 153, 0.6)');
                initChart('chartHortiProduksi', 'bar', hortiLabels, hortiProd, 'Produksi', 'rgba(219, 39, 119, 0.6)');

                // 3. DATA PERKEBUNAN (Tetap)
                const bunLabels = res.bun.map(item => item.komoditas_nama);
                const bunLuas = res.bun.map(item => parseFloat(item.total_luas_panen || 0));
                const bunProd = res.bun.map(item => parseFloat(item.total_produksi || 0));

                initChart('chartBunLuas', 'bar', bunLabels, bunLuas, 'Luas Areal (Ha)', 'rgba(16, 185, 129, 0.6)');
                initChart('chartBunProduksi', 'bar', bunLabels, bunProd, 'Produksi (Kg)', 'rgba(5, 150, 105, 0.6)');
            }
        });
    }

    function loadAlsintanStats() {
        $.ajax({
            url: "{{ route('statistik.data-alsintan') }}",
            type: 'GET',
            success: function(res) {
                const labels = res.map(item => item.jenis);
                const totals = res.map(item => parseInt(item.total || 0));
                initChart('chartAlsintan', 'bar', labels, totals, 'Jumlah Unit', 'rgba(245, 158, 11, 0.6)');
            }
        });
    }

    function loadInfraStats() {
        $.ajax({
            url: "{{ route('statistik.data-infrastruktur') }}",
            type: 'GET',
            success: function(res) {
                const labels = res.map(item => item.jenis);
                const totals = res.map(item => parseInt(item.total || 0));
                initChart('chartInfra', 'doughnut', labels, totals, 'Jumlah Unit', [
                    'rgba(14, 165, 233, 0.6)',
                    'rgba(139, 92, 246, 0.6)',
                    'rgba(236, 72, 153, 0.6)',
                    'rgba(16, 185, 129, 0.6)'
                ]);
            }
        });
    }

    function loadPupukStats() {
        const tahun = $('#filterTahun').val();
        $.ajax({
            url: "{{ route('statistik.data-pupuk') }}",
            type: 'GET',
            data: { tahun: tahun },
            success: function(res) {
                const labels = res.map(item => item.jenis);
                const totals = res.map(item => parseFloat(item.total_penyaluran || 0));
                initChart('chartPupuk', 'bar', labels, totals, 'Penyaluran (Ton)', 'rgba(79, 70, 229, 0.6)');
            }
        });
    }

    // Load initial
    loadProduksiStats();
    loadAlsintanStats();
    loadInfraStats();
    loadPupukStats();

    $('#btnFilter').on('click', function() {
        loadProduksiStats();
        loadPupukStats();
    });
});
</script>
@endsection
