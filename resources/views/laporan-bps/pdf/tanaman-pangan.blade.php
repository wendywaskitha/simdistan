<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    @page { size: a4 landscape; margin: 20mm 15mm 20mm 20mm; }
    * { box-sizing: border-box; }
    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 9px;
        color: #000;
        margin: 0;
    }

    /* ─── KOP SURAT ─── */
    .kop { text-align: center; border-bottom: 2px solid #000; padding-bottom: 8px; margin-bottom: 12px; }
    .kop .instansi  { font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
    .kop .alamat    { font-size: 8px; margin-top: 2px; }
    .kop .judul     { font-size: 12px; font-weight: bold; text-transform: uppercase; margin-top: 6px; letter-spacing: 1px; }
    .kop .sub-judul { font-size: 9px; margin-top: 2px; }
    .kop .periode   { font-size: 8px; margin-top: 2px; font-style: italic; }

    /* ─── TABEL ─── */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 8px;
        margin-bottom: 16px;
        page-break-inside: auto;
    }
    tr { page-break-inside: avoid; page-break-after: auto; }
    thead { display: table-header-group; }

    th {
        border: 0.5px solid #000;
        padding: 4px 5px;
        text-align: center;
        font-size: 8px;
        font-weight: bold;
        background: none;
    }
    td {
        border: 0.5px solid #000;
        padding: 3px 5px;
        font-size: 8px;
    }

    .text-right  { text-align: right; }
    .text-center { text-align: center; }
    .text-left   { text-align: left; }

    /* baris sub-header kecamatan */
    .row-kec td {
        font-weight: bold;
        background: none;
        border-top: 1px solid #000;
        border-bottom: 1px solid #000;
        font-size: 8px;
        padding: 3px 5px;
    }
    /* baris sub-total */
    .row-subtotal td {
        font-weight: bold;
        font-size: 8px;
        border-top: 0.5px solid #555;
    }
    /* baris grand total */
    .row-grandtotal td {
        font-weight: bold;
        font-size: 8px;
        border-top: 1.5px solid #000;
        border-bottom: 1.5px solid #000;
    }

    /* ─── JUDUL SEKSI ─── */
    .section-title {
        font-size: 9px;
        font-weight: bold;
        text-transform: uppercase;
        margin-bottom: 4px;
        margin-top: 10px;
        letter-spacing: 0.3px;
    }

    /* ─── FOOTER ─── */
    .footer {
        position: fixed;
        bottom: 0;
        left: 0; right: 0;
        border-top: 0.5px solid #000;
        padding-top: 4px;
        font-size: 7.5px;
        display: flex;
        justify-content: space-between;
    }

    /* ─── TT TANGAN ─── */
    .ttd-box {
        width: 100%;
        margin-top: 24px;
    }
    .ttd-col {
        display: inline-block;
        width: 33%;
        vertical-align: top;
        text-align: center;
        font-size: 8px;
    }
    .ttd-col .ttd-space { height: 48px; }
    .ttd-col .ttd-nama  { border-top: 0.5px solid #000; padding-top: 2px; font-weight: bold; }
</style>
</head>
<body>

{{-- ─── KOP ─── --}}
<div class="kop">
    <div class="instansi">Pemerintah Kabupaten Muna Barat</div>
    <div class="alamat">Dinas Pertanian dan Ketahanan Pangan</div>
    <div class="judul">Laporan Produksi Tanaman Pangan</div>
    <div class="sub-judul">
        Kabupaten Muna Barat
        @if(isset($kecamatanNama) && $kecamatanNama)
            &nbsp;&mdash;&nbsp;Kecamatan {{ $kecamatanNama }}
        @endif
    </div>
    <div class="periode">Periode Tahun {{ $tahun }} | Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB</div>
</div>

{{-- ─── TABEL I: RINGKASAN PER KOMODITAS ─── --}}
@php
    // Agregasi total per komoditas dari semua kecamatan
    $ringkasan = collect();
    foreach ($grouped as $group) {
        foreach ($group['rows'] as $row) {
            $kid = $row['komoditas']->id;
            if (!$ringkasan->has($kid)) {
                $ringkasan->put($kid, [
                    'komoditas'    => $row['komoditas'],
                    'luas_lahan'   => 0,
                    'luas_tanam'   => 0,
                    'luas_panen'   => 0,
                    'produksi'     => 0,
                ]);
            }
            $r = $ringkasan->get($kid);
            $r['luas_lahan']  += $row['luas_lahan'];
            $r['luas_tanam']  += $row['luas_tanam'];
            $r['luas_panen']  += $row['luas_panen'];
            $r['produksi']    += $row['produksi'];
            $ringkasan->put($kid, $r);
        }
    }
    $ringkasan = $ringkasan->values();
@endphp

<div class="section-title">Tabel I. Ringkasan Produksi Tanaman Pangan Per Komoditas (Ha / Ton)</div>
<table>
    <thead>
        <tr>
            <th width="4%" rowspan="2">No</th>
            <th width="22%" rowspan="2">Komoditas</th>
            <th colspan="3">Luas (Ha)</th>
            <th rowspan="2">Produksi (Ton)</th>
            <th rowspan="2">Produktivitas (Ton/Ha)</th>
        </tr>
        <tr>
            <th>Lahan Baku</th>
            <th>Tanam</th>
            <th>Panen</th>
        </tr>
    </thead>
    <tbody>
        @php $no = 1; $gtLahan=0; $gtTanam=0; $gtPanen=0; $gtProduksi=0; @endphp
        @foreach($ringkasan as $row)
        @php
            $prod = $row['produksi'];
            $panen = $row['luas_panen'];
            $produktivitas = $panen > 0 ? $prod / $panen : 0;
            $gtLahan   += $row['luas_lahan'];
            $gtTanam   += $row['luas_tanam'];
            $gtPanen   += $row['luas_panen'];
            $gtProduksi+= $row['produksi'];
        @endphp
        <tr>
            <td class="text-center">{{ $no++ }}</td>
            <td>{{ $row['komoditas']->nama }}</td>
            <td class="text-right">{{ number_format($row['luas_lahan'],2,',','.') }}</td>
            <td class="text-right">{{ number_format($row['luas_tanam'],2,',','.') }}</td>
            <td class="text-right">{{ number_format($row['luas_panen'],2,',','.') }}</td>
            <td class="text-right">{{ number_format($prod,2,',','.') }}</td>
            <td class="text-right">{{ number_format($produktivitas,4,',','.') }}</td>
        </tr>
        @endforeach
        <tr class="row-grandtotal">
            <td colspan="2" class="text-center">J U M L A H</td>
            <td class="text-right">{{ number_format($gtLahan,2,',','.') }}</td>
            <td class="text-right">{{ number_format($gtTanam,2,',','.') }}</td>
            <td class="text-right">{{ number_format($gtPanen,2,',','.') }}</td>
            <td class="text-right">{{ number_format($gtProduksi,2,',','.') }}</td>
            <td></td>
        </tr>
    </tbody>
</table>

{{-- ─── TABEL II: DETAIL PER KECAMATAN ─── --}}
<div class="section-title">Tabel II. Rincian Produksi Tanaman Pangan Per Kecamatan dan Komoditas (Ha / Ton)</div>
<table>
    <thead>
        <tr>
            <th width="4%" rowspan="2">No</th>
            <th width="22%" rowspan="2">Kecamatan / Komoditas</th>
            <th colspan="3">Luas (Ha)</th>
            <th rowspan="2">Produksi (Ton)</th>
            <th rowspan="2">Produktivitas (Ton/Ha)</th>
        </tr>
        <tr>
            <th>Lahan Baku</th>
            <th>Tanam</th>
            <th>Panen</th>
        </tr>
    </thead>
    <tbody>
        @php
            $no2 = 1;
            $gt2Lahan=0; $gt2Tanam=0; $gt2Panen=0; $gt2Produksi=0;
        @endphp
        @foreach($grouped as $group)
        {{-- Header Kecamatan --}}
        <tr class="row-kec">
            <td colspan="7">Kecamatan {{ $group['kecamatan']->nama }}</td>
        </tr>
        @foreach($group['rows'] as $row)
        @php
            $prod2 = $row['produksi'];
            $panen2 = $row['luas_panen'];
            $produktivitas2 = $panen2 > 0 ? $prod2 / $panen2 : 0;
        @endphp
        <tr>
            <td class="text-center">{{ $no2++ }}</td>
            <td class="text-left" style="padding-left:14px;">{{ $row['komoditas']->nama }}</td>
            <td class="text-right">{{ number_format($row['luas_lahan'],2,',','.') }}</td>
            <td class="text-right">{{ number_format($row['luas_tanam'],2,',','.') }}</td>
            <td class="text-right">{{ number_format($row['luas_panen'],2,',','.') }}</td>
            <td class="text-right">{{ number_format($prod2,2,',','.') }}</td>
            <td class="text-right">{{ number_format($produktivitas2,4,',','.') }}</td>
        </tr>
        @endforeach
        {{-- Sub-total Kecamatan --}}
        <tr class="row-subtotal">
            <td colspan="2" class="text-right">Sub-Total Kec. {{ $group['kecamatan']->nama }}</td>
            <td class="text-right">{{ number_format($group['total_lahan'],2,',','.') }}</td>
            <td class="text-right">{{ number_format($group['total_tanam'],2,',','.') }}</td>
            <td class="text-right">{{ number_format($group['total_panen'],2,',','.') }}</td>
            <td class="text-right">{{ number_format($group['total_produksi'],2,',','.') }}</td>
            <td></td>
        </tr>
        @php
            $gt2Lahan    += $group['total_lahan'];
            $gt2Tanam    += $group['total_tanam'];
            $gt2Panen    += $group['total_panen'];
            $gt2Produksi += $group['total_produksi'];
        @endphp
        @endforeach
        <tr class="row-grandtotal">
            <td colspan="2" class="text-center">G R A N D &nbsp; T O T A L</td>
            <td class="text-right">{{ number_format($gt2Lahan,2,',','.') }}</td>
            <td class="text-right">{{ number_format($gt2Tanam,2,',','.') }}</td>
            <td class="text-right">{{ number_format($gt2Panen,2,',','.') }}</td>
            <td class="text-right">{{ number_format($gt2Produksi,2,',','.') }}</td>
            <td></td>
        </tr>
    </tbody>
</table>

{{-- ─── TTD ─── --}}
<table style="width:100%; border:none; margin-top:20px;">
    <tr>
        <td style="width:50%; border:none;"></td>
        <td style="width:50%; border:none; text-align:center; font-size:8px;">
            Raha, {{ now()->translatedFormat('d F Y') }}<br>
            Kepala Dinas Pertanian dan Ketahanan Pangan<br>
            Kabupaten Muna Barat,<br><br><br><br>
            <strong>( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</strong><br>
            NIP. ................................................
        </td>
    </tr>
</table>

{{-- ─── FOOTER ─── --}}
<div class="footer">
    <span>SIM-Distan Kab. Muna Barat &mdash; Sistem Informasi Manajemen Dinas Pertanian</span>
    <span>Laporan Produksi Tanaman Pangan Tahun {{ $tahun }}</span>
</div>

</body>
</html>
