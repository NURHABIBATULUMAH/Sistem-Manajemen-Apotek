<?php
require_once __DIR__ . '/../config/database.php';
session_start();

$id = $_GET['id'] ?? die("ID tidak ditemukan");

// 1. Ambil data detail untuk mengurangi kembali stok obat 
$sql_items = "SELECT id_obat, qty FROM pembelian_detail WHERE id_pembelian = ?";
$items = db_fetch_all($conn, $sql_items, '', $id);

foreach ($items as $item) {
    // Kurangi stok obat karena pembeliannya dibatalkan/dihapus
    sqlsrv_query($conn, "UPDATE obat SET stok = stok - ? WHERE id_obat = ?", [$item['qty'], $item['id_obat']]);
}

// 2. Hapus dari tabel Detail dulu
sqlsrv_query($conn, "DELETE FROM pembelian_detail WHERE id_pembelian = ?", [$id]);

// 3. Baru hapus dari tabel Header
sqlsrv_query($conn, "DELETE FROM pembelian_header WHERE id_pembelian = ?", [$id]);

header("Location: index.php?status=terhapus");
exit;