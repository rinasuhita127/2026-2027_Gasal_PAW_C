<?php
include __DIR__ . "/../auth.php";
wajib_level([1]);
include __DIR__ . "/../koneksi.php";
include __DIR__ . "/../layout.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : (int)($_POST['id'] ?? 0);
$data = ['username' => '', 'nama' => '', 'alamat' => '', 'hp' => '', 'level' => 2];
$error = '';

if ($id > 0) {
    $stmt = mysqli_prepare($koneksi, "SELECT username, nama, alamat, hp, level FROM user WHERE id_user = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$row) { header("Location: index.php"); exit; }
    $data = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['username'] = trim($_POST['username'] ?? '');
    $data['nama']     = trim($_POST['nama'] ?? '');
    $data['alamat']   = trim($_POST['alamat'] ?? '');
    $data['hp']       = trim($_POST['hp'] ?? '');
    $data['level']    = (int)($_POST['level'] ?? 2) === 1 ? 1 : 2;
    $password         = $_POST['password'] ?? '';

    if ($data['username'] === '' || $data['nama'] === '' || ($id === 0 && $password === '')) {
        $error = 'Username, nama, dan password (untuk user baru) wajib diisi.';
    } else {
        if ($id > 0) {
            if ($password !== '') {
                $hash = md5($password);
                $stmt = mysqli_prepare($koneksi, "UPDATE user SET username=?, password=?, nama=?, alamat=?, hp=?, level=? WHERE id_user=?");
                mysqli_stmt_bind_param($stmt, "sssssii", $data['username'], $hash, $data['nama'], $data['alamat'], $data['hp'], $data['level'], $id);
            } else {
                $stmt = mysqli_prepare($koneksi, "UPDATE user SET username=?, nama=?, alamat=?, hp=?, level=? WHERE id_user=?");
                mysqli_stmt_bind_param($stmt, "ssssii", $data['username'], $data['nama'], $data['alamat'], $data['hp'], $data['level'], $id);
            }
        } else {
            $hash = md5($password);
            $stmt = mysqli_prepare($koneksi, "INSERT INTO user (username, password, nama, alamat, hp, level) VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sssssi", $data['username'], $hash, $data['nama'], $data['alamat'], $data['hp'], $data['level']);
        }
        if (mysqli_stmt_execute($stmt)) {
            // jika mengedit akun sendiri, sinkronkan session
            if ($id === (int)$_SESSION['id_user']) {
                $_SESSION['username'] = $data['username'];
                $_SESSION['nama']     = $data['nama'];
                $_SESSION['level']    = $data['level'];
            }
            header("Location: index.php"); exit;
        }
        $error = mysqli_errno($koneksi) == 1062 ? 'Username sudah dipakai.' : 'Gagal menyimpan data: ' . mysqli_error($koneksi);
    }
}
page_start($id ? 'Edit User' : 'Tambah User');
?>
<h3><?= $id ? 'Edit' : 'Tambah' ?> Data Master User</h3>
<hr>
<?php if ($error): ?><div class="alert alert-danger"><?= h($error) ?></div><?php endif; ?>
<form method="post">
    <input type="hidden" name="id" value="<?= $id ?>">
    <div class="form-group"><label>Username</label>
        <input type="text" name="username" class="form-control" maxlength="30" value="<?= h($data['username']) ?>" required></div>
    <div class="form-group"><label>Password <?= $id ? '<small class="text-muted">(kosongkan jika tidak diganti)</small>' : '' ?></label>
        <input type="password" name="password" class="form-control" <?= $id ? '' : 'required' ?>></div>
    <div class="form-group"><label>Nama</label>
        <input type="text" name="nama" class="form-control" maxlength="50" value="<?= h($data['nama']) ?>" required></div>
    <div class="form-group"><label>Alamat</label>
        <input type="text" name="alamat" class="form-control" maxlength="150" value="<?= h($data['alamat']) ?>"></div>
    <div class="form-group"><label>HP</label>
        <input type="text" name="hp" class="form-control" maxlength="20" value="<?= h($data['hp']) ?>"></div>
    <div class="form-group"><label>Level</label>
        <select name="level" class="form-control">
            <option value="1" <?= (int)$data['level'] === 1 ? 'selected' : '' ?>>1 - Owner</option>
            <option value="2" <?= (int)$data['level'] === 2 ? 'selected' : '' ?>>2 - Kasir</option>
        </select></div>
    <button class="btn btn-success"><?= $id ? 'Update' : 'Simpan' ?></button>
    <a href="index.php" class="btn btn-danger">Batal</a>
</form>
<?php page_end(); ?>
