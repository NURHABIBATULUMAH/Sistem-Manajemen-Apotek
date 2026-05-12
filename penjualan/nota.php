<?php
require_once __DIR__ . '/../config/database.php';
$id = $_GET['id'] ?? die("ID Missing");
$h = db_fetch_one($conn, "SELECT h.*, p.nama_pelanggan, pt.nama_petugas FROM penjualan_header h LEFT JOIN pelanggan p ON h.id_pelanggan = p.id_pelanggan JOIN petugas pt ON h.id_petugas = pt.id_petugas WHERE h.id_penjualan = ?", '', $id);
// Query Sakti JOIN ke resep_detail untuk ambil Dosis
$sql_d = "SELECT pd.*, o.nama_obat, rd.dosis, rd.aturan_pakai FROM penjualan_detail pd JOIN penjualan_header ph ON pd.id_penjualan = ph.id_penjualan JOIN obat o ON pd.id_obat = o.id_obat LEFT JOIN resep_detail rd ON ph.id_resep = rd.id_resep AND pd.id_obat = rd.id_obat WHERE pd.id_penjualan = ?";
$d = db_fetch_all($conn, $sql_d, '', $id);
?>
<!DOCTYPE html>
<html>
<head><title>Nota <?= $h['no_penjualan'] ?></title><style>body{font-family:monospace;width:300px;margin:auto;} .no-print{text-align:center;padding:10px;} @media print{.no-print{display:none;}} table{width:100%; border-collapse:collapse;}</style></head>
<body>
    <div class="no-print"><button onclick="window.print()">Cetak</button> <a href="tambah.php"><button>Kembali</button></a></div>
    <center><strong>APOTEK UTM</strong><br>Bangkalan, Madura</center><hr>
    Tgl: <?= $h['tgl_transaksi']->format('d/m/Y H:i') ?><br>No : <?= $h['no_penjualan'] ?><br>Pel: <?= $h['nama_pelanggan'] ?? 'Umum' ?><hr>
    <table>
    <?php foreach($d as $i): ?>
        <tr><td colspan="2"><strong><?= $i['nama_obat'] ?></strong></td></tr>
        <tr>
            <td style="font-size:11px;"><?= $i['qty'] ?> x <?= number_format($i['harga_satuan']) ?><br>
                <?php if($i['dosis']): ?><i>Aturan: <?= $i['dosis'] ?> (<?= $i['aturan_pakai'] ?>)</i><?php endif; ?>
            </td>
            <td align="right" valign="top"><?= number_format($i['subtotal']) ?></td>
        </tr>
    <?php endforeach; ?>
    </table><hr>
    <strong>TOTAL : <?= number_format($h['total_harga']) ?></strong><br>Bayar : <?= number_format($h['uang_bayar']) ?><br>Sisa  : <?= number_format($h['uang_kembali']) ?><hr>
    <center>Semoga Lekas Sembuh!</center>
</body>
</html>