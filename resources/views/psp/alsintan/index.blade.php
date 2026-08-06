@extends('layouts.admin')

@section('title', 'Bantuan Alsintan - Bidang PSP')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Prasarana & Sarana Pertanian (PSP)'],
    ['label' => 'Bantuan Alsintan']
]" />

<div class="card custom-card border-0 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1">Daftar Bantuan Alsintan</h5>
            <p class="text-muted small mb-0">Kelola data penerima bantuan alat dan mesin pertanian beserta status operasionalnya.</p>
        </div>
        <a href="{{ route('alsintans.create') }}" class="btn btn-success rounded-3 px-4 py-2">
            <i class="bi bi-plus-circle me-1"></i> Tambah Bantuan
        </a>
    </div>

    <x-table :headers="['No', 'Nama Alat', 'Jenis Alat', 'Merek', 'Kelompok Tani', 'Ketua', 'Kondisi', 'Sumber Dana', 'Tahun', 'Aksi']" id="alsintanTable" />
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#alsintanTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('alsintans.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '5%'},
                {data: 'nama_alat', name: 'nama_alat'},
                {data: 'jenis_alat_nama', name: 'jenis_alat_nama'},
                {data: 'merek', name: 'merek', defaultContent: '-'},
                {data: 'kelompok_tani_nama', name: 'kelompok_tani_nama'},
                {data: 'nama_ketua', name: 'nama_ketua', defaultContent: '-'},
                {
                    data: 'kondisi', 
                    name: 'kondisi',
                    render: function(data) {
                        let badgeClass = 'bg-success';
                        if (data === 'Rusak Ringan') {
                            badgeClass = 'bg-warning text-dark';
                        } else if (data === 'Rusak Berat') {
                            badgeClass = 'bg-danger';
                        }
                        return `<span class="badge ${badgeClass} px-3 py-2 rounded-pill">${data}</span>`;
                    }
                },
                {data: 'sumber_dana', name: 'sumber_dana'},
                {data: 'tahun_bantuan', name: 'tahun_bantuan'},
                {data: 'action', name: 'action', orderable: false, searchable: false, width: '18%'}
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            }
        });
    });
</script>
@endsection


