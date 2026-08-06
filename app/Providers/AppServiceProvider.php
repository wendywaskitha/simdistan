<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\BidangRepositoryInterface::class,
            \App\Repositories\BidangRepository::class
        );
        $this->app->bind(
            \App\Repositories\KecamatanRepositoryInterface::class,
            \App\Repositories\KecamatanRepository::class
        );
        $this->app->bind(
            \App\Repositories\DesaRepositoryInterface::class,
            \App\Repositories\DesaRepository::class
        );
        $this->app->bind(
            \App\Repositories\BppRepositoryInterface::class,
            \App\Repositories\BppRepository::class
        );
        $this->app->bind(
            \App\Repositories\PenyuluhRepositoryInterface::class,
            \App\Repositories\PenyuluhRepository::class
        );
        $this->app->bind(
            \App\Repositories\GapoktanRepositoryInterface::class,
            \App\Repositories\GapoktanRepository::class
        );
        $this->app->bind(
            \App\Repositories\KelompokTaniRepositoryInterface::class,
            \App\Repositories\KelompokTaniRepository::class
        );
        $this->app->bind(
            \App\Repositories\PetaniRepositoryInterface::class,
            \App\Repositories\PetaniRepository::class
        );
        $this->app->bind(
            \App\Repositories\KategoriKomoditasRepositoryInterface::class,
            \App\Repositories\KategoriKomoditasRepository::class
        );
        $this->app->bind(
            \App\Repositories\KomoditasRepositoryInterface::class,
            \App\Repositories\KomoditasRepository::class
        );
        $this->app->bind(
            \App\Repositories\VarietasRepositoryInterface::class,
            \App\Repositories\VarietasRepository::class
        );
        $this->app->bind(
            \App\Repositories\SatuanRepositoryInterface::class,
            \App\Repositories\SatuanRepository::class
        );
        $this->app->bind(
            \App\Repositories\LaporanProduksiRepositoryInterface::class,
            \App\Repositories\LaporanProduksiRepository::class
        );
        $this->app->bind(
            \App\Repositories\TokoPupukRepositoryInterface::class,
            \App\Repositories\TokoPupukRepository::class
        );
        $this->app->bind(
            \App\Repositories\JenisPupukRepositoryInterface::class,
            \App\Repositories\JenisPupukRepository::class
        );
        $this->app->bind(
            \App\Repositories\KuotaTahunanRepositoryInterface::class,
            \App\Repositories\KuotaTahunanRepository::class
        );
        $this->app->bind(
            \App\Repositories\AlsintanRepositoryInterface::class,
            \App\Repositories\AlsintanRepository::class
        );
        $this->app->bind(
            \App\Repositories\LaporanPemanfaatanAlsintanRepositoryInterface::class,
            \App\Repositories\LaporanPemanfaatanAlsintanRepository::class
        );
        $this->app->bind(
            \App\Repositories\RealokasiAlsintanRepositoryInterface::class,
            \App\Repositories\RealokasiAlsintanRepository::class
        );
        $this->app->bind(
            \App\Repositories\JenisAlatRepositoryInterface::class,
            \App\Repositories\JenisAlatRepository::class
        );
        $this->app->bind(
            \App\Repositories\InfrastrukturRepositoryInterface::class,
            \App\Repositories\InfrastrukturRepository::class
        );
        $this->app->bind(
            \App\Repositories\InfrastrukturLaporanRepositoryInterface::class,
            \App\Repositories\InfrastrukturLaporanRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
