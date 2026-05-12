<?php
// Link ke root folder sendiri
define('BASE_URL', '.'); 
require_once __DIR__ . '/config/database.php';

$halaman = 'Dashboard';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

// Statistik tetap sama
$stat_obat = db_fetch_one($conn, "SELECT COUNT(*) AS total FROM obat WHERE is_active=1");
$stat_stok = db_fetch_one($conn, "SELECT COUNT(*) AS total FROM vw_stok_menipis");
$jual_hari_ini = db_fetch_one($conn, "SELECT SUM(total_harga) AS total FROM penjualan_header WHERE CAST(tgl_transaksi AS DATE) = CAST(GETDATE() AS DATE) AND status='selesai'");
?>

<div class="container-fluid">
    <div class="page-header mt-4">
        <h4><i class="bi bi-speedometer2 me-2"></i>Dashboard Utama</h4>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card bg-primary">
                <div class="stat-value"><?= $stat_obat['total'] ?? 0 ?></div>
                <div class="stat-label">Total Obat</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card bg-warning">
                <div class="stat-value"><?= $stat_stok['total'] ?? 0 ?></div>
                <div class="stat-label">Stok Menipis</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card bg-success">
                <div class="stat-value"><?= rupiah((float)($jual_hari_ini['total'] ?? 0)) ?></div>
                <div class="stat-label">Pendapatan Hari Ini</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Stok Obat Menipis</div>
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead><tr><th>Obat</th><th>Stok</th><th>Minimum</th></tr></thead>
                <tbody>
                    <?php
                    $menipis = db_fetch_all($conn, "SELECT TOP 5 * FROM vw_stok_menipis");
                    foreach($menipis as $m) {
                        echo "<tr><td>{$m['nama_obat']}</td><td class='text-danger fw-bold'>{$m['stok']}</td><td>{$m['stok_minimum']}</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>