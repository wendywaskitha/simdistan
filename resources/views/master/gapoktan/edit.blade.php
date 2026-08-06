@extends('layouts.admin')

@section('title', 'Edit Data Gapoktan')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Master Data Gapoktan', 'url' => route('gapoktans.index')],
    ['label' => 'Edit Gapoktan']
]" />

<div class="row">
    <div class="col-md-6">
        <div class="card custom-card border-0 p-4">
            <h5 class="fw-bold mb-4">Form Edit Gapoktan</h5>

            <form action="{{ route('gapoktans.update', $gapoktan->id) }}" method="POST">
                @csrf
                @method('PUT')

                <x-form.select 
                    name="kecamatan_id" 
                    label="Kecamatan Wilayah Kerja" 
                    placeholder="-- Pilih Kecamatan --" 
                    :options="$kecamatans"
                    selected="{{ $gapoktan->kecamatan_id }}"
                    required="true"
                />

                <x-form.input 
                    name="nama" 
                    label="Nama Gapoktan" 
                    value="{{ $gapoktan->nama }}"
                    placeholder="Masukkan nama Gapoktan" 
                    required="true" 
                />

                <x-form.input 
                    name="ketua" 
                    label="Ketua Gapoktan" 
                    value="{{ $gapoktan->ketua }}"
                    placeholder="Masukkan nama ketua Gapoktan" 
                />

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-success px-4 rounded-3">Perbarui</button>
                    <a href="{{ route('gapoktans.index') }}" class="btn btn-light px-4 rounded-3 text-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
