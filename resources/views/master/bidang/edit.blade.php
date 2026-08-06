@extends('layouts.admin')

@section('title', 'Edit Data Bidang')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Master Data Bidang', 'url' => route('bidangs.index')],
    ['label' => 'Edit Bidang']
]" />

<div class="row">
    <div class="col-md-6">
        <div class="card custom-card border-0 p-4">
            <h5 class="fw-bold mb-4">Form Edit Bidang</h5>

            <form action="{{ route('bidangs.update', $bidang->id) }}" method="POST">
                @csrf
                @method('PUT')

                <x-form.input 
                    name="nama" 
                    label="Nama Bidang" 
                    value="{{ $bidang->nama }}"
                    placeholder="Masukkan nama bidang" 
                    required="true" 
                />

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-success px-4 rounded-3">Perbarui</button>
                    <a href="{{ route('bidangs.index') }}" class="btn btn-light px-4 rounded-3 text-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
