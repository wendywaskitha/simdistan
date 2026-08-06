@extends('layouts.admin')

@section('title', 'Edit Data Varietas')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Data Varietas', 'url' => route('varietas.index')],
    ['label' => 'Edit Varietas']
]" />

<div class="row">
    <div class="col-md-6">
        <div class="card custom-card border-0 p-4">
            <h5 class="fw-bold mb-4">Form Edit Varietas</h5>

            <form action="{{ route('varietas.update', $varietas->id) }}" method="POST">
                @csrf
                @method('PUT')

                <x-form.select 
                    name="komoditas_id" 
                    label="Pilih Komoditas" 
                    placeholder="-- Pilih Komoditas --" 
                    :options="$komoditas"
                    selected="{{ $varietas->komoditas_id }}"
                    required="true"
                />

                <x-form.input 
                    name="nama" 
                    label="Nama Varietas" 
                    value="{{ $varietas->nama }}"
                    placeholder="Masukkan nama varietas" 
                    required="true" 
                />

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-success px-4 rounded-3">Perbarui</button>
                    <a href="{{ route('varietas.index') }}" class="btn btn-light px-4 rounded-3 text-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
