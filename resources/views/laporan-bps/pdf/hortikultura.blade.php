<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 8px; margin: 20px; color: #1e293b; }
    h2 { font-size: 13px; text-align: center; margin-bottom: 2px; }
    .subtitle { text-align: center; font-size: 9px; color: #64748b; margin-bottom: 12px; }
    table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    th { background: #3b82f6; color: #fff; padding: 5px 6px; text-align: center; font-size: 8px; border: 1px solid #ddd; }
    td { padding: 4px 6px; border: 1px solid #ddd; }
    .text-end { text-align: right; }
    .text-center { text-align: center; }
    .total-row { background: #1e293b; color: #fff; font-weight: bold; }
    .footer { margin-top: 24px; font-size: 8px; text-align: right; color: #94a3b8; }
</style>
</head>
<body>
<h2>LAPORAN PRODUKSI HORTIKULTURA</h2>
<p class="subtitle">Kabupaten Muna Barat — Tahun {{ $tahun }}{{ $formType ? ' | Form: '.$formType : '' }} | Dicetak: {{ date('d/m/Y H:i') }}</p>

@php $semesters = [1=>'Sem. I',2=>'Sem. II']; @endphp

<table>
    <thead>
        <tr>
            <th>No</th><th>Kecamatan</th><th>Komoditas</th><th>Form</th><th>Semester</th>
            <th>Luas Tanam Akhir / Jml Pohon</th><th>Luas Panen (Ha)</th><th>Produksi</th><th>Satuan</th>
        </tr>
    </thead>
    <tbody>
        @php $no=1; @endphp
        @foreach($laporans as $lap)
        <tr>
            <td class="text-center">{{ $no++ }}</td>
            <td>{{ $lap->kecamatan?->nama ?? '-' }}</td>
            <td>{{ $lap->komoditas?->nama ?? '-' }}</td>
            <td class="text-center">{{ $lap->form_type ?? '-' }}</td>
            <td class="text-center">{{ $semesters[$lap->bulan] ?? $lap->bulan }}</td>
            <td class="text-end">{{ number_format($lap->luas_tanam_akhir ?? $lap->jumlah_tanaman_menghasilkan ?? 0, 2) }}</td>
            <td class="text-end">{{ number_format($lap->luas_panen ?? 0, 2) }}</td>
            <td class="text-end">{{ number_format($lap->produksi ?? 0, 2) }}</td>
            <td>{{ $lap->satuan?->nama ?? '-' }}</td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="6" class="text-end">TOTAL</td>
            <td class="text-end">{{ number_format($laporans->sum('luas_panen'),2) }}</td>
            <td class="text-end">{{ number_format($laporans->sum('produksi'),2) }}</td>
            <td></td>
        </tr>
    </tbody>
</table>
<p class="footer">SIM-Distan Kab. Muna Barat • Laporan ini dibuat secara otomatis oleh sistem.</p>
</body>
</html>
