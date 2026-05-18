<?php
define('BASE_URL', '..'); 
require_once __DIR__ . '/../config/database.php';
session_start();

if (!isset($_SESSION['petugas'])) { header('Location: ../login.php'); exit; }

// Query Master: Hanya ambil data unik per obat
$sql = "SELECT id_obat, kode_obat, nama_obat, harga_beli, harga_jual FROM obat WHERE is_active = 1 ORDER BY id_obat ASC";
$master_list = db_fetch_all($conn, $sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Master Obat - Apotek Sehat</title>
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
                <h4 class="fw-bold mb-0">Pengaturan Master Obat</h4>
                <p class="text-muted small mb-0">Gunakan halaman ini untuk Edit atau Hapus data master.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="index.php" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
                    <i class="bi bi-arrow-left me-1"></i> Lihat Stok
                </a>
                <a href="tambah.php" class="btn btn-success fw-bold">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Baru
                </a>
            </div>
        </div>

        <div class="card-custom shadow-sm border-0 overflow-hidden bg-white">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="small text-muted">
                            <th class="ps-4 py-3">KODE</th>
                            <th>NAMA OBAT</th>
                            <th>HARGA JUAL</th>
                            <th class="text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($master_list as $m): ?>
                        <tr>
                            <td class="ps-4 text-primary fw-bold"><?= $m['kode_obat'] ?></td>
                            <td class="fw-bold"><?= $m['nama_obat'] ?></td>
                            <td>Rp <?= number_format($m['harga_jual'], 0, ',', '.') ?></td>
                            <td class="text-center">
                                <a href="edit_master.php?id=<?= $m['id_obat'] ?>" class="btn btn-sm btn-light border text-primary rounded-3 me-1">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <button onclick="hapusObat(<?= $m['id_obat'] ?>)" class="btn btn-sm btn-light border text-danger rounded-3">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function hapusObat(id) {
    if(confirm('Hapus obat ini dari sistem? (Catatan: Pastikan stok sudah 0 agar tidak error)')) {
        window.location.href = 'hapus_master.php?id=' + id;
    }
}
</script>
</body>
</html>