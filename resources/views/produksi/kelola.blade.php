@extends('layouts.admin')

@section('title', 'Kelola Laporan Bulanan - ' . $komoditas->nama)

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Laporan Produksi', 'url' => route('laporan-produksis.index', ['kategori_id' => $kategori->id])],
    ['label' => 'Matriks Kecamatan Bulanan']
]" />

<div class="card custom-card border-0 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <div>
            <h5 class="fw-bold mb-1">Matriks Bulanan Kecamatan</h5>
            <p class="text-muted small mb-0">Komoditas: <span class="text-success fw-bold">{{ $komoditas->nama }}</span> | Kategori: {{ $kategori->nama }}</p>
        </div>
        <a href="{{ route('laporan-produksis.index', ['kategori_id' => $kategori->id]) }}" class="btn btn-light rounded-3 text-secondary border">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <!-- Filter Tahun & Indikator -->
    <div class="row g-3 mb-4 bg-light rounded-3 p-3 border border-light-subtle">
        <div class="col-md-3">
            <label for="filterTahun" class="form-label fw-semibold text-secondary small">Pilih Tahun</label>
            <select id="filterTahun" class="form-select border-0 shadow-sm rounded-3" onchange="changeTahun(this.value)">
                @foreach($years as $yr)
                    <option value="{{ $yr }}" {{ $tahun == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label for="filterIndikator" class="form-label fw-semibold text-secondary small">Pilih Indikator Tampilan</label>
            <select id="filterIndikator" class="form-select border-0 shadow-sm rounded-3" onchange="renderMatrix(this.value)">
                <option value="luas_tanam">Luas Tanam (Ha)</option>
                <option value="luas_panen">Luas Panen (Ha)</option>
                <option value="produksi">Hasil Produksi</option>
            </select>
        </div>
    </div>

    <!-- Tabel Matriks Bulanan -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle text-center small" id="matrixTable" style="width: 100%;">
            <thead class="table-light align-middle fw-bold">
                <tr>
                    <th width="15%" class="text-start">Kecamatan</th>
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
                    <th width="10%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $months = range(1, 12);
                    $totals = array_fill_keys($months, 0);
                    $rawLaporans = $laporans->toArray();
                @endphp

                @foreach($kecamatans as $kecamatan)
                    <tr class="kec-row" data-kec-id="{{ $kecamatan->id }}">
                        <td class="text-start fw-semibold text-secondary">{{ $kecamatan->nama }}</td>
                        @foreach($months as $m)
                            @php
                                $lap = $laporans->where('kecamatan_id', $kecamatan->id)->where('bulan', $m)->first();
                            @endphp
                            <td class="cell-val" 
                                data-tanam="{{ $lap ? floatval($lap->luas_tanam) : 0 }}" 
                                data-panen="{{ $lap ? floatval($lap->luas_panen) : 0 }}" 
                                data-produksi="{{ $lap ? floatval($lap->produksi) : 0 }}">
                                0.00
                            </td>
                        @endforeach
                        <td>
                            <a href="{{ route('laporan-produksis.input-mingguan', ['kecamatan_id' => $kecamatan->id, 'komoditas_id' => $komoditas->id, 'tahun' => $tahun]) }}" 
                               class="btn btn-sm btn-outline-success rounded-3 px-3 py-1 fw-bold">
                                Kelola/Edit
                            </a>
                        </td>
                    </tr>
                @endforeach
                <!-- Baris Total Bulanan -->
                <tr class="table-warning fw-bold text-success">
                    <td class="text-start">Total perbulan</td>
                    @foreach($months as $m)
                        <td id="total-month-{{ $m }}">0.00</td>
                    @endforeach
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const changeTahun = (val) => {
        window.location.href = "{{ route('laporan-produksis.kelola') }}?komoditas_id={{ $komoditas->id }}&tahun=" + val;
    };

    const renderMatrix = (indicator) => {
        let totals = Array(13).fill(0);

        $('.kec-row').each(function() {
            $(this).find('.cell-val').each(function(index) {
                const month = index + 1;
                const tanam = parseFloat($(this).attr('data-tanam')) || 0;
                const panen = parseFloat($(this).attr('data-panen')) || 0;
                const produksi = parseFloat($(this).attr('data-produksi')) || 0;

                let displayVal = 0;
                if (indicator === 'luas_tanam') {
                    displayVal = tanam;
                } else if (indicator === 'luas_panen') {
                    displayVal = panen;
                } else if (indicator === 'produksi') {
                    displayVal = produksi;
                }

                $(this).text(displayVal > 0 ? displayVal.toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '-');
                totals[month] += displayVal;
            });
        });

        // Update total row in footer
        for (let m = 1; m <= 12; m++) {
            const sum = totals[m];
            $(`#total-month-${m}`).text(sum > 0 ? sum.toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0.00');
        }
    };

    $(document).ready(function() {
        renderMatrix('luas_tanam'); // Render default
    });
</script>
<style>
    #matrixTable th, #matrixTable td {
        vertical-align: middle !important;
        font-size: 0.88rem !important;
    }
</style>
@endsection
