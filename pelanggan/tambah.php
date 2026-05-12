<?php
define('BASE_URL', '..'); 
require_once __DIR__ . '/../config/database.php';

$halaman = 'Tambah Pelanggan';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$pesan = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode    = $_POST['kode_pelanggan'];
    $nama    = $_POST['nama_pelanggan'];
    $telp    = $_POST['no_telepon'];
    $alamat  = $_POST['alamat'];
    $bpjs    = $_POST['no_bpjs'];
    $jenis   = $_POST['jenis_pelanggan'];

    $sql = "INSERT INTO pelanggan (kode_pelanggan, nama_pelanggan, no_telepon, alamat, no_bpjs, jenis_pelanggan, is_active) 
            VALUES (?, ?, ?, ?, ?, ?, 1)";
    
    $params = array($kode, $nama, $telp, $alamat, $bpjs, $jenis);
    $exec = sqlsrv_query($conn, $sql, $params);

    if ($exec) {
        $pesan = "<div class='alert alert-success'>Pelanggan baru berhasil didaftarkan!</div>";
        echo "<script>setTimeout(() => { window.location.href = 'index.php'; }, 1500);</script>";
    } else {
        $pesan = "<div class='alert alert-danger'>Gagal: " . print_r(sqlsrv_errors(), true) . "</div>";
    }
}
?>

<div class="container-fluid">
    <div class="page-header mt-4">
        <h4><i class="bi bi-person-plus me-2 text-primary"></i>Tambah Pelanggan</h4>
    </div>

    <div class="card mt-3 shadow-sm" style="max-width: 800px;">
        <div class="card-body">
            <?= $pesan ?>
            <form method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Kode Pelanggan</label>
                            <input type="text" name="kode_pelanggan" class="form-control" placeholder="PLG-00x" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama_pelanggan" class="form-control" placeholder="Nama Pasien" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No. Telepon / HP</label>
                            <input type="text" name="no_telepon" class="form-control" placeholder="08xxxx">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Jenis Pelanggan</label>
                            <select name="jenis_pelanggan" class="form-select" id="jenis_pelanggan" onchange="cekBPJS()">
                                <option value="umum">Umum (Tunai)</option>
                                <option value="bpjs">Peserta BPJS</option>
                            </select>
                        </div>
                        <div class="mb-3 d-none" id="field_bpjs">
                            <label class="form-label">Nomor Kartu BPJS</label>
                            <input type="text" name="no_bpjs" class="form-control" placeholder="000xxxx">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat Lengkap</label>
                            <textarea name="alamat" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="d-flex justify-content-end gap-2">
                    <a href="index.php" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-success px-4">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function cekBPJS() {
    const jenis = document.getElementById('jenis_pelanggan').value;
    const field = document.getElementById('field_bpjs');
    if(jenis === 'bpjs') {
        field.classList.remove('d-none');
    } else {
        field.classList.add('d-none');
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>