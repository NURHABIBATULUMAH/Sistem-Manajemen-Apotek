<?php
define('BASE_URL', '..'); 
require_once __DIR__ . '/../config/database.php';
session_start();
$halaman = 'Tambah Resep';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

// Ambil nomor resep berikutnya
$cek = db_fetch_one($conn, "SELECT TOP 1 no_resep FROM resep_header ORDER BY id_resep DESC");
$no_urut = ($cek) ? (int)substr($cek['no_resep'], 2) + 1 : 1;
$no_resep_baru = "R-" . str_pad($no_urut, 2, "0", STR_PAD_LEFT);

$pelanggan = db_fetch_all($conn, "SELECT id_pelanggan, nama_pelanggan FROM pelanggan WHERE is_active = 1");
$obat = db_fetch_all($conn, "SELECT id_obat, nama_obat FROM obat WHERE is_active = 1");
?>

<div class="container-fluid">
    <h4 class="mt-4"><i class="bi bi-file-earmark-medical me-2 text-info"></i>Input Resep Dokter</h4>
    <form action="simpan_resep.php" method="POST">
        <div class="row mt-3">
            <div class="col-md-4">
                <div class="card shadow-sm border-info">
                    <div class="card-body">
                        <div class="mb-3"><label class="fw-bold">No. Resep</label><input type="text" name="no_resep" class="form-control fw-bold text-primary" value="<?= $no_resep_baru ?>" readonly></div>
                        <div class="mb-3"><label class="fw-bold">Pasien</label><select name="id_pelanggan" class="form-select" required><option value="">-- Pilih --</option><?php foreach($pelanggan as $p): ?><option value="<?= $p['id_pelanggan'] ?>"><?= $p['nama_pelanggan'] ?></option><?php endforeach; ?></select></div>
                        <div class="mb-3"><label class="fw-bold">Dokter</label><input type="text" name="nama_dokter" class="form-control" required></div>
                        <div class="mb-0"><label class="fw-bold">Tanggal</label><input type="date" name="tgl_resep" class="form-control" value="<?= date('Y-m-d') ?>"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between"><span>Detail Obat</span><button type="button" class="btn btn-sm btn-info text-white" onclick="tambah()">+ Baris</button></div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead class="table-light"><tr><th>Obat</th><th width="10%">Qty</th><th>Dosis</th><th>Aturan</th><th width="5%"></th></tr></thead>
                            <tbody id="box"></tbody>
                        </table>
                    </div>
                    <div class="card-footer text-end"><button type="submit" class="btn btn-info text-white px-5 fw-bold">SIMPAN RESEP</button></div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let idx = 0; const obs = <?= json_encode($obat) ?>;
function tambah() {
    idx++; const row = `<tr id="r-${idx}">
        <td><select name="items[${idx}][id_obat]" class="form-select form-select-sm" required><option value="">-- Pilih --</option>${obs.map(o => `<option value="${o.id_obat}">${o.nama_obat}</option>`).join('')}</select></td>
        <td><input type="number" name="items[${idx}][qty]" class="form-control form-control-sm" value="1" required></td>
        <td><input type="text" name="items[${idx}][dosis]" class="form-control form-control-sm" placeholder="3x1" required></td>
        <td><select name="items[${idx}][aturan_pakai]" class="form-select form-select-sm" required><option value="Sesudah Makan">Sesudah Makan</option><option value="Sebelum Makan">Sebelum Makan</option><option value="Saat Perut Kosong">Saat Perut Kosong</option></select></td>
        <td><button type="button" class="btn btn-sm text-danger" onclick="document.getElementById('r-${idx}').remove()">x</button></td></tr>`;
    document.getElementById('box').insertAdjacentHTML('beforeend', row);
}
tambah();
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>