USE [apotek_db];
GO

/****** Object:  StoredProcedure [dbo].[sp_rekap_penjualan_obat]    Script Date: 6/10/2026 7:40:39 AM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

CREATE PROCEDURE [dbo].[sp_rekap_penjualan_obat]
    @p_tgl_awal     DATE,
    @p_tgl_akhir    DATE
AS
BEGIN
    SET NOCOUNT ON;

    -- Variabel cursor
    DECLARE @v_id_obat      INT;
    DECLARE @v_nama_obat    NVARCHAR(150);
    DECLARE @v_total_qty    INT;
    DECLARE @v_total_nilai  DECIMAL(14,2);

    -- Tabel sementara untuk menampung hasil
    CREATE TABLE #tmp_rekap (
        id_obat     INT,
        nama_obat   NVARCHAR(150),
        total_qty   INT,
        total_nilai DECIMAL(14,2)
    );

    -- CURSOR: iterasi semua obat dan hitung penjualannya
    DECLARE cur_obat CURSOR FOR
        SELECT
            o.id_obat,
            o.nama_obat,
            ISNULL(SUM(sd.qty), 0)          AS total_qty,
            ISNULL(SUM(sd.subtotal), 0)     AS total_nilai
        FROM obat o
        LEFT JOIN penjualan_detail sd   ON o.id_obat = sd.id_obat
        LEFT JOIN penjualan_header sh   ON sd.id_penjualan = sh.id_penjualan
            AND sh.status = 'selesai'
            AND CAST(sh.tgl_transaksi AS DATE) BETWEEN @p_tgl_awal AND @p_tgl_akhir
        WHERE o.is_active = 1
        GROUP BY o.id_obat, o.nama_obat;

    OPEN cur_obat;
    FETCH NEXT FROM cur_obat INTO @v_id_obat, @v_nama_obat, @v_total_qty, @v_total_nilai;

    WHILE @@FETCH_STATUS = 0
    BEGIN
        INSERT INTO #tmp_rekap VALUES (@v_id_obat, @v_nama_obat, @v_total_qty, @v_total_nilai);
        FETCH NEXT FROM cur_obat INTO @v_id_obat, @v_nama_obat, @v_total_qty, @v_total_nilai;
    END

    CLOSE cur_obat;
    DEALLOCATE cur_obat;

    -- Tampilkan hasil diurutkan dari nilai terbesar
    SELECT * FROM #tmp_rekap ORDER BY total_nilai DESC;

    DROP TABLE #tmp_rekap;
END
GO
