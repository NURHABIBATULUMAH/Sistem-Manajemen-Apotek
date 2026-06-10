USE [apotek_db];
GO

/****** Object:  Table [dbo].[resep_detail]    Script Date: 6/10/2026 7:40:39 AM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[resep_detail](
	[id_detail] [int] IDENTITY(1,1) NOT NULL,
	[id_resep] [int] NOT NULL,
	[id_obat] [int] NOT NULL,
	[qty] [int] NOT NULL,
	[dosis] [nvarchar](50) NULL,
	[aturan_pakai] [nvarchar](100) NULL,
	[catatan] [nvarchar](max) NULL,
PRIMARY KEY CLUSTERED 
(
	[id_detail] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
