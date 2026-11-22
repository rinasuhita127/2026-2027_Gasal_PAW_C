<?php
include __DIR__ . "/../auth.php";
wajib_level([1]);
include __DIR__ . "/../koneksi.php";
include __DIR__ . "/../layout.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : (int)($_POST['id'] ?? 0);
$data = ['nama' => '', 'telp' => '', 'alamat' => ''];
$error = '';

if ($id > 0) {
    $stmt = mysqli_prepare($koneksi, "SELECT nama, telp, alamat FROM supplier WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$row) { header("Location: index.php"); exit; }
    $data = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['nama']   = trim($_POST['nama'] ?? '');
    $data['telp']   = trim($_POST['telp'] ?? '');
    $data['alamat'] = trim($_POST['alamat'] ?? '');

    if ($data['nama'] === '' || $data['telp'] === '' || $data['alamat'] === '') {
        $error = 'Semua kolom wajib diisi.';
    } else {
        if ($id > 0) {
            $stmt = mysqli_prepare($koneksi, "UPDATE supplier SET nama=?, telp=?, alamat=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "sssi", $data['nama'], $data['telp'], $data['alamat'], $id);
        } else {
            $stmt = mysqli_prepare($koneksi, "INSERT INTO supplier (nama, telp, alamat) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sss", $data['nama'], $data['telp'], $data['alamat']);
        }
        if (mysqli_stmt_execute($stmt)) { header("Location: index.php"); exit; }
        $error = 'Gagal menyimpan data: ' . mysqli_error($koneksi);
    }
}
page_start($id ? 'Edit Supplier' : 'Tambah Supplier');
?>
<h3><?= $id ? 'Edit' : 'Tambah' ?> Data Master Supplier</h3>
<hr>
<?php if ($error): ?><div class="alert alert-danger"><?= h($error) ?></div><?php endif; ?>
<form method="post">
    <input type="hidden" name="id" value="<?= $id ?>">
    <div class="form-group"><label>Nama</label>
        <input type="text" name="nama" class="form-control" maxlength="100" value="<?= h($data['nama']) ?>" required></div>
    <div class="form-group"><label>Telp</label>
        <input type="text" name="telp" class="form-control" maxlength="12" value="<?= h($data['telp']) ?>" required></div>
    <div class="form-group"><label>Alamat</label>
        <input type="text" name="alamat" class="form-control" value="<?= h($data['alamat']) ?>" required></div>
    <button class="btn btn-success"><?= $id ? 'Update' : 'Simpan' ?></button>
    <a href="index.php" class="btn btn-danger">Batal</a>
</form>
<?php page_end(); ?>
