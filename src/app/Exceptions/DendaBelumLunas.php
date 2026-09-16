<?php

declare(strict_types=1);

namespace App\Exceptions;

final class DendaBelumLunas extends KesalahanPerpus
{
    public function __construct(private readonly int $totalDenda)
    {
        parent::__construct('Anda memiliki denda belum lunas dan tidak dapat meminjam buku baru.');
    }

    public function kodeHttp(): int
    {
        return 422;
    }

    public function konteks(): array
    {
        return ['total_denda' => $this->totalDenda];
    }
}
