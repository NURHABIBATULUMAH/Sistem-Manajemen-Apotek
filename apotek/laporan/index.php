<?php
define('BASE_URL', '..');
require_once __DIR__ . '/../config/database.php';
session_start();

if (!isset($_SESSION['petugas'])) {
    header('Location: ../login.php');
    exit;
}

$halaman = 'Laporan Penjualan';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

// 1. Logika Filter Tanggal (Default: Hari Ini)
$tgl_mulai  = $_GET['tgl_mulai'] ?? date('Y-m-d');
$tgl_sampai = $_GET['tgl_sampai'] ?? date('Y-m-d');
$params = array($tgl_mulai, $tgl_sampai);

// 2. QUERY 1: Tetap ambil data dari VIEW Penjualan Harian Anda
$sql_view = "SELECT tanggal, jumlah_transaksi, total_pendapatan, total_diskon, rata_rata_transaksi, metode_bayar, nama_petugas 
             FROM vw_penjualan_harian
             WHERE tanggal BETWEEN ? AND ?
             ORDER BY tanggal DESC, nama_petugas ASC";
$laporan_harian = db_fetch_all($conn, $sql_view, '', ...$params);

// 3. QUERY 2: Ambil data dari STORED PROCEDURE Rekap Obat Terlaris
$sql_sp = "EXEC sp_rekap_penjualan_obat ?, ?";
$laporan_obat = db_fetch_all($conn, $sql_sp, '', ...$params);


// Hitung Grand Total Pendapatan dari View Harian
$grand_total = 0;
foreach ($laporan_harian as $row) {
    $grand_total += (float)$row['total_pendapatan'];
}
?>

<div class="container-fluid">
    <div class="page-header mt-4 no-print">
        <h4><i class="bi bi-file-earmark-bar-graph me-2 text-primary"></i>Laporan Eksekutif Penjualan</h4>
        <p class="text-muted">Analisis performa keuangan harian dan rekap komoditas obat terlaris.</p>
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

    <div class="card shadow-sm mb-5">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <span class="fw-bold text-dark"><i class="bi bi-graph-up-arrow me-2 text-success"></i>1. Ringkasan Omzet Harian</span>
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
                        <?php if (empty($laporan_harian)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Tidak ada data transaksi ringkasan pada periode ini.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($laporan_harian as $l):
                                $tgl_obj = ($l['tanggal'] instanceof DateTime) ? $l['tanggal'] : new DateTime($l['tanggal']);
                            ?>
                                <tr>
                                    <td class="ps-4 fw-bold"><?= $tgl_obj->format('d M Y') ?></td>
                                    <td><?= htmlspecialchars($l['nama_petugas']) ?></td>
                                    <td class="text-center"><span class="badge bg-secondary-subtle text-secondary text-uppercase"><?= htmlspecialchars($l['metode_bayar']) ?></span></td>
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


    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <span class="fw-bold text-dark"><i class="bi bi-capsule me-2 text-primary"></i>2. Statistik Kuantitas Penjualan per Item Obat</span>
            <span class="badge bg-success no-print">Source: sp_rekap_penjualan_obat (Cursor)</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-secondary text-dark">
                        <tr>
                            <th class="ps-4" style="width: 15%;">ID Obat</th>
                            <th style="width: 45%;">Nama Obat</th>
                            <th class="text-center" style="width: 20%;">Total Qty Terjual</th>
                            <th class="text-end pe-4" style="width: 20%;">Total Nilai Penjualan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($laporan_obat)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Tidak ada perputaran item obat pada periode ini.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($laporan_obat as $lo): ?>
                                <tr>
                                    <td class="ps-4 text-secondary small">#<?= $lo['id_obat'] ?></td>
                                    <td class="fw-semibold text-dark"><?= htmlspecialchars($lo['nama_obat']) ?></td>
                                    <td class="text-center fw-bold text-success"><?= number_format($lo['total_qty'], 0, ',', '.') ?></td>
                                    <td class="text-end fw-bold text-navy pe-4">Rp <?= number_format($lo['total_nilai'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<style>
    @media print {

        .no-print,
        .btn,
        form,
        .sidebar,
        #header,
        header,
        .navbar {
            display: none !important;
        }

        #content {
            margin-left: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }

        .container-fluid {
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .card {
            border: 1px solid #dee2e6 !important;
            box-shadow: none !important;
            margin-bottom: 20px !important;
            break-inside: avoid;
        }

        .table-dark {
            background-color: #343a40 !important;
            color: #fff !important;
        }
    }
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>