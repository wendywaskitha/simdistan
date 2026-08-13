<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap LTT - {{ $komoditas->nama }} (Tahun {{ $tahun }})</title>
    <style>
        @page {
            size: a4 landscape;
            margin: 20px;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            background-color: #fff;
            color: #000;
            margin: 0;
            padding: 0;
            font-size: 11px;
        }
        .header-title {
            text-align: center;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .header-title div {
            margin: 3px 0;
        }
        .title-large {
            font-size: 14px;
        }
        .title-medium {
            font-size: 12px;
        }
        .divider {
            border-top: 2px solid #000;
            border-bottom: 1px solid #000;
            height: 3px;
            margin-bottom: 15px;
        }
        .table-title {
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 15px;
            margin-bottom: 5px;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px 3px;
            text-align: center;
            vertical-align: middle;
            font-size: 10px;
        }
        th {
            background-color: #e9e9e9;
            font-weight: bold;
        }
        .text-start {
            text-align: left;
            font-weight: bold;
        }
        .text-end {
            text-align: right;
        }
        .fw-bold {
            font-weight: bold;
        }
        .bg-light {
            background-color: #f5f5f5;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header-title">
        <div class="title-medium">TARGET & REALISASI LUAS TAMBAH TANAM (LTT) KOMODITAS {{ $komoditas->nama }}</div>
        <div class="title-medium">TAHUN {{ $tahun }}</div>
        <div class="title-large">DINAS PERTANIAN KABUPATEN MUNA BARAT</div>
    </div>
    <div class="divider"></div>

    <!-- 1. TABEL TARGET TANAM -->
    <div>
        <div class="table-title">I. TARGET TANAM (Ha)</div>
        <table>
            <thead>
                <tr>
                    <th width="3%">No</th>
                    <th width="15%" class="text-start">Nama Kecamatan</th>
                    @foreach(['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'] as $mName)
                        <th>{{ $mName }}</th>
                    @endforeach
                    <th width="8%">Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $colTotalsTarget = array_fill(1, 12, 0);
                    $grandTotalTarget = 0;
                @endphp
                @foreach($kecamatanListObj as $index => $kec)
                    @php $rowTotal = 0; @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="text-start">{{ $kec->nama }}</td>
                        @for($m = 1; $m <= 12; $m++)
                            @php
                                $val = $targetMap[$kec->id][$m] ?? 0;
                                $rowTotal += $val;
                                $colTotalsTarget[$m] += $val;
                            @endphp
                            <td class="text-end">{{ $val > 0 ? number_format($val, 2, ',', '.') : '-' }}</td>
                        @endfor
                        @php $grandTotalTarget += $rowTotal; @endphp
                        <td class="text-end fw-bold">{{ number_format($rowTotal, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr class="fw-bold bg-light">
                    <td></td>
                    <td class="text-start">Total</td>
                    @for($m = 1; $m <= 12; $m++)
                        <td class="text-end">{{ number_format($colTotalsTarget[$m], 2, ',', '.') }}</td>
                    @endfor
                    <td class="text-end">{{ number_format($grandTotalTarget, 2, ',', '.') }}</td>
                </tr>
                <tr class="fw-bold bg-light">
                    <td></td>
                    <td class="text-start">Triwulan</td>
                    <td colspan="3">TW1: {{ number_format($colTotalsTarget[1]+$colTotalsTarget[2]+$colTotalsTarget[3], 2, ',', '.') }}</td>
                    <td colspan="3">TW2: {{ number_format($colTotalsTarget[4]+$colTotalsTarget[5]+$colTotalsTarget[6], 2, ',', '.') }}</td>
                    <td colspan="3">TW3: {{ number_format($colTotalsTarget[7]+$colTotalsTarget[8]+$colTotalsTarget[9], 2, ',', '.') }}</td>
                    <td colspan="3">TW4: {{ number_format($colTotalsTarget[10]+$colTotalsTarget[11]+$colTotalsTarget[12], 2, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>

    <!-- 2. TABEL LUAS TANAM -->
    <div>
        <div class="table-title">II. LUAS TANAM (REALISASI) (Ha)</div>
        <table>
            <thead>
                <tr>
                    <th width="3%">No</th>
                    <th width="15%" class="text-start">Nama Kecamatan</th>
                    @foreach(['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'] as $mName)
                        <th>{{ $mName }}</th>
                    @endforeach
                    <th width="8%">Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $colTotalsTanam = array_fill(1, 12, 0);
                    $grandTotalTanam = 0;
                @endphp
                @foreach($kecamatanListObj as $index => $kec)
                    @php $rowTotal = 0; @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="text-start">{{ $kec->nama }}</td>
                        @for($m = 1; $m <= 12; $m++)
                            @php
                                $val = $tanamMap[$kec->id][$m] ?? 0;
                                $rowTotal += $val;
                                $colTotalsTanam[$m] += $val;
                            @endphp
                            <td class="text-end">{{ $val > 0 ? number_format($val, 2, ',', '.') : '-' }}</td>
                        @endfor
                        @php $grandTotalTanam += $rowTotal; @endphp
                        <td class="text-end fw-bold">{{ number_format($rowTotal, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr class="fw-bold bg-light">
                    <td></td>
                    <td class="text-start">Total</td>
                    @for($m = 1; $m <= 12; $m++)
                        <td class="text-end">{{ number_format($colTotalsTanam[$m], 2, ',', '.') }}</td>
                    @endfor
                    <td class="text-end">{{ number_format($grandTotalTanam, 2, ',', '.') }}</td>
                </tr>
                <tr class="fw-bold bg-light">
                    <td></td>
                    <td class="text-start">Triwulan</td>
                    <td colspan="3">TW1: {{ number_format($colTotalsTanam[1]+$colTotalsTanam[2]+$colTotalsTanam[3], 2, ',', '.') }}</td>
                    <td colspan="3">TW2: {{ number_format($colTotalsTanam[4]+$colTotalsTanam[5]+$colTotalsTanam[6], 2, ',', '.') }}</td>
                    <td colspan="3">TW3: {{ number_format($colTotalsTanam[7]+$colTotalsTanam[8]+$colTotalsTanam[9], 2, ',', '.') }}</td>
                    <td colspan="3">TW4: {{ number_format($colTotalsTanam[10]+$colTotalsTanam[11]+$colTotalsTanam[12], 2, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>

    <!-- 3. TABEL LUAS PANEN -->
    <div>
        <div class="table-title">III. LUAS PANEN (REALISASI) (Ha)</div>
        <table>
            <thead>
                <tr>
                    <th width="3%">No</th>
                    <th width="15%" class="text-start">Nama Kecamatan</th>
                    @foreach(['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'] as $mName)
                        <th>{{ $mName }}</th>
                    @endforeach
                    <th width="8%">Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $colTotalsPanen = array_fill(1, 12, 0);
                    $grandTotalPanen = 0;
                @endphp
                @foreach($kecamatanListObj as $index => $kec)
                    @php $rowTotal = 0; @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="text-start">{{ $kec->nama }}</td>
                        @for($m = 1; $m <= 12; $m++)
                            @php
                                $val = $panenMap[$kec->id][$m] ?? 0;
                                $rowTotal += $val;
                                $colTotalsPanen[$m] += $val;
                            @endphp
                            <td class="text-end">{{ $val > 0 ? number_format($val, 2, ',', '.') : '-' }}</td>
                        @endfor
                        @php $grandTotalPanen += $rowTotal; @endphp
                        <td class="text-end fw-bold">{{ number_format($rowTotal, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr class="fw-bold bg-light">
                    <td></td>
                    <td class="text-start">Total</td>
                    @for($m = 1; $m <= 12; $m++)
                        <td class="text-end">{{ number_format($colTotalsPanen[$m], 2, ',', '.') }}</td>
                    @endfor
                    <td class="text-end">{{ number_format($grandTotalPanen, 2, ',', '.') }}</td>
                </tr>
                <tr class="fw-bold bg-light">
                    <td></td>
                    <td class="text-start">Triwulan</td>
                    <td colspan="3">TW1: {{ number_format($colTotalsPanen[1]+$colTotalsPanen[2]+$colTotalsPanen[3], 2, ',', '.') }}</td>
                    <td colspan="3">TW2: {{ number_format($colTotalsPanen[4]+$colTotalsPanen[5]+$colTotalsPanen[6], 2, ',', '.') }}</td>
                    <td colspan="3">TW3: {{ number_format($colTotalsPanen[7]+$colTotalsPanen[8]+$colTotalsPanen[9], 2, ',', '.') }}</td>
                    <td colspan="3">TW4: {{ number_format($colTotalsPanen[10]+$colTotalsPanen[11]+$colTotalsPanen[12], 2, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
