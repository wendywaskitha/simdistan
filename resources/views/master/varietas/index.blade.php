@extends('layouts.admin')

@section('title', 'Master Data Varietas')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Data Varietas']
]" />

<div class="card custom-card border-0 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1">Daftar Varietas Komoditas</h5>
            <p class="text-muted small mb-0">Kelola varietas khusus komoditas pertanian (ciherang, inpari, dll).</p>
        </div>
        <a href="{{ route('varietas.create') }}" class="btn btn-success rounded-3 px-4 py-2">
            <i class="bi bi-plus-circle me-1"></i> Tambah Varietas
        </a>
    </div>

    <x-table :headers="['No', 'Nama Varietas', 'Komoditas', 'Kategori', 'Aksi']" id="varietasTable" />
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#varietasTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('varietas.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '8%'},
                {data: 'nama', name: 'nama'},
                {data: 'komoditas_nama', name: 'komoditas.nama'},
                {data: 'kategori_nama', name: 'komoditas.kategori.nama'},
                {data: 'action', name: 'action', orderable: false, searchable: false, width: '15%'}
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            }
        });
    });
</script>
@endsection

