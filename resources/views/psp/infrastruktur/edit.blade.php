@extends('layouts.admin')

@section('title', 'Ubah Proyek Infrastruktur & Irigasi')

@section('styles')
<!-- Tom Select CSS -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .ts-wrapper.form-select {
        border: none;
        padding: 0;
        height: auto;
    }
</style>
@endsection

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Prasarana & Sarana Pertanian (PSP)', 'url' => route('infrastrukturs.index')],
    ['label' => 'Infrastruktur & Irigasi', 'url' => route('infrastrukturs.index')],
    ['label' => 'Ubah Proyek']
]" />

<div class="row">
    <div class="col-lg-10 col-xl-8">
        <div class="card custom-card border-0 p-4">
            <h5 class="fw-bold mb-4">Form Ubah Proyek Infrastruktur & Irigasi</h5>

            <form action="{{ route('infrastrukturs.update', $infrastruktur->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Kelompok Utama: Informasi Proyek -->
                <div class="card bg-light border-0 p-3 mb-4 rounded-3">
                    <label class="form-label fw-bold text-success small mb-2"><i class="bi bi-info-circle-fill me-1"></i>Informasi Proyek</label>
                    <div class="row g-3">
                        <div class="col-12">
                            <x-form.input 
                                name="nama_proyek" 
                                label="Nama Proyek / Kegiatan" 
                                value="{{ $infrastruktur->nama_proyek }}"
                                placeholder="Masukkan nama proyek" 
                                required="true" 
                            />
                        </div>
                        <div class="col-md-6">
                            <x-form.select 
                                name="jenis_infrastruktur" 
                                label="Jenis Infrastruktur" 
                                placeholder="-- Pilih Jenis --" 
                                :options="$jenisOptions"
                                selected="{{ $infrastruktur->jenis_infrastruktur }}"
                                required="true"
                            />
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="kelompok_tani_id" class="form-label fw-semibold text-secondary">
                                    Kelompok Tani Penerima <span class="text-muted">(Opsional)</span>
                                </label>
                                <select id="kelompok_tani_id" name="kelompok_tani_id" class="form-select @error('kelompok_tani_id') is-invalid @enderror">
                                    <option value="">-- Umum (Non-Kelompok) --</option>
                                    @foreach($kelompokTanis as $item)
                                        <option value="{{ $item->id }}" {{ old('kelompok_tani_id', $infrastruktur->kelompok_tani_id) == $item->id ? 'selected' : '' }}>
                                            {{ $item->nama }} (Desa {{ $item->desa ? $item->desa->nama : '-' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('kelompok_tani_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kelompok Kedua: Lokasi Proyek -->
                <div class="card bg-light border-0 p-3 mb-4 rounded-3">
                    <label class="form-label fw-bold text-success small mb-2"><i class="bi bi-geo-alt-fill me-1"></i>Lokasi Proyek</label>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="kecamatan_id" class="form-label fw-semibold text-secondary">
                                    Kecamatan <span class="text-danger">*</span>
                                </label>
                                <select id="kecamatan_id" name="kecamatan_id" required class="form-select @error('kecamatan_id') is-invalid @enderror">
                                    <option value="" disabled>-- Pilih Kecamatan --</option>
                                    @foreach($kecamatans as $kec)
                                        <option value="{{ $kec->id }}" {{ old('kecamatan_id', $infrastruktur->kecamatan_id) == $kec->id ? 'selected' : '' }}>
                                            {{ $kec->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kecamatan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="desa_id" class="form-label fw-semibold text-secondary">
                                    Desa <span class="text-danger">*</span>
                                </label>
                                <select id="desa_id" name="desa_id" required class="form-select @error('desa_id') is-invalid @enderror">
                                    <option value="" disabled>-- Pilih Desa --</option>
                                    @foreach($desas as $desa)
                                        <option value="{{ $desa->id }}" {{ old('desa_id', $infrastruktur->desa_id) == $desa->id ? 'selected' : '' }}>
                                            {{ $desa->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('desa_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <x-form.input 
                                name="latitude" 
                                label="Koordinat Latitude" 
                                value="{{ $infrastruktur->latitude }}"
                                placeholder="Contoh: -6.2088" 
                            />
                        </div>
                        <div class="col-md-6">
                            <x-form.input 
                                name="longitude" 
                                label="Koordinat Longitude" 
                                value="{{ $infrastruktur->longitude }}"
                                placeholder="Contoh: 106.8456" 
                            />
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="kml_file" class="form-label fw-semibold text-secondary">File KML Area / Poligon <span class="text-muted">(Opsional)</span></label>
                                <input type="file" id="kml_file" name="kml_file" accept=".kml,.xml" class="form-control @error('kml_file') is-invalid @enderror">
                                <div class="form-text small text-muted">Upload file .kml baru untuk memperbarui wilayah/poligon area pembangunan infrastruktur.</div>
                                @if($infrastruktur->kml_file)
                                    <div class="mt-1 small">
                                        <i class="bi bi-file-earmark-check text-success"></i> File KML terunggah: 
                                        <a href="{{ asset('storage/' . $infrastruktur->kml_file) }}" target="_blank" class="text-decoration-none fw-semibold">Unduh KML</a>
                                    </div>
                                @endif
                                @error('kml_file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <label class="form-label fw-semibold text-secondary">Pilih Lokasi di Peta</label>
                            <div id="map" style="height: 350px; border-radius: 12px; border: 1px solid #dee2e6;"></div>
                            <span class="text-muted small">Klik pada peta atau geser penanda untuk menentukan koordinat lokasi proyek. Gunakan opsi lapisan peta di kanan atas untuk berpindah tampilan.</span>
                        </div>
                    </div>
                </div>

                <!-- Kelompok Ketiga: Anggaran & Volume -->
                <div class="card bg-light border-0 p-3 mb-4 rounded-3">
                    <label class="form-label fw-bold text-success small mb-2"><i class="bi bi-cash-stack me-1"></i>Anggaran, Volume & Status</label>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <x-form.input 
                                name="volume" 
                                label="Volume / Dimensi" 
                                type="number"
                                step="0.01"
                                value="{{ $infrastruktur->volume }}"
                                placeholder="Contoh: 500" 
                                required="true" 
                            />
                        </div>
                        <div class="col-md-4">
                            <x-form.input 
                                name="satuan" 
                                label="Satuan" 
                                value="{{ $infrastruktur->satuan }}"
                                placeholder="Contoh: Meter, Unit, m3" 
                                required="true" 
                            />
                        </div>
                        <div class="col-md-4">
                            <x-form.input 
                                name="nilai_anggaran" 
                                label="Nilai Anggaran (Rp)" 
                                type="number" 
                                value="{{ (int) $infrastruktur->nilai_anggaran }}"
                                placeholder="Contoh: 150000000" 
                                required="true" 
                            />
                        </div>
                        <div class="col-md-4">
                            <x-form.select 
                                name="sumber_dana" 
                                label="Sumber Dana" 
                                placeholder="-- Pilih Sumber Dana --" 
                                :options="$sumberDanaOptions"
                                selected="{{ $infrastruktur->sumber_dana }}"
                                required="true"
                            />
                        </div>
                        <div class="col-md-4">
                            <x-form.input 
                                name="tahun_anggaran" 
                                label="Tahun Anggaran" 
                                type="number"
                                value="{{ $infrastruktur->tahun_anggaran }}"
                                required="true" 
                            />
                        </div>
                        <div class="col-md-4">
                            <x-form.select 
                                name="status_pembangunan" 
                                label="Status Pembangunan" 
                                :options="$statusOptions"
                                selected="{{ $infrastruktur->status_pembangunan }}"
                                required="true"
                            />
                        </div>
                    </div>
                </div>

                <!-- Keterangan Tambahan -->
                <div class="mb-4">
                    <x-form.textarea 
                        name="keterangan" 
                        label="Keterangan Tambahan" 
                        value="{{ $infrastruktur->keterangan }}"
                        placeholder="Catatan proyek atau penjelasan lokasi spesifik..." 
                        rows="3"
                    />
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-success px-4 rounded-3">Simpan Perubahan</button>
                    <a href="{{ route('infrastrukturs.index') }}" class="btn btn-light px-4 rounded-3 text-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Tom Select JS -->
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Tom Select for Kelompok Tani
        new TomSelect('#kelompok_tani_id', {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            }
        });

        // AJAX dependent dropdown for Desa
        function loadDesas(kecamatanId, selectedDesaId = null) {
            const $desaSelect = $('#desa_id');
            if (!kecamatanId) {
                $desaSelect.html('<option value="" disabled selected>-- Pilih Desa --</option>').prop('disabled', true);
                return;
            }

            $.ajax({
                url: "{{ route('infrastrukturs.ajax-desas', '') }}/" + kecamatanId,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $desaSelect.html('<option value="" disabled selected>-- Pilih Desa --</option>');
                    $.each(data, function(key, val) {
                        const isSelected = selectedDesaId && selectedDesaId == val.id ? 'selected' : '';
                        $desaSelect.append(`<option value="${val.id}" ${isSelected}>${val.nama}</option>`);
                    });
                }
            });
        }

        $('#kecamatan_id').change(function() {
            loadDesas($(this).val());
        });

        // Leaflet Map Picker
        var standard = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        });

        var satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the Giz User Community'
        });

        // Default center to Muna / Southeast Sulawesi region or current coordinates
        var mapCenter = [-4.7602834310101025, 122.53024089444527];
        var mapZoom = 13;

        var currentLat = parseFloat($('#latitude').val());
        var currentLng = parseFloat($('#longitude').val());
        if (!isNaN(currentLat) && !isNaN(currentLng)) {
            mapCenter = [currentLat, currentLng];
            mapZoom = 14;
        }

        var map = L.map('map', {
            center: mapCenter,
            zoom: mapZoom,
            layers: [standard]
        });

        var baseMaps = {
            "Peta Standar": standard,
            "Citra Satelit": satellite
        };

        L.control.layers(baseMaps).addTo(map);

        var marker;

        function updateMarker(lat, lng, zoom = 13) {
            if (marker) {
                marker.setLatLng([lat, lng]);
            } else {
                marker = L.marker([lat, lng], {draggable: true}).addTo(map);
                marker.on('dragend', function(e) {
                    var position = marker.getLatLng();
                    $('#latitude').val(position.lat.toFixed(6));
                    $('#longitude').val(position.lng.toFixed(6));
                });
            }
            map.setView([lat, lng], zoom);
        }

        // Initialize marker if coordinates exist
        if (!isNaN(currentLat) && !isNaN(currentLng)) {
            updateMarker(currentLat, currentLng, 14);
        }

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

        // Map Click Event
        map.on('click', function(e) {
            var lat = e.latlng.lat;
            var lng = e.latlng.lng;
            $('#latitude').val(lat.toFixed(6));
            $('#longitude').val(lng.toFixed(6));
            updateMarker(lat, lng);
        });

        // Input Changes Event
        $('#latitude, #longitude').on('input', function() {
            var lat = parseFloat($('#latitude').val());
            var lng = parseFloat($('#longitude').val());
            if (!isNaN(lat) && !isNaN(lng)) {
                updateMarker(lat, lng);
            }
        });
    });
</script>
@endsection
