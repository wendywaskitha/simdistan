@extends('layouts.admin')

@section('title', 'Tambah Data BPP')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Master Data BPP', 'url' => route('bpps.index')],
    ['label' => 'Tambah BPP']
]" />

<div class="row">
    <div class="col-md-6">
        <div class="card custom-card border-0 p-4">
            <h5 class="fw-bold mb-4">Form Tambah BPP</h5>

            <form action="{{ route('bpps.store') }}" method="POST">
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
                    label="Nama BPP" 
                    placeholder="Masukkan nama BPP (contoh: BPP Sawerigadi)" 
                    required="true" 
                />

                <x-form.textarea 
                    name="alamat" 
                    label="Alamat Kantor BPP" 
                    placeholder="Tulis alamat lengkap BPP" 
                    rows="3" 
                />

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-success px-4 rounded-3">Simpan</button>
                    <a href="{{ route('bpps.index') }}" class="btn btn-light px-4 rounded-3 text-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
