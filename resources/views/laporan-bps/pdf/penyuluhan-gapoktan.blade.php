<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 8px; margin: 20px; color: #1e293b; }
    h2 { font-size: 13px; text-align: center; margin-bottom: 2px; }
    .subtitle { text-align: center; font-size: 9px; color: #64748b; margin-bottom: 12px; }
    table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    th { background: #0d9488; color: #fff; padding: 5px 6px; text-align: center; font-size: 8px; border: 1px solid #ddd; }
    td { padding: 4px 6px; border: 1px solid #ddd; }
    .text-center { text-align: center; }
    .total-row { background: #1e293b; color: #fff; font-weight: bold; }
    .footer { margin-top: 24px; font-size: 8px; text-align: right; color: #94a3b8; }
</style>
</head>
<body>
<h2>LAPORAN PENYULUHAN — DATA GAPOKTAN</h2>
<p class="subtitle">Kabupaten Muna Barat | Dicetak: {{ date('d/m/Y H:i') }}</p>
<table>
    <thead><tr><th>No</th><th>Nama Gapoktan</th><th>Ketua</th><th>Kecamatan</th><th>Jml Kelompok Tani</th></tr></thead>
    <tbody>
        @foreach($gapoktans as $i => $g)
        <tr>
            <td class="text-center">{{ $i+1 }}</td>
            <td>{{ $g->nama }}</td>
            <td>{{ $g->ketua ?? '-' }}</td>
            <td>{{ $g->kecamatan?->nama ?? '-' }}</td>
            <td class="text-center">{{ $g->kelompok_tanis_count }}</td>
        </tr>
        @endforeach
        <tr class="total-row"><td colspan="5">Total: {{ $gapoktans->count() }} gapoktan</td></tr>
    </tbody>
</table>
<p class="footer">SIM-Distan Kab. Muna Barat</p>
</body></html>
