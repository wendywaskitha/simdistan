@extends('layouts.admin')

@section('title', 'Edit Data Desa')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Master Data Desa', 'url' => route('desas.index')],
    ['label' => 'Edit Desa']
]" />

<div class="row">
    <div class="col-md-6">
        <div class="card custom-card border-0 p-4">
            <h5 class="fw-bold mb-4">Form Edit Desa</h5>

            <form action="{{ route('desas.update', $desa->id) }}" method="POST">
                @csrf
                @method('PUT')

                <x-form.select 
                    name="kecamatan_id" 
                    label="Pilih Kecamatan" 
                    placeholder="-- Pilih Kecamatan --" 
                    :options="$kecamatans"
                    selected="{{ $desa->kecamatan_id }}"
                    required="true"
                />

                <x-form.input 
                    name="nama" 
                    label="Nama Desa" 
                    value="{{ $desa->nama }}"
                    placeholder="Masukkan nama desa" 
                    required="true" 
                />

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-success px-4 rounded-3">Perbarui</button>
                    <a href="{{ route('desas.index') }}" class="btn btn-light px-4 rounded-3 text-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
