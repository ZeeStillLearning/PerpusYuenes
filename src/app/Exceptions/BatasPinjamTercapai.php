<?php

declare(strict_types=1);

namespace App\Exceptions;

final class BatasPinjamTercapai extends KesalahanPerpus
{
    public function __construct(private readonly int $batas)
    {
        parent::__construct("Batas maksimal {$batas} buku pinjaman aktif sudah tercapai.");
    }

    public function kodeHttp(): int
    {
        return 422;
    }

    public function konteks(): array
    {
        return ['batas' => $this->batas];
    }
}
