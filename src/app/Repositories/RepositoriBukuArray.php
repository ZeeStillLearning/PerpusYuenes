<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\RepositoriBuku;

/**
 * Sumber data sementara. Diganti implementasi Eloquent nanti.
 */
final class RepositoriBukuArray implements RepositoriBuku
{
    private const KATALOG = [
        'ISBN-001' => ['judul' => 'Laravel untuk Pemula', 'kategori' => 'teknologi', 'harga' => 95000, 'stok' => 3],
        'ISBN-002' => ['judul' => 'Filosofi Teras', 'kategori' => 'filsafat', 'harga' => 78000, 'stok' => 5],
        'ISBN-003' => ['judul' => 'Bumi Manusia', 'kategori' => 'sastra', 'harga' => 85000, 'stok' => 0],
        'ISBN-004' => ['judul' => 'Sapiens', 'kategori' => 'sains', 'harga' => 110000, 'stok' => 2],
        'ISBN-005' => ['judul' => 'Atomic Habits', 'kategori' => 'pengembangan_diri', 'harga' => 99000, 'stok' => 4],
        'ISBN-006' => ['judul' => 'Clean Code', 'kategori' => 'teknologi', 'harga' => 150000, 'stok' => 1],
        'ISBN-007' => ['judul' => 'Negeri 5 Menara', 'kategori' => 'sastra', 'harga' => 72000, 'stok' => 6],
        'ISBN-008' => ['judul' => 'Cosmos', 'kategori' => 'sains', 'harga' => 105000, 'stok' => 0],
    ];

    public function semua(): array
    {
        return array_map(
            static fn(string $isbn): array => self::baris($isbn),
            array_keys(self::KATALOG),
        );
    }

    public function cariIsbn(string $isbn): ?array
    {
        $isbn = strtoupper(trim($isbn));

        return isset(self::KATALOG[$isbn]) ? self::baris($isbn) : null;
    }

    /** @return array<string, mixed> */
    private static function baris(string $isbn): array
    {
        return ['isbn' => $isbn, ...self::KATALOG[$isbn]];
    }
}
