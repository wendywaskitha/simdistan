@extends('layouts.admin')

@section('title', 'Edit Data Kecamatan')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Master Data Kecamatan', 'url' => route('kecamatans.index')],
    ['label' => 'Edit Kecamatan']
]" />

<div class="row">
    <div class="col-md-6">
        <div class="card custom-card border-0 p-4">
            <h5 class="fw-bold mb-4">Form Edit Kecamatan</h5>

            <form action="{{ route('kecamatans.update', $kecamatan->id) }}" method="POST">
                @csrf
                @method('PUT')

                <x-form.input 
                    name="nama" 
                    label="Nama Kecamatan" 
                    value="{{ $kecamatan->nama }}"
                    placeholder="Masukkan nama kecamatan" 
                    required="true" 
                />

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-success px-4 rounded-3">Perbarui</button>
                    <a href="{{ route('kecamatans.index') }}" class="btn btn-light px-4 rounded-3 text-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
