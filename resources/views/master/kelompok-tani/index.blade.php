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
        <a href="{{ route('kelompok-tanis.create') }}" class="btn btn-success rounded-3 px-4 py-2">
            <i class="bi bi-plus-circle me-1"></i> Tambah Kelompok Tani
        </a>
    </div>

    <x-table :headers="['No', 'Nama Kelompok Tani', 'Desa (Kec)', 'Induk Gapoktan', 'Ketua', 'Dokumen SK', 'Berita Acara', 'Aksi']" id="kelompokTaniTable" />
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

