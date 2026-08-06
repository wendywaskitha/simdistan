@extends('layouts.admin')

@section('title', 'Tambah Pengguna Baru')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Manajemen Pengguna', 'url' => route('users.index')],
    ['label' => 'Tambah Pengguna Baru']
]" />

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4 shadow-sm" role="alert">
        <h6 class="fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i>Gagal Menyimpan Data:</h6>
        <ul class="mb-0 ps-3 small">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card custom-card border-0 shadow-sm p-4" style="border-radius: 16px;">
    <div class="mb-4">
        <h5 class="fw-bold mb-1">Tambah Pengguna Baru</h5>
        <p class="text-muted small mb-0">Buat kredensial login baru dan tetapkan role/hak akses bidang yang sesuai.</p>
    </div>

    <form action="{{ route('users.store') }}" method="POST" id="userForm">
        @csrf

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <x-form.input 
                    name="name" 
                    label="Nama Lengkap" 
                    placeholder="Tulis nama lengkap pengguna" 
                    value="{{ old('name') }}"
                    required="true"
                />
            </div>
            <div class="col-md-6">
                <x-form.input 
                    name="email" 
                    label="Alamat Email" 
                    type="email"
                    placeholder="contoh@simdistan.test" 
                    value="{{ old('email') }}"
                    required="true"
                />
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <x-form.input 
                    name="password" 
                    label="Kata Sandi" 
                    type="password"
                    placeholder="Minimal 8 karakter" 
                    required="true"
                />
            </div>
            <div class="col-md-6">
                <x-form.input 
                    name="password_confirmation" 
                    label="Konfirmasi Kata Sandi" 
                    type="password"
                    placeholder="Ulangi kata sandi" 
                    required="true"
                />
            </div>
        </div>

        {{-- Pilih Role/Peran --}}
        <div class="card border border-light-subtle rounded-3 p-4 bg-light shadow-sm mb-4">
            <h6 class="fw-bold mb-3 text-secondary text-uppercase" style="letter-spacing: 0.05em;">Tetapkan Hak Akses (Role) <span class="text-danger">*</span></h6>
            
            <div class="row">
                @foreach($roles as $id => $name)
                    <div class="col-md-4 mb-3">
                        <div class="form-check p-3 bg-white border rounded-3 shadow-sm d-flex align-items-center gap-2">
                            <input class="form-check-input ms-0 mt-0" type="checkbox" name="roles[]" value="{{ $name }}" id="role_{{ $id }}">
                            <label class="form-check-label fw-semibold text-secondary small" for="role_{{ $id }}">
                                {{ $name }}
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary px-4 rounded-3 shadow-sm">
                <i class="bi bi-check-circle me-1"></i> Simpan Pengguna
            </button>
            <a href="{{ route('users.index') }}" class="btn btn-light px-4 rounded-3 text-secondary border">Batal</a>
        </div>
    </form>
</div>
@endsection
