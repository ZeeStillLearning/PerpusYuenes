<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Konfigurasi Perpustakaan
|--------------------------------------------------------------------------
| Seluruh angka bisnis diletakkan di sini, bukan hard-coded di service.
*/

return [
    'nama_perpustakaan' => env('PERPUS_NAMA', 'Perpustakaan Kampus'),

    // AB-1
    'batas_pinjam' => (int) env('PERPUS_BATAS_PINJAM', 3),

    // AB-4
    'lama_pinjam_hari' => (int) env('PERPUS_LAMA_PINJAM', 7),

    // AB-5
    'denda_per_hari' => (int) env('PERPUS_DENDA_PER_HARI', 1000),

    // AB-6
    'lama_perpanjangan_hari' => (int) env('PERPUS_LAMA_PERPANJANGAN', 7),

    // Dipakai middleware JamLayanan (AB-9)
    'jam' => [
        'buka' => env('PERPUS_JAM_BUKA', '08:00'),
        'tutup' => env('PERPUS_JAM_TUTUP', '20:00'),
    ],

    /*
    | Daftar kunci API anggota/petugas. HANYA UNTUK LATIHAN.
    | Autentikasi sesungguhnya memakai Sanctum di modul lanjutan.
    */
    'anggota' => [
        env('PERPUS_KUNCI_ANGGOTA', 'anggota-dev-001') => [
            'nama' => 'Rani Wulandari',
            'peran' => 'anggota',
        ],
        env('PERPUS_KUNCI_PETUGAS', 'petugas-dev-001') => [
            'nama' => 'Bagas Prakoso',
            'peran' => 'petugas',
        ],
    ],

    'premium' => [
        'daftar_anggota' => explode(',', (string) env('PERPUS_ANGGOTA_PREMIUM', 'Rani Wulandari')),
        'bonus_hari' => (int) env('PERPUS_PREMIUM_BONUS_HARI', 3),
    ],
];
