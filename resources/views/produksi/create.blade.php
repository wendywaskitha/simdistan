@extends('layouts.admin')

@section('title', 'Tambah Laporan Produksi - ' . $kategori->nama)

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Laporan Produksi', 'url' => route('tanaman-pangan.index')],
    ['label' => 'Tambah Laporan ' . $kategori->nama]
]" />

<div class="card custom-card border-0 p-4">
    <div class="mb-4">
        <h5 class="fw-bold mb-1">Form Tambah Laporan Produksi ({{ $kategori->nama }})</h5>
        <p class="text-muted small mb-0">Isi data laporan produksi untuk seluruh komoditas yang terdaftar pada bulan terpilih.</p>
    </div>

    <form action="{{ route('tanaman-pangan.store') }}" method="POST" id="produksiForm">
        @csrf
        <input type="hidden" name="kategori_komoditas_id" value="{{ $kategori->id }}">

        <!-- Input Metadata Utama -->
        <div class="row g-3 mb-4 bg-light rounded-3 p-3 border border-light-subtle">
            <div class="col-md-3">
                <x-form.select 
                    name="kecamatan_id" 
                    label="Wilayah Kecamatan" 
                    placeholder="-- Pilih Kecamatan --" 
                    :options="$kecamatans"
                    selected="{{ request('kecamatan_id') }}"
                    required="true"
                />
            </div>
            <div class="col-md-3">
                <x-form.select 
                    name="bulan" 
                    label="Bulan Laporan" 
                    placeholder="-- Pilih Bulan --" 
                    :options="$months"
                    required="true"
                />
            </div>
            <div class="col-md-3">
                <x-form.input 
                    name="tahun" 
                    label="Tahun" 
                    type="number" 
                    value="{{ date('Y') }}" 
                    required="true" 
                />
            </div>
            <div class="col-md-3">
                <x-form.select 
                    name="satuan_id" 
                    label="Satuan Ukur" 
                    placeholder="-- Pilih Satuan --" 
                    :options="$satuans"
                    required="true"
                />
            </div>
        </div>

        @if($isTanamanPangan)
            <!-- UI Input Tanaman Pangan (Nested Per Komoditas) -->
            <div class="d-flex flex-column gap-4 mb-4">
                @foreach($komoditasList as $komoditas)
                    <div class="card border border-light-subtle rounded-3 p-4 bg-white shadow-sm">
                        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                            <h6 class="fw-bold mb-0 text-success text-uppercase" style="letter-spacing: 0.05em;">
                                <i class="bi bi-tag-fill me-2"></i>Komoditas: {{ $komoditas->nama }}
                            </h6>
                            <span class="badge bg-light text-secondary border">Tanaman Pangan</span>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0">
                                <thead class="table-light">
                                    <tr class="text-center small">
                                        <th width="15%">Minggu Ke-</th>
                                        <th>Luas Lahan (Ha)</th>
                                        <th>Luas Tanam (Ha)</th>
                                        <th>Luas Panen (Ha)</th>
                                        <th>Hasil Produksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @for($i = 0; $i < 4; $i++)
                                        <tr>
                                            <td class="text-center fw-bold text-secondary">Minggu {{ $i + 1 }}</td>
                                            <td>
                                                <input type="number" step="0.01" min="0" 
                                                       name="komoditas[{{ $komoditas->id }}][mingguans][{{ $i }}][luas_lahan]" 
                                                       class="form-control lahan-input-{{ $komoditas->id }} text-end" 
                                                       placeholder="0.00">
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" min="0" 
                                                       name="komoditas[{{ $komoditas->id }}][mingguans][{{ $i }}][luas_tanam]" 
                                                       class="form-control tanam-input-{{ $komoditas->id }} text-end" 
                                                       placeholder="0.00">
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" min="0" 
                                                       name="komoditas[{{ $komoditas->id }}][mingguans][{{ $i }}][luas_panen]" 
                                                       class="form-control panen-input-{{ $komoditas->id }} text-end" 
                                                       placeholder="0.00">
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" min="0" 
                                                       name="komoditas[{{ $komoditas->id }}][mingguans][{{ $i }}][produksi]" 
                                                       class="form-control produksi-input-{{ $komoditas->id }} text-end" 
                                                       placeholder="0.00">
                                            </td>
                                        </tr>
                                    @endfor
                                    <!-- Baris Akumulasi Bulanan Komoditas ini -->
                                    <tr class="table-warning fw-bold small">
                                        <td class="text-center">Total Bulanan</td>
                                        <td>
                                            <input type="text" id="totalLahan-{{ $komoditas->id }}" class="form-control-plaintext text-end fw-bold" readonly value="0.00 Ha">
                                        </td>
                                        <td>
                                            <input type="text" id="totalTanam-{{ $komoditas->id }}" class="form-control-plaintext text-end fw-bold" readonly value="0.00 Ha">
                                        </td>
                                        <td>
                                            <input type="text" id="totalPanen-{{ $komoditas->id }}" class="form-control-plaintext text-end fw-bold" readonly value="0.00 Ha">
                                        </td>
                                        <td>
                                            <input type="text" id="totalProduksi-{{ $komoditas->id }}" class="form-control-plaintext text-end fw-bold" readonly value="0.00">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- UI Input Bulanan Langsung (Matriks Komoditas) -->
            <div class="card border border-light-subtle rounded-3 p-4 bg-white shadow-sm mb-4">
                <h6 class="fw-bold mb-3 text-secondary text-uppercase" style="letter-spacing: 0.05em;">Daftar Komoditas & Hasil Produksi</h6>
                
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr class="text-center small">
                                <th width="30%">Nama Komoditas</th>
                                <th>Luas Tanam (Ha)</th>
                                <th>Luas Panen (Ha)</th>
                                <th>Hasil Produksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($komoditasList as $komoditas)
                                <tr>
                                    <td class="fw-semibold text-secondary">
                                        <i class="bi bi-tag me-2"></i>{{ $komoditas->nama }}
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0" 
                                               name="komoditas[{{ $komoditas->id }}][luas_tanam]" 
                                               class="form-control text-end" 
                                               placeholder="0.00">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0" 
                                               name="komoditas[{{ $komoditas->id }}][luas_panen]" 
                                               class="form-control text-end" 
                                               placeholder="0.00">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0" 
                                               name="komoditas[{{ $komoditas->id }}][produksi]" 
                                               class="form-control text-end" 
                                               placeholder="0.00">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success px-4 rounded-3">Simpan Semua Laporan</button>
            <a href="{{ route('tanaman-pangan.index') }}" class="btn btn-light px-4 rounded-3 text-secondary border">Batal</a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        @if($isTanamanPangan)
            @foreach($komoditasList as $komoditas)
                $(document).on('input', '.lahan-input-{{ $komoditas->id }}, .tanam-input-{{ $komoditas->id }}, .panen-input-{{ $komoditas->id }}, .produksi-input-{{ $komoditas->id }}', function() {
                    let totalLahan = 0;
                    let totalTanam = 0;
                    let totalPanen = 0;
                    let totalProduksi = 0;

                    $('.lahan-input-{{ $komoditas->id }}').each(function() {
                        totalLahan += parseFloat($(this).val()) || 0;
                    });
                    $('.tanam-input-{{ $komoditas->id }}').each(function() {
                        totalTanam += parseFloat($(this).val()) || 0;
                    });
                    $('.panen-input-{{ $komoditas->id }}').each(function() {
                        totalPanen += parseFloat($(this).val()) || 0;
                    });
                    $('.produksi-input-{{ $komoditas->id }}').each(function() {
                        totalProduksi += parseFloat($(this).val()) || 0;
                    });

                    $('#totalLahan-{{ $komoditas->id }}').val(totalLahan.toFixed(2) + ' Ha');
                    $('#totalTanam-{{ $komoditas->id }}').val(totalTanam.toFixed(2) + ' Ha');
                    $('#totalPanen-{{ $komoditas->id }}').val(totalPanen.toFixed(2) + ' Ha');
                    $('#totalProduksi-{{ $komoditas->id }}').val(totalProduksi.toFixed(2));
                });
            @endforeach
        @endif
    });
</script>
@endsection
