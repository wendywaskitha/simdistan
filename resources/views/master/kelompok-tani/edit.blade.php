@extends('layouts.admin')

@section('title', 'Edit Data Kelompok Tani')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Master Data Kelompok Tani', 'url' => route('kelompok-tanis.index')],
    ['label' => 'Edit Kelompok Tani']
]" />

<div class="row">
    <div class="col-md-6">
        <div class="card custom-card border-0 p-4">
            <h5 class="fw-bold mb-4">Form Edit Kelompok Tani</h5>

            <form action="{{ route('kelompok-tanis.update', $kelompokTani->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <x-form.select 
                    name="desa_id" 
                    label="Desa Wilayah" 
                    placeholder="-- Pilih Desa --" 
                    :options="$desas"
                    selected="{{ $kelompokTani->desa_id }}"
                    required="true"
                />

                <x-form.select 
                    name="gapoktan_id" 
                    label="Asosiasi Gapoktan (Induk)" 
                    placeholder="-- Pilih Gapoktan (Opsional) --" 
                    :options="$gapoktans"
                    selected="{{ $kelompokTani->gapoktan_id }}"
                />

                <x-form.input 
                    name="nama" 
                    label="Nama Kelompok Tani" 
                    value="{{ $kelompokTani->nama }}"
                    placeholder="Masukkan nama Kelompok Tani" 
                    required="true" 
                />

                <x-form.input 
                    name="ketua" 
                    label="Ketua Kelompok Tani" 
                    value="{{ $kelompokTani->ketua }}"
                    placeholder="Masukkan nama ketua Poktan" 
                />

                <div class="mb-3">
                    <label class="form-label fw-semibold">SK Pembentukan Kelompok Tani (PDF/Image/Word)</label>
                    <input type="file" name="sk_pembentukan" class="form-control rounded-3 @error('sk_pembentukan') is-invalid @enderror">
                    @error('sk_pembentukan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    @if($kelompokTani->sk_pembentukan)
                        <div class="mt-2 small">
                            <i class="bi bi-file-earmark-check text-success"></i> 
                            SK saat ini: <a href="{{ asset('storage/' . $kelompokTani->sk_pembentukan) }}" target="_blank" class="fw-semibold text-success">Lihat Dokumen</a>
                        </div>
                    @endif
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Berita Acara Pembentukan (PDF/Image/Word)</label>
                    <input type="file" name="berita_acara" class="form-control rounded-3 @error('berita_acara') is-invalid @enderror">
                    @error('berita_acara')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    @if($kelompokTani->berita_acara)
                        <div class="mt-2 small">
                            <i class="bi bi-file-earmark-check text-success"></i> 
                            Berita Acara saat ini: <a href="{{ asset('storage/' . $kelompokTani->berita_acara) }}" target="_blank" class="fw-semibold text-success">Lihat Dokumen</a>
                        </div>
                    @endif
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-success px-4 rounded-3">Perbarui</button>
                    <a href="{{ route('kelompok-tanis.index') }}" class="btn btn-light px-4 rounded-3 text-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
