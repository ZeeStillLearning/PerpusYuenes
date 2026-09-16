<?php

declare(strict_types=1);

namespace App\Contracts;

interface RepositoriAnggota
{
    /** @return array<int, array<string, string>> */
    public function semua(): array;
}