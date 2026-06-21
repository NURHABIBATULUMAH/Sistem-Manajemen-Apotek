<?php
// ============================================================
// pelanggan/edit.php - Edit Data Pelanggan (sesuai ERD)
// ============================================================

define('BASE_URL', '..'); 
require_once __DIR__ . '/../config/database.php';

$halaman = 'Edit Pelanggan';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$id = $_GET['id'] ?? null;
$p  = db_fetch_one($conn, "SELECT * FROM pelanggan WHERE id_pelanggan = ?", '', $id);
if (!$p) { header('Location: index.php'); exit; }

$pesan = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama   = $_POST['nama_pelanggan'];
    $telp   = $_POST['no_telepon'] ?: NULL;
    $alamat = $_POST['alamat'] ?: NULL;
    $bpjs   = $_POST['no_bpjs'] ?: NULL;
    $jenis  = $_POST['jenis_pelanggan'];

    $sql = "UPDATE pelanggan SET 
                nama_pelanggan  = ?, 
                no_telepon      = ?, 
                alamat          = ?, 
                no_bpjs         = ?, 
                jenis_pelanggan = ? 
            WHERE id_pelanggan  = ?";

    $params = [$nama, $telp, $alamat, $bpjs, $jenis, $id];
    $exec   = sqlsrv_query($conn, $sql, $params);

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
            <h4 class="fw-bold mb-0 text-dark">
                <i class="bi bi-pencil-square me-2 text-primary"></i>Edit Profil Pelanggan
            </h4>
            <p class="text-muted small mb-0">Perbarui informasi data diri pelanggan.</p>
        </div>
        <a href="index.php" class="btn btn-outline-secondary rounded-3">Batal</a>
    </div>

    <div class="card-custom shadow-sm border-0 p-4 bg-white rounded-4" style="max-width: 700px;">
        <?= $pesan ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-bold">Kode Pelanggan</label>
                <input type="text" class="form-control bg-light fw-bold" 
                       value="<?= $p['kode_pelanggan'] ?>" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" name="nama_pelanggan" class="form-control" 
                       value="<?= htmlspecialchars($p['nama_pelanggan']) ?>" required>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Nomor Telepon</label>
                    <input type="text" name="no_telepon" class="form-control" 
                           value="<?= $p['no_telepon'] ?? '' ?>" placeholder="08xxxx">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Jenis Pelanggan</label>
                    <select name="jenis_pelanggan" class="form-select" id="jenis_pelanggan" 
                            onchange="toggleBPJS()">
                        <option value="Umum" <?= $p['jenis_pelanggan'] == 'Umum' ? 'selected' : '' ?>>Umum</option>
                        <option value="BPJS" <?= $p['jenis_pelanggan'] == 'BPJS' ? 'selected' : '' ?>>BPJS</option>
                    </select>
                </div>
            </div>

            <div class="mb-3 <?= $p['jenis_pelanggan'] == 'BPJS' ? '' : 'd-none' ?>" id="div_bpjs">
                <label class="form-label fw-bold text-primary">Nomor Kartu BPJS</label>
                <input type="text" name="no_bpjs" class="form-control border-primary" 
                       value="<?= $p['no_bpjs'] ?? '' ?>">
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Alamat Lengkap</label>
                <textarea name="alamat" class="form-control" rows="3"><?= htmlspecialchars($p['alamat'] ?? '') ?></textarea>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="index.php" class="btn btn-light px-4">Batal</a>
                <button type="submit" class="btn btn-primary px-5 shadow-sm rounded-3">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleBPJS() {
    const jenis = document.getElementById('jenis_pelanggan').value;
    document.getElementById('div_bpjs').classList.toggle('d-none', jenis !== 'BPJS');
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
