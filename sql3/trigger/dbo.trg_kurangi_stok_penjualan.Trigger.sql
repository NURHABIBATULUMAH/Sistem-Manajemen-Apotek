USE [apotek_db]
GO

/****** Object:  Trigger [dbo].[trg_kurangi_stok_penjualan]    Script Date: 5/17/2026 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

-- ============================================================
-- TRIGGER 1: Kurangi stok saat penjualan_detail di-INSERT
-- Tabel  : penjualan_detail
-- Event  : AFTER INSERT
-- Fungsi : Otomatis mengurangi stok obat dan mencatat log_stok
--          setiap kali item penjualan baru dimasukkan
-- ============================================================

CREATE TRIGGER [dbo].[trg_kurangi_stok_penjualan]
ON [dbo].[penjualan_detail]
AFTER INSERT
AS
BEGIN
    SET NOCOUNT ON;

    -- Kurangi stok untuk setiap baris yang diinsert
    UPDATE o
    SET o.stok       = o.stok - i.qty,
        o.updated_at = GETDATE()
    FROM obat o
    INNER JOIN inserted i ON o.id_obat = i.id_obat;

    -- Catat ke log_stok
    INSERT INTO log_stok (
        id_obat,
        id_petugas,
        stok_sebelum,
        jumlah_perubahan,
        stok_sesudah,
        jenis_transaksi,
        id_referensi,
        tipe_referensi,
        keterangan
    )
    SELECT
        i.id_obat,
        ph.id_petugas,
        o.stok + i.qty,     -- stok sebelum (setelah dikurangi + qty dikembalikan)
        i.qty * -1,         -- perubahan negatif karena stok berkurang
        o.stok,             -- stok sesudah (sudah dikurangi oleh UPDATE di atas)
        'penjualan',
        i.id_penjualan,
        'penjualan_header',
        'Penjualan obat'
    FROM inserted i
    JOIN obat o              ON i.id_obat       = o.id_obat
    JOIN penjualan_header ph ON i.id_penjualan  = ph.id_penjualan;
END
GO
