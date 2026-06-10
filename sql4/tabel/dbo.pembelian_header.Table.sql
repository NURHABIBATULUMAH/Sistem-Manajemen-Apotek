USE [apotek_db];
GO

/****** Object:  Table [dbo].[pembelian_header]    Script Date: 6/10/2026 7:40:39 AM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[pembelian_header](
	[id_pembelian] [int] IDENTITY(1,1) NOT NULL,
	[no_pembelian] [nvarchar](20) NOT NULL,
	[id_supplier] [int] NOT NULL,
	[id_petugas] [int] NOT NULL,
	[tgl_pesan] [date] NOT NULL,
	[tgl_terima] [date] NULL,
	[total_harga] [decimal](14, 2) NOT NULL,
	[status] [nvarchar](20) NOT NULL,
	[keterangan] [nvarchar](max) NULL,
	[created_at] [datetime] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id_pembelian] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
