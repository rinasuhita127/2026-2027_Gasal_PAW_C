<?php
include __DIR__ . "/../auth.php";
wajib_level([1]);
include __DIR__ . "/../koneksi.php";
include __DIR__ . "/../layout.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : (int)($_POST['id'] ?? 0);
$data = ['kode_barang' => '', 'nama_barang' => '', 'harga' => '', 'stok' => '', 'supplier_id' => 0];
$error = '';

if ($id > 0) {
    $stmt = mysqli_prepare($koneksi, "SELECT kode_barang, nama_barang, harga, stok, supplier_id FROM barang WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$row) { header("Location: index.php"); exit; }
    $data = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['kode_barang'] = trim($_POST['kode_barang'] ?? '');
    $data['nama_barang'] = trim($_POST['nama_barang'] ?? '');
    $data['harga']       = (int)($_POST['harga'] ?? 0);
    $data['stok']        = (int)($_POST['stok'] ?? 0);
    $data['supplier_id'] = (int)($_POST['supplier_id'] ?? 0);

    if ($data['kode_barang'] === '' || $data['nama_barang'] === '' || $data['supplier_id'] <= 0) {
        $error = 'Kode, nama, dan supplier wajib diisi.';
    } else {
        if ($id > 0) {
            $stmt = mysqli_prepare($koneksi, "UPDATE barang SET kode_barang=?, nama_barang=?, harga=?, stok=?, supplier_id=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "ssiiii", $data['kode_barang'], $data['nama_barang'], $data['harga'], $data['stok'], $data['supplier_id'], $id);
        } else {
            $stmt = mysqli_prepare($koneksi, "INSERT INTO barang (kode_barang, nama_barang, harga, stok, supplier_id) VALUES (?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "ssiii", $data['kode_barang'], $data['nama_barang'], $data['harga'], $data['stok'], $data['supplier_id']);
        }
        if (mysqli_stmt_execute($stmt)) { header("Location: index.php"); exit; }
        $error = mysqli_errno($koneksi) == 1062 ? 'Kode barang sudah dipakai.' : 'Gagal menyimpan data: ' . mysqli_error($koneksi);
    }
}
$supplier = mysqli_query($koneksi, "SELECT id, nama FROM supplier ORDER BY nama");
page_start($id ? 'Edit Barang' : 'Tambah Barang');
?>
<h3><?= $id ? 'Edit' : 'Tambah' ?> Data Master Barang</h3>
<hr>
<?php if ($error): ?><div class="alert alert-danger"><?= h($error) ?></div><?php endif; ?>
<form method="post">
    <input type="hidden" name="id" value="<?= $id ?>">
    <div class="form-group"><label>Kode Barang</label>
        <input type="text" name="kode_barang" class="form-control" maxlength="10" value="<?= h($data['kode_barang']) ?>" required></div>
    <div class="form-group"><label>Nama Barang</label>
        <input type="text" name="nama_barang" class="form-control" maxlength="100" value="<?= h($data['nama_barang']) ?>" required></div>
    <div class="form-group"><label>Harga</label>
        <input type="number" name="harga" min="0" class="form-control" value="<?= h($data['harga']) ?>" required></div>
    <div class="form-group"><label>Stok</label>
        <input type="number" name="stok" min="0" class="form-control" value="<?= h($data['stok']) ?>" required></div>
    <div class="form-group"><label>Supplier</label>
        <select name="supplier_id" class="form-control" required>
            <option value="">-- pilih supplier --</option>
            <?php while ($s = mysqli_fetch_assoc($supplier)): ?>
                <option value="<?= (int)$s['id'] ?>" <?= (int)$data['supplier_id'] === (int)$s['id'] ? 'selected' : '' ?>><?= h($s['nama']) ?></option>
            <?php endwhile; ?>
        </select></div>
    <button class="btn btn-success"><?= $id ? 'Update' : 'Simpan' ?></button>
    <a href="index.php" class="btn btn-danger">Batal</a>
</form>
<?php page_end(); ?>
