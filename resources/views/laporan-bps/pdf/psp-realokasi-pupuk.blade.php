<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 8px; margin: 20px; color: #1e293b; }
    h2 { font-size: 13px; text-align: center; margin-bottom: 2px; }
    .subtitle { text-align: center; font-size: 9px; color: #64748b; margin-bottom: 12px; }
    table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    th { background: #f8f9fa; color: #1e293b; padding: 5px 6px; text-align: center; font-size: 8px; border: 1px solid #ddd; }
    td { padding: 4px 6px; border: 1px solid #ddd; }
    .text-end { text-align: right; }
    .text-center { text-align: center; }
    .footer { margin-top: 24px; font-size: 8px; text-align: right; color: #94a3b8; }
</style>
</head>
<body>
<h2>LAPORAN PSP — DETAIL REALOKASI PUPUK</h2>
<p class="subtitle">Kabupaten Muna Barat — Tahun {{ $tahun }} | Dicetak: {{ date('d/m/Y H:i') }}</p>
<table>
    <thead>
        <tr>
            <th style="width: 30px;">No</th>
            <th>Jenis Pupuk</th>
            <th>Kecamatan Asal</th>
            <th>Kecamatan Tujuan</th>
            <th class="text-end" style="width: 80px;">Jumlah (Kg)</th>
            <th>Bulan / Tahun</th>
            <th>Nama SK / Dokumen</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @php
            $bulans = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
                7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
        @endphp
        @forelse($realokasiPupuks as $i => $row)
        <tr>
            <td class="text-center">{{ $i + 1 }}</td>
            <td style="font-weight: bold;">{{ $row->jenis?->nama ?? '-' }}</td>
            <td>{{ $row->kecamatanAsal?->nama ?? '-' }}</td>
            <td>{{ $row->kecamatanTujuan?->nama ?? '-' }}</td>
            <td class="text-end">{{ number_format($row->jumlah, 2, ',', '.') }} Kg</td>
            <td class="text-center">{{ isset($bulans[$row->bulan]) ? $bulans[$row->bulan] : '-' }} {{ $row->tahun }}</td>
            <td>{{ $row->nama_sk ?? '-' }}</td>
            <td>{{ $row->keterangan ?? '-' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="text-center">Tidak ada data realokasi pupuk.</td>
        </tr>
        @endforelse
    </tbody>
</table>
<p class="footer">SIM-Distan Kab. Muna Barat</p>
</body></html>
