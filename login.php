<?php
// ============================================================
// login.php
// ============================================================

session_start(); // HARUS paling pertama sebelum output apapun

define('BASE_URL', '');
require_once __DIR__ . '/config/database.php';

// Jika sudah login, langsung ke dashboard
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
            's', $username
        );

        if ($row && $row['password_hash'] === MD5($password)) {
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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login — Apotek</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background: #f4f6f9; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
    .login-card { width: 100%; max-width: 400px; }
    .login-logo { font-size: 2.5rem; color: var(--bs-primary); }
  </style>
</head>
<body>
<div class="login-card card shadow-sm p-4">
  <div class="text-center mb-4">
    <div class="login-logo"><i class="bi bi-capsule-pill"></i></div>
    <h5 class="fw-semibold mt-2 mb-0">Sistem Apotek</h5>
    <small class="text-muted">Masuk dengan akun petugas</small>
  </div>

  <?php if ($error): ?>
    <div class="alert alert-danger py-2 small"><i class="bi bi-exclamation-circle me-1"></i><?= $error ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="mb-3">
      <label class="form-label">Username</label>
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-person"></i></span>
        <input type="text" name="username" class="form-control" placeholder="Masukkan username"
               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required autofocus>
      </div>
    </div>
    <div class="mb-4">
      <label class="form-label">Password</label>
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-lock"></i></span>
        <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
      </div>
    </div>
    <button type="submit" class="btn btn-primary w-100">
      <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
    </button>
  </form>

  <div class="text-muted text-center mt-3" style="font-size:11px">
    Default: admin / admin123 &nbsp;|&nbsp; apoteker / apotek123
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
