<?php
session_start();
include_once __DIR__ . '/config.php';

session_unset();    // hapus semua variabel session
session_destroy();  // hancurkan session di server

header("Location: " . BASE_URL . "/login.php");
exit;
