<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\RepositoriBuku;
use App\Contracts\RepositoriPeminjaman;
use App\Contracts\RepositoriAnggota;
use App\Repositories\RepositoriBukuArray;
use App\Repositories\RepositoriPeminjamanBerkas;
use App\Repositories\RepositoriAnggotaConfig;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(RepositoriBuku::class, RepositoriBukuArray::class);

        // singleton: satu instance dipakai ulang selama satu request
        $this->app->singleton(RepositoriPeminjaman::class, RepositoriPeminjamanBerkas::class);

        $this->app->bind(RepositoriAnggota::class, RepositoriAnggotaConfig::class);
    }

    public function boot(): void
    {
        //
    }
}