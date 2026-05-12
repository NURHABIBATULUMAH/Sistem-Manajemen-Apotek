<?php
define('BASE_URL', '..'); 
require_once __DIR__ . '/../config/database.php';
session_start();

$halaman = 'Kasir Penjualan';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

// 1. DATA MASTER
$sql_obat = "SELECT o.id_obat, o.nama_obat, o.harga_jual, o.stok,
             (SELECT MIN(tgl_kadaluarsa) FROM pembelian_detail WHERE id_obat = o.id_obat AND stok_sisa > 0) as exp_terdekat
             FROM obat o WHERE o.is_active = 1";
$obat_list = db_fetch_all($conn, $sql_obat);

$resep_siap = db_fetch_all($conn, "SELECT rh.id_resep, rh.no_resep, p.nama_pelanggan, rh.id_pelanggan 
                                   FROM resep_header rh JOIN pelanggan p ON rh.id_pelanggan = p.id_pelanggan 
                                   WHERE rh.status = 'diproses'");

// 2. PROSES SIMPAN TRANSAKSI
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['items'])) {
    $total = (float)$_POST['total_harga'];
    $bayar = (float)$_POST['uang_bayar'];

    if ($bayar < $total) {
        echo "<script>alert('Uang bayar kurang!'); window.history.back();</script>";
        exit;
    }

    $no_jual = "SL-" . date('YmdHis');
    $id_pel  = $_POST['id_pelanggan'] ?: null;
    $id_resep = $_POST['id_resep'] ?: null;
    $id_ptg  = $_SESSION['petugas']['id_petugas'] ?? 1;

    // A. Simpan ke Header
    $sql_h = "INSERT INTO penjualan_header (no_penjualan, id_pelanggan, id_petugas, id_resep, total_harga, uang_bayar, uang_kembali, tgl_transaksi, status) 
              VALUES (?, ?, ?, ?, ?, ?, ?, GETDATE(), 'selesai'); SELECT SCOPE_IDENTITY() AS id;";
    
    $stmt_h = sqlsrv_query($conn, $sql_h, [$no_jual, $id_pel, $id_ptg, $id_resep, $total, $bayar, ($bayar-$total)]);
    
    if ($stmt_h === false) { die("Gagal Simpan Header: " . print_r(sqlsrv_errors(), true)); }

    sqlsrv_next_result($stmt_h);
    $res_h = sqlsrv_fetch_array($stmt_h, SQLSRV_FETCH_ASSOC);
    $id_penjualan = $res_h['id'];

    if ($id_penjualan) {
        foreach ($_POST['items'] as $it) {
            $ido = $it['id_obat'];
            $qty = (int)$it['qty'];
            $hrg = (float)$it['harga'];

            // B. LOGIKA FEFO: Kurangi stok per Batch Expired
            $sql_fefo = "SELECT id_detail, stok_sisa FROM pembelian_detail 
                         WHERE id_obat = ? AND stok_sisa > 0 
                         ORDER BY tgl_kadaluarsa ASC";
            $stmt_fefo = sqlsrv_query($conn, $sql_fefo, [$ido]);

            if ($stmt_fefo === false) { die("Gagal Query FEFO: " . print_r(sqlsrv_errors(), true)); }

            $sisa_potong = $qty;
            while ($row_b = sqlsrv_fetch_array($stmt_fefo, SQLSRV_FETCH_ASSOC)) {
                if ($sisa_potong <= 0) break;
                
                $id_det_batch = $row_b['id_detail'];
                $stok_batch   = $row_b['stok_sisa'];

                if ($stok_batch >= $sisa_potong) {
                    // Potong stok di batch ini
                    sqlsrv_query($conn, "UPDATE pembelian_detail SET stok_sisa = stok_sisa - ? WHERE id_detail = ?", [$sisa_potong, $id_det_batch]);
                    $sisa_potong = 0;
                } else {
                    // Habiskan stok di batch ini, lanjut cari batch berikutnya
                    sqlsrv_query($conn, "UPDATE pembelian_detail SET stok_sisa = 0 WHERE id_detail = ?", [$id_det_batch]);
                    $sisa_potong -= $stok_batch;
                }
            }

            // C. Simpan Detail & Update Stok Master
            sqlsrv_query($conn, "INSERT INTO penjualan_detail (id_penjualan, id_obat, qty, harga_satuan, subtotal) VALUES (?,?,?,?,?)", [$id_penjualan, $ido, $qty, $hrg, ($qty*$hrg)]);
            sqlsrv_query($conn, "UPDATE obat SET stok = stok - ? WHERE id_obat = ?", [$qty, $ido]);
        }
        
        if ($id_resep) sqlsrv_query($conn, "UPDATE resep_header SET status = 'selesai' WHERE id_resep = ?", [$id_resep]);
        echo "<script>alert('Transaksi Berhasil!'); location.href='index.php';</script>";
        exit;
    }
}
?>

