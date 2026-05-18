<?php
define('BASE_URL', '..'); 
require_once __DIR__ . '/../config/database.php';

$halaman = 'Antrean Resep';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

// Gunakan View buatan kita
$sql = "SELECT * FROM vw_resep_pending";
$data = db_fetch_all($conn, $sql);
?>

<div class="container-fluid">
    <div class="page-header d-flex justify-content-between align-items-center mt-4">
        <h4><i class="bi bi-file-medical me-2 text-info"></i>Antrean Resep Dokter</h4>
        <a href="tambah.php" class="btn btn-success fw-bold">Input Resep Baru</a>
    </div>

    <div class="card mt-3">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>No. Resep</th>
                        <th>Pasien</th>
                        <th>Dokter</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($data): foreach($data as $r): ?>
                    <tr>
                        <td class="fw-bold"><?= $r['no_resep'] ?></td>
                        <td><?= $r['nama_pelanggan'] ?></td>
                        <td><?= $r['nama_dokter'] ?></td>
                        <td><span class="badge badge-<?= $r['status'] ?>"><?= ucfirst($r['status']) ?></span></td>
                        <td class="text-center">
                            <a href="proses.php?id=<?= $r['id_resep'] ?>" class="btn btn-sm btn-primary">Layani Resep</a>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="5" class="text-center py-4 text-muted">Tidak ada antrean resep.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>