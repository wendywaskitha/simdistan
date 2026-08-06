@extends('layouts.admin')

@section('title', 'Master Data Satuan')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Data Satuan']
]" />

<div class="card custom-card border-0 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1">Daftar Satuan Ukur</h5>
            <p class="text-muted small mb-0">Kelola satuan berat atau kuantitas hasil produksi (Kg, Ton, dll).</p>
        </div>
        <a href="{{ route('satuans.create') }}" class="btn btn-success rounded-3 px-4 py-2">
            <i class="bi bi-plus-circle me-1"></i> Tambah Satuan
        </a>
    </div>

    <x-table :headers="['No', 'Nama Satuan', 'Aksi']" id="satuanTable" />
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#satuanTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('satuans.index') }}",
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

