<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\LayananLaporan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class LaporanController extends Controller
{
    public function __construct(
        private readonly LayananLaporan $laporan,
    ) {}

    public function dendaHarian(Request $request): JsonResponse
    {
        $tanggal = $request->string('tanggal')->trim()->toString() ?: now()->toDateString();

        return response()->json([
            'data' => $this->laporan->dendaHarian($tanggal),
        ]);
    }

    public function terpopuler(Request $request): JsonResponse
    {
        $batas = (int) ($request->query('batas') ?? 5);

        return response()->json([
            'data' => $this->laporan->terpopuler(max(1, min($batas, 20))),
        ]);
    }
}