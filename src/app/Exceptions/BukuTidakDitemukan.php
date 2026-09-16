<?php

declare(strict_types=1);

namespace App\Exceptions;

final class BukuTidakDitemukan extends KesalahanPerpus
{
    public function __construct(private readonly string $isbn)
    {
        parent::__construct("Buku dengan ISBN {$isbn} tidak ditemukan.");
    }

    public function kodeHttp(): int
    {
        return 404;
    }

    public function konteks(): array
    {
        return ['isbn' => $this->isbn];
    }
}
