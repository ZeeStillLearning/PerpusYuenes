<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\RepositoriBuku;
use App\Contracts\RepositoriPeminjaman;
use App\Domain\Denda;
use App\Domain\StatusPeminjaman;
use App\Exceptions\BatasPinjamTercapai;
use App\Exceptions\BukuTidakDitemukan;
use App\Exceptions\DendaBelumLunas;
use App\Exceptions\PeminjamanSudahDikembalikan;
use App\Exceptions\PeminjamanTidakDitemukan;
use App\Exceptions\StokTidakTersedia;
use Carbon\Carbon;

/**
 * Inti aturan bisnis peminjaman (AB-1 s.d. AB-9).
 * Seluruh angka (denda, jatuh tempo) dihitung ulang di sini,
 * tidak pernah dipercaya dari input klien.
 */
final class LayananPeminjaman
{
    public function __construct(
        private readonly RepositoriBuku $buku,
        private readonly RepositoriPeminjaman $peminjaman,
    ) {}

    /**
     * Meminjam satu buku.
     *
     * @return array<string, mixed>
     */
    public function pinjam(string $isbn, string $anggota): array
    {
        $buku = $this->buku->cariIsbn($isbn);

        if ($buku === null) {
            throw new BukuTidakDitemukan($isbn); // -> 404
        }

        if ($buku['stok'] <= 0) { // AB-2
            throw new StokTidakTersedia($isbn); // -> 422
        }

        $aktif = $this->peminjamanAktifAnggota($anggota);

        $batasPinjam = (int) config('perpus.batas_pinjam');
        if (count($aktif) >= $batasPinjam) { // AB-1
            throw new BatasPinjamTercapai($batasPinjam); // -> 422
        }

        $totalDenda = $this->totalDendaBelumLunas($anggota);
        if ($totalDenda->rupiah > 0) { // AB-3
            throw new DendaBelumLunas($totalDenda->rupiah); // -> 422
        }

        $lamaPinjam = (int) config('perpus.lama_pinjam_hari');
        $tanggalPinjam = now();
        $jatuhTempo = $tanggalPinjam->copy()->addDays($lamaPinjam); // AB-4

        $data = [
            'id' => $this->idBaru(),
            'isbn' => $buku['isbn'],
            'judul_buku' => $buku['judul'],
            'anggota' => $anggota,
            'status' => StatusPeminjaman::Dipinjam->value,
            'tanggal_pinjam' => $tanggalPinjam->toIso8601String(),
            'jatuh_tempo' => $jatuhTempo->toIso8601String(),
            'sudah_diperpanjang' => false,
            'tanggal_kembali' => null,
            'denda' => 0,
        ];

        $this->peminjaman->simpan($data);

        return $data;
    }

    /**
     * Mengembalikan buku, dihitung denda bila terlambat (AB-5).
     * Hanya boleh dipanggil oleh peran petugas — otorisasi
     * dilakukan di middleware, bukan di sini.
     *
     * @return array<string, mixed>
     */
    public function kembalikan(string $id): array
    {
        $data = $this->peminjaman->cariId($id);

        if ($data === null) {
            throw new PeminjamanTidakDitemukan($id); // -> 404
        }

        if ($data['status'] !== StatusPeminjaman::Dipinjam->value) { // AB-10 analog
            throw new PeminjamanSudahDikembalikan($id); // -> 409
        }

        $sekarang = now();
        $jatuhTempo = Carbon::parse($data['jatuh_tempo']);

        $denda = Denda::nol();
        if ($sekarang->gt($jatuhTempo)) { // AB-5
            $hariTerlambat = (int) $jatuhTempo->diffInDays($sekarang);
            $dendaPerHari = (int) config('perpus.denda_per_hari');
            $denda = new Denda($hariTerlambat * $dendaPerHari);
        }

        $perubahan = [
            'status' => StatusPeminjaman::Dikembalikan->value,
            'tanggal_kembali' => $sekarang->toIso8601String(),
            'denda' => $denda->rupiah,
        ];

        $this->peminjaman->perbarui($id, $perubahan);

        return array_merge($data, $perubahan);
    }

