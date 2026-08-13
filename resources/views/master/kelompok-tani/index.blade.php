@extends('layouts.admin')

@section('title', 'Master Data Kelompok Tani')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Master Data Kelompok Tani']
]" />

<div class="card custom-card border-0 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1">Daftar Kelompok Tani (Poktan)</h5>
            <p class="text-muted small mb-0">Kelola kelompok tani terdaftar di tingkat desa.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-success rounded-3 px-4 py-2" data-bs-toggle="modal" data-bs-target="#importKelompokTaniModal">
                <i class="bi bi-file-earmark-excel me-1"></i> Import Excel
            </button>
            <a href="{{ route('kelompok-tanis.create') }}" class="btn btn-success rounded-3 px-4 py-2">
                <i class="bi bi-plus-circle me-1"></i> Tambah Kelompok Tani
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
            <h6 class="fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i>Terjadi Kesalahan Import:</h6>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <x-table :headers="['No', 'Nama Kelompok Tani', 'Desa (Kec)', 'Induk Gapoktan', 'Ketua', 'Dokumen SK', 'Berita Acara', 'Aksi']" id="kelompokTaniTable" />
</div>

<!-- Modal Import Kelompok Tani -->
<div class="modal fade" id="importKelompokTaniModal" tabindex="-1" aria-labelledby="importKelompokTaniModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-3 border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="importKelompokTaniModalLabel">Import Data Kelompok Tani</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('kelompok-tanis.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body py-4">
                    <div class="mb-4">
                        <label for="file_poktan" class="form-label fw-semibold">Pilih Berkas Excel (.xlsx, .xls)</label>
                        <input type="file" name="file" id="file_poktan" class="form-control rounded-3" accept=".xlsx, .xls" required>
                    </div>
                    
                    <div class="bg-light p-3 rounded-3 mb-3">
                        <h6 class="fw-bold mb-2 text-dark"><i class="bi bi-info-circle-fill text-info me-2"></i>Panduan & Aturan Pengisian:</h6>
                        <ul class="small mb-0 text-muted ps-3">
                            <li class="mb-1">Gunakan template yang telah disediakan dengan menekan tombol <strong>Unduh Template</strong> di bawah.</li>
                            <li class="mb-1"><strong>Nama Kelompok Tani</strong> wajib diisi dan harus unik untuk setiap Desa.</li>
                            <li class="mb-1"><strong>Kecamatan</strong> wajib diisi dan harus terdaftar di sistem.</li>
                            <li class="mb-1"><strong>Desa</strong> wajib diisi dan harus terdaftar di bawah Kecamatan tersebut.</li>
                            <li class="mb-1"><strong>Gapoktan</strong> (opsional) diisi jika kelompok tani memiliki induk Gapoktan yang terdaftar.</li>
                            <li class="mb-1"><strong>Ketua</strong> (opsional) diisi dengan nama ketua kelompok tani.</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 d-flex justify-content-between">
                    <a href="{{ route('kelompok-tanis.template') }}" class="btn btn-outline-success rounded-3">
                        <i class="bi bi-download me-1"></i> Unduh Template
                    </a>
                    <div>
                        <button type="button" class="btn btn-light rounded-3 me-2" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success rounded-3">Mulai Import</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#kelompokTaniTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('kelompok-tanis.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '6%'},
                {data: 'nama', name: 'nama'},
                {data: 'desa_nama', name: 'desa.nama'},
                {data: 'gapoktan_nama', name: 'gapoktan.nama'},
                {data: 'ketua', name: 'ketua', defaultContent: '-'},
                {data: 'sk_status', name: 'sk_status', orderable: false, searchable: false, class: 'text-center', width: '10%'},
                {data: 'ba_status', name: 'ba_status', orderable: false, searchable: false, class: 'text-center', width: '10%'},
                {data: 'action', name: 'action', orderable: false, searchable: false, width: '12%'}
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            }
        });
    });
</script>
@endsection

