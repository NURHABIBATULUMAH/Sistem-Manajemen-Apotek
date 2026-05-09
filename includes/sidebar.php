<?php
// ============================================================
// includes/sidebar.php
// ============================================================

$current = basename($_SERVER['PHP_SELF']);
$dir     = basename(dirname($_SERVER['PHP_SELF']));

function nav_item(string $href, string $icon, string $label, string $current, string $dir = ''): string {
    $base    = basename($href);
    $folder  = basename(dirname($href));
    $active  = ($current === $base || $dir === $folder) ? 'active' : '';
    return "
    <li class='nav-item'>
      <a class='nav-link {$active}' href='{$href}'>
        <i class='bi bi-{$icon} me-2'></i>{$label}
      </a>
    </li>";
}
?>

<!-- SIDEBAR -->
<nav id="sidebar" class="sidebar d-flex flex-column flex-shrink-0 p-3">
  <ul class="nav nav-pills flex-column mb-auto">

    <li class="nav-label">Utama</li>
    <?= nav_item(BASE_URL.'/index.php',                      'speedometer2',    'Dashboard',    $current, $dir) ?>

    <li class="nav-label mt-2">Master Data</li>
    <?= nav_item(BASE_URL.'/pages/obat/index.php',           'capsule',         'Obat',         $current, $dir) ?>
    <?= nav_item(BASE_URL.'/pages/supplier/index.php',       'truck',           'Supplier',     $current, $dir) ?>
    <?= nav_item(BASE_URL.'/pages/pelanggan/index.php',      'people',          'Pelanggan',    $current, $dir) ?>

    <li class="nav-label mt-2">Transaksi</li>
    <?= nav_item(BASE_URL.'/pages/pembelian/index.php',      'cart-plus',       'Pembelian',    $current, $dir) ?>
    <?= nav_item(BASE_URL.'/pages/penjualan/index.php',      'receipt',         'Penjualan',    $current, $dir) ?>
    <?= nav_item(BASE_URL.'/pages/resep/index.php',          'file-medical',    'Resep',        $current, $dir) ?>

    <li class="nav-label mt-2">Laporan</li>
    <?= nav_item(BASE_URL.'/pages/laporan/index.php',        'bar-chart-line',  'Laporan',      $current, $dir) ?>
  </ul>

  <div class="sidebar-footer text-muted small mt-3 pt-3 border-top">
    <i class="bi bi-calendar2 me-1"></i><?= date('d M Y') ?>
  </div>
</nav>

<!-- KONTEN UTAMA -->
<div id="content" class="flex-grow-1 p-4">
