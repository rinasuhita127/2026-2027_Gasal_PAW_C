<?php
include __DIR__ . "/../auth.php";
wajib_level([1]);
include __DIR__ . "/../koneksi.php";
include __DIR__ . "/../layout.php";

$pesan = ''; $tipe = 'danger';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_id'])) {
    $id = $_POST['hapus_id'];
    $stmt = mysqli_prepare($koneksi, "DELETE FROM pelanggan WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "s", $id);
    if (mysqli_stmt_execute($stmt)) {
        $pesan = "Pelanggan berhasil dihapus."; $tipe = 'success';
    } elseif (mysqli_errno($koneksi) == 1451) {
        $pesan = "Pelanggan tidak bisa dihapus karena sudah memiliki transaksi.";
    } else {
        $pesan = "Gagal menghapus data.";
    }
}

$hasil = mysqli_query($koneksi, "SELECT id, nama, jenis_kelamin, telp, alamat FROM pelanggan ORDER BY id");
page_start('Data Pelanggan');
?>
<div class="d-flex justify-content-between align-items-center">
    <h3>Data Master Pelanggan</h3>
    <a href="form.php" class="btn btn-success btn-sm">Tambah Data</a>
</div>
<hr>
<?php if ($pesan): ?><div class="alert alert-<?= $tipe ?>"><?= h($pesan) ?></div><?php endif; ?>
<table class="table table-bordered table-sm">
    <thead class="thead-light"><tr><th>No</th><th>ID</th><th>Nama</th><th>JK</th><th>Telp</th><th>Alamat</th><th>Tindakan</th></tr></thead>
    <tbody>
    <?php $no = 1; while ($r = mysqli_fetch_assoc($hasil)): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= h($r['id']) ?></td>
            <td><?= h($r['nama']) ?></td>
            <td><?= h($r['jenis_kelamin']) ?></td>
            <td><?= h($r['telp']) ?></td>
            <td><?= h($r['alamat']) ?></td>
            <td>
                <a href="form.php?id=<?= urlencode($r['id']) ?>" class="btn btn-warning btn-sm">Edit</a>
                <form method="post" class="d-inline" onsubmit="return confirm('Anda yakin akan menghapus pelanggan ini?');">
                    <input type="hidden" name="hapus_id" value="<?= h($r['id']) ?>">
                    <button class="btn btn-danger btn-sm">Hapus</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
<?php page_end(); ?>
