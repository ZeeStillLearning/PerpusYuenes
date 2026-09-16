<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Support\Str;
use RuntimeException;

/**
 * Induk seluruh kesalahan domain Perpustakaan.
 * Tidak menyebut response() maupun JsonResponse.
 */
abstract class KesalahanPerpus extends RuntimeException
{
    abstract public function kodeHttp(): int;

    public function kodeKesalahan(): string
    {
        return Str::snake(class_basename($this));
    }

    /** @return array<string, mixed> */
    public function konteks(): array
    {
        return [];
    }
}
