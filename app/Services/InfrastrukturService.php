<?php

namespace App\Services;

use App\Repositories\InfrastrukturRepositoryInterface;
use App\Repositories\InfrastrukturLaporanRepositoryInterface;
use App\Models\Infrastruktur;
use App\Models\InfrastrukturLaporan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\UploadedFile;

class InfrastrukturService
{
    protected $infrastrukturRepository;
    protected $laporanRepository;

    public function __construct(
        InfrastrukturRepositoryInterface $infrastrukturRepository,
        InfrastrukturLaporanRepositoryInterface $laporanRepository
    ) {
        $this->infrastrukturRepository = $infrastrukturRepository;
        $this->laporanRepository = $laporanRepository;
    }

    /**
     * Get all Infrastruktur.
     */
    public function getAllInfrastruktur(): Collection
    {
        return $this->infrastrukturRepository->all();
    }

    /**
     * Get an Infrastruktur by ID.
     */
    public function getInfrastrukturById(int $id): ?Infrastruktur
    {
        return $this->infrastrukturRepository->find($id);
    }

    /**
     * Create a new Infrastruktur record and its initial condition/progress report.
     */
    public function createInfrastruktur(array $data, ?UploadedFile $kmlFile = null): Infrastruktur
    {
        if ($kmlFile) {
            $path = $kmlFile->store('kmls', 'public');
            $data['kml_file'] = $path;
            
            $kmlContent = file_get_contents($kmlFile->getRealPath());
            $data['geojson'] = $this->parseKmlToGeoJson($kmlContent);
        }

        return DB::transaction(function () use ($data) {
            $infrastruktur = $this->infrastrukturRepository->create($data);

            // Create initial status report
            $progres = 0.00;
            if ($infrastruktur->status_pembangunan === 'Selesai') {
                $progres = 100.00;
            } elseif ($infrastruktur->status_pembangunan === 'Konstruksi') {
                $progres = 50.00;
            }

            $this->laporanRepository->create([
                'infrastruktur_id' => $infrastruktur->id,
                'tanggal_laporan' => now()->format('Y-m-d'),
                'kondisi' => $infrastruktur->status_pembangunan === 'Rusak' ? 'Rusak Berat' : 'Baik',
                'progres_fisik' => $progres,
                'keterangan' => 'Laporan awal saat pembangunan didaftarkan.'
            ]);

            return $infrastruktur;
        });
    }

    /**
     * Update an existing Infrastruktur.
     */
    public function updateInfrastruktur(int $id, array $data, ?UploadedFile $kmlFile = null): bool
    {
        if ($kmlFile) {
            $path = $kmlFile->store('kmls', 'public');
            $data['kml_file'] = $path;
            
            $kmlContent = file_get_contents($kmlFile->getRealPath());
            $data['geojson'] = $this->parseKmlToGeoJson($kmlContent);
        }

        return $this->infrastrukturRepository->update($id, $data);
    }

    /**
     * Delete an Infrastruktur.
     */
    public function deleteInfrastruktur(int $id): bool
    {
        return $this->infrastrukturRepository->delete($id);
    }

    /**
     * Parse KML file to GeoJSON format.
     */
    public function parseKmlToGeoJson(string $kmlContent): ?string
    {
        try {
            $xml = simplexml_load_string($kmlContent);
            if ($xml === false) {
                return null;
            }

            $namespaces = $xml->getDocNamespaces();
            $ns = isset($namespaces['']) ? $namespaces[''] : 'http://www.opengis.net/kml/2.2';
            $xml->registerXPathNamespace('kml', $ns);

            $polygons = $xml->xpath('//kml:Polygon');
            
            if (empty($polygons)) {
                $lineStrings = $xml->xpath('//kml:LineString');
                if (empty($lineStrings)) {
                    return null;
                }

                $coordinatesList = [];
                foreach ($lineStrings as $ls) {
                    if (isset($ls->coordinates)) {
                        $coordinatesList[] = $this->parseCoordinatesString((string) $ls->coordinates);
                    }
                }
                
                return json_encode([
                    'type' => 'MultiLineString',
                    'coordinates' => $coordinatesList
                ]);
            }

            $polygonCoordinates = [];
            foreach ($polygons as $poly) {
                $outerBoundary = $poly->xpath('kml:outerBoundaryIs/kml:LinearRing/kml:coordinates');
                if (!empty($outerBoundary)) {
                    $polygonCoordinates[] = [
                        $this->parseCoordinatesString((string) $outerBoundary[0])
                    ];
                }
            }

            if (empty($polygonCoordinates)) {
                return null;
            }

            return json_encode([
                'type' => count($polygonCoordinates) > 1 ? 'MultiPolygon' : 'Polygon',
                'coordinates' => count($polygonCoordinates) > 1 ? $polygonCoordinates : $polygonCoordinates[0]
            ]);

        } catch (\Exception $e) {
            \Log::error('KML Parsing Error: ' . $e->getMessage());
            return null;
        }
    }

    private function parseCoordinatesString(string $coordsStr): array
    {
        $points = [];
        $cleanStr = preg_replace('/\s+/', ' ', trim($coordsStr));
        $coordPairs = explode(' ', $cleanStr);

        foreach ($coordPairs as $pair) {
            $parts = explode(',', $pair);
            if (count($parts) >= 2) {
                $lng = (float) $parts[0];
                $lat = (float) $parts[1];
                $points[] = [$lng, $lat];
            }
        }
        return $points;
    }

    /**
     * Add a condition / progress report and sync with main status.
     */
    public function tambahLaporanKondisi(int $infrastrukturId, array $data): InfrastrukturLaporan
    {
        return DB::transaction(function () use ($infrastrukturId, $data) {
            $data['infrastruktur_id'] = $infrastrukturId;
            $laporan = $this->laporanRepository->create($data);

            // Sync main status_pembangunan based on latest progress and condition
            $infrastruktur = $this->getInfrastrukturById($infrastrukturId);
            if ($infrastruktur) {
                $status = $infrastruktur->status_pembangunan;
                if ($laporan->kondisi === 'Rusak Berat') {
                    $status = 'Rusak';
                } elseif ($laporan->progres_fisik >= 100) {
                    $status = 'Selesai';
                } elseif ($laporan->progres_fisik > 0) {
                    $status = 'Konstruksi';
                }

                $infrastruktur->update([
                    'status_pembangunan' => $status
                ]);
            }

            return $laporan;
        });
    }
}