<div class="container-fluid">
    <div class="page-header mt-4">
        <h4><i class="bi bi-cash-stack me-2 text-primary"></i>Kasir Penjualan (FEFO System)</h4>
    </div>
    
    <form method="POST">
        <div class="row mt-3">
            <div class="col-md-4">
                <div class="card shadow-sm border-primary">
                    <div class="card-body">
                        <label class="small fw-bold">Pilih Resep (Opsional)</label>
                        <select name="id_resep" class="form-select mb-3" onchange="tarikResep(this)">
                            <option value="">-- Umum --</option>
                            <?php foreach($resep_siap as $rs): ?>
                                <option value="<?= $rs['id_resep'] ?>" data-pel="<?= $rs['id_pelanggan'] ?>"><?= $rs['no_resep'] ?> - <?= $rs['nama_pelanggan'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="id_pelanggan" id="id_pelanggan">
                        
                        <div class="bg-dark p-3 text-center rounded mb-3">
                            <h6 class="text-white-50">TOTAL BAYAR</h6>
                            <h2 class="text-white mb-0" id="txt-total">Rp 0</h2>
                            <input type="hidden" name="total_harga" id="inp-total">
                        </div>
                        
                        <div class="mb-3">
                            <label class="small fw-bold">Uang Bayar (Rp)</label>
                            <input type="number" name="uang_bayar" id="inp-bayar" class="form-control form-control-lg fw-bold" oninput="hitung()" required>
                        </div>
                        <div class="alert alert-secondary py-2 text-center fw-bold" id="txt-kembali">Kembali: Rp 0</div>
                        
                        <button type="submit" id="btn-simpan" class="btn btn-primary btn-lg w-100 shadow" disabled>SIMPAN & CETAK</button>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Item Obat</span>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addRow()">+ Tambah Obat</button>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Obat (Exp Terdekat)</th>
                                    <th width="20%">Harga</th>
                                    <th width="15%">Qty</th>
                                    <th width="20%" class="text-end pe-3">Subtotal</th>
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

<script>
const listObat = <?= json_encode($obat_list) ?>;
let rowIdx = 0;

function addRow(id='', q=1, h=0) {
    rowIdx++;
    const row = `<tr class="tr-item" id="row-${rowIdx}">
        <td>
            <select name="items[${rowIdx}][id_obat]" class="form-select form-select-sm select-obat" onchange="setHarga(this, ${rowIdx})" required>
                <option value="">-- Pilih Obat --</option>
                ${listObat.map(o => `<option value="${o.id_obat}" data-h="${o.harga_jual}" ${o.id_obat==id?'selected':''}>${o.nama_obat} (${o.exp_terdekat || 'No Exp'})</option>`).join('')}
            </select>
        </td>
        <td><input type="number" name="items[${rowIdx}][harga]" id="h-${rowIdx}" class="form-control form-control-sm in-h" value="${h}" oninput="hitung()" readonly></td>
        <td><input type="number" name="items[${rowIdx}][qty]" class="form-control form-control-sm in-q" value="${q}" oninput="hitung()" min="1" required></td>
        <td class="text-end fw-bold pe-3 sub-txt">0</td>
        <td><button type="button" class="btn btn-sm text-danger" onclick="this.closest('tr').remove();hitung();"><i class="bi bi-trash"></i></button></td>
    </tr>`;
    document.getElementById('area-item').insertAdjacentHTML('beforeend', row);
    hitung();
}

function setHarga(el, id) {
    const opt = el.options[el.selectedIndex];
    document.getElementById(`h-${id}`).value = opt.dataset.h || 0;
    hitung();
}

function hitung() {
    let grand = 0;
    document.querySelectorAll('.tr-item').forEach(tr => {
        const h = parseFloat(tr.querySelector('.in-h').value) || 0;
        const q = parseFloat(tr.querySelector('.in-q').value) || 0;
        const sub = h * q;
        tr.querySelector('.sub-txt').innerText = sub.toLocaleString('id-ID');
        grand += sub;
    });
    document.getElementById('txt-total').innerText = 'Rp ' + grand.toLocaleString('id-ID');
    document.getElementById('inp-total').value = grand;
    
    const bayar = parseFloat(document.getElementById('inp-bayar').value) || 0;
    document.getElementById('txt-kembali').innerText = 'Kembali: Rp ' + (bayar - grand).toLocaleString('id-ID');
    
    const btn = document.getElementById('btn-simpan');
    btn.disabled = (grand === 0 || bayar < grand);
}
// --- FUNGSI TARIK RESEP OTOMATIS ---
async function tarikResep(el) {
    const idResep = el.value;
    const areaItem = document.getElementById('area-item');
    const inpPelanggan = document.getElementById('id_pelanggan');

    if (!idResep) {
        // Jika pilih "-- Umum --", kosongkan tabel dan kasih 1 baris kosong
        areaItem.innerHTML = '';
        inpPelanggan.value = '';
        addRow();
        return;
    }

    // Set ID Pelanggan otomatis dari data-pel di select option
    const idPel = el.options[el.selectedIndex].dataset.pel;
    inpPelanggan.value = idPel;

    try {
        // Ambil data dari file PHP yang kita buat tadi
        const response = await fetch(`get_resep_detail.php?id=${idResep}`);
        const items = await response.json();

        // Bersihkan tabel kasir dulu
        areaItem.innerHTML = '';

        if (items.length > 0) {
            items.forEach(item => {
                // Masukkan tiap obat dari resep ke tabel kasir secara otomatis
                // addRow(id_obat, qty, harga)
                addRow(item.id_obat, item.qty, item.harga_jual);
            });
        } else {
            alert('Resep ini tidak memiliki rincian obat.');
            addRow();
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Gagal mengambil data resep.');
    }
}

// Inisialisasi baris pertama
addRow();
</script>