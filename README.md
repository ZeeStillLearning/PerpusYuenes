# PerpusYuenes — REST API Sistem Peminjaman Buku Perpustakaan

Studi kasus tugas kelompok mata kuliah Praktik Pemrograman Back End (D3 Teknik Informatika, Sekolah Vokasi UNS). Menggantikan contoh POS Barokah Mart dari modul, dengan pola arsitektur yang sama: Routes -> Middleware -> Controller -> Service -> Domain/Repository.

## Tim

| Anggota | Bagian |
|---|---|
| FauZee | Soal 1, 4 (Latihan) |
| lelyonn | Soal 2, 3 (Latihan) |
| shirarta | Soal 5, 6 (Latihan) |
| zakinurrohimm | Soal 7, 9 (Latihan) |
| _(anggota 5)_ | Soal 8, 10 (Latihan) |

## Menjalankan Proyek

**Environment:** Windows + Docker Desktop (PowerShell), project Laravel ada di subfolder `src/`.

```powershell
git clone https://github.com/ZeeStillLearning/PerpusYuenes.git
cd PerpusYuenes
docker compose up -d --build
docker compose exec app bash
```

Composer `create-project` Laravel berjalan otomatis saat container pertama kali `up` jika `src/artisan` belum ada.

Server berjalan di `http://127.0.0.1:8080` (port host 8080 dipetakan ke port 8000 container).

## Catatan Testing di PowerShell

Gunakan `curl.exe` (bukan alias `curl` bawaan PowerShell yang sebenarnya adalah `Invoke-WebRequest`). Untuk body JSON, simpan ke file dulu (hindari `Out-File -Encoding utf8` karena menambahkan BOM yang merusak parsing JSON Laravel), lalu kirim dengan `-d "@file.json"`.

## Aturan Bisnis

Lihat `docs/aturan-bisnis.md` untuk daftar lengkap AB-1 s.d. AB-10.

## Kontrak Endpoint

Lihat `docs/kontrak-endpoint.md`.