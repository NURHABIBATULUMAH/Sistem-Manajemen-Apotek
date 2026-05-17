<?php
// ============================================================
// includes/sidebar.php — Medical Elegant Theme
// ============================================================
$petugas  = $_SESSION['petugas'] ?? [];
$initials = 'AP';
if (!empty($petugas['nama_petugas'])) {
    $parts    = explode(' ', $petugas['nama_petugas']);
    $initials = strtoupper($parts[0][0]);
    if (isset($parts[1])) $initials .= strtoupper($parts[1][0]);
}
$current = basename($_SERVER['PHP_SELF']);
$dir     = basename(dirname($_SERVER['PHP_SELF']));
 
function isActive($pages, $dir, $current) {
    foreach ($pages as $p) {
        if (strpos($p, '/') !== false) {
            list($d, $f) = explode('/', $p, 2);
            if ($dir === $d && ($f === '*' || $current === $f)) return 'active';
        } else {
            if ($current === $p && $dir === '.') return 'active';
        }
    }
    return '';
}
$base_url = BASE_URL ?? '';
?>
 
<nav id="sidebar">
 
  <!-- Brand -->
  <div class="sidebar-brand">
    <div class="sidebar-brand-icon">
      <i class="bi bi-heart-pulse"></i>
    </div>
    <div>
      <span class="sidebar-brand-name">MediPharm</span>
      <span class="sidebar-brand-sub">Sistem Apotek</span>
    </div>
  </div>
 
  <!-- Navigation -->
  <div class="sidebar-nav">
 
    <span class="nav-section-label">Utama</span>
 
    <a href="<?= $base_url ?>/index.php"
       class="sidebar-link <?= isActive(['index.php'], $dir, $current) ?>">
      <i class="bi bi-grid-1x2"></i> Dashboard
    </a>
 
    <span class="nav-section-label">Transaksi</span>
 
    <a href="<?= $base_url ?>/penjualan/index.php"
       class="sidebar-link <?= isActive(['penjualan/*'], $dir, $current) ?>">
      <i class="bi bi-bag-check"></i> Penjualan
    </a>
 
    <a href="<?= $base_url ?>/resep/index.php"
       class="sidebar-link <?= isActive(['resep/*'], $dir, $current) ?>">
      <i class="bi bi-file-earmark-medical"></i> Resep
      <span class="badge-dot"></span>
    </a>
 
    <a href="<?= $base_url ?>/pembelian/index.php"
       class="sidebar-link <?= isActive(['pembelian/*'], $dir, $current) ?>">
      <i class="bi bi-cart3"></i> Pembelian
    </a>
 
    <span class="nav-section-label">Master Data</span>
 
    <a href="<?= $base_url ?>/obat/index.php"
       class="sidebar-link <?= isActive(['obat/*'], $dir, $current) ?>">
      <i class="bi bi-capsule"></i> Data Obat
    </a>
 
    <a href="<?= $base_url ?>/obat/master.php"
       class="sidebar-link <?= isActive(['obat/master.php'], $dir, $current) ?>">
      <i class="bi bi-tags"></i> Kategori Obat
    </a>
 
    <a href="<?= $base_url ?>/pelanggan/index.php"
       class="sidebar-link <?= isActive(['pelanggan/*'], $dir, $current) ?>">
      <i class="bi bi-people"></i> Pelanggan
    </a>
 
    <a href="<?= $base_url ?>/supplier/index.php"
       class="sidebar-link <?= isActive(['supplier/*'], $dir, $current) ?>">
      <i class="bi bi-truck"></i> Supplier
    </a>
 
    <span class="nav-section-label">Laporan</span>
 
    <a href="<?= $base_url ?>/laporan/index.php"
       class="sidebar-link <?= isActive(['laporan/*'], $dir, $current) ?>">
      <i class="bi bi-clipboard2-data"></i> Laporan
    </a>
 
  </div>
 
  <!-- User Footer -->
  <div class="sidebar-footer">
    <div class="sidebar-user">
      <div class="sidebar-avatar"><?= $initials ?></div>
      <div class="sidebar-user-info">
        <span class="sidebar-user-name"><?= htmlspecialchars($petugas['nama_petugas'] ?? 'Admin') ?></span>
        <span class="sidebar-user-role"><?= ucfirst($petugas['role'] ?? 'petugas') ?></span>
      </div>
      <a href="<?= $base_url ?>/logout.php" class="sidebar-logout" title="Logout">
        <i class="bi bi-box-arrow-right"></i>
      </a>
    </div>
  </div>
 
</nav>