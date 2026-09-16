<?php

declare(strict_types=1);

namespace App\Exceptions;

final class PeminjamanSudahDikembalikan extends KesalahanPerpus
{
    public function __construct(private readonly string $id)
    {
        parent::__construct("Peminjaman {$id} sudah pernah dikembalikan.");
    }

    public function kodeHttp(): int
    {
        return 409;
    }

    public function konteks(): array
    {
        return ['id' => $this->id];
    }
}
