<?php
define('BASE_URL', '..'); 
require_once __DIR__ . '/../config/database.php';
session_start();

$id = $_GET['id'] ?? die("ID Tidak Ditemukan");

$halaman = 'Detail Pembelian';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

// 1. Ambil Data Header & Supplier
$sql_h = "SELECT h.*, s.nama_supplier, pt.nama_petugas 
          FROM pembelian_header h 
          JOIN supplier s ON h.id_supplier = s.id_supplier 
          JOIN petugas pt ON h.id_petugas = pt.id_petugas
          WHERE h.id_pembelian = ?";
$pembelian = db_fetch_one($conn, $sql_h, '', $id);

// 2. Ambil Data Item Obat
$sql_d = "SELECT d.*, o.nama_obat 
          FROM pembelian_detail d 
          JOIN obat o ON d.id_obat = o.id_obat 
          WHERE d.id_pembelian = ?";
$items = db_fetch_all($conn, $sql_d, '', $id);
?>

<div class="container-fluid">
    <div class="page-header mt-4">
        <h4><i class="bi bi-info-circle me-2 text-info"></i>Detail Transaksi Pembelian</h4>
        <a href="index.php" class="btn btn-sm btn-secondary mb-3"> Kembali ke Daftar</a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light fw-bold">Informasi Faktur</div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr><td>No. Beli</td><td class="fw-bold">: <?= $pembelian['no_pembelian'] ?></td></tr>
                        <tr><td>Tanggal</td><td>: <?= $pembelian['tgl_pesan']->format('d M Y') ?></td></tr>
                        <tr><td>Supplier</td><td>: <?= $pembelian['nama_supplier'] ?></td></tr>
                        <tr><td>Petugas</td><td>: <?= $pembelian['nama_petugas'] ?></td></tr>
                        <tr><td>Status</td><td>: <span class="badge bg-success">Selesai</span></td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light fw-bold">Daftar Obat Masuk</div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-info">
                            <tr>
                                <th>Nama Obat</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Harga Beli</th>
                                <th class="text-end">Subtotal</th>
                                <th class="text-center">Expired</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($items as $i): ?>
                            <tr>
                                <td><?= $i['nama_obat'] ?></td>
                                <td class="text-center"><?= $i['qty'] ?></td>
                                <td class="text-end">Rp <?= number_format($i['harga_beli']) ?></td>
                                <td class="text-end fw-bold">Rp <?= number_format($i['subtotal']) ?></td>
                                <td class="text-center small"><?= $i['tgl_kadaluarsa']->format('d/m/Y') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="3" class="text-end fw-bold">GRAND TOTAL :</td>
                                <td class="text-end fw-bold text-primary" style="font-size: 1.1rem;">
                                    Rp <?= number_format($pembelian['total_harga']) ?>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>