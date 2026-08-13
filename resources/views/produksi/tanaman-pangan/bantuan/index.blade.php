@extends('layouts.admin')

@section('title', 'Kelola Bantuan Benih Tanaman Pangan')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Tanaman Pangan', 'url' => route('tanaman-pangan.index')],
    ['label' => 'Bantuan Benih']
]" />

<div class="card custom-card border-0 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1">Daftar Bantuan Benih Tanaman Pangan</h5>
            <p class="text-muted small mb-0">Kelola riwayat bantuan benih padi, jagung, dll., kepada Kelompok Tani.</p>
        </div>
        <a href="{{ route('bantuan-benih-pangan.create') }}" class="btn btn-success rounded-3 px-4 py-2">
            <i class="bi bi-plus-circle me-1"></i> Tambah Bantuan
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <x-table :headers="['No', 'Kelompok Tani', 'Komoditas', 'Varietas', 'Jumlah Bantuan', 'Sumber Dana', 'Tahun', 'Keterangan', 'Aksi']" id="bantuanTable" />
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#bantuanTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('bantuan-benih-pangan.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '6%'},
                {data: 'kelompok_tani_nama', name: 'kelompokTani.nama'},
                {data: 'komoditas_nama', name: 'komoditas.nama'},
                {data: 'varietas_nama', name: 'varietas.nama', defaultContent: '-'},
                {data: 'jumlah_bantuan', name: 'jumlah_bantuan', class: 'text-end'},
                {data: 'sumber_dana', name: 'sumber_dana'},
                {data: 'tahun_bantuan', name: 'tahun_bantuan', class: 'text-center'},
                {data: 'keterangan', name: 'keterangan', defaultContent: '-'},
                {data: 'action', name: 'action', orderable: false, searchable: false, width: '12%'}
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            }
        });
    });
</script>
@endsection
