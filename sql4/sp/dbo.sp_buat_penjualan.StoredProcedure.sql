USE [apotek_db];
GO

/****** Object:  StoredProcedure [dbo].[sp_buat_penjualan]    Script Date: 6/10/2026 7:40:39 AM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

CREATE PROCEDURE [dbo].[sp_buat_penjualan]
    @p_id_pelanggan INT,
    @p_id_petugas   INT,
    @p_id_resep     INT = NULL,
    @p_metode_bayar NVARCHAR(20),
    @p_uang_bayar   DECIMAL(14,2),
    @p_id_penjualan INT OUTPUT,
    @p_no_penjualan NVARCHAR(20) OUTPUT,
    @p_pesan        NVARCHAR(255) OUTPUT
AS
BEGIN
    SET NOCOUNT ON;

    DECLARE @v_no NVARCHAR(20);

    -- Generate nomor penjualan unik
    SET @v_no = CONCAT('SL', FORMAT(GETDATE(), 'yyyyMMdd'),
                        RIGHT('000' + CAST(ABS(CHECKSUM(NEWID())) % 9999 AS NVARCHAR), 4));

    BEGIN TRY
        INSERT INTO penjualan_header
            (no_penjualan, id_pelanggan, id_petugas, id_resep, metode_bayar, uang_bayar, status)
        VALUES
            (@v_no, @p_id_pelanggan, @p_id_petugas, @p_id_resep, @p_metode_bayar, @p_uang_bayar, 'selesai');

        SET @p_id_penjualan = SCOPE_IDENTITY();
        SET @p_no_penjualan = @v_no;
        SET @p_pesan        = CONCAT('Transaksi berhasil. No: ', @v_no);
    END TRY
    BEGIN CATCH
        SET @p_pesan = CONCAT('ERROR: ', ERROR_MESSAGE());
    END CATCH
END
GO
