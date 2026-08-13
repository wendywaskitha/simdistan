@extends('layouts.admin')

@section('title', 'Master Data Petani')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Master Data Petani']
]" />

<div class="card custom-card border-0 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1">Daftar Petani Anggota</h5>
            <p class="text-muted small mb-0">Kelola data petani dan keanggotaan kelompok tani.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-success rounded-3 px-4 py-2" data-bs-toggle="modal" data-bs-target="#importPetaniModal">
                <i class="bi bi-file-earmark-excel me-1"></i> Import Excel
            </button>
            <a href="{{ route('petanis.create') }}" class="btn btn-success rounded-3 px-4 py-2">
                <i class="bi bi-plus-circle me-1"></i> Tambah Petani
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

    <x-table :headers="['No', 'NIK', 'Nama Petani', 'Kelompok Tani', 'Desa', 'Telepon', 'Luas Lahan (Ha)', 'Dokumen KTP', 'Aksi']" id="petaniTable" />
</div>

<!-- Modal Import Petani -->
<div class="modal fade" id="importPetaniModal" tabindex="-1" aria-labelledby="importPetaniModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-3 border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="importPetaniModalLabel">Import Data Petani</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('petanis.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body py-4">
                    <div class="mb-4">
                        <label for="file" class="form-label fw-semibold">Pilih Berkas Excel (.xlsx, .xls)</label>
                        <input type="file" name="file" id="file" class="form-control rounded-3" accept=".xlsx, .xls" required>
                    </div>
                    
                    <div class="bg-light p-3 rounded-3 mb-3">
                        <h6 class="fw-bold mb-2 text-dark"><i class="bi bi-info-circle-fill text-info me-2"></i>Panduan & Aturan Pengisian:</h6>
                        <ul class="small mb-0 text-muted ps-3">
                            <li class="mb-1">Gunakan template yang telah disediakan dengan menekan tombol <strong>Unduh Template</strong> di bawah.</li>
                            <li class="mb-1"><strong>NIK</strong> wajib diisi 16 digit angka dan harus unik (belum pernah terdaftar di sistem).</li>
                            <li class="mb-1"><strong>Nama</strong> wajib diisi.</li>
                            <li class="mb-1"><strong>Kelompok Tani</strong> wajib diisi dan nama kelompok tani harus sesuai dengan yang terdaftar di database.</li>
                            <li class="mb-1"><strong>Telepon</strong>, <strong>Alamat</strong>, dan <strong>Luas Lahan</strong> bersifat opsional.</li>
                            <li class="mb-1"><strong>Luas Lahan</strong> ditulis dalam satuan Hektar (Ha), gunakan tanda titik (.) sebagai pemisah desimal (contoh: 1.5).</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 d-flex justify-content-between">
                    <a href="{{ route('petanis.template') }}" class="btn btn-outline-success rounded-3">
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
        $('#petaniTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('petanis.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '6%'},
                {data: 'nik', name: 'nik'},
                {data: 'nama', name: 'nama'},
                {data: 'kelompok_tani_nama', name: 'kelompokTani.nama'},
                {data: 'desa_nama', name: 'kelompokTani.desa.nama'},
                {data: 'telepon', name: 'telepon', defaultContent: '-'},
                {data: 'luas_lahan', name: 'luas_lahan', class: 'text-end'},
                {data: 'ktp_status', name: 'ktp_status', orderable: false, searchable: false, class: 'text-center', width: '10%'},
                {data: 'action', name: 'action', orderable: false, searchable: false, width: '12%'}
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            }
        });
    });
</script>
@endsection

