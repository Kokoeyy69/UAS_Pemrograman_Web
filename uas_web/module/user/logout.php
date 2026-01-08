<?php
/**
 * PROSES LOGOUT (PEMBERSIHAN SESI TOTAL)
 * Standar keamanan untuk mengakhiri akses pengguna.
 */

// 1. Pastikan session dimulai sebelum dihancurkan agar PHP mengenali sesi mana yang akan dihapus
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Kosongkan semua variabel $_SESSION di memori server
$_SESSION = [];

/**
 * 3. Hapus Cookie Sesi di Browser (Sangat Penting untuk Keamanan)
 * Ini memastikan ID sesi lama tidak bisa digunakan kembali oleh peretas.
 */
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Hancurkan data sesi di server secara fisik
session_destroy();

/**
 * 5. Alihkan pengguna ke halaman login
 * Pastikan URL mengarah ke module/user/index.php yang berfungsi sebagai login.
 */
header("Location: /uas_web/index.php/user/index");
exit;