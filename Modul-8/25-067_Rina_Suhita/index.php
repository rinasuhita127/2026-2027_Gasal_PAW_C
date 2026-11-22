<?php
include __DIR__ . '/auth.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home - Sistem Penjualan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css">
</head>
<body>

<?php include __DIR__ . '/navbar.php'; ?>

<div class="container mt-4">
    <h3>Selamat datang, <?= htmlspecialchars($_SESSION['nama']) ?></h3>
    <p class="text-muted">
        Anda login sebagai
        <strong><?= $_SESSION['level'] == 1 ? 'Owner (level 1)' : 'Kasir (level 2)' ?></strong>.
    </p>
</div>

<!-- jQuery + Bootstrap JS: wajib ada agar dropdown menu berfungsi -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
