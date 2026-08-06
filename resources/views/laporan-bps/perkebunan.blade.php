@extends('layouts.admin')
@section('title', 'Laporan BPS – Perkebunan')

@section('content')
<x-breadcrumb :items="[['label'=>'Laporan BPS','url'=>route('laporan-bps.index')],['label'=>'Perkebunan']]" />

<div class="card custom-card border-0 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1"><i class="bi bi-tree me-2 text-warning"></i>Laporan BPS — Perkebunan {{ $tahun }}</h5>
            <p class="text-muted small mb-0">Laporan luas TBM, TM, TTM, produksi, dan jumlah petani.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('laporan-bps.perkebunan.excel', request()->query()) }}" class="btn btn-success rounded-3 px-3">
                <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
            </a>
            <a href="{{ route('laporan-bps.perkebunan.pdf', request()->query()) }}" class="btn btn-danger rounded-3 px-3" target="_blank">
                <i class="bi bi-file-earmark-pdf me-1"></i> Cetak PDF
            </a>
        </div>
    </div>

    <form method="GET" class="row g-3 mb-4 bg-light rounded-3 p-3 border border-light-subtle align-items-end">
        <div class="col-md-2">
            <label class="form-label fw-semibold text-secondary small">Tahun</label>
            <select name="tahun" class="form-select rounded-3 shadow-sm border-0" onchange="this.form.submit()">
                @foreach($years as $y)
                    <option value="{{ $y }}" {{ $y == $tahun ? 'selected':'' }}>{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold text-secondary small">Kecamatan</label>
            <select name="kecamatan_id" class="form-select rounded-3 shadow-sm border-0" onchange="this.form.submit()">
                <option value="">— Semua Kecamatan —</option>
                @foreach($kecamatans as $kec)
                    <option value="{{ $kec->id }}" {{ $kecamatanId == $kec->id ? 'selected':'' }}>{{ $kec->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold text-secondary small">Semester</label>
            <select name="bulan" class="form-select rounded-3 shadow-sm border-0" onchange="this.form.submit()">
                <option value="">— Semua Semester —</option>
                @foreach($semesters as $val => $label)
                    <option value="{{ $val }}" {{ $bulan == $val ? 'selected':'' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </form>

    @if($laporans->isEmpty())
        <div class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2"></i>Tidak ada data untuk filter yang dipilih.</div>
    @else
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle small">
            <thead class="table-light align-middle text-center fw-bold">
                <tr>
                    <th>No</th>
                    <th>Kecamatan</th>
                    <th>Komoditas</th>
                    <th>Semester</th>
                    <th>TBM (Ha)</th>
                    <th>TM (Ha)</th>
                    <th>TTM (Ha)</th>
                    <th>Total Luas (Ha)</th>
                    <th>Produksi</th>
                    <th>Wujud</th>
                    <th>Petani Pemilik</th>
                    <th>Petani BMU</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach($laporans as $lap)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $lap->kecamatan?->nama ?? '-' }}</td>
                    <td class="fw-semibold">{{ $lap->komoditas?->nama ?? '-' }}</td>
                    <td class="text-center">{{ $semesters[$lap->bulan] ?? $lap->bulan }}</td>
                    <td class="text-end">{{ number_format($lap->tbm ?? 0, 2) }}</td>
                    <td class="text-end">{{ number_format($lap->tm ?? 0, 2) }}</td>
                    <td class="text-end">{{ number_format($lap->ttm ?? 0, 2) }}</td>
                    <td class="text-end fw-semibold">{{ number_format($lap->luas_jumlah ?? 0, 2) }}</td>
                    <td class="text-end text-success fw-semibold">{{ number_format($lap->produksi ?? 0, 2) }}</td>
                    <td class="text-center">{{ $lap->wujud_produksi ?? '-' }}</td>
                    <td class="text-center">{{ $lap->jumlah_petani_pemilik ?? 0 }}</td>
                    <td class="text-center">{{ $lap->jumlah_petani_bmu ?? 0 }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="table-dark fw-bold">
                <tr>
                    <td colspan="4" class="text-end">TOTAL</td>
                    <td class="text-end">{{ number_format($laporans->sum('tbm'), 2) }}</td>
                    <td class="text-end">{{ number_format($laporans->sum('tm'), 2) }}</td>
                    <td class="text-end">{{ number_format($laporans->sum('ttm'), 2) }}</td>
                    <td class="text-end">{{ number_format($laporans->sum('luas_jumlah'), 2) }}</td>
                    <td class="text-end">{{ number_format($laporans->sum('produksi'), 2) }}</td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif
</div>
@endsection
