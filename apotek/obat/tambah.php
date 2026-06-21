<?php
define('BASE_URL', '..'); 
require_once __DIR__ . '/../config/database.php';
session_start();

if (!isset($_SESSION['petugas'])) { header('Location: ../login.php'); exit; }

// --- LOGIKA GENERATE KODE AMAN ---
$query_max = db_fetch_one($conn, "SELECT MAX(kode_obat) as max_kode FROM obat");
$max_num   = 0;
if ($query_max['max_kode']) {
    $max_num = (int) substr($query_max['max_kode'], 4);
}
$kode_auto = "OBT-" . str_pad($max_num + 1, 3, "0", STR_PAD_LEFT);

$pesan = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "INSERT INTO obat (kode_obat, nama_obat, harga_beli, harga_jual, stok, is_active) 
            VALUES (?, ?, ?, ?, 0, 1)";
    $params = array($_POST['kode_obat'], $_POST['nama_obat'], $_POST['harga_beli'], $_POST['harga_jual']);
    
    if (sqlsrv_query($conn, $sql, $params)) {
        echo "<script>alert('Obat Berhasil Disimpan!'); window.location.href='index.php';</script>";
    } else {
        $error_db = print_r(sqlsrv_errors(), true);
        $pesan = "<div class='alert alert-danger'>Gagal Simpan: $error_db</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Obat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/sidebar.php'; ?>
<div id="content">
    <div class="container-fluid">
        <h4 class="fw-bold mb-4">Tambah Master Obat</h4>
        <div class="card-custom shadow-sm p-4 bg-white" style="max-width: 500px;">
            <?= $pesan ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="text-label mb-2">KODE OBAT</label>
                    <input type="text" name="kode_obat" class="form-control bg-light fw-bold text-primary" value="<?= $kode_auto ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="text-label mb-2">NAMA OBAT</label>
                    <input type="text" name="nama_obat" class="form-control" placeholder="..." required>
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="text-label mb-2">HARGA BELI</label>
                        <input type="number" name="harga_beli" class="form-control" required>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="text-label mb-2">HARGA JUAL</label>
                        <input type="number" name="harga_jual" class="form-control" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold rounded-3 mt-3 shadow">SIMPAN MASTER</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>