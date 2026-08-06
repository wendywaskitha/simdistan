@extends('layouts.admin')

@section('title', 'Tambah Data Petani')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Master Data Petani', 'url' => route('petanis.index')],
    ['label' => 'Tambah Petani']
]" />

<div class="row">
    <div class="col-md-6">
        <div class="card custom-card border-0 p-4">
            <h5 class="fw-bold mb-4">Form Tambah Petani</h5>

            <form action="{{ route('petanis.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <x-form.select 
                    name="kelompok_tani_id" 
                    label="Kelompok Tani" 
                    placeholder="-- Pilih Kelompok Tani --" 
                    :options="$kelompokTanis"
                    required="true"
                />

                <x-form.input 
                    name="nama" 
                    label="Nama Petani" 
                    placeholder="Masukkan nama lengkap petani" 
                    required="true" 
                />

                <x-form.input 
                    name="nik" 
                    label="NIK (Nomor Induk Kependudukan)" 
                    placeholder="Masukkan NIK 16 digit" 
                    required="true" 
                    maxlength="16"
                />

                <x-form.input 
                    name="telepon" 
                    label="Nomor Telepon/WA" 
                    placeholder="Masukkan nomor telepon (opsional)" 
                />

                <x-form.textarea 
                    name="alamat" 
                    label="Alamat Lengkap" 
                    placeholder="Tulis alamat rumah lengkap petani (opsional)" 
                    rows="3" 
                />

                <x-form.input 
                    name="luas_lahan" 
                    label="Luas Lahan (Ha)" 
                    type="number"
                    step="0.01"
                    placeholder="Masukkan luas lahan dalam hektar (contoh: 1.5)" 
                />

                <div class="mb-3">
                    <label class="form-label fw-semibold">Dokumen KTP (PDF, JPG, JPEG, PNG)</label>
                    <input type="file" name="ktp" class="form-control rounded-3 @error('ktp') is-invalid @enderror">
                    @error('ktp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-success px-4 rounded-3">Simpan</button>
                    <a href="{{ route('petanis.index') }}" class="btn btn-light px-4 rounded-3 text-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
