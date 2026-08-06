@extends('layouts.admin')

@section('title', 'Master Data Kecamatan')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Master Data Kecamatan']
]" />


<div class="card custom-card border-0 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1">Daftar Kecamatan</h5>
            <p class="text-muted small mb-0">Kelola data kecamatan di wilayah Kabupaten Muna Barat.</p>
        </div>
        <a href="{{ route('kecamatans.create') }}" class="btn btn-success rounded-3 px-4 py-2">
            <i class="bi bi-plus-circle me-1"></i> Tambah Kecamatan
        </a>
    </div>

    <x-table :headers="['No', 'Nama Kecamatan', 'Aksi']" id="kecamatanTable" />
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#kecamatanTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('kecamatans.index') }}",
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

