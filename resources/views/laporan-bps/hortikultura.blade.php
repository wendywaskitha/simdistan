@extends('layouts.admin')
@section('title', 'Laporan BPS – Hortikultura')

@section('content')
<x-breadcrumb :items="[['label'=>'Laporan BPS','url'=>route('laporan-bps.index')],['label'=>'Hortikultura']]" />

<div class="card custom-card border-0 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1"><i class="bi bi-tree-fill me-2 text-primary"></i>Laporan BPS — Hortikultura {{ $tahun }}</h5>
            <p class="text-muted small mb-0">Laporan produksi SPH-SBS, SPH-BST, SPH-TBF per kecamatan.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('laporan-bps.hortikultura.excel', request()->query()) }}" class="btn btn-success rounded-3 px-3">
                <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
            </a>
            <a href="{{ route('laporan-bps.hortikultura.pdf', request()->query()) }}" class="btn btn-danger rounded-3 px-3" target="_blank">
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
            <label class="form-label fw-semibold text-secondary small">Form</label>
            <select name="form_type" class="form-select rounded-3 shadow-sm border-0" onchange="this.form.submit()">
                <option value="">— Semua Form —</option>
                @foreach($formTypes as $val => $label)
                    <option value="{{ $val }}" {{ $formType == $val ? 'selected':'' }}>{{ $label }}</option>
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
                    <th>Form</th>
                    <th>Semester</th>
                    <th>Luas Tanam Akhir / Jml Pohon</th>
                    <th>Luas Panen (Ha)</th>
                    <th>Produksi</th>
                    <th>Satuan</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $semesters = [1=>'Sem. I',2=>'Sem. II'];
                    $no = 1;
                @endphp
                @foreach($laporans as $lap)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $lap->kecamatan?->nama ?? '-' }}</td>
                    <td class="fw-semibold">{{ $lap->komoditas?->nama ?? '-' }}</td>
                    <td class="text-center"><span class="badge bg-primary-subtle text-primary">{{ $lap->form_type ?? '-' }}</span></td>
                    <td class="text-center">{{ $semesters[$lap->bulan] ?? $lap->bulan }}</td>
                    <td class="text-end">{{ number_format($lap->luas_tanam_akhir ?? $lap->jumlah_tanaman_menghasilkan ?? 0, 2) }}</td>
                    <td class="text-end">{{ number_format($lap->luas_panen ?? 0, 2) }}</td>
                    <td class="text-end fw-semibold">{{ number_format($lap->produksi ?? 0, 2) }}</td>
                    <td class="text-center">{{ $lap->satuan?->nama ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="table-dark fw-bold">
                <tr>
                    <td colspan="6" class="text-end">TOTAL</td>
                    <td class="text-end">{{ number_format($laporans->sum('luas_panen'),2) }}</td>
                    <td class="text-end">{{ number_format($laporans->sum('produksi'),2) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif
</div>
@endsection
