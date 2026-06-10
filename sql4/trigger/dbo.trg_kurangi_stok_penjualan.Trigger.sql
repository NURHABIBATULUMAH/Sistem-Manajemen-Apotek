USE [apotek_db];
GO

-- ============================================================
-- Trigger: trg_kurangi_stok_penjualan
-- Tabel  : penjualan_detail
-- Event  : AFTER INSERT
-- Fungsi : Kurangi stok otomatis + catat log_stok saat item penjualan diinsert
-- ============================================================

IF OBJECT_ID('dbo.trg_kurangi_stok_penjualan', 'TR') IS NOT NULL
    DROP TRIGGER [dbo].[trg_kurangi_stok_penjualan];
GO

CREATE TRIGGER [dbo].[trg_kurangi_stok_penjualan]
ON [dbo].[penjualan_detail]
AFTER INSERT
AS
BEGIN
    SET NOCOUNT ON;

    -- Kurangi stok obat berdasarkan qty yang dijual
    UPDATE o
    SET o.stok       = o.stok - i.qty,
        o.updated_at = GETDATE()
    FROM obat o
    INNER JOIN inserted i ON o.id_obat = i.id_obat;

    -- Catat ke log_stok
    INSERT INTO log_stok (
        id_obat, id_petugas, stok_sebelum, jumlah_perubahan,
        stok_sesudah, jenis_transaksi, keterangan
    )
    SELECT
        i.id_obat,
        ph.id_petugas,
        o.stok + i.qty,     -- stok sebelum (sudah dikurangi, dikembalikan)
        i.qty * -1,         -- negatif = pengurangan
        o.stok,             -- stok sesudah
        'penjualan',
        'Penjualan obat'
    FROM inserted i
    JOIN obat o              ON i.id_obat      = o.id_obat
    JOIN penjualan_header ph ON i.id_penjualan = ph.id_penjualan;
END
GO
