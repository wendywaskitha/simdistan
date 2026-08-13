@extends('layouts.admin')
@section('title', 'Laporan – Tanaman Pangan')

@section('content')
<x-breadcrumb :items="[['label'=>'Laporan','url'=>route('laporan-bps.index')],['label'=>'Tanaman Pangan']]" />

<div class="card custom-card border-0 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1"><i class="bi bi-cone-striped me-2 text-success"></i>Laporan — Tanaman Pangan {{ $tahun }}</h5>
            <p class="text-muted small mb-0">Rekap produksi per komoditas, per kecamatan, diakumulasi seluruh bulan dalam tahun terpilih.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('laporan-bps.tanaman-pangan.excel', request()->query()) }}" class="btn btn-success rounded-3 px-3">
                <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
            </a>
            <a href="{{ route('laporan-bps.tanaman-pangan.pdf', request()->query()) }}" class="btn btn-danger rounded-3 px-3" target="_blank">
                <i class="bi bi-file-earmark-pdf me-1"></i> Cetak PDF
            </a>
        </div>
    </div>

    {{-- Filter --}}
    <form method="GET" class="row g-3 mb-4 bg-light rounded-3 p-3 border border-light-subtle align-items-end">
        <div class="col-md-3">
            <label class="form-label fw-semibold text-secondary small">Tahun</label>
            <select name="tahun" class="form-select rounded-3 shadow-sm border-0" onchange="this.form.submit()">
                @foreach($years as $y)
                    <option value="{{ $y }}" {{ $y == $tahun ? 'selected':'' }}>{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-semibold text-secondary small">Kecamatan</label>
            <select name="kecamatan_id" class="form-select rounded-3 shadow-sm border-0" onchange="this.form.submit()">
                <option value="">— Semua Kecamatan —</option>
                @foreach($kecamatans as $kec)
                    <option value="{{ $kec->id }}" {{ $kecamatanId == $kec->id ? 'selected':'' }}>{{ $kec->nama }}</option>
                @endforeach
            </select>
        </div>
    </form>

    @if($grouped->isEmpty())
        <div class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2"></i>Tidak ada data untuk filter yang dipilih.</div>
    @else
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle small" id="laporanTable" style="width:100%">
            <thead class="table-light align-middle text-center fw-bold">
                <tr>
                    <th width="4%">No</th>
                    <th>Komoditas</th>
                    <th>Luas Lahan (Ha)</th>
                    <th>Luas Tanam (Ha)</th>
                    <th>Luas Panen (Ha)</th>
                    <th>Produksi (Ton)</th>
                    <th>Produktivitas (Ton/Ha)</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach($grouped as $group)
                <tr class="table-secondary">
                    <td colspan="7" class="fw-bold text-success small">
                        <i class="bi bi-geo-alt-fill me-1"></i>Kecamatan {{ $group['kecamatan']->nama }}
                    </td>
                </tr>
                @foreach($group['rows'] as $row)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td class="fw-semibold">{{ $row['komoditas']->nama }}</td>
                    <td class="text-end">{{ number_format($row['luas_lahan'],2) }}</td>
                    <td class="text-end">{{ number_format($row['luas_tanam'],2) }}</td>
                    <td class="text-end">{{ number_format($row['luas_panen'],2) }}</td>
                    <td class="text-end fw-semibold">{{ number_format($row['produksi'],2) }}</td>
                    <td class="text-end">{{ number_format($row['produktivitas'],4) }}</td>
                </tr>
                @endforeach
                <tr class="table-warning fw-bold">
                    <td colspan="2" class="text-end">Sub-Total {{ $group['kecamatan']->nama }}</td>
                    <td class="text-end">{{ number_format($group['total_lahan'],2) }}</td>
                    <td class="text-end">{{ number_format($group['total_tanam'],2) }}</td>
                    <td class="text-end">{{ number_format($group['total_panen'],2) }}</td>
                    <td class="text-end text-success">{{ number_format($group['total_produksi'],2) }}</td>
                    <td></td>
                </tr>
                @endforeach
                <tr class="table-dark fw-bold">
                    <td colspan="2" class="text-end">GRAND TOTAL</td>
                    <td class="text-end">{{ number_format($grouped->sum('total_lahan'),2) }}</td>
                    <td class="text-end">{{ number_format($grouped->sum('total_tanam'),2) }}</td>
                    <td class="text-end">{{ number_format($grouped->sum('total_panen'),2) }}</td>
                    <td class="text-end">{{ number_format($grouped->sum('total_produksi'),2) }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
