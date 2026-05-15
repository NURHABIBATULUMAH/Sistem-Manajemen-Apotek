-- ============================================================
-- SISTEM MANAJEMEN APOTEK - SQL SERVER
-- File        : 1_struktur_tabel.sql
-- Database    : apotek_db
-- Keterangan  : Membuat semua tabel dengan relasi
-- ============================================================

USE master;
GO

-- Buat database jika belum ada
IF NOT EXISTS (SELECT name FROM sys.databases WHERE name = 'apotek_db')
BEGIN
    CREATE DATABASE apotek_db;
END
GO

USE apotek_db;
GO

-- ============================================================
-- HAPUS TABEL JIKA SUDAH ADA (urutan terbalik karena FK)
-- ============================================================
IF OBJECT_ID('log_stok',            'U') IS NOT NULL DROP TABLE log_stok;
IF OBJECT_ID('penjualan_detail',    'U') IS NOT NULL DROP TABLE penjualan_detail;
IF OBJECT_ID('penjualan_header',    'U') IS NOT NULL DROP TABLE penjualan_header;
IF OBJECT_ID('resep_detail',        'U') IS NOT NULL DROP TABLE resep_detail;
IF OBJECT_ID('resep_header',        'U') IS NOT NULL DROP TABLE resep_header;
IF OBJECT_ID('pembelian_detail',    'U') IS NOT NULL DROP TABLE pembelian_detail;
IF OBJECT_ID('pembelian_header',    'U') IS NOT NULL DROP TABLE pembelian_header;
IF OBJECT_ID('obat',                'U') IS NOT NULL DROP TABLE obat;
IF OBJECT_ID('pelanggan',           'U') IS NOT NULL DROP TABLE pelanggan;
IF OBJECT_ID('petugas',             'U') IS NOT NULL DROP TABLE petugas;
IF OBJECT_ID('supplier',            'U') IS NOT NULL DROP TABLE supplier;
IF OBJECT_ID('kategori',            'U') IS NOT NULL DROP TABLE kategori;
GO

-- ============================================================
-- TABEL MASTER
-- ============================================================

CREATE TABLE kategori (
    id_kategori     INT             IDENTITY(1,1) PRIMARY KEY,
    kode_kategori   NVARCHAR(10)    NOT NULL UNIQUE,
    nama_kategori   NVARCHAR(100)   NOT NULL,
    deskripsi       NVARCHAR(MAX),
    jenis_obat      NVARCHAR(20)    NOT NULL DEFAULT 'bebas'
                    CHECK (jenis_obat IN ('bebas','bebas_terbatas','keras','narkotika','psikotropika')),
    is_active       BIT             NOT NULL DEFAULT 1,
    created_at      DATETIME        NOT NULL DEFAULT GETDATE()
);
GO

CREATE TABLE supplier (
    id_supplier     INT             IDENTITY(1,1) PRIMARY KEY,
    kode_supplier   NVARCHAR(10)    NOT NULL UNIQUE,
    nama_supplier   NVARCHAR(150)   NOT NULL,
    alamat          NVARCHAR(MAX),
    no_telepon      NVARCHAR(20),
    email           NVARCHAR(100),
    contact_person  NVARCHAR(100),
    is_active       BIT             NOT NULL DEFAULT 1,
    created_at      DATETIME        NOT NULL DEFAULT GETDATE()
);
GO

CREATE TABLE petugas (
    id_petugas      INT             IDENTITY(1,1) PRIMARY KEY,
    kode_petugas    NVARCHAR(10)    NOT NULL UNIQUE,
    nama_petugas    NVARCHAR(150)   NOT NULL,
    username        NVARCHAR(50)    NOT NULL UNIQUE,
    password_hash   NVARCHAR(255)   NOT NULL,
    role            NVARCHAR(20)    NOT NULL DEFAULT 'kasir'
                    CHECK (role IN ('admin','apoteker','kasir')),
    no_telepon      NVARCHAR(20),
    is_active       BIT             NOT NULL DEFAULT 1,
    created_at      DATETIME        NOT NULL DEFAULT GETDATE()
);
GO

CREATE TABLE pelanggan (
    id_pelanggan    INT             IDENTITY(1,1) PRIMARY KEY,
    kode_pelanggan  NVARCHAR(10)    NOT NULL UNIQUE,
    nama_pelanggan  NVARCHAR(150)   NOT NULL,
    no_telepon      NVARCHAR(20),
    alamat          NVARCHAR(MAX),
    no_bpjs         NVARCHAR(20),
    jenis_pelanggan NVARCHAR(10)    NOT NULL DEFAULT 'umum'
                    CHECK (jenis_pelanggan IN ('umum','bpjs')),
    created_at      DATETIME        NOT NULL DEFAULT GETDATE()
);
GO

