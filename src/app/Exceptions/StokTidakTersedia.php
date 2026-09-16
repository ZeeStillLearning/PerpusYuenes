<?php

declare(strict_types=1);

namespace App\Exceptions;

final class StokTidakTersedia extends KesalahanPerpus
{
    public function __construct(private readonly string $isbn)
    {
        parent::__construct("Buku {$isbn} sedang tidak tersedia.");
    }

    public function kodeHttp(): int
    {
        return 422;
    }

    public function konteks(): array
    {
        return ['isbn' => $this->isbn];
    }
}
