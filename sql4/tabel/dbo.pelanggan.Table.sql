USE [apotek_db];
GO

/****** Object:  Table [dbo].[pelanggan]    Script Date: 6/10/2026 7:40:39 AM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[pelanggan](
	[id_pelanggan] [int] IDENTITY(1,1) NOT NULL,
	[kode_pelanggan] [nvarchar](10) NOT NULL,
	[nama_pelanggan] [nvarchar](150) NOT NULL,
	[no_telepon] [nvarchar](20) NULL,
	[alamat] [nvarchar](max) NULL,
	[no_bpjs] [nvarchar](20) NULL,
	[jenis_pelanggan] [nvarchar](10) NOT NULL,
	[created_at] [datetime] NOT NULL,
	[is_active] [bit] NULL,
PRIMARY KEY CLUSTERED 
(
	[id_pelanggan] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
