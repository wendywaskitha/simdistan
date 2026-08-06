@extends('layouts.admin')

@section('title', 'Edit Kategori Komoditas')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Kategori Komoditas', 'url' => route('kategori-komoditas.index')],
    ['label' => 'Edit Kategori']
]" />

<div class="row">
    <div class="col-md-6">
        <div class="card custom-card border-0 p-4">
            <h5 class="fw-bold mb-4">Form Edit Kategori</h5>

            <form action="{{ route('kategori-komoditas.update', $kategori->id) }}" method="POST">
                @csrf
                @method('PUT')

                <x-form.input 
                    name="nama" 
                    label="Nama Kategori Komoditas" 
                    value="{{ $kategori->nama }}"
                    placeholder="Masukkan nama kategori" 
                    required="true" 
                />

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-success px-4 rounded-3">Perbarui</button>
                    <a href="{{ route('kategori-komoditas.index') }}" class="btn btn-light px-4 rounded-3 text-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
