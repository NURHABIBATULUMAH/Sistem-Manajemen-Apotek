<?php
require_once __DIR__ . '/../config/database.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nr = $_POST['no_resep']; $ip = $_POST['id_pelanggan']; $dr = $_POST['nama_dokter']; $tg = $_POST['tgl_resep']; $pt = $_SESSION['petugas']['id_petugas'] ?? 1;
    $sql_h = "INSERT INTO resep_header (no_resep, id_pelanggan, id_petugas, nama_dokter, tgl_resep, status) VALUES (?,?,?,?,?,'menunggu'); SELECT SCOPE_IDENTITY() AS id;";
    $st_h = sqlsrv_query($conn, $sql_h, [$nr, $ip, $pt, $dr, $tg]);
    if ($st_h === false) { die(print_r(sqlsrv_errors(), true)); }
    sqlsrv_next_result($st_h); $r_h = sqlsrv_fetch_array($st_h, SQLSRV_FETCH_ASSOC); $idr = $r_h['id'];
    if ($idr && isset($_POST['items'])) {
        foreach ($_POST['items'] as $it) {
            sqlsrv_query($conn, "INSERT INTO resep_detail (id_resep, id_obat, qty, dosis, aturan_pakai) VALUES (?,?,?,?,?)", [$idr, $it['id_obat'], $it['qty'], $it['dosis'], $it['aturan_pakai']]);
        }
    }
    header("Location: index.php?status=sukses"); exit;
}