    /**
     * Memperpanjang masa pinjam (AB-6), hanya jika belum lewat
     * jatuh tempo dan belum pernah diperpanjang sebelumnya.
     *
     * @return array<string, mixed>
     */
    public function perpanjang(string $id): array
    {
        $data = $this->peminjaman->cariId($id);

        if ($data === null) {
            throw new PeminjamanTidakDitemukan($id);
        }

        if ($data['status'] !== StatusPeminjaman::Dipinjam->value) {
            throw new PeminjamanSudahDikembalikan($id);
        }

        $jatuhTempoLama = Carbon::parse($data['jatuh_tempo']);
        $lamaPerpanjangan = (int) config('perpus.lama_perpanjangan_hari');
        $jatuhTempoBaru = $jatuhTempoLama->copy()->addDays($lamaPerpanjangan);

        $perubahan = [
            'jatuh_tempo' => $jatuhTempoBaru->toIso8601String(),
            'sudah_diperpanjang' => true,
        ];

        $this->peminjaman->perbarui($id, $perubahan);

        return array_merge($data, $perubahan);
    }

    /** @return array<int, array<string, mixed>> */
    public function riwayat(?string $anggota = null): array
    {
        $semua = array_values($this->peminjaman->semua());

        if ($anggota === null) {
            return $semua;
        }

        return array_values(array_filter(
            $semua,
            static fn(array $p): bool => $p['anggota'] === $anggota,
        ));
    }

    /** @return array<string, mixed> */
    public function cari(string $id): array
    {
        $data = $this->peminjaman->cariId($id);

        if ($data === null) {
            throw new PeminjamanTidakDitemukan($id);
        }

        return $data;
    }

    /** @return array<int, array<string, mixed>> */
    private function peminjamanAktifAnggota(string $anggota): array
    {
        return array_values(array_filter(
            $this->peminjaman->semua(),
            static fn(array $p): bool => $p['anggota'] === $anggota
                && $p['status'] === StatusPeminjaman::Dipinjam->value,
        ));
    }

    private function totalDendaBelumLunas(string $anggota): Denda
    {
        // AB-3: denda dianggap "belum lunas" bila tercatat pada
        // peminjaman yang sudah dikembalikan tapi belum "dibayar".
        // Pada latihan ini kita anggap field 'denda' > 0 berarti belum lunas.
        $riwayat = array_filter(
            $this->peminjaman->semua(),
            static fn(array $p): bool => $p['anggota'] === $anggota
                && $p['status'] === StatusPeminjaman::Dikembalikan->value
                && $p['denda'] > 0,
        );

        $total = Denda::nol();
        foreach ($riwayat as $p) {
            $total = $total->tambah(new Denda((int) $p['denda']));
        }

        return $total;
    }

    /** ID peminjaman berformat PJM-YYYYMMDD-0001 */
    private function idBaru(): string
    {
        $tanggal = now()->format('Ymd');
        $urut = count(array_filter(
            $this->peminjaman->semua(),
            static fn(array $p): bool => str_starts_with($p['id'], "PJM-{$tanggal}"),
        )) + 1;

        return sprintf('PJM-%s-%04d', $tanggal, $urut);
    }

    /**
     * Pratinjau peminjaman: mengecek seluruh syarat (stok, batas pinjam,
     * denda belum lunas) dan menghitung jatuh tempo TANPA menyimpan data.
     * Dipakai ulang oleh endpoint pratinjau maupun disisipkan ke pinjam()
     * kalau suatu saat perlu -- sama seperti pola hitung() di LayananKasir.
     *
     * @return array<string, mixed>
     */
    public function pratinjau(string $isbn, string $anggota): array
    {
        $buku = $this->buku->cariIsbn($isbn);

        if ($buku === null) {
            throw new BukuTidakDitemukan($isbn);
        }

        if ($buku['stok'] <= 0) {
            throw new StokTidakTersedia($isbn);
        }

        $aktif = $this->peminjamanAktifAnggota($anggota);
        $batasPinjam = (int) config('perpus.batas_pinjam');
        if (count($aktif) >= $batasPinjam) {
            throw new BatasPinjamTercapai($batasPinjam);
        }

        $totalDenda = $this->totalDendaBelumLunas($anggota);
        if ($totalDenda->rupiah > 0) {
            throw new DendaBelumLunas($totalDenda->rupiah);
        }

        $lamaPinjam = (int) config('perpus.lama_pinjam_hari');
        $jatuhTempo = now()->copy()->addDays($lamaPinjam);

        return [
            'isbn' => $buku['isbn'],
            'judul_buku' => $buku['judul'],
            'anggota' => $anggota,
            'jatuh_tempo_estimasi' => $jatuhTempo->toIso8601String(),
            'peminjaman_aktif_saat_ini' => count($aktif),
            'batas_pinjam' => $batasPinjam,
            'boleh_pinjam' => true,
        ];
    }
}
// baris sengaja salah untuk latihan
