@extends('layouts.admin')

@section('title', 'Detail Proyek Infrastruktur & Irigasi')

@section('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endsection

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Prasarana & Sarana Pertanian (PSP)', 'url' => route('infrastrukturs.index')],
    ['label' => 'Infrastruktur & Irigasi', 'url' => route('infrastrukturs.index')],
    ['label' => 'Detail Proyek']
]" />

<div class="row">
    <!-- Left Column: Specs -->
    <div class="col-lg-4 mb-4">
        <div class="card custom-card border-0 p-4 h-100">
            <div class="text-center mb-4">
                <div class="bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                    <i class="bi bi-water fs-2"></i>
                </div>
                <h5 class="fw-bold mb-1">{{ $infrastruktur->nama_proyek }}</h5>
                <span class="badge bg-primary-subtle text-primary px-3 py-1 rounded-pill mt-1">{{ $infrastruktur->jenis_infrastruktur }}</span>
            </div>

            <hr class="text-muted opacity-25">

            <div class="mb-4">
                <h6 class="fw-bold text-secondary mb-3 small uppercase"><i class="bi bi-people-fill me-1"></i>Penerima / Wilayah</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Kelompok Tani:</span>
                    <span class="fw-semibold small text-end">{{ $infrastruktur->kelompokTani ? $infrastruktur->kelompokTani->nama : 'Umum (Non-Kelompok)' }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Kecamatan:</span>
                    <span class="fw-semibold small text-end">{{ $infrastruktur->kecamatan ? $infrastruktur->kecamatan->nama : '-' }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Desa:</span>
                    <span class="fw-semibold small text-end">{{ $infrastruktur->desa ? $infrastruktur->desa->nama : '-' }}</span>
                </div>
                @if($infrastruktur->latitude && $infrastruktur->longitude)
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Koordinat:</span>
                    <span class="fw-semibold small text-end text-primary">
                        <a href="https://www.google.com/maps/search/?api=1&query={{ $infrastruktur->latitude }},{{ $infrastruktur->longitude }}" target="_blank" class="text-decoration-none">
                            {{ $infrastruktur->latitude }}, {{ $infrastruktur->longitude }} <i class="bi bi-box-arrow-up-right small"></i>
                        </a>
                    </span>
                </div>
                @endif
            </div>

            <div class="mb-4">
                <h6 class="fw-bold text-secondary mb-3 small uppercase"><i class="bi bi-cash-stack me-1"></i>Dimensi & Pembiayaan</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Volume Pembangunan:</span>
                    <span class="fw-semibold small text-end">{{ number_format($infrastruktur->volume, 0, ',', '.') }} {{ $infrastruktur->satuan }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Nilai Anggaran:</span>
                    <span class="fw-semibold small text-end text-success">Rp {{ number_format($infrastruktur->nilai_anggaran, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Sumber Dana:</span>
                    <span class="badge bg-success-subtle text-success px-2 py-1 rounded-3">{{ $infrastruktur->sumber_dana }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Tahun Anggaran:</span>
                    <span class="fw-semibold small text-end">{{ $infrastruktur->tahun_anggaran }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Status Utama:</span>
                    @php
                        $badgeClass = 'bg-secondary';
                        if ($infrastruktur->status_pembangunan === 'Rencana') {
                            $badgeClass = 'bg-info text-white';
                        } elseif ($infrastruktur->status_pembangunan === 'Konstruksi') {
                            $badgeClass = 'bg-warning text-dark';
                        } elseif ($infrastruktur->status_pembangunan === 'Selesai') {
                            $badgeClass = 'bg-success';
                        } elseif ($infrastruktur->status_pembangunan === 'Rusak') {
                            $badgeClass = 'bg-danger';
                        }
                    @endphp
                    <span class="badge {{ $badgeClass }} px-2 py-1 rounded-pill">{{ $infrastruktur->status_pembangunan }}</span>
                </div>
                @if($infrastruktur->kml_file)
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">File KML Area:</span>
                    <span class="fw-semibold small text-end">
                        <a href="{{ asset('storage/' . $infrastruktur->kml_file) }}" target="_blank" class="text-decoration-none">
                            Unduh KML <i class="bi bi-download small"></i>
                        </a>
                    </span>
                </div>
                @endif
            </div>

            @if($infrastruktur->latitude && $infrastruktur->longitude)
            <div class="mb-4">
                <h6 class="fw-bold text-secondary mb-2 small uppercase"><i class="bi bi-map me-1"></i>Lokasi Peta</h6>
                <div id="map" style="height: 220px; border-radius: 12px; border: 1px solid #dee2e6;"></div>
            </div>
            @endif

            @if($infrastruktur->keterangan)
            <div class="mb-4">
                <h6 class="fw-bold text-secondary mb-2 small uppercase">Keterangan</h6>
                <p class="text-muted small bg-light p-2 rounded-3 mb-0">{{ $infrastruktur->keterangan }}</p>
            </div>
            @endif

            <div class="mt-auto d-grid gap-2">
                <button type="button" class="btn btn-success rounded-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#tambahLaporanModal">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Laporan Kondisi
                </button>
                <a href="{{ route('infrastrukturs.edit', $infrastruktur->id) }}" class="btn btn-outline-info rounded-3">
                    <i class="bi bi-pencil-square me-1"></i> Edit Detail Proyek
                </a>
            </div>
        </div>
    </div>

    <!-- Right Column: Timeline / History Reports -->
    <div class="col-lg-8 mb-4">
        <div class="card custom-card border-0 p-4 h-100">
            <h5 class="fw-bold mb-4">Riwayat Kondisi & Pemeliharaan Berkala</h5>

            @if($infrastruktur->laporans->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-journal-x text-muted display-4"></i>
                    <p class="text-muted mt-3">Belum ada catatan pemeliharaan atau kondisi berkala untuk proyek ini.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light text-secondary">
                            <tr>
                                <th>Tanggal</th>
                                <th>Kondisi Fisik</th>
                                <th>Progres Fisik</th>
                                <th>Keterangan / Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($infrastruktur->laporans as $lap)
                                <tr>
                                    <td class="fw-semibold text-secondary">
                                        {{ \Carbon\Carbon::parse($lap->tanggal_laporan)->translatedFormat('d F Y') }}
                                    </td>
                                    <td>
                                        @php
                                            $condClass = 'bg-success';
                                            if ($lap->kondisi === 'Rusak Ringan') {
                                                $condClass = 'bg-warning text-dark';
                                            } elseif ($lap->kondisi === 'Rusak Berat') {
                                                $condClass = 'bg-danger';
                                            }
                                        @endphp
                                        <span class="badge {{ $condClass }} px-3 py-2 rounded-pill small">{{ $lap->kondisi }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 6px;">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $lap->progres_fisik }}%" aria-valuenow="{{ $lap->progres_fisik }}" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <span class="fw-bold small">{{ number_format($lap->progres_fisik, 0) }}%</span>
                                        </div>
                                    </td>
                                    <td class="text-muted small">{{ $lap->keterangan ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal: Tambah Laporan Kondisi -->
<div class="modal fade" id="tambahLaporanModal" tabindex="-1" aria-labelledby="tambahLaporanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="tambahLaporanModalLabel">Tambah Laporan Pemeliharaan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('infrastrukturs.laporan.store', $infrastruktur->id) }}" method="POST">
                @csrf
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label for="tanggal_laporan" class="form-label fw-semibold text-secondary">Tanggal Pemeriksaan <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="tanggal_laporan" name="tanggal_laporan" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="kondisi" class="form-label fw-semibold text-secondary">Kondisi Fisik <span class="text-danger">*</span></label>
                        <select class="form-select" id="kondisi" name="kondisi" required>
                            <option value="Baik" selected>Baik (Berfungsi Normal)</option>
                            <option value="Rusak Ringan">Rusak Ringan (Butuh Pemeliharaan Kecil)</option>
                            <option value="Rusak Berat">Rusak Berat (Tidak Berfungsi / Rusak Total)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="progres_fisik" class="form-label fw-semibold text-secondary">Progres Pembangunan Fisik (%) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="progres_fisik" name="progres_fisik" min="0" max="100" value="{{ (int) ($infrastruktur->laporans->first()->progres_fisik ?? 0) }}" required>
                        <div class="form-text small text-muted">Isi 100 jika proyek pembangunan sudah selesai sepenuhnya.</div>
                    </div>

                    <div class="mb-3">
                        <label for="keterangan_laporan" class="form-label fw-semibold text-secondary">Keterangan / Catatan Pemeliharaan</label>
                        <textarea class="form-control" id="keterangan_laporan" name="keterangan" rows="3" placeholder="Masukkan catatan kondisi terkini..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light px-4 rounded-3" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-success px-4 rounded-3">Simpan Laporan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@if($infrastruktur->latitude && $infrastruktur->longitude)
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    $(document).ready(function() {
        var lat = parseFloat("{{ $infrastruktur->latitude }}");
        var lng = parseFloat("{{ $infrastruktur->longitude }}");
        
        if (!isNaN(lat) && !isNaN(lng)) {
            var standard = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            });

            var satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                attribution: 'Tiles &copy; Esri'
            });

            var map = L.map('map', {
                center: [lat, lng],
                zoom: 15,
                layers: [standard]
            });

            var baseMaps = {
                "Peta Standar": standard,
                "Citra Satelit": satellite
            };

            L.control.layers(baseMaps).addTo(map);

            var marker = L.marker([lat, lng]).addTo(map)
                .bindPopup("<b>{{ $infrastruktur->nama_proyek }}</b><br>{{ $infrastruktur->jenis_infrastruktur }}").openPopup();

            // Render existing GeoJSON polygon if exists
            var geojsonData = {!! $infrastruktur->geojson ? $infrastruktur->geojson : 'null' !!};
            if (geojsonData) {
                var polygonLayer = L.geoJSON(geojsonData, {
                    style: {
                        color: "#28a745",
                        weight: 3,
                        opacity: 0.8,
                        fillColor: "#28a745",
                        fillOpacity: 0.3
                    }
                }).addTo(map);

                // Adjust bounds to fit the polygon
                map.fitBounds(polygonLayer.getBounds());
            }
        }
    });
</script>
@endif
@endsection
