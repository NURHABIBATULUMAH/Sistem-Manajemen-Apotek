-- ============================================================
-- SISTEM MANAJEMEN APOTEK - SQL SERVER
-- File        : 4_stored_procedure.sql
-- Keterangan  : Semua Stored Procedure
-- ============================================================

USE apotek_db;
GO

-- ============================================================
-- SP 1: Tambah stok manual
-- ============================================================
IF OBJECT_ID('sp_tambah_stok', 'P') IS NOT NULL DROP PROCEDURE sp_tambah_stok;
GO

CREATE PROCEDURE sp_tambah_stok
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

-- ============================================================
-- SP 2: Proses penerimaan pembelian (update status + tambah stok via cursor)
-- ============================================================
IF OBJECT_ID('sp_terima_pembelian', 'P') IS NOT NULL DROP PROCEDURE sp_terima_pembelian;
GO

CREATE PROCEDURE sp_terima_pembelian
    @p_id_pembelian INT,
    @p_id_petugas   INT,
    @p_pesan        NVARCHAR(255) OUTPUT
AS
BEGIN
    SET NOCOUNT ON;

    DECLARE @v_status       NVARCHAR(20);
    DECLARE @v_id_obat      INT;
    DECLARE @v_qty          INT;
    DECLARE @v_stok_sblm    INT;

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

            -- Catat log stok
            INSERT INTO log_stok (id_obat, id_petugas, stok_sebelum, jumlah_perubahan,
                                   stok_sesudah, jenis_transaksi, id_referensi,
                                   tipe_referensi, keterangan)
            VALUES (@v_id_obat, @p_id_petugas, @v_stok_sblm, @v_qty,
                    @v_stok_sblm + @v_qty, 'pembelian', @p_id_pembelian,
                    'pembelian_header', 'Penerimaan pembelian');

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

-- ============================================================
-- SP 3: Buat transaksi penjualan
-- ============================================================
IF OBJECT_ID('sp_buat_penjualan', 'P') IS NOT NULL DROP PROCEDURE sp_buat_penjualan;
GO

CREATE PROCEDURE sp_buat_penjualan
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

-- ============================================================
-- SP 4: Rekap penjualan per obat dalam periode (menggunakan CURSOR)
-- ============================================================
IF OBJECT_ID('sp_rekap_penjualan_obat', 'P') IS NOT NULL DROP PROCEDURE sp_rekap_penjualan_obat;
GO

CREATE PROCEDURE sp_rekap_penjualan_obat
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

-- ============================================================
-- SP 5: Proses nonaktifkan semua obat kadaluarsa (CURSOR massal)
-- ============================================================
IF OBJECT_ID('sp_proses_kadaluarsa', 'P') IS NOT NULL DROP PROCEDURE sp_proses_kadaluarsa;
GO

CREATE PROCEDURE sp_proses_kadaluarsa
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

PRINT 'Semua Stored Procedure berhasil dibuat.';
GO
