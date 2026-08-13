@extends('layouts.admin')

@section('title', 'Detail Bantuan Alsintan')

@section('content')
@php
    $toolName = strtolower($alsintan->jenisAlat->nama ?? '');
    $isTractorOrCombine = in_array($toolName, ['traktor roda 2', 'traktor roda 4', 'combine harvester']);
    
    $luasLahanLabel = 'Luas Lahan (Hektar/Ha)';
    if ($toolName === 'pompa air') {
        $luasLahanLabel = 'Luas Lahan yang Diairi (Hektar/Ha)';
    } elseif (in_array($toolName, ['cultivator', 'hand sprayer'])) {
        $luasLahanLabel = 'Luas Lahan yang Dikelola (Hektar/Ha)';
    }
    
    $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
@endphp
<x-breadcrumb :items="[
    ['label' => 'Prasarana & Sarana Pertanian (PSP)', 'url' => route('alsintans.index')],
    ['label' => 'Bantuan Alsintan', 'url' => route('alsintans.index')],
    ['label' => 'Detail Alat']
]" />

<div class="row">
    <!-- Left Column: Specs -->
    <div class="col-lg-4 mb-4">
        <div class="card custom-card border-0 p-4 h-100">
            <div class="text-center mb-4">
                <div class="bg-success-subtle text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                    <i class="bi bi-truck-flatbed fs-2"></i>
                </div>
                <h5 class="fw-bold mb-1">{{ $alsintan->nama_alat }}</h5>
                <span class="badge bg-success-subtle text-success px-3 py-1 rounded-pill">{{ $alsintan->jenisAlat ? $alsintan->jenisAlat->nama : '-' }}</span>
            </div>

            <hr class="text-muted opacity-25">

            <div class="mb-4">
                <h6 class="fw-bold text-secondary mb-3 small uppercase">Informasi Penerima</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Kelompok Tani:</span>
                    <span class="fw-semibold small text-end">{{ $alsintan->kelompokTani ? $alsintan->kelompokTani->nama : '-' }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Ketua Poktan:</span>
                    <span class="fw-semibold small text-end">{{ $alsintan->nama_ketua ?? '-' }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Nama Operator:</span>
                    <span class="fw-semibold small text-end">{{ $alsintan->nama_operator ?? '-' }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">HP Operator:</span>
                    <span class="fw-semibold small text-end">{{ $alsintan->no_hp_operator ?? '-' }}</span>
                </div>
            </div>

            <div class="mb-4">
                <h6 class="fw-bold text-secondary mb-3 small uppercase">Spesifikasi Unit</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Merek:</span>
                    <span class="fw-semibold small text-end">{{ $alsintan->merek ?? '-' }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">No Rangka:</span>
                    <span class="fw-semibold small text-end">{{ $alsintan->nomor_rangka ?? '-' }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">No Mesin:</span>
                    <span class="fw-semibold small text-end">{{ $alsintan->nomor_mesin ?? '-' }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Kondisi Alat:</span>
                    @php
                        $badgeClass = 'bg-success';
                        if ($alsintan->kondisi === 'Rusak Ringan') {
                            $badgeClass = 'bg-warning text-dark';
                        } elseif ($alsintan->kondisi === 'Rusak Berat') {
                            $badgeClass = 'bg-danger';
                        }
                    @endphp
                    <span class="badge {{ $badgeClass }} px-2 py-1 rounded-pill">{{ $alsintan->kondisi }}</span>
                </div>
            </div>

            <div class="mb-4">
                <h6 class="fw-bold text-secondary mb-3 small uppercase">Pengadaan & Anggaran</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Sumber Dana:</span>
                    <span class="badge bg-info text-white px-2 py-1 rounded-3">{{ $alsintan->sumber_dana }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Harga Pengadaan:</span>
                    <span class="fw-semibold small text-end text-success">Rp {{ number_format($alsintan->harga, 2, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Tahun Bantuan:</span>
                    <span class="fw-semibold small text-end">{{ $alsintan->tahun_bantuan }}</span>
                </div>
            </div>

            <div class="mt-auto d-grid gap-2">
                <a href="{{ route('alsintans.realokasi.form', $alsintan->id) }}" class="btn btn-warning text-dark rounded-3 fw-semibold">
                    <i class="bi bi-arrow-left-right me-1"></i> Realokasikan Alat
                </a>
                <a href="{{ route('alsintans.edit', $alsintan->id) }}" class="btn btn-outline-info rounded-3">
                    <i class="bi bi-pencil-square me-1"></i> Edit Spesifikasi
                </a>
            </div>
        </div>
    </div>

    <!-- Right Column: Tabs -->
    <div class="col-lg-8">
        <div class="card custom-card border-0 p-4 h-100">
            <!-- Nav Tabs -->
            <ul class="nav nav-pills mb-4" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active rounded-pill px-4" id="pills-pemanfaatan-tab" data-bs-toggle="pill" data-bs-target="#pills-pemanfaatan" type="button" role="tab" aria-controls="pills-pemanfaatan" aria-selected="true">
                        <i class="bi bi-activity me-1"></i> Laporan Pemanfaatan
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-4 ms-2" id="pills-realokasi-tab" data-bs-toggle="pill" data-bs-target="#pills-realokasi" type="button" role="tab" aria-controls="pills-realokasi" aria-selected="false">
                        <i class="bi bi-clock-history me-1"></i> Riwayat Realokasi
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="pills-tabContent">
                <!-- Tab: Laporan Pemanfaatan -->
                <div class="tab-pane fade show active" id="pills-pemanfaatan" role="tabpanel" aria-labelledby="pills-pemanfaatan-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">Catatan Riwayat Pemanfaatan Alat</h6>
                        <button type="button" class="btn btn-success btn-sm rounded-3 px-3 py-2" data-bs-toggle="modal" data-bs-target="#tambahLaporanModal">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Laporan
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>{{ $luasLahanLabel }}</th>
                                    <th>Waktu Kerja (Jam)</th>
                                    <th>Biaya (Rp)</th>
                                    @if($isTractorOrCombine)
                                        <th>HM Awal</th>
                                        <th>HM Akhir</th>
                                        <th>Foto HM Awal</th>
                                        <th>Foto HM Akhir</th>
                                    @else
                                        <th>Tanggal Mulai</th>
                                        <th>Tanggal Selesai</th>
                                    @endif
                                    <th>Foto Dokumentasi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($alsintan->laporanPemanfaatan as $index => $laporan)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $laporan->tanggal->format('d-m-Y') }}</td>
                                        <td>{{ number_format($laporan->luas_lahan, 2, ',', '.') }} Ha</td>
                                        <td>{{ $laporan->waktu_pengerjaan }} Jam</td>
                                        <td>Rp {{ number_format($laporan->biaya_pengolahan, 2, ',', '.') }}</td>
                                        @if($isTractorOrCombine)
                                            <td>{{ $laporan->hour_meter_awal ? number_format($laporan->hour_meter_awal, 2, ',', '.') : '-' }}</td>
                                            <td>{{ $laporan->hour_meter_akhir ? number_format($laporan->hour_meter_akhir, 2, ',', '.') : '-' }}</td>
                                            <td>
                                                @if($laporan->foto_hm_awal)
                                                    <button type="button" class="btn btn-xs btn-outline-success px-2 py-1 rounded small" style="font-size: 0.75rem;" onclick="showFotoModal('{{ asset('storage/' . $laporan->foto_hm_awal) }}', 'Foto HM Awal')">
                                                        <i class="bi bi-image me-1"></i>Lihat
                                                    </button>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if($laporan->foto_hm_akhir)
                                                    <button type="button" class="btn btn-xs btn-outline-success px-2 py-1 rounded small" style="font-size: 0.75rem;" onclick="showFotoModal('{{ asset('storage/' . $laporan->foto_hm_akhir) }}', 'Foto HM Akhir')">
                                                        <i class="bi bi-image me-1"></i>Lihat
                                                    </button>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        @else
                                            <td>{{ $laporan->tanggal_mulai ? $laporan->tanggal_mulai->format('d-m-Y') : '-' }}</td>
                                            <td>{{ $laporan->tanggal_selesai ? $laporan->tanggal_selesai->format('d-m-Y') : '-' }}</td>
                                        @endif
                                        <td>
                                            @if($laporan->foto_dokumentasi)
                                                <button type="button" class="btn btn-xs btn-outline-success px-2 py-1 rounded small" style="font-size: 0.75rem;" onclick="showFotoModal('{{ asset('storage/' . $laporan->foto_dokumentasi) }}', 'Foto Dokumentasi Kerja')">
                                                    <i class="bi bi-image me-1"></i>Lihat
                                                </button>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <button type="button" class="btn btn-xs btn-outline-primary px-2 py-1 rounded small" style="font-size: 0.75rem;"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editLaporanModal"
                                                    data-id="{{ $laporan->id }}"
                                                    data-action="{{ route('alsintans.laporan.update', $laporan->id) }}"
                                                    data-tanggal="{{ $laporan->tanggal->format('Y-m-d') }}"
                                                    data-luas="{{ $laporan->luas_lahan }}"
                                                    data-waktu="{{ $laporan->waktu_pengerjaan }}"
                                                    data-biaya="{{ $laporan->biaya_pengolahan }}"
                                                    data-awal="{{ $laporan->hour_meter_awal }}"
                                                    data-akhir="{{ $laporan->hour_meter_akhir }}"
                                                    data-tanggal-mulai="{{ $laporan->tanggal_mulai ? $laporan->tanggal_mulai->format('Y-m-d') : '' }}"
                                                    data-tanggal-selesai="{{ $laporan->tanggal_selesai ? $laporan->tanggal_selesai->format('Y-m-d') : '' }}"
                                                    data-foto-awal="{{ $laporan->foto_hm_awal ? asset('storage/' . $laporan->foto_hm_awal) : '' }}"
                                                    data-foto-akhir="{{ $laporan->foto_hm_akhir ? asset('storage/' . $laporan->foto_hm_akhir) : '' }}"
                                                    data-foto-dokumentasi="{{ $laporan->foto_dokumentasi ? asset('storage/' . $laporan->foto_dokumentasi) : '' }}"
                                                    onclick="populateEditModal(this)">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <form action="{{ route('alsintans.laporan.destroy', $laporan->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-xs btn-outline-danger px-2 py-1 rounded small btn-delete-trigger" style="font-size: 0.75rem;">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $isTractorOrCombine ? 11 : 9 }}" class="text-center py-4 text-muted small">
                                            <i class="bi bi-info-circle d-block fs-3 mb-2"></i>
                                            Belum ada laporan pemanfaatan untuk alsintan ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab: Riwayat Realokasi -->
                <div class="tab-pane fade" id="pills-realokasi" role="tabpanel" aria-labelledby="pills-realokasi-tab">
                    <h6 class="fw-bold mb-3">Log Riwayat Pemindahan Kelompok Tani</h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Dari Kelompok Tani</th>
                                    <th>Ke Kelompok Tani</th>
                                    <th>Keterangan / Alasan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($alsintan->realokasi as $index => $log)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $log->tanggal_realokasi->format('d-m-Y') }}</td>
                                        <td>{{ $log->kelompokTaniAsal ? $log->kelompokTaniAsal->nama : '-' }}</td>
                                        <td>{{ $log->kelompokTaniTujuan ? $log->kelompokTaniTujuan->nama : '-' }}</td>
                                        <td>{{ $log->keterangan ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted small">
                                            <i class="bi bi-info-circle d-block fs-3 mb-2"></i>
                                            Alsintan ini belum pernah direalokasikan ke kelompok tani lain.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Tambah Laporan Pemanfaatan -->
<div class="modal fade" id="tambahLaporanModal" tabindex="-1" aria-labelledby="tambahLaporanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="tambahLaporanModalLabel"><i class="bi bi-activity text-success me-2"></i>Tambah Laporan Kerja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('alsintans.laporan.store', $alsintan->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body py-4">
                    @if($isTractorOrCombine)
                        <div class="mb-3">
                            <label for="tanggal" class="form-label fw-semibold small text-secondary">Tanggal Pengerjaan <span class="text-danger">*</span></label>
                            <input type="date" class="form-control rounded-3" id="tanggal" name="tanggal" required max="{{ date('Y-m-d') }}">
                        </div>
                    @endif

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="luas_lahan" class="form-label fw-semibold small text-secondary">{{ $luasLahanLabel }} <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control rounded-3" id="luas_lahan" name="luas_lahan" required placeholder="0.5">
                        </div>
                        <div class="col-md-6">
                            <label for="waktu_pengerjaan" class="form-label fw-semibold small text-secondary">Durasi Kerja (Jam) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control rounded-3" id="waktu_pengerjaan" name="waktu_pengerjaan" required placeholder="4">
                        </div>
                    </div>

                    @if($isTractorOrCombine)
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label for="biaya_pengolahan" class="form-label fw-semibold small text-secondary">Biaya Pengolahan (Rp) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control rounded-3" id="biaya_pengolahan" name="biaya_pengolahan" required placeholder="50000">
                            </div>
                            <div class="col-md-4">
                                <label for="hour_meter_awal" class="form-label fw-semibold small text-secondary">Hour Meter Awal <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control rounded-3" id="hour_meter_awal" name="hour_meter_awal" required placeholder="12.0">
                            </div>
                            <div class="col-md-4">
                                <label for="hour_meter_akhir" class="form-label fw-semibold small text-secondary">Hour Meter Akhir <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control rounded-3" id="hour_meter_akhir" name="hour_meter_akhir" required placeholder="14.5">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="foto_hm_awal" class="form-label fw-semibold small text-secondary">Foto HM Awal <span class="text-danger">*</span></label>
                                <input type="file" class="form-control rounded-3" id="foto_hm_awal" name="foto_hm_awal" required accept="image/*" onchange="previewImage(this, 'preview_hm_awal')">
                                <div class="mt-2 text-center d-none" id="container_hm_awal">
                                    <img id="preview_hm_awal" src="#" alt="Preview HM Awal" class="img-thumbnail rounded-3" style="max-height: 150px;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="foto_hm_akhir" class="form-label fw-semibold small text-secondary">Foto HM Akhir <span class="text-danger">*</span></label>
                                <input type="file" class="form-control rounded-3" id="foto_hm_akhir" name="foto_hm_akhir" required accept="image/*" onchange="previewImage(this, 'preview_hm_akhir')">
                                <div class="mt-2 text-center d-none" id="container_hm_akhir">
                                    <img id="preview_hm_akhir" src="#" alt="Preview HM Akhir" class="img-thumbnail rounded-3" style="max-height: 150px;">
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label for="biaya_pengolahan" class="form-label fw-semibold small text-secondary">Biaya Pengolahan (Rp) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control rounded-3" id="biaya_pengolahan" name="biaya_pengolahan" required placeholder="50000">
                            </div>
                            <div class="col-md-4">
                                <label for="tanggal_mulai" class="form-label fw-semibold small text-secondary">Tanggal Mulai Pemanfaatan <span class="text-danger">*</span></label>
                                <input type="date" class="form-control rounded-3" id="tanggal_mulai" name="tanggal_mulai" required>
                            </div>
                            <div class="col-md-4">
                                <label for="tanggal_selesai" class="form-label fw-semibold small text-secondary">Tanggal Selesai Pemanfaatan <span class="text-danger">*</span></label>
                                <input type="date" class="form-control rounded-3" id="tanggal_selesai" name="tanggal_selesai" required>
                            </div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label for="foto_dokumentasi" class="form-label fw-semibold small text-secondary">Foto Dokumentasi Kerja <span class="text-danger">*</span></label>
                        <input type="file" class="form-control rounded-3" id="foto_dokumentasi" name="foto_dokumentasi" required accept="image/*" onchange="previewImage(this, 'preview_dokumentasi')">
                        <div class="mt-2 text-center d-none" id="container_dokumentasi">
                            <img id="preview_dokumentasi" src="#" alt="Preview Dokumentasi" class="img-thumbnail rounded-3" style="max-height: 150px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-3 px-4">Simpan Laporan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Edit Laporan Pemanfaatan -->
<div class="modal fade" id="editLaporanModal" tabindex="-1" aria-labelledby="editLaporanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="editLaporanModalLabel"><i class="bi bi-activity text-success me-2"></i>Edit Laporan Kerja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="editLaporanForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body py-4">
                    @if($isTractorOrCombine)
                        <div class="mb-3">
                            <label for="edit_tanggal" class="form-label fw-semibold small text-secondary">Tanggal Pengerjaan <span class="text-danger">*</span></label>
                            <input type="date" class="form-control rounded-3" id="edit_tanggal" name="tanggal" required max="{{ date('Y-m-d') }}">
                        </div>
                    @endif

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="edit_luas_lahan" class="form-label fw-semibold small text-secondary">{{ $luasLahanLabel }} <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control rounded-3" id="edit_luas_lahan" name="luas_lahan" required placeholder="0.5">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_waktu_pengerjaan" class="form-label fw-semibold small text-secondary">Durasi Kerja (Jam) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control rounded-3" id="edit_waktu_pengerjaan" name="waktu_pengerjaan" required placeholder="4">
                        </div>
                    </div>

                    @if($isTractorOrCombine)
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label for="edit_biaya_pengolahan" class="form-label fw-semibold small text-secondary">Biaya Pengolahan (Rp) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control rounded-3" id="edit_biaya_pengolahan" name="biaya_pengolahan" required placeholder="50000">
                            </div>
                            <div class="col-md-4">
                                <label for="edit_hour_meter_awal" class="form-label fw-semibold small text-secondary">Hour Meter Awal <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control rounded-3" id="edit_hour_meter_awal" name="hour_meter_awal" required placeholder="12.0">
                            </div>
                            <div class="col-md-4">
                                <label for="edit_hour_meter_akhir" class="form-label fw-semibold small text-secondary">Hour Meter Akhir <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control rounded-3" id="edit_hour_meter_akhir" name="hour_meter_akhir" required placeholder="14.5">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="edit_foto_hm_awal" class="form-label fw-semibold small text-secondary">Foto HM Awal <span class="text-muted">(Biarkan kosong jika tidak diubah)</span></label>
                                <input type="file" class="form-control rounded-3" id="edit_foto_hm_awal" name="foto_hm_awal" accept="image/*" onchange="previewImage(this, 'edit_preview_hm_awal')">
                                <div class="mt-2 text-center" id="container_edit_preview_hm_awal">
                                    <img id="edit_preview_hm_awal" src="#" alt="Preview HM Awal" class="img-thumbnail rounded-3" style="max-height: 150px;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_foto_hm_akhir" class="form-label fw-semibold small text-secondary">Foto HM Akhir <span class="text-muted">(Biarkan kosong jika tidak diubah)</span></label>
                                <input type="file" class="form-control rounded-3" id="edit_foto_hm_akhir" name="foto_hm_akhir" accept="image/*" onchange="previewImage(this, 'edit_preview_hm_akhir')">
                                <div class="mt-2 text-center" id="container_edit_preview_hm_akhir">
                                    <img id="edit_preview_hm_akhir" src="#" alt="Preview HM Akhir" class="img-thumbnail rounded-3" style="max-height: 150px;">
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label for="edit_biaya_pengolahan" class="form-label fw-semibold small text-secondary">Biaya Pengolahan (Rp) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control rounded-3" id="edit_biaya_pengolahan" name="biaya_pengolahan" required placeholder="50000">
                            </div>
                            <div class="col-md-4">
                                <label for="edit_tanggal_mulai" class="form-label fw-semibold small text-secondary">Tanggal Mulai Pemanfaatan <span class="text-danger">*</span></label>
                                <input type="date" class="form-control rounded-3" id="edit_tanggal_mulai" name="tanggal_mulai" required>
                            </div>
                            <div class="col-md-4">
                                <label for="edit_tanggal_selesai" class="form-label fw-semibold small text-secondary">Tanggal Selesai Pemanfaatan <span class="text-danger">*</span></label>
                                <input type="date" class="form-control rounded-3" id="edit_tanggal_selesai" name="tanggal_selesai" required>
                            </div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label for="edit_foto_dokumentasi" class="form-label fw-semibold small text-secondary">Foto Dokumentasi Kerja <span class="text-muted">(Biarkan kosong jika tidak diubah)</span></label>
                        <input type="file" class="form-control rounded-3" id="edit_foto_dokumentasi" name="foto_dokumentasi" accept="image/*" onchange="previewImage(this, 'edit_preview_dokumentasi')">
                        <div class="mt-2 text-center" id="container_edit_preview_dokumentasi">
                            <img id="edit_preview_dokumentasi" src="#" alt="Preview Dokumentasi" class="img-thumbnail rounded-3" style="max-height: 150px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-3 px-4">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    const container = document.getElementById('container_' + previewId.replace('preview_', ''));
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            container.classList.remove('d-none');
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.src = "#";
        container.classList.add('d-none');
    }
}

function populateEditModal(button) {
    // Set form action
    document.getElementById('editLaporanForm').action = button.dataset.action;

    // Populate standard fields
    if (document.getElementById('edit_tanggal')) {
        document.getElementById('edit_tanggal').value = button.dataset.tanggal;
    }
    document.getElementById('edit_luas_lahan').value = button.dataset.luas;
    document.getElementById('edit_waktu_pengerjaan').value = button.dataset.waktu;
    document.getElementById('edit_biaya_pengolahan').value = button.dataset.biaya;
    if (document.getElementById('edit_hour_meter_awal')) {
        document.getElementById('edit_hour_meter_awal').value = button.dataset.awal;
    }
    if (document.getElementById('edit_hour_meter_akhir')) {
        document.getElementById('edit_hour_meter_akhir').value = button.dataset.akhir;
    }
    if (document.getElementById('edit_tanggal_mulai')) {
        document.getElementById('edit_tanggal_mulai').value = button.dataset.tanggalMulai;
    }
    if (document.getElementById('edit_tanggal_selesai')) {
        document.getElementById('edit_tanggal_selesai').value = button.dataset.tanggalSelesai;
    }

    // Populate or reset image previews
    if (document.getElementById('edit_preview_hm_awal')) {
        setupImagePreview('edit_preview_hm_awal', button.dataset.fotoAwal);
    }
    if (document.getElementById('edit_preview_hm_akhir')) {
        setupImagePreview('edit_preview_hm_akhir', button.dataset.fotoAkhir);
    }
    setupImagePreview('edit_preview_dokumentasi', button.dataset.fotoDokumentasi);
}

function setupImagePreview(previewId, imageUrl) {
    const preview = document.getElementById(previewId);
    const container = document.getElementById('container_' + previewId);
    if (imageUrl) {
        preview.src = imageUrl;
        container.classList.remove('d-none');
    } else {
        preview.src = "#";
        container.classList.add('d-none');
    }
}

function showFotoModal(url, title) {
    document.getElementById('previewFotoModalLabel').innerText = title;
    document.getElementById('previewFotoImg').src = url;
    const myModal = new bootstrap.Modal(document.getElementById('previewFotoModal'));
    myModal.show();
}
</script>

<!-- Modal: Preview Foto -->
<div class="modal fade" id="previewFotoModal" tabindex="-1" aria-labelledby="previewFotoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="previewFotoModalLabel">Pratinjau Foto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <img id="previewFotoImg" src="#" alt="Pratinjau Foto" class="img-fluid rounded-3 shadow-sm" style="max-height: 500px;">
            </div>
        </div>
    </div>
</div>
@endsection
