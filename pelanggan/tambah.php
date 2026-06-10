<?php
// ============================================================
// pelanggan/tambah.php - Tambah Pelanggan Baru (sesuai ERD)
// ============================================================

define('BASE_URL', '..'); 
require_once __DIR__ . '/../config/database.php';

$halaman = 'Tambah Pelanggan';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

// --- GENERATE KODE PELANGGAN OTOMATIS ---
$query_count = db_fetch_one($conn, "SELECT COUNT(*) AS total FROM pelanggan");
$next_id     = ($query_count['total'] ?? 0) + 1;
$kode_auto   = "PLG-" . str_pad($next_id, 3, "0", STR_PAD_LEFT);

$pesan = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode   = $_POST['kode_pelanggan'];
    $nama   = $_POST['nama_pelanggan'];
    $telp   = $_POST['no_telepon'] ?: NULL;
    $alamat = $_POST['alamat'] ?: NULL;
    $bpjs   = $_POST['no_bpjs'] ?: NULL;
    $jenis  = $_POST['jenis_pelanggan'];

    $sql = "INSERT INTO pelanggan 
                (kode_pelanggan, nama_pelanggan, no_telepon, alamat, 
                 no_bpjs, jenis_pelanggan, is_active, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, 1, GETDATE())";

    $params = [$kode, $nama, $telp, $alamat, $bpjs, $jenis];
    $exec   = sqlsrv_query($conn, $sql, $params);

    if ($exec) {
        $pesan = "<div class='alert alert-success shadow-sm rounded-3'>Pelanggan baru berhasil didaftarkan!</div>";
        echo "<script>setTimeout(() => { window.location.href = 'index.php'; }, 1500);</script>";
    } else {
        $pesan = "<div class='alert alert-danger rounded-3'>Gagal menyimpan: " . print_r(sqlsrv_errors(), true) . "</div>";
    }
}
?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0 text-dark">
                <i class="bi bi-person-plus me-2 text-primary"></i>Registrasi Pelanggan Baru
            </h4>
            <p class="text-muted small mb-0">Lengkapi formulir di bawah untuk menambahkan pelanggan ke sistem.</p>
        </div>
        <a href="index.php" class="btn btn-outline-secondary rounded-3">Batal</a>
    </div>

    <div class="card-custom shadow-sm border-0 p-4 bg-white rounded-4" style="max-width: 700px;">
        <?= $pesan ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-bold">Kode Pelanggan (Otomatis)</label>
                <input type="text" name="kode_pelanggan" class="form-control bg-light fw-bold" 
                       value="<?= $kode_auto ?>" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" name="nama_pelanggan" class="form-control" 
                       placeholder="Masukkan nama lengkap pelanggan" required>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Nomor Telepon</label>
                    <input type="text" name="no_telepon" class="form-control" 
                           placeholder="08123456789">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Kategori Pelanggan</label>
                    <select name="jenis_pelanggan" class="form-select" id="jenis_pelanggan" 
                            onchange="toggleBPJS()">
                        <option value="Umum">Umum</option>
                        <option value="BPJS">BPJS</option>
                    </select>
                </div>
            </div>

            <div class="mb-3 d-none" id="div_bpjs">
                <label class="form-label fw-bold text-primary">Nomor Kartu BPJS</label>
                <input type="text" name="no_bpjs" class="form-control border-primary" 
                       placeholder="13 digit nomor BPJS">
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Alamat Lengkap</label>
                <textarea name="alamat" class="form-control" rows="3" 
                          placeholder="Masukkan alamat domisili"></textarea>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="reset" class="btn btn-light px-4">Reset</button>
                <button type="submit" class="btn btn-primary px-5 shadow-sm rounded-3 fw-bold">
                    Daftarkan Pelanggan
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
