<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class JamLayanan
{
    public function handle(Request $request, Closure $next): Response
    {
        $buka = (string) config('perpus.jam.buka');
        $tutup = (string) config('perpus.jam.tutup');

        $sekarang = now();
        $jamBuka = now()->setTimeFromTimeString($buka);
        $jamTutup = now()->setTimeFromTimeString($tutup);

        if ($sekarang->lt($jamBuka) || $sekarang->gt($jamTutup)) {
            return response()->json([
                'kesalahan' => 'di_luar_jam_layanan',
                'pesan' => "Peminjaman hanya dilayani pukul {$buka} sampai {$tutup}.",
                'waktu_server' => $sekarang->toIso8601String(),
            ], 403);
        }

        return $next($request);
    }
}