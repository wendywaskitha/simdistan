@extends('layouts.admin')

@section('title', 'Kelola Laporan Mingguan')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Laporan Produksi', 'url' => route('tanaman-pangan.index')],
    ['label' => 'Matriks Bulanan', 'url' => route('tanaman-pangan.kelola', ['komoditas_id' => $komoditas->id, 'tahun' => $tahun])],
    ['label' => 'Kelola Data Mingguan']
]" />

<div class="card custom-card border-0 p-4">
    <div class="mb-4">
        <h5 class="fw-bold mb-1">Form Pengisian Data Mingguan ({{ $kategori->nama }})</h5>
        <p class="text-muted small mb-0">Komoditas: <span class="text-success fw-bold">{{ $komoditas->nama }}</span> | Tahun: <strong>{{ $tahun }}</strong></p>
    </div>

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

    <form action="{{ route('tanaman-pangan.simpan-mingguan') }}" method="POST" id="mingguanForm">
        @csrf
        <input type="hidden" name="kecamatan_id" value="{{ $kecamatan->id }}">
        <input type="hidden" name="komoditas_id" value="{{ $komoditas->id }}">
        <input type="hidden" name="tahun" value="{{ $tahun }}">

        <!-- Metadata Input -->
        <div class="row g-3 mb-4 bg-light rounded-3 p-3 border border-light-subtle">
            <div class="col-md-4">
                <label class="form-label fw-bold text-secondary small mb-1">Kecamatan</label>
                <input type="text" class="form-control border-0 shadow-sm bg-white" value="{{ $kecamatan->nama }}" readonly>
            </div>
            <div class="col-md-4">
                <x-form.select 
                    name="bulan" 
                    label="Pilih Bulan Laporan" 
                    placeholder="-- Pilih Bulan --" 
                    :options="$months"
                    selected="{{ $bulan }}"
                    onchange="changeBulan(this.value)"
                    required="true"
                />
            </div>
            <div class="col-md-4">
                <x-form.select 
                    name="satuan_id" 
                    label="Pilih Satuan Ukur" 
                    placeholder="-- Pilih Satuan --" 
                    :options="$satuans"
                    selected="{{ $laporan ? $laporan->satuan_id : '' }}"
                    required="true"
                />
            </div>
        </div>

        <!-- Tabel Rincian Mingguan -->
        <div class="table-responsive mb-4 border rounded-3 overflow-hidden">
            <table class="table table-bordered align-middle mb-0 text-center">
                <thead class="table-light small fw-bold">
                    <tr>
                        <th width="15%">Minggu Ke-</th>
                        <th>Luas Lahan (Ha)</th>
                        <th>Luas Tanam (Ha)</th>
                        <th>Luas Panen (Ha)</th>
                        <th>Produktivitas</th>
                        <th>Hasil Produksi (Sesuai Satuan)</th>
                    </tr>
                </thead>
                <tbody>
                    @for($i = 0; $i < 4; $i++)
                        @php
                            $detail = $mingguans[$i] ?? null;
                        @endphp
                        <tr class="minggu-row">
                            <td class="fw-bold text-secondary">Minggu {{ $i + 1 }}</td>
                            <td>
                                <input type="number" step="0.01" min="0" 
                                       name="mingguans[{{ $i }}][luas_lahan]" 
                                       value="{{ $detail['luas_lahan'] ?? '' }}"
                                       class="form-control lahan-input text-end border-0 shadow-none bg-transparent" 
                                       placeholder="0.00" required>
                            </td>
                            <td>
                                <input type="number" step="0.01" min="0" 
                                       name="mingguans[{{ $i }}][luas_tanam]" 
                                       value="{{ $detail['luas_tanam'] ?? '' }}"
                                       class="form-control tanam-input text-end border-0 shadow-none bg-transparent" 
                                       placeholder="0.00" required>
                            </td>
                            <td>
                                <input type="number" step="0.01" min="0" 
                                       name="mingguans[{{ $i }}][luas_panen]" 
                                       value="{{ $detail['luas_panen'] ?? '' }}"
                                       class="form-control panen-input text-end border-0 shadow-none bg-transparent" 
                                       placeholder="0.00" required>
                            </td>
                            <td>
                                <input type="number" step="0.01" min="0" 
                                       name="mingguans[{{ $i }}][produktivitas]" 
                                       value="{{ $detail['produktivitas'] ?? '' }}"
                                       class="form-control prodv-input text-end border-0 shadow-none bg-transparent" 
                                       placeholder="0.00" required>
                            </td>
                            <td>
                                <input type="number" step="0.01" min="0" 
                                       name="mingguans[{{ $i }}][produksi]" 
                                       value="{{ $detail['produksi'] ?? '' }}"
                                       class="form-control prod-input text-end border-0 shadow-none bg-transparent fw-bold text-dark" 
                                       placeholder="0.00" readonly required>
                            </td>
                        </tr>
                    @endfor
                    <!-- Akumulasi Total Bulanan -->
                    <tr class="table-warning fw-bold">
                        <td>Total Bulanan</td>
                        <td>
                            <input type="text" id="totalLahan" class="form-control-plaintext text-end fw-bold" readonly value="0.00 Ha">
                        </td>
                        <td>
                            <input type="text" id="totalTanam" class="form-control-plaintext text-end fw-bold" readonly value="0.00 Ha">
                        </td>
                        <td>
                            <input type="text" id="totalPanen" class="form-control-plaintext text-end fw-bold" readonly value="0.00 Ha">
                        </td>
                        <td>
                            <input type="text" id="totalProduktivitas" class="form-control-plaintext text-end fw-bold" readonly value="0.00">
                        </td>
                        <td>
                            <input type="text" id="totalProduksi" class="form-control-plaintext text-end fw-bold" readonly value="0.00">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success px-4 rounded-3">Simpan</button>
            <a href="{{ route('tanaman-pangan.kelola', ['komoditas_id' => $komoditas->id, 'tahun' => $tahun]) }}" class="btn btn-light px-4 rounded-3 text-secondary border">Batal</a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    const changeBulan = (val) => {
        window.location.href = "{{ route('tanaman-pangan.input-mingguan') }}?kecamatan_id={{ $kecamatan->id }}&komoditas_id={{ $komoditas->id }}&tahun={{ $tahun }}&bulan=" + val;
    };

    $(document).ready(function() {
        const calculateRow = (row) => {
            const panen = parseFloat(row.find('.panen-input').val()) || 0;
            const prodv = parseFloat(row.find('.prodv-input').val()) || 0;
            const prod = panen * prodv;
            row.find('.prod-input').val(prod.toFixed(2));
        };

        const calculateTotals = () => {
            let totalLahan = 0;
            let totalTanam = 0;
            let totalPanen = 0;
            let totalProduksi = 0;

            $('.minggu-row').each(function() {
                calculateRow($(this));
                
                totalLahan += parseFloat($(this).find('.lahan-input').val()) || 0;
                totalTanam += parseFloat($(this).find('.tanam-input').val()) || 0;
                totalPanen += parseFloat($(this).find('.panen-input').val()) || 0;
                totalProduksi += parseFloat($(this).find('.prod-input').val()) || 0;
            });

            $('#totalLahan').val(totalLahan.toFixed(2) + ' Ha');
            $('#totalTanam').val(totalTanam.toFixed(2) + ' Ha');
            $('#totalPanen').val(totalPanen.toFixed(2) + ' Ha');
            
            const totalProdv = totalPanen > 0 ? (totalProduksi / totalPanen) : 0;
            $('#totalProduktivitas').val(totalProdv.toFixed(2));
            $('#totalProduksi').val(totalProduksi.toFixed(2));
        };

        $(document).on('input', '.lahan-input, .tanam-input, .panen-input, .prodv-input', calculateTotals);
        
        calculateTotals();
    });
</script>
@endsection
