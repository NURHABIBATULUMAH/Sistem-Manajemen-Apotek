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
