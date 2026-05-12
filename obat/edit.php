<?php
define('BASE_URL', '..'); 
require_once __DIR__ . '/../config/database.php'; 
session_start();

$id = $_GET['id'] ?? die("ID Tidak Ada");
$obat = db_fetch_one($conn, "SELECT * FROM obat WHERE id_obat = ?", '', $id);

$halaman = 'Edit Master Obat';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<div class="container-fluid">
    <h4 class="mt-4">Edit Data Obat</h4>
    <div class="card shadow-sm mt-3 col-md-6">
        <form action="update.php" method="POST">
            <input type="hidden" name="id_obat" value="<?= $obat['id_obat'] ?>">
            <div class="card-body">
                <div class="mb-3">
                    <label class="fw-bold small">Kode Obat</label>
                    <input type="text" class="form-control bg-light" value="<?= $obat['kode_obat'] ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="fw-bold small">Nama Obat</label>
                    <input type="text" name="nama_obat" class="form-control" value="<?= $obat['nama_obat'] ?>" required>
                </div>
                <div class="mb-3">
                    <label class="fw-bold small">Harga Jual (Rp)</label>
                    <input type="number" name="harga_jual" class="form-control" value="<?= (int)$obat['harga_jual'] ?>" required>
                </div>
                <div class="mb-0">
                    <label class="fw-bold small">Status</label>
                    <select name="is_active" class="form-select">
                        <option value="1" <?= $obat['is_active'] == 1 ? 'selected' : '' ?>>Aktif</option>
                        <option value="0" <?= $obat['is_active'] == 0 ? 'selected' : '' ?>>Non-Aktif</option>
                    </select>
                </div>
            </div>
            <div class="card-footer text-end">
                <a href="index.php" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary px-4">Update Data</button>
            </div>
        </form>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>