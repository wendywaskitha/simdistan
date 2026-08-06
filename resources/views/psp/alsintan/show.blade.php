@extends('layouts.admin')

@section('title', 'Detail Bantuan Alsintan')

@section('content')
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
                                    <th>Luas Lahan (Ha)</th>
                                    <th>Waktu Kerja (Jam)</th>
                                    <th>Biaya (Rp)</th>
                                    <th>Hour Meter (HM)</th>
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
                                        <td>{{ number_format($laporan->hour_meter, 2, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted small">
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
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="tambahLaporanModalLabel"><i class="bi bi-activity text-success me-2"></i>Tambah Laporan Kerja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('alsintans.laporan.store', $alsintan->id) }}" method="POST">
                @csrf
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label for="tanggal" class="form-label fw-semibold small text-secondary">Tanggal Pengerjaan <span class="text-danger">*</span></label>
                        <input type="date" class="form-control rounded-3" id="tanggal" name="tanggal" required max="{{ date('Y-m-d') }}">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label for="luas_lahan" class="form-label fw-semibold small text-secondary">Luas Lahan (Hektar/Ha) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control rounded-3" id="luas_lahan" name="luas_lahan" required placeholder="0.5">
                        </div>
                        <div class="col-6">
                            <label for="waktu_pengerjaan" class="form-label fw-semibold small text-secondary">Durasi Kerja (Jam) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control rounded-3" id="waktu_pengerjaan" name="waktu_pengerjaan" required placeholder="4">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label for="biaya_pengolahan" class="form-label fw-semibold small text-secondary">Biaya Pengolahan (Rp) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control rounded-3" id="biaya_pengolahan" name="biaya_pengolahan" required placeholder="50000">
                        </div>
                        <div class="col-6">
                            <label for="hour_meter" class="form-label fw-semibold small text-secondary">Hour Meter Alat (HM) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control rounded-3" id="hour_meter" name="hour_meter" required placeholder="12.5">
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
@endsection
