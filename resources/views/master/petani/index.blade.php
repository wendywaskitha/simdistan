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
        <a href="{{ route('petanis.create') }}" class="btn btn-success rounded-3 px-4 py-2">
            <i class="bi bi-plus-circle me-1"></i> Tambah Petani
        </a>
    </div>

    <x-table :headers="['No', 'NIK', 'Nama Petani', 'Kelompok Tani', 'Desa', 'Telepon', 'Luas Lahan (Ha)', 'Dokumen KTP', 'Aksi']" id="petaniTable" />
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

