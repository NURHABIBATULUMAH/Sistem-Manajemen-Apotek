USE [apotek_db];
GO

-- ============================================================
-- Trigger: trg_update_total_pembelian
-- Tabel  : pembelian_detail
-- Event  : AFTER INSERT, UPDATE
-- Fungsi : Hitung ulang total_harga di pembelian_header
-- ============================================================

IF OBJECT_ID('dbo.trg_update_total_pembelian', 'TR') IS NOT NULL
    DROP TRIGGER [dbo].[trg_update_total_pembelian];
GO

CREATE TRIGGER [dbo].[trg_update_total_pembelian]
ON [dbo].[pembelian_detail]
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
