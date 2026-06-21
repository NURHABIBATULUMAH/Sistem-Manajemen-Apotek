USE [apotek_db];
GO

/****** Object:  Table [dbo].[obat]    Script Date: 6/10/2026 7:40:39 AM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[obat](
	[id_obat] [int] IDENTITY(1,1) NOT NULL,
	[kode_obat] [nvarchar](15) NOT NULL,
	[nama_obat] [nvarchar](150) NOT NULL,
	[id_kategori] [int] NULL,
	[id_supplier] [int] NULL,
	[satuan] [nvarchar](20) NOT NULL,
	[stok] [int] NOT NULL,
	[stok_minimum] [int] NOT NULL,
	[harga_beli] [decimal](12, 2) NOT NULL,
	[harga_jual] [decimal](12, 2) NOT NULL,
	[tgl_kadaluarsa] [date] NULL,
	[is_active] [bit] NOT NULL,
	[created_at] [datetime] NOT NULL,
	[updated_at] [datetime] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id_obat] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
ALTER TABLE [dbo].[obat] ADD UNIQUE NONCLUSTERED 
(
	[kode_obat] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
ALTER TABLE [dbo].[obat] ADD  DEFAULT ('pcs') FOR [satuan]
GO
ALTER TABLE [dbo].[obat] ADD  DEFAULT ((0)) FOR [stok]
GO
ALTER TABLE [dbo].[obat] ADD  DEFAULT ((10)) FOR [stok_minimum]
GO
ALTER TABLE [dbo].[obat] ADD  DEFAULT ((0)) FOR [harga_beli]
GO
ALTER TABLE [dbo].[obat] ADD  DEFAULT ((0)) FOR [harga_jual]
GO
ALTER TABLE [dbo].[obat] ADD  DEFAULT ((1)) FOR [is_active]
GO
ALTER TABLE [dbo].[obat] ADD  DEFAULT (getdate()) FOR [created_at]
GO
ALTER TABLE [dbo].[obat] ADD  DEFAULT (getdate()) FOR [updated_at]
GO
ALTER TABLE [dbo].[obat]  WITH CHECK ADD  CONSTRAINT [fk_obat_kategori] FOREIGN KEY([id_kategori])
REFERENCES [dbo].[kategori] ([id_kategori])
GO
