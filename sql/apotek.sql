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

DELIMITER $$

-- 1. Tambah stok manual
CREATE PROCEDURE sp_tambah_stok(
  IN  p_id_obat         INT,
  IN  p_jumlah          INT,
  IN  p_id_petugas      INT,
  IN  p_keterangan      TEXT,
  OUT p_stok_sesudah    INT,
  OUT p_pesan           VARCHAR(255)
)
BEGIN
  DECLARE v_stok_sebelum INT DEFAULT 0;

  SELECT stok INTO v_stok_sebelum FROM obat WHERE id_obat = p_id_obat;

  IF v_stok_sebelum IS NULL THEN
    SET p_pesan = 'ERROR: Obat tidak ditemukan';
    SET p_stok_sesudah = 0;
  ELSEIF p_jumlah <= 0 THEN
    SET p_pesan = 'ERROR: Jumlah harus lebih dari 0';
    SET p_stok_sesudah = v_stok_sebelum;
  ELSE
    UPDATE obat SET stok = stok + p_jumlah WHERE id_obat = p_id_obat;
    SET p_stok_sesudah = v_stok_sebelum + p_jumlah;

    INSERT INTO log_stok (id_obat, id_petugas, stok_sebelum, jumlah_perubahan, stok_sesudah, jenis_transaksi, keterangan)
    VALUES (p_id_obat, p_id_petugas, v_stok_sebelum, p_jumlah, p_stok_sesudah, 'penyesuaian', p_keterangan);

    SET p_pesan = CONCAT('Stok berhasil ditambah. Stok sekarang: ', p_stok_sesudah);
  END IF;
END$$

-- 2. Proses penerimaan pembelian (update status + tambah stok)
CREATE PROCEDURE sp_terima_pembelian(
  IN  p_id_pembelian  INT,
  IN  p_id_petugas    INT,
  OUT p_pesan         VARCHAR(255)
)
BEGIN
  DECLARE v_status    VARCHAR(20);
  DECLARE v_done      INT DEFAULT 0;
  DECLARE v_id_obat   INT;
  DECLARE v_qty       INT;
  DECLARE v_stok_sblm INT;

  DECLARE cur_detail CURSOR FOR
    SELECT id_obat, qty_pesan FROM pembelian_detail WHERE id_pembelian = p_id_pembelian;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_done = 1;

  SELECT status INTO v_status FROM pembelian_header WHERE id_pembelian = p_id_pembelian;

  IF v_status IS NULL THEN
    SET p_pesan = 'ERROR: Nomor pembelian tidak ditemukan';
  ELSEIF v_status != 'pending' THEN
    SET p_pesan = CONCAT('ERROR: Status pembelian sudah ', v_status);
  ELSE
    START TRANSACTION;

    OPEN cur_detail;
    loop_detail: LOOP
      FETCH cur_detail INTO v_id_obat, v_qty;
      IF v_done = 1 THEN LEAVE loop_detail; END IF;

      SELECT stok INTO v_stok_sblm FROM obat WHERE id_obat = v_id_obat;

      UPDATE obat SET stok = stok + v_qty WHERE id_obat = v_id_obat;

      INSERT INTO log_stok (id_obat, id_petugas, stok_sebelum, jumlah_perubahan, stok_sesudah,
                            jenis_transaksi, id_referensi, tipe_referensi, keterangan)
      VALUES (v_id_obat, p_id_petugas, v_stok_sblm, v_qty, v_stok_sblm + v_qty,
              'pembelian', p_id_pembelian, 'pembelian_header', 'Penerimaan pembelian');
    END LOOP;
    CLOSE cur_detail;

    UPDATE pembelian_header
    SET status = 'diterima', tgl_terima = CURDATE()
    WHERE id_pembelian = p_id_pembelian;

    COMMIT;
    SET p_pesan = 'Pembelian berhasil diterima dan stok diperbarui';
  END IF;
END$$

