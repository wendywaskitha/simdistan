@extends('layouts.admin')

@section('title', 'Tambah Laporan Produksi - ' . $kategori->nama)

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Laporan Produksi', 'url' => route('perkebunan.index')],
    ['label' => 'Tambah Laporan ' . $kategori->nama]
]" />

{{-- Alert Error --}}
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4 shadow-sm" role="alert">
        <h6 class="fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i>Gagal Menyimpan Data:</h6>
        <ul class="mb-0 ps-3 small">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card custom-card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
    {{-- Header Banner --}}
    <div class="px-4 py-3 d-flex align-items-center justify-content-between"
         style="background: linear-gradient(135deg, #15803d, #22c55e); transition: background 0.4s ease;">
        <div>
            <h5 class="fw-bold text-white mb-0">
                <i class="bi bi-clipboard2-data me-2"></i>
                Form Laporan Produksi Perkebunan Rakyat
            </h5>
            <p class="text-white-50 small mb-0">Isi data luas areal, kondisi tanaman, wujud produksi, dan jumlah petani pemilik</p>
        </div>
        <span class="badge fs-6 px-3 py-2 rounded-pill" style="background: rgba(255,255,255,0.2); color: #fff; letter-spacing: 1px;">SPH-BUN</span>
    </div>

    <div class="p-4">
        <form action="{{ route('perkebunan.store') }}" method="POST" id="produksiForm">
            @csrf
            <input type="hidden" name="kategori_komoditas_id" value="{{ $kategori->id }}">

            {{-- ── FILTER UTAMA ──────────────────────────────────────────────────── --}}
            <div class="row g-3 mb-4 bg-light rounded-3 p-3 border border-light-subtle align-items-end">
                {{-- Kecamatan --}}
                <div class="col-md-3">
                    <label for="kecamatan_id" class="form-label fw-semibold text-secondary small">Wilayah Kecamatan <span class="text-danger">*</span></label>
                    <select name="kecamatan_id" id="kecamatan_id" class="form-select border-0 shadow-sm rounded-3" required>
                        <option value="">-- Pilih Kecamatan --</option>
                        @foreach($kecamatans as $id => $nama)
                            <option value="{{ $id }}" {{ old('kecamatan_id') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Periode / Semester --}}
                <div class="col-md-3">
                    <label for="bulan" class="form-label fw-semibold text-secondary small">Periode Semester <span class="text-danger">*</span></label>
                    <select name="bulan" id="bulan" class="form-select border-0 shadow-sm rounded-3" required>
                        @foreach($months as $val => $text)
                            <option value="{{ $val }}" {{ old('bulan') == $val ? 'selected' : '' }}>{{ $text }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Tahun --}}
                <div class="col-md-2">
                    <label for="tahun" class="form-label fw-semibold text-secondary small">Tahun <span class="text-danger">*</span></label>
                    <input type="number" name="tahun" id="tahun" class="form-control border-0 shadow-sm rounded-3"
                           value="{{ old('tahun', date('Y')) }}" min="2020" max="2050" required>
                </div>

                {{-- Satuan --}}
                <div class="col-md-3">
                    <label for="satuan_id" class="form-label fw-semibold text-secondary small">Satuan Ukur <span class="text-danger">*</span></label>
                    <select name="satuan_id" id="satuan_id" class="form-select border-0 shadow-sm rounded-3" required>
                        <option value="">-- Pilih Satuan --</option>
                        @foreach($satuans as $id => $nama)
                            <option value="{{ $id }}" {{ old('satuan_id') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Tombol Muat Data --}}
                <div class="col-md-1">
                    <button type="button" id="btnLoadPrev" class="btn btn-outline-success w-100 rounded-3 shadow-sm" title="Muat data semester sebelumnya">
                        <i class="bi bi-cloud-download"></i>
                    </button>
                </div>
            </div>

            {{-- ── TABEL MATRIKS PERKEBUNAN ─────────────────────────────────────── --}}
            <div class="table-responsive" style="max-height: 550px; overflow-y: auto;">
                <table class="table table-bordered table-hover align-middle mb-0" style="min-width: 1400px;">
                    <thead class="text-center small fw-bold" style="position: sticky; top: 0; z-index: 2; background: #f0fdf4;">
                        <tr>
                            <th rowspan="3" style="min-width:150px; vertical-align: middle;">Jenis Komoditas</th>
                            <th rowspan="3" style="min-width:110px; vertical-align: middle; background:#fef9c3;">(3) Luas Areal Akhir Tahun/Smt Lalu (Ha)</th>
                            <th colspan="4" style="min-width:380px;">Mutasi Luas Areal Dalam Tahun Laporan (Ha)</th>
                            <th colspan="3" style="min-width:270px;">Kondisi Areal Akhir Periode (Ha)</th>
                            <th colspan="2" style="min-width:200px;">Produksi (Kg)</th>
                            <th rowspan="3" style="min-width:120px; vertical-align: middle;">(16) Wujud Produksi</th>
                            <th colspan="2" style="min-width:160px;">Jumlah Petani</th>
                        </tr>
                        <tr>
                            <th rowspan="2" style="min-width:90px; vertical-align: middle;">(4) Tanam Ulang</th>
                            <th rowspan="2" style="min-width:90px; vertical-align: middle;">(5) Tanam Baru</th>
                            <th rowspan="2" style="min-width:90px; vertical-align: middle;">(6) Pengurangan</th>
                            <th rowspan="2" style="min-width:110px; vertical-align: middle; background:#fef9c3;">(7) Jumlah Areal = (3)+(5)-(6)</th>
                            <th rowspan="2" style="min-width:90px; vertical-align: middle;">(8) TBM</th>
                            <th rowspan="2" style="min-width:90px; vertical-align: middle;">(9) TM (Panen)</th>
                            <th rowspan="2" style="min-width:90px; vertical-align: middle;">(10) TTM / Rusak</th>
                            <th rowspan="2" style="min-width:100px; vertical-align: middle;">(12) Akhir Tahun Lalu</th>
                            <th rowspan="2" style="min-width:100px; vertical-align: middle;">(14) Tahun Laporan</th>
                            <th style="min-width:80px;">(17) Pemilik</th>
                            <th style="min-width:80px;">(18) Penggarap (BMU)</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($komoditasList as $kom)
                        <tr>
                            <td class="fw-semibold text-secondary small">
                                <i class="bi bi-circle-fill me-1 text-success" style="font-size:6px;"></i>{{ $kom->nama }}
                                <input type="hidden" name="komoditas[{{ $kom->id }}][jenis_periode]" value="{{ $kom->jenis_periode }}">
                                <input type="hidden" name="komoditas[{{ $kom->id }}][form_type]" value="Perkebunan">
                            </td>
                            {{-- (3) Luas Akhir Tahun/Smt Lalu --}}
                            <td class="p-1" style="background:#fef9c3;">
                                <input type="number" step="0.01" min="0"
                                       name="komoditas[{{ $kom->id }}][luas_akhir_tahun_lalu]"
                                       class="form-control form-control-sm text-end border-0 col-col3 prev-fill-bun"
                                       data-komoditas-id="{{ $kom->id }}"
                                       placeholder="0.00" style="background:transparent;">
                            </td>
                            {{-- (4) Tanam Ulang --}}
                            <td class="p-1">
                                <input type="number" step="0.01" min="0"
                                       name="komoditas[{{ $kom->id }}][tanam_ulang]"
                                       class="form-control form-control-sm text-end col-calc" data-komoditas-id="{{ $kom->id }}"
                                       placeholder="0.00">
                            </td>
                            {{-- (5) Tanam Baru (mapped to luas_tanam) --}}
                            <td class="p-1">
                                <input type="number" step="0.01" min="0"
                                       name="komoditas[{{ $kom->id }}][tanam_baru]"
                                       class="form-control form-control-sm text-end col-tanam-baru col-calc" data-komoditas-id="{{ $kom->id }}"
                                       placeholder="0.00">
                            </td>
                            {{-- (6) Pengurangan --}}
                            <td class="p-1">
                                <input type="number" step="0.01" min="0"
                                       name="komoditas[{{ $kom->id }}][pengurangan]"
                                       class="form-control form-control-sm text-end col-pengurangan col-calc" data-komoditas-id="{{ $kom->id }}"
                                       placeholder="0.00">
                            </td>
                            {{-- (7) Jumlah Areal = (3)+(5)-(6) --}}
                            <td class="p-1" style="background:#fef9c3;">
                                <input type="number" step="0.01"
                                       name="komoditas[{{ $kom->id }}][luas_jumlah]"
                                       class="form-control form-control-sm text-end fw-bold border-0 col-jumlah"
                                       data-komoditas-id="{{ $kom->id }}"
                                       placeholder="0.00" readonly style="background:transparent; color:#14532d;">
                            </td>
                            {{-- (8) TBM --}}
                            <td class="p-1">
                                <input type="number" step="0.01" min="0"
                                       name="komoditas[{{ $kom->id }}][tbm]"
                                       class="form-control form-control-sm text-end"
                                       placeholder="0.00">
                            </td>
                            {{-- (9) TM (Panen) (mapped to luas_panen) --}}
                            <td class="p-1">
                                <input type="number" step="0.01" min="0"
                                       name="komoditas[{{ $kom->id }}][luas_panen]"
                                       class="form-control form-control-sm text-end"
                                       placeholder="0.00">
                            </td>
                            {{-- (10) TTM / Rusak --}}
                            <td class="p-1">
                                <input type="number" step="0.01" min="0"
                                       name="komoditas[{{ $kom->id }}][ttm]"
                                       class="form-control form-control-sm text-end"
                                       placeholder="0.00">
                            </td>
                            {{-- (12) Produksi Akhir Tahun Lalu --}}
                            <td class="p-1">
                                <input type="number" step="0.01" min="0"
                                       name="komoditas[{{ $kom->id }}][produksi_akhir_tahun_lalu]"
                                       class="form-control form-control-sm text-end"
                                       placeholder="0.00">
                            </td>
                            {{-- (14) Produksi Tahun Laporan (mapped to produksi) --}}
                            <td class="p-1">
                                <input type="number" step="0.01" min="0"
                                       name="komoditas[{{ $kom->id }}][produksi]"
                                       class="form-control form-control-sm text-end"
                                       placeholder="0.00">
                            </td>
                            {{-- (16) Wujud Produksi --}}
                            <td class="p-1">
                                <input type="text"
                                       name="komoditas[{{ $kom->id }}][wujud_produksi]"
                                       class="form-control form-control-sm text-center"
                                       placeholder="Biji Kering/Karet Kering">
                            </td>
                            {{-- (17) Jumlah Petani Pemilik --}}
                            <td class="p-1">
                                <input type="number" min="0"
                                       name="komoditas[{{ $kom->id }}][jumlah_petani_pemilik]"
                                       class="form-control form-control-sm text-end"
                                       placeholder="0">
                            </td>
                            {{-- (18) Jumlah Petani Penggarap (BMU) --}}
                            <td class="p-1">
                                <input type="number" min="0"
                                       name="komoditas[{{ $kom->id }}][jumlah_petani_bmu]"
                                       class="form-control form-control-sm text-end"
                                       placeholder="0">
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Keterangan --}}
            <div class="d-flex align-items-center gap-3 mt-3 mb-4">
                <span class="d-flex align-items-center gap-1 small text-muted">
                    <span style="display:inline-block; width:14px; height:14px; background:#fef9c3; border:1px solid #d97706; border-radius:3px;"></span>
                    Kolom kuning = auto-fill / auto-hitung
                </span>
            </div>

            {{-- Action Buttons --}}
            <div class="d-flex gap-2 pt-3 border-top">
                <button type="submit" class="btn btn-success px-4 rounded-3 shadow-sm">
                    <i class="bi bi-check-circle me-2"></i>Simpan Laporan
                </button>
                <a href="{{ route('perkebunan.index') }}" class="btn btn-light px-4 rounded-3 text-secondary border">
                    <i class="bi bi-arrow-left me-2"></i>Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function () {

    // ── Auto-kalkulasi Jumlah Areal = (3)+(5)-(6) ──────────────────────────
    function recalcBun(komId) {
        const col3 = parseFloat($('input[name="komoditas['+komId+'][luas_akhir_tahun_lalu]"]').val()) || 0;
        const col5 = parseFloat($('input[name="komoditas['+komId+'][tanam_baru]"]').val()) || 0;
        const col6 = parseFloat($('input[name="komoditas['+komId+'][pengurangan]"]').val()) || 0;
        const total = Math.max(0, col3 + col5 - col6);
        $('input[name="komoditas['+komId+'][luas_jumlah]"]').val(total.toFixed(2));
    }

    $(document).on('input', '.col-calc, .col-col3', function () {
        recalcBun($(this).data('komoditas-id'));
    });

    // ── Auto-fill data periode sebelumnya via AJAX ──────────────────────────
    $('#btnLoadPrev').on('click', function () {
        const kecamatanId = $('#kecamatan_id').val();
        const bulan       = $('#bulan').val();
        const tahun       = $('#tahun').val();

        if (!kecamatanId || !bulan || !tahun) {
            alert('Pilih Kecamatan, Periode, dan Tahun terlebih dahulu!');
            return;
        }

        const $btn = $(this);
        $btn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i>');

        $.ajax({
            url: "{{ route('perkebunan.prev-data') }}",
            type: 'GET',
            data: { kecamatan_id: kecamatanId, bulan: bulan, tahun: tahun },
            success: function (data) {
                let filled = 0;
                $.each(data, function (komId, fields) {
                    if (fields.luas_akhir_tahun_lalu !== undefined) {
                        $('input[name="komoditas['+komId+'][luas_akhir_tahun_lalu]"]').val(fields.luas_akhir_tahun_lalu);
                        filled++;
                        recalcBun(komId);
                    }
                });
                if (filled > 0) {
                    $btn.html('<i class="bi bi-check-circle text-success"></i>');
                    setTimeout(function() { $btn.html('<i class="bi bi-cloud-download"></i>'); }, 2000);
                } else {
                    $btn.html('<i class="bi bi-exclamation-circle text-warning"></i>');
                    setTimeout(function() { $btn.html('<i class="bi bi-cloud-download"></i>'); }, 2000);
                }
            },
            error: function () {
                alert('Gagal memuat data periode sebelumnya.');
                $btn.html('<i class="bi bi-cloud-download"></i>');
            },
            complete: function () {
                $btn.prop('disabled', false);
            }
        });
    });
});
</script>

<style>
    .table th, .table td {
        font-size: 0.78rem;
        padding: 4px 5px !important;
        vertical-align: middle;
    }
    .table input.form-control-sm {
        font-size: 0.8rem;
        padding: 3px 6px;
    }
    thead th {
        line-height: 1.3;
    }
    .table-responsive::-webkit-scrollbar { height: 6px; }
    .table-responsive::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
</style>
@endsection
