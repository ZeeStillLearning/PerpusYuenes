<?php

declare(strict_types=1);

namespace App\Domain;

enum StatusPeminjaman: string
{
    case Dipinjam = 'dipinjam';
    case Dikembalikan = 'dikembalikan';
    case Hilang = 'hilang';

    public function label(): string
    {
        return match ($this) {
            self::Dipinjam => 'Dipinjam',
            self::Dikembalikan => 'Dikembalikan',
            self::Hilang => 'Hilang',
        };
    }
}