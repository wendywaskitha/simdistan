@extends('layouts.admin')

@section('title', 'Realokasi Bantuan Alsintan')

@section('styles')
<!-- Tom Select CSS -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
    .ts-wrapper.form-select {
        border: none;
        padding: 0;
        height: auto;
    }
</style>
@endsection

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Prasarana & Sarana Pertanian (PSP)', 'url' => route('alsintans.index')],
    ['label' => 'Bantuan Alsintan', 'url' => route('alsintans.index')],
    ['label' => $alsintan->nama_alat, 'url' => route('alsintans.show', $alsintan->id)],
    ['label' => 'Realokasi Alat']
]" />

<div class="row">
    <div class="col-lg-8">
        <div class="card custom-card border-0 p-4">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="bg-warning-subtle text-warning rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-arrow-left-right fs-4"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1">Realokasi Alat Mesin Pertanian</h5>
                    <p class="text-muted small mb-0">Alihkan hak pakai dan kelola Alsintan non-produktif ke Kelompok Tani lain yang membutuhkan.</p>
                </div>
            </div>

            <!-- Current Location Summary -->
            <div class="alert alert-warning border-0 rounded-4 p-3 mb-4 d-flex align-items-center gap-3">
                <i class="bi bi-exclamation-triangle-fill fs-3 text-warning"></i>
                <div>
                    <small class="text-muted d-block lh-1 mb-1">Kelompok Tani Saat Ini (Asal)</small>
                    <span class="fw-bold text-dark fs-6">{{ $alsintan->kelompokTani ? $alsintan->kelompokTani->nama : '-' }}</span>
                    <span class="d-block small text-secondary">Ketua Poktan: {{ $alsintan->nama_ketua ?? '-' }}</span>
                </div>
            </div>

            <form action="{{ route('alsintans.realokasi.store', $alsintan->id) }}" method="POST">
                @csrf
                
                <!-- Hidden inputs to help with validation -->
                <input type="hidden" name="kelompok_tani_asal_id" value="{{ $alsintan->kelompok_tani_id }}">

                <div class="card bg-light border-0 p-3 mb-4 rounded-3">
                    <label class="form-label fw-bold text-success small mb-2"><i class="bi bi-person-check-fill me-1"></i>Pilih Penerima Realokasi Baru</label>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="kelompok_tani_tujuan_id" class="form-label fw-semibold text-secondary">
                                    Kelompok Tani Tujuan <span class="text-danger">*</span>
                                </label>
                                <select id="kelompok_tani_tujuan_id" name="kelompok_tani_tujuan_id" required class="form-select @error('kelompok_tani_tujuan_id') is-invalid @enderror">
                                    <option value="" disabled {{ is_null(old('kelompok_tani_tujuan_id')) ? 'selected' : '' }}>Pilih Kelompok Tani Baru</option>
                                    @foreach($kelompokTanis as $item)
                                        <option value="{{ $item->id }}" data-ketua="{{ $item->ketua }}" {{ old('kelompok_tani_tujuan_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->nama }} (Desa {{ $item->desa ? $item->desa->nama : '-' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('kelompok_tani_tujuan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <x-form.input 
                                name="nama_ketua_tujuan" 
                                label="Nama Ketua Kelompok Tani Baru" 
                                placeholder="Kosongkan untuk mengambil otomatis" 
                            />
                        </div>
                    </div>
                </div>

                <div class="card bg-light border-0 p-3 mb-4 rounded-3">
                    <label class="form-label fw-bold text-success small mb-2"><i class="bi bi-calendar-event-fill me-1"></i>Detail Realokasi</label>
                    
                    <div class="mb-3">
                        <x-form.input 
                            name="tanggal_realokasi" 
                            type="date"
                            label="Tanggal Realokasi" 
                            value="{{ date('Y-m-d') }}"
                            required="true"
                        />
                    </div>

                    <div class="mb-3">
                        <label for="keterangan" class="form-label fw-semibold text-secondary small">Keterangan / Alasan Pemindahan</label>
                        <textarea name="keterangan" id="keterangan" rows="3" class="form-control rounded-3 {{ $errors->has('keterangan') ? 'is-invalid' : '' }}" placeholder="Masukkan alasan pemindahan, misal: Alat tidak produktif di kelompok asal, pemindahan aset dinas, dll.">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-warning text-dark px-4 rounded-3 fw-semibold">Proses Realokasi</button>
                    <a href="{{ route('alsintans.show', $alsintan->id) }}" class="btn btn-light px-4 rounded-3 text-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Tom Select JS -->
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ktSelect = new TomSelect('#kelompok_tani_tujuan_id', {
            create: false,
            onChange: function(value) {
                const option = ktSelect.options[value];
                if (option) {
                    const optElement = option.$option;
                    const ketua = optElement ? optElement.getAttribute('data-ketua') : '';
                    document.getElementById('nama_ketua_tujuan').value = ketua || '';
                } else {
                    document.getElementById('nama_ketua_tujuan').value = '';
                }
            }
        });
    });
</script>
@endsection

