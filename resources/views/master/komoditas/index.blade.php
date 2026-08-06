@extends('layouts.admin')

@section('title', 'Master Data Komoditas')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Data Komoditas']
]" />

<div class="card custom-card border-0 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1">Daftar Komoditas Pertanian</h5>
            <p class="text-muted small mb-0">Kelola data komoditas dinamis (padi, jagung, cengkeh, dll).</p>
        </div>
        <a href="{{ route('komoditas.create') }}" class="btn btn-success rounded-3 px-4 py-2">
            <i class="bi bi-plus-circle me-1"></i> Tambah Komoditas
        </a>
    </div>

    <div class="mb-4">
        <ul class="nav nav-pills custom-pills" id="kategoriTabs">
            <li class="nav-item">
                <a class="nav-link active rounded-pill px-4" href="#" data-id="">Semua Kategori</a>
            </li>
            @foreach($kategoris as $kategori)
                <li class="nav-item">
                    <a class="nav-link rounded-pill px-4" href="#" data-id="{{ $kategori->id }}">{{ $kategori->nama }}</a>
                </li>
            @endforeach
        </ul>
    </div>

    <x-table :headers="['No', 'Nama Komoditas', 'Kategori', 'Durasi Panen', 'Aksi']" id="komoditasTable" />
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        let kategoriId = '';

        const table = $('#komoditasTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('komoditas.index') }}",
                data: function(d) {
                    d.kategori_id = kategoriId;
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '10%'},
                {data: 'nama', name: 'nama'},
                {data: 'kategori_nama', name: 'kategori.nama'},
                {data: 'durasi_formatted', name: 'durasi_panen_bulan'},
                {data: 'action', name: 'action', orderable: false, searchable: false, width: '20%'}
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            }
        });

        $('#kategoriTabs .nav-link').on('click', function(e) {
            e.preventDefault();
            $('#kategoriTabs .nav-link').removeClass('active');
            $(this).addClass('active');
            kategoriId = $(this).data('id');
            table.ajax.reload();
        });
    });
</script>
@endsection

