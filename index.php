<?php
// ============================================================
// index.php — Dashboard
// ============================================================

define('BASE_URL', '');
require_once __DIR__ . '/config/database.php';

$halaman = 'Dashboard';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

// --- Ambil statistik ---
$stat_obat      = db_fetch_one($conn, "SELECT COUNT(*) AS total FROM obat WHERE is_active=1");
$stat_stok_tipis = db_fetch_one($conn, "SELECT COUNT(*) AS total FROM vw_stok_menipis");
$stat_kadaluarsa = db_fetch_one($conn, "SELECT COUNT(*) AS total FROM vw_obat_kadaluarsa");
$stat_resep      = db_fetch_one($conn, "SELECT COUNT(*) AS total FROM vw_resep_pending");

$penjualan_hari_ini = db_fetch_one($conn,
    "SELECT COALESCE(SUM(total_harga),0) AS total, COUNT(*) AS jumlah
     FROM penjualan_header
     WHERE DATE(tgl_transaksi) = CURDATE() AND status='selesai'"
);

// --- Stok menipis (5 teratas) ---
$stok_menipis = db_fetch_all($conn, "SELECT * FROM vw_stok_menipis LIMIT 5");

// --- Obat hampir kadaluarsa (5 teratas) ---
$kadaluarsa = db_fetch_all($conn, "SELECT * FROM vw_obat_kadaluarsa LIMIT 5");

// --- Resep pending ---
$resep_pending = db_fetch_all($conn, "SELECT * FROM vw_resep_pending LIMIT 5");

// --- Penjualan 7 hari terakhir ---
$grafik = db_fetch_all($conn,
    "SELECT DATE(tgl_transaksi) AS tgl, SUM(total_harga) AS total
     FROM penjualan_header
     WHERE tgl_transaksi >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) AND status='selesai'
     GROUP BY DATE(tgl_transaksi)
     ORDER BY tgl ASC"
);
?>

<!-- Page Header -->
<div class="page-header d-flex justify-content-between align-items-center">
  <div>
    <h4><i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard</h4>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb"><li class="breadcrumb-item active">Dashboard</li></ol>
    </nav>
  </div>
  <span class="text-muted small"><i class="bi bi-calendar2 me-1"></i><?= tgl_indo(date('Y-m-d')) ?></span>
</div>

<!-- Statistik -->
<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <div class="stat-card bg-primary">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="stat-value"><?= $stat_obat['total'] ?></div>
          <div class="stat-label">Total Obat</div>
        </div>
        <i class="bi bi-capsule stat-icon"></i>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card bg-warning">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="stat-value"><?= $stat_stok_tipis['total'] ?></div>
          <div class="stat-label">Stok Menipis</div>
        </div>
        <i class="bi bi-exclamation-triangle stat-icon"></i>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card bg-danger">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="stat-value"><?= $stat_kadaluarsa['total'] ?></div>
          <div class="stat-label">Hampir Kadaluarsa</div>
        </div>
        <i class="bi bi-calendar-x stat-icon"></i>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card bg-success">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <div class="stat-value"><?= rupiah($penjualan_hari_ini['total']) ?></div>
          <div class="stat-label">Pendapatan Hari Ini (<?= $penjualan_hari_ini['jumlah'] ?> transaksi)</div>
        </div>
        <i class="bi bi-cash-stack stat-icon"></i>
      </div>
    </div>
  </div>
</div>

<!-- Baris 2: Stok Menipis + Kadaluarsa -->
<div class="row g-3 mb-4">
  <div class="col-md-6">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between align-items-center py-2">
        <span><i class="bi bi-exclamation-triangle text-warning me-2"></i>Stok Menipis</span>
        <a href="<?= BASE_URL ?>/pages/obat/index.php?filter=menipis" class="btn btn-sm btn-outline-secondary">Lihat Semua</a>
      </div>
      <div class="card-body p-0">
        <table class="table table-hover mb-0">
          <thead><tr><th>Obat</th><th>Stok</th><th>Min</th><th>Kurang</th></tr></thead>
          <tbody>
          <?php if ($stok_menipis): foreach ($stok_menipis as $r): ?>
            <tr>
              <td><?= htmlspecialchars($r['nama_obat']) ?></td>
              <td class="stok-kritis"><?= $r['stok'] ?> <?= $r['satuan'] ?></td>
              <td><?= $r['stok_minimum'] ?></td>
              <td><span class="badge bg-danger"><?= $r['kekurangan'] ?></span></td>
            </tr>
          <?php endforeach; else: ?>
            <tr><td colspan="4" class="text-center text-muted py-3">Semua stok aman</td></tr>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between align-items-center py-2">
        <span><i class="bi bi-calendar-x text-danger me-2"></i>Hampir Kadaluarsa</span>
        <a href="<?= BASE_URL ?>/pages/laporan/index.php?tab=kadaluarsa" class="btn btn-sm btn-outline-secondary">Lihat Semua</a>
      </div>
      <div class="card-body p-0">
        <table class="table table-hover mb-0">
          <thead><tr><th>Obat</th><th>Kadaluarsa</th><th>Sisa</th><th>Status</th></tr></thead>
          <tbody>
          <?php if ($kadaluarsa): foreach ($kadaluarsa as $r): ?>
            <tr>
              <td><?= htmlspecialchars($r['nama_obat']) ?></td>
              <td><?= tgl_indo($r['tgl_kadaluarsa']) ?></td>
              <td><?= $r['sisa_hari'] ?> hari</td>
              <td><span class="badge badge-<?= strtolower($r['status_kadaluarsa']) ?>"><?= $r['status_kadaluarsa'] ?></span></td>
            </tr>
          <?php endforeach; else: ?>
            <tr><td colspan="4" class="text-center text-muted py-3">Tidak ada obat mendekati kadaluarsa</td></tr>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Resep Pending -->
<?php if ($resep_pending): ?>
<div class="card mb-4">
  <div class="card-header d-flex justify-content-between align-items-center py-2">
    <span><i class="bi bi-file-medical text-info me-2"></i>Resep Menunggu Dilayani</span>
    <a href="<?= BASE_URL ?>/pages/resep/index.php" class="btn btn-sm btn-outline-secondary">Lihat Semua</a>
  </div>
  <div class="card-body p-0">
    <table class="table table-hover mb-0">
      <thead><tr><th>No. Resep</th><th>Pelanggan</th><th>Dokter</th><th>Tanggal</th><th>Status</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($resep_pending as $r): ?>
        <tr>
          <td class="fw-semibold"><?= $r['no_resep'] ?></td>
          <td><?= htmlspecialchars($r['nama_pelanggan']) ?></td>
          <td><?= htmlspecialchars($r['nama_dokter']) ?></td>
          <td><?= tgl_indo($r['tgl_resep']) ?></td>
          <td><span class="badge badge-<?= $r['status'] ?>"><?= ucfirst($r['status']) ?></span></td>
          <td><a href="<?= BASE_URL ?>/pages/resep/detail.php?id=<?= $r['id_resep'] ?>" class="btn btn-sm btn-primary btn-action">Proses</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
