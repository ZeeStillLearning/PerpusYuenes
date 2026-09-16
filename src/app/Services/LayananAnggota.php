<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\RepositoriAnggota;

final class LayananAnggota
{
    public function __construct(
        private readonly RepositoriAnggota $repositori,
    ) {}

    /** @return array<int, array<string, string>> */
    public function daftar(): array
    {
        return $this->repositori->semua();
    }
}