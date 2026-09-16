<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

final class CatatRequest
{
    private const AMBANG_DURASI_MS = 100;

    public function handle(Request $request, Closure $next): Response
    {
        $mulai = microtime(true);
        $idRequest = (string) Str::uuid();

        $request->attributes->set('id_request', $idRequest);

        $response = $next($request);

        $durasiMs = round((microtime(true) - $mulai) * 1000, 2);
        $status = $response->getStatusCode();

        logger()->info('PERPUS.HTTP', [
            'id' => $idRequest,
            'method' => $request->method(),
            'uri' => $request->path(),
            'status' => $response->getStatusCode(),
            'durasi_ms' => $durasiMs,
            'pengguna' => $request->attributes->get('pengguna')['nama'] ?? '-',
            'ip' => $request->ip(),
        ]);

        $response->headers->set('X-Request-Id', $idRequest);
        $response->headers->set('X-Response-Time', $durasiMs . 'ms');

        return $response;
    }
}