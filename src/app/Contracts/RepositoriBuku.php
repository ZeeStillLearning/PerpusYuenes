<?php

declare(strict_types=1);

namespace App\Contracts;

/**
 * Kontrak sumber data buku. Service hanya bergantung pada
 * antarmuka ini, tidak pada implementasinya.
 */
interface RepositoriBuku
{
    /** @return array<int, array<string, mixed>> */
    public function semua(): array;

    /** @return array<string, mixed>|null */
    public function cariIsbn(string $isbn): ?array;
}