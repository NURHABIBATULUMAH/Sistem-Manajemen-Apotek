<?php
// ============================================================
// includes/header.php — Medical Elegant Theme
// ============================================================
 
if (session_status() === PHP_SESSION_NONE) session_start();
 
if (!isset($_SESSION['petugas'])) {
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}
 
$petugas  = $_SESSION['petugas'];
$halaman  = $halaman  ?? 'Dashboard';
$base_url = BASE_URL   ?? '';
$initials = strtoupper(substr($petugas['nama_petugas'], 0, 1));
if (strpos($petugas['nama_petugas'], ' ') !== false) {
    $parts = explode(' ', $petugas['nama_petugas']);
    $initials = strtoupper($parts[0][0] . $parts[1][0]);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($halaman) ?> — MediPharm</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
  <link href="<?= $base_url ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
 
<div id="wrapper">
 
<?php include BASE_URL . '/includes/sidebar.php'; ?>
 
<div id="content">
 
<!-- TOPBAR -->
<div id="topbar">
  <div class="topbar-left">
    <div class="topbar-page-title"><?= htmlspecialchars($halaman) ?></div>
    <div class="topbar-breadcrumb">
      <i class="bi bi-house-door me-1"></i>MedPharm
      <span class="mx-1" style="color:#ccc">/</span>
      <?= htmlspecialchars($halaman) ?>
    </div>
  </div>
  <div class="topbar-right">
    <div class="topbar-search">
      <i class="bi bi-search"></i>
      <span>Cari data...</span>
    </div>
    <a href="#" class="topbar-icon-btn" title="Notifikasi">
      <i class="bi bi-bell"></i>
      <span class="notif-badge"></span>
    </a>
    <a href="<?= $base_url ?>/logout.php" class="topbar-icon-btn" title="Logout">
      <i class="bi bi-box-arrow-right"></i>
    </a>
  </div>
</div>
<!-- /TOPBAR -->
 


