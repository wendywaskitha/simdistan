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
    .total-row { font-weight: bold; background: #ffffff; }
    .footer { margin-top: 24px; font-size: 8px; text-align: right; color: #94a3b8; }
</style>
</head>
<body>
<h2>LAPORAN PSP — DETAIL PEMANFAATAN ALSINTAN</h2>
<p class="subtitle">Kabupaten Muna Barat — Tahun {{ $tahun }} | Dicetak: {{ date('d/m/Y H:i') }}</p>

@php
    $byKelompok = $pemanfaatanLaporans->groupBy(function($lap) {
        return $lap->alsintan->kelompokTani?->nama ?? 'Tidak Ada Kelompok Tani';
    });

    $byAlat = $pemanfaatanLaporans->groupBy(function($lap) {
        return ($lap->alsintan->jenisAlat?->nama ?? 'Alat') . ' - ' . ($lap->alsintan->nama_alat ?? '');
    });
@endphp

<div class="section-title">A. Ringkasan Pemanfaatan Per Kelompok Tani</div>
@forelse($byKelompok as $namaKelompok => $items)
    <div class="group-header">Kelompok Tani: {{ $namaKelompok }}</div>
    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Alat Alsintan</th>
                <th>Kecamatan</th>
                <th>Waktu Pemanfaatan / Tanggal</th>
                <th class="text-end" style="width: 80px;">Luas Lahan (Ha)</th>
                <th class="text-center" style="width: 80px;">Durasi Kerja (Jam)</th>
                <th class="text-end" style="width: 100px;">Biaya Pengolahan (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $i => $lap)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $lap->alsintan?->nama_alat ?? '-' }} ({{ $lap->alsintan?->jenisAlat?->nama ?? '-' }})</td>
                <td>{{ $lap->alsintan?->kelompokTani?->desa?->kecamatan?->nama ?? '-' }}</td>
                <td>
                    @if($lap->tanggal_mulai && $lap->tanggal_selesai)
                        {{ $lap->tanggal_mulai->format('d/m/Y') }} s/d {{ $lap->tanggal_selesai->format('d/m/Y') }}
                    @else
                        {{ $lap->tanggal ? $lap->tanggal->format('d/m/Y') : '-' }}
                    @endif
                </td>
                <td class="text-end">{{ number_format($lap->luas_lahan, 2, ',', '.') }} Ha</td>
                <td class="text-center">{{ $lap->waktu_pengerjaan }} Jam</td>
                <td class="text-end">Rp {{ number_format($lap->biaya_pengolahan, 2, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4" class="text-end">Total</td>
                <td class="text-end">{{ number_format($items->sum('luas_lahan'), 2, ',', '.') }} Ha</td>
                <td class="text-center">{{ $items->sum('waktu_pengerjaan') }} Jam</td>
                <td class="text-end">Rp {{ number_format($items->sum('biaya_pengolahan'), 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
@empty
    <p>Tidak ada data pemanfaatan.</p>
@endforelse

<div class="section-title" style="page-break-before: always;">B. Ringkasan Pemanfaatan Per Alat Alsintan</div>
@forelse($byAlat as $namaAlat => $items)
    <div class="group-header">Alat: {{ $namaAlat }}</div>
    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Kelompok Tani Pengguna</th>
                <th>Kecamatan</th>
                <th>Waktu Pemanfaatan / Tanggal</th>
                <th class="text-end" style="width: 80px;">Luas Lahan (Ha)</th>
                <th class="text-center" style="width: 80px;">Durasi Kerja (Jam)</th>
                <th class="text-end" style="width: 100px;">Biaya Pengolahan (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $i => $lap)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $lap->alsintan?->kelompokTani?->nama ?? '-' }}</td>
                <td>{{ $lap->alsintan?->kelompokTani?->desa?->kecamatan?->nama ?? '-' }}</td>
                <td>
                    @if($lap->tanggal_mulai && $lap->tanggal_selesai)
                        {{ $lap->tanggal_mulai->format('d/m/Y') }} s/d {{ $lap->tanggal_selesai->format('d/m/Y') }}
                    @else
                        {{ $lap->tanggal ? $lap->tanggal->format('d/m/Y') : '-' }}
                    @endif
                </td>
                <td class="text-end">{{ number_format($lap->luas_lahan, 2, ',', '.') }} Ha</td>
                <td class="text-center">{{ $lap->waktu_pengerjaan }} Jam</td>
                <td class="text-end">Rp {{ number_format($lap->biaya_pengolahan, 2, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4" class="text-end">Total</td>
                <td class="text-end">{{ number_format($items->sum('luas_lahan'), 2, ',', '.') }} Ha</td>
                <td class="text-center">{{ $items->sum('waktu_pengerjaan') }} Jam</td>
                <td class="text-end">Rp {{ number_format($items->sum('biaya_pengolahan'), 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
@empty
    <p>Tidak ada data pemanfaatan.</p>
@endforelse

<p class="footer">SIM-Distan Kab. Muna Barat</p>
</body></html>
