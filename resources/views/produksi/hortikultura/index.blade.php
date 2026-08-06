@extends('layouts.admin')

@section('title', 'Laporan Produksi - Hortikultura')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Laporan Produksi'],
    ['label' => 'Hortikultura']
]" />

<div class="card custom-card border-0 shadow-sm p-4" style="border-radius: 16px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1"><i class="bi bi-flower1 me-2 text-primary"></i>Laporan Produksi Hortikultura</h5>
            <p class="text-muted small mb-0">Kelola dan pantau statistik SPH-SBS (Sayuran/Buah Semusim), SPH-BST (Tahunan), dan SPH-TBF (Biofarmaka).</p>
        </div>
        <a href="{{ route('hortikultura.create') }}" class="btn btn-primary rounded-3 px-4 py-2 shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Tambah Laporan
        </a>
    </div>

    <!-- Filter Kecamatan & Komoditas -->
    <div class="row g-3 mb-4 bg-light rounded-3 p-3 border border-light-subtle">
        <div class="col-md-4">
            <label for="filterKecamatan" class="form-label fw-semibold text-secondary small">Filter Kecamatan</label>
            <select id="filterKecamatan" class="form-select border-0 shadow-sm rounded-3">
                <option value="">-- Semua Kecamatan --</option>
                @foreach($kecamatans as $id => $nama)
                    <option value="{{ $id }}">{{ $nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label for="filterKomoditas" class="form-label fw-semibold text-secondary small">Filter Komoditas</label>
            <select id="filterKomoditas" class="form-select border-0 shadow-sm rounded-3">
                <option value="">-- Semua Komoditas --</option>
                @foreach($komoditas as $id => $nama)
                    <option value="{{ $id }}">{{ $nama }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Tabel DataTables --}}
    <x-table :headers="['No', 'Kecamatan', 'Komoditas', 'Periode', 'Tahun', 'Form', 'Luas Akhir / Jml Pohon', 'Luas Panen', 'Produksi', 'Satuan', 'Aksi']" id="laporanTable" />
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        const table = $('#laporanTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('hortikultura.index') }}",
                data: function(d) {
                    d.kecamatan_id = $('#filterKecamatan').val();
                    d.komoditas_id = $('#filterKomoditas').val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '4%'},
                {data: 'kecamatan_nama', name: 'kecamatan.nama'},
                {data: 'komoditas_nama', name: 'komoditas.nama'},
                {data: 'bulan_nama', name: 'bulan'},
                {data: 'tahun', name: 'tahun', className: 'text-center'},
                {data: 'form_badge', name: 'form_type', className: 'text-center'},
                {data: 'tanam_atau_pohon', name: 'luas_tanam_akhir', className: 'text-end'},
                {data: 'panen_formatted', name: 'luas_panen', className: 'text-end'},
                {data: 'produksi_formatted', name: 'produksi', className: 'text-end'},
                {data: 'satuan_nama', name: 'satuan.nama'},
                {data: 'action', name: 'action', orderable: false, searchable: false, width: '10%'}
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            }
        });

        $('#filterKecamatan, #filterKomoditas').on('change', function() {
            table.draw();
        });
    });
</script>
@endsection
