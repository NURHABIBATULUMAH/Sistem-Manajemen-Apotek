<?php
define('BASE_URL', '..'); 
require_once __DIR__ . '/../config/database.php';

$halaman = 'Edit Pelanggan';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$id = $_GET['id'] ?? null;
$p = db_fetch_one($conn, "SELECT * FROM pelanggan WHERE id_pelanggan = ?", '', $id);

if (!$p) { header('Location: index.php'); exit; }

$pesan = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama    = $_POST['nama_pelanggan'];
    $telp    = $_POST['no_telepon'];
    $alamat  = $_POST['alamat'];
    $bpjs    = $_POST['no_bpjs'];
    $jenis   = $_POST['jenis_pelanggan'];

    $sql = "UPDATE pelanggan SET nama_pelanggan=?, no_telepon=?, alamat=?, no_bpjs=?, jenis_pelanggan=? WHERE id_pelanggan=?";
    $params = array($nama, $telp, $alamat, $bpjs, $jenis, $id);
    $exec = sqlsrv_query($conn, $sql, $params);

    if ($exec) {
        $pesan = "<div class='alert alert-success'>Data berhasil diperbarui!</div>";
        echo "<script>setTimeout(() => { window.location.href = 'index.php'; }, 1500);</script>";
    }
}
?>

<div class="container-fluid">
    <div class="page-header mt-4">
        <h4><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Data Pelanggan</h4>
    </div>

    <div class="card mt-3 shadow-sm" style="max-width: 800px;">
        <div class="card-body">
            <?= $pesan ?>
            <form method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Kode Pelanggan (Tidak bisa diubah)</label>
                            <input type="text" class="form-control bg-light" value="<?= $p['kode_pelanggan'] ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama_pelanggan" class="form-control" value="<?= $p['nama_pelanggan'] ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" name="no_telepon" class="form-control" value="<?= $p['no_telepon'] ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Jenis Pelanggan</label>
                            <select name="jenis_pelanggan" class="form-select" id="jenis_pelanggan" onchange="cekBPJS()">
                                <option value="umum" <?= $p['jenis_pelanggan'] == 'umum' ? 'selected' : '' ?>>Umum</option>
                                <option value="bpjs" <?= $p['jenis_pelanggan'] == 'bpjs' ? 'selected' : '' ?>>BPJS</option>
                            </select>
                        </div>
                        <div class="mb-3 <?= $p['jenis_pelanggan'] == 'bpjs' ? '' : 'd-none' ?>" id="field_bpjs">
                            <label class="form-label">No. BPJS</label>
                            <input type="text" name="no_bpjs" class="form-control" value="<?= $p['no_bpjs'] ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="3"><?= $p['alamat'] ?></textarea>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="d-flex justify-content-end gap-2">
                    <a href="index.php" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary px-4">Update Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function cekBPJS() {
    const jenis = document.getElementById('jenis_pelanggan').value;
    const field = document.getElementById('field_bpjs');
    field.classList.toggle('d-none', jenis !== 'bpjs');
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>