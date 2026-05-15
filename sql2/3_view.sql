-- ============================================================
-- SISTEM MANAJEMEN APOTEK - SQL SERVER
-- File        : 3_view.sql
-- Keterangan  : Semua VIEW untuk laporan dan monitoring
-- ============================================================

USE apotek_db;
GO

-- ============================================================
-- VIEW 1: Obat dengan stok menipis
-- ============================================================
IF OBJECT_ID('vw_stok_menipis', 'V') IS NOT NULL DROP VIEW vw_stok_menipis;
GO

CREATE VIEW vw_stok_menipis AS
SELECT
    o.id_obat,
    o.kode_obat,
    o.nama_obat,
    k.nama_kategori,
    s.nama_supplier,
    o.satuan,
    o.stok,
    o.stok_minimum,
    (o.stok_minimum - o.stok)  AS kekurangan,
    o.harga_beli,
    o.lokasi_rak
FROM obat o
JOIN kategori k ON o.id_kategori = k.id_kategori
JOIN supplier s ON o.id_supplier = s.id_supplier
WHERE o.stok <= o.stok_minimum
  AND o.is_active = 1;
GO

-- ============================================================
-- VIEW 2: Obat mendekati / sudah kadaluarsa (90 hari ke depan)
-- ============================================================
IF OBJECT_ID('vw_obat_kadaluarsa', 'V') IS NOT NULL DROP VIEW vw_obat_kadaluarsa;
GO

CREATE VIEW vw_obat_kadaluarsa AS
SELECT
    o.id_obat,
    o.kode_obat,
    o.nama_obat,
    k.nama_kategori,
    o.stok,
    o.satuan,
    o.tgl_kadaluarsa,
    DATEDIFF(DAY, CAST(GETDATE() AS DATE), o.tgl_kadaluarsa) AS sisa_hari,
    CASE
        WHEN o.tgl_kadaluarsa < CAST(GETDATE() AS DATE)                                    THEN 'Kadaluarsa'
        WHEN DATEDIFF(DAY, CAST(GETDATE() AS DATE), o.tgl_kadaluarsa) <= 30               THEN 'Kritis'
        WHEN DATEDIFF(DAY, CAST(GETDATE() AS DATE), o.tgl_kadaluarsa) <= 90               THEN 'Perhatian'
        ELSE 'Aman'
    END AS status_kadaluarsa
FROM obat o
JOIN kategori k ON o.id_kategori = k.id_kategori
WHERE o.tgl_kadaluarsa IS NOT NULL
  AND o.tgl_kadaluarsa <= DATEADD(DAY, 90, CAST(GETDATE() AS DATE))
  AND o.is_active = 1;
GO

-- ============================================================
-- VIEW 3: Rekap penjualan harian
-- ============================================================
IF OBJECT_ID('vw_penjualan_harian', 'V') IS NOT NULL DROP VIEW vw_penjualan_harian;
GO

CREATE VIEW vw_penjualan_harian AS
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

-- ============================================================
-- VIEW 4: Resep yang belum dilayani
-- ============================================================
IF OBJECT_ID('vw_resep_pending', 'V') IS NOT NULL DROP VIEW vw_resep_pending;
GO

CREATE VIEW vw_resep_pending AS
SELECT
    rh.id_resep,
    rh.no_resep,
    pl.nama_pelanggan,
    pl.no_telepon,
    rh.nama_dokter,
    rh.asal_klinik,
    rh.tgl_resep,
    rh.status,
    COUNT(rd.id_detail)                                                     AS jumlah_item,
    DATEDIFF(DAY, rh.tgl_resep, CAST(GETDATE() AS DATE))                   AS hari_menunggu
FROM resep_header rh
JOIN pelanggan pl   ON rh.id_pelanggan = pl.id_pelanggan
JOIN resep_detail rd ON rh.id_resep    = rd.id_resep
WHERE rh.status IN ('menunggu','diproses')
GROUP BY
    rh.id_resep, rh.no_resep, pl.nama_pelanggan, pl.no_telepon,
    rh.nama_dokter, rh.asal_klinik, rh.tgl_resep, rh.status;
GO

PRINT 'Semua VIEW berhasil dibuat.';
GO
