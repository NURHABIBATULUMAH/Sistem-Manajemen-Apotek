USE [apotek_db]
GO

/****** Object:  Trigger [dbo].[trg_update_status_resep]    Script Date: 5/17/2026 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

-- ============================================================
-- TRIGGER 5: Update status resep otomatis saat penjualan dibuat
-- Tabel  : penjualan_header
-- Event  : AFTER INSERT
-- Fungsi : Otomatis mengubah status resep menjadi 'selesai'
--          ketika transaksi penjualan yang terhubung ke resep
--          berhasil dibuat
-- ============================================================

CREATE TRIGGER [dbo].[trg_update_status_resep]
ON [dbo].[penjualan_header]
AFTER INSERT
AS
BEGIN
    SET NOCOUNT ON;

    -- Hanya update resep yang terhubung (id_resep tidak NULL)
    UPDATE rh
    SET rh.status = 'selesai'
    FROM resep_header rh
    JOIN inserted i ON rh.id_resep = i.id_resep
    WHERE i.id_resep IS NOT NULL;
END
GO
