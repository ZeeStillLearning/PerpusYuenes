<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\RepositoriPeminjaman;
use Illuminate\Support\Facades\Storage;

/**
 * Menyimpan peminjaman pada satu berkas JSON di storage/app/private/perpus.
 * Hanya untuk latihan, tidak ada penguncian berkas.
 */
final class RepositoriPeminjamanBerkas implements RepositoriPeminjaman
{
    private const BERKAS = 'perpus/peminjaman.json';

    public function semua(): array
    {
        if (! Storage::disk('local')->exists(self::BERKAS)) {
            return [];
        }

        $isi = Storage::disk('local')->get(self::BERKAS);

        return json_decode($isi ?? '[]', true, 512, JSON_THROW_ON_ERROR) ?: [];
    }

    public function cariId(string $id): ?array
    {
        return $this->semua()[$id] ?? null;
    }

    public function simpan(array $peminjaman): void
    {
        $semua = $this->semua();
        $semua[$peminjaman['id']] = $peminjaman;
        $this->tulis($semua);
    }

    public function perbarui(string $id, array $perubahan): void
    {
        $semua = $this->semua();

        if (! isset($semua[$id])) {
            return;
        }

        $semua[$id] = array_merge($semua[$id], $perubahan);
        $this->tulis($semua);
    }

    /** @param array<string, array<string, mixed>> $data */
    private function tulis(array $data): void
    {
        Storage::disk('local')->put(
            self::BERKAS,
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
        );
    }
}
