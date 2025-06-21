<?php

return [
    // Pesan umum
    'success' => 'Berhasil',
    'error' => 'Terjadi kesalahan',
    'not_found' => 'Tidak ditemukan',
    'unauthorized' => 'Tidak memiliki akses',
    'validation_error' => 'Data tidak valid',
    'server_error' => 'Kesalahan server',

    // Autentikasi
    'auth' => [
        'login_success' => 'Login berhasil',
        'login_failed' => 'Email atau password salah',
        'logout_success' => 'Logout berhasil',
        'register_success' => 'Registrasi berhasil',
        'register_failed' => 'Registrasi gagal',
        'token_invalid' => 'Token tidak valid',
        'token_expired' => 'Token sudah kadaluarsa',
        'email_required' => 'Email wajib diisi',
        'password_required' => 'Password wajib diisi',
        'name_required' => 'Nama wajib diisi',
        'email_invalid' => 'Format email tidak valid',
        'password_min' => 'Password minimal 6 karakter',
        'email_exists' => 'Email sudah terdaftar',
    ],

    // Game
    'game' => [
        'id_check_success' => 'ID game ditemukan',
        'id_check_failed' => 'ID game tidak ditemukan',
        'id_required' => 'ID game wajib diisi',
        'zone_id_required' => 'Zone ID wajib diisi untuk game ini',
        'game_code_required' => 'Kode game wajib diisi',
        'game_not_supported' => 'Game tidak didukung',
        'provider_error' => 'Kesalahan pada provider',
        'all_providers_failed' => 'Semua provider gagal',
        'username_found' => 'Username ditemukan: :username',
        'checking_id' => 'Sedang mengecek ID game...',
    ],

    // Transaksi
    'transaction' => [
        'topup_success' => 'Top-up berhasil',
        'topup_failed' => 'Top-up gagal',
        'topup_pending' => 'Top-up sedang diproses',
        'amount_required' => 'Jumlah pembayaran wajib diisi',
        'product_required' => 'Produk wajib dipilih',
        'insufficient_balance' => 'Saldo tidak mencukupi',
        'transaction_not_found' => 'Transaksi tidak ditemukan',
        'transaction_cancelled' => 'Transaksi dibatalkan',
    ],

    // Provider
    'provider' => [
        'apigames' => 'ApiGames',
        'codashop' => 'Codashop',
        'duniagames' => 'Dunia Games',
        'not_supported' => 'Provider tidak didukung',
        'connection_error' => 'Koneksi ke provider gagal',
        'timeout' => 'Timeout koneksi ke provider',
    ],

    // Games
    'games' => [
        'mobilelegend' => 'Mobile Legends',
        'mobilelegends' => 'Mobile Legends: Bang Bang',
        'freefire' => 'Free Fire',
        'pubgmobile' => 'PUBG Mobile',
        'valorant' => 'Valorant',
        'genshinimpact' => 'Genshin Impact',
        'aov' => 'Arena of Valor',
        'wildrift' => 'League of Legends: Wild Rift',
        'honkaiimpact' => 'Honkai Impact 3rd',
        'ragnarokmobile' => 'Ragnarok M: Eternal Love',
        'pointblank' => 'Point Blank',
    ],

    // Fields
    'fields' => [
        'user_id' => 'ID Pengguna',
        'zone_id' => 'Zone ID',
        'server_id' => 'Server ID',
        'character_name' => 'Nama Karakter',
        'email' => 'Email',
        'phone' => 'Nomor Telepon',
    ],

    // API Messages
    'api' => [
        'welcome' => 'Selamat datang di API PPOB Game',
        'version' => 'Versi API: :version',
        'documentation' => 'Dokumentasi tersedia di: :url',
        'rate_limit_exceeded' => 'Batas permintaan terlampaui',
        'invalid_request' => 'Permintaan tidak valid',
        'missing_parameter' => 'Parameter :parameter wajib diisi',
        'invalid_parameter' => 'Parameter :parameter tidak valid',
    ],

    // Status
    'status' => [
        'active' => 'Aktif',
        'inactive' => 'Tidak Aktif',
        'pending' => 'Menunggu',
        'processing' => 'Sedang Diproses',
        'completed' => 'Selesai',
        'failed' => 'Gagal',
        'cancelled' => 'Dibatalkan',
    ],
];
