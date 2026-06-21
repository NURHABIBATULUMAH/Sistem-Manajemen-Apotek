<?php
// ============================================================
// login.php — Semua petugas aktif bisa login (kasir & admin jadi satu)
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

        $row = db_fetch_one($conn,
            "SELECT * FROM petugas WHERE username = ? AND is_active = 1",
            '', $username
        );

        // PERBAIKAN: Mengubah password_verify() menjadi pengecekan MD5 sesuai data di database
        if ($row && md5($password) === $row['password_hash']) {
            $_SESSION['petugas'] = [
                'id_petugas'   => $row['id_petugas'],
                'name_petugas' => $row['nama_petugas'],
                'username'     => $row['username'],
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
  <title>Login — Apotek</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background: #f4f6f9;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .login-card {
      width: 100%;
      max-width: 400px;
      border-radius: 15px;
    }
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
    <div class="alert alert-danger py-2 small">
      <i class="bi bi-exclamation-circle me-1"></i><?= $error ?>
    </div>
  <?php endif; ?>

  <form method="POST">
    <div class="mb-3">
      <label class="form-label fw-semibold">Username</label>
      <input type="text" name="username" class="form-control"
             placeholder="Masukkan username" required autofocus
             value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
    </div>
    <div class="mb-4">
      <label class="form-label fw-semibold">Password</label>
      <input type="password" name="password" class="form-control"
             placeholder="••••••••" required>
    </div>
    <button type="submit" class="btn btn-primary w-100 fw-bold py-2">
      <i class="bi bi-box-arrow-in-right me-2"></i>Login
    </button>
  </form>
</div>

</body>
</html>