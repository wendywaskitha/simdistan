@extends('layouts.admin')

@section('title', 'Master Data Penyuluh')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Master Data Penyuluh']
]" />

<div class="card custom-card border-0 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1">Daftar Penyuluh Lapangan</h5>
            <p class="text-muted small mb-0">Kelola data penyuluh pertanian di Kabupaten Muna Barat.</p>
        </div>
        <a href="{{ route('penyuluhs.create') }}" class="btn btn-success rounded-3 px-4 py-2">
            <i class="bi bi-plus-circle me-1"></i> Tambah Penyuluh
        </a>
    </div>

    <x-table :headers="['No', 'Nama Penyuluh', 'NIP', 'Telepon', 'Kantor BPP', 'Aksi']" id="penyuluhTable" />
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#penyuluhTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('penyuluhs.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '8%'},
                {data: 'nama', name: 'nama'},
                {data: 'nip', name: 'nip', defaultContent: '-'},
                {data: 'telepon', name: 'telepon', defaultContent: '-'},
                {data: 'bpp_nama', name: 'bpp.nama'},
                {data: 'action', name: 'action', orderable: false, searchable: false, width: '15%'}
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            }
        });
    });
</script>
@endsection

