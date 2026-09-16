<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class PeranAnggota
{
    public function handle(Request $request, Closure $next, string ...$peranDiizinkan): Response
    {
        $pengguna = $request->attributes->get('pengguna');

        if ($pengguna === null || ! in_array($pengguna['peran'], $peranDiizinkan, true)) {
            return response()->json([
                'kesalahan' => 'peran_tidak_berwenang',
                'pesan' => 'Aksi ini hanya boleh dilakukan oleh: ' . implode(', ', $peranDiizinkan) . '.',
                'peran_anda' => $pengguna['peran'] ?? null,
            ], 403);
        }

        return $next($request);
    }
}
