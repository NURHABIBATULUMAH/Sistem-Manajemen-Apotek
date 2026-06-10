<?php
// ============================================================
// penjualan/tambah.php - Kasir Minimalis (FEFO & Validasi Stok)
// ============================================================

define('BASE_URL', '..'); 
require_once __DIR__ . '/../config/database.php';
session_start();

// Cek Login
if (!isset($_SESSION['petugas'])) { header('Location: ../login.php'); exit; }

// --- 1. AMBIL DATA MASTER OBAT (Mencari EXP Terdekat & Stok Real yang AMAN) ---
$sql_obat = "SELECT o.id_obat, o.nama_obat, o.harga_jual, 
             -- Hitung HANYA stok yang belum kadaluarsa
             (SELECT ISNULL(SUM(stok_sisa), 0) 
              FROM pembelian_detail 
              WHERE id_obat = o.id_obat 
                AND stok_sisa > 0 
                AND tgl_kadaluarsa >= CAST(GETDATE() AS DATE)) as stok,
             -- Cari tanggal expired terdekat yang aman
             (SELECT MIN(tgl_kadaluarsa)
              FROM pembelian_detail
              WHERE id_obat = o.id_obat 
                AND stok_sisa > 0 
                AND tgl_kadaluarsa >= CAST(GETDATE() AS DATE)) as exp_terdekat
             FROM obat o 
             WHERE o.is_active = 1";
$obat_list = db_fetch_all($conn, $sql_obat);

