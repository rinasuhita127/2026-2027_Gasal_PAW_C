<?php
include __DIR__ . "/../auth.php";
wajib_level([1]);
include __DIR__ . "/../koneksi.php";
include __DIR__ . "/../layout.php";

$pesan = ''; $tipe = 'danger';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_id'])) {
    $id = (int)$_POST['hapus_id'];
    if ($id === (int)$_SESSION['id_user']) {
        $pesan = "Anda tidak bisa menghapus akun yang sedang dipakai login.";
    } else {
        $stmt = mysqli_prepare($koneksi, "DELETE FROM user WHERE id_user = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) { $pesan = "User berhasil dihapus."; $tipe = 'success'; }
        else { $pesan = "Gagal menghapus data."; }
    }
}

$hasil = mysqli_query($koneksi, "SELECT id_user, username, nama, alamat, hp, level FROM user ORDER BY id_user");
page_start('Data User');
?>
<div class="d-flex justify-content-between align-items-center">
    <h3>Data Master User</h3>
    <a href="form.php" class="btn btn-success btn-sm">Tambah Data</a>
</div>
<hr>
<?php if ($pesan): ?><div class="alert alert-<?= $tipe ?>"><?= h($pesan) ?></div><?php endif; ?>
<table class="table table-bordered table-sm">
    <thead class="thead-light"><tr><th>No</th><th>Username</th><th>Nama</th><th>Alamat</th><th>HP</th><th>Level</th><th>Tindakan</th></tr></thead>
    <tbody>
    <?php $no = 1; while ($r = mysqli_fetch_assoc($hasil)): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= h($r['username']) ?></td>
            <td><?= h($r['nama']) ?></td>
            <td><?= h($r['alamat']) ?></td>
            <td><?= h($r['hp']) ?></td>
            <td><?= (int)$r['level'] === 1 ? 'Owner' : 'Kasir' ?></td>
            <td>
                <a href="form.php?id=<?= (int)$r['id_user'] ?>" class="btn btn-warning btn-sm">Edit</a>
                <form method="post" class="d-inline" onsubmit="return confirm('Yakin hapus user ini?');">
                    <input type="hidden" name="hapus_id" value="<?= (int)$r['id_user'] ?>">
                    <button class="btn btn-danger btn-sm">Hapus</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
<?php page_end(); ?>
