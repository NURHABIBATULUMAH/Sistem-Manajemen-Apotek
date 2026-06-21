USE [apotek_db];
GO

-- ============================================================
-- Trigger: trg_update_total_penjualan
-- Tabel  : penjualan_detail
-- Event  : AFTER INSERT, UPDATE
-- Fungsi : Hitung ulang subtotal, total_harga, uang_kembali di penjualan_header
-- ============================================================

IF OBJECT_ID('dbo.trg_update_total_penjualan', 'TR') IS NOT NULL
    DROP TRIGGER [dbo].[trg_update_total_penjualan];
GO

CREATE TRIGGER [dbo].[trg_update_total_penjualan]
ON [dbo].[penjualan_detail]
AFTER INSERT, UPDATE
AS
BEGIN
    SET NOCOUNT ON;

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
