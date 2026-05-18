<?php
// ============================================================
// supplier/edit.php - Edit Data Supplier
// ============================================================

define('BASE_URL', '..'); 
require_once __DIR__ . '/../config/database.php';

$halaman = 'Edit Supplier';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$id = $_GET['id'] ?? null;

// Ambil data lama - Pastikan $id ikut dikirim sebagai parameter jika db_fetch_one membutuhkan array
$p = db_fetch_one($conn, "SELECT * FROM supplier WHERE id_supplier = ?", '', array($id));

if (!$p) { 
    header('Location: index.php'); 
    exit; 
}

$pesan = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama    = $_POST['nama_supplier'];
    $telp    = $_POST['no_telepon'];
    $contact = $_POST['contact_person'];

    // PERBAIKAN 1: Menghilangkan koma sebelum WHERE
    $sql = "UPDATE supplier SET 
                nama_supplier = ?, 
                no_telepon = ?, 
                contact_person = ? 
            WHERE id_supplier = ?";
            
    // PERBAIKAN 2: Menambahkan $id ke dalam array params (Total 4 parameter)
    $params = array($nama, $telp, $contact, $id);
    $exec = sqlsrv_query($conn, $sql, $params);

    if ($exec) {
        $pesan = "<div class='alert alert-success shadow-sm'>Data berhasil diperbarui!</div>";
        echo "<script>setTimeout(() => { window.location.href = 'index.php'; }, 1500);</script>";
    } else {
        $pesan = "<div class='alert alert-danger'>Gagal: " . print_r(sqlsrv_errors(), true) . "</div>";
    }
}
?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0 text-dark"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Profil Supplier</h4>
            <p class="text-muted small mb-0">Perbarui informasi Supplier.</p>
        </div>
    </div>

    <div class="card-custom shadow-sm border-0 p-4 bg-white" style="max-width: 900px;">
        <?= $pesan ?>
        <form method="POST">
            <div class="row g-3">
                <div class="col-md-12 mb-3 text-start">
                    <label class="form-label">Kode Supplier</label>
                    <input type="text" class="form-control bg-light fw-bold" value="<?= htmlspecialchars($p['kode_supplier'] ?? '') ?>" readonly>
                </div>

                <div class="col-md-12 mb-3 text-start">
                    <label class="form-label">Nama Supplier</label>
                    <input type="text" name="nama_supplier" class="form-control" value="<?= htmlspecialchars($p['nama_supplier'] ?? '') ?>" required>
                </div>

                <!-- PERBAIKAN 3: Mengubah name="text" menjadi nama field yang sesuai -->
                <div class="col-md-12 mb-3 text-start">
                    <label class="form-label">Nomor Telepon</label>
                    <input type="text" name="no_telepon" class="form-control" value="<?= htmlspecialchars($p['no_telepon'] ?? '') ?>">
                </div>

                <div class="col-md-12 mb-3 text-start">
                    <label class="form-label">Contact Person</label>
                    <input type="text" name="contact_person" class="form-control" value="<?= htmlspecialchars($p['contact_person'] ?? '') ?>">
                </div>
                
                <div class="col-md-12 text-start mt-3">
                    <a href="index.php" class="btn btn-outline-secondary rounded-3 me-2">Batal</a>
                    <button type="submit" class="btn btn-primary px-5 shadow-sm rounded-3">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>