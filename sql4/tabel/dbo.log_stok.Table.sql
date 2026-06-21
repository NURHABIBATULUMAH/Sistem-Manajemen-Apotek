USE [apotek_db];
GO

/****** Object:  Table [dbo].[log_stok]    Script Date: 6/10/2026 7:40:39 AM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[log_stok](
	[id_log] [int] IDENTITY(1,1) NOT NULL,
	[id_obat] [int] NOT NULL,
	[id_petugas] [int] NULL,
	[stok_sebelum] [int] NOT NULL,
	[jumlah_perubahan] [int] NOT NULL,
	[stok_sesudah] [int] NOT NULL,
	[jenis_transaksi] [nvarchar](20) NOT NULL,
	[keterangan] [nvarchar](max) NULL,
	[created_at] [datetime] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id_log] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
SET IDENTITY_INSERT [dbo].[log_stok] ON 

INSERT [dbo].[log_stok] ([id_log], [id_obat], [id_petugas], [stok_sebelum], [jumlah_perubahan], [stok_sesudah], [jenis_transaksi], [id_referensi], [tipe_referensi], [keterangan], [created_at]) VALUES (1, 5, 1, 30, -1, 29, N'penjualan', 1, N'penjualan_header', N'Penjualan obat', CAST(N'2026-05-17T01:23:44.543' AS DateTime))
INSERT [dbo].[log_stok] ([id_log], [id_obat], [id_petugas], [stok_sebelum], [jumlah_perubahan], [stok_sesudah], [jenis_transaksi], [id_referensi], [tipe_referensi], [keterangan], [created_at]) VALUES (2, 5, 1, 14, -1, 13, N'penjualan', 2, N'penjualan_header', N'Penjualan obat', CAST(N'2026-05-17T01:28:24.107' AS DateTime))
SET IDENTITY_INSERT [dbo].[log_stok] OFF
GO
ALTER TABLE [dbo].[log_stok] ADD  DEFAULT ((0)) FOR [stok_sebelum]
GO
ALTER TABLE [dbo].[log_stok] ADD  DEFAULT ((0)) FOR [jumlah_perubahan]
GO
ALTER TABLE [dbo].[log_stok] ADD  DEFAULT ((0)) FOR [stok_sesudah]
GO
ALTER TABLE [dbo].[log_stok] ADD  DEFAULT (getdate()) FOR [created_at]
GO
ALTER TABLE [dbo].[log_stok]  WITH CHECK ADD  CONSTRAINT [fk_ls_obat] FOREIGN KEY([id_obat])
REFERENCES [dbo].[obat] ([id_obat])
GO
ALTER TABLE [dbo].[log_stok] CHECK CONSTRAINT [fk_ls_obat]
GO
ALTER TABLE [dbo].[log_stok]  WITH CHECK ADD  CONSTRAINT [fk_ls_petugas] FOREIGN KEY([id_petugas])
REFERENCES [dbo].[petugas] ([id_petugas])
GO
ALTER TABLE [dbo].[log_stok] CHECK CONSTRAINT [fk_ls_petugas]
GO
ALTER TABLE [dbo].[log_stok]  WITH CHECK ADD CHECK  (([jenis_transaksi]='return' OR [jenis_transaksi]='penyesuaian' OR [jenis_transaksi]='penjualan' OR [jenis_transaksi]='pembelian'))
GO
