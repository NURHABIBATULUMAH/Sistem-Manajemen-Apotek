<?php
define('BASE_URL', '..'); 
require_once __DIR__ . '/../config/database.php';

$halaman = 'Data Supplier';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$sql = "SELECT * FROM supplier WHERE is_active = 1 ORDER BY kode_supplier ASC";
$data = db_fetch_all($conn, $sql);
?>

<div class="container-fluid">
    <div class="page-header d-flex justify-content-between align-items-center mt-4">
        <h4><i class="bi bi-truck me-2 text-primary"></i>Data Supplier</h4>
        <a href="tambah.php" class="btn btn-primary btn-sm">Tambah Supplier</a>
    </div>

    <div class="card mt-3">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Supplier</th>
                        <th>Telepon</th>
                        <th>Contact Person</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($data as $r): ?>
                    <tr>
                        <td class="fw-bold"><?= $r['kode_supplier'] ?></td>
                        <td><?= htmlspecialchars($r['nama_supplier']) ?></td>
                        <td><?= $r['no_telepon'] ?></td>
                        <td><?= $r['contact_person'] ?></td>
                        <td class="text-center">
                            <a href="edit.php?id=<?= $r['id_supplier'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>