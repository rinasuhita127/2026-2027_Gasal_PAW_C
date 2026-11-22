<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once __DIR__ . '/config.php';

// Cegah browser menyimpan cache halaman,
// supaya tombol Back setelah logout tidak menampilkan halaman lama.
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

// 1. Belum login -> arahkan ke halaman login
if (!isset($_SESSION['id_user'])) {
    header("Location: " . BASE_URL . "/login.php");
    exit;
}

// 2. Pembatas hak akses berdasarkan level
//    Contoh: wajib_level([1]); -> hanya level 1 (owner) yang boleh
function wajib_level(array $levelDiizinkan)
{
    if (!in_array((int) $_SESSION['level'], $levelDiizinkan, true)) {
        header("Location: " . BASE_URL . "/index.php");
        exit;
    }
}
