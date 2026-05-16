<?php
// penjualan/nota.php
require_once __DIR__ . '/../config/database.php';

$id = $_GET['id'] ?? die("ID Transaksi tidak ditemukan.");
$h = db_fetch_one($conn, "SELECT h.*, p.nama_pelanggan, pt.nama_petugas FROM penjualan_header h LEFT JOIN pelanggan p ON h.id_pelanggan = p.id_pelanggan JOIN petugas pt ON h.id_petugas = pt.id_petugas WHERE h.id_penjualan = ?", '', $id);

$sql_d = "SELECT pd.*, o.nama_obat FROM penjualan_detail pd JOIN obat o ON pd.id_obat = o.id_obat WHERE pd.id_penjualan = ?";
$d = db_fetch_all($conn, $sql_d, '', $id);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Nota Penjualan - <?= $h['no_penjualan'] ?></title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; width: 320px; margin: 10px auto; font-size: 13px; }
        .text-center { text-align: center; }
        .border-bottom { border-bottom: 1px dashed #000; padding-bottom: 5px; margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; }
        .no-print { text-align: center; margin-bottom: 20px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()">Cetak (Ctrl+P)</button>
        <a href="index.php"><button>Riwayat</button></a>
        <a href="tambah.php"><button>Kasir</button></a>
    </div>

    <div class="text-center">
        <h3 style="margin:0;">APOTEK SEHAT</h3>
        <p style="margin:0;">Bangkalan - Madura</p>
        <p style="margin:5px 0;">Telp: 08123456xxx</p>
    </div>
    
    <div class="border-bottom"></div>
    
    <p style="margin:2px 0;">Tgl: <?= $h['tgl_transaksi']->format('d/m/Y H:i') ?></p>
    <p style="margin:2px 0;">No : <?= $h['no_penjualan'] ?></p>
    <p style="margin:2px 0;">Kasir: <?= $h['nama_petugas'] ?></p>
    <p style="margin:2px 0;">Pel : <?= $h['nama_pelanggan'] ?? 'Umum' ?></p>
    
    <div class="border-bottom"></div>

    <table>
        <?php foreach($d as $i): ?>
        <tr>
            <td colspan="2"><?= $i['nama_obat'] ?></td>
        </tr>
        <tr>
            <td><?= $i['qty'] ?> x <?= number_format($i['harga_satuan'], 0, ',', '.') ?></td>
            <td align="right"><?= number_format($i['subtotal'], 0, ',', '.') ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <div class="border-bottom"></div>

    <table>
        <tr><td align="right">TOTAL:</td><td align="right"><strong>Rp <?= number_format($h['total_harga'], 0, ',', '.') ?></strong></td></tr>
        <tr><td align="right">TUNAI:</td><td align="right">Rp <?= number_format($h['uang_bayar'], 0, ',', '.') ?></td></tr>
        <tr><td align="right">KEMBALI:</td><td align="right">Rp <?= number_format($h['uang_kembali'], 0, ',', '.') ?></td></tr>
    </table>

    <div class="border-bottom"></div>
    <div class="text-center">
        <p>Semoga Cepat Sembuh!</p>
        <p>Terima Kasih</p>
    </div>
</body>
</html>