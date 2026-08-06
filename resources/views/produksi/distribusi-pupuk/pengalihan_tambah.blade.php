@extends('layouts.admin')

@section('title', 'Tambah Pengalihan Kuota Pupuk')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Distribusi Pupuk', 'url' => route('distribusi-pupuk.index')],
    ['label' => 'Pengalihan Kuota', 'url' => route('distribusi-pupuk.pengalihan.index')],
    ['label' => 'Tambah Transaksi']
]" />

<div class="row">
    <div class="col-md-8">
        <div class="card custom-card border-0 p-4">
            <h5 class="fw-bold mb-4"><i class="bi bi-arrow-left-right text-success me-2"></i>Form Pengalihan Kuota Pupuk</h5>

            @if($errors->any())
                <div class="alert alert-danger rounded-3 mb-4">
                    <ul class="mb-0">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('distribusi-pupuk.pengalihan.store') }}" method="POST" id="pengalihanForm" enctype="multipart/form-data">
                @csrf

                <!-- Periode & Jenis Pupuk -->
                <div class="row g-3 mb-4 bg-light rounded-3 p-3 border border-light-subtle">
                    <div class="col-md-4">
                        <label for="tahun" class="form-label fw-semibold text-secondary small">Tahun</label>
                        <select name="tahun" id="tahun" class="form-select border-0 shadow-sm rounded-3" required>
                            @foreach($years as $yr)
                                <option value="{{ $yr }}" {{ date('Y') == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="bulan" class="form-label fw-semibold text-secondary small">Bulan</label>
                        <select name="bulan" id="bulan" class="form-select border-0 shadow-sm rounded-3" required>
                            @foreach($months as $num => $nama)
                                <option value="{{ $num }}" {{ date('n') == $num ? 'selected' : '' }}>{{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="jenis_pupuk_id" class="form-label fw-semibold text-secondary small">Jenis Pupuk</label>
                        <select name="jenis_pupuk_id" id="jenis_pupuk_id" class="form-select border-0 shadow-sm rounded-3" required>
                            <option value="">-- Pilih Jenis Pupuk --</option>
                            @foreach($jenisPupuks as $jp)
                                <option value="{{ $jp->id }}">{{ $jp->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Form Inputs yang tersembunyi hingga Periode/Jenis dipilih -->
                <div id="dynamic-form-fields" class="d-none">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="kecamatan_asal_id" class="form-label fw-semibold text-secondary small">Kecamatan Asal (Penebusan < 75%)</label>
                            <select name="kecamatan_asal_id" id="kecamatan_asal_id" class="form-select rounded-3 shadow-sm border border-light-subtle" required>
                                <option value="">-- Pilih Kecamatan Asal --</option>
                            </select>
                            <div class="form-text text-danger small mt-1 d-none" id="sisa-info-container">
                                Sisa kuota tersedia: <strong id="sisa-val">0.00</strong> Kg.
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="kecamatan_tujuan_id" class="form-label fw-semibold text-secondary small">Kecamatan Tujuan (Penebusan ≥ 75%)</label>
                            <select name="kecamatan_tujuan_id" id="kecamatan_tujuan_id" class="form-select rounded-3 shadow-sm border border-light-subtle" required>
                                <option value="">-- Pilih Kecamatan Tujuan --</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="jumlah" class="form-label fw-semibold text-secondary small">Jumlah Dialihkan (Kg)</label>
                            <input type="number" step="0.01" min="0.01" name="jumlah" id="jumlah" class="form-control rounded-3 shadow-sm border border-light-subtle" placeholder="0.00" required>
                        </div>
                        <div class="col-md-6">
                            <label for="keterangan" class="form-label fw-semibold text-secondary small">Keterangan / Alasan Pengalihan</label>
                            <input type="text" name="keterangan" id="keterangan" class="form-control rounded-3 shadow-sm border border-light-subtle" placeholder="Contoh: Realisasi rendah di kecamatan asal, dialihkan ke tujuan">
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="nama_sk" class="form-label fw-semibold text-secondary small">Nama SK Relokasi</label>
                            <input type="text" name="nama_sk" id="nama_sk" class="form-control rounded-3 shadow-sm border border-light-subtle" placeholder="Contoh: SK Relokasi 1" required>
                        </div>
                        <div class="col-md-6">
                            <label for="bukti_sk" class="form-label fw-semibold text-secondary small">Dokumen SK Relokasi (PDF/Gambar)</label>
                            <input type="file" name="bukti_sk" id="bukti_sk" class="form-control rounded-3 shadow-sm border border-light-subtle" required>
                        </div>
                    </div>
                </div>

                <div id="select-placeholder" class="text-center p-5 border border-dashed rounded-3 text-muted mb-4">
                    <i class="bi bi-arrow-left-right fs-1 text-secondary mb-3 d-block"></i>
                    Silakan pilih Jenis Pupuk terlebih dahulu untuk memuat daftar kecamatan yang memenuhi syarat pengalihan.
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success px-4 rounded-3 d-none" id="btn-submit">Simpan Transaksi</button>
                    <a href="{{ route('distribusi-pupuk.pengalihan.index') }}" class="btn btn-light px-4 rounded-3 text-secondary border">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        let asalKecamatans = [];

        const loadEligibleKecamatans = () => {
            const tahun = $('#tahun').val();
            const bulan = $('#bulan').val();
            const jenisId = $('#jenis_pupuk_id').val();

            if (!jenisId) {
                $('#dynamic-form-fields').addClass('d-none');
                $('#btn-submit').addClass('d-none');
                $('#select-placeholder').removeClass('d-none');
                return;
            }

            $.ajax({
                url: "{{ route('distribusi-pupuk.ajax-kecamatan-pengalihan') }}",
                type: 'GET',
                data: {
                    tahun: tahun,
                    bulan: bulan,
                    jenis_pupuk_id: jenisId
                },
                success: function(response) {
                    $('#select-placeholder').addClass('d-none');
                    $('#dynamic-form-fields').removeClass('d-none');
                    $('#btn-submit').removeClass('d-none');

                    asalKecamatans = response.asal;

                    // Populate Asal
                    let asalHtml = '<option value="">-- Pilih Kecamatan Asal --</option>';
                    if (asalKecamatans.length === 0) {
                        asalHtml = '<option value="">-- Tidak ada kecamatan asal (<75%) --</option>';
                    } else {
                        asalKecamatans.forEach(kec => {
                            asalHtml += `<option value="${kec.id}">${kec.nama} (Realisasi: ${kec.persentase}%)</option>`;
                        });
                    }
                    $('#kecamatan_asal_id').html(asalHtml);

                    // Populate Tujuan
                    let tujuanHtml = '<option value="">-- Pilih Kecamatan Tujuan --</option>';
                    if (response.tujuan.length === 0) {
                        tujuanHtml = '<option value="">-- Tidak ada kecamatan tujuan (≥75%) --</option>';
                    } else {
                        response.tujuan.forEach(kec => {
                            tujuanHtml += `<option value="${kec.id}">${kec.nama} (Realisasi: ${kec.persentase}%)</option>`;
                        });
                    }
                    $('#kecamatan_tujuan_id').html(tujuanHtml);
                    $('#sisa-info-container').addClass('d-none');
                    $('#jumlah').removeAttr('max');
                }
            });
        };

        $('#tahun, #bulan, #jenis_pupuk_id').on('change', loadEligibleKecamatans);

        // Show max transferable amount when asal is selected
        $('#kecamatan_asal_id').on('change', function() {
            const val = $(this).val();
            if (!val) {
                $('#sisa-info-container').addClass('d-none');
                $('#jumlah').removeAttr('max');
                return;
            }

            const chosen = asalKecamatans.find(k => k.id == val);
            if (chosen) {
                $('#sisa-val').text(chosen.sisa.toLocaleString('id-ID', {minimumFractionDigits: 2}));
                $('#sisa-info-container').removeClass('d-none');
                $('#jumlah').attr('max', chosen.sisa);
            }
        });
    });
</script>
<style>
    .border-dashed {
        border-style: dashed !important;
    }
</style>
@endsection
