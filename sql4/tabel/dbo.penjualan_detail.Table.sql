USE [apotek_db];
GO

/****** Object:  Table [dbo].[penjualan_detail]    Script Date: 6/10/2026 7:40:39 AM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[penjualan_detail](
	[id_detail] [int] IDENTITY(1,1) NOT NULL,
	[id_penjualan] [int] NOT NULL,
	[id_obat] [int] NOT NULL,
	[qty] [int] NOT NULL,
	[harga_satuan] [decimal](12, 2) NOT NULL,
	[diskon_pct] [decimal](5, 2) NOT NULL,
	[subtotal] [decimal](14, 2) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id_detail] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
SET IDENTITY_INSERT [dbo].[kategori] ON 

INSERT [dbo].[kategori] ([id_kategori], [kode_kategori], [nama_kategori], [deskripsi], [jenis_obat], [is_active], [created_at]) VALUES (1, N'KAT001', N'Analgesik', N'Obat pereda nyeri dan demam', N'bebas', 1, CAST(N'2026-05-16T17:28:18.527' AS DateTime))
INSERT [dbo].[kategori] ([id_kategori], [kode_kategori], [nama_kategori], [deskripsi], [jenis_obat], [is_active], [created_at]) VALUES (2, N'KAT002', N'Antibiotik', N'Obat melawan infeksi bakteri', N'keras', 1, CAST(N'2026-05-16T17:28:18.527' AS DateTime))
INSERT [dbo].[kategori] ([id_kategori], [kode_kategori], [nama_kategori], [deskripsi], [jenis_obat], [is_active], [created_at]) VALUES (3, N'KAT003', N'Vitamin', N'Suplemen vitamin dan mineral', N'bebas', 1, CAST(N'2026-05-16T17:28:18.527' AS DateTime))
INSERT [dbo].[kategori] ([id_kategori], [kode_kategori], [nama_kategori], [deskripsi], [jenis_obat], [is_active], [created_at]) VALUES (4, N'KAT004', N'Antasida', N'Obat gangguan lambung dan maag', N'bebas', 1, CAST(N'2026-05-16T17:28:18.527' AS DateTime))
INSERT [dbo].[kategori] ([id_kategori], [kode_kategori], [nama_kategori], [deskripsi], [jenis_obat], [is_active], [created_at]) VALUES (5, N'KAT005', N'Antihipertensi', N'Obat tekanan darah tinggi', N'keras', 1, CAST(N'2026-05-16T17:28:18.527' AS DateTime))
INSERT [dbo].[kategori] ([id_kategori], [kode_kategori], [nama_kategori], [deskripsi], [jenis_obat], [is_active], [created_at]) VALUES (6, N'K001', N'Analgesik', N'Obat Pereda Nyeri', N'bebas', 1, CAST(N'2026-06-10T00:38:19.543' AS DateTime))
INSERT [dbo].[kategori] ([id_kategori], [kode_kategori], [nama_kategori], [deskripsi], [jenis_obat], [is_active], [created_at]) VALUES (7, N'K002', N'Antibiotik', N'Obat Pembunuh Bakteri', N'keras', 1, CAST(N'2026-06-10T00:38:19.543' AS DateTime))
SET IDENTITY_INSERT [dbo].[kategori] OFF
GO
SET IDENTITY_INSERT [dbo].[log_stok] ON 

INSERT [dbo].[log_stok] ([id_log], [id_obat], [id_petugas], [stok_sebelum], [jumlah_perubahan], [stok_sesudah], [jenis_transaksi], [keterangan], [created_at]) VALUES (3, 24, 1, 5, 50, 55, N'pembelian', N'Penerimaan pembelian', CAST(N'2026-06-10T00:42:01.240' AS DateTime))
INSERT [dbo].[log_stok] ([id_log], [id_obat], [id_petugas], [stok_sebelum], [jumlah_perubahan], [stok_sesudah], [jenis_transaksi], [keterangan], [created_at]) VALUES (4, 23, 1, 50, -10, 40, N'penjualan', N'Penjualan obat', CAST(N'2026-06-10T00:42:16.980' AS DateTime))
SET IDENTITY_INSERT [dbo].[log_stok] OFF
GO
SET IDENTITY_INSERT [dbo].[obat] ON 

INSERT [dbo].[obat] ([id_obat], [kode_obat], [nama_obat], [id_kategori], [id_supplier], [satuan], [stok], [stok_minimum], [harga_beli], [harga_jual], [tgl_kadaluarsa], [is_active], [created_at], [updated_at]) VALUES (5, N'OBT-001', N'Paracetamol 500mg', NULL, NULL, N'pcs', 11, 10, CAST(5000.00 AS Decimal(12, 2)), CAST(7500.00 AS Decimal(12, 2)), NULL, 1, CAST(N'2026-05-17T00:19:54.810' AS DateTime), CAST(N'2026-05-17T01:28:24.107' AS DateTime))
INSERT [dbo].[obat] ([id_obat], [kode_obat], [nama_obat], [id_kategori], [id_supplier], [satuan], [stok], [stok_minimum], [harga_beli], [harga_jual], [tgl_kadaluarsa], [is_active], [created_at], [updated_at]) VALUES (6, N'OBT-002', N'Amoxicillin 500mg', NULL, NULL, N'pcs', 0, 10, CAST(12000.00 AS Decimal(12, 2)), CAST(15000.00 AS Decimal(12, 2)), NULL, 1, CAST(N'2026-05-17T00:19:54.810' AS DateTime), CAST(N'2026-05-17T00:19:54.810' AS DateTime))
INSERT [dbo].[obat] ([id_obat], [kode_obat], [nama_obat], [id_kategori], [id_supplier], [satuan], [stok], [stok_minimum], [harga_beli], [harga_jual], [tgl_kadaluarsa], [is_active], [created_at], [updated_at]) VALUES (7, N'OBT-003', N'Ibuprofen 400mg', NULL, NULL, N'pcs', 0, 10, CAST(8000.00 AS Decimal(12, 2)), CAST(11000.00 AS Decimal(12, 2)), NULL, 1, CAST(N'2026-05-17T00:19:54.810' AS DateTime), CAST(N'2026-05-17T00:19:54.810' AS DateTime))
INSERT [dbo].[obat] ([id_obat], [kode_obat], [nama_obat], [id_kategori], [id_supplier], [satuan], [stok], [stok_minimum], [harga_beli], [harga_jual], [tgl_kadaluarsa], [is_active], [created_at], [updated_at]) VALUES (8, N'OBT-004', N'OBH Combi Sirup', NULL, NULL, N'pcs', 0, 10, CAST(18000.00 AS Decimal(12, 2)), CAST(22500.00 AS Decimal(12, 2)), NULL, 1, CAST(N'2026-05-17T00:19:54.810' AS DateTime), CAST(N'2026-05-17T00:19:54.810' AS DateTime))
INSERT [dbo].[obat] ([id_obat], [kode_obat], [nama_obat], [id_kategori], [id_supplier], [satuan], [stok], [stok_minimum], [harga_beli], [harga_jual], [tgl_kadaluarsa], [is_active], [created_at], [updated_at]) VALUES (9, N'OBT-005', N'Antasida Doen', NULL, NULL, N'pcs', 0, 10, CAST(4000.00 AS Decimal(12, 2)), CAST(6000.00 AS Decimal(12, 2)), NULL, 1, CAST(N'2026-05-17T00:19:54.810' AS DateTime), CAST(N'2026-05-17T00:19:54.810' AS DateTime))
INSERT [dbo].[obat] ([id_obat], [kode_obat], [nama_obat], [id_kategori], [id_supplier], [satuan], [stok], [stok_minimum], [harga_beli], [harga_jual], [tgl_kadaluarsa], [is_active], [created_at], [updated_at]) VALUES (10, N'OBT-006', N'Betadine Solution', NULL, NULL, N'pcs', 0, 10, CAST(15000.00 AS Decimal(12, 2)), CAST(18500.00 AS Decimal(12, 2)), NULL, 1, CAST(N'2026-05-17T00:19:54.810' AS DateTime), CAST(N'2026-05-17T00:19:54.810' AS DateTime))
INSERT [dbo].[obat] ([id_obat], [kode_obat], [nama_obat], [id_kategori], [id_supplier], [satuan], [stok], [stok_minimum], [harga_beli], [harga_jual], [tgl_kadaluarsa], [is_active], [created_at], [updated_at]) VALUES (11, N'OBT-007', N'Sangobion Kapsul', NULL, NULL, N'pcs', 0, 10, CAST(14000.00 AS Decimal(12, 2)), CAST(18000.00 AS Decimal(12, 2)), NULL, 1, CAST(N'2026-05-17T00:19:54.810' AS DateTime), CAST(N'2026-05-17T00:19:54.810' AS DateTime))
INSERT [dbo].[obat] ([id_obat], [kode_obat], [nama_obat], [id_kategori], [id_supplier], [satuan], [stok], [stok_minimum], [harga_beli], [harga_jual], [tgl_kadaluarsa], [is_active], [created_at], [updated_at]) VALUES (12, N'OBT-008', N'Counterpain Salep', NULL, NULL, N'pcs', 0, 10, CAST(25000.00 AS Decimal(12, 2)), CAST(32000.00 AS Decimal(12, 2)), NULL, 1, CAST(N'2026-05-17T00:19:54.810' AS DateTime), CAST(N'2026-05-17T00:19:54.810' AS DateTime))
INSERT [dbo].[obat] ([id_obat], [kode_obat], [nama_obat], [id_kategori], [id_supplier], [satuan], [stok], [stok_minimum], [harga_beli], [harga_jual], [tgl_kadaluarsa], [is_active], [created_at], [updated_at]) VALUES (13, N'OBT-010', N'Promag Tablet', NULL, NULL, N'pcs', 0, 10, CAST(7500.00 AS Decimal(12, 2)), CAST(9500.00 AS Decimal(12, 2)), NULL, 1, CAST(N'2026-05-17T00:19:54.810' AS DateTime), CAST(N'2026-05-17T00:19:54.810' AS DateTime))
INSERT [dbo].[obat] ([id_obat], [kode_obat], [nama_obat], [id_kategori], [id_supplier], [satuan], [stok], [stok_minimum], [harga_beli], [harga_jual], [tgl_kadaluarsa], [is_active], [created_at], [updated_at]) VALUES (14, N'OBT-011', N'Decolgen Flu', NULL, NULL, N'pcs', 0, 10, CAST(6000.00 AS Decimal(12, 2)), CAST(8000.00 AS Decimal(12, 2)), NULL, 1, CAST(N'2026-05-17T00:19:54.810' AS DateTime), CAST(N'2026-05-17T00:19:54.810' AS DateTime))
INSERT [dbo].[obat] ([id_obat], [kode_obat], [nama_obat], [id_kategori], [id_supplier], [satuan], [stok], [stok_minimum], [harga_beli], [harga_jual], [tgl_kadaluarsa], [is_active], [created_at], [updated_at]) VALUES (15, N'OBT-012', N'Panadol Merah', NULL, NULL, N'pcs', 0, 10, CAST(11000.00 AS Decimal(12, 2)), CAST(13500.00 AS Decimal(12, 2)), NULL, 1, CAST(N'2026-05-17T00:19:54.810' AS DateTime), CAST(N'2026-05-17T00:19:54.810' AS DateTime))
INSERT [dbo].[obat] ([id_obat], [kode_obat], [nama_obat], [id_kategori], [id_supplier], [satuan], [stok], [stok_minimum], [harga_beli], [harga_jual], [tgl_kadaluarsa], [is_active], [created_at], [updated_at]) VALUES (16, N'OBT-013', N'Insto Eye Drops', NULL, NULL, N'pcs', 0, 10, CAST(12500.00 AS Decimal(12, 2)), CAST(16000.00 AS Decimal(12, 2)), NULL, 1, CAST(N'2026-05-17T00:19:54.810' AS DateTime), CAST(N'2026-05-17T00:19:54.810' AS DateTime))
INSERT [dbo].[obat] ([id_obat], [kode_obat], [nama_obat], [id_kategori], [id_supplier], [satuan], [stok], [stok_minimum], [harga_beli], [harga_jual], [tgl_kadaluarsa], [is_active], [created_at], [updated_at]) VALUES (17, N'OBT-014', N'Vicks Formula 44', NULL, NULL, N'pcs', 0, 10, CAST(21000.00 AS Decimal(12, 2)), CAST(26000.00 AS Decimal(12, 2)), NULL, 1, CAST(N'2026-05-17T00:19:54.810' AS DateTime), CAST(N'2026-05-17T00:19:54.810' AS DateTime))
INSERT [dbo].[obat] ([id_obat], [kode_obat], [nama_obat], [id_kategori], [id_supplier], [satuan], [stok], [stok_minimum], [harga_beli], [harga_jual], [tgl_kadaluarsa], [is_active], [created_at], [updated_at]) VALUES (18, N'OBT-015', N'Enervon-C', NULL, NULL, N'pcs', 0, 10, CAST(10000.00 AS Decimal(12, 2)), CAST(14000.00 AS Decimal(12, 2)), NULL, 1, CAST(N'2026-05-17T00:19:54.810' AS DateTime), CAST(N'2026-05-17T00:19:54.810' AS DateTime))
INSERT [dbo].[obat] ([id_obat], [kode_obat], [nama_obat], [id_kategori], [id_supplier], [satuan], [stok], [stok_minimum], [harga_beli], [harga_jual], [tgl_kadaluarsa], [is_active], [created_at], [updated_at]) VALUES (22, N'OBT-016', N'Bodrex', NULL, NULL, N'pcs', 0, 10, CAST(10000.00 AS Decimal(12, 2)), CAST(15000.00 AS Decimal(12, 2)), NULL, 1, CAST(N'2026-05-17T00:23:51.667' AS DateTime), CAST(N'2026-05-17T00:23:51.667' AS DateTime))
INSERT [dbo].[obat] ([id_obat], [kode_obat], [nama_obat], [id_kategori], [id_supplier], [satuan], [stok], [stok_minimum], [harga_beli], [harga_jual], [tgl_kadaluarsa], [is_active], [created_at], [updated_at]) VALUES (23, N'OB001', N'Paracetamol 500mg', 1, 1, N'pcs', 40, 10, CAST(5000.00 AS Decimal(12, 2)), CAST(7500.00 AS Decimal(12, 2)), CAST(N'2027-06-10' AS Date), 1, CAST(N'2026-06-10T00:38:19.620' AS DateTime), CAST(N'2026-06-10T00:42:16.980' AS DateTime))
INSERT [dbo].[obat] ([id_obat], [kode_obat], [nama_obat], [id_kategori], [id_supplier], [satuan], [stok], [stok_minimum], [harga_beli], [harga_jual], [tgl_kadaluarsa], [is_active], [created_at], [updated_at]) VALUES (24, N'OB002', N'Amoxicillin', 2, 1, N'pcs', 55, 10, CAST(10000.00 AS Decimal(12, 2)), CAST(15000.00 AS Decimal(12, 2)), CAST(N'2027-06-10' AS Date), 1, CAST(N'2026-06-10T00:38:19.620' AS DateTime), CAST(N'2026-06-10T00:42:01.240' AS DateTime))
INSERT [dbo].[obat] ([id_obat], [kode_obat], [nama_obat], [id_kategori], [id_supplier], [satuan], [stok], [stok_minimum], [harga_beli], [harga_jual], [tgl_kadaluarsa], [is_active], [created_at], [updated_at]) VALUES (25, N'OB003', N'Vitamin C', 1, 1, N'pcs', 20, 10, CAST(2000.00 AS Decimal(12, 2)), CAST(5000.00 AS Decimal(12, 2)), CAST(N'2026-06-20' AS Date), 1, CAST(N'2026-06-10T00:38:19.620' AS DateTime), CAST(N'2026-06-10T00:38:19.620' AS DateTime))
INSERT [dbo].[obat] ([id_obat], [kode_obat], [nama_obat], [id_kategori], [id_supplier], [satuan], [stok], [stok_minimum], [harga_beli], [harga_jual], [tgl_kadaluarsa], [is_active], [created_at], [updated_at]) VALUES (26, N'OB004', N'Bodrex Lama', 1, 1, N'pcs', 15, 10, CAST(1000.00 AS Decimal(12, 2)), CAST(2000.00 AS Decimal(12, 2)), CAST(N'2026-06-05' AS Date), 1, CAST(N'2026-06-10T00:38:19.620' AS DateTime), CAST(N'2026-06-10T00:38:19.620' AS DateTime))
SET IDENTITY_INSERT [dbo].[obat] OFF
GO
SET IDENTITY_INSERT [dbo].[pelanggan] ON 

INSERT [dbo].[pelanggan] ([id_pelanggan], [kode_pelanggan], [nama_pelanggan], [no_telepon], [alamat], [no_bpjs], [jenis_pelanggan], [created_at], [is_active]) VALUES (1, N'PLG-001', N'Umum / Tanpa Nama', N'-', N'-', NULL, N'umum', CAST(N'2026-05-16T17:28:18.933' AS DateTime), 1)
INSERT [dbo].[pelanggan] ([id_pelanggan], [kode_pelanggan], [nama_pelanggan], [no_telepon], [alamat], [no_bpjs], [jenis_pelanggan], [created_at], [is_active]) VALUES (4, N'PL001', N'Pasien Umum', NULL, NULL, NULL, N'umum', CAST(N'2026-06-10T00:38:19.607' AS DateTime), 1)
INSERT [dbo].[pelanggan] ([id_pelanggan], [kode_pelanggan], [nama_pelanggan], [no_telepon], [alamat], [no_bpjs], [jenis_pelanggan], [created_at], [is_active]) VALUES (5, N'PL002', N'Ibu Budi', NULL, NULL, NULL, N'bpjs', CAST(N'2026-06-10T00:38:19.607' AS DateTime), 1)
SET IDENTITY_INSERT [dbo].[pelanggan] OFF
GO
SET IDENTITY_INSERT [dbo].[pembelian_detail] ON 

INSERT [dbo].[pembelian_detail] ([id_detail], [id_pembelian], [id_obat], [qty_pesan], [qty_terima], [harga_satuan], [subtotal], [tgl_kadaluarsa], [no_batch], [stok_sisa]) VALUES (2, 6, 5, 10, 10, CAST(5000.00 AS Decimal(12, 2)), CAST(50000.00 AS Decimal(14, 2)), CAST(N'2026-05-31' AS Date), NULL, 9)
INSERT [dbo].[pembelian_detail] ([id_detail], [id_pembelian], [id_obat], [qty_pesan], [qty_terima], [harga_satuan], [subtotal], [tgl_kadaluarsa], [no_batch], [stok_sisa]) VALUES (3, 7, 5, 5, 5, CAST(5000.00 AS Decimal(12, 2)), CAST(25000.00 AS Decimal(14, 2)), CAST(N'2026-05-24' AS Date), NULL, 3)
INSERT [dbo].[pembelian_detail] ([id_detail], [id_pembelian], [id_obat], [qty_pesan], [qty_terima], [harga_satuan], [subtotal], [tgl_kadaluarsa], [no_batch], [stok_sisa]) VALUES (6, 10, 24, 50, 0, CAST(10000.00 AS Decimal(12, 2)), CAST(500000.00 AS Decimal(14, 2)), NULL, NULL, 0)
SET IDENTITY_INSERT [dbo].[pembelian_detail] OFF
GO
SET IDENTITY_INSERT [dbo].[pembelian_header] ON 

INSERT [dbo].[pembelian_header] ([id_pembelian], [no_pembelian], [id_supplier], [id_petugas], [tgl_pesan], [tgl_terima], [total_harga], [status], [keterangan], [created_at]) VALUES (6, N'PO-20260516180454', 1, 1, CAST(N'2026-05-17' AS Date), NULL, CAST(50000.00 AS Decimal(14, 2)), N'diterima', NULL, CAST(N'2026-05-17T01:04:54.450' AS DateTime))
INSERT [dbo].[pembelian_header] ([id_pembelian], [no_pembelian], [id_supplier], [id_petugas], [tgl_pesan], [tgl_terima], [total_harga], [status], [keterangan], [created_at]) VALUES (7, N'PO-20260516182441', 1, 1, CAST(N'2026-05-17' AS Date), NULL, CAST(25000.00 AS Decimal(14, 2)), N'diterima', NULL, CAST(N'2026-05-17T01:24:41.540' AS DateTime))
INSERT [dbo].[pembelian_header] ([id_pembelian], [no_pembelian], [id_supplier], [id_petugas], [tgl_pesan], [tgl_terima], [total_harga], [status], [keterangan], [created_at]) VALUES (8, N'PO-001', 1, 1, CAST(N'2026-06-10' AS Date), CAST(N'2026-06-10' AS Date), CAST(0.00 AS Decimal(14, 2)), N'diterima', NULL, CAST(N'2026-06-10T00:40:15.663' AS DateTime))
INSERT [dbo].[pembelian_header] ([id_pembelian], [no_pembelian], [id_supplier], [id_petugas], [tgl_pesan], [tgl_terima], [total_harga], [status], [keterangan], [created_at]) VALUES (10, N'PO-002', 1, 1, CAST(N'2026-06-10' AS Date), CAST(N'2026-06-10' AS Date), CAST(500000.00 AS Decimal(14, 2)), N'diterima', NULL, CAST(N'2026-06-10T00:42:01.123' AS DateTime))
SET IDENTITY_INSERT [dbo].[pembelian_header] OFF
GO
SET IDENTITY_INSERT [dbo].[penjualan_detail] ON 

INSERT [dbo].[penjualan_detail] ([id_detail], [id_penjualan], [id_obat], [qty], [harga_satuan], [diskon_pct], [subtotal]) VALUES (4, 4, 23, 10, CAST(7500.00 AS Decimal(12, 2)), CAST(0.00 AS Decimal(5, 2)), CAST(75000.00 AS Decimal(14, 2)))
SET IDENTITY_INSERT [dbo].[penjualan_detail] OFF
GO
SET IDENTITY_INSERT [dbo].[penjualan_header] ON 

INSERT [dbo].[penjualan_header] ([id_penjualan], [no_penjualan], [id_pelanggan], [id_petugas], [id_resep], [tgl_transaksi], [subtotal], [diskon], [total_harga], [uang_bayar], [uang_kembali], [metode_bayar], [status], [created_at]) VALUES (3, N'SL-20260609170423', 1, 1, 3, CAST(N'2026-06-10T00:04:23.733' AS DateTime), CAST(0.00 AS Decimal(14, 2)), CAST(0.00 AS Decimal(14, 2)), CAST(7500.00 AS Decimal(14, 2)), CAST(10000.00 AS Decimal(14, 2)), CAST(2500.00 AS Decimal(14, 2)), N'tunai', N'selesai', CAST(N'2026-06-10T00:04:23.733' AS DateTime))
INSERT [dbo].[penjualan_header] ([id_penjualan], [no_penjualan], [id_pelanggan], [id_petugas], [id_resep], [tgl_transaksi], [subtotal], [diskon], [total_harga], [uang_bayar], [uang_kembali], [metode_bayar], [status], [created_at]) VALUES (4, N'SL202606103934', 5, 1, 4, CAST(N'2026-06-10T00:42:16.850' AS DateTime), CAST(75000.00 AS Decimal(14, 2)), CAST(0.00 AS Decimal(14, 2)), CAST(75000.00 AS Decimal(14, 2)), CAST(100000.00 AS Decimal(14, 2)), CAST(25000.00 AS Decimal(14, 2)), N'tunai', N'selesai', CAST(N'2026-06-10T00:42:16.850' AS DateTime))
SET IDENTITY_INSERT [dbo].[penjualan_header] OFF
GO
SET IDENTITY_INSERT [dbo].[petugas] ON 

INSERT [dbo].[petugas] ([id_petugas], [kode_petugas], [nama_petugas], [username], [password_hash], [is_active], [created_at]) VALUES (1, N'PTG001', N'Admin Apotek', N'admin', N'0192023a7bbd73250516f069df18b500', 1, CAST(N'2026-05-16T17:28:18.733' AS DateTime))
INSERT [dbo].[petugas] ([id_petugas], [kode_petugas], [nama_petugas], [username], [password_hash], [is_active], [created_at]) VALUES (2, N'PTG002', N'Drs. Rina Apoteker', N'apoteker', N'6814066e4388d49866348536288ee450', 1, CAST(N'2026-05-16T17:28:18.733' AS DateTime))
INSERT [dbo].[petugas] ([id_petugas], [kode_petugas], [nama_petugas], [username], [password_hash], [is_active], [created_at]) VALUES (3, N'PTG003', N'Kasir Satu', N'kasir1', N'de28f8f7998f23ab4194b51a6029416f', 1, CAST(N'2026-05-16T17:28:18.733' AS DateTime))
SET IDENTITY_INSERT [dbo].[petugas] OFF
GO
SET IDENTITY_INSERT [dbo].[resep_detail] ON 

INSERT [dbo].[resep_detail] ([id_detail], [id_resep], [id_obat], [qty], [dosis], [aturan_pakai], [catatan]) VALUES (3, 3, 5, 1, N'3x1', N'Setelah makan', NULL)
INSERT [dbo].[resep_detail] ([id_detail], [id_resep], [id_obat], [qty], [dosis], [aturan_pakai], [catatan]) VALUES (4, 4, 23, 10, N'3x1', N'Sesudah makan', NULL)
SET IDENTITY_INSERT [dbo].[resep_detail] OFF
GO
SET IDENTITY_INSERT [dbo].[resep_header] ON 

INSERT [dbo].[resep_header] ([id_resep], [no_resep], [id_pelanggan], [id_petugas], [nama_dokter], [tgl_resep], [status], [created_at], [asal_klinik]) VALUES (3, N'RSP-20260609170404', 1, 1, N'dr.Andi', CAST(N'2026-06-10' AS Date), N'selesai', CAST(N'2026-06-10T00:04:04.513' AS DateTime), NULL)
INSERT [dbo].[resep_header] ([id_resep], [no_resep], [id_pelanggan], [id_petugas], [nama_dokter], [tgl_resep], [status], [created_at], [asal_klinik]) VALUES (4, N'RSP-002', 5, 1, N'Dr. Tirta', CAST(N'2026-06-10' AS Date), N'selesai', CAST(N'2026-06-10T00:42:15.200' AS DateTime), NULL)
SET IDENTITY_INSERT [dbo].[resep_header] OFF
GO
SET IDENTITY_INSERT [dbo].[supplier] ON 

INSERT [dbo].[supplier] ([id_supplier], [kode_supplier], [nama_supplier], [alamat], [no_telepon], [email], [contact_person], [is_active], [created_at]) VALUES (1, N'SUP001', N'PT Kimia Farma', N'Jl. Veteran No.9, Jakarta', N'02140000001', N'cs@kimiafarma.co.id', N'Budi Santoso', 1, CAST(N'2026-05-16T17:28:18.593' AS DateTime))
INSERT [dbo].[supplier] ([id_supplier], [kode_supplier], [nama_supplier], [alamat], [no_telepon], [email], [contact_person], [is_active], [created_at]) VALUES (2, N'SUP002', N'PT Kalbe Farma', N'Jl. Let.Jend.Suprapto No.4, Jakarta', N'02140000002', N'cs@kalbe.co.id', N'Siti Rahayu', 1, CAST(N'2026-05-16T17:28:18.593' AS DateTime))
INSERT [dbo].[supplier] ([id_supplier], [kode_supplier], [nama_supplier], [alamat], [no_telepon], [email], [contact_person], [is_active], [created_at]) VALUES (3, N'SUP003', N'PT Dexa Medica', N'Jl. Palembang-Prabumulih, Palembang', N'07114000003', N'cs@dexa.co.id', N'Ahmad Fauzi', 1, CAST(N'2026-05-16T17:28:18.593' AS DateTime))
INSERT [dbo].[supplier] ([id_supplier], [kode_supplier], [nama_supplier], [alamat], [no_telepon], [email], [contact_person], [is_active], [created_at]) VALUES (4, N'S001', N'PT Kimia Farma', N'Surabaya', N'08111111', NULL, NULL, 1, CAST(N'2026-06-10T00:38:19.570' AS DateTime))
SET IDENTITY_INSERT [dbo].[supplier] OFF
GO
SET ANSI_PADDING ON
GO