-- 3. Proses penjualan obat
CREATE PROCEDURE sp_buat_penjualan(
  IN  p_id_pelanggan  INT,
  IN  p_id_petugas    INT,
  IN  p_id_resep      INT,
  IN  p_metode_bayar  VARCHAR(20),
  IN  p_uang_bayar    DECIMAL(14,2),
  OUT p_id_penjualan  INT,
  OUT p_no_penjualan  VARCHAR(20),
  OUT p_pesan         VARCHAR(255)
)
BEGIN
  DECLARE v_no VARCHAR(20);
  DECLARE v_total DECIMAL(14,2) DEFAULT 0;

  SET v_no = CONCAT('SL', DATE_FORMAT(NOW(), '%Y%m%d'), LPAD(FLOOR(RAND()*9999), 4, '0'));

  INSERT INTO penjualan_header (no_penjualan, id_pelanggan, id_petugas, id_resep,
                                 metode_bayar, uang_bayar, status)
  VALUES (v_no, p_id_pelanggan, p_id_petugas, p_id_resep,
          p_metode_bayar, p_uang_bayar, 'selesai');

  SET p_id_penjualan = LAST_INSERT_ID();
  SET p_no_penjualan = v_no;
  SET p_pesan = CONCAT('Transaksi berhasil. No: ', v_no);
END$$

-- 4. Laporan rekap penjualan per obat (menggunakan cursor)
CREATE PROCEDURE sp_rekap_penjualan_obat(
  IN p_tgl_awal  DATE,
  IN p_tgl_akhir DATE
)
BEGIN
  -- 1. DECLARE variabel dulu
  DECLARE v_done        INT DEFAULT 0;
  DECLARE v_id_obat     INT;
  DECLARE v_nama_obat   VARCHAR(150);
  DECLARE v_total_qty   INT;
  DECLARE v_total_nilai DECIMAL(14,2);

  -- 2. DECLARE cursor
  DECLARE cur_obat CURSOR FOR
    SELECT
      o.id_obat,
      o.nama_obat,
      COALESCE(SUM(sd.qty), 0)       AS total_qty,
      COALESCE(SUM(sd.subtotal), 0)  AS total_nilai
    FROM obat o
    LEFT JOIN penjualan_detail sd ON o.id_obat = sd.id_obat
    LEFT JOIN penjualan_header sh ON sd.id_penjualan = sh.id_penjualan
      AND sh.status = 'selesai'
      AND DATE(sh.tgl_transaksi) BETWEEN p_tgl_awal AND p_tgl_akhir
    WHERE o.is_active = 1
    GROUP BY o.id_obat, o.nama_obat;

  -- 3. DECLARE handler
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_done = 1;

  -- 4. Baru statement biasa
  DROP TEMPORARY TABLE IF EXISTS tmp_rekap;
  CREATE TEMPORARY TABLE tmp_rekap (
    id_obat     INT,
    nama_obat   VARCHAR(150),
    total_qty   INT,
    total_nilai DECIMAL(14,2)
  );

  OPEN cur_obat;
  loop_obat: LOOP
    FETCH cur_obat INTO v_id_obat, v_nama_obat, v_total_qty, v_total_nilai;
    IF v_done = 1 THEN LEAVE loop_obat; END IF;
    INSERT INTO tmp_rekap VALUES (v_id_obat, v_nama_obat, v_total_qty, v_total_nilai);
  END LOOP;
  CLOSE cur_obat;

  SELECT * FROM tmp_rekap ORDER BY total_nilai DESC;
  DROP TEMPORARY TABLE tmp_rekap;
END$$

-- 5. Tandai semua obat kadaluarsa (cursor massal)
CREATE PROCEDURE sp_proses_kadaluarsa()
BEGIN
  DECLARE v_done        INT DEFAULT 0;
  DECLARE v_id_obat     INT;
  DECLARE v_nama_obat   VARCHAR(150);
  DECLARE v_tgl_exp     DATE;
  DECLARE v_jumlah      INT DEFAULT 0;

  DECLARE cur_exp CURSOR FOR
    SELECT id_obat, nama_obat, tgl_kadaluarsa
    FROM obat
    WHERE tgl_kadaluarsa < CURDATE()
      AND stok > 0
      AND is_active = 1;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_done = 1;

  OPEN cur_exp;
  loop_exp: LOOP
    FETCH cur_exp INTO v_id_obat, v_nama_obat, v_tgl_exp;
    IF v_done = 1 THEN LEAVE loop_exp; END IF;

    UPDATE obat SET is_active = 0 WHERE id_obat = v_id_obat;

    INSERT INTO log_stok (id_obat, stok_sebelum, jumlah_perubahan, stok_sesudah,
                           jenis_transaksi, keterangan)
    SELECT stok, stok * -1, 0, 'penyesuaian',
           CONCAT('Nonaktif - kadaluarsa sejak ', v_tgl_exp)
    FROM obat WHERE id_obat = v_id_obat;

    SET v_jumlah = v_jumlah + 1;
  END LOOP;
  CLOSE cur_exp;

  SELECT CONCAT(v_jumlah, ' obat kadaluarsa telah dinonaktifkan') AS hasil;
