@extends('layouts.admin')

@section('title', 'Laporan Produksi - Perkebunan')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Laporan Produksi'],
    ['label' => 'Perkebunan']
]" />

<div class="card custom-card border-0 shadow-sm p-4" style="border-radius:16px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1"><i class="bi bi-tree me-2 text-success"></i>Laporan Produksi Perkebunan Rakyat</h5>
            <p class="text-muted small mb-0">Kelola dan pantau statistik semesteran mutasi luas areal perkebunan rakyat.</p>
        </div>
        <a href="{{ route('perkebunan.create') }}" class="btn btn-success rounded-3 px-4 py-2 shadow-sm">
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

    {{-- Tabel DataTables dengan headers SPH Perkebunan --}}
    <x-table :headers="['No', 'Kecamatan', 'Komoditas', 'Periode', 'Tahun', 'Luas Areal (Ha)', 'TBM (Ha)', 'TM (Ha)', 'TTM (Ha)', 'Produksi (Kg)', 'Wujud Produksi', 'Petani Pemilik', 'Aksi']" id="laporanTable" />
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#laporanTable').append(`
            <tfoot class="table-light fw-bold">
                <tr>
                    <td colspan="5" class="text-end">Total Halaman Ini:</td>
                    <td id="footerAreal" class="text-end text-success">0.00 Ha</td>
                    <td id="footerTBM" class="text-end text-success">0.00 Ha</td>
                    <td id="footerTM" class="text-end text-success">0.00 Ha</td>
                    <td id="footerTTM" class="text-end text-success">0.00 Ha</td>
                    <td id="footerProduksi" class="text-end text-success">0.00 Kg</td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        `);

        const table = $('#laporanTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('perkebunan.index') }}",
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
                {
                    data: 'luas_jumlah',
                    name: 'luas_jumlah',
                    className: 'text-end',
                    render: function(data){ return parseFloat(data || 0).toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' Ha'; }
                },
                {
                    data: 'tbm',
                    name: 'tbm',
                    className: 'text-end',
                    render: function(data){ return parseFloat(data || 0).toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' Ha'; }
                },
                {
                    data: 'tm',
                    name: 'tm',
                    className: 'text-end',
                    render: function(data){ return parseFloat(data || 0).toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' Ha'; }
                },
                {
                    data: 'ttm',
                    name: 'ttm',
                    className: 'text-end',
                    render: function(data){ return parseFloat(data || 0).toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' Ha'; }
                },
                {
                    data: 'produksi',
                    name: 'produksi',
                    className: 'text-end',
                    render: function(data){ return parseFloat(data || 0).toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' Kg'; }
                },
                {
                    data: 'wujud_produksi',
                    name: 'wujud_produksi',
                    className: 'text-center',
                    render: function(data){ return data ? data : '-'; }
                },
                {
                    data: 'jumlah_petani_pemilik',
                    name: 'jumlah_petani_pemilik',
                    className: 'text-end',
                    render: function(data){ return parseInt(data || 0).toLocaleString('id-ID') + ' KK'; }
                },
                {data: 'action', name: 'action', orderable: false, searchable: false, width: '8%'}
            ],
            footerCallback: function (row, data, start, end, display) {
                var api = this.api();

                var intVal = function (i) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '')*1 :
                        typeof i === 'number' ?
                            i : 0;
                };

                var totalAreal = api
                    .column(5, { page: 'current' })
                    .data()
                    .reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);

                var totalTBM = api
                    .column(6, { page: 'current' })
                    .data()
                    .reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);

                var totalTM = api
                    .column(7, { page: 'current' })
                    .data()
                    .reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);

                var totalTTM = api
                    .column(8, { page: 'current' })
                    .data()
                    .reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);

                var totalProduksi = api
                    .column(9, { page: 'current' })
                    .data()
                    .reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);

                $('#footerAreal').html(totalAreal.toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' Ha');
                $('#footerTBM').html(totalTBM.toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' Ha');
                $('#footerTM').html(totalTM.toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' Ha');
                $('#footerTTM').html(totalTTM.toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' Ha');
                $('#footerProduksi').html(totalProduksi.toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' Kg');
            },
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
