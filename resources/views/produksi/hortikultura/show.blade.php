@extends('layouts.admin')

@section('title', 'Detail Laporan Produksi - ' . $kategori->nama)

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Laporan Produksi', 'url' => route('hortikultura.index')],
    ['label' => 'Detail Laporan ' . $kategori->nama]
]" />

@php
    $ft = $laporan->form_type ?? 'SPH-SBS';
    $bannerStyles = [
        'SPH-SBS' => ['bg' => 'linear-gradient(135deg, #1d4ed8, #3b82f6)', 'title' => 'Sayuran & Buah Semusim (SBS)', 'badge' => 'SPH-SBS'],
        'SPH-BST' => ['bg' => 'linear-gradient(135deg, #15803d, #22c55e)', 'title' => 'Buah-buahan & Sayuran Tahunan (BST)', 'badge' => 'SPH-BST'],
        'SPH-TBF' => ['bg' => 'linear-gradient(135deg, #6d28d9, #a78bfa)', 'title' => 'Tanaman Biofarmaka (TBF)', 'badge' => 'SPH-TBF']
    ];
    $style = $bannerStyles[$ft] ?? $bannerStyles['SPH-SBS'];
@endphp

<div class="card custom-card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
    {{-- Header Banner --}}
    <div class="px-4 py-3 d-flex align-items-center justify-content-between" style="background: {{ $style['bg'] }};">
        <div>
            <h5 class="fw-bold text-white mb-0">
                <i class="bi bi-eye me-2"></i>
                Detail Laporan Hortikultura — {{ $style['title'] }} (Read-Only)
            </h5>
            <p class="text-white-50 small mb-0">Informasi detail statistik luas tanam, luas panen, dan hasil produksi komoditas</p>
        </div>
        <span class="badge fs-6 px-3 py-2 rounded-pill" style="background: rgba(255,255,255,0.2); color: #fff; letter-spacing: 1px;">{{ $style['badge'] }}</span>
    </div>

    <div class="p-4">
        {{-- Metadata Utama (Read-only) --}}
        <div class="row g-3 mb-4 bg-light rounded-3 p-3 border border-light-subtle">
            <div class="col-md-3">
                <label class="form-label fw-semibold text-secondary small">Wilayah Kecamatan</label>
                <input type="text" class="form-control border-0 shadow-sm bg-white" value="{{ $laporan->kecamatan->nama }}" disabled>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold text-secondary small">Periode / Bulan</label>
                <input type="text" class="form-control border-0 shadow-sm bg-white" value="{{ $periods[$laporan->bulan] ?? '-' }}" disabled>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold text-secondary small">Tahun</label>
                <input type="text" class="form-control border-0 shadow-sm bg-white" value="{{ $laporan->tahun }}" disabled>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold text-secondary small">Satuan Ukur</label>
                <input type="text" class="form-control border-0 shadow-sm bg-white" value="{{ $laporan->satuan->nama }}" disabled>
            </div>
        </div>

        {{-- Detail Kolom Sesuai Form Type --}}
        @if ($ft === 'SPH-SBS')
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0" style="min-width: 1100px;">
                    <thead class="text-center small fw-bold table-light">
                        <tr>
                            <th rowspan="2" style="vertical-align: middle;">Nama Tanaman</th>
                            <th rowspan="2" style="background:#fef9c3; vertical-align: middle;">(3) Luas Tanam Akhir Bulan Lalu (Ha)</th>
                            <th colspan="2">Luas Panen (Ha)</th>
                            <th rowspan="2" style="vertical-align: middle;">(6) Luas Rusak / Puso (Ha)</th>
                            <th rowspan="2" style="vertical-align: middle;">(7) Penanaman Baru / Tambah Tanam (Ha)</th>
                            <th rowspan="2" style="background:#fef9c3; vertical-align: middle;">(8) Luas Tanam Akhir (Ha)</th>
                            <th colspan="2">Produksi (Kwintal)</th>
                            <th rowspan="2" style="vertical-align: middle;">(11) Rata-rata Harga Jual (Rp/Kg)</th>
                        </tr>
                        <tr>
                            <th>(4) Habis / Dibongkar</th>
                            <th>(5) Belum Habis</th>
                            <th>(9) Dipanen</th>
                            <th>(10) Belum Habis</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-semibold text-secondary small">{{ $laporan->komoditas->nama }}</td>
                            <td style="background:#fef9c3;" class="text-end fw-bold">{{ number_format($laporan->luas_tanam_akhir_bulan_lalu ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($laporan->luas_panen ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($laporan->luas_panen_belum_habis ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($laporan->luas_rusak ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($laporan->luas_tanam ?? 0, 2) }}</td>
                            <td style="background:#fef9c3;" class="text-end fw-bold text-primary">{{ number_format($laporan->luas_tanam_akhir ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($laporan->produksi ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($laporan->produksi_belum_habis ?? 0, 2) }}</td>
                            <td class="text-end">Rp {{ number_format($laporan->harga_jual ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        @elseif ($ft === 'SPH-BST')
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0" style="min-width: 1200px;">
                    <thead class="text-center small fw-bold table-light">
                        <tr>
                            <th rowspan="2" style="vertical-align: middle;">Nama Tanaman</th>
                            <th rowspan="2" style="background:#fef9c3; vertical-align: middle;">(3) Jml. Tanaman Akhir Triwulan Lalu</th>
                            <th rowspan="2" style="vertical-align: middle;">(4) Tanaman Dibongkar</th>
                            <th rowspan="2" style="vertical-align: middle;">(5) Penanaman Baru</th>
                            <th rowspan="2" style="background:#fef9c3; vertical-align: middle;">(6) Jml. Tanaman Akhir Triwulan</th>
                            <th colspan="3">Di Akhir Triwulan (Pohon/Rumpun)</th>
                            <th rowspan="2" style="vertical-align: middle;">(10) Produksi (Kw)</th>
                            <th rowspan="2" style="vertical-align: middle;">(11) Rata-rata Harga Jual (Rp/Kg)</th>
                        </tr>
                        <tr>
                            <th>(7) Blm Menghasilkan</th>
                            <th>(8) Menghasilkan</th>
                            <th>(9) Tua / Rusak</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-semibold text-secondary small">{{ $laporan->komoditas->nama }}</td>
                            <td style="background:#fef9c3;" class="text-end fw-bold">{{ number_format($laporan->jumlah_tanaman_akhir_triwulan_lalu ?? 0) }}</td>
                            <td class="text-end">{{ number_format($laporan->tanaman_dibongkar ?? 0) }}</td>
                            <td class="text-end">{{ number_format($laporan->tanaman_baru ?? 0) }}</td>
                            <td style="background:#fef9c3;" class="text-end fw-bold text-success">{{ number_format($laporan->jumlah_tanaman_menghasilkan ?? 0) }}</td>
                            <td class="text-end">{{ number_format($laporan->tanaman_tidak_menghasilkan ?? 0) }}</td>
                            <td class="text-end">{{ number_format($laporan->luas_tanam ?? 0) }}</td>
                            <td class="text-end">{{ number_format($laporan->tanaman_tus_rusak ?? 0) }}</td>
                            <td class="text-end">{{ number_format($laporan->produksi ?? 0, 2) }}</td>
                            <td class="text-end">Rp {{ number_format($laporan->harga_jual ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        @elseif ($ft === 'SPH-TBF')
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0" style="min-width: 1100px;">
                    <thead class="text-center small fw-bold table-light">
                        <tr>
                            <th rowspan="2" style="vertical-align: middle;">Nama Tanaman</th>
                            <th rowspan="2" style="background:#fef9c3; vertical-align: middle;">(3) Luas Tanam Akhir Triwulan Lalu (m²)</th>
                            <th colspan="2">Luas Panen (m²)</th>
                            <th rowspan="2" style="vertical-align: middle;">(6) Luas Rusak / Puso (m²)</th>
                            <th rowspan="2" style="vertical-align: middle;">(7) Penanaman Baru (m²)</th>
                            <th rowspan="2" style="background:#fef9c3; vertical-align: middle;">(8) Luas Tanam Akhir (m²)</th>
                            <th colspan="2">Produksi (Kg)</th>
                            <th rowspan="2" style="vertical-align: middle;">(11) Rata-rata Harga Jual (Rp/Kg)</th>
                        </tr>
                        <tr>
                            <th>(4) Habis / Dibongkar</th>
                            <th>(5) Belum Habis</th>
                            <th>(9) Dipanen</th>
                            <th>(10) Belum Habis</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-semibold text-secondary small">{{ $laporan->komoditas->nama }}</td>
                            <td style="background:#fef9c3;" class="text-end fw-bold">{{ number_format($laporan->luas_tanam_akhir_bulan_lalu ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($laporan->luas_panen ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($laporan->luas_panen_belum_habis ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($laporan->luas_rusak ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($laporan->luas_tanam ?? 0, 2) }}</td>
                            <td style="background:#fef9c3;" class="text-end fw-bold text-purple" style="color:#6d28d9;">{{ number_format($laporan->luas_tanam_akhir ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($laporan->produksi ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($laporan->produksi_belum_habis ?? 0, 2) }}</td>
                            <td class="text-end">Rp {{ number_format($laporan->harga_jual ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Action Buttons --}}
        <div class="d-flex gap-2 pt-4 mt-4 border-top">
            <a href="{{ route('hortikultura.edit', $laporan->id) }}" class="btn btn-warning px-4 rounded-3 shadow-sm text-white">
                <i class="bi bi-pencil-square me-2"></i>Edit Laporan
            </a>
            <a href="{{ route('hortikultura.index') }}" class="btn btn-light px-4 rounded-3 text-secondary border">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>
</div>
@endsection

<style>
    .table th, .table td {
        font-size: 0.78rem;
        padding: 6px 8px !important;
        vertical-align: middle;
    }
</style>
