<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\RepositoriPeminjaman;
use App\Domain\StatusPeminjaman;

final class LayananLaporan
{
    public function __construct(
        private readonly RepositoriPeminjaman $peminjaman,
    ) {}

    /** @return array<string, mixed> */
    public function dendaHarian(string $tanggal): array
    {
        $dikembalikan = $this->dikembalikanTanggal($tanggal);

        $totalDenda = array_sum(array_column($dikembalikan, 'denda'));
        $jumlahTerlambat = count(array_filter(
            $dikembalikan,
            static fn (array $p): bool => $p['denda'] > 0,
        ));

        return [
            'tanggal' => $tanggal,
            'jumlah_pengembalian' => count($dikembalikan),
            'jumlah_terlambat' => $jumlahTerlambat,
            'total_denda' => $totalDenda,
        ];
    }

    /** @return array<int, array<string, mixed>> */
    public function terpopuler(int $batas = 5): array
    {
        $rekap = [];

        foreach ($this->peminjaman->semua() as $p) {
            $isbn = $p['isbn'];
            $rekap[$isbn] ??= [
                'isbn' => $isbn,
                'judul' => $p['judul_buku'],
                'jumlah_dipinjam' => 0,
            ];
            $rekap[$isbn]['jumlah_dipinjam']++;
        }

        usort($rekap, static fn (array $a, array $b): int => $b['jumlah_dipinjam'] <=> $a['jumlah_dipinjam']);

        return array_slice(array_values($rekap), 0, $batas);
    }

    /** @return array<int, array<string, mixed>> */
    private function dikembalikanTanggal(string $tanggal): array
    {
        return array_values(array_filter(
            $this->peminjaman->semua(),
            static fn (array $p): bool => $p['status'] === StatusPeminjaman::Dikembalikan->value
                && $p['tanggal_kembali'] !== null
                && str_starts_with($p['tanggal_kembali'], $tanggal),
        ));
    }
}