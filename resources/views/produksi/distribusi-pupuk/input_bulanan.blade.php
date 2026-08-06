@extends('layouts.admin')

@section('title', 'Input Laporan Distribusi Pupuk')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Distribusi Pupuk', 'url' => route('distribusi-pupuk.index')],
    ['label' => 'Input Laporan Bulanan']
]" />

<div class="card custom-card border-0 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <div>
            <h5 class="fw-bold mb-1">Form Pengisian Distribusi Pupuk Bulanan</h5>
            <p class="text-muted small mb-0">Masukkan kuota dan realisasi penebusan pupuk bersubsidi per Toko Distributor.</p>
        </div>
        <a href="{{ route('distribusi-pupuk.index') }}" class="btn btn-light rounded-3 text-secondary border">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <form action="{{ route('distribusi-pupuk.simpan-bulanan') }}" method="POST" id="laporanForm">
        @csrf

        <!-- Metadata Input -->
        <div class="row g-3 mb-4 bg-light rounded-3 p-3 border border-light-subtle">
            <div class="col-md-3">
                <label for="toko_pupuk_id" class="form-label fw-semibold text-secondary small">Pilih Toko/Distributor</label>
                <select name="toko_pupuk_id" id="toko_pupuk_id" class="form-select border-0 shadow-sm rounded-3" required>
                    <option value="">-- Pilih Toko/Distributor --</option>
                    @foreach($tokos as $toko)
                        <option value="{{ $toko->id }}">{{ $toko->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="satuan_id" class="form-label fw-semibold text-secondary small">Satuan Ukur</label>
                <select name="satuan_id" id="satuan_id" class="form-select border-0 shadow-sm rounded-3" required>
                    <option value="">-- Pilih Satuan --</option>
                    @foreach($satuans as $id => $nama)
                        <option value="{{ $id }}">{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="bulan" class="form-label fw-semibold text-secondary small">Bulan Laporan</label>
                <select name="bulan" id="bulan" class="form-select border-0 shadow-sm rounded-3" required>
                    @foreach($months as $num => $nama)
                        <option value="{{ $num }}" {{ date('n') == $num ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="tahun" class="form-label fw-semibold text-secondary small">Tahun</label>
                <select name="tahun" id="tahun" class="form-select border-0 shadow-sm rounded-3" required>
                    @foreach($years as $yr)
                        <option value="{{ $yr }}" {{ date('Y') == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Dynamic Grid Container -->
        <div id="grid-container" class="mb-4 d-none">
            <h6 class="fw-bold text-success mb-3"><i class="bi bi-grid-3x3-gap-fill me-2"></i>Rincian Wilayah Ampuan dan Penebusan Pupuk</h6>
            <div class="table-responsive border rounded-3 overflow-hidden">
                <table class="table table-bordered align-middle mb-0 text-center small">
                    <thead class="table-light align-middle fw-bold">
                        <tr>
                            <th class="text-start" width="25%">Kecamatan</th>
                            @foreach($jenisPupuks as $jp)
                                <th>Penebusan {{ $jp->nama }} <span class="unit-label">(Kg)</span></th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody id="grid-body">
                        <!-- Loaded dynamically -->
                    </tbody>
                </table>
            </div>
        </div>

        <div id="initial-placeholder" class="text-center p-5 border border-dashed rounded-3 text-muted">
            <i class="bi bi-shop fs-1 text-secondary mb-3 d-block"></i>
            Silakan pilih Toko/Distributor terlebih dahulu untuk memuat form wilayah ampuannya.
        </div>

        <div class="d-flex gap-2 d-none" id="form-actions">
            <button type="submit" class="btn btn-success px-4 rounded-3">Simpan Laporan</button>
            <a href="{{ route('distribusi-pupuk.index') }}" class="btn btn-light px-4 rounded-3 text-secondary border">Batal</a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        const jenisPupuks = @json($jenisPupuks);

        const updateHeaderUnits = () => {
            const unitText = $('#satuan_id option:selected').text().trim() || 'Kg';
            if (unitText && unitText !== '-- Pilih Satuan --') {
                $('.unit-label').text('(' + unitText + ')');
            }
        };
        $('#satuan_id').on('change', updateHeaderUnits);

        const loadGrid = () => {
            const tokoId = $('#toko_pupuk_id').val();
            const bulan = $('#bulan').val();
            const tahun = $('#tahun').val();

            if (!tokoId) {
                $('#grid-container').addClass('d-none');
                $('#form-actions').addClass('d-none');
                $('#initial-placeholder').removeClass('d-none');
                return;
            }

            $.ajax({
                url: "{{ route('distribusi-pupuk.ajax-toko-kecamatan') }}",
                type: 'GET',
                data: {
                    toko_pupuk_id: tokoId,
                    bulan: bulan,
                    tahun: tahun
                },
                success: function(response) {
                    $('#initial-placeholder').addClass('d-none');
                    $('#grid-container').removeClass('d-none');
                    $('#form-actions').removeClass('d-none');

                    if (response.satuan_id) {
                        $('#satuan_id').val(response.satuan_id);
                    } else {
                        const kgOption = $('#satuan_id option').filter(function() {
                            return $(this).text().trim().toLowerCase() === 'kg';
                        });
                        if (kgOption.length) {
                            $('#satuan_id').val(kgOption.val());
                        }
                    }
                    updateHeaderUnits();

                    let html = '';
                    if (response.kecamatans.length === 0) {
                        html = `<tr><td colspan="${jenisPupuks.length + 1}" class="text-center text-danger py-4">Toko ini belum dikonfigurasi wilayah kecamatan ampuannya. Silakan edit data Toko di menu Master.</td></tr>`;
                        $('#form-actions').addClass('d-none');
                    } else {
                        response.kecamatans.forEach(kec => {
                            let cells = '';
                            jenisPupuks.forEach(jp => {
                                let penebusan = '';

                                if (response.existing_details[kec.id] && response.existing_details[kec.id][jp.id]) {
                                    penebusan = response.existing_details[kec.id][jp.id].penebusan;
                                }

                                cells += `
                                    <td>
                                        <input type="number" step="0.01" min="0" name="data[${kec.id}][${jp.id}][penebusan]" value="${penebusan}" class="form-control text-end shadow-none border-0 bg-transparent fw-bold text-success" placeholder="0.00">
                                    </td>
                                `;
                            });

                            html += `
                                <tr>
                                    <td class="text-start fw-semibold text-secondary bg-light">${kec.nama}</td>
                                    ${cells}
                                </tr>
                            `;
                        });
                    }

                    $('#grid-body').html(html);
                }
            });
        };

        $('#toko_pupuk_id, #bulan, #tahun').on('change', loadGrid);
    });
</script>
<style>
    .border-dashed {
        border-style: dashed !important;
    }
</style>
@endsection
