<?php
// ============================================================
// penjualan/index.php - Riwayat Transaksi Modern
// ============================================================

define('BASE_URL', '..'); 
require_once __DIR__ . '/../config/database.php';

$halaman = 'Riwayat Penjualan';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

// Ambil data penjualan dengan JOIN ke pelanggan dan petugas
$sql = "SELECT ph.*, p.nama_pelanggan, pt.nama_petugas 
        FROM penjualan_header ph
        LEFT JOIN pelanggan p ON ph.id_pelanggan = p.id_pelanggan
        JOIN petugas pt ON ph.id_petugas = pt.id_petugas
        ORDER BY ph.tgl_transaksi DESC";
$data = db_fetch_all($conn, $sql);
?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0 text-dark"><i class="bi bi-receipt me-2 text-primary"></i>Riwayat Transaksi Penjualan</h4>
            <p class="text-muted small mb-0">Daftar seluruh nota penjualan yang telah diterbitkan.</p>
        </div>
        <a href="tambah.php" class="btn btn-primary rounded-3 shadow-sm px-4 fw-bold">
            <i class="bi bi-plus-lg me-1"></i> Pesanan Baru
        </a>
    </div>

    <div class="card-custom shadow-sm border-0 bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr class="bg-light text-muted small">
                        <th class="ps-4 py-3">NO. FAKTUR</th>
                        <th>TANGGAL</th>
                        <th>PELANGGAN</th>
                        <th>KASIR</th>
                        <th>TOTAL HARGA</th>
                        <th>METODE</th>
                        <th class="text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($data): foreach($data as $r): ?>
                    <tr>
                        <td class="ps-4 fw-bold text-primary"><?= $r['no_penjualan'] ?></td>
                        <td class="small">
                            <?= $r['tgl_transaksi'] instanceof DateTime ? $r['tgl_transaksi']->format('d/m/Y H:i') : '-' ?>
                        </td>
                        <td><?= $r['nama_pelanggan'] ?: '<span class="text-muted italic small">Umum</span>' ?></td>
                        <td class="small text-muted"><?= htmlspecialchars($r['nama_petugas']) ?></td>
                        <td class="fw-bold text-dark">Rp <?= number_format($r['total_harga'], 0, ',', '.') ?></td>
                        <td>
                            <span class="badge bg-light text-dark border px-2"><?= strtoupper($r['metode_bayar']) ?></span>
                        </td>
                        <td class="text-center">
                            <a href="nota.php?id=<?= $r['id_penjualan'] ?>" target="_blank" class="btn btn-sm btn-light border" title="Cetak Nota">
                                <i class="bi bi-printer text-muted"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="7" class="text-center py-5 text-muted">Belum ada transaksi terekam.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>