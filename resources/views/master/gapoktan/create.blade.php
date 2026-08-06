@extends('layouts.admin')

@section('title', 'Tambah Data Gapoktan')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Master Data Gapoktan', 'url' => route('gapoktans.index')],
    ['label' => 'Tambah Gapoktan']
]" />

<div class="row">
    <div class="col-md-6">
        <div class="card custom-card border-0 p-4">
            <h5 class="fw-bold mb-4">Form Tambah Gapoktan</h5>

            <form action="{{ route('gapoktans.store') }}" method="POST">
                @csrf

                <x-form.select 
                    name="kecamatan_id" 
                    label="Kecamatan Wilayah Kerja" 
                    placeholder="-- Pilih Kecamatan --" 
                    :options="$kecamatans"
                    required="true"
                />

                <x-form.input 
                    name="nama" 
                    label="Nama Gapoktan" 
                    placeholder="Masukkan nama Gapoktan (contoh: Gapoktan Tunas Harapan)" 
                    required="true" 
                />

                <x-form.input 
                    name="ketua" 
                    label="Ketua Gapoktan" 
                    placeholder="Masukkan nama ketua Gapoktan (opsional)" 
                />

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-success px-4 rounded-3">Simpan</button>
                    <a href="{{ route('gapoktans.index') }}" class="btn btn-light px-4 rounded-3 text-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
