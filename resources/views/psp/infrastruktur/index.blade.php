@extends('layouts.admin')

@section('title', 'Infrastruktur & Irigasi Pertanian - Bidang PSP')

@section('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endsection

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Prasarana & Sarana Pertanian (PSP)'],
    ['label' => 'Infrastruktur & Irigasi']
]" />

<div class="card custom-card border-0 p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1">Daftar Pembangunan Infrastruktur & Irigasi</h5>
            <p class="text-muted small mb-0">Kelola dan pantau program bantuan serta pembangunan jaringan irigasi, embung, dan sarana pertanian lainnya.</p>
        </div>
        <a href="{{ route('infrastrukturs.create') }}" class="btn btn-success rounded-3 px-4 py-2">
            <i class="bi bi-plus-circle me-1"></i> Tambah Proyek
        </a>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs mb-4 border-bottom" id="infrastrukturTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold text-success border-0 border-bottom border-3 border-success px-3 pb-2" id="table-tab" data-bs-toggle="tab" data-bs-target="#table-tab-pane" type="button" role="tab" aria-controls="table-tab-pane" aria-selected="true">
                <i class="bi bi-list-task me-1"></i> Daftar Proyek
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold text-secondary border-0 px-3 pb-2" id="map-tab" data-bs-toggle="tab" data-bs-target="#map-tab-pane" type="button" role="tab" aria-controls="map-tab-pane" aria-selected="false">
                <i class="bi bi-map me-1"></i> Peta Sebaran
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="infrastrukturTabsContent">
        <!-- Tab 1: Table -->
        <div class="tab-pane fade show active" id="table-tab-pane" role="tabpanel" aria-labelledby="table-tab" tabindex="0">
            <!-- Filter Section -->
            <div class="card bg-light border-0 p-3 mb-4 rounded-3">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="filterKecamatan" class="form-label fw-semibold text-secondary small">Kecamatan</label>
                        <select id="filterKecamatan" class="form-select">
                            <option value="">Semua Kecamatan</option>
                            @foreach($kecamatans as $kec)
                                <option value="{{ $kec->id }}">{{ $kec->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filterDesa" class="form-label fw-semibold text-secondary small">Desa</label>
                        <select id="filterDesa" class="form-select" disabled>
                            <option value="">Pilih Kecamatan dahulu</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filterJenis" class="form-label fw-semibold text-secondary small">Jenis Infrastruktur</label>
                        <select id="filterJenis" class="form-select">
                            <option value="">Semua Jenis</option>
                            @foreach($jenisOptions as $val => $lbl)
                                <option value="{{ $val }}">{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="button" id="btnFilterReset" class="btn btn-secondary w-100 rounded-3">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
                        </button>
                    </div>
                </div>
            </div>

            <x-table :headers="['No', 'Nama Proyek', 'Jenis', 'Volume', 'Anggaran', 'Kecamatan', 'Desa', 'Kelompok Tani', 'Status', 'Aksi']" id="infrastrukturTable" />
        </div>

        <!-- Tab 2: Map Sebaran -->
        <div class="tab-pane fade" id="map-tab-pane" role="tabpanel" aria-labelledby="map-tab" tabindex="0">
            <div id="map-sebaran" style="height: 550px; border-radius: 16px; border: 1px solid #dee2e6;"></div>
            <div class="mt-2 text-muted small">
                <i class="bi bi-info-circle me-1"></i> Peta di atas menampilkan sebaran seluruh proyek pembangunan infrastruktur pertanian yang telah memiliki data koordinat lokasi.
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        const table = $('#infrastrukturTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('infrastrukturs.index') }}",
                data: function(d) {
                    d.kecamatan_id = $('#filterKecamatan').val();
                    d.desa_id = $('#filterDesa').val();
                    d.jenis_infrastruktur = $('#filterJenis').val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '5%'},
                {data: 'nama_proyek', name: 'nama_proyek'},
                {data: 'jenis_infrastruktur', name: 'jenis_infrastruktur'},
                {data: 'volume_format', name: 'volume', className: 'text-end'},
                {data: 'nilai_anggaran_format', name: 'nilai_anggaran', className: 'text-end'},
                {data: 'kecamatan_nama', name: 'kecamatan.nama'},
                {data: 'desa_nama', name: 'desa.nama'},
                {data: 'kelompok_tani_nama', name: 'kelompokTani.nama'},
                {
                    data: 'status_pembangunan', 
                    name: 'status_pembangunan',
                    render: function(data) {
                        let badgeClass = 'bg-secondary';
                        if (data === 'Rencana') {
                            badgeClass = 'bg-info text-white';
                        } else if (data === 'Konstruksi') {
                            badgeClass = 'bg-warning text-dark';
                        } else if (data === 'Selesai') {
                            badgeClass = 'bg-success';
                        } else if (data === 'Rusak') {
                            badgeClass = 'bg-danger';
                        }
                        return `<span class="badge ${badgeClass} px-3 py-2 rounded-pill">${data}</span>`;
                    }
                },
                {data: 'action', name: 'action', orderable: false, searchable: false, width: '18%'}
            ],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            }
        });

        // Trigger filters
        $('#filterKecamatan, #filterDesa, #filterJenis').change(function() {
            table.draw();
        });

        // Dependent dropdown for filters
        $('#filterKecamatan').change(function() {
            const kecamatanId = $(this).val();
            const $desaSelect = $('#filterDesa');

            if (!kecamatanId) {
                $desaSelect.html('<option value="">Pilih Kecamatan dahulu</option>').prop('disabled', true);
                table.draw();
                return;
            }

            $.ajax({
                url: "{{ route('infrastrukturs.ajax-desas', '') }}/" + kecamatanId,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $desaSelect.html('<option value="">Semua Desa</option>').prop('disabled', false);
                    $.each(data, function(key, val) {
                        $desaSelect.append(`<option value="${val.id}">${val.nama}</option>`);
                    });
                    table.draw();
                }
            });
        });

        // Reset Filter
        $('#btnFilterReset').click(function() {
            $('#filterKecamatan').val('').trigger('change');
            $('#filterJenis').val('');
            table.draw();
        });

        // Tab transition listener to initialize map once shown
        var mapSebaranInitialized = false;
        var mapSebaran;
        var markersGroup;

        function loadMapLocations() {
            var filterKecId = $('#filterKecamatan').val();
            var filterDesaId = $('#filterDesa').val();
            var filterJenis = $('#filterJenis').val();

            $.ajax({
                url: "{{ route('infrastrukturs.ajax-maps') }}",
                type: 'GET',
                data: {
                    kecamatan_id: filterKecId,
                    desa_id: filterDesaId,
                    jenis_infrastruktur: filterJenis
                },
                dataType: 'json',
                success: function(locations) {
                    if (markersGroup) {
                        markersGroup.clearLayers();
                    } else {
                        markersGroup = L.layerGroup().addTo(mapSebaran);
                    }

                    var bounds = [];

                    $.each(locations, function(key, loc) {
                        var lat = parseFloat(loc.latitude);
                        var lng = parseFloat(loc.longitude);
                        if (!isNaN(lat) && !isNaN(lng)) {
                            var showUrl = "{{ route('infrastrukturs.show', ':id') }}".replace(':id', loc.id);
                            
                            var statusBadge = 'bg-secondary';
                            if (loc.status_pembangunan === 'Rencana') statusBadge = 'bg-info text-white';
                            else if (loc.status_pembangunan === 'Konstruksi') statusBadge = 'bg-warning text-dark';
                            else if (loc.status_pembangunan === 'Selesai') statusBadge = 'bg-success';
                            else if (loc.status_pembangunan === 'Rusak') statusBadge = 'bg-danger';

                            var popupContent = `
                                <div class="p-1" style="min-width: 200px;">
                                    <h6 class="fw-bold mb-1" style="font-size: 14px;">${loc.nama_proyek}</h6>
                                    <p class="text-muted small mb-2">${loc.jenis_infrastruktur}</p>
                                    <table class="table table-sm table-borderless small mb-2" style="font-size: 11px;">
                                        <tr><td class="p-0"><b>Kecamatan:</b></td><td class="p-0">${loc.kecamatan ? loc.kecamatan.nama : '-'}</td></tr>
                                        <tr><td class="p-0"><b>Desa:</b></td><td class="p-0">${loc.desa ? loc.desa.nama : '-'}</td></tr>
                                        <tr><td class="p-0"><b>Anggaran:</b></td><td class="p-0 text-success">Rp ${new Intl.NumberFormat('id-ID').format(loc.nilai_anggaran)}</td></tr>
                                        <tr><td class="p-0"><b>Status:</b></td><td class="p-0"><span class="badge ${statusBadge} px-2 py-0.5 rounded-pill">${loc.status_pembangunan}</span></td></tr>
                                    </table>
                                    <a href="${showUrl}" class="btn btn-sm btn-primary w-100 text-white rounded-3 mt-1" style="font-size: 11px;"><i class="bi bi-eye"></i> Detail Proyek</a>
                                </div>
                            `;

                            var marker = L.marker([lat, lng]).bindPopup(popupContent);
                            markersGroup.addLayer(marker);
                            bounds.push([lat, lng]);

                            // Draw polygon if exists
                            if (loc.geojson) {
                                try {
                                    var geojsonObj = typeof loc.geojson === 'string' ? JSON.parse(loc.geojson) : loc.geojson;
                                    var polyLayer = L.geoJSON(geojsonObj, {
                                        style: {
                                            color: "#198754",
                                            weight: 3,
                                            opacity: 0.7,
                                            fillColor: "#198754",
                                            fillOpacity: 0.25
                                        }
                                    }).bindPopup(popupContent);
                                    markersGroup.addLayer(polyLayer);
                                } catch(e) {
                                    console.error("Invalid GeoJSON parsing for loc: " + loc.id, e);
                                }
                            }
                        }
                    });

                    if (bounds.length > 0) {
                        mapSebaran.fitBounds(bounds, {padding: [50, 50]});
                    } else {
                        // Reset center to default coordinate
                        mapSebaran.setView([-4.7602834310101025, 122.53024089444527], 13);
                    }
                }
            });
        }

        function initMapSebaran() {
            if (mapSebaranInitialized) {
                mapSebaran.invalidateSize();
                loadMapLocations();
                return;
            }

            var standard = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            });

            var satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                attribution: 'Tiles &copy; Esri'
            });

            mapSebaran = L.map('map-sebaran', {
                center: [-4.7602834310101025, 122.53024089444527],
                zoom: 13,
                layers: [standard]
            });

            var baseMaps = {
                "Peta Standar": standard,
                "Citra Satelit": satellite
            };

            L.control.layers(baseMaps).addTo(mapSebaran);
            mapSebaranInitialized = true;
            loadMapLocations();
        }

        // Tab click event
        var mapTabEl = document.querySelector('#map-tab');
        if (mapTabEl) {
            mapTabEl.addEventListener('shown.bs.tab', function (event) {
                initMapSebaran();
            });
        }

        // Re-load map locations when filters change
        $('#filterKecamatan, #filterDesa, #filterJenis').change(function() {
            if (mapSebaranInitialized) {
                loadMapLocations();
            }
        });
        $('#btnFilterReset').click(function() {
            if (mapSebaranInitialized) {
                loadMapLocations();
            }
        });
    });
</script>
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endsection


