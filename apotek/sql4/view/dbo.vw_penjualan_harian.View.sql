USE [apotek_db];
GO

/****** Object:  View [dbo].[vw_penjualan_harian]    Script Date: 6/10/2026 7:40:39 AM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

CREATE VIEW [dbo].[vw_penjualan_harian] AS
SELECT
    CAST(ph.tgl_transaksi AS DATE)  AS tanggal,
    COUNT(ph.id_penjualan)          AS jumlah_transaksi,
    SUM(ph.total_harga)             AS total_pendapatan,
    SUM(ph.diskon)                  AS total_diskon,
    AVG(ph.total_harga)             AS rata_rata_transaksi,
    ph.metode_bayar,
    p.nama_petugas
FROM penjualan_header ph
JOIN petugas p ON ph.id_petugas = p.id_petugas
WHERE ph.status = 'selesai'
GROUP BY
    CAST(ph.tgl_transaksi AS DATE),
    ph.metode_bayar,
    p.nama_petugas;
GO
