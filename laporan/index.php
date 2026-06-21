<?php
define('BASE_URL', '..'); 
require_once __DIR__ . '/../config/database.php';
session_start();

if (!isset($_SESSION['petugas'])) { header('Location: ../login.php'); exit; }

$halaman = 'Laporan Penjualan';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

// 1. Logika Filter Tanggal (Default: Hari Ini)
$tgl_mulai  = $_GET['tgl_mulai'] ?? date('Y-m-d');
$tgl_sampai = $_GET['tgl_sampai'] ?? date('Y-m-d');

// 2. Query Menembak Database VIEW Rekap Penjualan Harian
$sql = "SELECT tanggal, jumlah_transaksi, total_pendapatan, total_diskon, rata_rata_transaksi, metode_bayar, nama_petugas 
        FROM vw_penjualan_harian
        WHERE tanggal BETWEEN ? AND ?
        ORDER BY tanggal DESC, nama_petugas ASC";

$params = array($tgl_mulai, $tgl_sampai);
$laporan = db_fetch_all($conn, $sql, '', ...$params);

// Hitung Grand Total Pendapatan di Periode Tersebut
$grand_total = 0;
foreach ($laporan as $row) {
    $grand_total += (float)$row['total_pendapatan'];
}
?>

<div class="container-fluid">
    <div class="page-header mt-4 no-print">
        <h4><i class="bi bi-file-earmark-bar-graph me-2 text-primary"></i>Rekap Penjualan Harian (Database View)</h4>
        <p class="text-muted">Memantau ringkasan omzet transaksi apotek berdasarkan data View ter-compile.</p>
    </div>

    <div class="card shadow-sm mb-4 no-print">
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
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <span class="fw-bold">Periode Laporan: <?= date('d/m/Y', strtotime($tgl_mulai)) ?> - <?= date('d/m/Y', strtotime($tgl_sampai)) ?></span>
            <span class="badge bg-primary no-print">Source: vw_penjualan_harian</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-4">Tanggal</th>
                            <th>Kasir / Petugas</th>
                            <th class="text-center">Metode Bayar</th>
                            <th class="text-center">Jumlah Transaksi</th>
                            <th class="text-end">Total Diskon</th>
                            <th class="text-end">Rata-rata / Transaksi</th>
                            <th class="text-end pe-4">Total Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($laporan)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">Tidak ada data transaksi ringkasan pada periode ini.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($laporan as $l): 
                                // Handling format tanggal dari SQL Server
                                $tgl_obj = ($l['tanggal'] instanceof DateTime) ? $l['tanggal'] : new DateTime($l['tanggal']);
                            ?>
                                <tr>
                                    <td class="ps-4 fw-bold"><?= $tgl_obj->format('d M Y') ?></td>
                                    <td><?= $l['nama_petugas'] ?></td>
                                    <td class="text-center"><span class="badge bg-secondary-subtle text-secondary text-uppercase"><?= $l['metode_bayar'] ?></span></td>
                                    <td class="text-center fw-bold"><?= $l['jumlah_transaksi'] ?></td>
                                    <td class="text-end text-muted">Rp <?= number_format($l['total_diskon'], 0, ',', '.') ?></td>
                                    <td class="text-end">Rp <?= number_format($l['rata_rata_transaksi'], 0, ',', '.') ?></td>
                                    <td class="text-end fw-bold text-primary pe-4">Rp <?= number_format($l['total_pendapatan'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="6" class="text-end fw-bold py-3">TOTAL OMZET PERIODE :</td>
                            <td class="text-end fw-bold text-success py-3 pe-4" style="font-size: 1.1rem;">
                                Rp <?= number_format($grand_total, 0, ',', '.') ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print, .btn, form, .sidebar, #header, header, .navbar { display: none !important; }
    #content { margin-left: 0 !important; padding: 0 !important; width: 100% !important; }
    .container-fluid { width: 100% !important; margin: 0 !important; padding: 0 !important; }
    .card { border: none !important; box-shadow: none !important; }
    .table-dark { background-color: #343a40 !important; color: #fff !important; }
}
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>