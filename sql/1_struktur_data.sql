-- ============================================================
-- SISTEM MANAJEMEN APOTEK
-- Database : apotek_db
-- Versi     : 1.0
-- ============================================================

CREATE DATABASE IF NOT EXISTS apotek_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE apotek_db;

-- ------------------------------------------------------------
-- TABEL MASTER
-- ------------------------------------------------------------

CREATE TABLE kategori (
  id_kategori   INT             AUTO_INCREMENT PRIMARY KEY,
  kode_kategori VARCHAR(10)     NOT NULL UNIQUE,
  nama_kategori VARCHAR(100)    NOT NULL,
  deskripsi     TEXT,
  jenis_obat    ENUM('bebas','bebas_terbatas','keras','narkotika','psikotropika') NOT NULL DEFAULT 'bebas',
  is_active     TINYINT(1)      NOT NULL DEFAULT 1,
  created_at    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE supplier (
  id_supplier     INT           AUTO_INCREMENT PRIMARY KEY,
  kode_supplier   VARCHAR(10)   NOT NULL UNIQUE,
  nama_supplier   VARCHAR(150)  NOT NULL,
  alamat          TEXT,
  no_telepon      VARCHAR(20),
  email           VARCHAR(100),
  contact_person  VARCHAR(100),
  is_active       TINYINT(1)    NOT NULL DEFAULT 1,
  created_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE petugas (
  id_petugas    INT             AUTO_INCREMENT PRIMARY KEY,
  kode_petugas  VARCHAR(10)     NOT NULL UNIQUE,
  nama_petugas  VARCHAR(150)    NOT NULL,
  username      VARCHAR(50)     NOT NULL UNIQUE,
  password_hash VARCHAR(255)    NOT NULL,
  role          ENUM('admin','apoteker','kasir') NOT NULL DEFAULT 'kasir',
  no_telepon    VARCHAR(20),
  is_active     TINYINT(1)      NOT NULL DEFAULT 1,
  created_at    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE pelanggan (
  id_pelanggan    INT           AUTO_INCREMENT PRIMARY KEY,
  kode_pelanggan  VARCHAR(10)   NOT NULL UNIQUE,
  nama_pelanggan  VARCHAR(150)  NOT NULL,
  no_telepon      VARCHAR(20),
  alamat          TEXT,
  no_bpjs         VARCHAR(20),
  jenis_pelanggan ENUM('umum','bpjs') NOT NULL DEFAULT 'umum',
  created_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE obat (
  id_obat         INT           AUTO_INCREMENT PRIMARY KEY,
  kode_obat       VARCHAR(15)   NOT NULL UNIQUE,
  nama_obat       VARCHAR(150)  NOT NULL,
  id_kategori     INT           NOT NULL,
  id_supplier     INT           NOT NULL,
  satuan          VARCHAR(20)   NOT NULL DEFAULT 'pcs',
  stok            INT           NOT NULL DEFAULT 0,
  stok_minimum    INT           NOT NULL DEFAULT 10,
  harga_beli      DECIMAL(12,2) NOT NULL DEFAULT 0,
  harga_jual      DECIMAL(12,2) NOT NULL DEFAULT 0,
  tgl_kadaluarsa  DATE,
  lokasi_rak      VARCHAR(20),
  is_active       TINYINT(1)    NOT NULL DEFAULT 1,
  created_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_obat_kategori  FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori),
  CONSTRAINT fk_obat_supplier  FOREIGN KEY (id_supplier) REFERENCES supplier(id_supplier)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- TABEL TRANSAKSI PEMBELIAN
-- ------------------------------------------------------------

CREATE TABLE pembelian_header (
  id_pembelian    INT           AUTO_INCREMENT PRIMARY KEY,
  no_pembelian    VARCHAR(20)   NOT NULL UNIQUE,
  id_supplier     INT           NOT NULL,
  id_petugas      INT           NOT NULL,
  tgl_pesan       DATE          NOT NULL,
  tgl_terima      DATE,
  total_harga     DECIMAL(14,2) NOT NULL DEFAULT 0,
  status          ENUM('pending','diterima','dibatalkan') NOT NULL DEFAULT 'pending',
  keterangan      TEXT,
  created_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_ph_supplier FOREIGN KEY (id_supplier) REFERENCES supplier(id_supplier),
  CONSTRAINT fk_ph_petugas  FOREIGN KEY (id_petugas)  REFERENCES petugas(id_petugas)
) ENGINE=InnoDB;

CREATE TABLE pembelian_detail (
  id_detail       INT           AUTO_INCREMENT PRIMARY KEY,
  id_pembelian    INT           NOT NULL,
  id_obat         INT           NOT NULL,
  qty_pesan       INT           NOT NULL DEFAULT 0,
  qty_terima      INT           NOT NULL DEFAULT 0,
  harga_satuan    DECIMAL(12,2) NOT NULL DEFAULT 0,
  subtotal        DECIMAL(14,2) NOT NULL DEFAULT 0,
  tgl_kadaluarsa  DATE,
  no_batch        VARCHAR(50),
  CONSTRAINT fk_pd_pembelian FOREIGN KEY (id_pembelian) REFERENCES pembelian_header(id_pembelian) ON DELETE CASCADE,
  CONSTRAINT fk_pd_obat      FOREIGN KEY (id_obat)      REFERENCES obat(id_obat)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- TABEL RESEP
-- ------------------------------------------------------------

CREATE TABLE resep_header (
  id_resep          INT         AUTO_INCREMENT PRIMARY KEY,
  no_resep          VARCHAR(20) NOT NULL UNIQUE,
  id_pelanggan      INT         NOT NULL,
  id_petugas        INT         NOT NULL,
  nama_dokter       VARCHAR(150),
  asal_klinik       VARCHAR(150),
  tgl_resep         DATE        NOT NULL,
  status            ENUM('menunggu','diproses','selesai','dibatalkan') NOT NULL DEFAULT 'menunggu',
  created_at        DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_rh_pelanggan FOREIGN KEY (id_pelanggan) REFERENCES pelanggan(id_pelanggan),
  CONSTRAINT fk_rh_petugas   FOREIGN KEY (id_petugas)   REFERENCES petugas(id_petugas)
) ENGINE=InnoDB;

CREATE TABLE resep_detail (
  id_detail       INT           AUTO_INCREMENT PRIMARY KEY,
  id_resep        INT           NOT NULL,
  id_obat         INT           NOT NULL,
  qty             INT           NOT NULL DEFAULT 1,
  dosis           VARCHAR(50),
  aturan_pakai    VARCHAR(100),
  catatan         TEXT,
  CONSTRAINT fk_rd_resep FOREIGN KEY (id_resep) REFERENCES resep_header(id_resep) ON DELETE CASCADE,
  CONSTRAINT fk_rd_obat  FOREIGN KEY (id_obat)  REFERENCES obat(id_obat)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- TABEL TRANSAKSI PENJUALAN
-- ------------------------------------------------------------

CREATE TABLE penjualan_header (
  id_penjualan    INT           AUTO_INCREMENT PRIMARY KEY,
  no_penjualan    VARCHAR(20)   NOT NULL UNIQUE,
  id_pelanggan    INT,
  id_petugas      INT           NOT NULL,
  id_resep        INT,
  tgl_transaksi   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  subtotal        DECIMAL(14,2) NOT NULL DEFAULT 0,
  diskon          DECIMAL(14,2) NOT NULL DEFAULT 0,
  total_harga     DECIMAL(14,2) NOT NULL DEFAULT 0,
  uang_bayar      DECIMAL(14,2) NOT NULL DEFAULT 0,
  uang_kembali    DECIMAL(14,2) NOT NULL DEFAULT 0,
  metode_bayar    ENUM('tunai','transfer','bpjs','debit','kredit') NOT NULL DEFAULT 'tunai',
  status          ENUM('selesai','dibatalkan') NOT NULL DEFAULT 'selesai',
  created_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_sh_pelanggan FOREIGN KEY (id_pelanggan) REFERENCES pelanggan(id_pelanggan),
  CONSTRAINT fk_sh_petugas   FOREIGN KEY (id_petugas)   REFERENCES petugas(id_petugas),
  CONSTRAINT fk_sh_resep     FOREIGN KEY (id_resep)     REFERENCES resep_header(id_resep)
) ENGINE=InnoDB;

CREATE TABLE penjualan_detail (
  id_detail       INT           AUTO_INCREMENT PRIMARY KEY,
  id_penjualan    INT           NOT NULL,
  id_obat         INT           NOT NULL,
  qty             INT           NOT NULL DEFAULT 1,
  harga_satuan    DECIMAL(12,2) NOT NULL DEFAULT 0,
  diskon_pct      DECIMAL(5,2)  NOT NULL DEFAULT 0,
  subtotal        DECIMAL(14,2) NOT NULL DEFAULT 0,
  CONSTRAINT fk_sd_penjualan FOREIGN KEY (id_penjualan) REFERENCES penjualan_header(id_penjualan) ON DELETE CASCADE,
  CONSTRAINT fk_sd_obat      FOREIGN KEY (id_obat)      REFERENCES obat(id_obat)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- TABEL LOG STOK
-- ------------------------------------------------------------

CREATE TABLE log_stok (
  id_log            INT         AUTO_INCREMENT PRIMARY KEY,
  id_obat           INT         NOT NULL,
  id_petugas        INT,
  stok_sebelum      INT         NOT NULL DEFAULT 0,
  jumlah_perubahan  INT         NOT NULL DEFAULT 0,
  stok_sesudah      INT         NOT NULL DEFAULT 0,
  jenis_transaksi   ENUM('pembelian','penjualan','penyesuaian','return') NOT NULL,
  id_referensi      INT,
  tipe_referensi    VARCHAR(30),
  keterangan        TEXT,
  created_at        DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_ls_obat    FOREIGN KEY (id_obat)    REFERENCES obat(id_obat),
  CONSTRAINT fk_ls_petugas FOREIGN KEY (id_petugas) REFERENCES petugas(id_petugas)
) ENGINE=InnoDB;


-- ============================================================
-- VIEW
-- ============================================================

-- 1. Obat dengan stok menipis
CREATE OR REPLACE VIEW vw_stok_menipis AS
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
  o.harga_beli,
  o.lokasi_rak
FROM obat o
JOIN kategori k ON o.id_kategori = k.id_kategori
JOIN supplier s ON o.id_supplier = s.id_supplier
WHERE o.stok <= o.stok_minimum
  AND o.is_active = 1
ORDER BY kekurangan DESC;

-- 2. Obat mendekati / sudah kadaluarsa (dalam 90 hari ke depan)
CREATE OR REPLACE VIEW vw_obat_kadaluarsa AS
SELECT
  o.id_obat,
  o.kode_obat,
  o.nama_obat,
  k.nama_kategori,
  o.stok,
  o.satuan,
  o.tgl_kadaluarsa,
  DATEDIFF(o.tgl_kadaluarsa, CURDATE()) AS sisa_hari,
  CASE
    WHEN o.tgl_kadaluarsa < CURDATE()          THEN 'Kadaluarsa'
    WHEN DATEDIFF(o.tgl_kadaluarsa, CURDATE()) <= 30 THEN 'Kritis'
    WHEN DATEDIFF(o.tgl_kadaluarsa, CURDATE()) <= 90 THEN 'Perhatian'
    ELSE 'Aman'
  END AS status_kadaluarsa
FROM obat o
JOIN kategori k ON o.id_kategori = k.id_kategori
WHERE o.tgl_kadaluarsa IS NOT NULL
  AND o.tgl_kadaluarsa <= DATE_ADD(CURDATE(), INTERVAL 90 DAY)
  AND o.is_active = 1
ORDER BY o.tgl_kadaluarsa ASC;

-- 3. Rekap penjualan harian
CREATE OR REPLACE VIEW vw_penjualan_harian AS
SELECT
  DATE(ph.tgl_transaksi)    AS tanggal,
  COUNT(ph.id_penjualan)    AS jumlah_transaksi,
  SUM(ph.total_harga)       AS total_pendapatan,
  SUM(ph.diskon)            AS total_diskon,
  AVG(ph.total_harga)       AS rata_rata_transaksi,
  ph.metode_bayar,
  p.nama_petugas
FROM penjualan_header ph
JOIN petugas p ON ph.id_petugas = p.id_petugas
WHERE ph.status = 'selesai'
GROUP BY DATE(ph.tgl_transaksi), ph.metode_bayar, p.nama_petugas
ORDER BY tanggal DESC;

-- 4. Resep yang belum dilayani
CREATE OR REPLACE VIEW vw_resep_pending AS
SELECT
  rh.id_resep,
  rh.no_resep,
  pl.nama_pelanggan,
  pl.no_telepon,
  rh.nama_dokter,
  rh.asal_klinik,
  rh.tgl_resep,
  rh.status,
  COUNT(rd.id_detail)       AS jumlah_item,
  DATEDIFF(CURDATE(), rh.tgl_resep) AS hari_menunggu
FROM resep_header rh
JOIN pelanggan pl ON rh.id_pelanggan = pl.id_pelanggan
JOIN resep_detail rd ON rh.id_resep  = rd.id_resep
WHERE rh.status IN ('menunggu','diproses')
GROUP BY rh.id_resep
ORDER BY rh.tgl_resep ASC;


-- ============================================================
-- STORED PROCEDURE
-- ============================================================

