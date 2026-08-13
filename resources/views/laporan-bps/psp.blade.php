@extends('layouts.admin')
@section('title', 'Laporan – PSP')

@section('content')
<x-breadcrumb :items="[['label'=>'Laporan','url'=>route('laporan-bps.index')],['label'=>'PSP']]" />

<div class="card custom-card border-0 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1"><i class="bi bi-truck-flatbed me-2 text-purple"></i>Laporan — Prasarana &amp; Sarana (PSP) {{ $tahun }}</h5>
            <p class="text-muted small mb-0">Alsintan, Infrastruktur & Irigasi, Distribusi Pupuk.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('laporan-bps.psp.excel', array_merge(request()->query(),['tab'=>$tab])) }}" class="btn btn-success rounded-3 px-3">
                <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
            </a>
            <a href="{{ route('laporan-bps.psp.pdf', array_merge(request()->query(),['tab'=>$tab])) }}" class="btn btn-danger rounded-3 px-3" target="_blank">
                <i class="bi bi-file-earmark-pdf me-1"></i> Cetak PDF
            </a>
        </div>
    </div>

    {{-- Filter --}}
    <form method="GET" class="row g-3 mb-4 bg-light rounded-3 p-3 border border-light-subtle align-items-end">
        <input type="hidden" name="tab" value="{{ $tab }}">
        <div class="col-md-2">
            <label class="form-label fw-semibold text-secondary small">Tahun</label>
            <select name="tahun" class="form-select rounded-3 shadow-sm border-0" onchange="this.form.submit()">
                @foreach($years as $y)
                    <option value="{{ $y }}" {{ $y == $tahun ? 'selected':'' }}>{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-semibold text-secondary small">Kecamatan</label>
            <select name="kecamatan_id" class="form-select rounded-3 shadow-sm border-0" onchange="this.form.submit()">
                <option value="">— Semua Kecamatan —</option>
                @foreach($kecamatans as $kec)
                    <option value="{{ $kec->id }}" {{ $kecamatanId == $kec->id ? 'selected':'' }}>{{ $kec->nama }}</option>
                @endforeach
            </select>
        </div>
    </form>

    {{-- Nav Tabs --}}
    <ul class="nav nav-tabs mb-4">
        @foreach([
            'alsintan'=>'Bantuan Alsintan',
            'pemanfaatan'=>'Laporan Pemanfaatan Alsintan',
            'realokasi-alsintan'=>'Realokasi Alsintan',
            'infrastruktur'=>'Infrastruktur & Irigasi',
            'laporan-infrastruktur'=>'Laporan Kondisi Infrastruktur',
            'pupuk'=>'Distribusi Pupuk',
            'realokasi-pupuk'=>'Realokasi Pupuk'
        ] as $key => $label)
        <li class="nav-item">
            <a class="nav-link fw-semibold {{ $tab === $key ? 'active text-success fw-bold' : 'text-secondary' }}"
               href="{{ route('laporan-bps.psp', array_merge(request()->query(), ['tab'=>$key])) }}">
                @if($key==='alsintan')<i class="bi bi-truck-flatbed me-1"></i>
                @elseif($key==='infrastruktur')<i class="bi bi-water me-1"></i>
                @elseif($key==='laporan-infrastruktur')<i class="bi bi-chat-left-text me-1"></i>
                @elseif($key==='realokasi-alsintan')<i class="bi bi-arrow-left-right me-1"></i>
                @elseif($key==='realokasi-pupuk')<i class="bi bi-arrow-repeat me-1"></i>
                @elseif($key==='pemanfaatan')<i class="bi bi-activity me-1"></i>
                @else<i class="bi bi-droplet-half me-1"></i>@endif
                {{ $label }}
            </a>
        </li>
        @endforeach
    </ul>

    {{-- ─── ALSINTAN ─── --}}
    @if($tab === 'alsintan')
        @if($alsintans->isEmpty())
            <div class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2"></i>Tidak ada data.</div>
        @else
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle small">
                <thead class="table-light fw-bold text-center">
                    <tr>
                        <th>No</th><th>Kelompok Tani</th><th>Kecamatan</th><th>Jenis Alat</th>
                        <th>Nama Alat</th><th>Merek</th><th>Kondisi</th><th>Sumber Dana</th><th>Tahun Bantuan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($alsintans as $i => $als)
                    <tr>
                        <td class="text-center">{{ $i+1 }}</td>
                        <td>{{ $als->kelompokTani?->nama ?? '-' }}</td>
                        <td>{{ $als->kelompokTani?->desa?->kecamatan?->nama ?? '-' }}</td>
                        <td>{{ $als->jenisAlat?->nama ?? '-' }}</td>
                        <td class="fw-semibold">{{ $als->nama_alat ?? '-' }}</td>
                        <td>{{ $als->merek ?? '-' }}</td>
                        <td class="text-center">
                            @php $kondisi = $als->kondisi ?? '-'; @endphp
                            <span class="badge rounded-pill {{ $kondisi==='Baik'?'bg-success-subtle text-success':($kondisi==='Rusak Berat'?'bg-danger-subtle text-danger':'bg-warning-subtle text-warning') }}">
                                {{ $kondisi }}
                            </span>
                        </td>
                        <td>{{ $als->sumber_dana ?? '-' }}</td>
                        <td class="text-center">{{ $als->tahun_bantuan }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-dark fw-bold"><tr><td colspan="9">Total: {{ $alsintans->count() }} unit alsintan</td></tr></tfoot>
            </table>
        </div>
        @endif

    {{-- ─── INFRASTRUKTUR ─── --}}
    @elseif($tab === 'infrastruktur')
        @if($infrastrukturs->isEmpty())
            <div class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2"></i>Tidak ada data.</div>
        @else
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle small">
                <thead class="table-light fw-bold text-center">
                    <tr>
                        <th>No</th><th>Nama Proyek</th><th>Jenis</th><th>Kecamatan</th><th>Desa</th>
                        <th>Volume</th><th>Satuan</th><th>Nilai Anggaran (Rp)</th><th>Sumber Dana</th><th>Tahun</th><th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($infrastrukturs as $i => $inf)
                    <tr>
                        <td class="text-center">{{ $i+1 }}</td>
                        <td class="fw-semibold">{{ $inf->nama_proyek }}</td>
                        <td>{{ $inf->jenis_infrastruktur }}</td>
                        <td>{{ $inf->kecamatan?->nama ?? '-' }}</td>
                        <td>{{ $inf->desa?->nama ?? '-' }}</td>
                        <td class="text-end">{{ number_format($inf->volume, 0) }}</td>
                        <td>{{ $inf->satuan }}</td>
                        <td class="text-end">{{ number_format($inf->nilai_anggaran, 0, ',', '.') }}</td>
                        <td>{{ $inf->sumber_dana }}</td>
                        <td class="text-center">{{ $inf->tahun_anggaran }}</td>
                        <td class="text-center">
                            <span class="badge {{ $inf->status_pembangunan==='Selesai'?'bg-success':'bg-warning text-dark' }} rounded-pill">
                                {{ $inf->status_pembangunan }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-dark fw-bold">
                    <tr>
                        <td colspan="7" class="text-end">Total Nilai Anggaran:</td>
                        <td class="text-end">Rp {{ number_format($infrastrukturs->sum('nilai_anggaran'), 0, ',', '.') }}</td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif

    {{-- ─── LAPORAN KONDISI INFRASTRUKTUR ─── --}}
    @elseif($tab === 'laporan-infrastruktur')
        @if($infrastrukturLaporans->isEmpty())
            <div class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2"></i>Tidak ada data laporan kondisi.</div>
        @else
            @php
                $byProyek = $infrastrukturLaporans->groupBy(function($lap) {
                    return ($lap->infrastruktur?->nama_proyek ?? 'Proyek') . ' (' . ($lap->infrastruktur?->jenis_infrastruktur ?? '-') . ')';
                });
            @endphp

            <h6 class="fw-bold mb-3 text-dark">Ringkasan Laporan Kondisi Proyek Infrastruktur</h6>
            @foreach($byProyek as $namaProyek => $items)
                <div class="mb-4">
                    <div class="fw-semibold mb-2 text-dark" style="font-size: 0.95rem;">Proyek: {{ $namaProyek }}</div>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle small" style="border-color: #dee2e6;">
                            <thead>
                                <tr style="background-color: #f8f9fa;">
                                    <th class="text-center" style="width: 50px;">No</th>
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
                                            <div class="fw-semibold">{{ $lap->infrastruktur?->nama_proyek ?? '-' }}</div>
                                            <div class="text-muted small">Jenis: {{ $lap->infrastruktur?->jenis_infrastruktur ?? '-' }}</div>
                                            <div class="text-muted small">Poktan: {{ $lap->infrastruktur?->kelompokTani?->nama ?? '-' }}</div>
                                        </td>
                                        <td rowspan="{{ $items->count() }}">
                                            <div>Kec. {{ $lap->infrastruktur?->kecamatan?->nama ?? '-' }}</div>
                                            <div class="text-muted small">Desa: {{ $lap->infrastruktur?->desa?->nama ?? '-' }}</div>
                                            @if($lap->infrastruktur?->latitude && $lap->infrastruktur?->longitude)
                                                <div class="text-muted small">Koord: {{ $lap->infrastruktur->latitude }}, {{ $lap->infrastruktur->longitude }}</div>
                                            @endif
                                        </td>
                                        <td rowspan="{{ $items->count() }}">
                                            <div>Volume: {{ $lap->infrastruktur?->volume ?? '-' }} {{ $lap->infrastruktur?->satuan ?? '' }}</div>
                                            <div class="text-muted small">Anggaran: Rp {{ number_format($lap->infrastruktur?->nilai_anggaran ?? 0, 0, ',', '.') }}</div>
                                            <div class="text-muted small">Sumber: {{ $lap->infrastruktur?->sumber_dana ?? '-' }} (TA: {{ $lap->infrastruktur?->tahun_anggaran ?? '-' }})</div>
                                        </td>
                                    @endif
                                    <td>
                                        <div>Tanggal Lapor: {{ \Carbon\Carbon::parse($lap->tanggal_laporan)->format('d-m-Y') }}</div>
                                        <div class="text-muted small">Kondisi: <span class="fw-semibold">{{ $lap->kondisi }}</span> (Progres: {{ $lap->progres_fisik }}%)</div>
                                        <div class="text-muted small">Catatan: {{ $lap->keterangan ?? '-' }}</div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        @endif

    {{-- ─── PUPUK ─── --}}
    @elseif($tab === 'pupuk')
        @if($pupukData->isEmpty())
            <div class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2"></i>Tidak ada data.</div>
        @else
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle small">
                <thead class="table-light fw-bold text-center">
                    <tr>
                        <th>No</th><th>Kecamatan</th><th>Jenis Pupuk</th>
                        <th>Kuota (Kg)</th><th>Realisasi (Kg)</th><th>Selisih (Kg)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pupukData as $i => $row)
                    <tr>
                        <td class="text-center">{{ $i+1 }}</td>
                        <td>{{ $row['kecamatan']->nama }}</td>
                        <td class="fw-semibold">{{ $row['jenis']->nama }}</td>
                        <td class="text-end">{{ number_format($row['kuota'], 2) }}</td>
                        <td class="text-end">{{ number_format($row['realisasi'], 2) }}</td>
                        <td class="text-end {{ $row['selisih'] < 0 ? 'text-danger fw-bold' : 'text-success' }}">
                            {{ number_format($row['selisih'], 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-dark fw-bold">
                    <tr>
                        <td colspan="3" class="text-end">TOTAL</td>
                        <td class="text-end">{{ number_format($pupukData->sum('kuota'), 2) }}</td>
                        <td class="text-end">{{ number_format($pupukData->sum('realisasi'), 2) }}</td>
                        <td class="text-end">{{ number_format($pupukData->sum('selisih'), 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif

    {{-- ─── REALOKASI ALSINTAN ─── --}}
    @elseif($tab === 'realokasi-alsintan')
        @if($realokasiAlsintans->isEmpty())
            <div class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2"></i>Tidak ada data realokasi alsintan.</div>
        @else
        <div class="table-responsive">
            <table class="table table-bordered align-middle small" style="border-color: #dee2e6;">
                <thead>
                    <tr style="background-color: #f8f9fa;">
                        <th class="text-center" style="width: 50px;">No</th>
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
                    @foreach($realokasiAlsintans as $i => $row)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>{{ $row->alsintan?->nama_alat ?? '-' }} ({{ $row->alsintan?->jenisAlat?->nama ?? '-' }})</td>
                        <td>{{ $row->kelompokTaniAsal?->nama ?? '-' }}</td>
                        <td>{{ $row->kelompokTaniAsal?->desa?->kecamatan?->nama ?? '-' }}</td>
                        <td>{{ $row->kelompokTaniTujuan?->nama ?? '-' }}</td>
                        <td>{{ $row->kelompokTaniTujuan?->desa?->kecamatan?->nama ?? '-' }}</td>
                        <td>{{ $row->tanggal_realokasi ? $row->tanggal_realokasi->format('d/m/Y') : '-' }}</td>
                        <td>{{ $row->keterangan ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

    {{-- ─── REALOKASI PUPUK ─── --}}
    @elseif($tab === 'realokasi-pupuk')
        @if($realokasiPupuks->isEmpty())
            <div class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2"></i>Tidak ada data realokasi pupuk.</div>
        @else
        <div class="table-responsive">
            <table class="table table-bordered align-middle small" style="border-color: #dee2e6;">
                <thead>
                    <tr style="background-color: #f8f9fa;">
                        <th class="text-center" style="width: 50px;">No</th>
                        <th>Jenis Pupuk</th>
                        <th>Kecamatan Asal</th>
                        <th>Kecamatan Tujuan</th>
                        <th class="text-end">Jumlah (Kg)</th>
                        <th>Bulan / Tahun</th>
                        <th>Nama SK / Dokumen</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $bulans = [
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
                            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                        ];
                    @endphp
                    @foreach($realokasiPupuks as $i => $row)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td class="fw-semibold">{{ $row->jenis?->nama ?? '-' }}</td>
                        <td>{{ $row->kecamatanAsal?->nama ?? '-' }}</td>
                        <td>{{ $row->kecamatanTujuan?->nama ?? '-' }}</td>
                        <td class="text-end">{{ number_format($row->jumlah, 2, ',', '.') }} Kg</td>
                        <td>{{ isset($bulans[$row->bulan]) ? $bulans[$row->bulan] : '-' }} {{ $row->tahun }}</td>
                        <td>
                            @if($row->file_path)
                                <a href="{{ asset('storage/' . $row->file_path) }}" target="_blank">{{ $row->nama_sk ?? 'Dokumen SK' }}</a>
                            @else
                                {{ $row->nama_sk ?? '-' }}
                            @endif
                        </td>
                        <td>{{ $row->keterangan ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

    {{-- ─── PEMANFAATAN ALSINTAN ─── --}}
    @elseif($tab === 'pemanfaatan')
        @if($pemanfaatanLaporans->isEmpty())
            <div class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2"></i>Tidak ada data pemanfaatan.</div>
        @else
            @php
                $byKelompok = $pemanfaatanLaporans->groupBy(function($lap) {
                    return $lap->alsintan->kelompokTani?->nama ?? 'Tidak Ada Kelompok Tani';
                });

                $byAlat = $pemanfaatanLaporans->groupBy(function($lap) {
                    return ($lap->alsintan->jenisAlat?->nama ?? 'Alat') . ' - ' . ($lap->alsintan->nama_alat ?? '');
                });
            @endphp

            <h6 class="fw-bold mb-3 text-dark">A. Ringkasan Pemanfaatan Per Kelompok Tani</h6>
            @foreach($byKelompok as $namaKelompok => $items)
                <div class="mb-4">
                    <div class="fw-semibold mb-2 text-dark" style="font-size: 0.95rem;">Kelompok Tani: {{ $namaKelompok }}</div>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle small" style="border-color: #dee2e6;">
                            <thead>
                                <tr style="background-color: #f8f9fa;">
                                    <th class="text-center" style="width: 50px;">No</th>
                                    <th>Alat Alsintan</th>
                                    <th>Kecamatan</th>
                                    <th>Waktu Pemanfaatan / Tanggal</th>
                                    <th class="text-end">Luas Lahan (Ha)</th>
                                    <th class="text-center">Durasi Kerja (Jam)</th>
                                    <th class="text-end">Biaya Pengolahan (Rp)</th>
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
                            </tbody>
                            <tfoot>
                                <tr style="background-color: #ffffff; font-weight: bold;">
                                    <td colspan="4" class="text-end">Total</td>
                                    <td class="text-end">{{ number_format($items->sum('luas_lahan'), 2, ',', '.') }} Ha</td>
                                    <td class="text-center">{{ $items->sum('waktu_pengerjaan') }} Jam</td>
                                    <td class="text-end">Rp {{ number_format($items->sum('biaya_pengolahan'), 2, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @endforeach

            <hr class="my-4" style="border-color: #dee2e6;">

            <h6 class="fw-bold mb-3 text-dark">B. Ringkasan Pemanfaatan Per Alat Alsintan</h6>
            @foreach($byAlat as $namaAlat => $items)
                <div class="mb-4">
                    <div class="fw-semibold mb-2 text-dark" style="font-size: 0.95rem;">Alat: {{ $namaAlat }}</div>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle small" style="border-color: #dee2e6;">
                            <thead>
                                <tr style="background-color: #f8f9fa;">
                                    <th class="text-center" style="width: 50px;">No</th>
                                    <th>Kelompok Tani Pengguna</th>
                                    <th>Kecamatan</th>
                                    <th>Waktu Pemanfaatan / Tanggal</th>
                                    <th class="text-end">Luas Lahan (Ha)</th>
                                    <th class="text-center">Durasi Kerja (Jam)</th>
                                    <th class="text-end">Biaya Pengolahan (Rp)</th>
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
                            </tbody>
                            <tfoot>
                                <tr style="background-color: #ffffff; font-weight: bold;">
                                    <td colspan="4" class="text-end">Total</td>
                                    <td class="text-end">{{ number_format($items->sum('luas_lahan'), 2, ',', '.') }} Ha</td>
                                    <td class="text-center">{{ $items->sum('waktu_pengerjaan') }} Jam</td>
                                    <td class="text-end">Rp {{ number_format($items->sum('biaya_pengolahan'), 2, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @endforeach
        @endif
    @endif
</div>
@endsection
