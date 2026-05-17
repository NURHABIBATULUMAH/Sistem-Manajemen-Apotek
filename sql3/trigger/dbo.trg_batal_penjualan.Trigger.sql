USE [apotek_db]
GO

/****** Object:  Trigger [dbo].[trg_batal_penjualan]    Script Date: 5/17/2026 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

-- ============================================================
-- TRIGGER 2: Kembalikan stok jika penjualan dibatalkan
-- Tabel  : penjualan_header
-- Event  : AFTER UPDATE
-- Fungsi : Otomatis mengembalikan stok obat ke kondisi semula
--          jika status penjualan diubah dari 'selesai' menjadi
--          'dibatalkan'
-- ============================================================

CREATE TRIGGER [dbo].[trg_batal_penjualan]
ON [dbo].[penjualan_header]
AFTER UPDATE
AS
BEGIN
    SET NOCOUNT ON;

    -- Hanya proses jika status berubah dari 'selesai' ke 'dibatalkan'
    IF EXISTS (
        SELECT 1
        FROM inserted i
        JOIN deleted d ON i.id_penjualan = d.id_penjualan
        WHERE i.status = 'dibatalkan'
          AND d.status = 'selesai'
    )
    BEGIN
        -- Kembalikan stok semua obat dalam transaksi yang dibatalkan
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
