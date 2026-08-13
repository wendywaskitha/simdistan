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
<h2>LAPORAN PSP — DETAIL REALOKASI ALSINTAN</h2>
<p class="subtitle">Kabupaten Muna Barat — Tahun {{ $tahun }} | Dicetak: {{ date('d/m/Y H:i') }}</p>
<table>
    <thead>
        <tr>
            <th style="width: 30px;">No</th>
            <th>Alat Alsintan</th>
            <th>Kelompok Tani Asal</th>
            <th>Kecamatan Asal</th>
            <th>Kelompok Tani Tujuan</th>
            <th>Kecamatan Tujuan</th>
            <th>Tanggal Realokasi</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @forelse($realokasiAlsintans as $i => $row)
        <tr>
            <td class="text-center">{{ $i + 1 }}</td>
            <td style="font-weight: bold;">{{ $row->alsintan?->nama_alat ?? '-' }} ({{ $row->alsintan?->jenisAlat?->nama ?? '-' }})</td>
            <td>{{ $row->kelompokTaniAsal?->nama ?? '-' }}</td>
            <td>{{ $row->kelompokTaniAsal?->desa?->kecamatan?->nama ?? '-' }}</td>
            <td>{{ $row->kelompokTaniTujuan?->nama ?? '-' }}</td>
            <td>{{ $row->kelompokTaniTujuan?->desa?->kecamatan?->nama ?? '-' }}</td>
            <td class="text-center">{{ $row->tanggal_realokasi ? $row->tanggal_realokasi->format('d/m/Y') : '-' }}</td>
            <td>{{ $row->keterangan ?? '-' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="text-center">Tidak ada data realokasi alsintan.</td>
        </tr>
        @endforelse
    </tbody>
</table>
<p class="footer">SIM-Distan Kab. Muna Barat</p>
</body></html>
