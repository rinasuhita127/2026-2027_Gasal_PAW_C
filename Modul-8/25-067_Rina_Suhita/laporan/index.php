<?php
include __DIR__ . "/../auth.php";   // level 1 & 2 boleh
include __DIR__ . "/../koneksi.php";
include __DIR__ . "/../layout.php";

$dari   = $_GET['dari']   ?? '';
$sampai = $_GET['sampai'] ?? '';

$sql = "SELECT t.id, t.waktu_transaksi, t.keterangan, t.total, p.nama AS nama_pelanggan,
               (SELECT COALESCE(SUM(qty),0) FROM transaksi_detail d WHERE d.transaksi_id = t.id) AS jml_item
          FROM transaksi t LEFT JOIN pelanggan p ON p.id = t.pelanggan_id";
$where = []; $types = ''; $params = [];
if ($dari !== '')   { $where[] = "t.waktu_transaksi >= ?"; $types .= 's'; $params[] = $dari; }
if ($sampai !== '') { $where[] = "t.waktu_transaksi <= ?"; $types .= 's'; $params[] = $sampai; }
if ($where) $sql .= " WHERE " . implode(" AND ", $where);
$sql .= " ORDER BY t.waktu_transaksi, t.id";

$stmt = mysqli_prepare($koneksi, $sql);
if ($params) mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);

page_start('Laporan');
?>
<h3>Laporan Penjualan</h3>
<hr>
<form method="get" class="form-inline mb-3">
    <label class="mr-2">Dari</label>
    <input type="date" name="dari" class="form-control mr-3" value="<?= h($dari) ?>">
    <label class="mr-2">Sampai</label>
    <input type="date" name="sampai" class="form-control mr-3" value="<?= h($sampai) ?>">
    <button class="btn btn-primary mr-2">Tampilkan</button>
    <a href="index.php" class="btn btn-secondary">Reset</a>
</form>

<table class="table table-bordered table-sm">
    <thead class="thead-light"><tr><th>No</th><th>Tanggal</th><th>Pelanggan</th><th>Keterangan</th><th class="text-right">Jml Item</th><th class="text-right">Total</th><th></th></tr></thead>
    <tbody>
    <?php $no = 1; $grand = 0; while ($r = mysqli_fetch_assoc($hasil)): $grand += (int)$r['total']; ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= h($r['waktu_transaksi']) ?></td>
            <td><?= h($r['nama_pelanggan']) ?></td>
            <td><?= h($r['keterangan']) ?></td>
            <td class="text-right"><?= (int)$r['jml_item'] ?></td>
            <td class="text-right"><?= rupiah($r['total']) ?></td>
            <td><a href="../transaksi/detail.php?id=<?= (int)$r['id'] ?>" class="btn btn-info btn-sm">Detail</a></td>
        </tr>
    <?php endwhile; ?>
    <?php if ($no === 1): ?><tr><td colspan="7">Tidak ada data.</td></tr><?php endif; ?>
    </tbody>
    <tfoot><tr class="font-weight-bold"><td colspan="5" class="text-right">Total Penjualan</td><td class="text-right"><?= rupiah($grand) ?></td><td></td></tr></tfoot>
</table>
<?php page_end(); ?>
