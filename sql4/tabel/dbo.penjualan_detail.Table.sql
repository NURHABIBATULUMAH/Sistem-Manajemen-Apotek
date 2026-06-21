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
SET IDENTITY_INSERT [dbo].[penjualan_detail] OFF
GO
ALTER TABLE [dbo].[penjualan_detail] ADD  DEFAULT ((1)) FOR [qty]
GO
ALTER TABLE [dbo].[penjualan_detail] ADD  DEFAULT ((0)) FOR [harga_satuan]
GO
ALTER TABLE [dbo].[penjualan_detail] ADD  DEFAULT ((0)) FOR [diskon_pct]
GO
ALTER TABLE [dbo].[penjualan_detail] ADD  DEFAULT ((0)) FOR [subtotal]
GO
ALTER TABLE [dbo].[penjualan_detail]  WITH CHECK ADD  CONSTRAINT [fk_sd_obat] FOREIGN KEY([id_obat])
REFERENCES [dbo].[obat] ([id_obat])
GO
ALTER TABLE [dbo].[penjualan_detail] CHECK CONSTRAINT [fk_sd_obat]
GO
ALTER TABLE [dbo].[penjualan_detail]  WITH CHECK ADD  CONSTRAINT [fk_sd_penjualan] FOREIGN KEY([id_penjualan])
REFERENCES [dbo].[penjualan_header] ([id_penjualan])
GO
ALTER TABLE [dbo].[penjualan_detail] CHECK CONSTRAINT [fk_sd_penjualan]
GO
