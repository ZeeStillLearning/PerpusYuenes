<?php

declare(strict_types=1);

namespace App\Domain;

use InvalidArgumentException;

/**
 * Value object rupiah untuk denda. Seluruh nilai uang pada sistem
 * perpustakaan disimpan sebagai bilangan bulat rupiah, bukan float.
 */
final readonly class Denda
{
    public function __construct(public int $rupiah)
    {
        if ($rupiah < 0) {
            throw new InvalidArgumentException('Nilai denda tidak boleh negatif.');
        }
    }

    public static function nol(): self
    {
        return new self(0);
    }

    public function tambah(self $lain): self
    {
        return new self($this->rupiah + $lain->rupiah);
    }

    public function lebihDari(self $lain): bool
    {
        return $this->rupiah > $lain->rupiah;
    }

    public function format(): string
    {
        return 'Rp ' . number_format($this->rupiah, 0, ',', '.');
    }
}