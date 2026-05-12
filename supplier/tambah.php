<?php
// ============================================================
// supplier/tambah.php — Form Tambah Data Supplier
// ============================================================

define('BASE_URL', '..'); 
require_once __DIR__ . '/../config/database.php';

$halaman = 'Tambah Supplier';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$pesan = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode    = $_POST['kode_supplier'];
    $nama    = $_POST['nama_supplier'];
    $alamat  = $_POST['alamat'];
    $telp    = $_POST['no_telepon'];
    $cp      = $_POST['contact_person']; // Ini CP Utama/Kantor

    $sql = "INSERT INTO supplier (kode_supplier, nama_supplier, alamat, no_telepon, contact_person, is_active) 
            VALUES (?, ?, ?, ?, ?, 1)";
    
    $params = array($kode, $nama, $alamat, $telp, $cp);
    $exec = sqlsrv_query($conn, $sql, $params);

    if ($exec) {
        $pesan = "<div class='alert alert-success'>Supplier berhasil didaftarkan!</div>";
        echo "<script>setTimeout(() => { window.location.href = 'index.php'; }, 1500);</script>";
    } else {
        $pesan = "<div class='alert alert-danger'>Gagal: " . print_r(sqlsrv_errors(), true) . "</div>";
    }
}
?>

<div class="container-fluid">
    <div class="page-header mt-4">
        <h4><i class="bi bi-truck me-2 text-primary"></i>Tambah Supplier Baru</h4>
    </div>

    <div class="card mt-3 shadow-sm" style="max-width: 700px;">
        <div class="card-body">
            <?= $pesan ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Kode Supplier</label>
                    <input type="text" name="kode_supplier" class="form-control" placeholder="Contoh: SUP-KF" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama Perusahaan Supplier</label>
                    <input type="text" name="nama_supplier" class="form-control" placeholder="Contoh: PT Kimia Farma" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Alamat Kantor</label>
                    <textarea name="alamat" class="form-control" rows="2"></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Telepon Kantor</label>
                        <input type="text" name="no_telepon" class="form-control" placeholder="031-xxxx">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Contact Person Utama</label>
                        <input type="text" name="contact_person" class="form-control" placeholder="Nama Manager/Admin">
                    </div>
                </div>
                <hr>
                <div class="d-flex justify-content-end gap-2">
                    <a href="index.php" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Supplier</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>