<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\RepositoriBuku;
use App\Exceptions\BukuTidakDitemukan;

/**
 * Layanan katalog buku. Tidak mengenal Request, response(), maupun status HTTP.
 */
final class LayananKatalog
{
    public function __construct(
        private readonly RepositoriBuku $repositori,
    ) {}

    /** @return array<int, array<string, mixed>> */
    public function daftar(?string $kategori = null, ?string $cari = null): array
    {
        $buku = $this->repositori->semua();

        if ($kategori !== null) {
            $buku = array_filter(
                $buku,
                static fn(array $b): bool => $b['kategori'] === $kategori,
            );
        }

        if ($cari !== null) {
            $kunci = mb_strtolower($cari);
            $buku = array_filter(
                $buku,
                static fn(array $b): bool => str_contains(mb_strtolower($b['judul']), $kunci),
            );
        }

        return array_values(array_map($this->format(...), $buku));
    }

    /** @return array<string, mixed> */
    public function ambil(string $isbn): array
    {
        $buku = $this->repositori->cariIsbn($isbn);

        if ($buku === null) {
            throw new BukuTidakDitemukan($isbn);
        }

        return $this->format($buku);
    }

    /**
     * @param array<string, mixed> $buku
     * @return array<string, mixed>
     */
    private function format(array $buku): array
    {
        return [
            'isbn' => $buku['isbn'],
            'judul' => $buku['judul'],
            'kategori' => $buku['kategori'],
            'harga' => $buku['harga'],
            'stok' => $buku['stok'],
            'tersedia' => $buku['stok'] > 0,
        ];
    }
}
