<?php
define('BASE_URL', '..'); 
require_once __DIR__ . '/../config/database.php';

$halaman = 'Transaksi Penjualan';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$sql = "SELECT ph.*, p.nama_pelanggan, pt.nama_petugas 
        FROM penjualan_header ph
        LEFT JOIN pelanggan p ON ph.id_pelanggan = p.id_pelanggan
        JOIN petugas pt ON ph.id_petugas = pt.id_petugas
        ORDER BY ph.tgl_transaksi DESC";
$data = db_fetch_all($conn, $sql);
?>

<div class="container-fluid">
    <div class="page-header d-flex justify-content-between align-items-center mt-4">
        <h4><i class="bi bi-receipt me-2 text-primary"></i>Daftar Penjualan</h4>
        <a href="tambah.php" class="btn btn-primary btn-sm">Transaksi Baru</a>
    </div>

    <div class="card mt-3">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>No. Faktur</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Total</th>
                        <th>Metode</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($data as $r): ?>
                    <tr>
                        <td class="fw-bold"><?= $r['no_penjualan'] ?></td>
                        <td><?= tgl_indo($r['tgl_transaksi']) ?></td>
                        <td><?= $r['nama_pelanggan'] ?: 'Umum' ?></td>
                        <td><?= rupiah((float)$r['total_harga']) ?></td>
                        <td><span class="badge bg-light text-dark border"><?= strtoupper($r['metode_bayar']) ?></span></td>
                        <td class="text-center">
                            <a href="nota.php?id=<?= $r['id_penjualan'] ?>" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="bi bi-printer"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>