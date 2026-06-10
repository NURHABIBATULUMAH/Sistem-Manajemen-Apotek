<?php
// ============================================================
// resep/tambah.php - Versi Simple & Proteksi Stok Ketat
// ============================================================

define('BASE_URL', '..'); 
require_once __DIR__ . '/../config/database.php';
session_start();

if (!isset($_SESSION['petugas'])) { header('Location: ../login.php'); exit; }

// 1. DATA MASTER
$pelanggan = db_fetch_all($conn, "SELECT id_pelanggan, nama_pelanggan, kode_pelanggan FROM pelanggan WHERE is_active = 1");
// KODE BARU (MENGHITUNG TOTAL STOK BATCH YANG BELUM KADALUARSA)
// KODE BARU: Menghitung stok riil dari (Total Diterima di Pembelian) - (Total Terjual di Penjualan)
// KODE YANG SUDAH DIPERBAIKI (Mencegah nilai NULL jika obat belum pernah terjual)
// KODE YANG SUDAH DIPERBAIKI (Sinkron dengan FEFO Kasir & Anti Expired)
$sql_obat_fefo = "
    SELECT 
        o.id_obat, 
        o.nama_obat,
        (
            SELECT ISNULL(SUM(stok_sisa), 0) 
            FROM pembelian_detail 
            WHERE id_obat = o.id_obat 
              AND stok_sisa > 0 
              AND tgl_kadaluarsa >= CAST(GETDATE() AS DATE)
        ) AS stok
    FROM obat o
    WHERE o.is_active = 1
    ORDER BY o.nama_obat ASC
";

$obat_list = db_fetch_all($conn, $sql_obat_fefo);

// 2. LOGIKA SIMPAN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['items'])) {
    $no_resep = "RSP-" . date('YmdHis');
    $id_pel   = $_POST['id_pelanggan'];
    $dokter   = $_POST['nama_dokter'];
    $id_ptg   = $_SESSION['petugas']['id_petugas'];

    $sql_h = "INSERT INTO resep_header (no_resep, id_pelanggan, id_petugas, nama_dokter, tgl_resep, status) 
              VALUES (?, ?, ?, ?, GETDATE(), 'diproses'); SELECT SCOPE_IDENTITY() AS id;";
    
    $stmt_h = sqlsrv_query($conn, $sql_h, [$no_resep, $id_pel, $id_ptg, $dokter]);
    sqlsrv_next_result($stmt_h);
    $res_h = sqlsrv_fetch_array($stmt_h, SQLSRV_FETCH_ASSOC);
    $id_resep = $res_h['id'];

    if ($id_resep) {
        foreach ($_POST['items'] as $it) {
            sqlsrv_query($conn, "INSERT INTO resep_detail (id_resep, id_obat, qty, dosis, aturan_pakai) VALUES (?,?,?,?,?)", 
                        [$id_resep, $it['id_obat'], $it['qty'], $it['dosis'], $it['aturan']]);
        }
        echo "<script>alert('Resep Dokter Berhasil Disimpan!'); location.href='index.php';</script>";
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buat Resep - Apotek Sehat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include '../includes/sidebar.php'; ?>

<div id="content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Input Resep Dokter</h4>
            <span class="badge bg-white text-danger border px-3 py-2 shadow-sm rounded-pill">
                <i class="bi bi-shield-lock me-1"></i> Strict Stock Check
            </span>
        </div>

        <form method="POST" id="form-resep">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card-custom shadow-sm p-4 bg-white sticky-top" style="top:25px;">
                        <div class="mb-3">
                            <label class="text-label mb-2">NAMA DOKTER</label>
                            <input type="text" name="nama_dokter" class="form-control" placeholder="Contoh: dr. Ahmad" required>
                        </div>

                        <div class="mb-4">
                            <label class="text-label mb-2">PILIH PASIEN</label>
                            <select name="id_pelanggan" class="form-select" required>
                                <option value="">-- Cari Pasien --</option>
                                <?php foreach($pelanggan as $p): ?>
                                    <option value="<?= $p['id_pelanggan'] ?>"><?= $p['kode_pelanggan'] ?> - <?= $p['nama_pelanggan'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="alert alert-warning border-0 small rounded-4 shadow-sm mb-4">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            Sistem akan menolak resep jika obat yang dipilih memiliki stok 0 di gudang.
                        </div>

                        <button type="submit" id="btn-simpan" class="btn btn-primary btn-lg w-100 rounded-3 fw-bold py-3 shadow" disabled>
                            SIMPAN RESEP
                        </button>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card-custom p-0 shadow-sm overflow-hidden bg-white">
                        <div class="d-flex justify-content-between align-items-center p-4">
                            <h6 class="fw-bold mb-0">Daftar Obat (Cek Stok Master)</h6>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="addRow()">+ Tambah Baris</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light small text-muted">
                                    <tr>
                                        <th class="ps-4 py-3">OBAT</th>
                                        <th width="12%">QTY</th>
                                        <th width="20%">DOSIS</th>
                                        <th width="25%">ATURAN</th>
                                        <th width="5%"></th>
                                    </tr>
                                </thead>
                                <tbody id="area-item"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
const listObat = <?= json_encode($obat_list) ?>;
let rowIdx = 0;

function addRow() {
    rowIdx++;
    const row = `<tr class="tr-item" id="row-${rowIdx}">
        <td class="ps-4">
            <select name="items[${rowIdx}][id_obat]" class="form-select select-obat" onchange="validasi()" required>
                <option value="">-- Pilih --</option>
                ${listObat.map(o => `<option value="${o.id_obat}" data-stok="${o.stok}">${o.nama_obat} (Stok: ${o.stok})</option>`).join('')}
            </select>
        </td>
        <td><input type="number" name="items[${rowIdx}][qty]" class="form-control in-q" value="1" min="1" oninput="validasi()" required></td>
        <td><input type="text" name="items[${rowIdx}][dosis]" class="form-control" placeholder="3x1" required></td>
        <td><input type="text" name="items[${rowIdx}][aturan]" class="form-control" placeholder="Setelah Makan" required></td>
        <td><button type="button" class="btn btn-sm text-danger" onclick="this.closest('tr').remove();validasi();"><i class="bi bi-trash"></i></button></td>
    </tr>`;
    document.getElementById('area-item').insertAdjacentHTML('beforeend', row);
    validasi();
}

function validasi() {
    let aman = true;
    let adaItem = false;

    document.querySelectorAll('.tr-item').forEach(tr => {
        adaItem = true;
        const sel = tr.querySelector('.select-obat');
        const opt = sel.options[sel.selectedIndex];
        
        if (sel.value !== '') {
            const stok = parseInt(opt.dataset.stok) || 0;
            const qty  = parseInt(tr.querySelector('.in-q').value) || 0;

            // PROTEKSI KERAS: Jika stok 0 atau qty melebihi stok, baris merah
            if (stok <= 0 || qty > stok) {
                tr.style.backgroundColor = '#fff1f0';
                tr.querySelector('.in-q').classList.add('is-invalid');
                aman = false;
            } else {
                tr.style.backgroundColor = 'transparent';
                tr.querySelector('.in-q').classList.remove('is-invalid');
            }
        } else { aman = false; }
    });

    document.getElementById('btn-simpan').disabled = (!aman || !adaItem);
}

addRow();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>