// --- 2. AMBIL DAFTAR RESEP YANG BERSTATUS 'DIPROSES' ---
$resep_siap = db_fetch_all($conn, "SELECT rh.id_resep, rh.no_resep, p.nama_pelanggan, rh.id_pelanggan 
                                   FROM resep_header rh JOIN pelanggan p ON rh.id_pelanggan = p.id_pelanggan 
                                   WHERE rh.status = 'diproses'");

// --- 3. LOGIKA SIMPAN TRANSAKSI (FEFO SYSTEM) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['items'])) {
    $total = (float)$_POST['total_harga'];
    $bayar = (float)$_POST['uang_bayar'];

    if ($bayar < $total) {
        echo "<script>alert('Maaf, uang bayarnya kurang!'); window.history.back();</script>";
        exit;
    }

    $no_jual = "SL-" . date('YmdHis');
    $id_pel  = !empty($_POST['id_pelanggan']) ? $_POST['id_pelanggan'] : null;
    $id_resep = !empty($_POST['id_resep']) ? $_POST['id_resep'] : null;
    $id_ptg  = $_SESSION['petugas']['id_petugas'] ?? 1;

    // A. Simpan ke Header Penjualan
    $sql_h = "INSERT INTO penjualan_header (no_penjualan, id_pelanggan, id_petugas, id_resep, total_harga, uang_bayar, uang_kembali, tgl_transaksi, status, metode_bayar) 
              VALUES (?, ?, ?, ?, ?, ?, ?, GETDATE(), 'selesai', 'tunai'); SELECT SCOPE_IDENTITY() AS id;";
    
    $stmt_h = sqlsrv_query($conn, $sql_h, [$no_jual, $id_pel, $id_ptg, $id_resep, $total, $bayar, ($bayar-$total)]);
    sqlsrv_next_result($stmt_h);
    $res_h = sqlsrv_fetch_array($stmt_h, SQLSRV_FETCH_ASSOC);
    $id_penjualan = $res_h['id'];

    if ($id_penjualan) {
        foreach ($_POST['items'] as $it) {
            $ido = $it['id_obat'];
            $qty = (int)$it['qty'];
            $hrg = (float)$it['harga'];

            // B. LOGIKA FEFO KETAT: Hanya kurangi dari batch yang belum expired
            $sql_fefo = "SELECT id_detail, stok_sisa FROM pembelian_detail 
                         WHERE id_obat = ? 
                           AND stok_sisa > 0 
                           AND tgl_kadaluarsa >= CAST(GETDATE() AS DATE) 
                         ORDER BY tgl_kadaluarsa ASC";
            $stmt_fefo = sqlsrv_query($conn, $sql_fefo, [$ido]);
            $sisa_potong = $qty;

            while ($row_b = sqlsrv_fetch_array($stmt_fefo, SQLSRV_FETCH_ASSOC)) {
                if ($sisa_potong <= 0) break;
                if ($row_b['stok_sisa'] >= $sisa_potong) {
                    sqlsrv_query($conn, "UPDATE pembelian_detail SET stok_sisa = stok_sisa - ? WHERE id_detail = ?", [$sisa_potong, $row_b['id_detail']]);
                    $sisa_potong = 0;
                } else {
                    sqlsrv_query($conn, "UPDATE pembelian_detail SET stok_sisa = 0 WHERE id_detail = ?", [$row_b['id_detail']]);
                    $sisa_potong -= $row_b['stok_sisa'];
                }
            }

            // C. Simpan Detail & Update Stok Master
            sqlsrv_query($conn, "INSERT INTO penjualan_detail (id_penjualan, id_obat, qty, harga_satuan, subtotal) VALUES (?,?,?,?,?)", [$id_penjualan, $ido, $qty, $hrg, ($qty*$hrg)]);
            sqlsrv_query($conn, "UPDATE obat SET stok = stok - ? WHERE id_obat = ?", [$qty, $ido]);
        }
        
        if ($id_resep) sqlsrv_query($conn, "UPDATE resep_header SET status = 'selesai' WHERE id_resep = ?", [$id_resep]);
        echo "<script>alert('Transaksi Berhasil!'); location.href='nota.php?id=$id_penjualan';</script>";
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kasir Penjualan - Apotek Sehat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include '../includes/sidebar.php'; ?>

<div id="content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">Kasir Penjualan</h4>
                <p class="text-muted small mb-0">Sistem FEFO & Validasi Stok Real-time aktif.</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-white text-dark border px-3 py-2 rounded-pill shadow-sm">
                    <i class="bi bi-person-circle me-1 text-primary"></i> <?= $_SESSION['petugas']['nama_petugas'] ?>
                </span>
            </div>
        </div>

        <form method="POST" id="form-kasir">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card-custom p-4 shadow-sm bg-white sticky-top" style="top: 25px; z-index: 10;">
                        <label class="text-label mb-2">TARIK DATA RESEP</label>
                        <select name="id_resep" class="form-select mb-4 py-2" onchange="tarikResep(this)">
                            <option value="">-- Transaksi Umum (Tanpa Resep) --</option>
                            <?php foreach($resep_siap as $rs): ?>
                                <option value="<?= $rs['id_resep'] ?>" data-pel="<?= $rs['id_pelanggan'] ?>"><?= $rs['no_resep'] ?> - <?= $rs['nama_pelanggan'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="id_pelanggan" id="id_pelanggan">
                        
                        <div class="bg-dark p-4 text-center rounded-4 mb-4 shadow">
                            <div class="text-white-50 small mb-1">TOTAL TAGIHAN</div>
                            <h2 class="text-white fw-bold mb-0" id="txt-total">Rp 0</h2>
                            <input type="hidden" name="total_harga" id="inp-total">
                        </div>
                        
                        <div class="mb-3">
                            <label class="text-label mb-2">UANG TUNAI (RP)</label>
                            <input type="number" name="uang_bayar" id="inp-bayar" class="form-control form-control-lg fw-bold text-center border-2 border-primary" oninput="hitung()" required>
                        </div>
                        
                        <div class="alert alert-secondary py-3 text-center fw-bold rounded-3 mb-4" id="txt-kembali" style="font-size: 1.1rem;">Kembali: Rp 0</div>
                        
                        <button type="submit" id="btn-simpan" class="btn btn-primary btn-lg w-100 rounded-3 shadow fw-bold py-3" disabled>
                            <i class="bi bi-printer-fill me-2"></i> SELESAI & CETAK
                        </button>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card-custom p-0 overflow-hidden shadow-sm border-0 bg-white">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center py-4 px-4 border-0">
                            <h6 class="fw-bold mb-0">Rincian Obat</h6>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="addRow()">
                                <i class="bi bi-plus-circle me-1"></i> Tambah Obat
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-muted small">
                                    <tr>
                                        <th class="ps-4 py-3">NAMA OBAT (EXP TERDEKAT)</th>
                                        <th width="20%">HARGA</th>
                                        <th width="15%">QTY</th>
                                        <th width="20%" class="text-end pe-4">SUBTOTAL</th>
                                        <th width="5%"></th>
                                    </tr>
                                </thead>
                                <tbody id="area-item">
                                    </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Data master obat dari PHP
const listObat = <?= json_encode($obat_list) ?>;
let rowIdx = 0;

// Fungsi Tambah Baris
function addRow(id='', q=1, h=0) {
    rowIdx++;
    const row = `<tr class="tr-item" id="row-${rowIdx}">
        <td class="ps-4">
            <select name="items[${rowIdx}][id_obat]" class="form-select select-obat" onchange="setHarga(this, ${rowIdx})" required>
                <option value="">-- Pilih Obat --</option>
                ${listObat.map(o => `
                    <option value="${o.id_obat}" 
                            data-h="${o.harga_jual}" 
                            data-stok="${o.stok}" 
                            ${o.id_obat==id?'selected':''}>
                        ${o.nama_obat} (${o.exp_terdekat ? o.exp_terdekat.date.substring(0,10) : 'No Exp'}) - Sisa Aman: ${o.stok}
                    </option>`).join('')}
            </select>
        </td>
        <td><input type="number" name="items[${rowIdx}][harga]" id="h-${rowIdx}" class="form-control bg-light in-h" value="${h}" readonly></td>
        <td><input type="number" name="items[${rowIdx}][qty]" class="form-control in-q" value="${q}" oninput="hitung()" min="1" required></td>
        <td class="text-end fw-bold pe-4 text-primary sub-txt">0</td>
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

// Logika Hitung & Validasi Stok
function hitung() {
    let grand = 0;
    let stokAman = true;

    document.querySelectorAll('.tr-item').forEach(tr => {
        const sel = tr.querySelector('.select-obat');
        const opt = sel.options[sel.selectedIndex];
        
        // Pengecekan Stok Real-time
        if (sel.value !== '') {
            const stokTersedia = parseInt(opt.dataset.stok) || 0;
            const qtyBeli = parseInt(tr.querySelector('.in-q').value) || 0;
            
            if (qtyBeli > stokTersedia || stokTersedia <= 0) {
                tr.style.backgroundColor = '#fff5f5'; // Merah muda jika stok kurang
                tr.querySelector('.in-q').classList.add('is-invalid');
                stokAman = false;
            } else {
                tr.style.backgroundColor = 'transparent';
                tr.querySelector('.in-q').classList.remove('is-invalid');
            }
        }

        const h = parseFloat(tr.querySelector('.in-h').value) || 0;
        const q = parseFloat(tr.querySelector('.in-q').value) || 0;
        const sub = h * q;
        tr.querySelector('.sub-txt').innerText = sub.toLocaleString('id-ID');
        grand += sub;
    });

    document.getElementById('txt-total').innerText = 'Rp ' + grand.toLocaleString('id-ID');
    document.getElementById('inp-total').value = grand;
    
    const bayar = parseFloat(document.getElementById('inp-bayar').value) || 0;
    const kembali = bayar - grand;
    document.getElementById('txt-kembali').innerText = 'Kembali: Rp ' + (kembali < 0 ? 0 : kembali).toLocaleString('id-ID');
    
    // Kunci tombol jika total 0, uang kurang, atau stok bermasalah
    const btn = document.getElementById('btn-simpan');
    btn.disabled = (grand === 0 || bayar < grand || !stokAman);
}

// Tarik Resep dengan Validasi Stok Awal
async function tarikResep(el) {
    const idResep = el.value;
    const areaItem = document.getElementById('area-item');
    if (!idResep) { areaItem.innerHTML = ''; addRow(); return; }
    
    document.getElementById('id_pelanggan').value = el.options[el.selectedIndex].dataset.pel;

    try {
        const res = await fetch(`get_resep_detail.php?id=${idResep}`);
        const items = await res.json();
        areaItem.innerHTML = '';

        if (items.length > 0) {
            items.forEach(i => {
                // Beri peringatan jika stok di resep melebihi stok gudang
                if (parseInt(i.stok) < parseInt(i.qty)) {
                    alert(`PERINGATAN: Stok ${i.nama_obat} tidak cukup! Tersedia: ${i.stok}, Dibutuhkan: ${i.qty}`);
                }
                addRow(i.id_obat, i.qty, i.harga_jual);
            });
        } else {
            addRow();
        }
    } catch (e) { alert('Gagal mengambil data resep.'); }
}

// Jalankan baris pertama saat load
addRow();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>