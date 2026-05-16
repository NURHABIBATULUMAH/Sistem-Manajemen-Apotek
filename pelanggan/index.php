<?php
// ============================================================
// pelanggan/index.php - Daftar Pelanggan Modern (Persis Gambar)
// ============================================================

define('BASE_URL', '..'); 
require_once __DIR__ . '/../config/database.php';

$halaman = 'Data Pelanggan';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

// Ambil semua data pelanggan aktif
$sql = "SELECT * FROM pelanggan WHERE is_active = 1 ORDER BY nama_pelanggan ASC";
$data_pelanggan = db_fetch_all($conn, $sql);
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Manajemen Pelanggan</h4>
            <p class="text-muted small mb-0">Daftar pelanggan aktif di database.</p>
        </div>
        <a href="tambah.php" class="btn btn-primary rounded-3 shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Tambah Pelanggan
        </a>
    </div>

    <div class="card-custom">
        <div class="table-responsive">
            <table class="table table-hover align-middle border-0">
                <thead>
                    <tr class="text-muted">
                        <th class="ps-4">No.</th>
                        <th>Foto</th>
                        <th>Kode/ID</th>
                        <th>Nama Pelanggan</th>
                        <th>No HP/Tlp</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($data_pelanggan): $no = 1; foreach ($data_pelanggan as $p): ?>
                    <tr>
                        <td class="ps-4"><?= $no++ ?></td>
                        <td>
                            <img src="<?= BASE_URL ?>/assets/img/profiles/<?= $p['foto'] ?? 'default_pelanggan.png' ?>" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                        </td>
                        <td class="fw-bold text-muted"><?= $p['kode_pelanggan'] ?></td>
                        <td class="fw-bold text-main"><?= htmlspecialchars($p['nama_pelanggan']) ?></td>
                        <td><?= htmlspecialchars($p['no_hp'] ?? '-') ?></td>
                        <td>
                            <span class="badge <?= $p['jenis_pelanggan'] == 'BPJS' ? 'badge-selesai' : 'bg-light text-dark' ?>">
                                <?= $p['jenis_pelanggan'] ?>
                            </span>
                        </td>
                        <td><span class="badge badge-selesai">Aktif</span></td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="detail.php?id=<?= $p['id_pelanggan'] ?>" class="text-muted p-2" title="Liat Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="edit.php?id=<?= $p['id_pelanggan'] ?>" class="text-muted p-2" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="hapus.php?id=<?= $p['id_pelanggan'] ?>" class="text-danger p-2 btn-hapus" data-nama="<?= $p['nama_pelanggan'] ?>" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">Belum ada data pelanggan.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>