@extends('layouts.admin')

@section('title', 'PSP - Pengalihan Kuota Pupuk')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Distribusi Pupuk', 'url' => route('distribusi-pupuk.index')],
    ['label' => 'Daftar Pengalihan Kuota']
]" />

<div class="card custom-card border-0 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h5 class="fw-bold mb-1"><i class="bi bi-arrow-left-right text-success me-2"></i>Daftar Pengalihan Kuota Pupuk</h5>
            <p class="text-muted small mb-0">Riwayat pengalihan kuota pupuk bersubsidi antar kecamatan berdasarkan aturan penebusan bulanan.</p>
        </div>
        <a href="{{ route('distribusi-pupuk.pengalihan.create') }}" class="btn btn-success rounded-3 px-3 py-2">
            <i class="bi bi-plus-circle me-1"></i> Tambah Pengalihan
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle text-center small" id="pengalihanTable">
            <thead class="table-light align-middle fw-bold">
                <tr>
                    <th width="5%">No</th>
                    <th>Periode</th>
                    <th>Jenis Pupuk</th>
                    <th>Kecamatan Asal (&lt;75%)</th>
                    <th>Kecamatan Tujuan (≥75%)</th>
                    <th>Jumlah Dialihkan (Kg)</th>
                    <th>SK Relokasi</th>
                    <th>Keterangan</th>
                    <th>Tanggal Input</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pengalihans as $index => $p)
                    @php
                        $months = [
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                        ];
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="fw-bold text-secondary">{{ $months[$p->bulan] }} {{ $p->tahun }}</td>
                        <td><span class="badge bg-success rounded-3 px-2 py-1">{{ $p->jenis->nama }}</span></td>
                        <td class="text-danger fw-semibold">{{ $p->kecamatanAsal->nama }}</td>
                        <td class="text-primary fw-semibold">{{ $p->kecamatanTujuan->nama }}</td>
                        <td class="text-end fw-bold">{{ number_format($p->jumlah, 2, ',', '.') }} Kg</td>
                        <td>
                            @if($p->file_path)
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <a href="{{ asset('storage/' . $p->file_path) }}" target="_blank" class="text-success fw-semibold text-decoration-none">
                                        <i class="bi bi-file-earmark-pdf-fill"></i> {{ $p->nama_sk }}
                                    </a>
                                    <button type="button" class="btn btn-xs btn-outline-success px-1 py-0" style="font-size: 0.75rem;" onclick="previewSK('{{ asset('storage/' . $p->file_path) }}')">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            @else
                                <span class="text-muted">{{ $p->nama_sk ?: '-' }}</span>
                            @endif
                        </td>
                        <td class="text-start">{{ $p->keterangan ?? '-' }}</td>
                        <td>{{ $p->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Preview SK -->
<div class="modal fade" id="previewSKModal" tabindex="-1" aria-labelledby="previewSKModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-3 shadow-lg border-0">
            <div class="modal-header bg-success text-white border-0 py-3">
                <h6 class="modal-title fw-bold" id="previewSKModalLabel"><i class="bi bi-file-earmark-pdf-fill me-2"></i>Preview Dokumen SK Relokasi</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" style="height: 550px;">
                <div id="preview-content-placeholder" class="w-100 h-100 d-flex align-items-center justify-content-center text-muted">
                    <i class="bi bi-hourglass-split fs-2 me-2"></i> Memuat file...
                </div>
                <iframe id="pdf-preview-frame" class="w-100 h-100 d-none border-0"></iframe>
                <div id="img-preview-container" class="w-100 h-100 d-none overflow-auto text-center p-3">
                    <img id="img-preview-el" class="img-fluid rounded border shadow-sm" style="max-height: 500px;">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function previewSK(url) {
        if (!url) return;
        
        const ext = url.split('.').pop().toLowerCase();
        const modal = new bootstrap.Modal(document.getElementById('previewSKModal'));
        
        $('#preview-content-placeholder').removeClass('d-none');
        $('#pdf-preview-frame').addClass('d-none').attr('src', '');
        $('#img-preview-container').addClass('d-none');
        
        if (ext === 'pdf') {
            $('#pdf-preview-frame').attr('src', url).on('load', function() {
                $('#preview-content-placeholder').addClass('d-none');
                $('#pdf-preview-frame').removeClass('d-none');
            });
        } else if (['jpg', 'jpeg', 'png'].includes(ext)) {
            $('#img-preview-el').attr('src', url);
            $('#preview-content-placeholder').addClass('d-none');
            $('#img-preview-container').removeClass('d-none');
        } else {
            window.open(url, '_blank');
            return;
        }
        
        modal.show();
    }

    $(document).ready(function() {
        $('#pengalihanTable').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            }
        });
    });
</script>
@endsection


