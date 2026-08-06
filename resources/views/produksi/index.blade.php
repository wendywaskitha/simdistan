@extends('layouts.admin')

@section('title', 'Laporan Produksi Pertanian')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Laporan Produksi']
]" />

<div class="card custom-card border-0 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1">Laporan Produksi Hasil Pertanian</h5>
            <p class="text-muted small mb-0">Kelola dan pantau statistik luas lahan serta hasil panen komoditas per tahun.</p>
        </div>
        <a href="#" id="btnTambahLaporan" class="btn btn-success rounded-3 px-4 py-2">
            <i class="bi bi-plus-circle me-1"></i> Kelola Laporan Bulanan
        </a>
    </div>

    <!-- Tab Kategori -->
    <ul class="nav nav-tabs nav-tabs-custom border-bottom-0 mb-4" id="kategoriTab" role="tablist">
        @foreach($kategoris as $kategori)
            <li class="nav-item" role="presentation">
                <a href="{{ route('laporan-produksis.index', ['kategori_id' => $kategori->id]) }}" 
                   class="nav-link {{ $activeKategoriId == $kategori->id ? 'active fw-bold text-success border-success' : 'text-secondary' }} border-top-0 border-start-0 border-end-0 border-bottom-3" 
                   role="tab">
                    {{ $kategori->nama }}
                </a>
            </li>
        @endforeach
    </ul>

    <!-- Filter Kecamatan -->
    <div class="row g-3 mb-4 bg-light rounded-3 p-3 border border-light-subtle">
        <div class="col-md-4">
            <label for="filterKecamatan" class="form-label fw-semibold text-secondary small">Wilayah Kerja Kecamatan</label>
            <select id="filterKecamatan" class="form-select border-0 shadow-sm rounded-3">
                @foreach($kecamatans as $id => $nama)
                    <option value="{{ $id }}" {{ $loop->first ? 'selected' : '' }}>{{ $nama }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Table Matriks Akumulasi Tahunan -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle" id="laporanTable" style="width:100%">
            <thead class="table-light align-middle text-center small fw-bold">
                <tr>
                    <th rowspan="2" width="4%">No</th>
                    <th rowspan="2" width="12%">Komoditas</th>
                    @for($tahun = 2022; $tahun <= 2026; $tahun++)
                        <th colspan="4" class="border-bottom">{{ $tahun }}</th>
                    @endfor
                    <th rowspan="2" width="6%">Aksi</th>
                </tr>
                <tr class="small text-muted">
                    @for($tahun = 2022; $tahun <= 2026; $tahun++)
                        <th>Tanam</th>
                        <th>Panen</th>
                        <th>Prod.</th>
                        <th>Pdv.</th>
                    @endfor
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        const updateCreateUrl = () => {
            const kecId = $('#filterKecamatan').val();
            const url = "{{ route('laporan-produksis.create') }}?kategori_id={{ $activeKategoriId }}&kecamatan_id=" + kecId;
            $('#btnTambahLaporan').attr('href', url);
        };

        const table = $('#laporanTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('laporan-produksis.index') }}",
                data: function(d) {
                    d.kategori_id = "{{ $activeKategoriId }}";
                    d.kecamatan_id = $('#filterKecamatan').val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'komoditas_nama', name: 'komoditas_nama'},
                
                // Generasi Kolom 2022-2026
                @for($tahun = 2022; $tahun <= 2026; $tahun++)
                    {data: 'tanam_{{ $tahun }}', name: 'tanam_{{ $tahun }}', searchable: false, render: function(data){ return parseFloat(data).toLocaleString('id-ID') + ' Ha'; }},
                    {data: 'panen_{{ $tahun }}', name: 'panen_{{ $tahun }}', searchable: false, render: function(data){ return parseFloat(data).toLocaleString('id-ID') + ' Ha'; }},
                    {data: 'produksi_{{ $tahun }}', name: 'produksi_{{ $tahun }}', searchable: false, render: function(data){ return parseFloat(data).toLocaleString('id-ID'); }},
                    {data: 'produktivitas_{{ $tahun }}', name: 'produktivitas_{{ $tahun }}', searchable: false, render: function(data){ return parseFloat(data).toFixed(2).toLocaleString('id-ID'); }},
                @endfor
                
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            }
        });

        // Trigger redraw on filter change
        $('#filterKecamatan').on('change', function() {
            table.draw();
            updateCreateUrl();
        });

        // Init URL
        updateCreateUrl();
    });
</script>
<style>
    .nav-tabs-custom .nav-link {
        border-bottom: 3px solid transparent;
        transition: all 0.3s ease;
    }
    .nav-tabs-custom .nav-link.active {
        border-bottom-color: var(--primary) !important;
        background: transparent !important;
    }
    /* Mengurangi padding untuk menampung tabel yang sangat lebar */
    #laporanTable th, #laporanTable td {
        padding: 6px 4px !important;
        font-size: 0.82rem !important;
        white-space: nowrap;
    }
</style>
@endsection


