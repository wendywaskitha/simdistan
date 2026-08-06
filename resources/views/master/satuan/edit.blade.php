@extends('layouts.admin')

@section('title', 'Edit Data Satuan')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Data Satuan', 'url' => route('satuans.index')],
    ['label' => 'Edit Satuan']
]" />

<div class="row">
    <div class="col-md-6">
        <div class="card custom-card border-0 p-4">
            <h5 class="fw-bold mb-4">Form Edit Satuan</h5>

            <form action="{{ route('satuans.update', $satuan->id) }}" method="POST">
                @csrf
                @method('PUT')

                <x-form.input 
                    name="nama" 
                    label="Nama Satuan" 
                    value="{{ $satuan->nama }}"
                    placeholder="Masukkan nama satuan" 
                    required="true" 
                />

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-success px-4 rounded-3">Perbarui</button>
                    <a href="{{ route('satuans.index') }}" class="btn btn-light px-4 rounded-3 text-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
