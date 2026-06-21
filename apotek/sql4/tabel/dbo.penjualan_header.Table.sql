USE [apotek_db];
GO

/****** Object:  Table [dbo].[penjualan_header]    Script Date: 6/10/2026 7:40:39 AM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[penjualan_header](
	[id_penjualan] [int] IDENTITY(1,1) NOT NULL,
	[no_penjualan] [nvarchar](20) NOT NULL,
	[id_pelanggan] [int] NULL,
	[id_petugas] [int] NOT NULL,
	[id_resep] [int] NULL,
	[tgl_transaksi] [datetime] NOT NULL,
	[subtotal] [decimal](14, 2) NOT NULL,
	[diskon] [decimal](14, 2) NOT NULL,
	[total_harga] [decimal](14, 2) NOT NULL,
	[uang_bayar] [decimal](14, 2) NOT NULL,
	[uang_kembali] [decimal](14, 2) NOT NULL,
	[metode_bayar] [nvarchar](20) NOT NULL,
	[status] [nvarchar](20) NOT NULL,
	[created_at] [datetime] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id_penjualan] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
ALTER TABLE [dbo].[penjualan_header] ADD UNIQUE NONCLUSTERED 
(
	[no_penjualan] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
ALTER TABLE [dbo].[penjualan_header] ADD  DEFAULT (getdate()) FOR [tgl_transaksi]
GO
ALTER TABLE [dbo].[penjualan_header] ADD  DEFAULT ((0)) FOR [subtotal]
GO
ALTER TABLE [dbo].[penjualan_header] ADD  DEFAULT ((0)) FOR [diskon]
GO
ALTER TABLE [dbo].[penjualan_header] ADD  DEFAULT ((0)) FOR [total_harga]
GO
ALTER TABLE [dbo].[penjualan_header] ADD  DEFAULT ((0)) FOR [uang_bayar]
GO
ALTER TABLE [dbo].[penjualan_header] ADD  DEFAULT ((0)) FOR [uang_kembali]
GO
ALTER TABLE [dbo].[penjualan_header] ADD  DEFAULT ('tunai') FOR [metode_bayar]
GO
ALTER TABLE [dbo].[penjualan_header] ADD  DEFAULT ('selesai') FOR [status]
GO
ALTER TABLE [dbo].[penjualan_header] ADD  DEFAULT (getdate()) FOR [created_at]
GO
ALTER TABLE [dbo].[penjualan_header]  WITH CHECK ADD  CONSTRAINT [fk_sh_pelanggan] FOREIGN KEY([id_pelanggan])
REFERENCES [dbo].[pelanggan] ([id_pelanggan])
GO
ALTER TABLE [dbo].[penjualan_header] CHECK CONSTRAINT [fk_sh_pelanggan]
GO
ALTER TABLE [dbo].[penjualan_header]  WITH CHECK ADD  CONSTRAINT [fk_sh_petugas] FOREIGN KEY([id_petugas])
REFERENCES [dbo].[petugas] ([id_petugas])
GO
ALTER TABLE [dbo].[penjualan_header] CHECK CONSTRAINT [fk_sh_petugas]
GO
ALTER TABLE [dbo].[penjualan_header]  WITH CHECK ADD  CONSTRAINT [fk_sh_resep] FOREIGN KEY([id_resep])
REFERENCES [dbo].[resep_header] ([id_resep])
GO
ALTER TABLE [dbo].[penjualan_header] CHECK CONSTRAINT [fk_sh_resep]
GO
ALTER TABLE [dbo].[penjualan_header]  WITH CHECK ADD CHECK  (([metode_bayar]='kredit' OR [metode_bayar]='debit' OR [metode_bayar]='bpjs' OR [metode_bayar]='transfer' OR [metode_bayar]='tunai'))
GO
ALTER TABLE [dbo].[penjualan_header]  WITH CHECK ADD CHECK  (([status]='dibatalkan' OR [status]='selesai'))
GO

