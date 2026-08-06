@extends('layouts.admin')

@section('title', 'Tambah Data Penyuluh')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Master Data Penyuluh', 'url' => route('penyuluhs.index')],
    ['label' => 'Tambah Penyuluh']
]" />

<div class="row">
    <div class="col-md-6">
        <div class="card custom-card border-0 p-4">
            <h5 class="fw-bold mb-4">Form Tambah Penyuluh</h5>

            <form action="{{ route('penyuluhs.store') }}" method="POST">
                @csrf

                <x-form.input 
                    name="nama" 
                    label="Nama Penyuluh" 
                    placeholder="Masukkan nama lengkap penyuluh" 
                    required="true" 
                />

                <x-form.input 
                    name="nip" 
                    label="NIP (Nomor Induk Pegawai)" 
                    placeholder="Masukkan NIP (opsional)" 
                />

                <x-form.input 
                    name="telepon" 
                    label="Nomor Telepon/WA" 
                    placeholder="Masukkan nomor telepon (opsional)" 
                />

                <x-form.select 
                    name="bpp_id" 
                    label="Kantor BPP" 
                    placeholder="-- Pilih BPP --" 
                    :options="$bpps"
                    required="true"
                />

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-success px-4 rounded-3">Simpan</button>
                    <a href="{{ route('penyuluhs.index') }}" class="btn btn-light px-4 rounded-3 text-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
