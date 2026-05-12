<?php
// ============================================================
// config/database.php - VERSI SQL SERVER
// ============================================================

$serverName = "."; 
$connectionInfo = array(
    "Database" => "db_apotek", // Nama database baru yang kita buat tadi
    "UID" => "", 
    "PWD" => "",
    "CharacterSet" => "UTF-8"
);

$conn = sqlsrv_connect($serverName, $connectionInfo);

if (!$conn) {
    die(json_encode([
        'status'  => 'error',
        'message' => 'Koneksi SQL Server gagal: ' . print_r(sqlsrv_errors(), true)
    ]));
}

// --- HELPER FUNCTIONS (Nama fungsi tetap sama agar UI Habibah tidak error) ---

function db_fetch_all($conn, string $sql, $types = '', ...$params): array {
    $stmt = sqlsrv_query($conn, $sql, $params);
    if ($stmt === false) return [];
    $rows = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $rows[] = $row;
    }
    return $rows;
}

function db_fetch_one($conn, string $sql, $types = '', ...$params): ?array {
    $rows = db_fetch_all($conn, $sql, $types, ...$params);
    return $rows[0] ?? null;
}

function db_execute($conn, string $sql, $types = '', ...$params): int {
    $stmt = sqlsrv_query($conn, $sql, $params);
    if ($stmt === false) return 0;
    return sqlsrv_rows_affected($stmt);
}

function db_insert($conn, string $sql, $types = '', ...$params): int {
    sqlsrv_query($conn, $sql, $params);
    $res = sqlsrv_query($conn, "SELECT SCOPE_IDENTITY() AS last_id");
    $row = sqlsrv_fetch_array($res, SQLSRV_FETCH_ASSOC);
    return (int)$row['last_id'];
}

// Fungsi tambahan Habibah
function rupiah(float $angka): string { return 'Rp ' . number_format($angka, 0, ',', '.'); }

function tgl_indo($tgl) {
    if (!$tgl) return '-';
    if ($tgl instanceof DateTime) return $tgl->format('d F Y');
    return $tgl;
}
?>