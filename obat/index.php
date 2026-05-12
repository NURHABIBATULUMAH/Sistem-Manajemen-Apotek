<?php
define('BASE_URL', '..'); 
require_once __DIR__ . '/../config/database.php'; 
session_start();

$halaman = 'Stok Obat per Batch';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

/**
 * QUERY INI ADALAH KUNCI:
 * Kita tidak ambil dari tabel 'obat' saja, tapi JOIN ke 'pembelian_detail'.
 * Ini yang bikin tampilannya pecah per Expired Date.
 */
$sql = "SELECT 
            o.id_obat, 
            o.kode_obat,
            o.nama_obat, 
            pd.stok_sisa, 
            pd.tgl_kadaluarsa, 
            s.nama_supplier,
            ph.no_pembelian
        FROM pembelian_detail pd
        JOIN obat o ON pd.id_obat = o.id_obat
        JOIN pembelian_header ph ON pd.id_pembelian = ph.id_pembelian
        JOIN supplier s ON ph.id_supplier = s.id_supplier
        WHERE pd.stok_sisa > 0
        ORDER BY o.nama_obat ASC, pd.tgl_kadaluarsa ASC";

$daftar_stok = db_fetch_all($conn, $sql);
?>

<div class="container-fluid">
    <div class="page-header mt-4 d-flex justify-content-between align-items-center">
        <div>
            <h4><i class="bi bi-box-seam me-2 text-primary"></i>Daftar Stok per Batch (Expired)</h4>
            <p class="text-muted small">Data stok yang tampil di sini murni berdasarkan barang masuk dari Supplier.</p>
        </div>
        <div class="btn-group">
            <a href="tambah.php" class="btn btn-outline-primary btn-sm">Daftar Master Baru</a>
            <a href="../pembelian/tambah.php" class="btn btn-success btn-sm">Input Stok Supplier</a>
        </div>
    </div>

    <div class="card shadow-sm mt-3">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-3">Nama Obat</th>
                        <th>No. Faktur</th>
                        <th>Supplier</th>
                        <th class="text-center">Stok Sisa</th>
                        <th class="text-center">Tgl. Expired</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($daftar_stok)): ?>
                        <tr><td colspan="7" class="text-center py-5">Belum ada barang masuk dari supplier.</td></tr>
                    <?php else: ?>
                        <?php foreach ($daftar_stok as $ds): 
                            $exp = $ds['tgl_kadaluarsa'];
                            $skrg = new DateTime();
                            $diff = $skrg->diff($exp);
                            $hari = (int)$diff->format("%r%a");

                            $row_class = ""; $status = '<span class="badge bg-success">Aman</span>';
                            if ($hari <= 0) {
                                $row_class = "table-dark text-white";
                                $status = '<span class="badge bg-secondary">EXPIRED</span>';
                            } elseif ($hari <= 30) {
                                $row_class = "table-warning";
                                $status = '<span class="badge bg-danger">MAU EXPIRED</span>';
                            }
                        ?>
                            <tr class="<?= $row_class ?>">
                                <td class="ps-3 fw-bold"><?= $ds['nama_obat'] ?></td>
                                <td><small><?= $ds['no_pembelian'] ?></small></td>
                                <td><?= $ds['nama_supplier'] ?></td>
                                <td class="text-center fw-bold"><?= $ds['stok_sisa'] ?></td>
                                <td class="text-center"><?= $ds['tgl_kadaluarsa']->format('d/m/Y') ?></td>
                                <td><?= $status ?></td>
                                <td class="text-center">
                                    <a href="edit.php?id=<?= $ds['id_obat'] ?>" class="btn btn-sm btn-link"><i class="bi bi-pencil"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>