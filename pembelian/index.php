<?php
// ============================================================
// pembelian/index.php — Riwayat Stok Masuk dari Supplier
// ============================================================

define('BASE_URL', '..'); 
require_once __DIR__ . '/../config/database.php';
session_start();

$halaman = 'Riwayat Pembelian';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

// Query untuk mengambil semua riwayat pembelian
// Kita join ke tabel supplier agar muncul nama PT-nya, bukan cuma ID
$sql = "SELECT h.*, s.nama_supplier 
        FROM pembelian_header h 
        JOIN supplier s ON h.id_supplier = s.id_supplier 
        ORDER BY h.tgl_pesan DESC, h.id_pembelian DESC";
$riwayat = db_fetch_all($conn, $sql);
?>

<div class="container-fluid">
    <div class="page-header mt-4 d-flex justify-content-between align-items-center">
        <div>
            <h4><i class="bi bi-cart-plus me-2 text-success"></i>Riwayat Pembelian (Stok Masuk)</h4>
            <p class="text-muted small">Daftar obat yang masuk dari supplier untuk menambah stok gudang.</p>
        </div>
        <a href="tambah.php" class="btn btn-success fw-bold">
            <i class="bi bi-plus-lg me-1"></i> Input Pembelian Baru
        </a>
    </div>

    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] == 'sukses'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Berhasil!</strong> Data pembelian stok telah disimpan ke database.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif ($_GET['status'] == 'terhapus'): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Terhapus!</strong> Riwayat pembelian telah dihapus dan stok telah disesuaikan kembali.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="card shadow-sm mt-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase" style="font-size: 0.85rem;">
                        <tr>
                            <th class="ps-3" width="18%">No. Pembelian</th>
                            <th width="15%">Tanggal</th>
                            <th width="25%">Supplier</th>
                            <th width="15%">Total Harga</th>
                            <th width="10%" class="text-center">Status</th>
                            <th width="17%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($riwayat)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                    Belum ada data pembelian stok.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($riwayat as $row): ?>
                                <tr>
                                    <td class="ps-3 fw-bold text-primary"><?= $row['no_pembelian'] ?></td>
                                    <td><?= $row['tgl_pesan'] ? $row['tgl_pesan']->format('d M Y') : '-' ?></td>
                                    <td><?= $row['nama_supplier'] ?></td>
                                    <td class="fw-bold">
                                        <span class="<?= ($row['total_harga'] <= 0) ? 'text-danger' : 'text-dark' ?>">
                                            Rp <?= number_format($row['total_harga'], 0, ',', '.') ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3">Selesai</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="detail.php?id=<?= $row['id_pembelian'] ?>" class="btn btn-sm btn-info text-white">
                                                <i class="bi bi-eye"></i> Detail
                                            </a>
                                            <a href="hapus.php?id=<?= $row['id_pembelian'] ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('Yakin ingin menghapus riwayat ini? Stok obat yang berkaitan akan dikurangi kembali secara otomatis.')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php 
    $total_pengeluaran = 0;
    foreach($riwayat as $r) { $total_pengeluaran += $r['total_harga']; }
    ?>
    <div class="mt-3 text-end pe-2">
        <h6 class="text-muted">Total Seluruh Pengeluaran: 
            <span class="fw-bold text-dark fs-5 ms-2">Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></span>
        </h6>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>