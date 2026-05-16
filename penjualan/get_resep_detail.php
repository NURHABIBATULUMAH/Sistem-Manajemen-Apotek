<?php
require_once __DIR__ . '/../config/database.php';

$id_resep = $_GET['id'] ?? '';

if ($id_resep) {
    // Tambahkan o.stok dalam query agar kita tahu sisa obat di gudang
    $sql = "SELECT rd.id_obat, rd.qty, o.harga_jual, o.stok, o.nama_obat 
            FROM resep_detail rd
            JOIN obat o ON rd.id_obat = o.id_obat
            WHERE rd.id_resep = ?";
    
    $params = array($id_resep);
    $stmt = sqlsrv_query($conn, $sql, $params);
    
    $data = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $data[] = $row;
    }
    
    header('Content-Type: application/json');
    echo json_encode($data);
}