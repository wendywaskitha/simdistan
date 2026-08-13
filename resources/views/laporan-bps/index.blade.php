@extends('layouts.admin')
@section('title', 'Laporan')

@section('content')
<x-breadcrumb :items="[['label'=>'Laporan']]" />

<div class="card custom-card border-0 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1"><i class="bi bi-file-earmark-bar-graph me-2 text-success"></i>Laporan Data Pertanian</h5>
            <p class="text-muted small mb-0">Pilih bidang untuk melihat dan mengekspor laporan lengkap.</p>
        </div>
    </div>

    <div class="row g-4">
        {{-- Tanaman Pangan --}}
        @can('akses tanaman pangan')
        <div class="col-md-6 col-lg-3">
            <a href="{{ route('laporan-bps.tanaman-pangan') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 text-center p-4 rounded-4" style="transition:.2s;cursor:pointer;" onmouseenter="this.style.transform='translateY(-4px)'" onmouseleave="this.style.transform=''">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle" style="width:64px;height:64px;background:linear-gradient(135deg,#10b981,#059669);">
                        <i class="bi bi-cone-striped text-white fs-3"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Tanaman Pangan</h6>
                    <p class="text-muted small mb-0">Laporan produksi per komoditas & kecamatan.</p>
                    <span class="badge bg-success-subtle text-success mt-3 rounded-pill px-3">Lihat Laporan →</span>
                </div>
            </a>
        </div>
        @endcan

        {{-- Hortikultura --}}
        @can('akses hortikultura')
        <div class="col-md-6 col-lg-3">
            <a href="{{ route('laporan-bps.hortikultura') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 text-center p-4 rounded-4" style="transition:.2s;cursor:pointer;" onmouseenter="this.style.transform='translateY(-4px)'" onmouseleave="this.style.transform=''">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle" style="width:64px;height:64px;background:linear-gradient(135deg,#3b82f6,#1d4ed8);">
                        <i class="bi bi-tree-fill text-white fs-3"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Hortikultura</h6>
                    <p class="text-muted small mb-0">Laporan produksi SPH-SBS, BST, TBF.</p>
                    <span class="badge bg-primary-subtle text-primary mt-3 rounded-pill px-3">Lihat Laporan →</span>
                </div>
            </a>
        </div>
        @endcan

        {{-- Perkebunan --}}
        @can('akses perkebunan')
        <div class="col-md-6 col-lg-3">
            <a href="{{ route('laporan-bps.perkebunan') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 text-center p-4 rounded-4" style="transition:.2s;cursor:pointer;" onmouseenter="this.style.transform='translateY(-4px)'" onmouseleave="this.style.transform=''">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle" style="width:64px;height:64px;background:linear-gradient(135deg,#f59e0b,#d97706);">
                        <i class="bi bi-tree text-white fs-3"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Perkebunan</h6>
                    <p class="text-muted small mb-0">Laporan luas TBM, TM, TTM, produksi per semester.</p>
                    <span class="badge text-warning mt-3 rounded-pill px-3" style="background:rgba(245,158,11,.15);">Lihat Laporan →</span>
                </div>
            </a>
        </div>
        @endcan

        {{-- PSP --}}
        @can('akses psp')
        <div class="col-md-6 col-lg-3">
            <a href="{{ route('laporan-bps.psp') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 text-center p-4 rounded-4" style="transition:.2s;cursor:pointer;" onmouseenter="this.style.transform='translateY(-4px)'" onmouseleave="this.style.transform=''">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle" style="width:64px;height:64px;background:linear-gradient(135deg,#8b5cf6,#6d28d9);">
                        <i class="bi bi-truck-flatbed text-white fs-3"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">PSP</h6>
                    <p class="text-muted small mb-0">Alsintan, Infrastruktur & Irigasi, Distribusi Pupuk.</p>
                    <span class="badge text-purple mt-3 rounded-pill px-3" style="background:rgba(139,92,246,.15);color:#6d28d9;">Lihat Laporan →</span>
                </div>
            </a>
        </div>
        @endcan

        {{-- Penyuluhan --}}
        @can('akses penyuluhan')
        <div class="col-md-6 col-lg-3">
            <a href="{{ route('laporan-bps.penyuluhan') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 text-center p-4 rounded-4" style="transition:.2s;cursor:pointer;" onmouseenter="this.style.transform='translateY(-4px)'" onmouseleave="this.style.transform=''">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle" style="width:64px;height:64px;background:linear-gradient(135deg,#0d9488,#0f766e);">
                        <i class="bi bi-person-workspace text-white fs-3"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Penyuluhan</h6>
                    <p class="text-muted small mb-0">Penyuluh, Gapoktan, Kelompok Tani, Petani, BPP.</p>
                    <span class="badge mt-3 rounded-pill px-3" style="background:rgba(13,148,136,.15);color:#0d9488;">Lihat Laporan →</span>
                </div>
            </a>
        </div>
        @endcan
    </div>
</div>
@endsection
