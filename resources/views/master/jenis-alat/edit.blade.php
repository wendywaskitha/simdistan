@extends('layouts.admin')

@section('title', 'Edit Data Jenis Alat Alsintan')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Master Data Jenis Alat', 'url' => route('jenis-alats.index')],
    ['label' => 'Edit Jenis Alat']
]" />

<div class="row">
    <div class="col-md-6">
        <div class="card custom-card border-0 p-4">
            <h5 class="fw-bold mb-4">Form Edit Jenis Alat Alsintan</h5>

            <form action="{{ route('jenis-alats.update', $jenis->id) }}" method="POST">
                @csrf
                @method('PUT')

                <x-form.input 
                    name="nama" 
                    label="Nama Jenis Alat" 
                    value="{{ $jenis->nama }}"
                    placeholder="Masukkan nama jenis alat" 
                    required="true" 
                />

                <div class="mb-3">
                    <label for="deskripsi" class="form-label fw-semibold text-secondary small">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" class="form-control rounded-3" rows="3" placeholder="Masukkan deskripsi">{{ $jenis->deskripsi }}</textarea>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-success px-4 rounded-3">Perbarui</button>
                    <a href="{{ route('jenis-alats.index') }}" class="btn btn-light px-4 rounded-3 text-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
