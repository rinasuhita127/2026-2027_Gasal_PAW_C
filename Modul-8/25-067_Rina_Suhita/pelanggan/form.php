<?php
include __DIR__ . "/../auth.php";
wajib_level([1]);
include __DIR__ . "/../koneksi.php";
include __DIR__ . "/../layout.php";

$id = $_GET['id'] ?? ($_POST['id_lama'] ?? '');
$edit = ($id !== '');
$data = ['id' => '', 'nama' => '', 'jenis_kelamin' => 'L', 'telp' => '', 'alamat' => ''];
$error = '';

if ($edit) {
    $stmt = mysqli_prepare($koneksi, "SELECT id, nama, jenis_kelamin, telp, alamat FROM pelanggan WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "s", $id);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$row) { header("Location: index.php"); exit; }
    $data = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['id']            = $edit ? $id : trim($_POST['id'] ?? '');
    $data['nama']          = trim($_POST['nama'] ?? '');
    $data['jenis_kelamin'] = ($_POST['jenis_kelamin'] ?? 'L') === 'P' ? 'P' : 'L';
    $data['telp']          = trim($_POST['telp'] ?? '');
    $data['alamat']        = trim($_POST['alamat'] ?? '');

    if ($data['id'] === '' || $data['nama'] === '' || $data['telp'] === '' || $data['alamat'] === '') {
        $error = 'Semua kolom wajib diisi.';
    } else {
        if ($edit) {
            $stmt = mysqli_prepare($koneksi, "UPDATE pelanggan SET nama=?, jenis_kelamin=?, telp=?, alamat=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "sssss", $data['nama'], $data['jenis_kelamin'], $data['telp'], $data['alamat'], $id);
        } else {
            $stmt = mysqli_prepare($koneksi, "INSERT INTO pelanggan (id, nama, jenis_kelamin, telp, alamat) VALUES (?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sssss", $data['id'], $data['nama'], $data['jenis_kelamin'], $data['telp'], $data['alamat']);
        }
        if (mysqli_stmt_execute($stmt)) { header("Location: index.php"); exit; }
        $error = mysqli_errno($koneksi) == 1062 ? 'ID pelanggan sudah dipakai.' : 'Gagal menyimpan data: ' . mysqli_error($koneksi);
    }
}
page_start($edit ? 'Edit Pelanggan' : 'Tambah Pelanggan');
?>
<h3><?= $edit ? 'Edit' : 'Tambah' ?> Data Master Pelanggan</h3>
<hr>
<?php if ($error): ?><div class="alert alert-danger"><?= h($error) ?></div><?php endif; ?>
<form method="post">
    <input type="hidden" name="id_lama" value="<?= h($edit ? $id : '') ?>">
    <div class="form-group"><label>ID Pelanggan</label>
        <input type="text" name="id" class="form-control" maxlength="20" value="<?= h($data['id']) ?>" <?= $edit ? 'readonly' : 'required' ?>></div>
    <div class="form-group"><label>Nama</label>
        <input type="text" name="nama" class="form-control" maxlength="100" value="<?= h($data['nama']) ?>" required></div>
    <div class="form-group"><label>Jenis Kelamin</label>
        <select name="jenis_kelamin" class="form-control">
            <option value="L" <?= $data['jenis_kelamin'] === 'L' ? 'selected' : '' ?>>Laki-laki</option>
            <option value="P" <?= $data['jenis_kelamin'] === 'P' ? 'selected' : '' ?>>Perempuan</option>
        </select></div>
    <div class="form-group"><label>Telp</label>
        <input type="text" name="telp" class="form-control" maxlength="12" value="<?= h($data['telp']) ?>" required></div>
    <div class="form-group"><label>Alamat</label>
        <input type="text" name="alamat" class="form-control" value="<?= h($data['alamat']) ?>" required></div>
    <button class="btn btn-success"><?= $edit ? 'Update' : 'Simpan' ?></button>
    <a href="index.php" class="btn btn-danger">Batal</a>
</form>
<?php page_end(); ?>
