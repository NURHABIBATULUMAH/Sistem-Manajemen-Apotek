USE [apotek_db];
GO

/****** Object:  StoredProcedure [dbo].[sp_terima_pembelian]    Script Date: 6/10/2026 7:40:39 AM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

CREATE PROCEDURE [dbo].[sp_terima_pembelian]
    @p_id_pembelian INT,
    @p_id_petugas   INT,
    @p_pesan        NVARCHAR(255) OUTPUT
AS
BEGIN
    SET NOCOUNT ON;

    DECLARE @v_status    NVARCHAR(20);
    DECLARE @v_id_obat   INT;
    DECLARE @v_qty       INT;
    DECLARE @v_stok_sblm INT;

    -- Cek status pembelian
    SELECT @v_status = status
    FROM pembelian_header
    WHERE id_pembelian = @p_id_pembelian;

    IF @v_status IS NULL
    BEGIN
        SET @p_pesan = 'ERROR: Nomor pembelian tidak ditemukan';
        RETURN;
    END

    IF @v_status != 'pending'
    BEGIN
        SET @p_pesan = CONCAT('ERROR: Status pembelian sudah ', @v_status);
        RETURN;
    END

    BEGIN TRY
        BEGIN TRANSACTION;

        -- CURSOR: iterasi setiap obat dalam detail pembelian
        DECLARE cur_detail CURSOR FOR
            SELECT id_obat, qty_pesan
            FROM pembelian_detail
            WHERE id_pembelian = @p_id_pembelian;

        OPEN cur_detail;
        FETCH NEXT FROM cur_detail INTO @v_id_obat, @v_qty;

        WHILE @@FETCH_STATUS = 0
        BEGIN
            SELECT @v_stok_sblm = stok
            FROM obat
            WHERE id_obat = @v_id_obat;

            -- Tambah stok obat
            UPDATE obat
            SET stok       = stok + @v_qty,
                updated_at = GETDATE()
            WHERE id_obat = @v_id_obat;

            -- Catat log stok (tanpa id_referensi & tipe_referensi)
            INSERT INTO log_stok
                (id_obat, id_petugas, stok_sebelum, jumlah_perubahan,
                 stok_sesudah, jenis_transaksi, keterangan)
            VALUES
                (@v_id_obat, @p_id_petugas, @v_stok_sblm, @v_qty,
                 @v_stok_sblm + @v_qty, 'pembelian', 'Penerimaan pembelian');

            FETCH NEXT FROM cur_detail INTO @v_id_obat, @v_qty;
        END

        CLOSE cur_detail;
        DEALLOCATE cur_detail;

        -- Update status pembelian menjadi diterima
        UPDATE pembelian_header
        SET status     = 'diterima',
            tgl_terima = CAST(GETDATE() AS DATE)
        WHERE id_pembelian = @p_id_pembelian;

        COMMIT TRANSACTION;
        SET @p_pesan = 'Pembelian berhasil diterima dan stok diperbarui';
    END TRY
    BEGIN CATCH
        IF @@TRANCOUNT > 0 ROLLBACK TRANSACTION;
        SET @p_pesan = CONCAT('ERROR: ', ERROR_MESSAGE());
    END CATCH
END
GO
USE [master]
GO
ALTER DATABASE [apotek_db] SET  READ_WRITE 
GO