END$$

DELIMITER ;


-- ============================================================
-- TRIGGER
-- ============================================================

DELIMITER $$

-- 1. Kurangi stok otomatis saat penjualan_detail di-INSERT
CREATE TRIGGER trg_kurangi_stok_penjualan
AFTER INSERT ON penjualan_detail
FOR EACH ROW
BEGIN
  DECLARE v_stok_sblm INT;
  DECLARE v_id_pet    INT;

  SELECT stok INTO v_stok_sblm FROM obat WHERE id_obat = NEW.id_obat;
  SELECT id_petugas INTO v_id_pet FROM penjualan_header WHERE id_penjualan = NEW.id_penjualan;

  UPDATE obat SET stok = stok - NEW.qty WHERE id_obat = NEW.id_obat;

  INSERT INTO log_stok (id_obat, id_petugas, stok_sebelum, jumlah_perubahan, stok_sesudah,
                         jenis_transaksi, id_referensi, tipe_referensi, keterangan)
  VALUES (NEW.id_obat, v_id_pet, v_stok_sblm, NEW.qty * -1, v_stok_sblm - NEW.qty,
          'penjualan', NEW.id_penjualan, 'penjualan_header', 'Penjualan obat');
END$$

-- 2. Kembalikan stok jika penjualan dibatalkan
CREATE TRIGGER trg_batal_penjualan
AFTER UPDATE ON penjualan_header
FOR EACH ROW
BEGIN
  IF NEW.status = 'dibatalkan' AND OLD.status = 'selesai' THEN
    UPDATE obat o
    JOIN penjualan_detail sd ON o.id_obat = sd.id_obat
    SET o.stok = o.stok + sd.qty
    WHERE sd.id_penjualan = NEW.id_penjualan;
  END IF;
END$$

-- 3. Update total_harga penjualan_header otomatis
CREATE TRIGGER trg_update_total_penjualan
AFTER INSERT ON penjualan_detail
FOR EACH ROW
BEGIN
  UPDATE penjualan_header
  SET
    subtotal     = (SELECT COALESCE(SUM(subtotal), 0) FROM penjualan_detail WHERE id_penjualan = NEW.id_penjualan),
    total_harga  = subtotal - diskon,
    uang_kembali = uang_bayar - (subtotal - diskon)
  WHERE id_penjualan = NEW.id_penjualan;
END$$

-- 4. Update total_harga pembelian_header otomatis
CREATE TRIGGER trg_update_total_pembelian
AFTER INSERT ON pembelian_detail
FOR EACH ROW
BEGIN
  UPDATE pembelian_header
  SET total_harga = (
    SELECT COALESCE(SUM(subtotal), 0)
    FROM pembelian_detail
    WHERE id_pembelian = NEW.id_pembelian
  )
  WHERE id_pembelian = NEW.id_pembelian;
END$$

-- 5. Update status resep saat penjualan dibuat
CREATE TRIGGER trg_update_status_resep
AFTER INSERT ON penjualan_header
FOR EACH ROW
BEGIN
  IF NEW.id_resep IS NOT NULL THEN
    UPDATE resep_header SET status = 'selesai' WHERE id_resep = NEW.id_resep;
  END IF;
END$$

DELIMITER ;


-- ============================================================
-- DUMMY DATA
-- ============================================================

INSERT INTO kategori (kode_kategori, nama_kategori, deskripsi, jenis_obat) VALUES
('KAT001', 'Analgesik',       'Obat pereda nyeri',            'bebas'),
('KAT002', 'Antibiotik',      'Obat melawan bakteri',         'keras'),
('KAT003', 'Vitamin',         'Suplemen vitamin dan mineral', 'bebas'),
('KAT004', 'Antasida',        'Obat gangguan lambung',        'bebas'),
('KAT005', 'Antihipertensi',  'Obat tekanan darah tinggi',    'keras');

INSERT INTO supplier (kode_supplier, nama_supplier, alamat, no_telepon, email, contact_person) VALUES
('SUP001', 'PT Kimia Farma',      'Jl. Veteran No.9, Jakarta',       '02140000001', 'cs@kimiafarma.co.id',  'Budi Santoso'),
('SUP002', 'PT Kalbe Farma',      'Jl. Let.Jend.Suprapto No.4, Jakarta', '02140000002', 'cs@kalbe.co.id',  'Siti Rahayu'),
('SUP003', 'PT Dexa Medica',      'Jl. Palembang-Prabumulih, Palembang', '071140000003', 'cs@dexa.co.id',  'Ahmad Fauzi');

