@extends('layouts.admin')

@section('title', 'Kadin Executive Monitoring')

@section('content')
<div class="container-fluid py-4" style="background: #f8fafc; min-height: 100vh; font-family: 'Inter', sans-serif;">
    
    {{-- Header Banner Premium - Light Theme --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: #fff;">
        <div class="card-body p-4 p-md-5 d-flex align-items-center justify-content-between">
            <div>
                <span class="badge bg-white bg-opacity-20 px-3 py-2 rounded-pill mb-3 small text-white" style="letter-spacing: 1px; font-weight: 700;">DASHBOARD MONITORING KEPALA DINAS</span>
                <h2 class="fw-bold mb-1 text-white" style="font-size: 2.2rem;">Laporan Eksekutif Dinas Pertanian Muna Barat</h2>
                <p class="mb-0 text-white-50 fs-5">Pantau realisasi komoditas, alokasi pupuk bersubsidi, kondisi alsintan, dan sarana irigasi daerah.</p>
            </div>
            <div class="d-none d-lg-block text-end">
                <i class="bi bi-file-earmark-bar-graph text-white" style="font-size: 5rem; opacity: 0.35;"></i>
            </div>
        </div>
    </div>

    {{-- 3 Kartu Sekilas Info Terang Kontras Tinggi --}}
    <div class="row g-4 mb-4">
        {{-- Pangan --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 bg-white" style="border-radius: 16px; border-left: 6px solid #2563eb !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary fw-bold text-uppercase fs-6" style="letter-spacing: 0.5px;">TANAMAN PANGAN</span>
                    <span class="badge bg-primary bg-opacity-10 text-primary px-2.5 py-1.5 rounded-pill"><i class="bi bi-egg-fill"></i> Pangan</span>
                </div>
                <h1 class="fw-bold text-dark my-2" style="font-size: 2.4rem;">{{ number_format($panganProduksi, 2, ',', '.') }} <span class="fs-5 text-muted fw-normal">Ton</span></h1>
                <div class="d-flex justify-content-between text-muted fs-6">
                    <span>Luas Areal Panen:</span>
                    <span class="fw-bold text-dark">{{ number_format($panganLuas, 2, ',', '.') }} Ha</span>
                </div>
            </div>
        </div>

        {{-- Hortikultura --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 bg-white" style="border-radius: 16px; border-left: 6px solid #16a34a !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary fw-bold text-uppercase fs-6" style="letter-spacing: 0.5px;">HORTIKULTURA</span>
                    <span class="badge bg-success bg-opacity-10 text-success px-2.5 py-1.5 rounded-pill"><i class="bi bi-flower1"></i> Horti</span>
                </div>
                <h1 class="fw-bold text-dark my-2" style="font-size: 2.4rem;">{{ number_format($hortiProduksi, 2, ',', '.') }} <span class="fs-5 text-muted fw-normal">Kw/Kg</span></h1>
                <div class="d-flex justify-content-between text-muted fs-6">
                    <span>Luas Areal Panen:</span>
                    <span class="fw-bold text-dark">{{ number_format($hortiLuas, 2, ',', '.') }} Ha</span>
                </div>
            </div>
        </div>

        {{-- Perkebunan --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 bg-white" style="border-radius: 16px; border-left: 6px solid #d97706 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary fw-bold text-uppercase fs-6" style="letter-spacing: 0.5px;">PERKEBUNAN</span>
                    <span class="badge bg-warning bg-opacity-10 text-warning px-2.5 py-1.5 rounded-pill"><i class="bi bi-tree-fill"></i> Perkebunan</span>
                </div>
                <h1 class="fw-bold text-dark my-2" style="font-size: 2.4rem;">{{ number_format($bunProduksi, 2, ',', '.') }} <span class="fs-5 text-muted fw-normal">Kg</span></h1>
                <div class="d-flex justify-content-between text-muted fs-6">
                    <span>Luas Areal Panen:</span>
                    <span class="fw-bold text-dark">{{ number_format($bunLuas, 2, ',', '.') }} Ha</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Grafik Fluktuasi Produksi 5 Tahun dengan Filter --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm p-4 bg-white" style="border-radius: 16px;">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold text-dark mb-1" style="font-size:1.25rem;"><i class="bi bi-graph-up-arrow text-primary me-2"></i>Tren Fluktuasi Produksi Daerah (5 Tahun Terakhir)</h5>
                        <p class="small text-muted mb-0">Lihat tren perkembangan produksi per subsektor atau filter berdasarkan komoditas pilihan.</p>
                    </div>
                    
                    {{-- Dropdown Filter --}}
                    <div class="col-md-3 mt-3 mt-md-0">
                        <label class="form-label text-secondary small fw-bold mb-1">Filter Komoditas</label>
                        <select id="filterKomoditas" class="form-select border rounded-3 text-dark">
                            <option value="">-- Semua Subsektor --</option>
                            @foreach($komoditasDropdown as $kom)
                                <option value="{{ $kom->id }}">{{ $kom->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div style="height: 320px;" class="mb-4">
                    <canvas id="chartTrend5Tahun"></canvas>
                </div>

                {{-- Tabel Detail Angka Rincian di Bawah Grafik --}}
                <div class="mt-4 p-3 bg-light rounded-3 border">
                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-card-list me-1 text-primary"></i>Rincian Nilai Produksi Tahunan</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle text-center mb-0" id="tableTrendRincian">
                            <thead class="table-secondary">
                                <tr id="theadYears">
                                    <th>Kategori / Komoditas</th>
                                    @foreach($yearsList as $yr)
                                        <th>Th. {{ $yr }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody id="tbodyTrendRincian" class="fs-6 fw-semibold">
                                {{-- Diisi dinamis lewat JS --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Detail Produksi Komoditas & Aset --}}
    <div class="row g-4 mb-4">
        {{-- Detail Produksi --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm p-4 bg-white h-100" style="border-radius: 16px;">
                <h5 class="fw-bold text-dark mb-3" style="font-size:1.25rem;"><i class="bi bi-table text-primary me-2"></i>Tabel Monitoring Hasil Panen &amp; Komoditas</h5>
                <p class="small text-muted mb-4">Urutan komoditas berdasarkan total jumlah hasil produksi daerah terbanyak.</p>

                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-hover table-bordered align-middle mb-0">
                        <thead class="table-light sticky-top fs-6">
                            <tr>
                                <th>Nama Komoditas</th>
                                <th>Kategori</th>
                                <th class="text-end">Total Panen (Ha)</th>
                                <th class="text-end">Total Produksi</th>
                            </tr>
                        </thead>
                        <tbody class="fs-6">
                            @foreach($detailKomoditas as $det)
                                <tr>
                                    <td class="fw-bold text-dark">{{ $det->komoditas_nama }}</td>
                                    <td><span class="badge bg-light text-secondary border">{{ $det->kategori_nama }}</span></td>
                                    <td class="text-end">{{ number_format($det->total_luas, 2, ',', '.') }} Ha</td>
                                    <td class="text-end text-success fw-bold">{{ number_format($det->total_produksi, 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Monitoring Alsintan & Infrastruktur --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm p-4 bg-white h-100" style="border-radius: 16px;">
                <h5 class="fw-bold text-dark mb-3" style="font-size:1.25rem;"><i class="bi bi-truck-flatbed text-success me-2"></i>Status Aset &amp; Kondisi Alsintan</h5>
                <p class="small text-muted mb-4">Menampilkan jumlah unit alsintan berdasarkan kondisi kelayakan pakai.</p>

                <div class="row g-3 mb-4">
                    @foreach($alsintanKondisi as $con)
                        @php
                            $badgeClass = 'bg-success';
                            if($con->kondisi === 'Rusak Ringan') $badgeClass = 'bg-warning text-dark';
                            if($con->kondisi === 'Rusak Berat') $badgeClass = 'bg-danger';
                        @endphp
                        <div class="col-4 text-center">
                            <div class="p-3 bg-light rounded-3 border">
                                <span class="badge {{ $badgeClass }} mb-2 px-2.5 py-1.5 rounded-pill">{{ $con->kondisi }}</span>
                                <h3 class="fw-bold text-dark mb-0 my-1">{{ $con->total }}</h3>
                                <small class="text-muted">Unit</small>
                            </div>
                        </div>
                    @endforeach
                </div>

                <hr class="my-4">

                <h5 class="fw-bold text-dark mb-3" style="font-size:1.15rem;"><i class="bi bi-water text-info me-2"></i>Infrastruktur &amp; Irigasi Terkini</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>Nama Proyek</th>
                                <th>Kecamatan</th>
                                <th>Sumber Dana</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            @foreach($listInfrastruktur as $inf)
                                <tr>
                                    <td class="fw-semibold text-dark">{{ $inf->nama_proyek }}</td>
                                    <td>{{ $inf->kecamatan_nama }}</td>
                                    <td><span class="badge bg-light text-primary border">{{ $inf->sumber_dana }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Monitoring Realisasi Pupuk Bersubsidi --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm p-4 bg-white" style="border-radius: 16px;">
                <h5 class="fw-bold text-dark mb-3" style="font-size:1.25rem;"><i class="bi bi-droplet-half text-warning me-2"></i>Monitoring Penyaluran Pupuk Bersubsidi Per Kecamatan</h5>
                <p class="small text-muted mb-4">Total kuota penyaluran/penebusan pupuk bersubsidi yang terdata riil di tiap kios resmi.</p>

                <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                    <table class="table table-hover table-bordered align-middle mb-0">
                        <thead class="table-light fs-6">
                            <tr>
                                <th>Kecamatan</th>
                                <th>Jenis Pupuk</th>
                                <th class="text-end">Total Penebusan (Ton)</th>
                            </tr>
                        </thead>
                        <tbody class="fs-6">
                            @foreach($pupukRealisasi as $pup)
                                <tr>
                                    <td class="fw-bold text-dark">{{ $pup->kecamatan_nama }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $pup->jenis_pupuk }}</span></td>
                                    <td class="text-end text-primary fw-bold">{{ number_format($pup->total_penebusan, 2, ',', '.') }} Ton</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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
    let trendChart = null;

    // Data default semua kategori
    const years = {!! json_encode($yearsList) !!};
    const defaultPangan = {!! json_encode($panganTrend) !!};
    const defaultHorti = {!! json_encode($hortiTrend) !!};
    const defaultBun = {!! json_encode($bunTrend) !!};

    const trendCtx = document.getElementById('chartTrend5Tahun').getContext('2d');

    // Helper format rupiah/angka Indonesia
    function formatNumber(num) {
        return num.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // Fungsi Render Tabel Rincian
    function renderRincianTable(rows) {
        const tbody = $('#tbodyTrendRincian');
        tbody.empty();

        rows.forEach(row => {
            let cells = `<tr><td class="text-start fw-bold text-dark">${row.label}</td>`;
            row.data.forEach(val => {
                cells += `<td>${formatNumber(val)}</td>`;
            });
            cells += `</tr>`;
            tbody.append(cells);
        });
    }

    // Fungsi menggambar Chart
    function renderLineChart(labels, datasets) {
        if (trendChart) {
            trendChart.destroy();
        }
        trendChart = new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                        position: 'top',
                        labels: { font: { size: 13, weight: '600' } }
                    }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: { 
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: { callback: function(value) { return value.toLocaleString('id-ID'); } }
                    }
                }
            }
        });
    }

    // Set datasets default
    const defaultDatasets = [
        {
            label: 'Tanaman Pangan (Ton)',
            data: defaultPangan,
            borderColor: '#2563eb',
            backgroundColor: 'rgba(37, 99, 235, 0.1)',
            borderWidth: 3,
            tension: 0.35,
            fill: true
        },
        {
            label: 'Hortikultura (Kw/Kg)',
            data: defaultHorti,
            borderColor: '#16a34a',
            backgroundColor: 'rgba(22, 163, 74, 0.1)',
            borderWidth: 3,
            tension: 0.35,
            fill: true
        },
        {
            label: 'Perkebunan (Kg)',
            data: defaultBun,
            borderColor: '#d97706',
            backgroundColor: 'rgba(217, 119, 6, 0.1)',
            borderWidth: 3,
            tension: 0.35,
            fill: true
        }
    ];

    // Gambar chart pertama kali
    renderLineChart(years, defaultDatasets);

    // Render tabel pertama kali
    renderRincianTable([
        { label: 'Tanaman Pangan (Ton)', data: defaultPangan },
        { label: 'Hortikultura (Kw/Kg)', data: defaultHorti },
        { label: 'Perkebunan (Kg)', data: defaultBun }
    ]);

    // Event handler ketika filter komoditas berubah
    $('#filterKomoditas').on('change', function() {
        const komoditasId = $(this).val();

        if (komoditasId === '') {
            // Tampilkan kembali default
            renderLineChart(years, defaultDatasets);
            renderRincianTable([
                { label: 'Tanaman Pangan (Ton)', data: defaultPangan },
                { label: 'Hortikultura (Kw/Kg)', data: defaultHorti },
                { label: 'Perkebunan (Kg)', data: defaultBun }
            ]);
            return;
        }

        $(this).prop('disabled', true);

        $.ajax({
            url: "{{ route('dashboard.komoditas-trend') }}",
            type: "GET",
            data: { komoditas_id: komoditasId },
            success: function(response) {
                const komoditasName = $('#filterKomoditas option:selected').text();
                
                const filteredDatasets = [{
                    label: `Hasil Produksi ${komoditasName}`,
                    data: response.data,
                    borderColor: '#7c3aed',
                    backgroundColor: 'rgba(124, 58, 237, 0.1)',
                    borderWidth: 4,
                    tension: 0.3,
                    fill: true
                }];

                // Update Chart
                renderLineChart(response.years, filteredDatasets);

                // Update Tabel Rincian
                renderRincianTable([
                    { label: komoditasName, data: response.data }
                ]);
            },
            error: function() {
                alert('Gagal mengambil data tren komoditas.');
            },
            complete: function() {
                $('#filterKomoditas').prop('disabled', false);
            }
        });
    });
});
</script>

<style>
    .table th {
        font-size: 0.95rem !important;
        font-weight: 700 !important;
        color: #475569 !important;
    }
    .table td {
        font-size: 0.95rem !important;
    }
</style>
@endsection
