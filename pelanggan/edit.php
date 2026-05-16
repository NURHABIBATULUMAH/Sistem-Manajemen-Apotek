<?php
// ============================================================
// pelanggan/edit.php - Edit Data Pelanggan & Upload Foto
// ============================================================

define('BASE_URL', '..'); 
require_once __DIR__ . '/../config/database.php';

$halaman = 'Edit Pelanggan';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$id = $_GET['id'] ?? null;
// Ambil data lama
$p = db_fetch_one($conn, "SELECT * FROM pelanggan WHERE id_pelanggan = ?", '', $id);

if (!$p) { header('Location: index.php'); exit; }

$pesan = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama    = $_POST['nama_pelanggan'];
    $telp    = $_POST['no_telepon'];
    $alamat  = $_POST['alamat'];
    $bpjs    = $_POST['no_bpjs'];
    $jenis   = $_POST['jenis_pelanggan'];
    $gender  = $_POST['jenis_kelamin'];
    $email   = $_POST['email'];
    $tgl_lhr = $_POST['tgl_lahir'];
    
    $foto_nama = $p['foto']; // Default pakai foto lama

    // --- LOGIKA UPLOAD FOTO ---
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $nama_file_baru = "pelanggan_" . time() . "." . $ext;
        $tujuan = __DIR__ . '/../assets/img/profiles/' . $nama_file_baru;

        // Buat folder jika belum ada
        if (!is_dir(__DIR__ . '/../assets/img/profiles/')) {
            mkdir(__DIR__ . '/../assets/img/profiles/', 0777, true);
        }

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $tujuan)) {
            $foto_nama = $nama_file_baru;
        }
    }

    $sql = "UPDATE pelanggan SET 
                nama_pelanggan = ?, 
                no_telepon = ?, 
                alamat = ?, 
                no_bpjs = ?, 
                jenis_pelanggan = ?, 
                jenis_kelamin = ?, 
                email = ?, 
                tgl_lahir = ?, 
                foto = ? 
            WHERE id_pelanggan = ?";
            
    $params = array($nama, $telp, $alamat, $bpjs, $jenis, $gender, $email, $tgl_lhr, $foto_nama, $id);
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
            <h4 class="fw-bold mb-0 text-dark"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Profil Pelanggan</h4>
            <p class="text-muted small mb-0">Perbarui informasi data diri dan dokumen pelanggan.</p>
        </div>
        <a href="index.php" class="btn btn-outline-secondary rounded-3">Batal</a>
    </div>

    <div class="card-custom shadow-sm border-0 p-4 bg-white" style="max-width: 900px;">
        <?= $pesan ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="row g-4">
                <div class="col-md-4 text-center border-end">
                    <label class="form-label d-block">Foto Profil</label>
                    <img src="<?= BASE_URL ?>/assets/img/profiles/<?= $p['foto'] ?? 'default_pelanggan.png' ?>" 
                         class="rounded-circle border mb-3 p-1" width="150" height="150" id="previewFoto" style="object-fit: cover;">
                    <div class="mb-3">
                        <input type="file" name="foto" class="form-control form-control-sm" id="inputFoto" accept="image/*">
                        <small class="text-muted" style="font-size: 10px;">Format: JPG, PNG. Max: 2MB</small>
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label">Kode Pelanggan</label>
                        <input type="text" class="form-control bg-light fw-bold" value="<?= $p['kode_pelanggan'] ?>" readonly>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama_pelanggan" class="form-control" value="<?= htmlspecialchars($p['nama_pelanggan']) ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= $p['email'] ?? '' ?>" placeholder="contoh@mail.com">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nomor Telepon</label>
                            <input type="text" name="no_telepon" class="form-control" value="<?= $p['no_telepon'] ?? '' ?>" placeholder="08xxxx">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select">
                                <option value="Laki-laki" <?= ($p['jenis_kelamin'] ?? '') == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                                <option value="Perempuan" <?= ($p['jenis_kelamin'] ?? '') == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tgl_lahir" class="form-control" 
                                   value="<?= ($p['tgl_lahir'] instanceof DateTime) ? $p['tgl_lahir']->format('Y-m-d') : '' ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Pelanggan</label>
                            <select name="jenis_pelanggan" class="form-select" id="jenis_pelanggan" onchange="toggleBPJS()">
                                <option value="Umum" <?= $p['jenis_pelanggan'] == 'Umum' ? 'selected' : '' ?>>Umum</option>
                                <option value="BPJS" <?= $p['jenis_pelanggan'] == 'BPJS' ? 'selected' : '' ?>>BPJS</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3 <?= $p['jenis_pelanggan'] == 'BPJS' ? '' : 'd-none' ?>" id="div_bpjs">
                            <label class="form-label">Nomor Kartu BPJS</label>
                            <input type="text" name="no_bpjs" class="form-control" value="<?= $p['no_bpjs'] ?? '' ?>">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Alamat Lengkap</label>
                            <textarea name="alamat" class="form-control" rows="3"><?= $p['alamat'] ?? '' ?></textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-5 shadow-sm rounded-3">Simpan Perubahan</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Fungsi muncul/sembunyi field BPJS
function toggleBPJS() {
    const jenis = document.getElementById('jenis_pelanggan').value;
    const div = document.getElementById('div_bpjs');
    div.classList.toggle('d-none', jenis !== 'BPJS');
}

// Preview Foto Instan
document.getElementById('inputFoto').onchange = function (evt) {
    const [file] = this.files;
    if (file) {
        document.getElementById('previewFoto').src = URL.createObjectURL(file);
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>