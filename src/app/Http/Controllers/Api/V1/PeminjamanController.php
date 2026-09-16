<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\LayananPeminjaman;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class PeminjamanController extends Controller
{
    public function __construct(
        private readonly LayananPeminjaman $peminjaman,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $anggota = $request->attributes->get('pengguna');

        // petugas bisa lihat semua, anggota hanya lihat miliknya sendiri
        $filterAnggota = $anggota['peran'] === 'petugas' ? null : $anggota['nama'];

        $riwayat = $this->peminjaman->riwayat($filterAnggota);

        return response()->json([
            'data' => $riwayat,
            'meta' => ['jumlah' => count($riwayat)],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'isbn' => ['required', 'string'],
        ]);

        $pengguna = $request->attributes->get('pengguna');

        $hasil = $this->peminjaman->pinjam($data['isbn'], $pengguna['nama']);

        return response()
            ->json(['data' => $hasil], 201)
            ->header('Location', route('api.v1.perpus.peminjaman.show', $hasil['id']));
    }

    public function show(string $id): JsonResponse
    {
        return response()->json([
            'data' => $this->peminjaman->cari($id),
        ]);
    }

    public function kembalikan(string $id): JsonResponse
    {
        return response()->json([
            'data' => $this->peminjaman->kembalikan($id),
        ]);
    }

    public function perpanjang(string $id): JsonResponse
    {
        return response()->json([
            'data' => $this->peminjaman->perpanjang($id),
        ]);
    }

    public function pratinjau(Request $request): JsonResponse
    {
        $data = $request->validate([
            'isbn' => ['required', 'string'],
        ]);

        $pengguna = $request->attributes->get('pengguna');

        $rincian = $this->peminjaman->pratinjau($data['isbn'], $pengguna['nama']);

        return response()->json(['data' => $rincian]);
    }
}
