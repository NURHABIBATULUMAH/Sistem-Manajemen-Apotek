<?php
// ============================================================
// pelanggan/index.php — Manajemen Data Pelanggan / Pasien
// ============================================================

// Karena folder pelanggan ada di folder utama, naiknya cuma 1 tingkat ke root
define('BASE_URL', '..'); 
require_once __DIR__ . '/../config/database.php';

$halaman = 'Data Pelanggan';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

// Ambil data pelanggan (SQL Server Syntax)
$sql = "SELECT * FROM pelanggan WHERE is_active = 1 ORDER BY nama_pelanggan ASC";
$data_pelanggan = db_fetch_all($conn, $sql);
?>

<div class="container-fluid">
    <div class="page-header d-flex justify-content-between align-items-center mt-4">
        <div>
            <h4><i class="bi bi-people me-2 text-primary"></i>Data Pelanggan</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Pelanggan</li>
                </ol>
            </nav>
        </div>
        <a href="tambah.php" class="btn btn-success btn-sm">
            <i class="bi bi-person-plus me-1"></i>Tambah Pelanggan
        </a>
    </div>

    <div class="card mt-3">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Pelanggan</th>
                        <th>No. Telepon</th>
                        <th>Alamat</th>
                        <th>Jenis</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($data_pelanggan): foreach ($data_pelanggan as $p): ?>
                    <tr>
                        <td class="fw-bold text-primary"><?= $p['kode_pelanggan'] ?></td>
                        <td><?= htmlspecialchars($p['nama_pelanggan']) ?></td>
                        <td><?= $p['no_telepon'] ?: '<span class="text-muted small">Tidak ada</span>' ?></td>
                        <td><small class="text-muted"><?= htmlspecialchars($p['alamat']) ?></small></td>
                        <td>
                            <span class="badge <?= ($p['jenis_pelanggan'] == 'bpjs') ? 'bg-info' : 'bg-secondary' ?>">
                                <?= strtoupper($p['jenis_pelanggan']) ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="edit.php?id=<?= $p['id_pelanggan'] ?>" class="btn btn-sm btn-outline-primary btn-action">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="hapus.php?id=<?= $p['id_pelanggan'] ?>" 
                               class="btn btn-sm btn-outline-danger btn-action btn-hapus" 
                               data-nama="<?= $p['nama_pelanggan'] ?>">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">Belum ada data pelanggan terdaftar.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>