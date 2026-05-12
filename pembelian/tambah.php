<?php
// ============================================================
// pembelian/tambah.php — Form Stok Masuk (Auto-Faktur & FEFO)
// ============================================================

define('BASE_URL', '..'); 
require_once __DIR__ . '/../config/database.php';
session_start();

$halaman = 'Tambah Stok Supplier';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

// --- LOGIKA NOMOR PEMBELIAN OTOMATIS (FKT-20260512-001) ---
$hari_ini = date('Ymd'); // Hasilnya: 20260512
$query_cek = "SELECT TOP 1 no_pembelian FROM pembelian_header 
              WHERE no_pembelian LIKE 'FKT-$hari_ini%' 
              ORDER BY no_pembelian DESC";
$cek_data = db_fetch_one($conn, $query_cek);

if ($cek_data) {
    // Jika sudah ada transaksi hari ini, ambil 3 digit terakhir dan tambah 1
    $last_no = (int)substr($cek_data['no_pembelian'], -3);
    $no_urut = $last_no + 1;
} else {
    // Jika belum ada transaksi hari ini, mulai dari 1
    $no_urut = 1;
}
$nomor_otomatis = "FKT-" . $hari_ini . "-" . str_pad($no_urut, 3, "0", STR_PAD_LEFT);

// Ambil Data Master
$supplier = db_fetch_all($conn, "SELECT id_supplier, nama_supplier FROM supplier WHERE is_active = 1");
$obat_list = db_fetch_all($conn, "SELECT id_obat, nama_obat FROM obat WHERE is_active = 1");
?>

<div class="container-fluid">
    <div class="page-header mt-4">
        <h4><i class="bi bi-truck me-2 text-primary"></i>Input Stok Masuk Supplier</h4>
        <p class="text-muted">Gunakan form ini untuk mencatat obat yang datang dari supplier.</p>
    </div>
    
    <form action="simpan_pembelian.php" method="POST">
        <div class="row mt-3">
            <div class="col-md-3">
                <div class="card shadow-sm border-primary mb-3">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="fw-bold small">Nomor Pembelian</label>
                            <input type="text" name="no_pembelian" class="form-control fw-bold text-primary bg-light" 
                                   value="<?= $nomor_otomatis ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold small">Supplier</label>
                            <select name="id_supplier" class="form-select border-primary" required>
                                <option value="">-- Pilih Supplier --</option>
                                <?php foreach($supplier as $s): ?>
                                    <option value="<?= $s['id_supplier'] ?>"><?= $s['nama_supplier'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-0">
                            <label class="fw-bold small">Tgl. Pesan / Masuk</label>
                            <input type="date" name="tgl_pesan" class="form-control" value="<?= date('Y-m-d') ?>">
                        </div>
                    </div>
                </div>
                <div class="alert alert-info small">
                    <i class="bi bi-info-circle me-1"></i> 
                    Nomor faktur digenerate otomatis berdasarkan tanggal hari ini.
                </div>
            </div>

            <div class="col-md-9">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
                        <span class="fw-bold"><i class="bi bi-capsule-pill me-2"></i>Rincian Obat & Expired</span>
                        <button type="button" class="btn btn-sm btn-primary" onclick="tambahBaris()">
                            <i class="bi bi-plus-lg me-1"></i> Tambah Baris
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="35%">Nama Obat</th>
                                        <th width="12%">Qty</th>
                                        <th width="20%">Harga Beli</th>
                                        <th width="20%">Expired Date</th>
                                        <th width="5%"></th>
                                    </tr>
                                </thead>
                                <tbody id="area-pembelian">
                                    </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer text-end bg-white py-3">
                        <a href="index.php" class="btn btn-secondary me-2">Batal</a>
                        <button type="submit" class="btn btn-success px-5 fw-bold shadow">
                            <i class="bi bi-save me-2"></i> SIMPAN STOK
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let idx = 0;
const obats = <?= json_encode($obat_list) ?>;

/**
 * Fungsi untuk menambah baris obat secara dinamis
 */
function tambahBaris() {
    idx++;
    const row = `
    <tr id="r-${idx}">
        <td>
            <select name="items[${idx}][id_obat]" class="form-select form-select-sm" required>
                <option value="">-- Pilih Obat --</option>
                ${obats.map(o => `<option value="${o.id_obat}">${o.nama_obat}</option>`).join('')}
            </select>
        </td>
        <td>
            <input type="number" name="items[${idx}][qty]" class="form-control form-control-sm" min="1" placeholder="0" required>
        </td>
        <td>
            <input type="number" name="items[${idx}][harga_beli]" class="form-control form-control-sm" placeholder="Rp" required>
        </td>
        <td>
            <input type="date" name="items[${idx}][tgl_kadaluarsa]" class="form-control form-control-sm" required>
        </td>
        <td>
            <button type="button" class="btn btn-sm text-danger" onclick="document.getElementById('r-${idx}').remove()">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    </tr>`;
    document.getElementById('area-pembelian').insertAdjacentHTML('beforeend', row);
}

// Munculkan 1 baris kosong saat pertama kali load
tambahBaris();
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>