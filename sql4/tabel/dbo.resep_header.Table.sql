USE [apotek_db];
GO

/****** Object:  Table [dbo].[resep_header]    Script Date: 6/10/2026 7:40:39 AM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[resep_header](
	[id_resep] [int] IDENTITY(1,1) NOT NULL,
	[no_resep] [nvarchar](20) NOT NULL,
	[id_pelanggan] [int] NOT NULL,
	[id_petugas] [int] NOT NULL,
	[nama_dokter] [nvarchar](150) NULL,
	[tgl_resep] [date] NOT NULL,
	[status] [nvarchar](20) NOT NULL,
	[created_at] [datetime] NOT NULL,
	[asal_klinik] [nvarchar](150) NULL,
PRIMARY KEY CLUSTERED 
(
	[id_resep] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
ALTER TABLE [dbo].[resep_header] ADD UNIQUE NONCLUSTERED 
(
	[no_resep] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
ALTER TABLE [dbo].[resep_header] ADD  DEFAULT ('menunggu') FOR [status]
GO
ALTER TABLE [dbo].[resep_header] ADD  DEFAULT (getdate()) FOR [created_at]
GO
ALTER TABLE [dbo].[resep_header]  WITH CHECK ADD  CONSTRAINT [fk_rh_pelanggan] FOREIGN KEY([id_pelanggan])
REFERENCES [dbo].[pelanggan] ([id_pelanggan])
GO
ALTER TABLE [dbo].[resep_header] CHECK CONSTRAINT [fk_rh_pelanggan]
GO
ALTER TABLE [dbo].[resep_header]  WITH CHECK ADD  CONSTRAINT [fk_rh_petugas] FOREIGN KEY([id_petugas])
REFERENCES [dbo].[petugas] ([id_petugas])
GO
ALTER TABLE [dbo].[resep_header] CHECK CONSTRAINT [fk_rh_petugas]
GO
ALTER TABLE [dbo].[resep_header]  WITH CHECK ADD CHECK  (([status]='dibatalkan' OR [status]='selesai' OR [status]='diproses' OR [status]='menunggu'))
GO
