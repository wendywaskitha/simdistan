@extends('layouts.admin')

@section('title', 'Master Data Jenis Pupuk')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Master Data Jenis Pupuk']
]" />

<div class="card custom-card border-0 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1">Daftar Jenis Pupuk</h5>
            <p class="text-muted small mb-0">Kelola data jenis pupuk bersubsidi.</p>
        </div>
        <a href="{{ route('jenis-pupuks.create') }}" class="btn btn-success rounded-3 px-4 py-2">
            <i class="bi bi-plus-circle me-1"></i> Tambah Jenis Pupuk
        </a>
    </div>

    <x-table :headers="['No', 'Nama Jenis Pupuk', 'Deskripsi', 'Aksi']" id="jenisPupukTable" />
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#jenisPupukTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('jenis-pupuks.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '10%'},
                {data: 'nama', name: 'nama'},
                {data: 'deskripsi', name: 'deskripsi'},
                {data: 'action', name: 'action', orderable: false, searchable: false, width: '20%'}
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            }
        });
    });
</script>
@endsection


