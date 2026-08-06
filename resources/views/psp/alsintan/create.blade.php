@extends('layouts.admin')

@section('title', 'Tambah Penerima Bantuan Alsintan')

@section('styles')
<!-- Tom Select CSS -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
    .ts-wrapper.form-select {
        border: none;
        padding: 0;
        height: auto;
    }
</style>
@endsection

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Prasarana & Sarana Pertanian (PSP)', 'url' => route('alsintans.index')],
    ['label' => 'Bantuan Alsintan', 'url' => route('alsintans.index')],
    ['label' => 'Tambah Bantuan']
]" />

@php
    $currentYear = (int) date('Y');
    $years = [];
    for ($i = 0; $i < 5; $i++) {
        $y = $currentYear - $i;
        $years[$y] = $y;
    }

    $kondisiOptions = [
        'Baik' => 'Baik',
        'Rusak Ringan' => 'Rusak Ringan',
        'Rusak Berat' => 'Rusak Berat'
    ];

    $sumberDanaOptions = [
        'APBD' => 'APBD',
        'APBN' => 'APBN',
        'DAK' => 'DAK',
        'BANPER' => 'BANPER',
        'MANDIRI' => 'MANDIRI'
    ];
@endphp

<div class="row">
    <div class="col-lg-10 col-xl-8">
        <div class="card custom-card border-0 p-4">
            <h5 class="fw-bold mb-4">Form Tambah Penerima Bantuan Alsintan</h5>

            <form action="{{ route('alsintans.store') }}" method="POST">
                @csrf

                <div class="card bg-light border-0 p-3 mb-4 rounded-3">
                    <label class="form-label fw-bold text-success small mb-2"><i class="bi bi-people-fill me-1"></i>Penerima Bantuan</label>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="kelompok_tani_id" class="form-label fw-semibold text-secondary">
                                    Kelompok Tani <span class="text-danger">*</span>
                                </label>
                                <select id="kelompok_tani_id" name="kelompok_tani_id" required class="form-select @error('kelompok_tani_id') is-invalid @enderror">
                                    <option value="" disabled {{ is_null(old('kelompok_tani_id')) ? 'selected' : '' }}>Pilih Kelompok Tani</option>
                                    @foreach($kelompokTanis as $item)
                                        <option value="{{ $item->id }}" data-ketua="{{ $item->ketua }}" {{ old('kelompok_tani_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->nama }} (Desa {{ $item->desa ? $item->desa->nama : '-' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('kelompok_tani_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <x-form.input 
                                name="nama_ketua" 
                                label="Nama Ketua Kelompok Tani" 
                                placeholder="Kosongkan untuk mengambil otomatis" 
                            />
                        </div>
                    </div>
                </div>

                <div class="card bg-light border-0 p-3 mb-4 rounded-3">
                    <label class="form-label fw-bold text-success small mb-2"><i class="bi bi-info-circle-fill me-1"></i>Spesifikasi Alat</label>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="jenis_alat_id" class="form-label fw-semibold text-secondary">
                                    Jenis Alat <span class="text-danger">*</span>
                                </label>
                                <select id="jenis_alat_id" name="jenis_alat_id" required class="form-select @error('jenis_alat_id') is-invalid @enderror">
                                    <option value="" disabled {{ is_null(old('jenis_alat_id')) ? 'selected' : '' }}>Pilih Jenis Alat</option>
                                    @foreach($jenisAlats as $jenis)
                                        <option value="{{ $jenis->id }}" {{ old('jenis_alat_id') == $jenis->id ? 'selected' : '' }}>
                                            {{ $jenis->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('jenis_alat_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <x-form.input 
                                name="nama_alat" 
                                label="Nama Alat" 
                                placeholder="Masukkan nama alat" 
                                required="true"
                            />
                        </div>
                    </div>
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <x-form.input 
                                name="merek" 
                                label="Merek" 
                                placeholder="Masukkan merek" 
                            />
                        </div>
                        <div class="col-md-6">
                            <x-form.select 
                                name="kondisi" 
                                label="Kondisi" 
                                :options="$kondisiOptions" 
                                selected="Baik"
                                required="true"
                            />
                        </div>
                    </div>
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <x-form.input 
                                name="nomor_rangka" 
                                label="Nomor Rangka" 
                                placeholder="Masukkan nomor rangka" 
                            />
                        </div>
                        <div class="col-md-6">
                            <x-form.input 
                                name="nomor_mesin" 
                                label="Nomor Mesin" 
                                placeholder="Masukkan nomor mesin" 
                            />
                        </div>
                    </div>
                </div>

                <div class="card bg-light border-0 p-3 mb-4 rounded-3">
                    <label class="form-label fw-bold text-success small mb-2"><i class="bi bi-cash-stack me-1"></i>Detail Pembiayaan & Operasional</label>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <x-form.select 
                                name="sumber_dana" 
                                label="Sumber Dana" 
                                :options="$sumberDanaOptions" 
                                placeholder="Pilih Sumber Dana"
                                required="true"
                            />
                        </div>
                        <div class="col-md-4">
                            <x-form.input 
                                name="harga" 
                                type="number" 
                                step="0.01" 
                                label="Harga (Rp)" 
                                placeholder="Masukkan harga" 
                                required="true"
                            />
                        </div>
                        <div class="col-md-4">
                            <x-form.select 
                                name="tahun_bantuan" 
                                label="Tahun Bantuan (5 Tahun Terakhir)" 
                                :options="$years" 
                                selected="{{ $currentYear }}"
                                required="true"
                            />
                        </div>
                    </div>
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <x-form.input 
                                name="nama_operator" 
                                label="Nama Operator" 
                                placeholder="Masukkan nama operator" 
                            />
                        </div>
                        <div class="col-md-6">
                            <x-form.input 
                                name="no_hp_operator" 
                                label="Nomor HP Operator" 
                                placeholder="Masukkan nomor HP operator" 
                            />
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-success px-4 rounded-3">Simpan</button>
                    <a href="{{ route('alsintans.index') }}" class="btn btn-light px-4 rounded-3 text-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Tom Select JS -->
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ktSelect = new TomSelect('#kelompok_tani_id', {
            create: false,
            onChange: function(value) {
                const option = ktSelect.options[value];
                if (option) {
                    // Extract data-ketua attribute from the option element
                    const optElement = option.$option;
                    const ketua = optElement ? optElement.getAttribute('data-ketua') : '';
                    document.getElementById('nama_ketua').value = ketua || '';
                } else {
                    document.getElementById('nama_ketua').value = '';
                }
            }
        });

        new TomSelect('#jenis_alat_id', {
            create: false
        });
    });
</script>
@endsection
