<?php
// ============================================================
// pelanggan/hapus.php — Proses Hapus (Soft Delete)
// ============================================================

require_once __DIR__ . '/../config/database.php';

$id = $_GET['id'] ?? null;

if ($id) {
    // Soft Delete: Hanya ubah status is_active jadi 0
    $sql = "UPDATE pelanggan SET is_active = 0 WHERE id_pelanggan = ?";
    $hapus = db_execute($conn, $sql, '', $id);

    if ($hapus) {
        header('Location: index.php?status=deleted');
    } else {
        echo "Gagal menghapus: " . print_r(sqlsrv_errors(), true);
    }
} else {
    header('Location: index.php');
}
exit;