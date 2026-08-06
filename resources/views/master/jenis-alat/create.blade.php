@extends('layouts.admin')

@section('title', 'Tambah Data Jenis Alat Alsintan')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Master Data Jenis Alat', 'url' => route('jenis-alats.index')],
    ['label' => 'Tambah Jenis Alat']
]" />

<div class="row">
    <div class="col-md-6">
        <div class="card custom-card border-0 p-4">
            <h5 class="fw-bold mb-4">Form Tambah Jenis Alat Alsintan</h5>

            <form action="{{ route('jenis-alats.store') }}" method="POST">
                @csrf

                <x-form.input 
                    name="nama" 
                    label="Nama Jenis Alat" 
                    placeholder="Masukkan nama jenis alat (contoh: Traktor Roda 2, Pompa Air)" 
                    required="true" 
                />

                <div class="mb-3">
                    <label for="deskripsi" class="form-label fw-semibold text-secondary small">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" class="form-control rounded-3" rows="3" placeholder="Masukkan deskripsi jenis alat"></textarea>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-success px-4 rounded-3">Simpan</button>
                    <a href="{{ route('jenis-alats.index') }}" class="btn btn-light px-4 rounded-3 text-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
