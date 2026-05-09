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
