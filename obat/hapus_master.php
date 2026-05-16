<?php
require_once __DIR__ . '/../config/database.php';
session_start();

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Set is_active = 0 (Soft Delete) agar data transaksi lama tidak rusak
    $sql = "UPDATE obat SET is_active = 0 WHERE id_obat = ?";
    $params = array($id);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt) {
        echo "<script>alert('Obat berhasil dihapus!'); window.location.href='master.php';</script>";
    } else {
        echo "<script>alert('Gagal hapus obat!'); window.history.back();</script>";
    }
}
?>