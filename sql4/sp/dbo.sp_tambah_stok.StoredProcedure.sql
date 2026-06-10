USE [apotek_db];
GO

/****** Object:  StoredProcedure [dbo].[sp_tambah_stok]    Script Date: 6/10/2026 7:40:39 AM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

CREATE PROCEDURE [dbo].[sp_tambah_stok]
    @p_id_obat      INT,
    @p_jumlah       INT,
    @p_id_petugas   INT,
    @p_keterangan   NVARCHAR(MAX),
    @p_stok_sesudah INT OUTPUT,
    @p_pesan        NVARCHAR(255) OUTPUT
AS
BEGIN
    SET NOCOUNT ON;

    DECLARE @v_stok_sebelum INT = 0;

    SELECT @v_stok_sebelum = stok
    FROM obat
    WHERE id_obat = @p_id_obat;

    IF @v_stok_sebelum IS NULL
    BEGIN
        SET @p_pesan        = 'ERROR: Obat tidak ditemukan';
        SET @p_stok_sesudah = 0;
        RETURN;
    END

    IF @p_jumlah <= 0
    BEGIN
        SET @p_pesan        = 'ERROR: Jumlah harus lebih dari 0';
        SET @p_stok_sesudah = @v_stok_sebelum;
        RETURN;
    END

    UPDATE obat
    SET stok       = stok + @p_jumlah,
        updated_at = GETDATE()
    WHERE id_obat = @p_id_obat;

    SET @p_stok_sesudah = @v_stok_sebelum + @p_jumlah;

    INSERT INTO log_stok (id_obat, id_petugas, stok_sebelum, jumlah_perubahan,
                           stok_sesudah, jenis_transaksi, keterangan)
    VALUES (@p_id_obat, @p_id_petugas, @v_stok_sebelum, @p_jumlah,
            @p_stok_sesudah, 'penyesuaian', @p_keterangan);

    SET @p_pesan = CONCAT('Stok berhasil ditambah. Stok sekarang: ', @p_stok_sesudah);
END
GO
