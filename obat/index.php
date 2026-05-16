<?php
define('BASE_URL', '..'); 
require_once __DIR__ . '/../config/database.php';
session_start();

if (!isset($_SESSION['petugas'])) { header('Location: ../login.php'); exit; }

// Query Monitoring Stok: Menggunakan nama kolom rill (stok_sisa, harga_satuan)
$sql = "SELECT o.nama_obat, pd.tgl_kadaluarsa, pd.stok_sisa, pd.harga_satuan, 
        DATEDIFF(day, GETDATE(), pd.tgl_kadaluarsa) as sisa_hari
        FROM pembelian_detail pd 
        LEFT JOIN obat o ON pd.id_obat = o.id_obat 
        WHERE pd.stok_sisa > 0 
        ORDER BY pd.tgl_kadaluarsa ASC";
$stok_list = db_fetch_all($conn, $sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Monitoring Stok - Apotek Sehat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/sidebar.php'; ?>
<div id="content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">Monitoring Stok (FEFO)</h4>
                <p class="text-muted small mb-0">Stok ditampilkan per batch tanggal kadaluarsa.</p>
            </div>
            <a href="master.php" class="btn btn-dark rounded-pill px-4 shadow-sm">
                <i class="bi bi-gear-fill me-1"></i> Kelola Data Master
            </a>
        </div>

        <div class="card-custom shadow-sm border-0 overflow-hidden bg-white">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="small text-muted text-uppercase">
                            <th class="ps-4 py-3">Nama Obat</th>
                            <th>Kadaluarsa</th>
                            <th class="text-center">Sisa Stok</th>
                            <th>Harga Beli</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($stok_list)): ?>
                            <tr><td colspan="5" class="text-center py-5 text-muted">Belum ada stok aktif di gudang.</td></tr>
                        <?php else: ?>
                            <?php foreach ($stok_list as $s): ?>
                            <tr>
                                <td class="ps-4 fw-bold"><?= $s['nama_obat'] ?></td>
                                <td><i class="bi bi-calendar3 me-2"></i><?= $s['tgl_kadaluarsa']->format('d M Y') ?></td>
                                <td class="text-center fw-bold text-primary"><?= $s['stok_sisa'] ?></td>
                                <td>Rp <?= number_format($s['harga_satuan'], 0, ',', '.') ?></td>
                                <td class="text-center">
                                    <?php if($s['sisa_hari'] < 0): ?>
                                        <span class="badge bg-danger rounded-pill px-3">Expired</span>
                                    <?php elseif($s['sisa_hari'] < 90): ?>
                                        <span class="badge bg-warning text-dark rounded-pill px-3">Dekat Exp (<?= $s['sisa_hari'] ?> hari)</span>
                                    <?php else: ?>
                                        <span class="badge bg-success-subtle text-success rounded-pill px-3">Aman</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>