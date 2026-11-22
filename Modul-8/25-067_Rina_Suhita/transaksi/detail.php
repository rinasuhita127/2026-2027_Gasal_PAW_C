<?php
include __DIR__ . "/../auth.php";   // level 1 & 2 boleh
include __DIR__ . "/../koneksi.php";
include __DIR__ . "/../layout.php";

$id    = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$pesan = '';
$tipe  = 'danger';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $barang_id = (int)($_POST['barang_id'] ?? 0);
    $qty       = (int)($_POST['qty'] ?? 0);

    if ($barang_id <= 0 || $qty <= 0) {
        $pesan = "Pilih barang dan isi qty minimal 1.";
    } else {
        $cek = mysqli_prepare($koneksi, "SELECT 1 FROM transaksi_detail WHERE transaksi_id = ? AND barang_id = ?");
        mysqli_stmt_bind_param($cek, "ii", $id, $barang_id);
        mysqli_stmt_execute($cek);
        $sudah = mysqli_stmt_get_result($cek)->num_rows > 0;

        if ($sudah) {
            $pesan = "Barang tersebut sudah ada di transaksi ini dan tidak boleh diinput dua kali.";
        } else {
            $qb = mysqli_prepare($koneksi, "SELECT harga FROM barang WHERE id = ?");
            mysqli_stmt_bind_param($qb, "i", $barang_id);
            mysqli_stmt_execute($qb);
            $brg = mysqli_fetch_assoc(mysqli_stmt_get_result($qb));

            if (!$brg) {
                $pesan = "Barang tidak ditemukan.";
            } else {
                $harga = (int)$brg['harga'];
                mysqli_begin_transaction($koneksi);
                $ok = true;

                $ins = mysqli_prepare($koneksi, "INSERT INTO transaksi_detail (transaksi_id, barang_id, harga, qty) VALUES (?, ?, ?, ?)");
                mysqli_stmt_bind_param($ins, "iiii", $id, $barang_id, $harga, $qty);
                $ok = $ok && mysqli_stmt_execute($ins);

                $upd = mysqli_prepare($koneksi,
                    "UPDATE transaksi SET total = (SELECT COALESCE(SUM(harga * qty), 0) FROM transaksi_detail WHERE transaksi_id = ?) WHERE id = ?");
                mysqli_stmt_bind_param($upd, "ii", $id, $id);
                $ok = $ok && mysqli_stmt_execute($upd);

                if ($ok) {
                    mysqli_commit($koneksi);
                    $pesan = "Detail berhasil disimpan dan total transaksi diperbarui.";
                    $tipe  = 'success';
                } else {
                    mysqli_rollback($koneksi);
                    $pesan = "Gagal menyimpan: " . mysqli_error($koneksi);
                }
            }
        }
    }
}

$stmt = mysqli_prepare($koneksi,
    "SELECT t.*, p.nama AS nama_pelanggan FROM transaksi t
       LEFT JOIN pelanggan p ON p.id = t.pelanggan_id WHERE t.id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$master = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$master) { header("Location: index.php"); exit; }

$stmt = mysqli_prepare($koneksi,
    "SELECT b.kode_barang, b.nama_barang, d.harga, d.qty, (d.harga * d.qty) AS subtotal
       FROM transaksi_detail d INNER JOIN barang b ON b.id = d.barang_id
      WHERE d.transaksi_id = ? ORDER BY b.nama_barang");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$detail = mysqli_stmt_get_result($stmt);

$stmt = mysqli_prepare($koneksi,
    "SELECT b.id, b.kode_barang, b.nama_barang, b.harga, d.barang_id AS sudah_dipakai
       FROM barang b LEFT JOIN transaksi_detail d ON d.barang_id = b.id AND d.transaksi_id = ?
      ORDER BY b.nama_barang");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$opsi = mysqli_stmt_get_result($stmt);

page_start('Detail Transaksi');
?>
<h3>Master Transaksi</h3>
<div class="card card-body bg-light mb-3">
    <div>No. Transaksi : <strong><?= (int)$master['id'] ?></strong></div>
    <div>Tanggal : <?= h($master['waktu_transaksi']) ?></div>
    <div>Pelanggan : <?= h($master['pelanggan_id'] . ' - ' . $master['nama_pelanggan']) ?></div>
    <div>Keterangan : <?= h($master['keterangan']) ?></div>
    <div>Total : <strong><?= rupiah($master['total']) ?></strong></div>
</div>

<?php if ($pesan): ?><div class="alert alert-<?= $tipe ?>"><?= h($pesan) ?></div><?php endif; ?>

<h5>Detail Transaksi</h5>
<table class="table table-bordered table-sm">
    <thead class="thead-light"><tr><th>No</th><th>Kode</th><th>Nama Barang</th><th class="text-right">Harga</th><th class="text-right">Qty</th><th class="text-right">Subtotal</th></tr></thead>
    <tbody>
    <?php $no = 1; while ($d = mysqli_fetch_assoc($detail)): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= h($d['kode_barang']) ?></td>
            <td><?= h($d['nama_barang']) ?></td>
            <td class="text-right"><?= rupiah($d['harga']) ?></td>
            <td class="text-right"><?= (int)$d['qty'] ?></td>
            <td class="text-right"><?= rupiah($d['subtotal']) ?></td>
        </tr>
    <?php endwhile; ?>
    <?php if ($no === 1): ?><tr><td colspan="6">Belum ada detail.</td></tr><?php endif; ?>
    </tbody>
</table>

<form method="post" class="card card-body mb-3">
    <h5>Tambah Barang ke Transaksi</h5>
    <div class="form-row">
        <div class="form-group col-md-8"><label>Barang</label>
            <select name="barang_id" class="form-control" required>
                <option value="">-- pilih barang --</option>
                <?php while ($b = mysqli_fetch_assoc($opsi)): ?>
                    <option value="<?= (int)$b['id'] ?>" <?= $b['sudah_dipakai'] ? 'disabled' : '' ?>>
                        <?= h($b['kode_barang'] . ' - ' . $b['nama_barang'] . ' (' . rupiah($b['harga']) . ')') ?><?= $b['sudah_dipakai'] ? ' [sudah dipilih]' : '' ?>
                    </option>
                <?php endwhile; ?>
            </select></div>
        <div class="form-group col-md-4"><label>Qty</label>
            <input type="number" name="qty" min="1" value="1" class="form-control" required></div>
    </div>
    <div><button class="btn btn-primary">Simpan Detail</button></div>
</form>
<a class="btn btn-secondary" href="index.php">&laquo; Kembali ke daftar transaksi</a>
<?php page_end(); ?>
