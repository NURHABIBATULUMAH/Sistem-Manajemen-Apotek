<?php
require_once __DIR__ . '/../config/database.php';
session_start();

$id = $_GET['id'] ?? die("ID Tidak Ada");

// 1. Hapus dulu log atau detail yang berhubungan (biar gak error FK)
sqlsrv_query($conn, "DELETE FROM pembelian_detail WHERE id_obat = ?", array($id));
sqlsrv_query($conn, "DELETE FROM log_stok WHERE id_obat = ?", array($id));

// 2. Baru hapus master obatnya
$sql = "DELETE FROM obat WHERE id_obat = ?";
$stmt = sqlsrv_query($conn, $sql, array($id));

if ($stmt) {
    header("Location: index.php?status=deleted");
} else {
    die(print_r(sqlsrv_errors(), true));
}