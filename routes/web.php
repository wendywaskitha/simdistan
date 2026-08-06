<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BidangController;
use App\Http\Controllers\KecamatanController;
use App\Http\Controllers\DesaController;
use App\Http\Controllers\BppController;
use App\Http\Controllers\PenyuluhController;
use App\Http\Controllers\GapoktanController;
use App\Http\Controllers\KelompokTaniController;
use App\Http\Controllers\PetaniController;
use App\Http\Controllers\KategoriKomoditasController;
use App\Http\Controllers\KomoditasController;
use App\Http\Controllers\VarietasController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\TanamanPanganController;
use App\Http\Controllers\HortikulturaController;
use App\Http\Controllers\PerkebunanController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Rute Home / Pengalihan awal
Route::get('/', function () {
    return redirect()->route('login');
});

// Rute Autentikasi
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // Dashboard Utama
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // Super Admin Route Group (Users, Master Data)
    Route::middleware('role:Super Admin')->group(function () {
        Route::get('users/{id}/impersonate', [UserController::class, 'impersonate'])->name('users.impersonate');
        Route::resource('users', UserController::class);
        
        // Master Data Rute
        Route::resource('bidangs', BidangController::class);
        Route::resource('kecamatans', KecamatanController::class);
        Route::resource('desas', DesaController::class);
        Route::resource('kategori-komoditas', KategoriKomoditasController::class);
        Route::resource('komoditas', KomoditasController::class);
        Route::resource('varietas', VarietasController::class);
        Route::resource('satuans', SatuanController::class);
    });

    // Rute Baru Penyuluhan & BPP
    Route::middleware('permission:akses penyuluhan')->group(function () {
        Route::resource('bpps', BppController::class);
        Route::resource('penyuluhs', PenyuluhController::class);
        Route::resource('gapoktans', GapoktanController::class);
        // Kelola Anggota Kelompok Tani
        Route::get('kelompok-tanis/{id}/kelola-anggota',  [KelompokTaniController::class, 'kelolaAnggota'])->name('kelompok-tanis.kelola-anggota');
        Route::post('kelompok-tanis/{id}/attach-anggota', [KelompokTaniController::class, 'attachAnggota'])->name('kelompok-tanis.anggota.attach');
        Route::post('kelompok-tanis/{id}/create-new-anggota', [KelompokTaniController::class, 'createNewAnggota'])->name('kelompok-tanis.anggota.create-new');
        Route::post('kelompok-tanis/{kelompokTaniId}/anggota/{petaniId}/set-ketua', [KelompokTaniController::class, 'setKetua'])->name('kelompok-tanis.anggota.set-ketua');
        Route::delete('kelompok-tanis/{kelompokTaniId}/anggota/{petaniId}', [KelompokTaniController::class, 'removeAnggota'])->name('kelompok-tanis.anggota.remove');
        Route::resource('kelompok-tanis', KelompokTaniController::class);
        Route::resource('petanis', PetaniController::class);
    });

    Route::get('dashboard/data-komoditas-trend', [\App\Http\Controllers\DashboardController::class, 'getKomoditasTrend'])->name('dashboard.komoditas-trend');
    Route::get('leave-impersonate', [UserController::class, 'leaveImpersonate'])->name('users.leave-impersonate');

    // Rute Baru Tanaman Pangan
    Route::middleware('permission:akses tanaman pangan')->group(function () {
        Route::get('tanaman-pangan/kelola', [TanamanPanganController::class, 'kelola'])->name('tanaman-pangan.kelola');
        Route::get('tanaman-pangan/input-mingguan', [TanamanPanganController::class, 'inputMingguan'])->name('tanaman-pangan.input-mingguan');
        Route::post('tanaman-pangan/simpan-mingguan', [TanamanPanganController::class, 'simpanMingguan'])->name('tanaman-pangan.simpan-mingguan');
        Route::get('tanaman-pangan/data-grafik', [TanamanPanganController::class, 'dataGrafik'])->name('tanaman-pangan.data-grafik');
        Route::resource('tanaman-pangan', TanamanPanganController::class);
    });

    // Rute Baru Hortikultura
    Route::middleware('permission:akses hortikultura')->group(function () {
        Route::get('hortikultura/prev-data', [HortikulturaController::class, 'ajaxPrevData'])->name('hortikultura.prev-data');
        Route::resource('hortikultura', HortikulturaController::class);
    });

    // Rute Baru Perkebunan
    Route::middleware('permission:akses perkebunan')->group(function () {
        Route::get('perkebunan/prev-data', [PerkebunanController::class, 'ajaxPrevData'])->name('perkebunan.prev-data');
        Route::resource('perkebunan', PerkebunanController::class);
    });

    // Rute Fitur PSP (Prasarana & Sarana)
    Route::middleware('permission:akses psp')->group(function () {
        // Rute Master Data Toko & Jenis Pupuk
        Route::resource('toko-pupuks', \App\Http\Controllers\TokoPupukController::class);
        Route::resource('jenis-pupuks', \App\Http\Controllers\JenisPupukController::class);
        Route::get('kuota-tahunan/ajax-data', [\App\Http\Controllers\KuotaTahunanController::class, 'ajaxKuotaData'])->name('kuota-tahunan.ajax-data');
        Route::resource('kuota-tahunan', \App\Http\Controllers\KuotaTahunanController::class)->only(['index', 'store']);

        // Rute Fitur Distribusi Pupuk (Bidang PSP)
        Route::get('distribusi-pupuk', [\App\Http\Controllers\DistribusiPupukController::class, 'index'])->name('distribusi-pupuk.index');
        Route::get('distribusi-pupuk/input-bulanan', [\App\Http\Controllers\DistribusiPupukController::class, 'inputBulanan'])->name('distribusi-pupuk.input-bulanan');
        Route::post('distribusi-pupuk/simpan-bulanan', [\App\Http\Controllers\DistribusiPupukController::class, 'simpanBulanan'])->name('distribusi-pupuk.simpan-bulanan');
        Route::get('distribusi-pupuk/pengalihan', [\App\Http\Controllers\DistribusiPupukController::class, 'pengalihanList'])->name('distribusi-pupuk.pengalihan.index');
        Route::get('distribusi-pupuk/pengalihan/tambah', [\App\Http\Controllers\DistribusiPupukController::class, 'pengalihanTambah'])->name('distribusi-pupuk.pengalihan.create');
        Route::post('distribusi-pupuk/pengalihan/simpan', [\App\Http\Controllers\DistribusiPupukController::class, 'pengalihanSimpan'])->name('distribusi-pupuk.pengalihan.store');
        Route::get('distribusi-pupuk/ajax-toko-kecamatan', [\App\Http\Controllers\DistribusiPupukController::class, 'ajaxTokoKecamatan'])->name('distribusi-pupuk.ajax-toko-kecamatan');
        Route::get('distribusi-pupuk/ajax-laporan-data', [\App\Http\Controllers\DistribusiPupukController::class, 'ajaxLaporanData'])->name('distribusi-pupuk.ajax-laporan-data');
        Route::get('distribusi-pupuk/data-grafik', [\App\Http\Controllers\DistribusiPupukController::class, 'dataGrafik'])->name('distribusi-pupuk.data-grafik');
        Route::get('distribusi-pupuk/ajax-kecamatan-pengalihan', [\App\Http\Controllers\DistribusiPupukController::class, 'ajaxKecamatanPengalihan'])->name('distribusi-pupuk.ajax-kecamatan-pengalihan');

        // Rute Fitur Alsintan (Bidang PSP)
        Route::post('alsintans/{id}/laporan', [\App\Http\Controllers\AlsintanController::class, 'storeLaporan'])->name('alsintans.laporan.store');
        Route::get('alsintans/{id}/realokasi', [\App\Http\Controllers\AlsintanController::class, 'realokasiForm'])->name('alsintans.realokasi.form');
        Route::post('alsintans/{id}/realokasi', [\App\Http\Controllers\AlsintanController::class, 'realokasiStore'])->name('alsintans.realokasi.store');
        Route::resource('alsintans', \App\Http\Controllers\AlsintanController::class);
        Route::resource('jenis-alats', \App\Http\Controllers\JenisAlatController::class);

        // Rute Fitur Infrastruktur & Irigasi (Bidang PSP)
        Route::get('infrastrukturs/ajax-maps', [\App\Http\Controllers\InfrastrukturController::class, 'getMapLocations'])->name('infrastrukturs.ajax-maps');
        Route::get('infrastrukturs/ajax-desas/{kecamatan_id?}', [\App\Http\Controllers\InfrastrukturController::class, 'getDesasByKecamatan'])->name('infrastrukturs.ajax-desas');
        Route::post('infrastrukturs/{id}/laporan', [\App\Http\Controllers\InfrastrukturController::class, 'storeLaporan'])->name('infrastrukturs.laporan.store');
        Route::resource('infrastrukturs', \App\Http\Controllers\InfrastrukturController::class);
    });

    // Rute Statistik Gabungan
    Route::get('statistik', [\App\Http\Controllers\StatistikController::class, 'index'])->name('statistik.index');
    Route::get('statistik/data-produksi', [\App\Http\Controllers\StatistikController::class, 'dataProduksi'])->name('statistik.data-produksi');
    Route::get('statistik/data-alsintan', [\App\Http\Controllers\StatistikController::class, 'dataAlsintan'])->name('statistik.data-alsintan');
    Route::get('statistik/data-infrastruktur', [\App\Http\Controllers\StatistikController::class, 'dataInfrastruktur'])->name('statistik.data-infrastruktur');
    Route::get('statistik/data-pupuk', [\App\Http\Controllers\StatistikController::class, 'dataPupuk'])->name('statistik.data-pupuk');

    // Rute Laporan BPS
    Route::get('laporan-bps', [\App\Http\Controllers\LaporanBpsController::class, 'index'])->name('laporan-bps.index');
    Route::get('laporan-bps/tanaman-pangan', [\App\Http\Controllers\LaporanBpsController::class, 'tanamanPangan'])->name('laporan-bps.tanaman-pangan');
    Route::get('laporan-bps/tanaman-pangan/pdf', [\App\Http\Controllers\LaporanBpsController::class, 'tanamanPanganPdf'])->name('laporan-bps.tanaman-pangan.pdf');
    Route::get('laporan-bps/tanaman-pangan/excel', [\App\Http\Controllers\LaporanBpsController::class, 'tanamanPanganExcel'])->name('laporan-bps.tanaman-pangan.excel');
    Route::get('laporan-bps/hortikultura', [\App\Http\Controllers\LaporanBpsController::class, 'hortikultura'])->name('laporan-bps.hortikultura');
    Route::get('laporan-bps/hortikultura/pdf', [\App\Http\Controllers\LaporanBpsController::class, 'hortikulturaPdf'])->name('laporan-bps.hortikultura.pdf');
    Route::get('laporan-bps/hortikultura/excel', [\App\Http\Controllers\LaporanBpsController::class, 'hortikulturaExcel'])->name('laporan-bps.hortikultura.excel');
    Route::get('laporan-bps/perkebunan', [\App\Http\Controllers\LaporanBpsController::class, 'perkebunan'])->name('laporan-bps.perkebunan');
    Route::get('laporan-bps/perkebunan/pdf', [\App\Http\Controllers\LaporanBpsController::class, 'perkebunanPdf'])->name('laporan-bps.perkebunan.pdf');
    Route::get('laporan-bps/perkebunan/excel', [\App\Http\Controllers\LaporanBpsController::class, 'perkebunanExcel'])->name('laporan-bps.perkebunan.excel');
    Route::get('laporan-bps/psp', [\App\Http\Controllers\LaporanBpsController::class, 'psp'])->name('laporan-bps.psp');
    Route::get('laporan-bps/psp/pdf', [\App\Http\Controllers\LaporanBpsController::class, 'pspPdf'])->name('laporan-bps.psp.pdf');
    Route::get('laporan-bps/psp/excel', [\App\Http\Controllers\LaporanBpsController::class, 'pspExcel'])->name('laporan-bps.psp.excel');
    Route::get('laporan-bps/penyuluhan', [\App\Http\Controllers\LaporanBpsController::class, 'penyuluhan'])->name('laporan-bps.penyuluhan');
    Route::get('laporan-bps/penyuluhan/pdf', [\App\Http\Controllers\LaporanBpsController::class, 'penyuluhanPdf'])->name('laporan-bps.penyuluhan.pdf');
    Route::get('laporan-bps/penyuluhan/excel', [\App\Http\Controllers\LaporanBpsController::class, 'penyuluhanExcel'])->name('laporan-bps.penyuluhan.excel');
});

