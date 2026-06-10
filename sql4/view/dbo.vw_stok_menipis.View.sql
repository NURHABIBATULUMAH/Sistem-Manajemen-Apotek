USE [apotek_db];
GO

/****** Object:  View [dbo].[vw_stok_menipis]    Script Date: 6/10/2026 7:40:39 AM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

CREATE VIEW [dbo].[vw_stok_menipis] AS
SELECT
    o.id_obat,
    o.kode_obat,
    o.nama_obat,
    k.nama_kategori,
    s.nama_supplier,
    o.satuan,
    o.stok,
    o.stok_minimum,
    (o.stok_minimum - o.stok) AS kekurangan,
    o.harga_beli
FROM obat o
LEFT JOIN kategori k ON o.id_kategori = k.id_kategori
LEFT JOIN supplier s ON o.id_supplier = s.id_supplier
WHERE o.stok <= o.stok_minimum
  AND o.is_active = 1;
GO
