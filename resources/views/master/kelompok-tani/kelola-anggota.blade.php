@extends('layouts.admin')
@section('title', 'Kelola Anggota — {{ $kelompokTani->nama }}')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Kelompok Tani', 'url' => route('kelompok-tanis.index')],
    ['label' => 'Kelola Anggota: ' . $kelompokTani->nama]
]" />

<div class="row g-4">
    {{-- ─── Info Card ─── --}}
    <div class="col-12">
        <div class="card custom-card border-0 p-4">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <h5 class="fw-bold mb-1">
                        <i class="bi bi-people-fill me-2 text-success"></i>{{ $kelompokTani->nama }}
                    </h5>
                    <div class="d-flex flex-wrap gap-3 text-muted small mt-1">
                        <span><i class="bi bi-geo-alt me-1"></i>{{ $kelompokTani->desa?->nama ?? '-' }}, {{ $kelompokTani->desa?->kecamatan?->nama ?? '-' }}</span>
                        <span><i class="bi bi-diagram-3 me-1"></i>{{ $kelompokTani->gapoktan?->nama ?? 'Tidak ada Gapoktan' }}</span>
                        <span><i class="bi bi-person me-1"></i>Ketua: {{ $kelompokTani->ketua ?? '-' }}</span>
                        <span><i class="bi bi-people me-1"></i>{{ $petanis->total() }} Anggota</span>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-success rounded-3 px-3" data-bs-toggle="modal" data-bs-target="#modalAttach">
                        <i class="bi bi-person-plus-fill me-1"></i> Tambah Anggota
                    </button>
                    <a href="{{ route('kelompok-tanis.index') }}" class="btn btn-outline-secondary rounded-3 px-3">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Tabel Anggota ─── --}}
    <div class="col-12">
        <div class="card custom-card border-0 p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-list-ul me-2 text-success"></i>Daftar Anggota Petani</h6>

            @if($petanis->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-person-x fs-1 d-block mb-2 text-secondary"></i>
                    Belum ada petani terdaftar. Klik <strong>Tambah Anggota</strong> untuk mulai.
                </div>
            @else
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle small">
                    <thead class="table-light fw-bold text-center">
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Petani</th>
                            <th>NIK</th>
                            <th>Telepon</th>
                            <th>Alamat</th>
                            <th>Luas Lahan (Ha)</th>
                            <th width="10%">KTP</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($petanis as $i => $p)
                        <tr>
                            <td class="text-center">{{ $petanis->firstItem() + $i }}</td>
                            <td class="fw-semibold">
                                {{ $p->nama }}
                                @if($kelompokTani->ketua_petani_id === $p->id)
                                    <span class="badge bg-success-subtle text-success ms-1 rounded-pill small"><i class="bi bi-star-fill me-1"></i>Ketua</span>
                                @endif
                            </td>
                            <td>{{ $p->nik ?? '-' }}</td>
                            <td>{{ $p->telepon ?? '-' }}</td>
                            <td>{{ $p->alamat && $p->alamat != 'Desa , Kec. Barangka' ? $p->alamat : (($p->kelompokTani?->desa?->nama ?? '—') . ', Kec. ' . ($p->kelompokTani?->desa?->kecamatan?->nama ?? '—')) }}</td>
                            <td class="text-end fw-semibold">{{ number_format($p->luas_lahan, 2) }} Ha</td>
                            <td class="text-center">
                                @if($p->ktp)
                                    <a href="{{ asset('storage/' . $p->ktp) }}" target="_blank" class="text-success"><i class="bi bi-patch-check-fill fs-5"></i></a>
                                @else
                                    <span class="text-danger">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    @if($kelompokTani->ketua_petani_id !== $p->id)
                                        <form action="{{ route('kelompok-tanis.anggota.set-ketua', [$kelompokTani->id, $p->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Jadikan Ketua Kelompok">
                                                <i class="bi bi-star"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('kelompok-tanis.anggota.remove', [$kelompokTani->id, $p->id]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger btn-delete-trigger" title="Lepas dari kelompok ini">
                                            <i class="bi bi-person-dash-fill"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted">
                    Menampilkan <strong>{{ $petanis->firstItem() }}–{{ $petanis->lastItem() }}</strong>
                    dari <strong>{{ number_format($petanis->total()) }}</strong> anggota
                </small>
                @if($petanis->hasPages())
                <nav>
                    <ul class="pagination pagination-sm mb-0 gap-1">
                        <li class="page-item {{ $petanis->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link rounded-2" href="{{ $petanis->previousPageUrl() }}">‹ Prev</a>
                        </li>
                        @foreach($petanis->getUrlRange(max(1,$petanis->currentPage()-2), min($petanis->lastPage(),$petanis->currentPage()+2)) as $page => $url)
                            <li class="page-item {{ $page == $petanis->currentPage() ? 'active' : '' }}">
                                <a class="page-link rounded-2" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endforeach
                        <li class="page-item {{ !$petanis->hasMorePages() ? 'disabled' : '' }}">
                            <a class="page-link rounded-2" href="{{ $petanis->nextPageUrl() }}">Next ›</a>
                        </li>
                    </ul>
                </nav>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════════════
     MODAL: TAMBAH ANGGOTA
     (Tab 1: Attach Petani Sudah Ada | Tab 2: Buat Petani Baru)
═══════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalAttach" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-person-plus-fill me-2 text-success"></i>Tambah Anggota Petani
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pt-3">

                {{-- Nav Tabs --}}
                <ul class="nav nav-tabs mb-4" id="attachTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active fw-semibold" data-bs-toggle="tab" data-bs-target="#tabAttach" type="button">
                            <i class="bi bi-search me-1"></i> Pilih Petani Terdaftar
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#tabCreate" type="button">
                            <i class="bi bi-person-plus me-1"></i> Buat Petani Baru
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="attachTabContent">
                    {{-- ─── TAB 1: ATTACH PETANI SUDAH ADA ─── --}}
                    <div class="tab-pane fade show active" id="tabAttach">
                        <form action="{{ route('kelompok-tanis.anggota.attach', $kelompokTani->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-secondary small">Cari & Pilih Petani</label>
                                <input type="text" id="searchPetani" class="form-control rounded-3 mb-2"
                                       placeholder="Ketik nama petani untuk filter...">
                                <div class="border rounded-3 p-2" style="max-height:280px;overflow-y:auto;" id="petaniListWrapper">
                                    @forelse($availablePetanis as $ap)
                                    <label class="d-flex align-items-center gap-2 px-2 py-2 rounded-3 petani-item"
                                           style="cursor:pointer;transition:.1s;" onmouseenter="this.style.background='#f0fdf4'" onmouseleave="this.style.background=''">
                                        <input type="checkbox" name="petani_ids[]" value="{{ $ap->id }}" class="form-check-input mt-0">
                                        <div>
                                            <div class="fw-semibold">{{ $ap->nama }}</div>
                                            <div class="text-muted small">NIK: {{ $ap->nik ?? '-' }} | Tel: {{ $ap->telepon ?? '-' }}</div>
                                        </div>
                                    </label>
                                    @empty
                                    <p class="text-muted small text-center py-3 mb-0">Semua petani sudah menjadi anggota kelompok ini, atau belum ada petani terdaftar.</p>
                                    @endforelse
                                </div>
                            </div>
                            <div class="d-flex justify-content-end gap-2 mt-3">
                                <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-success rounded-3 px-4">
                                    <i class="bi bi-check2-circle me-1"></i> Attach Anggota
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- ─── TAB 2: BUAT PETANI BARU ─── --}}
                    <div class="tab-pane fade" id="tabCreate">
                        <form action="{{ route('kelompok-tanis.anggota.create-new', $kelompokTani->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-secondary">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" name="nama" class="form-control rounded-3 @error('nama') is-invalid @enderror"
                                           value="{{ old('nama') }}" placeholder="Nama petani..." required>
                                    @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-secondary">NIK</label>
                                    <input type="text" name="nik" class="form-control rounded-3"
                                           value="{{ old('nik') }}" placeholder="16 digit NIK..." maxlength="16">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-secondary">No. Telepon</label>
                                    <input type="text" name="telepon" class="form-control rounded-3"
                                           value="{{ old('telepon') }}" placeholder="08xxxxxxxxxx">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-secondary">Alamat</label>
                                    <input type="text" name="alamat" class="form-control rounded-3"
                                           value="{{ old('alamat') }}" placeholder="Alamat tempat tinggal...">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-secondary">Luas Lahan (Ha)</label>
                                    <input type="number" name="luas_lahan" class="form-control rounded-3" step="0.01"
                                           value="{{ old('luas_lahan', 0) }}" placeholder="Contoh: 1.5">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold small text-secondary">Dokumen KTP (PDF, JPG, JPEG, PNG)</label>
                                    <input type="file" name="ktp" class="form-control rounded-3 @error('ktp') is-invalid @enderror">
                                    @error('ktp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-success rounded-3 px-4">
                                    <i class="bi bi-person-plus-fill me-1"></i> Simpan & Tambahkan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Filter petani list by name
    document.getElementById('searchPetani')?.addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.petani-item').forEach(item => {
            const name = item.querySelector('.fw-semibold').textContent.toLowerCase();
            item.style.display = name.includes(q) ? '' : 'none';
        });
    });

    // Re-open modal on tab-create if validation error occurred
    @if($errors->any() && old('_tab') === 'create')
    document.addEventListener('DOMContentLoaded', () => {
        const modal = new bootstrap.Modal(document.getElementById('modalAttach'));
        modal.show();
        document.querySelector('[data-bs-target="#tabCreate"]').click();
    });
    @endif
</script>
@endsection
