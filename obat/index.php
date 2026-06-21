<?php
define('BASE_URL', '..'); 
require_once __DIR__ . '/../config/database.php';
session_start();

if (!isset($_SESSION['petugas'])) { header('Location: ../login.php'); exit; }

// --- QUERY PERBAIKAN: Menggabungkan stok dengan GROUP BY dan SUM ---
$sql = "SELECT 
            o.nama_obat, 
            pd.tgl_kadaluarsa, 
            SUM(pd.stok_sisa) AS stok_sisa,       -- Menjumlahkan stok dari baris yang sama
            MAX(pd.harga_satuan) AS harga_satuan, -- Mengambil harga jika ada selisih di pembelian beda waktu
            DATEDIFF(day, CAST(GETDATE() AS DATE), pd.tgl_kadaluarsa) as sisa_hari,
            CASE
                WHEN pd.tgl_kadaluarsa < CAST(GETDATE() AS DATE) THEN 'Kadaluarsa'
                WHEN DATEDIFF(day, CAST(GETDATE() AS DATE), pd.tgl_kadaluarsa) <= 30 THEN 'Kritis'
                WHEN DATEDIFF(day, CAST(GETDATE() AS DATE), pd.tgl_kadaluarsa) <= 90 THEN 'Perhatian'
                ELSE 'Aman'
            END AS status_stok
        FROM pembelian_detail pd 
        LEFT JOIN obat o ON pd.id_obat = o.id_obat 
        WHERE pd.stok_sisa > 0 
        GROUP BY 
            o.nama_obat,            -- Kelompokkan berdasarkan Nama Obat
            pd.tgl_kadaluarsa       -- Kelompokkan berdasarkan Tanggal Expired
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
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">Monitoring Stok (FEFO)</h4>
                <p class="text-muted small mb-0">Stok ditampilkan per batch tanggal kadaluarsa.</p>
            </div>
            <a href="master.php" class="btn btn-dark rounded-pill px-4 shadow-sm">
                <i class="bi bi-gear-fill me-1"></i> Kelola Data Master
            </a>
        </div>

        <div class="card shadow-sm border-0 overflow-hidden bg-white" style="border-radius: 15px;">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-muted text-uppercase">
                            <th class="ps-4 py-3">Nama Obat</th>
                            <th>Kadaluarsa</th>
                            <th class="text-center">Sisa Stok</th>
                            <th>Harga Beli (Max)</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($stok_list)): ?>
                            <tr><td colspan="5" class="text-center py-5 text-muted">Belum ada stok aktif di gudang.</td></tr>
                        <?php else: ?>
                            <?php foreach ($stok_list as $s): 
                                // Penyesuaian warna badge berdasarkan string output
                                switch ($s['status_stok']) {
                                    case 'Kadaluarsa':
                                        $badge_class = 'bg-danger text-white';
                                        break;
                                    case 'Kritis':
                                        $badge_class = 'bg-danger-subtle text-danger';
                                        break;
                                    case 'Perhatian':
                                        $badge_class = 'bg-warning-subtle text-warning-emphasis';
                                        break;
                                    default: // 'Aman'
                                        $badge_class = 'bg-success-subtle text-success';
                                        break;
                                }
                                
                                // Amankan format tanggal (jika dari SQL Server berupa objek DateTime)
                                $tgl_exp = is_string($s['tgl_kadaluarsa']) ? date('d M Y', strtotime($s['tgl_kadaluarsa'])) : $s['tgl_kadaluarsa']->format('d M Y');
                            ?>
                            <tr>
                                <td class="ps-4 fw-semibold text-dark"><?= htmlspecialchars($s['nama_obat']) ?></td>
                                <td><i class="bi bi-calendar3 me-2 text-secondary"></i><?= $tgl_exp ?></td>
                                <td class="text-center fw-bold text-primary"><?= number_format($s['stok_sisa']) ?></td>
                                <td>Rp <?= number_format($s['harga_satuan'], 0, ',', '.') ?></td>
                                <td class="text-center">
                                    <span class="badge <?= $badge_class ?> rounded-pill px-3 py-2 fw-bold small">
                                        <?= htmlspecialchars($s['status_stok']) ?> 
                                        <small class="d-block fw-normal" style="font-size: 0.75rem;">
                                            <?= ($s['sisa_hari'] < 0) ? 'Sudah lewat ' . abs($s['sisa_hari']) . ' Hari' : $s['sisa_hari'] . ' Hari Lagi' ?>
                                        </small>
                                    </span>
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