<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 8px; margin: 20px; color: #1e293b; }
    h2 { font-size: 13px; text-align: center; margin-bottom: 2px; }
    .subtitle { text-align: center; font-size: 9px; color: #64748b; margin-bottom: 12px; }
    table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    th { background: #8b5cf6; color: #fff; padding: 5px 6px; text-align: center; font-size: 8px; border: 1px solid #ddd; }
    td { padding: 4px 6px; border: 1px solid #ddd; }
    .text-end { text-align: right; }
    .text-center { text-align: center; }
    .total-row { background: #1e293b; color: #fff; font-weight: bold; }
    .footer { margin-top: 24px; font-size: 8px; text-align: right; color: #94a3b8; }
</style>
</head>
<body>
<h2>LAPORAN PSP — INFRASTRUKTUR & IRIGASI</h2>
<p class="subtitle">Kabupaten Muna Barat — Tahun {{ $tahun }} | Dicetak: {{ date('d/m/Y H:i') }}</p>
<table>
    <thead><tr>
        <th>No</th><th>Nama Proyek</th><th>Jenis</th><th>Kecamatan</th><th>Desa</th>
        <th>Volume</th><th>Satuan</th><th>Nilai Anggaran (Rp)</th><th>Sumber</th><th>Tahun</th><th>Status</th>
    </tr></thead>
    <tbody>
        @foreach($infrastrukturs as $i => $inf)
        <tr>
            <td class="text-center">{{ $i+1 }}</td>
            <td>{{ $inf->nama_proyek }}</td>
            <td>{{ $inf->jenis_infrastruktur }}</td>
            <td>{{ $inf->kecamatan?->nama ?? '-' }}</td>
            <td>{{ $inf->desa?->nama ?? '-' }}</td>
            <td class="text-end">{{ number_format($inf->volume, 0) }}</td>
            <td>{{ $inf->satuan }}</td>
            <td class="text-end">{{ number_format($inf->nilai_anggaran, 0, ',', '.') }}</td>
            <td>{{ $inf->sumber_dana }}</td>
            <td class="text-center">{{ $inf->tahun_anggaran }}</td>
            <td class="text-center">{{ $inf->status_pembangunan }}</td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="7" class="text-end">Total Nilai Anggaran:</td>
            <td class="text-end">{{ number_format($infrastrukturs->sum('nilai_anggaran'), 0, ',', '.') }}</td>
            <td colspan="3"></td>
        </tr>
    </tbody>
</table>
<p class="footer">SIM-Distan Kab. Muna Barat</p>
</body></html>
