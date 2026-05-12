<?php
require_once __DIR__ . '/../config/database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $no_pembelian = $_POST['no_pembelian']; 
    $id_sup       = $_POST['id_supplier'];
    $tgl_pesan    = $_POST['tgl_pesan']; 
    $id_ptg       = $_SESSION['petugas']['id_petugas'] ?? 1;

    // --- LOGIKA BARU: HITUNG TOTAL SEMUA OBAT ---
    $grand_total = 0;
    foreach ($_POST['items'] as $it) {
        $grand_total += (int)$it['qty'] * (float)$it['harga_beli'];
    }

    // Masukkan ke Header (Sekarang total_harga ikut disimpan)
    $sql_h = "INSERT INTO pembelian_header (no_pembelian, id_supplier, id_petugas, tgl_pesan, total_harga) 
              VALUES (?, ?, ?, ?, ?); SELECT SCOPE_IDENTITY() AS id;";
    
    $stmt_h = sqlsrv_query($conn, $sql_h, array($no_pembelian, $id_sup, $id_ptg, $tgl_pesan, $grand_total));
    
    if ($stmt_h === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    sqlsrv_next_result($stmt_h);
    $row_h = sqlsrv_fetch_array($stmt_h, SQLSRV_FETCH_ASSOC);
    $id_pembelian = $row_h['id'];

    if ($id_pembelian) {
        foreach ($_POST['items'] as $it) {
            $ido = $it['id_obat'];
            $qty = (int)$it['qty'];
            $hrg = (float)$it['harga_beli'];
            $exp = $it['tgl_kadaluarsa'];
            $sub = $qty * $hrg; // Ini kalkulasi Rp 150.000 yang kamu maksud

            // Simpan detail
            $sql_d = "INSERT INTO pembelian_detail (id_pembelian, id_obat, qty, harga_beli, subtotal, tgl_kadaluarsa, stok_sisa) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
            sqlsrv_query($conn, $sql_d, array($id_pembelian, $ido, $qty, $hrg, $sub, $exp, $qty));

            // Update Stok Utama
            sqlsrv_query($conn, "UPDATE obat SET stok = stok + ? WHERE id_obat = ?", array($qty, $ido));
        }

        header("Location: index.php?status=sukses");
        exit;
    }
}