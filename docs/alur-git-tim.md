# Dokumentasi Alur Kerja Git Tim Perpustakaan (Soal 10)

Dokumen ini mencatat pembagian tugas, struktur cabang (branch), urutan penggabungan Pull Request (PR), dan grafik riwayat commit pada proyek Sistem Peminjaman Perpustakaan.

---

## 1. Tabel Pembagian Tugas & Branch Tim

| Anggota | Soal | Bagian yang Dikerjakan | Nama Branch |
|---|---|---|---|
| **FauZee** | 1, 4 | Endpoint Pratinjau Peminjaman & Bonus Masa Pinjam Member Premium | `feature/latihan-1-4-fauzee` |
| **lelyonn** | 2, 3 | Middleware `TolakUserAgentKosong` & `CatatRequest` Selektif | `feature/latihan-2-3-lelyonn` |
| **shirarta** | 5, 6 | Layanan & Repositori Anggota (`LayananAnggota`) & Diagram Alur Request | `feature/latihan-5-6-shirarta` |
| **zakinurrohimm** | 7, 9 | Dokumentasi Koleksi Postman & Latihan `git revert` | `feature/latihan-7-9-zakinurrohimm` |
| **Anggota 4** | 8, 10 | Simulasi Konflik Git & Dokumentasi Alur Kerja Tim | `feature/latihan-8-10-anggota4` |

---

## 2. Urutan Penggabungan Pull Request (Merge Sequence)

1. **`feature/latihan-5-6-shirarta` (Soal 5 & 6):** Digabungkan paling awal karena menambahkan contract dan repositori baru yang independen.
2. **`feature/latihan-1-4-fauzee` & `feature/latihan-2-3-lelyonn`:** Digabungkan secara paralel karena menyunting berkas yang berbeda.
3. **`feature/latihan-7-9-zakinurrohimm` (Soal 7 & 9):** Digabungkan setelah seluruh endpoint stabil agar dokumentasi Postman lengkap.
4. **`feature/latihan-8-10-anggota4` (Soal 8 & 10):** Digabungkan paling akhir untuk merekam seluruh riwayat commit tim.