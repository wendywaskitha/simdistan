<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Support\Collection;

class LaporanBpsExport implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    protected string $type;
    protected $data;
    protected int $tahun;

    public function __construct(string $type, $data, int $tahun)
    {
        $this->type  = $type;
        $this->data  = $data;
        $this->tahun = $tahun;
    }

    public function collection(): Collection
    {
        return match ($this->type) {
            'tanaman-pangan'       => $this->panganRows(),
            'hortikultura'         => $this->hortikulturaRows(),
            'perkebunan'           => $this->perkebunanRows(),
            'psp-alsintan'         => $this->alsintanRows(),
            'psp-infrastruktur'    => $this->infraRows(),
            'psp-pupuk'            => $this->pupukRows(),
            'psp-pemanfaatan'      => $this->pemanfaatanRows(),
            'psp-laporan-infrastruktur' => $this->laporanInfraRows(),
            'psp-realokasi-alsintan'    => $this->realokasiAlsintanRows(),
            'psp-realokasi-pupuk'       => $this->realokasiPupukRows(),
            'penyuluhan-penyuluh'  => $this->penyuluhRows(),
            'penyuluhan-gapoktan'  => $this->gapoktanRows(),
            'penyuluhan-kelompoktani' => $this->kelompokTaniRows(),
            'penyuluhan-petani'    => $this->petaniRows(),
            'penyuluhan-bpp'       => $this->bppRows(),
            default                => collect(),
        };
    }

    public function headings(): array
    {
        return match ($this->type) {
            'tanaman-pangan'       => ['No', 'Kecamatan', 'Komoditas', 'Luas Lahan (Ha)', 'Luas Tanam (Ha)', 'Luas Panen (Ha)', 'Produksi (Ton)', 'Produktivitas (Ton/Ha)'],
            'hortikultura'         => ['No', 'Kecamatan', 'Komoditas', 'Form', 'Semester', 'Luas Tanam Akhir / Jumlah Pohon', 'Luas Panen (Ha)', 'Produksi', 'Satuan'],
            'perkebunan'           => ['No', 'Kecamatan', 'Komoditas', 'Semester', 'TBM (Ha)', 'TM (Ha)', 'TTM (Ha)', 'Total Luas (Ha)', 'Produksi', 'Wujud Produksi', 'Petani Pemilik', 'Petani BMU'],
            'psp-alsintan'         => ['No', 'Kelompok Tani', 'Kecamatan', 'Jenis Alat', 'Nama Alat', 'Merek', 'Kondisi', 'Sumber Dana', 'Tahun Bantuan'],
            'psp-infrastruktur'    => ['No', 'Nama Proyek', 'Jenis Infrastruktur', 'Kecamatan', 'Desa', 'Volume', 'Satuan', 'Nilai Anggaran (Rp)', 'Sumber Dana', 'Tahun Anggaran', 'Status'],
            'psp-pupuk'            => ['No', 'Kecamatan', 'Jenis Pupuk', 'Kuota (Kg)', 'Realisasi (Kg)', 'Selisih (Kg)'],
            'psp-pemanfaatan'      => ['No', 'Kelompok Tani', 'Kecamatan', 'Alat', 'Tanggal/Rentang Pemanfaatan', 'Luas Lahan (Ha)', 'Durasi Kerja (Jam)', 'Biaya Pengolahan (Rp)'],
            'psp-laporan-infrastruktur' => ['No', 'Proyek Infrastruktur', 'Jenis', 'Kecamatan', 'Desa', 'Tanggal Laporan', 'Kondisi', 'Progres Fisik (%)', 'Keterangan'],
            'psp-realokasi-alsintan'    => ['No', 'Alat Alsintan', 'Kelompok Tani Asal', 'Kecamatan Asal', 'Kelompok Tani Tujuan', 'Kecamatan Tujuan', 'Tanggal Realokasi', 'Keterangan'],
            'psp-realokasi-pupuk'       => ['No', 'Jenis Pupuk', 'Kecamatan Asal', 'Kecamatan Tujuan', 'Jumlah (Kg)', 'Bulan', 'Tahun', 'Nama SK / Dokumen', 'Keterangan'],
            'penyuluhan-penyuluh'  => ['No', 'Nama Penyuluh', 'NIP', 'Telepon', 'BPP', 'Kecamatan'],
            'penyuluhan-gapoktan'  => ['No', 'Nama Gapoktan', 'Ketua', 'Kecamatan', 'Jumlah Kelompok Tani'],
            'penyuluhan-kelompoktani' => ['No', 'Nama Kelompok Tani', 'Ketua', 'Desa', 'Kecamatan', 'Gapoktan', 'Jumlah Petani'],
            'penyuluhan-petani'    => ['No', 'Nama Petani', 'NIK', 'Telepon', 'Alamat', 'Kelompok Tani', 'Kecamatan'],
            'penyuluhan-bpp'       => ['No', 'Nama BPP', 'Kecamatan', 'Alamat'],
            default                => [],
        };
    }

    public function title(): string
    {
        return match ($this->type) {
            'tanaman-pangan'          => 'Tanaman Pangan',
            'hortikultura'            => 'Hortikultura',
            'perkebunan'              => 'Perkebunan',
            'psp-alsintan'            => 'Alsintan',
            'psp-infrastruktur'       => 'Infrastruktur',
            'psp-pupuk'               => 'Distribusi Pupuk',
            'psp-pemanfaatan'         => 'Pemanfaatan Alsintan',
            'psp-laporan-infrastruktur' => 'Kondisi Infrastruktur',
            'psp-realokasi-alsintan'    => 'Realokasi Alsintan',
            'psp-realokasi-pupuk'       => 'Realokasi Pupuk',
            'penyuluhan-penyuluh'     => 'Data Penyuluh',
            'penyuluhan-gapoktan'     => 'Data Gapoktan',
            'penyuluhan-kelompoktani' => 'Kelompok Tani',
            'penyuluhan-petani'       => 'Data Petani',
            'penyuluhan-bpp'          => 'Data BPP',
            default                   => 'Laporan',
        };
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF10B981']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    // ─── Row builders ─────────────────────────────────────────────────────────
    private function panganRows(): Collection
    {
        $rows = collect();
        $no   = 1;
        foreach ($this->data as $group) {
            foreach ($group['rows'] as $row) {
                $rows->push([
                    $no++,
                    $group['kecamatan']->nama,
                    $row['komoditas']->nama,
                    number_format($row['luas_lahan'], 2),
                    number_format($row['luas_tanam'], 2),
                    number_format($row['luas_panen'], 2),
                    number_format($row['produksi'], 2),
                    number_format($row['produktivitas'], 4),
                ]);
            }
            // Sub-total per kecamatan
            $rows->push([
                '', 'Sub-Total: ' . $group['kecamatan']->nama, '',
                number_format($group['total_lahan'], 2),
                number_format($group['total_tanam'], 2),
                number_format($group['total_panen'], 2),
                number_format($group['total_produksi'], 2), '',
            ]);
        }
        return $rows;
    }

    private function hortikulturaRows(): Collection
    {
        $semesters = [1 => 'Semester I', 2 => 'Semester II'];
        return $this->data->values()->map(function ($lap, $i) use ($semesters) {
            return [
                $i + 1,
                $lap->kecamatan->nama ?? '-',
                $lap->komoditas->nama ?? '-',
                $lap->form_type ?? '-',
                $semesters[$lap->bulan] ?? $lap->bulan,
                number_format($lap->luas_tanam_akhir ?? $lap->jumlah_tanaman_menghasilkan ?? 0, 2),
                number_format($lap->luas_panen ?? 0, 2),
                number_format($lap->produksi ?? 0, 2),
                $lap->satuan->nama ?? '-',
            ];
        });
    }

    private function perkebunanRows(): Collection
    {
        $semesters = [1 => 'Semester I', 2 => 'Semester II'];
        return $this->data->values()->map(function ($lap, $i) use ($semesters) {
            return [
                $i + 1,
                $lap->kecamatan->nama ?? '-',
                $lap->komoditas->nama ?? '-',
                $semesters[$lap->bulan] ?? $lap->bulan,
                number_format($lap->tbm ?? 0, 2),
                number_format($lap->tm ?? 0, 2),
                number_format($lap->ttm ?? 0, 2),
                number_format($lap->luas_jumlah ?? 0, 2),
                number_format($lap->produksi ?? 0, 2),
                $lap->wujud_produksi ?? '-',
                $lap->jumlah_petani_pemilik ?? 0,
                $lap->jumlah_petani_bmu ?? 0,
            ];
        });
    }

    private function alsintanRows(): Collection
    {
        return $this->data->values()->map(function ($als, $i) {
            $kec = $als->kelompokTani?->desa?->kecamatan?->nama ?? '-';
            return [
                $i + 1,
                $als->kelompokTani?->nama ?? '-',
                $kec,
                $als->jenisAlat?->nama ?? '-',
                $als->nama_alat ?? '-',
                $als->merek ?? '-',
                $als->kondisi ?? '-',
                $als->sumber_dana ?? '-',
                $als->tahun_bantuan,
            ];
        });
    }

    private function infraRows(): Collection
    {
        return $this->data->values()->map(function ($inf, $i) {
            return [
                $i + 1,
                $inf->nama_proyek,
                $inf->jenis_infrastruktur,
                $inf->kecamatan?->nama ?? '-',
                $inf->desa?->nama ?? '-',
                $inf->volume,
                $inf->satuan,
                number_format($inf->nilai_anggaran, 0, ',', '.'),
                $inf->sumber_dana,
                $inf->tahun_anggaran,
                $inf->status_pembangunan,
            ];
        });
    }

    private function pupukRows(): Collection
    {
        return $this->data->values()->map(function ($row, $i) {
            return [
                $i + 1,
                $row['kecamatan']->nama,
                $row['jenis']->nama,
                number_format($row['kuota'], 2),
                number_format($row['realisasi'], 2),
                number_format($row['selisih'], 2),
            ];
        });
    }

    private function pemanfaatanRows(): Collection
    {
        return $this->data->values()->map(function ($lap, $i) {
            $kelompok = $lap->alsintan?->kelompokTani?->nama ?? '-';
            $kec = $lap->alsintan?->kelompokTani?->desa?->kecamatan?->nama ?? '-';
            $alat = ($lap->alsintan?->jenisAlat?->nama ?? '-') . ' - ' . ($lap->alsintan?->nama_alat ?? '-');
            
            $rentang = '-';
            if ($lap->tanggal_mulai && $lap->tanggal_selesai) {
                $rentang = $lap->tanggal_mulai->format('d/m/Y') . ' s/d ' . $lap->tanggal_selesai->format('d/m/Y');
            } elseif ($lap->tanggal) {
                $rentang = $lap->tanggal->format('d/m/Y');
            }

            return [
                $i + 1,
                $kelompok,
                $kec,
                $alat,
                $rentang,
                number_format($lap->luas_lahan, 2),
                $lap->waktu_pengerjaan,
                number_format($lap->biaya_pengolahan, 2),
            ];
        });
    }

    private function laporanInfraRows(): Collection
    {
        return $this->data->values()->map(function ($lap, $i) {
            $nama = $lap->infrastruktur?->nama_proyek ?? '-';
            $jenis = $lap->infrastruktur?->jenis_infrastruktur ?? '-';
            $kec = $lap->infrastruktur?->kecamatan?->nama ?? '-';
            $desa = $lap->infrastruktur?->desa?->nama ?? '-';
            $tgl = $lap->tanggal_laporan ? \Carbon\Carbon::parse($lap->tanggal_laporan)->format('d/m/Y') : '-';
            return [
                $i + 1,
                $nama,
                $jenis,
                $kec,
                $desa,
                $tgl,
                $lap->kondisi ?? '-',
                ($lap->progres_fisik ?? 0) . '%',
                $lap->keterangan ?? '-',
            ];
        });
    }

    private function realokasiAlsintanRows(): Collection
    {
        return $this->data->values()->map(function ($row, $i) {
            $alat = ($row->alsintan?->jenisAlat?->nama ?? '-') . ' - ' . ($row->alsintan?->nama_alat ?? '-');
            return [
                $i + 1,
                $alat,
                $row->kelompokTaniAsal?->nama ?? '-',
                $row->kelompokTaniAsal?->desa?->kecamatan?->nama ?? '-',
                $row->kelompokTaniTujuan?->nama ?? '-',
                $row->kelompokTaniTujuan?->desa?->kecamatan?->nama ?? '-',
                $row->tanggal_realokasi ? \Carbon\Carbon::parse($row->tanggal_realokasi)->format('d/m/Y') : '-',
                $row->keterangan ?? '-',
            ];
        });
    }

    private function realokasiPupukRows(): Collection
    {
        return $this->data->values()->map(function ($row, $i) {
            $bulans = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
                7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
            $bln = isset($bulans[$row->bulan]) ? $bulans[$row->bulan] : '-';
            return [
                $i + 1,
                $row->jenis?->nama ?? '-',
                $row->kecamatanAsal?->nama ?? '-',
                $row->kecamatanTujuan?->nama ?? '-',
                number_format($row->jumlah, 2, ',', '.'),
                $bln,
                $row->tahun,
                $row->nama_sk ?? '-',
                $row->keterangan ?? '-',
            ];
        });
    }

    // ─── Penyuluhan rows ──────────────────────────────────────────────────────
    private function penyuluhRows(): Collection
    {
        return $this->data->values()->map(function ($p, $i) {
            return [
                $i + 1,
                $p->nama,
                $p->nip ?? '-',
                $p->telepon ?? '-',
                $p->bpp?->nama ?? '-',
                $p->bpp?->kecamatan?->nama ?? '-',
            ];
        });
    }

    private function gapoktanRows(): Collection
    {
        return $this->data->values()->map(function ($g, $i) {
            return [
                $i + 1,
                $g->nama,
                $g->ketua ?? '-',
                $g->kecamatan?->nama ?? '-',
                $g->kelompok_tanis_count ?? 0,
            ];
        });
    }

    private function kelompokTaniRows(): Collection
    {
        return $this->data->values()->map(function ($k, $i) {
            return [
                $i + 1,
                $k->nama,
                $k->ketua ?? '-',
                $k->desa?->nama ?? '-',
                $k->desa?->kecamatan?->nama ?? '-',
                $k->gapoktan?->nama ?? '-',
                $k->petanis_count ?? 0,
            ];
        });
    }

    private function petaniRows(): Collection
    {
        return $this->data->values()->map(function ($p, $i) {
            return [
                $i + 1,
                $p->nama,
                $p->nik ?? '-',
                $p->telepon ?? '-',
                $p->alamat ?? '-',
                $p->kelompokTani?->nama ?? '-',
                $p->kelompokTani?->desa?->kecamatan?->nama ?? '-',
            ];
        });
    }

    private function bppRows(): Collection
    {
        return $this->data->values()->map(function ($b, $i) {
            return [
                $i + 1,
                $b->nama,
                $b->kecamatan?->nama ?? '-',
                $b->alamat ?? '-',
            ];
        });
    }
}
