# Alur Request — POST /api/v1/perpus/peminjaman

Aplikasi anggota
|
| POST /api/v1/perpus/peminjaman
| X-API-Key: anggota-dev-001
| { "isbn": "ISBN-002" }
v

1. public/index.php ................ single entry point
2. bootstrap/app.php ............... build aplikasi, middleware didaftarkan
3. Service Provider ................ RepositoriBuku & RepositoriPeminjaman di-bind
4. Middleware global (grup api) .... CatatRequest -> mulai stopwatch, buat X-Request-Id
5. Router .......................... cocokkan POST + URI -> PeminjamanController@store
6. Middleware rute (berurutan):
   a. anggota ................... validasi X-API-Key, sisipkan identitas pengguna
   b. jam.layanan ............... tolak bila di luar jam operasional -> 403
7. Controller ...................... validasi bentuk masukan (isbn wajib), panggil LayananPeminjaman
8. Service (LayananPeminjaman) ..... AB-1 s.d. AB-9 dihitung di sini: - cek stok buku (AB-2) - cek batas pinjam aktif (AB-1) - cek total denda belum lunas (AB-3) - hitung jatuh tempo (AB-4)
9. Repository (RepositoriPeminjamanBerkas) .. data peminjaman disimpan ke peminjaman.json
10. Response 201 ................... dibentuk controller, header Location menuju
    detail peminjaman yang baru dibuat
    |
    v arah balik: middleware dilewati dengan URUTAN TERBALIK
11. jam.layanan (tidak melakukan apa-apa pada arah balik)
12. anggota (tidak melakukan apa-apa pada arah balik)
13. CatatRequest ................... hitung durasi, tulis log (jika >100ms atau 4xx/5xx),
    tempel header X-Request-Id & X-Response-Time
    |
    v
    Aplikasi anggota menerima 201 Created
