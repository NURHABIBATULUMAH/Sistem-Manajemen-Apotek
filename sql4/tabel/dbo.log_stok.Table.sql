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
