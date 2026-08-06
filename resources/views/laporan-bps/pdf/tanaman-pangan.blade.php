<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 9px; margin: 20px; color: #1e293b; }
    h2 { font-size: 13px; text-align: center; margin-bottom: 2px; }
    .subtitle { text-align: center; font-size: 9px; color: #64748b; margin-bottom: 12px; }
    table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    th { background: #10b981; color: #fff; padding: 5px 6px; text-align: center; font-size: 8px; border: 1px solid #ddd; }
    td { padding: 4px 6px; border: 1px solid #ddd; }
    .text-end { text-align: right; }
    .text-center { text-align: center; }
    .sub-header { background: #e2f5ed; font-weight: bold; color: #059669; }
    .sub-total { background: #fef3c7; font-weight: bold; }
    .grand-total { background: #1e293b; color: #fff; font-weight: bold; }
    .footer { margin-top: 24px; font-size: 8px; text-align: right; color: #94a3b8; }
</style>
</head>
<body>
<h2>LAPORAN PRODUKSI TANAMAN PANGAN</h2>
<p class="subtitle">Kabupaten Muna Barat — Tahun {{ $tahun }} | Dicetak: {{ date('d/m/Y H:i') }}</p>

<table>
    <thead>
        <tr>
            <th width="4%">No</th>
            <th>Komoditas</th>
            <th>Luas Lahan (Ha)</th>
            <th>Luas Tanam (Ha)</th>
            <th>Luas Panen (Ha)</th>
            <th>Produksi (Ton)</th>
            <th>Produktivitas (Ton/Ha)</th>
        </tr>
    </thead>
    <tbody>
        @php $no = 1; @endphp
        @foreach($grouped as $group)
        <tr class="sub-header">
            <td colspan="7">Kecamatan {{ $group['kecamatan']->nama }}</td>
        </tr>
        @foreach($group['rows'] as $row)
        <tr>
            <td class="text-center">{{ $no++ }}</td>
            <td>{{ $row['komoditas']->nama }}</td>
            <td class="text-end">{{ number_format($row['luas_lahan'],2) }}</td>
            <td class="text-end">{{ number_format($row['luas_tanam'],2) }}</td>
            <td class="text-end">{{ number_format($row['luas_panen'],2) }}</td>
            <td class="text-end">{{ number_format($row['produksi'],2) }}</td>
            <td class="text-end">{{ number_format($row['produktivitas'],4) }}</td>
        </tr>
        @endforeach
        <tr class="sub-total">
            <td colspan="2" class="text-end">Sub-Total {{ $group['kecamatan']->nama }}</td>
            <td class="text-end">{{ number_format($group['total_lahan'],2) }}</td>
            <td class="text-end">{{ number_format($group['total_tanam'],2) }}</td>
            <td class="text-end">{{ number_format($group['total_panen'],2) }}</td>
            <td class="text-end">{{ number_format($group['total_produksi'],2) }}</td>
            <td></td>
        </tr>
        @endforeach
        <tr class="grand-total">
            <td colspan="2" class="text-end">GRAND TOTAL</td>
            <td class="text-end">{{ number_format($grouped->sum('total_lahan'),2) }}</td>
            <td class="text-end">{{ number_format($grouped->sum('total_tanam'),2) }}</td>
            <td class="text-end">{{ number_format($grouped->sum('total_panen'),2) }}</td>
            <td class="text-end">{{ number_format($grouped->sum('total_produksi'),2) }}</td>
            <td></td>
        </tr>
    </tbody>
</table>
<p class="footer">SIM-Distan Kab. Muna Barat • Laporan ini dibuat secara otomatis oleh sistem.</p>
</body>
</html>
