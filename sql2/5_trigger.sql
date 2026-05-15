-- ============================================================
-- SISTEM MANAJEMEN APOTEK - SQL SERVER
-- File        : 5_trigger.sql
-- Keterangan  : Semua Trigger
-- Di SQL Server trigger menggunakan tabel INSERTED dan DELETED
-- ============================================================

USE apotek_db;
GO

-- ============================================================
-- TRIGGER 1: Kurangi stok saat penjualan_detail di-INSERT
-- ============================================================
IF OBJECT_ID('trg_kurangi_stok_penjualan', 'TR') IS NOT NULL
    DROP TRIGGER trg_kurangi_stok_penjualan;
GO

CREATE TRIGGER trg_kurangi_stok_penjualan
ON penjualan_detail
AFTER INSERT
AS
BEGIN
    SET NOCOUNT ON;

    -- Kurangi stok untuk setiap baris yang diinsert
    UPDATE o
    SET o.stok      = o.stok - i.qty,
        o.updated_at = GETDATE()
    FROM obat o
    INNER JOIN inserted i ON o.id_obat = i.id_obat;

    -- Catat ke log_stok
    INSERT INTO log_stok (id_obat, id_petugas, stok_sebelum, jumlah_perubahan,
                           stok_sesudah, jenis_transaksi, id_referensi,
                           tipe_referensi, keterangan)
    SELECT
        i.id_obat,
        ph.id_petugas,
        o.stok + i.qty,     -- stok sebelum (setelah dikurangi + qty kembali)
        i.qty * -1,
        o.stok,             -- stok sesudah (sudah dikurangi oleh UPDATE di atas)
        'penjualan',
        i.id_penjualan,
        'penjualan_header',
        'Penjualan obat'
    FROM inserted i
    JOIN obat o             ON i.id_obat      = o.id_obat
    JOIN penjualan_header ph ON i.id_penjualan = ph.id_penjualan;
END
GO

-- ============================================================
-- TRIGGER 2: Kembalikan stok jika penjualan dibatalkan
-- ============================================================
IF OBJECT_ID('trg_batal_penjualan', 'TR') IS NOT NULL
    DROP TRIGGER trg_batal_penjualan;
GO

CREATE TRIGGER trg_batal_penjualan
ON penjualan_header
AFTER UPDATE
AS
BEGIN
    SET NOCOUNT ON;

    -- Hanya proses jika status berubah dari 'selesai' ke 'dibatalkan'
    IF EXISTS (
        SELECT 1 FROM inserted i
        JOIN deleted d ON i.id_penjualan = d.id_penjualan
        WHERE i.status = 'dibatalkan' AND d.status = 'selesai'
    )
    BEGIN
        UPDATE o
        SET o.stok       = o.stok + sd.qty,
            o.updated_at = GETDATE()
        FROM obat o
        JOIN penjualan_detail sd ON o.id_obat = sd.id_obat
        JOIN inserted i          ON sd.id_penjualan = i.id_penjualan
        WHERE i.status = 'dibatalkan';
    END
END
GO

-- ============================================================
-- TRIGGER 3: Update total_harga penjualan_header otomatis
-- ============================================================
IF OBJECT_ID('trg_update_total_penjualan', 'TR') IS NOT NULL
    DROP TRIGGER trg_update_total_penjualan;
GO

CREATE TRIGGER trg_update_total_penjualan
ON penjualan_detail
AFTER INSERT, UPDATE
AS
BEGIN
    SET NOCOUNT ON;

    -- Update header untuk setiap penjualan yang detailnya berubah
    UPDATE ph
    SET
        ph.subtotal     = agg.total_sub,
        ph.total_harga  = agg.total_sub - ph.diskon,
        ph.uang_kembali = ph.uang_bayar - (agg.total_sub - ph.diskon)
    FROM penjualan_header ph
    JOIN (
        SELECT id_penjualan, SUM(subtotal) AS total_sub
        FROM penjualan_detail
        WHERE id_penjualan IN (SELECT DISTINCT id_penjualan FROM inserted)
        GROUP BY id_penjualan
    ) agg ON ph.id_penjualan = agg.id_penjualan;
END
GO

-- ============================================================
-- TRIGGER 4: Update total_harga pembelian_header otomatis
-- ============================================================
IF OBJECT_ID('trg_update_total_pembelian', 'TR') IS NOT NULL
    DROP TRIGGER trg_update_total_pembelian;
GO

CREATE TRIGGER trg_update_total_pembelian
ON pembelian_detail
AFTER INSERT, UPDATE
AS
BEGIN
    SET NOCOUNT ON;

    UPDATE ph
    SET ph.total_harga = agg.total_sub
    FROM pembelian_header ph
    JOIN (
        SELECT id_pembelian, SUM(subtotal) AS total_sub
        FROM pembelian_detail
        WHERE id_pembelian IN (SELECT DISTINCT id_pembelian FROM inserted)
        GROUP BY id_pembelian
    ) agg ON ph.id_pembelian = agg.id_pembelian;
END
GO

-- ============================================================
-- TRIGGER 5: Update status resep otomatis saat penjualan dibuat
-- ============================================================
IF OBJECT_ID('trg_update_status_resep', 'TR') IS NOT NULL
    DROP TRIGGER trg_update_status_resep;
GO

CREATE TRIGGER trg_update_status_resep
ON penjualan_header
AFTER INSERT
AS
BEGIN
    SET NOCOUNT ON;

    UPDATE rh
    SET rh.status = 'selesai'
    FROM resep_header rh
    JOIN inserted i ON rh.id_resep = i.id_resep
    WHERE i.id_resep IS NOT NULL;
END
GO

PRINT 'Semua Trigger berhasil dibuat.';
GO
