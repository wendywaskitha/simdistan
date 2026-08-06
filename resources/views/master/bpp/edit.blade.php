@extends('layouts.admin')

@section('title', 'Edit Data BPP')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Master Data BPP', 'url' => route('bpps.index')],
    ['label' => 'Edit BPP']
]" />

<div class="row">
    <div class="col-md-6">
        <div class="card custom-card border-0 p-4">
            <h5 class="fw-bold mb-4">Form Edit BPP</h5>

            <form action="{{ route('bpps.update', $bpp->id) }}" method="POST">
                @csrf
                @method('PUT')

                <x-form.select 
                    name="kecamatan_id" 
                    label="Kecamatan Wilayah Kerja" 
                    placeholder="-- Pilih Kecamatan --" 
                    :options="$kecamatans"
                    selected="{{ $bpp->kecamatan_id }}"
                    required="true"
                />

                <x-form.input 
                    name="nama" 
                    label="Nama BPP" 
                    value="{{ $bpp->nama }}"
                    placeholder="Masukkan nama BPP" 
                    required="true" 
                />

                <x-form.textarea 
                    name="alamat" 
                    label="Alamat Kantor BPP" 
                    value="{{ $bpp->alamat }}"
                    placeholder="Tulis alamat lengkap BPP" 
                    rows="3" 
                />

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-success px-4 rounded-3">Perbarui</button>
                    <a href="{{ route('bpps.index') }}" class="btn btn-light px-4 rounded-3 text-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
