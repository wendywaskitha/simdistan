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
<h2>LAPORAN PSP — DISTRIBUSI PUPUK</h2>
<p class="subtitle">Kabupaten Muna Barat — Tahun {{ $tahun }} | Dicetak: {{ date('d/m/Y H:i') }}</p>
<table>
    <thead><tr>
        <th>No</th><th>Kecamatan</th><th>Jenis Pupuk</th>
        <th>Kuota (Kg)</th><th>Realisasi (Kg)</th><th>Selisih (Kg)</th>
    </tr></thead>
    <tbody>
        @foreach($pupukData as $i => $row)
        <tr>
            <td class="text-center">{{ $i+1 }}</td>
            <td>{{ $row['kecamatan']->nama }}</td>
            <td>{{ $row['jenis']->nama }}</td>
            <td class="text-end">{{ number_format($row['kuota'], 2) }}</td>
            <td class="text-end">{{ number_format($row['realisasi'], 2) }}</td>
            <td class="text-end">{{ number_format($row['selisih'], 2) }}</td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="3" class="text-end">TOTAL</td>
            <td class="text-end">{{ number_format($pupukData->sum('kuota'), 2) }}</td>
            <td class="text-end">{{ number_format($pupukData->sum('realisasi'), 2) }}</td>
            <td class="text-end">{{ number_format($pupukData->sum('selisih'), 2) }}</td>
        </tr>
    </tbody>
</table>
<p class="footer">SIM-Distan Kab. Muna Barat</p>
</body></html>
