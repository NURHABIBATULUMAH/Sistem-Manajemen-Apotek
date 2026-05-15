-- ============================================================
-- SISTEM MANAJEMEN APOTEK - SQL SERVER
-- File        : 2_dummy_data.sql
-- Keterangan  : Insert data awal / dummy data
-- ============================================================

USE apotek_db;
GO

-- ============================================================
-- KATEGORI OBAT
-- ============================================================
INSERT INTO kategori (kode_kategori, nama_kategori, deskripsi, jenis_obat) VALUES
('KAT001', 'Analgesik',       'Obat pereda nyeri dan demam',      'bebas'),
('KAT002', 'Antibiotik',      'Obat melawan infeksi bakteri',     'keras'),
('KAT003', 'Vitamin',         'Suplemen vitamin dan mineral',     'bebas'),
('KAT004', 'Antasida',        'Obat gangguan lambung dan maag',   'bebas'),
('KAT005', 'Antihipertensi',  'Obat tekanan darah tinggi',        'keras');
GO

-- ============================================================
-- SUPPLIER
-- ============================================================
INSERT INTO supplier (kode_supplier, nama_supplier, alamat, no_telepon, email, contact_person) VALUES
('SUP001', 'PT Kimia Farma',   'Jl. Veteran No.9, Jakarta',            '02140000001', 'cs@kimiafarma.co.id', 'Budi Santoso'),
('SUP002', 'PT Kalbe Farma',   'Jl. Let.Jend.Suprapto No.4, Jakarta',  '02140000002', 'cs@kalbe.co.id',      'Siti Rahayu'),
('SUP003', 'PT Dexa Medica',   'Jl. Palembang-Prabumulih, Palembang',  '07114000003', 'cs@dexa.co.id',       'Ahmad Fauzi');
GO

-- ============================================================
-- PETUGAS
-- Password: admin=admin123 | apoteker=apotek123 | kasir1=kasir123
-- Hash menggunakan MD5 via HASHBYTES
-- ============================================================
INSERT INTO petugas (kode_petugas, nama_petugas, username, password_hash, role, no_telepon) VALUES
('PTG001', 'Admin Apotek',       'admin',    '0192023a7bbd73250516f069df18b500', 'admin',    '081234567001'),
('PTG002', 'Drs. Rina Apoteker', 'apoteker', '6814066e4388d49866348536288ee450', 'apoteker', '081234567002'),
('PTG003', 'Kasir Satu',         'kasir1',   'de28f8f7998f23ab4194b51a6029416f', 'kasir',    '081234567003');
GO

-- ============================================================
-- PELANGGAN
-- ============================================================
INSERT INTO pelanggan (kode_pelanggan, nama_pelanggan, no_telepon, alamat, no_bpjs, jenis_pelanggan) VALUES
('PLG001', 'Umum / Tanpa Nama', '-',            '-',                       NULL,             'umum'),
('PLG002', 'Budi Hartono',      '08111111111',  'Jl. Melati No.5, Gresik', NULL,             'umum'),
('PLG003', 'Sari Dewi',         '08222222222',  'Jl. Mawar No.3, Gresik',  '0001234567890',  'bpjs'),
('PLG004', 'Ahmad Fauzi',       '08333333333',  'Jl. Kenanga No.7, Gresik',NULL,             'umum');
GO

-- ============================================================
-- OBAT
-- ============================================================
INSERT INTO obat (kode_obat, nama_obat, id_kategori, id_supplier, satuan, stok, stok_minimum, harga_beli, harga_jual, tgl_kadaluarsa, lokasi_rak) VALUES
('OBT001', 'Paracetamol 500mg',   1, 1, 'strip',  150, 20,  1500.00,  3000.00, '2026-12-31', 'A1'),
('OBT002', 'Amoxicillin 500mg',   2, 2, 'kapsul',  80, 15,  3000.00,  6000.00, '2026-06-30', 'B2'),
('OBT003', 'Vitamin C 500mg',     3, 1, 'tablet', 200, 30,  1000.00,  2500.00, '2027-03-31', 'A2'),
('OBT004', 'Antasida Doen',       4, 3, 'tablet',   5, 20,  2000.00,  4500.00, '2025-11-30', 'C1'),
('OBT005', 'Amlodipine 5mg',      5, 2, 'tablet',  60, 10,  5000.00, 10000.00, '2026-09-30', 'B3'),
('OBT006', 'Ibuprofen 400mg',     1, 1, 'tablet',   8, 15,  2000.00,  5000.00, '2026-08-31', 'A3'),
('OBT007', 'Vitamin B Complex',   3, 3, 'tablet', 120, 25,  1500.00,  4000.00, '2027-01-31', 'A4'),
('OBT008', 'Omeprazole 20mg',     4, 2, 'kapsul',  45, 10,  4000.00,  8000.00, '2026-10-31', 'C2');
GO

-- ============================================================
-- PEMBELIAN
-- ============================================================
INSERT INTO pembelian_header (no_pembelian, id_supplier, id_petugas, tgl_pesan, total_harga, status) VALUES
('PB20240101001', 1, 1, '2024-01-01', 300000.00, 'diterima'),
('PB20240201001', 2, 1, '2024-02-01', 450000.00, 'diterima');
GO

INSERT INTO pembelian_detail (id_pembelian, id_obat, qty_pesan, qty_terima, harga_satuan, subtotal) VALUES
(1, 1, 100, 100, 1500.00, 150000.00),
(1, 3, 150, 150, 1000.00, 150000.00),
(2, 2,  50,  50, 3000.00, 150000.00),
(2, 5,  60,  60, 5000.00, 300000.00);
GO

-- ============================================================
-- RESEP
-- ============================================================
INSERT INTO resep_header (no_resep, id_pelanggan, id_petugas, nama_dokter, asal_klinik, tgl_resep, status) VALUES
('RS20240301001', 3, 2, 'dr. Hendro Sp.PD', 'Klinik Sehat Gresik', '2024-03-01', 'selesai'),
('RS20240601001', 4, 2, 'dr. Andi Sp.Um',   'Puskesmas Gresik',    '2024-06-01', 'menunggu');
GO

INSERT INTO resep_detail (id_resep, id_obat, qty, dosis, aturan_pakai) VALUES
(1, 1, 10, '500mg', '3x1 sesudah makan'),
(1, 5,  7, '5mg',   '1x1 pagi hari'),
(2, 2,  6, '500mg', '3x1 habiskan');
GO

PRINT 'Dummy data berhasil diinsert.';
GO
