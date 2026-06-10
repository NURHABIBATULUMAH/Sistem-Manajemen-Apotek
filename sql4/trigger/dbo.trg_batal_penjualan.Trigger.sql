USE [apotek_db];
GO

-- ============================================================
-- Trigger: trg_batal_penjualan
-- Tabel  : penjualan_header
-- Event  : AFTER UPDATE
-- Fungsi : Kembalikan stok saat status penjualan berubah ke 'dibatalkan'
-- ============================================================

IF OBJECT_ID('dbo.trg_batal_penjualan', 'TR') IS NOT NULL
    DROP TRIGGER [dbo].[trg_batal_penjualan];
GO

CREATE TRIGGER [dbo].[trg_batal_penjualan]
ON [dbo].[penjualan_header]
AFTER UPDATE
AS
BEGIN
    SET NOCOUNT ON;

    -- Hanya proses jika status berubah dari 'selesai' ke 'dibatalkan'
    IF EXISTS (
        SELECT 1 FROM inserted i
        JOIN deleted d ON i.id_penjualan = d.id_penjualan
        WHERE i.status = 'dibatalkan'
          AND d.status = 'selesai'
    )
    BEGIN
        UPDATE o
        SET o.stok       = o.stok + sd.qty,
            o.updated_at = GETDATE()
        FROM obat o
        JOIN penjualan_detail sd ON o.id_obat       = sd.id_obat
        JOIN inserted i          ON sd.id_penjualan = i.id_penjualan
        WHERE i.status = 'dibatalkan';
    END
END
GO
