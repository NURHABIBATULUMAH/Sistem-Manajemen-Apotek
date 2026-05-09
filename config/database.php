<?php
// ============================================================
// config/database.php
// Konfigurasi koneksi database MySQL
// ============================================================

define('DB_HOST',   'localhost');
define('DB_USER',   'root');        // Ganti sesuai user MySQL kamu
define('DB_PASS',   '');            // Ganti sesuai password MySQL kamu
define('DB_NAME',   'db_apotek');
define('DB_CHARSET','utf8mb4');

// Buat koneksi
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek error koneksi
if ($conn->connect_error) {
    die(json_encode([
        'status'  => 'error',
        'message' => 'Koneksi database gagal: ' . $conn->connect_error
    ]));
}

// Set charset
$conn->set_charset(DB_CHARSET);

// Set timezone MySQL sesuai WIB
$conn->query("SET time_zone = '+07:00'");

// ============================================================
// Helper Functions
// ============================================================

/**
 * Eksekusi query SELECT dan kembalikan semua baris
 */
function db_fetch_all(mysqli $conn, string $sql, string $types = '', ...$params): array {
    if ($types !== '' && count($params) > 0) {
        $stmt = $conn->prepare($sql);
        if (!$stmt) return [];
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
    } else {
        $result = $conn->query($sql);
    }
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

/**
 * Eksekusi query SELECT dan kembalikan satu baris
 */
function db_fetch_one(mysqli $conn, string $sql, string $types = '', ...$params): ?array {
    $rows = db_fetch_all($conn, $sql, $types, ...$params);
    return $rows[0] ?? null;
}

/**
 * Eksekusi INSERT/UPDATE/DELETE
 * Kembalikan jumlah baris yang terpengaruh
 */
function db_execute(mysqli $conn, string $sql, string $types = '', ...$params): int {
    if ($types && $params) {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->affected_rows;
    }
    $conn->query($sql);
    return $conn->affected_rows;
}

/**
 * Insert data dan kembalikan ID terakhir
 */
function db_insert(mysqli $conn, string $sql, string $types = '', ...$params): int {
    db_execute($conn, $sql, $types, ...$params);
    return (int)$conn->insert_id;
}

/**
 * Escape string untuk keamanan
 */
function db_escape(mysqli $conn, $value): string {
    return $conn->real_escape_string((string)$value);
}

/**
 * Format angka ke Rupiah
 */
function rupiah(float $angka): string {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

/**
 * Format tanggal ke format Indonesia
 */
function tgl_indo(string $tgl): string {
    if (!$tgl || $tgl === '0000-00-00') return '-';
    $bulan = ['','Januari','Februari','Maret','April','Mei','Juni',
              'Juli','Agustus','September','Oktober','November','Desember'];
    $t = explode('-', $tgl);
    return (int)$t[2] . ' ' . $bulan[(int)$t[1]] . ' ' . $t[0];
}

/**
 * Generate nomor unik
 * Contoh: SL20240515001
 */
function generate_no(string $prefix): string {
    return $prefix . date('Ymd') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
}
