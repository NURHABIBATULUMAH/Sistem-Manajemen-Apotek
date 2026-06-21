<?php
require_once __DIR__ . '/config/database.php';

// Kita atur password barunya jadi 'admin123'
$password_baru = 'admin123';

// Proses 'blender' password menjadi hash asli bawaan PHP
$hash_asli = password_hash($password_baru, PASSWORD_DEFAULT);

// Update paksa ke database untuk user admin
$sql = "UPDATE petugas SET password_hash = ? WHERE username = 'admin'";
$stmt = sqlsrv_query($conn, $sql, array($hash_asli));

if ($stmt) {
    echo "<h1 style='color:green;'>RESET BERHASIL!</h1>";
    echo "<h3>Silakan login menggunakan:</h3>";
    echo "<ul>";
    echo "<li>Username : <b>admin</b></li>";
    echo "<li>Password : <b>admin123</b></li>";
    echo "</ul>";
    echo "<a href='login.php'>Klik di sini untuk kembali ke halaman Login</a>";
} else {
    echo "<h1>Waduh, Gagal:</h1>";
    die(print_r(sqlsrv_errors(), true));
}
?>