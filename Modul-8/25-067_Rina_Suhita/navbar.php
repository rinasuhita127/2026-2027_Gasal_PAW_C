<nav class="navbar navbar-expand navbar-dark bg-primary">
    <a class="navbar-brand" href="<?= BASE_URL ?>/index.php">Sistem Penjualan</a>

    <ul class="navbar-nav mr-auto">
        <li class="nav-item">
            <a class="nav-link" href="<?= BASE_URL ?>/index.php">Home</a>
        </li>

        <?php if ($_SESSION['level'] == 1): ?>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">Data Master</a>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="<?= BASE_URL ?>/barang/">Data Barang</a>
                <a class="dropdown-item" href="<?= BASE_URL ?>/supplier/">Data Supplier</a>
                <a class="dropdown-item" href="<?= BASE_URL ?>/pelanggan/">Data Pelanggan</a>
                <a class="dropdown-item" href="<?= BASE_URL ?>/user/">Data User</a>
            </div>
        </li>
        <?php endif; ?>

        <li class="nav-item">
            <a class="nav-link" href="<?= BASE_URL ?>/transaksi/">Transaksi</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?= BASE_URL ?>/laporan/">Laporan</a>
        </li>
    </ul>

    <ul class="navbar-nav">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
                <?= htmlspecialchars($_SESSION['nama']) ?>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="<?= BASE_URL ?>/logout.php">Logout</a>
            </div>
        </li>
    </ul>
</nav>
