<?php

declare(strict_types=1);

namespace App\Contracts;

interface RepositoriPeminjaman
{
    /** @return array<string, array<string, mixed>> dikunci oleh id peminjaman */
    public function semua(): array;

    /** @return array<string, mixed>|null */
    public function cariId(string $id): ?array;

    /** @param array<string, mixed> $peminjaman */
    public function simpan(array $peminjaman): void;

    /** @param array<string, mixed> $perubahan */
    public function perbarui(string $id, array $perubahan): void;
}
