<?php
// ============================================================
// resep/proses.php — Ubah Status Resep
// ============================================================

require_once __DIR__ . '/../config/database.php';

$id = $_GET['id'] ?? null;

if ($id) {
    // Ubah status resep menjadi 'diproses'
    $sql = "UPDATE resep_header SET status = 'diproses' WHERE id_resep = ?";
    $update = db_execute($conn, $sql, '', $id);

    if ($update) {
        // Balik ke index dengan notifikasi
        header('Location: index.php?status=proses_berhasil');
    } else {
        echo "Gagal memproses resep: " . print_r(sqlsrv_errors(), true);
    }
} else {
    header('Location: index.php');
}
exit;