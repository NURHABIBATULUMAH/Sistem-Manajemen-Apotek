-- 1. Tambah kolom stok_sisa ke tabel pembelian_detail
ALTER TABLE pembelian_detail ADD stok_sisa INT DEFAULT 0;
GO

-- 2. Update data dummy agar memiliki stok_sisa dan tanggal kadaluarsa
-- Kita ambil data dari tabel obat agar sinkron
UPDATE pd
SET 
    pd.stok_sisa = pd.qty_terima,
    pd.tgl_kadaluarsa = o.tgl_kadaluarsa
FROM pembelian_detail pd
JOIN obat o ON pd.id_obat = o.id_obat;
GO

-- 3. Pastikan kolom is_active di tabel obat bernilai 1
UPDATE obat SET is_active = 1;
GO


--- munculin pelanggan 
USE apotek_db;
GO

-- 1. Tambahkan kolom is_active ke tabel pelanggan
ALTER TABLE pelanggan ADD is_active BIT DEFAULT 1;
GO

-- 2. Pastikan semua data dummy yang sudah ada diset menjadi aktif (1)
UPDATE pelanggan SET is_active = 1;
GO

--- riwayat pelanggan buat dummy doang foto
USE apotek_db;
GO

-- Tambah kolom foto ke tabel pelanggan (VARCHAR untuk menyimpan nama file)
ALTER TABLE pelanggan ADD foto VARCHAR(255) DEFAULT 'default_pelanggan.png';
GO

-- Pastikan semua data dummy pelanggan memiliki foto default
UPDATE pelanggan SET foto = 'default_pelanggan.png';
GO



--- nambah buat detail.php di dalam pelanggan + dummy tes
USE apotek_db;
GO

-- Menambahkan kolom yang kurang agar sesuai dengan tampilan Detail Profil
ALTER TABLE pelanggan ADD tgl_lahir DATE;
ALTER TABLE pelanggan ADD jenis_kelamin NVARCHAR(20);
ALTER TABLE pelanggan ADD email NVARCHAR(100);
GO

-- (Opsional) Mengisi data untuk pelanggan yang sudah ada agar tidak kosong
UPDATE pelanggan SET 
    tgl_lahir = '1995-01-01', 
    jenis_kelamin = 'Laki-laki', 
    email = 'pelanggan@example.com' 
WHERE kode_pelanggan = 'PLG001';
GO


--- tes hapus data dummy 
USE apotek_db;
GO

-- 1. Hapus tabel log/riwayat stok (Penyebab Error Msg 547)
DELETE FROM log_stok;

-- 2. Hapus detail transaksi
DELETE FROM penjualan_detail;
DELETE FROM resep_detail;
DELETE FROM pembelian_detail;

-- 3. Hapus header transaksi
DELETE FROM penjualan_header;
DELETE FROM resep_header;
DELETE FROM pembelian_header;

-- 4. Hapus data Master Obat
DELETE FROM obat;

-- 5. Reset Identity (Biar ID balik ke 1)
DBCC CHECKIDENT ('log_stok', RESEED, 0);
DBCC CHECKIDENT ('obat', RESEED, 0);
DBCC CHECKIDENT ('penjualan_header', RESEED, 0);
DBCC CHECKIDENT ('penjualan_detail', RESEED, 0);
DBCC CHECKIDENT ('resep_header', RESEED, 0);
DBCC CHECKIDENT ('resep_detail', RESEED, 0);
DBCC CHECKIDENT ('pembelian_header', RESEED, 0);
DBCC CHECKIDENT ('pembelian_detail', RESEED, 0);

GO


--- biar bisa isi dummy
USE apotek_db;
GO

-- Mengubah kolom id_kategori agar boleh kosong (NULL)
ALTER TABLE obat ALTER COLUMN id_kategori INT NULL;

-- Lakukan hal yang sama untuk id_satuan jika ada dan error juga
-- ALTER TABLE obat ALTER COLUMN id_satuan INT NULL; 

GO


--- biar muncul stok
USE apotek_db;
GO
ALTER TABLE pembelian_detail ADD stok_sisa INT;
GO