<?php
// ============================================================
// obat/edit_master.php - Edit Data Master
// ============================================================

define('BASE_URL', '..'); 
require_once __DIR__ . '/../config/database.php';
session_start();

if (!isset($_SESSION['petugas'])) { header('Location: ../login.php'); exit; }

$id = $_GET['id'] ?? null;
if (!$id) { header('Location: master.php'); exit; }

// --- 1. AMBIL DATA LAMA ---
$obat = db_fetch_one($conn, "SELECT * FROM obat WHERE id_obat = ?", [$id]);
if (!$obat) { die("Data obat tidak ditemukan!"); }

$pesan = "";
// --- 2. PROSES UPDATE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama   = $_POST['nama_obat'];
    $h_beli = $_POST['harga_beli'];
    $h_jual = $_POST['harga_jual'];

    $sql = "UPDATE obat SET nama_obat = ?, harga_beli = ?, harga_jual = ? WHERE id_obat = ?";
    $params = [$nama, $h_beli, $h_jual, $id];
    $exec = sqlsrv_query($conn, $sql, $params);

    if ($exec) {
        echo "<script>alert('Data Master Berhasil Diupdate!'); window.location.href='master.php';</script>";
    } else {
        $pesan = "<div class='alert alert-danger'>Gagal Update: " . print_r(sqlsrv_errors(), true) . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Master Obat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include '../includes/sidebar.php'; ?>

<div id="content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Edit Master Obat</h4>
            <a href="master.php" class="btn btn-outline-secondary rounded-pill px-4">Batal</a>
        </div>

        <div class="card-custom shadow-sm p-4 bg-white" style="max-width: 600px;">
            <?= $pesan ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="text-label mb-2">KODE OBAT</label>
                    <input type="text" class="form-control bg-light fw-bold" value="<?= $obat['kode_obat'] ?>" readonly>
                    <small class="text-muted">Kode obat tidak dapat diubah (Unique Key).</small>
                </div>
                
                <div class="mb-4">
                    <label class="text-label mb-2">NAMA OBAT</label>
                    <input type="text" name="nama_obat" class="form-control py-2" value="<?= $obat['nama_obat'] ?>" required>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <label class="text-label mb-2">HARGA BELI (RP)</label>
                        <input type="number" name="harga_beli" class="form-control py-2" value="<?= (int)$obat['harga_beli'] ?>" required>
                    </div>
                    <div class="col-6">
                        <label class="text-label mb-2">HARGA JUAL (RP)</label>
                        <input type="number" name="harga_jual" class="form-control py-2" value="<?= (int)$obat['harga_jual'] ?>" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-3 shadow">
                    <i class="bi bi-check-circle me-2"></i> SIMPAN PERUBAHAN
                </button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>