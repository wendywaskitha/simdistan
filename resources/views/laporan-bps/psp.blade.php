@extends('layouts.admin')
@section('title', 'Laporan BPS – PSP')

@section('content')
<x-breadcrumb :items="[['label'=>'Laporan BPS','url'=>route('laporan-bps.index')],['label'=>'PSP']]" />

<div class="card custom-card border-0 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1"><i class="bi bi-truck-flatbed me-2 text-purple"></i>Laporan BPS — Prasarana & Sarana (PSP) {{ $tahun }}</h5>
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
        @foreach(['alsintan'=>'Bantuan Alsintan','infrastruktur'=>'Infrastruktur & Irigasi','pupuk'=>'Distribusi Pupuk'] as $key => $label)
        <li class="nav-item">
            <a class="nav-link fw-semibold {{ $tab === $key ? 'active text-success fw-bold' : 'text-secondary' }}"
               href="{{ route('laporan-bps.psp', array_merge(request()->query(), ['tab'=>$key])) }}">
                @if($key==='alsintan')<i class="bi bi-truck-flatbed me-1"></i>
                @elseif($key==='infrastruktur')<i class="bi bi-water me-1"></i>
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

    {{-- ─── PUPUK ─── --}}
    @else
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
    @endif
</div>
@endsection
