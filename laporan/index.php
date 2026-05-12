<?php
define('BASE_URL', '..'); 
require_once __DIR__ . '/../config/database.php';
session_start();

$halaman = 'Laporan Penjualan';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

// 1. Logika Filter Tanggal (Default: Hari Ini)
$tgl_mulai  = $_GET['tgl_mulai'] ?? date('Y-m-d');
$tgl_sampai = $_GET['tgl_sampai'] ?? date('Y-m-d');

// 2. Query Sakti JOIN 3 Tabel untuk Laporan Detail
$sql = "SELECT 
            h.no_penjualan, 
            h.tgl_transaksi, 
            p.nama_pelanggan, 
            o.nama_obat, 
            d.qty, 
            d.harga_satuan, 
            d.subtotal,
            pt.nama_petugas
        FROM penjualan_header h
        JOIN penjualan_detail d ON h.id_penjualan = d.id_penjualan
        JOIN obat o ON d.id_obat = o.id_obat
        JOIN petugas pt ON h.id_petugas = pt.id_petugas
        LEFT JOIN pelanggan p ON h.id_pelanggan = p.id_pelanggan
        WHERE CAST(h.tgl_transaksi AS DATE) BETWEEN ? AND ?
        ORDER BY h.tgl_transaksi DESC";

$params = array($tgl_mulai, $tgl_sampai);
$laporan = db_fetch_all($conn, $sql, '', ...$params);

// Hitung Total Pendapatan di Periode Tersebut
$grand_total = 0;
foreach ($laporan as $row) {
    $grand_total += $row['subtotal'];
}
?>

<div class="container-fluid">
    <div class="page-header mt-4">
        <h4><i class="bi bi-file-earmark-bar-graph me-2 text-primary"></i>Laporan Penjualan Detail</h4>
        <p class="text-muted">Memantau daftar obat yang terjual dalam periode tertentu.</p>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Dari Tanggal</label>
                    <input type="date" name="tgl_mulai" class="form-control" value="<?= $tgl_mulai ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Sampai Tanggal</label>
                    <input type="date" name="tgl_sampai" class="form-control" value="<?= $tgl_sampai ?>">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-2"></i>Filter Laporan
                    </button>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-success w-100" onclick="window.print()">
                        <i class="bi bi-printer me-2"></i>Cetak Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <span class="fw-bold">Data Penjualan: <?= date('d/m/Y', strtotime($tgl_mulai)) ?> - <?= date('d/m/Y', strtotime($tgl_sampai)) ?></span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Tgl & No. Nota</th>
                            <th>Pelanggan</th>
                            <th>Nama Obat</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Harga Satuan</th>
                            <th class="text-end">Subtotal</th>
                            <th>Kasir</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($laporan)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Tidak ada transaksi pada periode ini.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($laporan as $l): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?= $l['no_penjualan'] ?></div>
                                        <small class="text-muted"><?= $l['tgl_transaksi']->format('d/m/Y H:i') ?></small>
                                    </td>
                                    <td><?= $l['nama_pelanggan'] ?? '<span class="text-muted">Umum</span>' ?></td>
                                    <td><?= $l['nama_obat'] ?></td>
                                    <td class="text-center"><?= $l['qty'] ?></td>
                                    <td class="text-end">Rp <?= number_format($l['harga_satuan'], 0, ',', '.') ?></td>
                                    <td class="text-end fw-bold text-primary">Rp <?= number_format($l['subtotal'], 0, ',', '.') ?></td>
                                    <td><small><?= $l['nama_petugas'] ?></small></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="5" class="text-end fw-bold py-3">TOTAL PENDAPATAN :</td>
                            <td class="text-end fw-bold text-success py-3" style="font-size: 1.1rem;">
                                Rp <?= number_format($grand_total, 0, ',', '.') ?>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print, .btn, form, .sidebar, .header { display: none !important; }
    .container-fluid { width: 100%; margin: 0; padding: 0; }
    .card { border: none !important; box-shadow: none !important; }
    .table-dark { background-color: #fff !important; color: #000 !important; }
}
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>