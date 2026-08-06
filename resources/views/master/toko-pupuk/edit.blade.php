@extends('layouts.admin')

@section('title', 'Edit Data Toko/Distributor Pupuk')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Master Data Toko/Distributor', 'url' => route('toko-pupuks.index')],
    ['label' => 'Edit Toko']
]" />

<div class="row">
    <div class="col-md-8">
        <div class="card custom-card border-0 p-4">
            <h5 class="fw-bold mb-4">Form Edit Toko/Distributor Pupuk</h5>

            <form action="{{ route('toko-pupuks.update', $toko->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <x-form.input 
                            name="nama" 
                            label="Nama Toko/Distributor" 
                            placeholder="Masukkan nama toko/distributor" 
                            value="{{ $toko->nama }}"
                            required="true" 
                        />
                    </div>
                    <div class="col-md-6">
                        <x-form.input 
                            name="pemilik" 
                            label="Nama Pemilik" 
                            value="{{ $toko->pemilik }}"
                            placeholder="Masukkan nama pemilik" 
                        />
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <x-form.input 
                            name="telepon" 
                            label="Nomor Telepon" 
                            value="{{ $toko->telepon }}"
                            placeholder="Masukkan nomor telepon" 
                        />
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="alamat" class="form-label fw-semibold text-secondary small">Alamat</label>
                            <input name="alamat" id="alamat" class="form-control rounded-3" value="{{ $toko->alamat }}" placeholder="Masukkan alamat toko">
                        </div>
                    </div>
                </div>

                <div class="card bg-light border-0 p-3 mb-4 rounded-3">
                    <label class="form-label fw-bold text-success small mb-2"><i class="bi bi-geo-alt-fill me-1"></i>Wilayah Ampuan / Kecamatan diampu</label>
                    <p class="text-muted small mb-3">Pilih satu atau beberapa kecamatan wilayah kerja distribusi toko ini.</p>
                    
                    <div class="row g-2">
                        @foreach($kecamatans as $id => $nama)
                            <div class="col-md-4 col-sm-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="kecamatan_ids[]" value="{{ $id }}" id="kecamatan_{{ $id }}"
                                        {{ in_array($id, $selectedKecamatans) ? 'checked' : '' }}>
                                    <label class="form-check-label small fw-semibold text-secondary" for="kecamatan_{{ $id }}">
                                        {{ $nama }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('kecamatan_ids')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-success px-4 rounded-3">Simpan Perubahan</button>
                    <a href="{{ route('toko-pupuks.index') }}" class="btn btn-light px-4 rounded-3 text-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
