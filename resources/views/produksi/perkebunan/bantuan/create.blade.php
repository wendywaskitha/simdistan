@extends('layouts.admin')

@section('title', 'Tambah Bantuan Perkebunan')

@section('content')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        height: 38px;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 4px 12px;
    }
    .select2-container--default .select2-selection--single .select2-selection--arrow {
        height: 36px;
    }
    .select2-container {
        width: 100% !important;
    }
</style>

<x-breadcrumb :items="[
    ['label' => 'Perkebunan', 'url' => route('perkebunan.index')],
    ['label' => 'Bantuan Bibit', 'url' => route('bantuan-bibit-perkebunan.index')],
    ['label' => 'Tambah Bantuan']
]" />

<form action="{{ route('bantuan-bibit-perkebunan.store') }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-md-6">
            <div class="card custom-card border-0 p-4 mb-4">
                <h5 class="fw-bold mb-4">Form Tambah Bantuan Bibit</h5>

                <div class="mb-3">
                    <label for="kelompok_tani_id" class="form-label fw-semibold text-secondary">
                        Kelompok Tani <span class="text-danger">*</span>
                    </label>
                    <select id="kelompok_tani_id" name="kelompok_tani_id" class="form-select rounded-3 border-secondary-subtle" required>
                        <option value="">-- Cari Kelompok Tani --</option>
                    </select>
                </div>

                <x-form.select 
                    name="komoditas_id" 
                    label="Komoditas" 
                    placeholder="-- Pilih Komoditas --" 
                    :options="$komoditas"
                    required="true"
                />

                <div class="row">
                    <div class="col-md-6">
                        <x-form.input 
                            id="jumlahBantuan"
                            name="jumlah_bantuan" 
                            label="Jumlah Bantuan (Dihitung Otomatis)" 
                            type="number"
                            step="0.01"
                            placeholder="0" 
                            required="true" 
                            readonly="true"
                        />
                    </div>
                    <div class="col-md-6">
                        <x-form.input 
                            name="satuan" 
                            label="Satuan" 
                            placeholder="Contoh: Batang, Bibit, Kg" 
                            required="true" 
                            value="Batang"
                        />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <x-form.select 
                            name="sumber_dana" 
                            label="Sumber Dana" 
                            placeholder="-- Pilih --" 
                            :options="['APBN' => 'APBN', 'APBD Provinsi' => 'APBD Provinsi', 'APBD Kabupaten' => 'APBD Kabupaten', 'DAK' => 'DAK', 'Lainnya' => 'Lainnya']"
                            required="true"
                        />
                    </div>
                    <div class="col-md-6">
                        <x-form.input 
                            name="tahun_bantuan" 
                            label="Tahun Bantuan" 
                            type="number"
                            placeholder="Tahun" 
                            required="true" 
                            value="{{ date('Y') }}"
                        />
                    </div>
                </div>

                <x-form.textarea 
                    name="keterangan" 
                    label="Keterangan" 
                    placeholder="Keterangan tambahan (opsional)" 
                    rows="3" 
                />

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('bantuan-bibit-perkebunan.index') }}" class="btn btn-light rounded-3 px-4">Batal</a>
                    <button type="submit" class="btn btn-success rounded-3 px-4">Simpan Data</button>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card custom-card border-0 p-4">
                <h5 class="fw-bold mb-2">Detail Distribusi per Petani</h5>
                <p class="text-muted small mb-4">Pilih Kelompok Tani terlebih dahulu untuk memuat daftar petani anggotanya.</p>

                <div id="petaniListContainer" class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Petani</th>
                                <th>NIK</th>
                                <th style="width: 150px;">Jumlah Diterima</th>
                            </tr>
                        </thead>
                        <tbody id="petaniTableBody">
                            <tr>
                                <td colspan="3" class="text-center text-muted">Belum ada kelompok tani yang dipilih</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        const selectKelompokTani = $('#kelompok_tani_id');
        const tableBody = $('#petaniTableBody');
        const jumlahBantuanInput = $('#jumlah_bantuan');

        selectKelompokTani.select2({
            placeholder: '-- Cari Kelompok Tani (Ketik untuk mencari) --',
            allowClear: true,
            ajax: {
                url: "{{ route('kelompok-tanis.search') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.results
                    };
                },
                cache: true
            },
            minimumInputLength: 0
        });

        $('#komoditas_id').select2({
            placeholder: '-- Pilih Komoditas --',
            allowClear: true
        });

        function calculateTotal() {
            let total = 0;
            $('.petani-jumlah-input').each(function() {
                const val = parseFloat($(this).val());
                if (!isNaN(val)) {
                    total += val;
                }
            });
            jumlahBantuanInput.val(total.toFixed(2).replace(/\.00$/, ''));
        }

        tableBody.on('input', '.petani-jumlah-input', function() {
            calculateTotal();
        });

        selectKelompokTani.on('change', function() {
            const kelompokTaniId = $(this).val();
            if (!kelompokTaniId) {
                tableBody.html('<tr><td colspan="3" class="text-center text-muted">Belum ada kelompok tani yang dipilih</td></tr>');
                jumlahBantuanInput.val(0);
                return;
            }

            tableBody.html('<tr><td colspan="3" class="text-center"><div class="spinner-border spinner-border-sm text-success" role="status"></div> Memuat...</td></tr>');

            $.ajax({
                url: `/kelompok-tanis/${kelompokTaniId}/petanis`,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    tableBody.empty();
                    if (data.length === 0) {
                        tableBody.html('<tr><td colspan="3" class="text-center text-muted">Kelompok tani ini belum memiliki anggota petani</td></tr>');
                        jumlahBantuanInput.val(0);
                        return;
                    }

                    data.forEach(petani => {
                        tableBody.append(`
                            <tr>
                                <td>
                                    <span class="fw-semibold text-dark">${petani.nama}</span>
                                </td>
                                <td>
                                    <span class="text-muted small">${petani.nik}</span>
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" name="petani_jumlah[${petani.id}]" class="form-control form-control-sm petani-jumlah-input rounded-3 border-secondary-subtle" placeholder="0">
                                </td>
                            </tr>
                        `);
                    });
                    jumlahBantuanInput.val(0);
                },
                error: function() {
                    tableBody.html('<tr><td colspan="3" class="text-center text-danger">Gagal memuat data petani</td></tr>');
                }
            });
        });
    });
</script>
@endsection
