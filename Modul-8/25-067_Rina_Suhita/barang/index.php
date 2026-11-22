<?php
include __DIR__ . "/../auth.php";
wajib_level([1]);
include __DIR__ . "/../koneksi.php";
include __DIR__ . "/../layout.php";

$pesan = ''; $tipe = 'danger';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_id'])) {
    $id = (int)$_POST['hapus_id'];
    $stmt = mysqli_prepare($koneksi, "DELETE FROM barang WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) {
        $pesan = "Barang berhasil dihapus."; $tipe = 'success';
    } elseif (mysqli_errno($koneksi) == 1451) {
        $pesan = "Barang tidak dapat dihapus karena sudah dipakai pada transaksi.";
    } else {
        $pesan = "Gagal menghapus data.";
    }
}

$hasil = mysqli_query($koneksi,
    "SELECT b.id, b.kode_barang, b.nama_barang, b.harga, b.stok, s.nama AS nama_supplier
       FROM barang b LEFT JOIN supplier s ON s.id = b.supplier_id
      ORDER BY b.id");
page_start('Data Barang');
?>
<div class="d-flex justify-content-between align-items-center">
    <h3>Data Master Barang</h3>
    <a href="form.php" class="btn btn-success btn-sm">Tambah Data</a>
</div>
<hr>
<?php if ($pesan): ?><div class="alert alert-<?= $tipe ?>"><?= h($pesan) ?></div><?php endif; ?>
<table class="table table-bordered table-sm">
    <thead class="thead-light"><tr><th>No</th><th>Kode</th><th>Nama Barang</th><th class="text-right">Harga</th><th class="text-right">Stok</th><th>Supplier</th><th>Tindakan</th></tr></thead>
    <tbody>
    <?php $no = 1; while ($r = mysqli_fetch_assoc($hasil)): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= h($r['kode_barang']) ?></td>
            <td><?= h($r['nama_barang']) ?></td>
            <td class="text-right"><?= rupiah($r['harga']) ?></td>
            <td class="text-right"><?= (int)$r['stok'] ?></td>
            <td><?= h($r['nama_supplier']) ?></td>
            <td>
                <a href="form.php?id=<?= (int)$r['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                <form method="post" class="d-inline" onsubmit="return confirm('Yakin hapus barang ini?');">
                    <input type="hidden" name="hapus_id" value="<?= (int)$r['id'] ?>">
                    <button class="btn btn-danger btn-sm">Hapus</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
<?php page_end(); ?>