INSERT INTO petugas (kode_petugas, nama_petugas, username, password_hash, role, no_telepon) VALUES
('PTG001', 'Admin Apotek',       'admin',    '0192023a7bbd73250516f069df18b500', 'admin',     '081234567001'),
('PTG002', 'Drs. Rina Apoteker', 'apoteker', '6814066e4388d49866348536288ee450', 'apoteker',  '081234567002'),
('PTG003', 'Kasir Satu',         'kasir1',   'de28f8f7998f23ab4194b51a6029416f', 'kasir',     '081234567003');
-- password: admin=admin123 | apoteker=apotek123 | kasir1=kasir123

INSERT INTO pelanggan (kode_pelanggan, nama_pelanggan, no_telepon, alamat, no_bpjs, jenis_pelanggan) VALUES
('PLG001', 'Umum / Tanpa Nama', '-',             '-',                        NULL,              'umum'),
('PLG002', 'Budi Hartono',      '08111111111',   'Jl. Melati No.5, Gresik',  NULL,              'umum'),
('PLG003', 'Sari Dewi',         '08222222222',   'Jl. Mawar No.3, Gresik',   '0001234567890',   'bpjs'),
('PLG004', 'Ahmad Fauzi',       '08333333333',   'Jl. Kenanga No.7, Gresik', NULL,              'umum');

INSERT INTO obat (kode_obat, nama_obat, id_kategori, id_supplier, satuan, stok, stok_minimum, harga_beli, harga_jual, tgl_kadaluarsa, lokasi_rak) VALUES
('OBT001', 'Paracetamol 500mg',     1, 1, 'strip', 150, 20, 1500.00,  3000.00,  '2026-12-31', 'A1'),
('OBT002', 'Amoxicillin 500mg',     2, 2, 'kapsul', 80, 15, 3000.00,  6000.00,  '2026-06-30', 'B2'),
('OBT003', 'Vitamin C 500mg',       3, 1, 'tablet', 200,30, 1000.00,  2500.00,  '2027-03-31', 'A2'),
('OBT004', 'Antasida Doen',         4, 3, 'tablet', 5,  20, 2000.00,  4500.00,  '2025-11-30', 'C1'),
('OBT005', 'Amlodipine 5mg',        5, 2, 'tablet', 60, 10, 5000.00,  10000.00, '2026-09-30', 'B3'),
('OBT006', 'Ibuprofen 400mg',       1, 1, 'tablet', 8,  15, 2000.00,  5000.00,  '2026-08-31', 'A3'),
('OBT007', 'Vitamin B Complex',     3, 3, 'tablet', 120,25, 1500.00,  4000.00,  '2027-01-31', 'A4'),
('OBT008', 'Omeprazole 20mg',       4, 2, 'kapsul', 45, 10, 4000.00,  8000.00,  '2026-10-31', 'C2');

INSERT INTO pembelian_header (no_pembelian, id_supplier, id_petugas, tgl_pesan, status) VALUES
('PB20240101001', 1, 1, '2024-01-01', 'diterima'),
('PB20240201001', 2, 1, '2024-02-01', 'diterima');

INSERT INTO pembelian_detail (id_pembelian, id_obat, qty_pesan, qty_terima, harga_satuan, subtotal) VALUES
(1, 1, 100, 100, 1500.00, 150000.00),
(1, 3, 150, 150, 1000.00, 150000.00),
(2, 2,  50,  50, 3000.00, 150000.00),
(2, 5,  60,  60, 5000.00, 300000.00);

UPDATE pembelian_header SET total_harga = 300000.00 WHERE id_pembelian = 1;
UPDATE pembelian_header SET total_harga = 450000.00 WHERE id_pembelian = 2;

INSERT INTO resep_header (no_resep, id_pelanggan, id_petugas, nama_dokter, asal_klinik, tgl_resep, status) VALUES
('RS20240301001', 3, 2, 'dr. Hendro Sp.PD', 'Klinik Sehat Gresik', '2024-03-01', 'selesai'),
('RS20240601001', 4, 2, 'dr. Andi Sp.Um',   'Puskesmas Gresik',    '2024-06-01', 'menunggu');

INSERT INTO resep_detail (id_resep, id_obat, qty, dosis, aturan_pakai) VALUES
(1, 1, 10, '500mg', '3x1 sesudah makan'),
(1, 5,  7, '5mg',   '1x1 pagi hari'),
(2, 2,  6, '500mg', '3x1 habiskan');
