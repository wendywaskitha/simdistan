@extends('layouts.admin')

@section('title', 'Beranda Utama')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Dashboard']
]" />

{{-- Banner Ucapan Selamat Datang Premium --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 16px; background: linear-gradient(135deg, #1e3a8a, #2563eb);">
            <div class="card-body p-4 p-md-5 text-white position-relative">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <span class="badge bg-white bg-opacity-20 px-3 py-2 rounded-pill mb-3 text-white small" style="letter-spacing: 0.5px;">SISTEM INFORMASI TERINTEGRASI</span>
                        <h3 class="fw-bold mb-2">Selamat Datang di SIM-Distan Muna Barat</h3>
                        <p class="mb-4 text-white-50">Portal Satu Data untuk memantau penyuluhan pertanian, mutasi komoditas pangan &amp; hortikultura, bantuan alsintan, dan pendistribusian pupuk bersubsidi secara realtime.</p>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('statistik.index') }}" class="btn btn-warning rounded-3 px-4 py-2 text-dark fw-semibold">
                                <i class="bi bi-graph-up-arrow me-1"></i> Analisis Statistik
                            </a>
                            <a href="{{ route('tanaman-pangan.create') }}" class="btn btn-outline-white bg-white bg-opacity-10 text-white border-white border-opacity-20 rounded-3 px-4 py-2">
                                <i class="bi bi-plus-circle me-1"></i> Tambah Laporan Produksi
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-4 d-none d-lg-block text-end">
                        <i class="bi bi-flower1" style="font-size: 8rem; opacity: 0.15; position: absolute; right: 40px; top: 10px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Widget Statistik Riil --}}
<div class="row mb-4">
    {{-- Petani --}}
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 12px; border-left: 5px solid #2563eb !important;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold d-block text-uppercase" style="letter-spacing:0.5px;">Petani Terdaftar</span>
                    <h3 class="fw-bold my-1 text-dark">{{ number_format($totalPetani) }}</h3>
                    <small class="text-secondary">KK terhimpun</small>
                </div>
                <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-3">
                    <i class="bi bi-person-check-fill fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Kelompok Tani --}}
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 12px; border-left: 5px solid #16a34a !important;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold d-block text-uppercase" style="letter-spacing:0.5px;">Kelompok Tani</span>
                    <h3 class="fw-bold my-1 text-dark">{{ number_format($totalPoktan) }}</h3>
                    <small class="text-secondary">Poktan terhimpun</small>
                </div>
                <div class="p-3 bg-success bg-opacity-10 text-success rounded-3">
                    <i class="bi bi-people-fill fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Penyuluh --}}
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 12px; border-left: 5px solid #ca8a04 !important;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold d-block text-uppercase" style="letter-spacing:0.5px;">Penyuluh Lapangan</span>
                    <h3 class="fw-bold my-1 text-dark">{{ number_format($totalPenyuluh) }}</h3>
                    <small class="text-secondary">Tersebar di BPP</small>
                </div>
                <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-3">
                    <i class="bi bi-person-workspace fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Alsintan --}}
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 12px; border-left: 5px solid #7c3aed !important;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold d-block text-uppercase" style="letter-spacing:0.5px;">Bantuan Alsintan</span>
                    <h3 class="fw-bold my-1 text-dark">{{ number_format($totalAlsintan) }}</h3>
                    <small class="text-secondary">Unit tersalurkan</small>
                </div>
                <div class="p-3 bg-purple bg-opacity-10 text-purple rounded-3" style="color: #7c3aed;">
                    <i class="bi bi-truck-flatbed fs-3"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Tabel 5 Laporan Terkini --}}
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm p-4 bg-white" style="border-radius:16px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Laporan Produksi Terkini</h5>
                <span class="badge bg-light text-dark border">Realtime</span>
            </div>
            <p class="small text-muted mb-3">Daftar laporan produksi komoditas pangan, hortikultura, dan perkebunan terbaru yang masuk ke dalam sistem.</p>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary small">
                        <tr>
                            <th>Kecamatan</th>
                            <th>Komoditas</th>
                            <th class="text-center">Periode</th>
                            <th class="text-end">Luas Panen</th>
                            <th class="text-end">Hasil Produksi</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        @forelse($laporanTerbaru as $lap)
                            <tr>
                                <td class="fw-semibold">{{ $lap->kecamatan_nama }}</td>
                                <td>
                                    {{ $lap->komoditas_nama }}
                                    <span class="badge bg-blue bg-opacity-10 text-primary ms-1" style="font-size: 10px;">{{ $lap->form_type ?? 'SBS' }}</span>
                                </td>
                                <td class="text-center">{{ $lap->bulan }}/{{ $lap->tahun }}</td>
                                <td class="text-end fw-semibold">{{ number_format($lap->luas_panen, 2, ',', '.') }} Ha</td>
                                <td class="text-end text-success fw-bold">{{ number_format($lap->produksi, 2, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">Belum ada laporan produksi terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Akses Cepat Modul --}}
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm p-4 bg-white" style="border-radius:16px; min-height: 380px;">
            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-lightning-charge-fill text-warning me-2"></i>Akses Cepat Modul</h5>
            <div class="d-flex flex-column gap-2">
                <a href="{{ route('tanaman-pangan.index') }}" class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3 text-decoration-none text-dark hover-shadow" style="transition: all 0.2s;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3"><i class="bi bi-egg-fill"></i></div>
                        <span class="fw-semibold">Tanaman Pangan</span>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>
                <a href="{{ route('hortikultura.index') }}" class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3 text-decoration-none text-dark hover-shadow" style="transition: all 0.2s;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-success bg-opacity-10 text-success p-2 rounded-3"><i class="bi bi-flower1"></i></div>
                        <span class="fw-semibold">Hortikultura</span>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>
                <a href="{{ route('perkebunan.index') }}" class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3 text-decoration-none text-dark hover-shadow" style="transition: all 0.2s;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-info bg-opacity-10 text-info p-2 rounded-3"><i class="bi bi-tree-fill"></i></div>
                        <span class="fw-semibold">Perkebunan</span>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>
                <a href="{{ route('distribusi-pupuk.index') }}" class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3 text-decoration-none text-dark hover-shadow" style="transition: all 0.2s;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-warning bg-opacity-10 text-warning p-2 rounded-3"><i class="bi bi-droplet-half"></i></div>
                        <span class="fw-semibold">Distribusi Pupuk</span>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
