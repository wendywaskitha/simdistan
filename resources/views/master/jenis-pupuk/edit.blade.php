@extends('layouts.admin')

@section('title', 'Edit Data Jenis Pupuk')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Master Data Jenis Pupuk', 'url' => route('jenis-pupuks.index')],
    ['label' => 'Edit Jenis Pupuk']
]" />

<div class="row">
    <div class="col-md-6">
        <div class="card custom-card border-0 p-4">
            <h5 class="fw-bold mb-4">Form Edit Jenis Pupuk</h5>

            <form action="{{ route('jenis-pupuks.update', $jenis->id) }}" method="POST">
                @csrf
                @method('PUT')

                <x-form.input 
                    name="nama" 
                    label="Nama Jenis Pupuk" 
                    placeholder="Masukkan nama jenis pupuk (contoh: Urea, NPK Phonska)" 
                    value="{{ $jenis->nama }}"
                    required="true" 
                />

                <div class="mb-3">
                    <label for="deskripsi" class="form-label fw-semibold text-secondary small">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" class="form-control rounded-3" rows="3" placeholder="Masukkan deskripsi jenis pupuk">{{ $jenis->deskripsi }}</textarea>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-success px-4 rounded-3">Simpan Perubahan</button>
                    <a href="{{ route('jenis-pupuks.index') }}" class="btn btn-light px-4 rounded-3 text-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