CREATE TABLE obat (
    id_obat         INT             IDENTITY(1,1) PRIMARY KEY,
    kode_obat       NVARCHAR(15)    NOT NULL UNIQUE,
    nama_obat       NVARCHAR(150)   NOT NULL,
    id_kategori     INT             NOT NULL,
    id_supplier     INT             NOT NULL,
    satuan          NVARCHAR(20)    NOT NULL DEFAULT 'pcs',
    stok            INT             NOT NULL DEFAULT 0,
    stok_minimum    INT             NOT NULL DEFAULT 10,
    harga_beli      DECIMAL(12,2)   NOT NULL DEFAULT 0,
    harga_jual      DECIMAL(12,2)   NOT NULL DEFAULT 0,
    tgl_kadaluarsa  DATE,
    lokasi_rak      NVARCHAR(20),
    is_active       BIT             NOT NULL DEFAULT 1,
    created_at      DATETIME        NOT NULL DEFAULT GETDATE(),
    updated_at      DATETIME        NOT NULL DEFAULT GETDATE(),
    CONSTRAINT fk_obat_kategori FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori),
    CONSTRAINT fk_obat_supplier FOREIGN KEY (id_supplier) REFERENCES supplier(id_supplier)
);
GO

-- ============================================================
-- TABEL TRANSAKSI PEMBELIAN
-- ============================================================

CREATE TABLE pembelian_header (
    id_pembelian    INT             IDENTITY(1,1) PRIMARY KEY,
    no_pembelian    NVARCHAR(20)    NOT NULL UNIQUE,
    id_supplier     INT             NOT NULL,
    id_petugas      INT             NOT NULL,
    tgl_pesan       DATE            NOT NULL,
    tgl_terima      DATE,
    total_harga     DECIMAL(14,2)   NOT NULL DEFAULT 0,
    status          NVARCHAR(20)    NOT NULL DEFAULT 'pending'
                    CHECK (status IN ('pending','diterima','dibatalkan')),
    keterangan      NVARCHAR(MAX),
    created_at      DATETIME        NOT NULL DEFAULT GETDATE(),
    CONSTRAINT fk_ph_supplier FOREIGN KEY (id_supplier) REFERENCES supplier(id_supplier),
    CONSTRAINT fk_ph_petugas  FOREIGN KEY (id_petugas)  REFERENCES petugas(id_petugas)
);
GO

CREATE TABLE pembelian_detail (
    id_detail       INT             IDENTITY(1,1) PRIMARY KEY,
    id_pembelian    INT             NOT NULL,
    id_obat         INT             NOT NULL,
    qty_pesan       INT             NOT NULL DEFAULT 0,
    qty_terima      INT             NOT NULL DEFAULT 0,
    harga_satuan    DECIMAL(12,2)   NOT NULL DEFAULT 0,
    subtotal        DECIMAL(14,2)   NOT NULL DEFAULT 0,
    tgl_kadaluarsa  DATE,
    no_batch        NVARCHAR(50),
    CONSTRAINT fk_pd_pembelian FOREIGN KEY (id_pembelian) REFERENCES pembelian_header(id_pembelian),
    CONSTRAINT fk_pd_obat      FOREIGN KEY (id_obat)      REFERENCES obat(id_obat)
);
GO

-- ============================================================
-- TABEL RESEP
-- ============================================================

CREATE TABLE resep_header (
    id_resep        INT             IDENTITY(1,1) PRIMARY KEY,
    no_resep        NVARCHAR(20)    NOT NULL UNIQUE,
    id_pelanggan    INT             NOT NULL,
    id_petugas      INT             NOT NULL,
    nama_dokter     NVARCHAR(150),
    asal_klinik     NVARCHAR(150),
    tgl_resep       DATE            NOT NULL,
    status          NVARCHAR(20)    NOT NULL DEFAULT 'menunggu'
                    CHECK (status IN ('menunggu','diproses','selesai','dibatalkan')),
    created_at      DATETIME        NOT NULL DEFAULT GETDATE(),
    CONSTRAINT fk_rh_pelanggan FOREIGN KEY (id_pelanggan) REFERENCES pelanggan(id_pelanggan),
    CONSTRAINT fk_rh_petugas   FOREIGN KEY (id_petugas)   REFERENCES petugas(id_petugas)
);
GO

