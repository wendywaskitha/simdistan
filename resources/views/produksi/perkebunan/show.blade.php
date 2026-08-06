@extends('layouts.admin')

@section('title', 'Detail Laporan Produksi - ' . $kategori->nama)

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Laporan Produksi', 'url' => route('perkebunan.index')],
    ['label' => 'Detail Laporan ' . $kategori->nama]
]" />

<div class="card custom-card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
    {{-- Header Banner --}}
    <div class="px-4 py-3 d-flex align-items-center justify-content-between"
         style="background: linear-gradient(135deg, #1e3a8a, #3b82f6);">
        <div>
            <h5 class="fw-bold text-white mb-0">
                <i class="bi bi-eye me-2"></i>
                Detail Laporan Produksi Perkebunan Rakyat (Read-Only)
            </h5>
            <p class="text-white-50 small mb-0">Informasi detail statistik mutasi luas areal dan produksi tanaman perkebunan</p>
        </div>
        <span class="badge fs-6 px-3 py-2 rounded-pill" style="background: rgba(255,255,255,0.2); color: #fff; letter-spacing: 1px;">SPH-BUN</span>
    </div>

    <div class="p-4">
        {{-- Metadata Utama (Read-only) --}}
        <div class="row g-3 mb-4 bg-light rounded-3 p-3 border border-light-subtle">
            <div class="col-md-3">
                <label class="form-label fw-semibold text-secondary small">Wilayah Kecamatan</label>
                <input type="text" class="form-control border-0 shadow-sm bg-white" value="{{ $laporan->kecamatan->nama }}" disabled>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold text-secondary small">Periode Semester</label>
                <input type="text" class="form-control border-0 shadow-sm bg-white" value="{{ $semesters[$laporan->bulan] ?? '-' }}" disabled>
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

        {{-- Tabel Matriks Perkebunan Read-Only --}}
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0" style="min-width: 1400px;">
                <thead class="text-center small fw-bold table-light">
                    <tr>
                        <th rowspan="3" style="min-width:150px; vertical-align: middle;">Jenis Komoditas</th>
                        <th rowspan="3" style="min-width:110px; vertical-align: middle; background:#fef08a;">(3) Luas Areal Akhir Tahun/Smt Lalu (Ha)</th>
                        <th colspan="4" style="min-width:380px;">Mutasi Luas Areal Dalam Tahun Laporan (Ha)</th>
                        <th colspan="3" style="min-width:270px;">Kondisi Areal Akhir Periode (Ha)</th>
                        <th colspan="2" style="min-width:200px;">Produksi (Kg)</th>
                        <th rowspan="3" style="min-width:120px; vertical-align: middle;">(16) Wujud Produksi</th>
                        <th colspan="2" style="min-width:160px;">Jumlah Petani</th>
                    </tr>
                    <tr>
                        <th rowspan="2" style="min-width:90px; vertical-align: middle;">(4) Tanam Ulang</th>
                        <th rowspan="2" style="min-width:90px; vertical-align: middle;">(5) Tanam Baru</th>
                        <th rowspan="2" style="min-width:90px; vertical-align: middle;">(6) Pengurangan</th>
                        <th rowspan="2" style="min-width:110px; vertical-align: middle; background:#fef08a;">(7) Jumlah Areal = (3)+(5)-(6)</th>
                        <th rowspan="2" style="min-width:90px; vertical-align: middle;">(8) TBM</th>
                        <th rowspan="2" style="min-width:90px; vertical-align: middle;">(9) TM (Panen)</th>
                        <th rowspan="2" style="min-width:90px; vertical-align: middle;">(10) TTM / Rusak</th>
                        <th rowspan="2" style="min-width:100px; vertical-align: middle;">(12) Akhir Tahun Lalu</th>
                        <th rowspan="2" style="min-width:100px; vertical-align: middle;">(14) Tahun Laporan</th>
                        <th style="min-width:80px;">(17) Pemilik</th>
                        <th style="min-width:80px;">(18) Penggarap (BMU)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="fw-semibold text-secondary small">
                            <i class="bi bi-circle-fill me-1 text-success" style="font-size:6px;"></i>{{ $laporan->komoditas->nama }}
                        </td>
                        {{-- (3) Luas Lalu --}}
                        <td class="p-1" style="background:#fef08a;">
                            <input type="text" class="form-control form-control-sm text-end border-0 bg-transparent text-dark" value="{{ number_format($laporan->luas_akhir_tahun_lalu ?? 0, 2) }}" disabled>
                        </td>
                        {{-- (4) Tanam Ulang --}}
                        <td class="p-1">
                            <input type="text" class="form-control form-control-sm text-end border-0 bg-white" value="{{ number_format($laporan->tanam_ulang ?? 0, 2) }}" disabled>
                        </td>
                        {{-- (5) Tanam Baru --}}
                        <td class="p-1">
                            <input type="text" class="form-control form-control-sm text-end border-0 bg-white" value="{{ number_format($laporan->tanam_baru ?? 0, 2) }}" disabled>
                        </td>
                        {{-- (6) Pengurangan --}}
                        <td class="p-1">
                            <input type="text" class="form-control form-control-sm text-end border-0 bg-white" value="{{ number_format($laporan->pengurangan ?? 0, 2) }}" disabled>
                        </td>
                        {{-- (7) Jumlah Areal --}}
                        <td class="p-1" style="background:#fef08a;">
                            <input type="text" class="form-control form-control-sm text-end border-0 bg-transparent text-dark fw-bold" value="{{ number_format($laporan->luas_jumlah ?? 0, 2) }}" disabled>
                        </td>
                        {{-- (8) TBM --}}
                        <td class="p-1">
                            <input type="text" class="form-control form-control-sm text-end border-0 bg-white" value="{{ number_format($laporan->tbm ?? 0, 2) }}" disabled>
                        </td>
                        {{-- (9) TM --}}
                        <td class="p-1">
                            <input type="text" class="form-control form-control-sm text-end border-0 bg-white" value="{{ number_format($laporan->luas_panen ?? 0, 2) }}" disabled>
                        </td>
                        {{-- (10) TTM --}}
                        <td class="p-1">
                            <input type="text" class="form-control form-control-sm text-end border-0 bg-white" value="{{ number_format($laporan->ttm ?? 0, 2) }}" disabled>
                        </td>
                        {{-- (12) Akhir Tahun Lalu --}}
                        <td class="p-1">
                            <input type="text" class="form-control form-control-sm text-end border-0 bg-white" value="{{ number_format($laporan->produksi_akhir_tahun_lalu ?? 0, 2) }}" disabled>
                        </td>
                        {{-- (14) Tahun Laporan --}}
                        <td class="p-1">
                            <input type="text" class="form-control form-control-sm text-end border-0 bg-white" value="{{ number_format($laporan->produksi ?? 0, 2) }}" disabled>
                        </td>
                        {{-- (16) Wujud --}}
                        <td class="p-1">
                            <input type="text" class="form-control form-control-sm text-center border-0 bg-white" value="{{ $laporan->wujud_produksi ?? '-' }}" disabled>
                        </td>
                        {{-- (17) Pemilik --}}
                        <td class="p-1">
                            <input type="text" class="form-control form-control-sm text-end border-0 bg-white" value="{{ number_format($laporan->jumlah_petani_pemilik ?? 0) }}" disabled>
                        </td>
                        {{-- (18) BMU --}}
                        <td class="p-1">
                            <input type="text" class="form-control form-control-sm text-end border-0 bg-white" value="{{ number_format($laporan->jumlah_petani_bmu ?? 0) }}" disabled>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Action Buttons --}}
        <div class="d-flex gap-2 pt-4 mt-4 border-top">
            <a href="{{ route('perkebunan.edit', $laporan->id) }}" class="btn btn-warning px-4 rounded-3 shadow-sm text-white">
                <i class="bi bi-pencil-square me-2"></i>Edit Laporan
            </a>
            <a href="{{ route('perkebunan.index') }}" class="btn btn-light px-4 rounded-3 text-secondary border">
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
