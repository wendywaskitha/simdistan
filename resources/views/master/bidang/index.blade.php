@extends('layouts.admin')

@section('title', 'Master Data Bidang')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Master Data Bidang']
]" />


<div class="card custom-card border-0 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1">Daftar Bidang</h5>
            <p class="text-muted small mb-0">Kelola bidang dinas pertanian untuk alokasi operator.</p>
        </div>
        <a href="{{ route('bidangs.create') }}" class="btn btn-success rounded-3 px-4 py-2">
            <i class="bi bi-plus-circle me-1"></i> Tambah Bidang
        </a>
    </div>

    <x-table :headers="['No', 'Nama Bidang', 'Aksi']" id="bidangTable" />
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#bidangTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('bidangs.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '10%'},
                {data: 'nama', name: 'nama'},
                {data: 'action', name: 'action', orderable: false, searchable: false, width: '20%'}
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            }
        });
    });
</script>
@endsection

