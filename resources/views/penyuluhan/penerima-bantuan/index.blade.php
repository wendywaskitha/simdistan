@extends('layouts.admin')

@section('title', 'Data Penerima Bantuan')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Penerima Bantuan']
]" />

<div class="card custom-card border-0 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1">Data Penerima Bantuan</h5>
            <p class="text-muted small mb-0">Rangkuman penerima bantuan sektor pertanian di Kabupaten Muna Barat.</p>
        </div>
        <div class="d-flex gap-2">
            <div style="width: 180px;">
                <label for="filterSumberDana" class="form-label small fw-semibold text-muted mb-1">Pilih Sumber Dana</label>
                <select id="filterSumberDana" class="form-select rounded-3 border-secondary-subtle">
                    <option value="">Semua Sumber Dana</option>
                    @foreach($sumberDana as $sd)
                        <option value="{{ $sd }}">{{ $sd }}</option>
                    @endforeach
                </select>
            </div>
            <div style="width: 150px;">
                <label for="filterTahun" class="form-label small fw-semibold text-muted mb-1">Pilih Tahun</label>
                <select id="filterTahun" class="form-select rounded-3 border-secondary-subtle">
                    <option value="">Semua Tahun</option>
                    @foreach($tahuns as $t)
                        <option value="{{ $t }}">{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div style="width: 200px;">
                <label for="filterBantuan" class="form-label small fw-semibold text-muted mb-1">Pilih Kategori Bantuan</label>
                <select id="filterBantuan" class="form-select rounded-3 border-secondary-subtle">
                    <option value="alsintan">Bantuan Alsintan</option>
                    <option value="infrastruktur">Bantuan Infrastruktur</option>
                    <option value="benih_pangan">Bantuan Benih (Pangan)</option>
                    <option value="bibit_horti">Bantuan Bibit (Hortikultura)</option>
                    <option value="bibit_perkebunan">Bantuan Bibit (Perkebunan)</option>
                </select>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle" id="penerimaBantuanTable" style="width: 100%;">
            <thead class="table-light">
                <tr id="tableHeaders">
                    <!-- Dinamis via JS -->
                </tr>
            </thead>
            <tbody>
                <!-- Dinamis via Datatables -->
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        let table = null;

        const config = {
            alsintan: {
                headers: ['No', 'Penerima (Kelompok)', 'Wilayah (Desa/Kec)', 'Alat & Mesin Pertanian', 'Tahun', 'Kondisi & Sumber Dana'],
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '5%'},
                    {data: 'penerima', name: 'penerima'},
                    {data: 'kecamatan_desa', name: 'kecamatan_desa'},
                    {data: 'nama_bantuan', name: 'nama_bantuan'},
                    {data: 'tahun', name: 'tahun', class: 'text-center', width: '10%'},
                    {data: 'detail', name: 'detail'}
                ]
            },
            infrastruktur: {
                headers: ['No', 'Penerima (Kelompok)', 'Wilayah (Desa/Kec)', 'Jenis & Proyek Infrastruktur', 'Tahun', 'Volume, Anggaran & Status'],
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '5%'},
                    {data: 'penerima', name: 'penerima'},
                    {data: 'kecamatan_desa', name: 'kecamatan_desa'},
                    {data: 'nama_bantuan', name: 'nama_bantuan'},
                    {data: 'tahun', name: 'tahun', class: 'text-center', width: '10%'},
                    {data: 'detail', name: 'detail'}
                ]
            },
            benih_pangan: {
                headers: ['No', 'Penerima (Kelompok)', 'Wilayah (Desa/Kec)', 'Komoditas & Varietas', 'Tahun', 'Jumlah, Satuan & Sumber Dana'],
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '5%'},
                    {data: 'penerima', name: 'penerima'},
                    {data: 'kecamatan_desa', name: 'kecamatan_desa'},
                    {data: 'nama_bantuan', name: 'nama_bantuan'},
                    {data: 'tahun', name: 'tahun', class: 'text-center', width: '10%'},
                    {data: 'detail', name: 'detail'}
                ]
            },
            bibit_horti: {
                headers: ['No', 'Penerima (Kelompok)', 'Wilayah (Desa/Kec)', 'Komoditas Bibit', 'Tahun', 'Jumlah, Satuan & Sumber Dana'],
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '5%'},
                    {data: 'penerima', name: 'penerima'},
                    {data: 'kecamatan_desa', name: 'kecamatan_desa'},
                    {data: 'nama_bantuan', name: 'nama_bantuan'},
                    {data: 'tahun', name: 'tahun', class: 'text-center', width: '10%'},
                    {data: 'detail', name: 'detail'}
                ]
            },
            bibit_perkebunan: {
                headers: ['No', 'Penerima (Kelompok)', 'Wilayah (Desa/Kec)', 'Komoditas Bibit', 'Tahun', 'Jumlah, Satuan & Sumber Dana'],
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '5%'},
                    {data: 'penerima', name: 'penerima'},
                    {data: 'kecamatan_desa', name: 'kecamatan_desa'},
                    {data: 'nama_bantuan', name: 'nama_bantuan'},
                    {data: 'tahun', name: 'tahun', class: 'text-center', width: '10%'},
                    {data: 'detail', name: 'detail'}
                ]
            }
        };

        function initTable(type) {
            // Hancurkan tabel jika sudah ada
            if (table) {
                table.destroy();
                $('#penerimaBantuanTable tbody').empty();
            }

            // Set Header Kolom
            const headerRow = $('#tableHeaders');
            headerRow.empty();
            config[type].headers.forEach(h => {
                headerRow.append(`<th class="fw-semibold">${h}</th>`);
            });

            // Inisialisasi DataTable baru
            table = $('#penerimaBantuanTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('penerima-bantuan.data') }}",
                    data: function(d) {
                        d.type = type;
                        d.sumber_dana = $('#filterSumberDana').val();
                        d.tahun = $('#filterTahun').val();
                    }
                },
                columns: config[type].columns,
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                }
            });
        }

        // Jalankan inisialisasi awal
        initTable('alsintan');

        // Event change dropdown kategori
        $('#filterBantuan').on('change', function() {
            initTable($(this).val());
        });

        // Event change dropdown sumber dana
        $('#filterSumberDana').on('change', function() {
            if (table) {
                table.ajax.reload();
            }
        });

        // Event change dropdown tahun
        $('#filterTahun').on('change', function() {
            if (table) {
                table.ajax.reload();
            }
        });
    });
</script>
@endsection
