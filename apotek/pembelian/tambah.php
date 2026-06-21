<?php
define('BASE_URL', '..'); 
require_once __DIR__ . '/../config/database.php';
session_start();

if (!isset($_SESSION['petugas'])) { header('Location: ../login.php'); exit; }

$obat_list = db_fetch_all($conn, "SELECT id_obat, nama_obat, harga_beli FROM obat WHERE is_active = 1");
$supplier  = db_fetch_all($conn, "SELECT id_supplier, nama_supplier FROM supplier");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['items'])) {
    $no_beli = "PO-" . date('YmdHis');
    $id_sup  = $_POST['id_supplier'];
    $total   = $_POST['total_harga'];
    $id_ptg  = $_SESSION['petugas']['id_petugas']; 

    // Header: Menggunakan tgl_pesan dan status 'diterima'
    $sql_h = "INSERT INTO pembelian_header (no_pembelian, id_supplier, id_petugas, tgl_pesan, total_harga, status) 
              VALUES (?, ?, ?, GETDATE(), ?, 'diterima'); SELECT SCOPE_IDENTITY() AS id;";
    
    $params_h = [$no_beli, $id_sup, $id_ptg, $total];
    $stmt_h = sqlsrv_query($conn, $sql_h, $params_h);

    if ($stmt_h === false) {
        die("<pre>Error Header: " . print_r(sqlsrv_errors(), true) . "</pre>");
    }

    sqlsrv_next_result($stmt_h);
    $res_h = sqlsrv_fetch_array($stmt_h, SQLSRV_FETCH_ASSOC);
    $id_pembelian = $res_h['id'];

    if ($id_pembelian) {
        foreach ($_POST['items'] as $it) {
            $ido = (int)$it['id_obat'];
            $qty = (int)$it['qty'];
            $hrg = (float)$it['harga'];
            $exp = $it['exp'];
            $sub = $qty * $hrg;

            // Detail: Sesuai kolom rill qty_pesan, qty_terima, harga_satuan, stok_sisa
            $sql_d = "INSERT INTO pembelian_detail (id_pembelian, id_obat, qty_pesan, qty_terima, harga_satuan, subtotal, tgl_kadaluarsa, stok_sisa) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            sqlsrv_query($conn, $sql_d, [$id_pembelian, $ido, $qty, $qty, $hrg, $sub, $exp, $qty]);
            
            // Update stok di tabel master
            sqlsrv_query($conn, "UPDATE obat SET stok = stok + ? WHERE id_obat = ?", [$qty, $ido]);
        }
        echo "<script>alert('Stok Berhasil Masuk!'); location.href='index.php';</script>";
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Input Pembelian - Apotek Sehat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/sidebar.php'; ?>
<div id="content">
    <div class="container-fluid">
        <h4 class="fw-bold mb-4">Input Pembelian Baru</h4>
        <form method="POST">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card-custom shadow-sm p-4 bg-white sticky-top" style="top:20px;">
                        <label class="text-label mb-2">SUPPLIER</label>
                        <select name="id_supplier" class="form-select mb-4" required>
                            <option value="">-- Pilih Supplier --</option>
                            <?php foreach($supplier as $s): ?>
                                <option value="<?= $s['id_supplier'] ?>"><?= $s['nama_supplier'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="bg-dark p-4 rounded-4 text-center mb-4">
                            <div class="text-white-50 small mb-1">TOTAL BAYAR</div>
                            <h2 class="text-white fw-bold mb-0" id="txt-total">Rp 0</h2>
                            <input type="hidden" name="total_harga" id="inp-total">
                        </div>
                        <button type="submit" id="btn-simpan" class="btn btn-primary btn-lg w-100 rounded-3 fw-bold py-3 shadow" disabled>SIMPAN STOK</button>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="card-custom p-0 shadow-sm overflow-hidden bg-white text-dark">
                        <div class="p-4 d-flex justify-content-between"><h6 class="fw-bold mb-0">Item Barang</h6><button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="addRow()">+ Tambah</button></div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light small text-muted"><tr><th class="ps-4">OBAT</th><th width="20%">HARGA</th><th width="15%">QTY</th><th width="25%">EXP</th><th width="5%"></th></tr></thead>
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
    const row = `<tr class="tr-item">
        <td class="ps-4"><select name="items[${rowIdx}][id_obat]" class="form-select" onchange="autoHarga(this, ${rowIdx})" required><option value="">-- Pilih --</option>${listObat.map(o => `<option value="${o.id_obat}" data-h="${o.harga_beli}">${o.nama_obat}</option>`).join('')}</select></td>
        <td><input type="number" name="items[${rowIdx}][harga]" id="h-${rowIdx}" class="form-control" oninput="hitung()" required></td>
        <td><input type="number" name="items[${rowIdx}][qty]" class="form-control" value="1" min="1" oninput="hitung()" required></td>
        <td><input type="date" name="items[${rowIdx}][exp]" class="form-control" required></td>
        <td><button type="button" class="btn btn-sm text-danger" onclick="this.closest('tr').remove();hitung();"><i class="bi bi-trash"></i></button></td>
    </tr>`;
    document.getElementById('area-item').insertAdjacentHTML('beforeend', row);
    hitung();
}
function autoHarga(el, id) { document.getElementById(`h-${id}`).value = el.options[el.selectedIndex].dataset.h || 0; hitung(); }
function hitung() {
    let grand = 0; let adaItem = false;
    document.querySelectorAll('.tr-item').forEach(tr => {
        adaItem = true;
        const h = parseFloat(tr.querySelector('input[name*="[harga]"]').value) || 0;
        const q = parseFloat(tr.querySelector('input[name*="[qty]"]').value) || 0;
        grand += (h * q);
    });
    document.getElementById('txt-total').innerText = 'Rp ' + grand.toLocaleString('id-ID');
    document.getElementById('inp-total').value = grand;
    document.getElementById('btn-simpan').disabled = !adaItem;
}
addRow();
</script>
</body>
</html>