CREATE TABLE resep_detail (
    id_detail       INT             IDENTITY(1,1) PRIMARY KEY,
    id_resep        INT             NOT NULL,
    id_obat         INT             NOT NULL,
    qty             INT             NOT NULL DEFAULT 1,
    dosis           NVARCHAR(50),
    aturan_pakai    NVARCHAR(100),
    catatan         NVARCHAR(MAX),
    CONSTRAINT fk_rd_resep FOREIGN KEY (id_resep) REFERENCES resep_header(id_resep),
    CONSTRAINT fk_rd_obat  FOREIGN KEY (id_obat)  REFERENCES obat(id_obat)
);
GO

-- ============================================================
-- TABEL TRANSAKSI PENJUALAN
-- ============================================================

CREATE TABLE penjualan_header (
    id_penjualan    INT             IDENTITY(1,1) PRIMARY KEY,
    no_penjualan    NVARCHAR(20)    NOT NULL UNIQUE,
    id_pelanggan    INT,
    id_petugas      INT             NOT NULL,
    id_resep        INT,
    tgl_transaksi   DATETIME        NOT NULL DEFAULT GETDATE(),
    subtotal        DECIMAL(14,2)   NOT NULL DEFAULT 0,
    diskon          DECIMAL(14,2)   NOT NULL DEFAULT 0,
    total_harga     DECIMAL(14,2)   NOT NULL DEFAULT 0,
    uang_bayar      DECIMAL(14,2)   NOT NULL DEFAULT 0,
    uang_kembali    DECIMAL(14,2)   NOT NULL DEFAULT 0,
    metode_bayar    NVARCHAR(20)    NOT NULL DEFAULT 'tunai'
                    CHECK (metode_bayar IN ('tunai','transfer','bpjs','debit','kredit')),
    status          NVARCHAR(20)    NOT NULL DEFAULT 'selesai'
                    CHECK (status IN ('selesai','dibatalkan')),
    created_at      DATETIME        NOT NULL DEFAULT GETDATE(),
    CONSTRAINT fk_sh_pelanggan FOREIGN KEY (id_pelanggan) REFERENCES pelanggan(id_pelanggan),
    CONSTRAINT fk_sh_petugas   FOREIGN KEY (id_petugas)   REFERENCES petugas(id_petugas),
    CONSTRAINT fk_sh_resep     FOREIGN KEY (id_resep)     REFERENCES resep_header(id_resep)
);
GO

CREATE TABLE penjualan_detail (
    id_detail       INT             IDENTITY(1,1) PRIMARY KEY,
    id_penjualan    INT             NOT NULL,
    id_obat         INT             NOT NULL,
    qty             INT             NOT NULL DEFAULT 1,
    harga_satuan    DECIMAL(12,2)   NOT NULL DEFAULT 0,
    diskon_pct      DECIMAL(5,2)    NOT NULL DEFAULT 0,
    subtotal        DECIMAL(14,2)   NOT NULL DEFAULT 0,
    CONSTRAINT fk_sd_penjualan FOREIGN KEY (id_penjualan) REFERENCES penjualan_header(id_penjualan),
    CONSTRAINT fk_sd_obat      FOREIGN KEY (id_obat)      REFERENCES obat(id_obat)
);
GO

-- ============================================================
-- TABEL LOG STOK
-- ============================================================

CREATE TABLE log_stok (
    id_log              INT             IDENTITY(1,1) PRIMARY KEY,
    id_obat             INT             NOT NULL,
    id_petugas          INT,
    stok_sebelum        INT             NOT NULL DEFAULT 0,
    jumlah_perubahan    INT             NOT NULL DEFAULT 0,
    stok_sesudah        INT             NOT NULL DEFAULT 0,
    jenis_transaksi     NVARCHAR(20)    NOT NULL
                        CHECK (jenis_transaksi IN ('pembelian','penjualan','penyesuaian','return')),
    id_referensi        INT,
    tipe_referensi      NVARCHAR(30),
    keterangan          NVARCHAR(MAX),
    created_at          DATETIME        NOT NULL DEFAULT GETDATE(),
    CONSTRAINT fk_ls_obat    FOREIGN KEY (id_obat)    REFERENCES obat(id_obat),
    CONSTRAINT fk_ls_petugas FOREIGN KEY (id_petugas) REFERENCES petugas(id_petugas)
);
GO

PRINT 'Semua tabel berhasil dibuat.';
GO
