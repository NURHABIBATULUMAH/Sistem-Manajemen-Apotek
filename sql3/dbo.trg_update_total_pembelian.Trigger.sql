USE [apotek_db]
GO

/****** Object:  Trigger [dbo].[trg_update_total_pembelian]    Script Date: 5/17/2026 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

-- ============================================================
-- TRIGGER 4: Update total_harga pembelian_header otomatis
-- Tabel  : pembelian_detail
-- Event  : AFTER INSERT, UPDATE
-- Fungsi : Otomatis menghitung ulang total_harga di
--          pembelian_header setiap kali detail pembelian
--          ditambah atau diubah
-- ============================================================

CREATE TRIGGER [dbo].[trg_update_total_pembelian]
ON [dbo].[pembelian_detail]
AFTER INSERT, UPDATE
AS
BEGIN
    SET NOCOUNT ON;

    -- Update total_harga header untuk setiap pembelian yang detailnya berubah
    UPDATE ph
    SET ph.total_harga = agg.total_sub
    FROM pembelian_header ph
    JOIN (
        SELECT
            id_pembelian,
            SUM(subtotal) AS total_sub
        FROM pembelian_detail
        WHERE id_pembelian IN (
            SELECT DISTINCT id_pembelian FROM inserted
        )
        GROUP BY id_pembelian
    ) agg ON ph.id_pembelian = agg.id_pembelian;
END
GO
