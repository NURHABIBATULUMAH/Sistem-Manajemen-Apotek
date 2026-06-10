<?php
// ============================================================
// pelanggan/detail.php - Profil & Riwayat Pelanggan (sesuai ERD)
// ============================================================

define('BASE_URL', '..'); 
require_once __DIR__ . '/../config/database.php';

$id = $_GET['id'] ?? null;
if (!$id) { header("Location: index.php"); exit; }

$p = db_fetch_one($conn, "SELECT * FROM pelanggan WHERE id_pelanggan = ?", '', $id);
if (!$p) { header("Location: index.php"); exit; }

$sql_trx = "SELECT 
                ph.id_penjualan,
                ph.no_penjualan,
                ph.tgl_transaksi,
                ph.total_harga,
                ph.status,
                ptr.nama_petugas
            FROM penjualan_header ph
            LEFT JOIN petugas ptr ON ph.id_petugas = ptr.id_petugas
            WHERE ph.id_pelanggan = ?
            ORDER BY ph.tgl_transaksi DESC";
$transactions = db_fetch_all($conn, $sql_trx, '', $id);

$halaman = 'Detail Pelanggan';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb small mb-1">
                    <li class="breadcrumb-item"><a href="../index.php" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Pelanggan</a></li>
                    <li class="breadcrumb-item active">Detail Profil</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-0 text-dark">Detail Profil Pelanggan</h4>
        </div>
        <a href="index.php" class="btn btn-outline-secondary rounded-3 px-4">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="row g-4 text-dark">
        <div class="col-lg-4">
            <div class="card-custom text-center mb-4 p-4 shadow-sm bg-white">
                <!-- Avatar inisial -->
                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center 
                            justify-content-center mb-3 fw-bold fs-3"
                     style="width:100px;height:100px;">
                    <?= strtoupper(substr($p['nama_pelanggan'], 0, 1)) ?>
                </div>
                <h5 class="fw-bold mb-1"><?= htmlspecialchars($p['nama_pelanggan']) ?></h5>
                <p class="text-muted small mb-3">ID: <?= $p['kode_pelanggan'] ?></p>
                <div class="d-flex justify-content-center gap-2 mb-4">
                    <span class="badge <?= ($p['jenis_pelanggan'] ?? '') == 'BPJS' ? 'bg-success' : 'bg-secondary' ?> px-3">
                        <?= $p['jenis_pelanggan'] ?? 'Umum' ?>
                    </span>
                </div>
                <hr class="opacity-50">
                <div class="small text-muted fst-italic">
                    Terdaftar: <?= $p['created_at'] instanceof DateTime ? $p['created_at']->format('d M Y') : '-' ?>
                </div>
            </div>

            <div class="card-custom p-4 shadow-sm bg-white">
                <h6 class="fw-bold mb-4 border-bottom pb-2">Informasi Kontak</h6>

                <div class="d-flex align-items-center mb-3">
                    <div class="bg-light p-2 rounded-3 me-3 text-muted">
                        <i class="bi bi-telephone fs-5"></i>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:11px;">Nomor Telepon</div>
                        <div class="fw-medium"><?= $p['no_telepon'] ?? '-' ?></div>
                    </div>
                </div>

                <?php if (($p['jenis_pelanggan'] ?? '') == 'BPJS'): ?>
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-light p-2 rounded-3 me-3 text-muted">
                        <i class="bi bi-card-text fs-5"></i>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:11px;">No. BPJS</div>
                        <div class="fw-medium"><?= $p['no_bpjs'] ?? '-' ?></div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="d-flex align-items-center mb-0">
                    <div class="bg-light p-2 rounded-3 me-3 text-muted">
                        <i class="bi bi-geo-alt fs-5"></i>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:11px;">Alamat</div>
                        <div class="fw-medium small"><?= $p['alamat'] ?? 'Belum diisi' ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card-custom p-4 shadow-sm bg-white">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="fw-bold mb-0">Riwayat Transaksi</h6>
                    <div class="small text-muted">Total: <?= count($transactions) ?> Transaksi</div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle border-0">
                        <thead class="bg-light">
                            <tr class="text-muted small">
                                <th class="ps-3 py-3">NO. TRANSAKSI</th>
                                <th>TANGGAL</th>
                                <th>KASIR</th>
                                <th>TOTAL</th>
                                <th>STATUS</th>
                                <th class="text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($transactions): foreach ($transactions as $tx): ?>
                            <tr>
                                <td class="ps-3 py-3 fw-bold text-primary"><?= $tx['no_penjualan'] ?></td>
                                <td class="small">
                                    <?= $tx['tgl_transaksi'] instanceof DateTime 
                                        ? $tx['tgl_transaksi']->format('d M Y, H:i') : '-' ?>
                                </td>
                                <td class="small text-muted"><?= htmlspecialchars($tx['nama_petugas']) ?></td>
                                <td class="fw-bold">Rp <?= number_format($tx['total_harga'], 0, ',', '.') ?></td>
                                <td>
                                    <span class="badge <?= $tx['status'] == 'selesai' ? 'bg-success' : 'bg-danger' ?> small">
                                        <?= ucfirst($tx['status']) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="../penjualan/nota.php?id=<?= $tx['id_penjualan'] ?>" 
                                       target="_blank" class="btn btn-sm btn-light border rounded-pill px-3">
                                        <i class="bi bi-receipt me-1"></i> Nota
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-cart-x display-4 d-block mb-3 text-light"></i>
                                    Belum ada transaksi.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
