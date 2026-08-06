@extends('layouts.admin')

@section('title', 'Master Kuota Tahunan Pupuk')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Master Data'],
    ['label' => 'Kuota Tahunan Pupuk']
]" />

<div class="card custom-card border-0 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3 pb-3 border-bottom">
        <div>
            <h5 class="fw-bold mb-1"><i class="bi bi-gear-fill text-success me-2"></i>Pengaturan Kuota Tahunan Pupuk</h5>
            <p class="text-muted small mb-0">Konfigurasi alokasi kuota awal tahunan pupuk bersubsidi per wilayah Kecamatan.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('kuota-tahunan.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Filter Tahun -->
        <div class="row g-3 mb-4 align-items-center bg-light rounded-3 p-3 border border-light-subtle">
            <div class="col-md-3">
                <label for="tahun" class="form-label fw-bold text-secondary small">Pilih Tahun Konfigurasi</label>
                <select name="tahun" id="tahun" class="form-select border-0 shadow-sm rounded-3">
                    @foreach($years as $yr)
                        <option value="{{ $yr }}" {{ $selectedYear == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5">
                <label for="bukti_sk" class="form-label fw-bold text-secondary small">Upload SK Alokasi Tahunan (PDF/Gambar)</label>
                <input type="file" name="bukti_sk" id="bukti_sk" class="form-control border-0 shadow-sm rounded-3 small">
                <div class="form-text small mt-1" id="file-link-container">
                    @if($dokumen)
                        <a href="{{ asset('storage/' . $dokumen->file_path) }}" target="_blank" class="text-success fw-bold me-2">
                            <i class="bi bi-file-earmark-pdf-fill me-1"></i>Lihat Dokumen SK Terunggah ({{ basename($dokumen->file_path) }})
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-success px-2 py-0 align-middle" onclick="previewSK('{{ asset('storage/' . $dokumen->file_path) }}')">
                            <i class="bi bi-eye"></i> Preview
                        </button>
                    @else
                        <span class="text-muted">Belum ada dokumen SK yang terunggah.</span>
                    @endif
                </div>
            </div>
            <div class="col-md-4 text-end pt-4">
                <button type="submit" class="btn btn-success px-4 rounded-3 py-2">
                    <i class="bi bi-save me-1"></i> Simpan Konfigurasi
                </button>
            </div>
        </div>

        <!-- Matriks Konfigurasi Kuota -->
        <div class="table-responsive border rounded-3 overflow-hidden mb-4">
            <table class="table table-bordered align-middle mb-0 text-center small">
                <thead class="table-light align-middle fw-bold">
                    <tr>
                        <th class="text-start" width="25%">Kecamatan</th>
                        @foreach($jenisPupuks as $jp)
                            <th>Kuota {{ $jp->nama }} (Kg)</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody id="quota-matrix-body">
                    @foreach($kecamatans as $kec)
                        <tr>
                            <td class="text-start fw-semibold text-secondary bg-light">{{ $kec->nama }}</td>
                            @foreach($jenisPupuks as $jp)
                                @php
                                    $val = $mappedQuotas[$kec->id][$jp->id] ?? '';
                                @endphp
                                <td>
                                    <input type="number" step="0.01" min="0" 
                                           name="data[{{ $kec->id }}][{{ $jp->id }}]" 
                                           id="quota_{{ $kec->id }}_{{ $jp->id }}" 
                                           value="{{ $val }}" 
                                           class="form-control text-end shadow-none border-0 bg-transparent" 
                                           placeholder="0.00">
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </form>
</div>

<!-- Modal Preview SK -->
<div class="modal fade" id="previewSKModal" tabindex="-1" aria-labelledby="previewSKModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-3 shadow-lg border-0">
            <div class="modal-header bg-success text-white border-0 py-3">
                <h6 class="modal-title fw-bold" id="previewSKModalLabel"><i class="bi bi-file-earmark-pdf-fill me-2"></i>Preview Dokumen SK</h6>
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
        const jenisPupuks = @json($jenisPupuks);

        // Load data dynamically when year is changed
        $('#tahun').on('change', function() {
            const tahun = $(this).val();
            
            $.ajax({
                url: "{{ route('kuota-tahunan.ajax-data') }}",
                type: 'GET',
                data: { tahun: tahun },
                success: function(response) {
                    // Update inputs
                    $('input[name^="data["]').val(''); // clear first
                    
                    for (const kecId in response.quotas) {
                        for (const jenisId in response.quotas[kecId]) {
                            const val = response.quotas[kecId][jenisId];
                            $(`#quota_${kecId}_${jenisId}`).val(val);
                        }
                    }

                    // Update file link
                    if (response.file_url) {
                        $('#file-link-container').html(`
                            <a href="${response.file_url}" target="_blank" class="text-success fw-bold me-2">
                                <i class="bi bi-file-earmark-pdf-fill me-1"></i>Lihat Dokumen SK Terunggah (${response.file_name})
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-success px-2 py-0 align-middle" onclick="previewSK('${response.file_url}')">
                                <i class="bi bi-eye"></i> Preview
                            </button>
                        `);
                    } else {
                        $('#file-link-container').html(`
                            <span class="text-muted">Belum ada dokumen SK yang terunggah.</span>
                        `);
                    }
                    $('#bukti_sk').val(''); // clear file input
                }
            });
        });
    });
</script>
@endsection
