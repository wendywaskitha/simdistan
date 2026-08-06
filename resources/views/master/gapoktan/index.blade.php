@extends('layouts.admin')

@section('title', 'Master Data Gapoktan')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Master Data Gapoktan']
]" />

<div class="card custom-card border-0 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1">Daftar Gabungan Kelompok Tani (Gapoktan)</h5>
            <p class="text-muted small mb-0">Kelola kelompok induk tani di tingkat kecamatan.</p>
        </div>
        <a href="{{ route('gapoktans.create') }}" class="btn btn-success rounded-3 px-4 py-2">
            <i class="bi bi-plus-circle me-1"></i> Tambah Gapoktan
        </a>
    </div>

    <x-table :headers="['No', 'Nama Gapoktan', 'Kecamatan', 'Ketua', 'Aksi']" id="gapoktanTable" />
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#gapoktanTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('gapoktans.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '10%'},
                {data: 'nama', name: 'nama'},
                {data: 'kecamatan_nama', name: 'kecamatan.nama'},
                {data: 'ketua', name: 'ketua', defaultContent: '-'},
                {data: 'action', name: 'action', orderable: false, searchable: false, width: '20%'}
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            }
        });
    });
</script>
@endsection

