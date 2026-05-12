<?php
require_once __DIR__ . '/../config/database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_obat'];
    $nama = $_POST['nama_obat'];
    $harga = $_POST['harga_jual'];
    $status = $_POST['is_active'];

    $sql = "UPDATE obat SET nama_obat = ?, harga_jual = ?, is_active = ? WHERE id_obat = ?";
    $stmt = sqlsrv_query($conn, $sql, array($nama, $harga, $status, $id));

    if ($stmt) {
        header("Location: index.php?status=updated");
    } else {
        die(print_r(sqlsrv_errors(), true));
    }
}