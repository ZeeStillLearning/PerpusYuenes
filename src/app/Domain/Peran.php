<?php

declare(strict_types=1);

namespace App\Domain;

enum Peran: string
{
    case Anggota = 'anggota';
    case Petugas = 'petugas';

    public function label(): string
    {
        return match ($this) {
            self::Anggota => 'Anggota',
            self::Petugas => 'Petugas',
        };
    }
}
