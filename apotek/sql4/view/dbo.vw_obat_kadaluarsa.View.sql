USE [apotek_db];
GO

/****** Object:  View [dbo].[vw_obat_kadaluarsa]    Script Date: 6/10/2026 7:40:39 AM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

CREATE VIEW [dbo].[vw_obat_kadaluarsa] AS
SELECT
    o.id_obat,
    o.kode_obat,
    o.nama_obat,
    k.nama_kategori,
    o.stok,
    o.satuan,
    o.tgl_kadaluarsa,
    DATEDIFF(DAY, CAST(GETDATE() AS DATE), o.tgl_kadaluarsa) AS sisa_hari,
    CASE
        WHEN o.tgl_kadaluarsa < CAST(GETDATE() AS DATE)                             THEN 'Kadaluarsa'
        WHEN DATEDIFF(DAY, CAST(GETDATE() AS DATE), o.tgl_kadaluarsa) <= 30         THEN 'Kritis'
        WHEN DATEDIFF(DAY, CAST(GETDATE() AS DATE), o.tgl_kadaluarsa) <= 90         THEN 'Perhatian'
        ELSE 'Aman'
    END AS status_kadaluarsa
FROM obat o
LEFT JOIN kategori k ON o.id_kategori = k.id_kategori
WHERE o.tgl_kadaluarsa IS NOT NULL
  AND o.tgl_kadaluarsa <= DATEADD(DAY, 90, CAST(GETDATE() AS DATE))
  AND o.is_active = 1;
GO
