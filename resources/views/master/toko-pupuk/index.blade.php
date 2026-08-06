@extends('layouts.admin')

@section('title', 'Master Data Toko/Distributor Pupuk')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Master Data Toko/Distributor']
]" />

<div class="card custom-card border-0 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1">Daftar Toko/Distributor Pupuk</h5>
            <p class="text-muted small mb-0">Kelola data Toko/Distributor pupuk resmi bersubsidi beserta wilayah ampuannya.</p>
        </div>
        <a href="{{ route('toko-pupuks.create') }}" class="btn btn-success rounded-3 px-4 py-2">
            <i class="bi bi-plus-circle me-1"></i> Tambah Toko
        </a>
    </div>

    <x-table :headers="['No', 'Nama Toko', 'Pemilik', 'Telepon', 'Alamat', 'Wilayah Ampuan (Kecamatan)', 'Aksi']" id="tokoPupukTable" />
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#tokoPupukTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('toko-pupuks.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '5%'},
                {data: 'nama', name: 'nama'},
                {data: 'pemilik', name: 'pemilik'},
                {data: 'telepon', name: 'telepon'},
                {data: 'alamat', name: 'alamat'},
                {data: 'kecamatan_list', name: 'kecamatan_list', orderable: false, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false, width: '15%'}
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            }
        });
    });
</script>
@endsection


