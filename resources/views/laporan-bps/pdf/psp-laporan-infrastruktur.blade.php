<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 8px; margin: 20px; color: #1e293b; }
    h2 { font-size: 13px; text-align: center; margin-bottom: 2px; }
    .subtitle { text-align: center; font-size: 9px; color: #64748b; margin-bottom: 12px; }
    table { width: 100%; border-collapse: collapse; margin-top: 8px; margin-bottom: 16px; }
    th { background: #f8f9fa; color: #1e293b; padding: 5px 6px; text-align: center; font-size: 8px; border: 1px solid #ddd; }
    td { padding: 4px 6px; border: 1px solid #ddd; }
    .text-end { text-align: right; }
    .text-center { text-align: center; }
    .group-header { font-weight: bold; font-size: 9px; margin-top: 12px; margin-bottom: 4px; }
    .section-title { font-weight: bold; font-size: 10px; margin-top: 16px; margin-bottom: 8px; border-bottom: 1px solid #1e293b; padding-bottom: 2px; }
    .footer { margin-top: 24px; font-size: 8px; text-align: right; color: #94a3b8; }
</style>
</head>
<body>
<h2>LAPORAN PSP — KONDISI DAN PEMELIHARAAN INFRASTRUKTUR</h2>
<p class="subtitle">Kabupaten Muna Barat — Tahun {{ $tahun }} | Dicetak: {{ date('d/m/Y H:i') }}</p>

@php
    $byProyek = $infrastrukturLaporans->groupBy(function($lap) {
        return ($lap->infrastruktur?->nama_proyek ?? 'Proyek') . ' (' . ($lap->infrastruktur?->jenis_infrastruktur ?? '-') . ')';
    });
@endphp

@forelse($byProyek as $namaProyek => $items)
    <div class="group-header">Proyek: {{ $namaProyek }}</div>
    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Detail Proyek & Kelompok Tani</th>
                <th>Wilayah & Koordinat</th>
                <th>Dimensi & Anggaran</th>
                <th>Riwayat Kondisi & Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $i => $lap)
            <tr>
                @if($loop->first)
                    <td class="text-center" rowspan="{{ $items->count() }}">{{ $loop->parent->iteration }}</td>
                    <td rowspan="{{ $items->count() }}">
                        <div style="font-weight: bold;">{{ $lap->infrastruktur?->nama_proyek ?? '-' }}</div>
                        <div style="color: #64748b; font-size: 7px;">Jenis: {{ $lap->infrastruktur?->jenis_infrastruktur ?? '-' }}</div>
                        <div style="color: #64748b; font-size: 7px;">Poktan: {{ $lap->infrastruktur?->kelompokTani?->nama ?? '-' }}</div>
                    </td>
                    <td rowspan="{{ $items->count() }}">
                        <div>Kec. {{ $lap->infrastruktur?->kecamatan?->nama ?? '-' }}</div>
                        <div style="color: #64748b; font-size: 7px;">Desa: {{ $lap->infrastruktur?->desa?->nama ?? '-' }}</div>
                        @if($lap->infrastruktur?->latitude && $lap->infrastruktur?->longitude)
                            <div style="color: #64748b; font-size: 7px;">Koord: {{ $lap->infrastruktur->latitude }}, {{ $lap->infrastruktur->longitude }}</div>
                        @endif
                    </td>
                    <td rowspan="{{ $items->count() }}">
                        <div>Volume: {{ $lap->infrastruktur?->volume ?? '-' }} {{ $lap->infrastruktur?->satuan ?? '' }}</div>
                        <div style="color: #64748b; font-size: 7px;">Anggaran: Rp {{ number_format($lap->infrastruktur?->nilai_anggaran ?? 0, 0, ',', '.') }}</div>
                        <div style="color: #64748b; font-size: 7px;">Sumber: {{ $lap->infrastruktur?->sumber_dana ?? '-' }} (TA: {{ $lap->infrastruktur?->tahun_anggaran ?? '-' }})</div>
                    </td>
                @endif
                <td>
                    <div>Tanggal Lapor: {{ \Carbon\Carbon::parse($lap->tanggal_laporan)->format('d-m-Y') }}</div>
                    <div style="color: #64748b; font-size: 7px;">Kondisi: <span style="font-weight: bold;">{{ $lap->kondisi }}</span> (Progres: {{ $lap->progres_fisik }}%)</div>
                    <div style="color: #64748b; font-size: 7px;">Catatan: {{ $lap->keterangan ?? '-' }}</div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
@empty
    <p>Tidak ada data laporan kondisi proyek.</p>
@endforelse

<p class="footer">SIM-Distan Kab. Muna Barat</p>
</body></html>
