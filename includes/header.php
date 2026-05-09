<?php
// ============================================================
// includes/header.php
// ============================================================

// Mulai session jika belum
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect ke login jika belum login
if (!isset($_SESSION['petugas'])) {
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}

$petugas  = $_SESSION['petugas'];
$halaman  = $halaman  ?? 'Dashboard';
$base_url = BASE_URL   ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($halaman) ?> — Apotek</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Custom CSS -->
  <link href="<?= $base_url ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary px-3 sticky-top shadow-sm">
  <a class="navbar-brand fw-semibold" href="<?= $base_url ?>/index.php">
    <i class="bi bi-capsule me-2"></i>Apotek
  </a>
  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav ms-auto align-items-center gap-2">
      <li class="nav-item">
        <span class="text-white-50 small">
          <i class="bi bi-person-circle me-1"></i>
          <?= htmlspecialchars($petugas['nama_petugas']) ?>
          <span class="badge bg-light text-primary ms-1"><?= ucfirst($petugas['role']) ?></span>
        </span>
      </li>
      <li class="nav-item">
        <a class="btn btn-outline-light btn-sm" href="<?= $base_url ?>/logout.php">
          <i class="bi bi-box-arrow-right me-1"></i>Logout
        </a>
      </li>
    </ul>
  </div>
</nav>

<!-- WRAPPER -->
<div class="d-flex" id="wrapper">
