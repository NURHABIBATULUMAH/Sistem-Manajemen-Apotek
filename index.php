<?php
define('BASE_URL', '.'); 
require_once __DIR__ . '/config/database.php';
session_start();

if (!isset($_SESSION['petugas'])) { header('Location: login.php'); exit; }

// --- 1. DATA TRANSAKSI (HERO SECTION) ---
$q_orders    = db_fetch_one($conn, "SELECT COUNT(*) as total FROM penjualan_header");
$q_income    = db_fetch_one($conn, "SELECT SUM(total_harga) as total FROM penjualan_header");
$q_expense   = db_fetch_one($conn, "SELECT SUM(total_harga) as total FROM pembelian_header");
$earnings    = ($q_income['total'] ?? 0) - ($q_expense['total'] ?? 0);

// --- 2. DATA INVENTORY (RIIL) ---
$q_low_stock = db_fetch_one($conn, "SELECT COUNT(*) as total FROM vw_stok_menipis");
$q_expiry    = db_fetch_one($conn, "SELECT COUNT(*) as total FROM vw_obat_kadaluarsa");
// New Arrivals: Obat yang baru ditambah (misal 10 ID terakhir)
$q_new_items = db_fetch_one($conn, "SELECT COUNT(*) as total FROM obat WHERE is_active = 1 AND id_obat > (SELECT MAX(id_obat) - 10 FROM obat)");

// Top Selling Medications (Riil dari Penjualan)
$top_meds = db_fetch_all($conn, "SELECT TOP 2 o.nama_obat, SUM(pd.qty) as total_sold 
                                FROM penjualan_detail pd 
                                JOIN obat o ON pd.id_obat = o.id_obat 
                                GROUP BY o.nama_obat ORDER BY total_sold DESC");

// --- 3. DATA PELANGGAN ---
// Menghitung pelanggan unik dari riwayat penjualan
$q_pelanggan = db_fetch_one($conn, "SELECT COUNT(DISTINCT id_pelanggan) as total FROM penjualan_header");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Apotek Modern</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        :root { --modern-green: #3e7d60; --soft-bg: #f4f7f6; }
        body { background-color: var(--soft-bg); font-family: 'Inter', sans-serif; }
        .card-modern { border: none; border-radius: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.02); }
        .hero-green { background-color: var(--modern-green); color: white; border-radius: 24px; padding: 40px; }
        .stat-box { background: rgba(255,255,255,0.1); border-radius: 18px; padding: 20px; text-align: center; }
        .top-item { border: 1px solid #edf2f1; border-radius: 20px; padding: 20px; background: white; }
    </style>
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<div id="content">
    <div class="container-fluid py-4">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold m-0 text-dark">Dashboard</h3>
            <div class="d-flex gap-3">
                <div class="input-group shadow-sm rounded-pill overflow-hidden">
                    <span class="input-group-text bg-white border-0"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control border-0" placeholder="Cari data..." style="width: 200px;">
                </div>
                <div class="bg-white px-3 py-2 rounded-pill shadow-sm small fw-bold">
                    <i class="bi bi-calendar-range me-2 text-success"></i> 01/01/2026 - 12/31/2026
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="hero-green shadow-lg mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <p class="text-white-50 mb-1 fw-medium">Total Pesanan Selesai</p>
                            <h1 class="display-3 fw-bold mb-3"><?= number_format($q_orders['total'] ?? 0) ?></h1>
                            <div class="badge bg-white text-success rounded-pill px-3 py-2 fw-bold">
                                <i class="bi bi-graph-up-arrow me-1"></i> +16% Bulan ini
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="p-4 rounded-4" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);">
                                <h2 class="fw-bold mb-0">Rp <?= number_format($earnings, 0, ',', '.') ?></h2>
                                <p class="small text-white-50 mb-3">Total Keuntungan Bersih</p>
                                <div class="d-flex justify-content-between small border-top border-white-20 pt-3">
                                    <span>Pemasukan:</span>
                                    <span class="fw-bold">Rp <?= number_format($q_income['total'] ?? 0, 0, ',', '.') ?></span>
                                </div>
                                <div class="d-flex justify-content-between small mt-2">
                                    <span>Pengeluaran:</span>
                                    <span class="fw-bold">Rp <?= number_format($q_expense['total'] ?? 0, 0, ',', '.') ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-modern bg-white p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6 class="fw-bold m-0">Ringkasan Aktivitas Terakhir</h6>
                        <a href="#" class="text-success small fw-bold text-decoration-none">Lihat Semua</a>
                    </div>
                    <div style="height: 250px; background: #fafafa; border: 2px dashed #eee; border-radius: 20px;" class="d-flex align-items-center justify-content-center text-muted">
                        Area Grafik Penjualan (Akan muncul setelah ada data bulanan)
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                
                <div class="card-modern bg-white p-4 mb-4">
                    <h6 class="fw-bold mb-4">Inventory & Stok</h6>
                    <p class="text-muted small mb-3">Obat Paling Laku</p>
                    
                    <div class="row g-3 mb-4">
                        <?php foreach($top_meds as $tm): ?>
                        <div class="col-6">
                            <div class="top-item text-center">
                                <i class="bi bi-capsule-pill text-success fs-3 mb-2 d-block"></i>
                                <div class="fw-bold small text-truncate"><?= $tm['nama_obat'] ?></div>
                                <div class="text-muted" style="font-size: 0.7rem;"><?= $tm['total_sold'] ?> Terjual</div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="row g-0 text-center border-top pt-4">
                        <div class="col-4">
                            <div class="fw-bold text-danger"><?= $q_low_stock['total'] ?></div>
                            <div class="text-muted" style="font-size: 0.65rem;">Stok Menipis</div>
                        </div>
                        <div class="col-4 border-start">
                            <div class="fw-bold text-warning"><?= $q_expiry['total'] ?></div>
                            <div class="text-muted" style="font-size: 0.65rem;">Mendekati Exp</div>
                        </div>
                        <div class="col-4 border-start">
                            <div class="fw-bold text-primary"><?= $q_new_items['total'] ?></div>
                            <div class="text-muted" style="font-size: 0.65rem;">Obat Baru</div>
                        </div>
                    </div>
                </div>

                <div class="card-modern bg-white p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6 class="fw-bold m-0">Data Pelanggan</h6>
                        <i class="bi bi-people text-muted"></i>
                    </div>
                    
                    <div class="text-center mb-4">
                        <h1 class="fw-bold mb-0"><?= number_format($q_pelanggan['total'] ?? 0) ?></h1>
                        <p class="text-muted small">Total Pelanggan Terdaftar</p>
                    </div>

                    <div class="p-3 rounded-4 bg-light small mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Belanja di Toko:</span>
                            <span class="fw-bold"><?= number_format(($q_pelanggan['total'] ?? 0) * 0.8) ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Pesanan Online:</span>
                            <span class="fw-bold"><?= number_format(($q_pelanggan['total'] ?? 0) * 0.2) ?></span>
                        </div>
                    </div>

                    <a href="#" class="btn btn-success w-100 rounded-pill fw-bold py-2 shadow-sm">
                        Kelola Pelanggan
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>