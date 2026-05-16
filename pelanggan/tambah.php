<?php
// ============================================================
// pelanggan/tambah.php - Tambah Pelanggan Baru & Upload Foto
// ============================================================

define('BASE_URL', '..'); 
require_once __DIR__ . '/../config/database.php';

$halaman = 'Tambah Pelanggan';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

// --- LOGIKA GENERATE KODE PELANGGAN OTOMATIS ---
$query_count = db_fetch_one($conn, "SELECT COUNT(*) AS total FROM pelanggan");
$next_id     = ($query_count['total'] ?? 0) + 1;
$kode_auto   = "PLG-" . str_pad($next_id, 3, "0", STR_PAD_LEFT);

$pesan = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode    = $_POST['kode_pelanggan'];
    $nama    = $_POST['nama_pelanggan'];
    $telp    = $_POST['no_telepon'];
    $alamat  = $_POST['alamat'];
    $bpjs    = $_POST['no_bpjs'] ?? NULL;
    $jenis   = $_POST['jenis_pelanggan'];
    $gender  = $_POST['jenis_kelamin'];
    $email   = $_POST['email'];
    $tgl_lhr = $_POST['tgl_lahir'];
    
    $foto_nama = 'default_pelanggan.png'; // Default jika tidak upload

    // --- LOGIKA UPLOAD FOTO ---
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $nama_file_baru = "pelanggan_" . time() . "." . $ext;
        $tujuan = __DIR__ . '/../assets/img/profiles/' . $nama_file_baru;

        // Pastikan folder ada
        if (!is_dir(__DIR__ . '/../assets/img/profiles/')) {
            mkdir(__DIR__ . '/../assets/img/profiles/', 0777, true);
        }

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $tujuan)) {
            $foto_nama = $nama_file_baru;
        }
    }

    $sql = "INSERT INTO pelanggan (kode_pelanggan, nama_pelanggan, no_telepon, alamat, no_bpjs, jenis_pelanggan, jenis_kelamin, email, tgl_lahir, foto, is_active) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";
            
    $params = array($kode, $nama, $telp, $alamat, $bpjs, $jenis, $gender, $email, $tgl_lhr, $foto_nama);
    $exec = sqlsrv_query($conn, $sql, $params);

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
            <h4 class="fw-bold mb-0 text-dark"><i class="bi bi-person-plus me-2 text-primary"></i>Registrasi Pelanggan Baru</h4>
            <p class="text-muted small mb-0">Lengkapi formulir di bawah untuk menambahkan pasien/pelanggan ke sistem.</p>
        </div>
        <a href="index.php" class="btn btn-outline-secondary rounded-3">Batal</a>
    </div>

    <div class="card-custom shadow-sm border-0 p-4 bg-white rounded-4" style="max-width: 1000px;">
        <?= $pesan ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="row g-4">
                <div class="col-md-4 text-center border-end">
                    <label class="form-label d-block fw-bold">Foto Profil</label>
                    <div class="position-relative d-inline-block mb-3">
                        <img src="<?= BASE_URL ?>/assets/img/profiles/default_pelanggan.png" 
                             class="rounded-circle border p-1" width="160" height="160" id="previewFoto" style="object-fit: cover;">
                        <label for="inputFoto" class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2 shadow-sm" style="cursor: pointer;">
                            <i class="bi bi-camera-fill"></i>
                        </label>
                    </div>
                    <input type="file" name="foto" class="d-none" id="inputFoto" accept="image/*">
                    <p class="text-muted small mt-2">Klik ikon kamera untuk upload foto</p>
                    
                    <hr>
                    
                    <div class="mb-3 text-start">
                        <label class="form-label fw-bold">Kode Pelanggan (Otomatis)</label>
                        <input type="text" name="kode_pelanggan" class="form-control bg-light fw-bold text-center" value="<?= $kode_auto ?>" readonly>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama_pelanggan" class="form-control" placeholder="Masukkan nama lengkap pelanggan" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nomor Telepon</label>
                            <input type="text" name="no_telepon" class="form-control" placeholder="Contoh: 08123456789">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="pelanggan@mail.com">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select">
                                <option value="" disabled selected>Pilih jenis kelamin</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tanggal Lahir</label>
                            <input type="date" name="tgl_lahir" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Kategori Pelanggan</label>
                            <select name="jenis_pelanggan" class="form-select" id="jenis_pelanggan" onchange="toggleBPJS()">
                                <option value="Umum">Umum</option>
                                <option value="BPJS">BPJS</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3 d-none" id="div_bpjs">
                            <label class="form-label fw-bold text-primary">Nomor Kartu BPJS</label>
                            <input type="text" name="no_bpjs" class="form-control border-primary" placeholder="Masukkan 13 digit nomor BPJS">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Alamat Lengkap</label>
                            <textarea name="alamat" class="form-control" rows="3" placeholder="Masukkan alamat domisili sekarang"></textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="reset" class="btn btn-light px-4">Reset</button>
                        <button type="submit" class="btn btn-primary px-5 shadow-sm rounded-3 fw-bold">Daftarkan Pelanggan</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Logika untuk menampilkan input BPJS hanya jika kategori BPJS dipilih
function toggleBPJS() {
    const jenis = document.getElementById('jenis_pelanggan').value;
    const div = document.getElementById('div_bpjs');
    div.classList.toggle('d-none', jenis !== 'BPJS');
}

// Fitur Instant Preview untuk foto yang akan diupload
document.getElementById('inputFoto').onchange = function (evt) {
    const [file] = this.files;
    if (file) {
        document.getElementById('previewFoto').src = URL.createObjectURL(file);
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>