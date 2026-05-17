USE [apotek_db]
GO

/****** Object:  Trigger [dbo].[trg_update_total_penjualan]    Script Date: 5/17/2026 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

-- ============================================================
-- TRIGGER 3: Update total_harga penjualan_header otomatis
-- Tabel  : penjualan_detail
-- Event  : AFTER INSERT, UPDATE
-- Fungsi : Otomatis menghitung ulang subtotal, total_harga,
--          dan uang_kembali di penjualan_header setiap kali
--          detail penjualan ditambah atau diubah
-- ============================================================

CREATE TRIGGER [dbo].[trg_update_total_penjualan]
ON [dbo].[penjualan_detail]
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
        SELECT
            id_penjualan,
            SUM(subtotal) AS total_sub
        FROM penjualan_detail
        WHERE id_penjualan IN (
            SELECT DISTINCT id_penjualan FROM inserted
        )
        GROUP BY id_penjualan
    ) agg ON ph.id_penjualan = agg.id_penjualan;
END
GO
