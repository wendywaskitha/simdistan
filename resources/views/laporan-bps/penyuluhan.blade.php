@extends('layouts.admin')
@section('title', 'Laporan Penyuluhan')

@section('content')
<x-breadcrumb :items="[['label'=>'Laporan','url'=>route('laporan-bps.index')],['label'=>'Penyuluhan']]" />

<div class="card custom-card border-0 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1"><i class="bi bi-person-workspace me-2 text-teal"></i>Laporan Penyuluhan</h5>
            <p class="text-muted small mb-0">Rekap data Penyuluh, Gapoktan, Kelompok Tani, Petani, dan BPP.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('laporan-bps.penyuluhan.excel', array_merge(request()->query(),['tab'=>$tab])) }}" class="btn btn-success rounded-3 px-3">
                <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
            </a>
            <a href="{{ route('laporan-bps.penyuluhan.pdf', array_merge(request()->query(),['tab'=>$tab])) }}" class="btn btn-danger rounded-3 px-3" target="_blank">
                <i class="bi bi-file-earmark-pdf me-1"></i> Cetak PDF
            </a>
        </div>
    </div>

    {{-- Filter --}}
    <form method="GET" class="row g-3 mb-4 bg-light rounded-3 p-3 border border-light-subtle align-items-end">
        <input type="hidden" name="tab" value="{{ $tab }}">
        <div class="col-md-5">
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
        @foreach(['penyuluh'=>'Data Penyuluh','gapoktan'=>'Gapoktan','kelompoktani'=>'Kelompok Tani','petani'=>'Data Petani','bpp'=>'Data BPP'] as $key => $label)
        <li class="nav-item">
            <a class="nav-link fw-semibold {{ $tab === $key ? 'active text-success fw-bold' : 'text-secondary' }}"
               href="{{ route('laporan-bps.penyuluhan', array_merge(request()->except('tab'), ['tab'=>$key])) }}">
                @if($key==='penyuluh')<i class="bi bi-person-workspace me-1"></i>
                @elseif($key==='gapoktan')<i class="bi bi-diagram-3-fill me-1"></i>
                @elseif($key==='kelompoktani')<i class="bi bi-people-fill me-1"></i>
                @elseif($key==='petani')<i class="bi bi-person-fill-gear me-1"></i>
                @else<i class="bi bi-building-fill-gear me-1"></i>@endif
                {{ $label }}
            </a>
        </li>
        @endforeach
    </ul>

    {{-- ─── PENYULUH ─── --}}
    @if($tab === 'penyuluh')
        @if($penyuluhs->isEmpty())
            <div class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2"></i>Tidak ada data.</div>
        @else
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle small">
                <thead class="table-light fw-bold text-center">
                    <tr><th>No</th><th>Nama Penyuluh</th><th>NIP</th><th>Telepon</th><th>BPP</th><th>Kecamatan</th></tr>
                </thead>
                <tbody>
                    @foreach($penyuluhs as $i => $p)
                    <tr>
                        <td class="text-center">{{ $i+1 }}</td>
                        <td class="fw-semibold">{{ $p->nama }}</td>
                        <td>{{ $p->nip ?? '-' }}</td>
                        <td>{{ $p->telepon ?? '-' }}</td>
                        <td>{{ $p->bpp?->nama ?? '-' }}</td>
                        <td>{{ $p->bpp?->kecamatan?->nama ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-dark fw-bold"><tr><td colspan="6">Total: {{ $penyuluhs->count() }} penyuluh</td></tr></tfoot>
            </table>
        </div>
        @endif

    {{-- ─── GAPOKTAN ─── --}}
    @elseif($tab === 'gapoktan')
        @if($gapoktans->isEmpty())
            <div class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2"></i>Tidak ada data.</div>
        @else
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle small">
                <thead class="table-light fw-bold text-center">
                    <tr><th>No</th><th>Nama Gapoktan</th><th>Ketua</th><th>Kecamatan</th><th>Jml Kelompok Tani</th></tr>
                </thead>
                <tbody>
                    @foreach($gapoktans as $i => $g)
                    <tr>
                        <td class="text-center">{{ $i+1 }}</td>
                        <td class="fw-semibold">{{ $g->nama }}</td>
                        <td>{{ $g->ketua ?? '-' }}</td>
                        <td>{{ $g->kecamatan?->nama ?? '-' }}</td>
                        <td class="text-center"><span class="badge bg-success-subtle text-success">{{ $g->kelompok_tanis_count }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-dark fw-bold"><tr><td colspan="5">Total: {{ $gapoktans->count() }} gapoktan</td></tr></tfoot>
            </table>
        </div>
        @endif

    {{-- ─── KELOMPOK TANI ─── --}}
    @elseif($tab === 'kelompoktani')
        @if($kelompokTanis->isEmpty())
            <div class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2"></i>Tidak ada data.</div>
        @else
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle small">
                <thead class="table-light fw-bold text-center">
                    <tr><th>No</th><th>Nama Kelompok Tani</th><th>Ketua</th><th>Desa</th><th>Kecamatan</th><th>Gapoktan</th><th>Jml Petani</th></tr>
                </thead>
                <tbody>
                    @foreach($kelompokTanis as $i => $k)
                    <tr>
                        <td class="text-center">{{ $i+1 }}</td>
                        <td class="fw-semibold">{{ $k->nama }}</td>
                        <td>{{ $k->ketua ?? '-' }}</td>
                        <td>{{ $k->desa?->nama ?? '-' }}</td>
                        <td>{{ $k->desa?->kecamatan?->nama ?? '-' }}</td>
                        <td>{{ $k->gapoktan?->nama ?? '-' }}</td>
                        <td class="text-center"><span class="badge bg-success-subtle text-success">{{ $k->petanis_count }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-dark fw-bold"><tr><td colspan="7">Total: {{ $kelompokTanis->count() }} kelompok tani</td></tr></tfoot>
            </table>
        </div>
        @endif

    {{-- ─── PETANI ─── --}}
    @elseif($tab === 'petani')
        @if($petanis->isEmpty())
            <div class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2"></i>Tidak ada data.</div>
        @else
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle small">
                <thead class="table-light fw-bold text-center">
                    <tr><th>No</th><th>Nama Petani</th><th>NIK</th><th>Telepon</th><th>Alamat</th><th>Kelompok Tani</th><th>Kecamatan</th></tr>
                </thead>
                <tbody>
                    @foreach($petanis as $i => $p)
                    <tr>
                        <td class="text-center">{{ $petanis->firstItem() + $i }}</td>
                        <td class="fw-semibold">{{ $p->nama }}</td>
                        <td>{{ $p->nik ?? '-' }}</td>
                        <td>{{ $p->telepon ?? '-' }}</td>
                        <td>{{ $p->alamat ?? '-' }}</td>
                        <td>{{ $p->kelompokTani?->nama ?? '-' }}</td>
                        <td>{{ $p->kelompokTani?->desa?->kecamatan?->nama ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-3">
            <small class="text-muted">
                Menampilkan <strong>{{ $petanis->firstItem() }}–{{ $petanis->lastItem() }}</strong>
                dari <strong>{{ number_format($petanis->total()) }}</strong> petani
            </small>
            @if($petanis->hasPages())
            <nav>
                <ul class="pagination pagination-sm mb-0 gap-1">
                    {{-- Prev --}}
                    <li class="page-item {{ $petanis->onFirstPage() ? 'disabled' : '' }}">
                        <a class="page-link rounded-2" href="{{ $petanis->previousPageUrl() }}">‹ Prev</a>
                    </li>
                    {{-- Page numbers --}}
                    @foreach($petanis->getUrlRange(max(1,$petanis->currentPage()-2), min($petanis->lastPage(),$petanis->currentPage()+2)) as $page => $url)
                        <li class="page-item {{ $page == $petanis->currentPage() ? 'active' : '' }}">
                            <a class="page-link rounded-2" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endforeach
                    {{-- Next --}}
                    <li class="page-item {{ !$petanis->hasMorePages() ? 'disabled' : '' }}">
                        <a class="page-link rounded-2" href="{{ $petanis->nextPageUrl() }}">Next ›</a>
                    </li>
                </ul>
            </nav>
            @endif
        </div>
        @endif

    {{-- ─── BPP ─── --}}
    @else
        @if($bpps->isEmpty())
            <div class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2"></i>Tidak ada data.</div>
        @else
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle small">
                <thead class="table-light fw-bold text-center">
                    <tr><th>No</th><th>Nama BPP</th><th>Kecamatan</th><th>Alamat</th></tr>
                </thead>
                <tbody>
                    @foreach($bpps as $i => $b)
                    <tr>
                        <td class="text-center">{{ $i+1 }}</td>
                        <td class="fw-semibold">{{ $b->nama }}</td>
                        <td>{{ $b->kecamatan?->nama ?? '-' }}</td>
                        <td>{{ $b->alamat ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-dark fw-bold"><tr><td colspan="4">Total: {{ $bpps->count() }} BPP</td></tr></tfoot>
            </table>
        </div>
        @endif
    @endif
</div>
@endsection
