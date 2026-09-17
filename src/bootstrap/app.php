<?php

use App\Exceptions\KesalahanPerpus;
use App\Http\Middleware\CatatRequest;
use App\Http\Middleware\JamLayanan;
use App\Http\Middleware\KunciApiAnggota;
use App\Http\Middleware\PeranAnggota;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use App\Http\Middleware\TolakUserAgentKosong;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(append: [
            CatatRequest::class,
        ]);

        $middleware->alias([
            'anggota' => KunciApiAnggota::class,
            'peran' => PeranAnggota::class,
            'jam.layanan' => JamLayanan::class,
            'user-agent' => TolakUserAgentKosong::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (KesalahanPerpus $e, Request $request) {
            if (! $request->expectsJson()) {
                return null;
            }

            return response()->json(array_merge([
                'kesalahan' => $e->kodeKesalahan(),
                'pesan' => $e->getMessage(),
            ], $e->konteks()), $e->kodeHttp());
        });
    })->create();
