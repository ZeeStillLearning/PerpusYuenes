<?php

declare(strict_types=1);

namespace App\Exceptions;

final class PeminjamanTidakDitemukan extends KesalahanPerpus
{
    public function __construct(private readonly string $id)
    {
        parent::__construct("Peminjaman {$id} tidak ditemukan.");
    }

    public function kodeHttp(): int
    {
        return 404;
    }

    public function konteks(): array
    {
        return ['id' => $this->id];
    }
}
