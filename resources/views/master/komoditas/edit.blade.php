@extends('layouts.admin')

@section('title', 'Edit Data Komoditas')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Data Komoditas', 'url' => route('komoditas.index')],
    ['label' => 'Edit Komoditas']
]" />

<div class="row">
    <div class="col-md-6">
        <div class="card custom-card border-0 p-4">
            <h5 class="fw-bold mb-4">Form Edit Komoditas</h5>

            <form action="{{ route('komoditas.update', $komoditas->id) }}" method="POST">
                @csrf
                @method('PUT')

                <x-form.select 
                    name="kategori_komoditas_id" 
                    label="Kategori Komoditas" 
                    placeholder="-- Pilih Kategori --" 
                    :options="$kategoris"
                    selected="{{ $komoditas->kategori_komoditas_id }}"
                    required="true"
                />

                <x-form.input 
                    name="nama" 
                    label="Nama Komoditas" 
                    value="{{ $komoditas->nama }}"
                    placeholder="Masukkan nama komoditas" 
                    required="true" 
                />

                <x-form.input 
                    name="durasi_panen_bulan" 
                    type="number"
                    label="Durasi Panen (Bulan)" 
                    placeholder="Masukkan durasi panen dalam bulan (default: 4)" 
                    value="{{ old('durasi_panen_bulan', $komoditas->durasi_panen_bulan ?? 4) }}"
                />

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-success px-4 rounded-3">Perbarui</button>
                    <a href="{{ route('komoditas.index') }}" class="btn btn-light px-4 rounded-3 text-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
