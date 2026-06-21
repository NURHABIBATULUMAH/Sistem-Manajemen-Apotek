USE [apotek_db];
GO

/****** Object:  StoredProcedure [dbo].[sp_proses_kadaluarsa]    Script Date: 6/10/2026 7:40:39 AM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

CREATE PROCEDURE [dbo].[sp_proses_kadaluarsa]
AS
BEGIN
    SET NOCOUNT ON;

    DECLARE @v_id_obat      INT;
    DECLARE @v_nama_obat    NVARCHAR(150);
    DECLARE @v_tgl_exp      DATE;
    DECLARE @v_stok_sblm    INT;
    DECLARE @v_jumlah       INT = 0;

    -- CURSOR: ambil semua obat yang sudah kadaluarsa dan masih aktif
    DECLARE cur_exp CURSOR FOR
        SELECT id_obat, nama_obat, tgl_kadaluarsa, stok
        FROM obat
        WHERE tgl_kadaluarsa < CAST(GETDATE() AS DATE)
          AND stok > 0
          AND is_active = 1;

    OPEN cur_exp;
    FETCH NEXT FROM cur_exp INTO @v_id_obat, @v_nama_obat, @v_tgl_exp, @v_stok_sblm;

    WHILE @@FETCH_STATUS = 0
    BEGIN
        -- Nonaktifkan obat
        UPDATE obat
        SET is_active  = 0,
            updated_at = GETDATE()
        WHERE id_obat = @v_id_obat;

        -- Catat log penonaktifan
        INSERT INTO log_stok (id_obat, stok_sebelum, jumlah_perubahan, stok_sesudah,
                               jenis_transaksi, keterangan)
        VALUES (@v_id_obat, @v_stok_sblm, @v_stok_sblm * -1, 0,
                'penyesuaian',
                CONCAT('Nonaktif - kadaluarsa sejak ', CONVERT(NVARCHAR, @v_tgl_exp, 103)));

        SET @v_jumlah = @v_jumlah + 1;

        FETCH NEXT FROM cur_exp INTO @v_id_obat, @v_nama_obat, @v_tgl_exp, @v_stok_sblm;
    END

    CLOSE cur_exp;
    DEALLOCATE cur_exp;

    SELECT CONCAT(@v_jumlah, ' obat kadaluarsa telah dinonaktifkan') AS hasil;
END
GO
