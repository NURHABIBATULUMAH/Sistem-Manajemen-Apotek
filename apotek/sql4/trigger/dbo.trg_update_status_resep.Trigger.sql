USE [apotek_db];
GO

-- ============================================================
-- Trigger: trg_update_status_resep
-- Tabel  : penjualan_header
-- Event  : AFTER INSERT
-- Fungsi : Ubah status resep -> 'selesai' saat penjualan dari resep dibuat
-- ============================================================

IF OBJECT_ID('dbo.trg_update_status_resep', 'TR') IS NOT NULL
    DROP TRIGGER [dbo].[trg_update_status_resep];
GO

CREATE TRIGGER [dbo].[trg_update_status_resep]
ON [dbo].[penjualan_header]
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
