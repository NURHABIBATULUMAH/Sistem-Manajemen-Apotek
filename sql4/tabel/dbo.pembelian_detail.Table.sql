USE [apotek_db];
GO

/****** Object:  Table [dbo].[pembelian_detail]    Script Date: 6/10/2026 7:40:39 AM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[pembelian_detail](
	[id_detail] [int] IDENTITY(1,1) NOT NULL,
	[id_pembelian] [int] NOT NULL,
	[id_obat] [int] NOT NULL,
	[qty_pesan] [int] NOT NULL,
	[qty_terima] [int] NOT NULL,
	[harga_satuan] [decimal](12, 2) NOT NULL,
	[subtotal] [decimal](14, 2) NOT NULL,
	[tgl_kadaluarsa] [date] NULL,
	[no_batch] [nvarchar](50) NULL,
	[stok_sisa] [int] NULL,
PRIMARY KEY CLUSTERED 
(
	[id_detail] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
SET IDENTITY_INSERT [dbo].[pembelian_detail] OFF
GO
ALTER TABLE [dbo].[pembelian_detail] ADD  DEFAULT ((0)) FOR [qty_pesan]
GO
ALTER TABLE [dbo].[pembelian_detail] ADD  DEFAULT ((0)) FOR [qty_terima]
GO
ALTER TABLE [dbo].[pembelian_detail] ADD  DEFAULT ((0)) FOR [harga_satuan]
GO
ALTER TABLE [dbo].[pembelian_detail] ADD  DEFAULT ((0)) FOR [subtotal]
GO
ALTER TABLE [dbo].[pembelian_detail] ADD  DEFAULT ((0)) FOR [stok_sisa]
GO
ALTER TABLE [dbo].[pembelian_detail]  WITH CHECK ADD  CONSTRAINT [fk_pd_obat] FOREIGN KEY([id_obat])
REFERENCES [dbo].[obat] ([id_obat])
GO
ALTER TABLE [dbo].[pembelian_detail] CHECK CONSTRAINT [fk_pd_obat]
GO
ALTER TABLE [dbo].[pembelian_detail]  WITH CHECK ADD  CONSTRAINT [fk_pd_pembelian] FOREIGN KEY([id_pembelian])
REFERENCES [dbo].[pembelian_header] ([id_pembelian])
GO
ALTER TABLE [dbo].[pembelian_detail] CHECK CONSTRAINT [fk_pd_pembelian]
GO

