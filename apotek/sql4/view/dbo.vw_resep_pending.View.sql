USE [apotek_db];
GO

/****** Object:  View [dbo].[vw_resep_pending]    Script Date: 6/10/2026 7:40:39 AM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

CREATE VIEW [dbo].[vw_resep_pending] AS
SELECT
    rh.id_resep,
    rh.no_resep,
    pl.nama_pelanggan,
    rh.nama_dokter,
    rh.asal_klinik,
    rh.tgl_resep,
    rh.status,
    COUNT(rd.id_detail)                                         AS jumlah_item,
    DATEDIFF(DAY, rh.tgl_resep, CAST(GETDATE() AS DATE))       AS hari_menunggu
FROM resep_header rh
JOIN pelanggan    pl  ON rh.id_pelanggan = pl.id_pelanggan
JOIN resep_detail rd  ON rh.id_resep     = rd.id_resep
WHERE rh.status IN ('menunggu', 'diproses')
GROUP BY
    rh.id_resep, rh.no_resep, pl.nama_pelanggan,
    rh.nama_dokter, rh.asal_klinik, rh.tgl_resep, rh.status;
GO
