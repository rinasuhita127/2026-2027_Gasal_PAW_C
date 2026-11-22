<?php
session_start();
include_once __DIR__ . '/config.php';
include __DIR__ . '/koneksi.php';

// Hanya terima request dari form (POST)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . BASE_URL . "/login.php");
    exit;
}

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Password di database disimpan dalam bentuk MD5
$hash = md5($password);

// Prepared statement: mencegah SQL Injection
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT id_user, username, nama, level FROM user WHERE username = ? AND password = ?"
);
mysqli_stmt_bind_param($stmt, "ss", $username, $hash);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($hasil)) {
    // Login berhasil -> SET SESSION
    session_regenerate_id(true);
    $_SESSION['id_user']  = $row['id_user'];
    $_SESSION['username'] = $row['username'];
    $_SESSION['nama']     = $row['nama'];
    $_SESSION['level']    = (int) $row['level'];

    header("Location: " . BASE_URL . "/index.php");
} else {
    // Login gagal
    header("Location: " . BASE_URL . "/login.php?error=1");
}
exit;
