<?php
include __DIR__ . "/../auth.php";   // level 1 & 2 boleh
include __DIR__ . "/../koneksi.php";
include __DIR__ . "/../layout.php";

$pesan = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal      = $_POST['waktu_transaksi'] ?? '';
    $pelanggan_id = $_POST['pelanggan_id'] ?? '';
    $keterangan   = trim($_POST['keterangan'] ?? '');

    if ($tanggal === '' || $pelanggan_id === '') {
        $pesan = "Tanggal dan pelanggan wajib diisi.";
    } else {
        $stmt = mysqli_prepare($koneksi,
            "INSERT INTO transaksi (waktu_transaksi, keterangan, total, pelanggan_id) VALUES (?, ?, 0, ?)");
        mysqli_stmt_bind_param($stmt, "sss", $tanggal, $keterangan, $pelanggan_id);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: detail.php?id=" . mysqli_insert_id($koneksi));
            exit;
        }
        $pesan = "Gagal menyimpan: " . mysqli_error($koneksi);
    }
}

$pelanggan = mysqli_query($koneksi, "SELECT id, nama FROM pelanggan ORDER BY nama");
$daftar = mysqli_query($koneksi,
    "SELECT t.id, t.waktu_transaksi, t.keterangan, t.total, p.nama AS nama_pelanggan
       FROM transaksi t LEFT JOIN pelanggan p ON p.id = t.pelanggan_id
      ORDER BY t.id DESC");
page_start('Transaksi');
?>
<h3>Data Transaksi (Master)</h3>
<hr>
<?php if ($pesan): ?><div class="alert alert-danger"><?= h($pesan) ?></div><?php endif; ?>

<form method="post" class="card card-body mb-4 bg-light">
    <h5>Tambah Transaksi Baru</h5>
    <div class="form-row">
        <div class="form-group col-md-3"><label>Tanggal</label>
            <input type="date" name="waktu_transaksi" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
        <div class="form-group col-md-4"><label>Pelanggan</label>
            <select name="pelanggan_id" class="form-control" required>
                <option value="">-- pilih pelanggan --</option>
                <?php while ($p = mysqli_fetch_assoc($pelanggan)): ?>
                    <option value="<?= h($p['id']) ?>"><?= h($p['id'] . ' - ' . $p['nama']) ?></option>
                <?php endwhile; ?>
            </select></div>
        <div class="form-group col-md-5"><label>Keterangan</label>
            <input type="text" name="keterangan" class="form-control"></div>
    </div>
    <div><button class="btn btn-primary">Simpan &amp; Isi Detail</button></div>
</form>

<table class="table table-bordered table-sm">
    <thead class="thead-light"><tr><th>ID</th><th>Tanggal</th><th>Pelanggan</th><th>Keterangan</th><th class="text-right">Total</th><th>Aksi</th></tr></thead>
    <tbody>
    <?php while ($r = mysqli_fetch_assoc($daftar)): ?>
        <tr>
            <td><?= (int)$r['id'] ?></td>
            <td><?= h($r['waktu_transaksi']) ?></td>
            <td><?= h($r['nama_pelanggan']) ?></td>
            <td><?= h($r['keterangan']) ?></td>
            <td class="text-right"><?= rupiah($r['total']) ?></td>
            <td><a class="btn btn-info btn-sm" href="detail.php?id=<?= (int)$r['id'] ?>">Lihat / Isi Detail</a></td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
<?php page_end(); ?>
