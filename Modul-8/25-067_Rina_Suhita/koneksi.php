<?php
mysqli_report(MYSQLI_REPORT_OFF);
$koneksi = mysqli_connect("localhost", "root", "", "penjualan_modul8");

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error() .
        "<br>Pastikan database <b>penjualan_modul8</b> sudah di-import (file penjualan_modul8.sql).");
}
mysqli_set_charset($koneksi, "utf8mb4");

function h($teks) {
    return htmlspecialchars((string)$teks, ENT_QUOTES, 'UTF-8');
}

function rupiah($angka) {
    return "Rp " . number_format((int)$angka, 0, ',', '.');
}
