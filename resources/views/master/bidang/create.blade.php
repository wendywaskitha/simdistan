@extends('layouts.admin')

@section('title', 'Tambah Data Bidang')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Master Data Bidang', 'url' => route('bidangs.index')],
    ['label' => 'Tambah Bidang']
]" />

<div class="row">
    <div class="col-md-6">
        <div class="card custom-card border-0 p-4">
            <h5 class="fw-bold mb-4">Form Tambah Bidang</h5>

            <form action="{{ route('bidangs.store') }}" method="POST">
                @csrf

                <x-form.input 
                    name="nama" 
                    label="Nama Bidang" 
                    placeholder="Masukkan nama bidang (contoh: Penyuluhan)" 
                    required="true" 
                />

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-success px-4 rounded-3">Simpan</button>
                    <a href="{{ route('bidangs.index') }}" class="btn btn-light px-4 rounded-3 text-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
