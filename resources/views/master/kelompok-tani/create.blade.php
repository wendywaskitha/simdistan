@extends('layouts.admin')

@section('title', 'Tambah Data Kelompok Tani')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Master Data Kelompok Tani', 'url' => route('kelompok-tanis.index')],
    ['label' => 'Tambah Kelompok Tani']
]" />

<div class="row">
    <div class="col-md-6">
        <div class="card custom-card border-0 p-4">
            <h5 class="fw-bold mb-4">Form Tambah Kelompok Tani</h5>

            <form action="{{ route('kelompok-tanis.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <x-form.select 
                    name="desa_id" 
                    label="Desa Wilayah" 
                    placeholder="-- Pilih Desa --" 
                    :options="$desas"
                    required="true"
                />

                <x-form.select 
                    name="gapoktan_id" 
                    label="Asosiasi Gapoktan (Induk)" 
                    placeholder="-- Pilih Gapoktan (Opsional) --" 
                    :options="$gapoktans"
                />

                <x-form.input 
                    name="nama" 
                    label="Nama Kelompok Tani" 
                    placeholder="Masukkan nama Kelompok Tani (contoh: Poktan Subur Makmur)" 
                    required="true" 
                />

                <x-form.input 
                    name="ketua" 
                    label="Ketua Kelompok Tani" 
                    placeholder="Masukkan nama ketua Poktan (opsional)" 
                />

                <div class="mb-3">
                    <label class="form-label fw-semibold">SK Pembentukan Kelompok Tani (PDF/Image/Word)</label>
                    <input type="file" name="sk_pembentukan" class="form-control rounded-3 @error('sk_pembentukan') is-invalid @enderror">
                    @error('sk_pembentukan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Berita Acara Pembentukan (PDF/Image/Word)</label>
                    <input type="file" name="berita_acara" class="form-control rounded-3 @error('berita_acara') is-invalid @enderror">
                    @error('berita_acara')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-success px-4 rounded-3">Simpan</button>
                    <a href="{{ route('kelompok-tanis.index') }}" class="btn btn-light px-4 rounded-3 text-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
