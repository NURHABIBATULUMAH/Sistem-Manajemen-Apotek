<?php
// ============================================================
// login.php (Versi SQL Server)
// ============================================================

session_start();
define('BASE_URL', '');
require_once __DIR__ . '/config/database.php';

if (isset($_SESSION['petugas'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username && $password) {
        // SQL Server Query menggunakan helper db_fetch_one
        $row = db_fetch_one($conn,
            "SELECT * FROM petugas WHERE username = ? AND is_active = 1",
            '', $username
        );

        // Verifikasi password dengan MD5 PHP
        if ($row && $row['password_hash'] === md5($password)) {
            $_SESSION['petugas'] = [
                'id_petugas'   => $row['id_petugas'],
                'nama_petugas' => $row['nama_petugas'],
                'username'     => $row['username'],
                'role'         => $row['role'],
            ];
            header('Location: index.php');
            exit;
        } else {
            $error = 'Username atau password salah.';
        }
    } else {
        $error = 'Username dan password wajib diisi.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login — Apotek Dimas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background: #f4f6f9; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
    .login-card { width: 100%; max-width: 400px; border-radius: 15px; }
  </style>
</head>
<body>
<div class="login-card card shadow p-4">
  <div class="text-center mb-4">
    <div class="text-primary display-4"><i class="bi bi-capsule-pill"></i></div>
    <h5 class="fw-bold mt-2">Sistem Apotek UTM</h5>
    <small class="text-muted">Login Petugas</small>
  </div>

  <?php if ($error): ?>
    <div class="alert alert-danger py-2 small"><?= $error ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="mb-3">
      <label class="form-label">Username</label>
      <input type="text" name="username" class="form-control" placeholder="admin" required autofocus>
    </div>
    <div class="mb-4">
      <label class="form-label">Password</label>
      <input type="password" name="password" class="form-control" placeholder="••••••••" required>
    </div>
    <button type="submit" class="btn btn-primary w-100">Login</button>
  </form>
</div>
</body>
</html>