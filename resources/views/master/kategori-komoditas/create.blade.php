@extends('layouts.admin')

@section('title', 'Tambah Kategori Komoditas')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Kategori Komoditas', 'url' => route('kategori-komoditas.index')],
    ['label' => 'Tambah Kategori']
]" />

<div class="row">
    <div class="col-md-6">
        <div class="card custom-card border-0 p-4">
            <h5 class="fw-bold mb-4">Form Tambah Kategori</h5>

            <form action="{{ route('kategori-komoditas.store') }}" method="POST">
                @csrf

                <x-form.input 
                    name="nama" 
                    label="Nama Kategori Komoditas" 
                    placeholder="Masukkan nama kategori (contoh: Tanaman Pangan)" 
                    required="true" 
                />

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-success px-4 rounded-3">Simpan</button>
                    <a href="{{ route('kategori-komoditas.index') }}" class="btn btn-light px-4 rounded-3 text-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
