@extends('layouts.admin')

@section('title', 'Edit Laporan Produksi - ' . $kategori->nama)

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Laporan Produksi', 'url' => route('perkebunan.index')],
    ['label' => 'Edit Laporan ' . $kategori->nama]
]" />

<div class="card custom-card border-0 p-4">
    <div class="mb-4">
        <h5 class="fw-bold mb-1">Form Edit Laporan Produksi ({{ $kategori->nama }})</h5>
        <p class="text-muted small mb-0">Perbarui data laporan produksi komoditas <strong>{{ $laporan->komoditas->nama }}</strong>.</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4 shadow-sm" role="alert">
            <h6 class="fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i>Gagal Memperbarui Data:</h6>
            <ul class="mb-0 ps-3 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('perkebunan.update', $laporan->id) }}" method="POST" id="produksiForm">
        @csrf
        @method('PUT')
        <input type="hidden" name="kategori_komoditas_id" value="{{ $kategori->id }}">

        <div class="row g-3 mb-4 bg-light rounded-3 p-3 border border-light-subtle">
            <div class="col-md-3">
                <x-form.select 
                    name="kecamatan_id" 
                    label="Wilayah Kecamatan" 
                    placeholder="-- Pilih Kecamatan --" 
                    :options="$kecamatans"
                    selected="{{ $laporan->kecamatan_id }}"
                    required="true"
                />
            </div>
            <div class="col-md-3">
                <x-form.select 
                    name="bulan" 
                    label="Bulan Laporan" 
                    placeholder="-- Pilih Bulan --" 
                    :options="$months"
                    selected="{{ $laporan->bulan }}"
                    required="true"
                />
            </div>
            <div class="col-md-3">
                <x-form.input 
                    name="tahun" 
                    label="Tahun" 
                    type="number" 
                    value="{{ $laporan->tahun }}" 
                    required="true" 
                />
            </div>
            <div class="col-md-3">
                <x-form.select 
                    name="satuan_id" 
                    label="Satuan Ukur" 
                    placeholder="-- Pilih Satuan --" 
                    :options="$satuans"
                    selected="{{ $laporan->satuan_id }}"
                    required="true"
                />
            </div>
        </div>

        <div class="card border border-light-subtle rounded-3 p-4 bg-white shadow-sm mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                <h6 class="fw-bold mb-0 text-success text-uppercase" style="letter-spacing: 0.05em;">
                    <i class="bi bi-tag-fill me-2"></i>Komoditas: {{ $laporan->komoditas->nama }}
                </h6>
                <span class="badge bg-light text-secondary border">{{ $kategori->nama }}</span>
            </div>

            <!-- Input Bulanan Langsung -->
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-secondary small">Luas Tanam (Ha)</label>
                    <input type="number" step="0.01" min="0" 
                           name="komoditas[{{ $laporan->komoditas_id }}][luas_tanam]" 
                           value="{{ $laporan->luas_tanam }}"
                           class="form-control text-end" placeholder="0.00" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-secondary small">Luas Panen (Ha)</label>
                    <input type="number" step="0.01" min="0" 
                           name="komoditas[{{ $laporan->komoditas_id }}][luas_panen]" 
                           value="{{ $laporan->luas_panen }}"
                           class="form-control text-end" placeholder="0.00" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-secondary small">Hasil Produksi</label>
                    <input type="number" step="0.01" min="0" 
                           name="komoditas[{{ $laporan->komoditas_id }}][produksi]" 
                           value="{{ $laporan->produksi }}"
                           class="form-control text-end" placeholder="0.00" required>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success px-4 rounded-3">Perbarui Laporan</button>
            <a href="{{ route('perkebunan.index') }}" class="btn btn-light px-4 rounded-3 text-secondary border">Batal</a>
        </div>
    </form>
</div>
@endsection
