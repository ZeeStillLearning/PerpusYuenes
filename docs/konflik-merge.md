# Dokumentasi Penyelesaian Konflik Merge

## 1. Penanda Konflik Sebelum Diselesaikan (Before)
Terdapat konflik pada berkas `src/routes/api.php` saat menggabungkan `main` ke cabang fitur:

<<<<<<< HEAD
    Route::get('/anggota', [AnggotaController::class, 'index'])
        ->name('anggota.index');
=======
    Route::get('/statistik', [StatistikController::class, 'index'])
        ->name('statistik.index');
>>>>>>> ffcef67229fa7e7dd542794479ecf6bd4cc79402

## 2. Hasil Setelah Konflik Diselesaikan (After)
Kedua rute dipertahankan dan disusun sejajar di dalam grup rute:

    Route::get('/anggota', [AnggotaController::class, 'index'])
        ->name('anggota.index');

    Route::get('/statistik', [StatistikController::class, 'index'])
        ->name('statistik.index');

## 3. Alasan Keputusan
Konflik terjadi karena dua pengembang menambahkan rute baru pada posisi awal grup middleware `anggota`. Keputusan yang diambil adalah **mempertahankan kedua rute**, karena rute `/anggota` diperlukan untuk fitur manajemen anggota dan rute `/statistik` diperlukan untuk laporan statistik aplikasi.