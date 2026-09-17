<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\BukuController;
use App\Http\Controllers\Api\V1\LaporanController;
use App\Http\Controllers\Api\V1\PeminjamanController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', fn() => response()->json([
    'status' => 'ok',
    'perpustakaan' => config('perpus.nama_perpustakaan'),
    'waktu' => now()->toIso8601String(),
]))
    ->name('api.ping')
    ->middleware('user-agent');

Route::prefix('v1/perpus')
    ->name('api.v1.perpus.')
    ->middleware('anggota')
    ->group(function () {
        
        Route::get('/buku', [BukuController::class, 'index'])
            ->name('buku.index');

        Route::get('/buku/{isbn}', [BukuController::class, 'show'])
            ->where('isbn', 'ISBN-[0-9]{3}')
            ->name('buku.show');

        Route::get('/peminjaman', [PeminjamanController::class, 'index'])
            ->name('peminjaman.index');

        Route::post('/peminjaman', [PeminjamanController::class, 'store'])
            ->middleware('jam.layanan')
            ->name('peminjaman.store');

        Route::get('/peminjaman/{id}', [PeminjamanController::class, 'show'])
            ->where('id', 'PJM-[0-9]{8}-[0-9]{4}')
            ->name('peminjaman.show');

        Route::post('/peminjaman/{id}/kembalikan', [PeminjamanController::class, 'kembalikan'])
            ->middleware('peran:petugas')
            ->where('id', 'PJM-[0-9]{8}-[0-9]{4}')
            ->name('peminjaman.kembalikan');

        Route::post('/peminjaman/{id}/perpanjang', [PeminjamanController::class, 'perpanjang'])
            ->where('id', 'PJM-[0-9]{8}-[0-9]{4}')
            ->name('peminjaman.perpanjang');

        Route::post('/pratinjau', [PeminjamanController::class, 'pratinjau'])
            ->name('pratinjau');

        Route::prefix('laporan')->name('laporan.')->group(function () {
            Route::get('/denda-harian', [LaporanController::class, 'dendaHarian'])
                ->middleware('peran:petugas')
                ->name('denda-harian');

            Route::get('/terpopuler', [LaporanController::class, 'terpopuler'])
                ->middleware('peran:petugas')
                ->name('terpopuler');
        });
    });
