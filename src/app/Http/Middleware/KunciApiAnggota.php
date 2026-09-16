<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class KunciApiAnggota
{
    public function handle(Request $request, Closure $next): Response
    {
        $kunci = (string) $request->header('X-API-Key', '');

        if ($kunci === '') {
            return $this->tolak('kunci_api_hilang', 'Header X-API-Key wajib disertakan.');
        }

        $identitas = $this->cariPengguna($kunci);

        if ($identitas === null) {
            return $this->tolak('kunci_api_tidak_dikenal', 'Kunci API tidak dikenal.');
        }

        $request->attributes->set('pengguna', $identitas);

        return $next($request);
    }

    private function cariPengguna(string $kunci): ?array
    {
        foreach ((array) config('perpus.anggota') as $kunciSah => $identitas) {
            if (hash_equals((string) $kunciSah, $kunci)) {
                return $identitas;
            }
        }

        return null;
    }

    private function tolak(string $kode, string $pesan): Response
    {
        return response()->json([
            'kesalahan' => $kode,
            'pesan' => $pesan,
        ], 401);
    }
}
