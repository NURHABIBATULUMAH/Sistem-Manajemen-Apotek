USE [apotek_db];
GO

/****** Object:  Table [dbo].[kategori]    Script Date: 6/10/2026 7:40:39 AM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[kategori](
	[id_kategori] [int] IDENTITY(1,1) NOT NULL,
	[kode_kategori] [nvarchar](10) NOT NULL,
	[nama_kategori] [nvarchar](100) NOT NULL,
	[deskripsi] [nvarchar](max) NULL,
	[jenis_obat] [nvarchar](20) NOT NULL,
	[is_active] [bit] NOT NULL,
	[created_at] [datetime] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id_kategori] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
SET IDENTITY_INSERT [dbo].[kategori] ON 

INSERT [dbo].[kategori] ([id_kategori], [kode_kategori], [nama_kategori], [deskripsi], [jenis_obat], [is_active], [created_at]) VALUES (1, N'KAT001', N'Analgesik', N'Obat pereda nyeri dan demam', N'bebas', 1, CAST(N'2026-05-16T17:28:18.527' AS DateTime))
INSERT [dbo].[kategori] ([id_kategori], [kode_kategori], [nama_kategori], [deskripsi], [jenis_obat], [is_active], [created_at]) VALUES (2, N'KAT002', N'Antibiotik', N'Obat melawan infeksi bakteri', N'keras', 1, CAST(N'2026-05-16T17:28:18.527' AS DateTime))
INSERT [dbo].[kategori] ([id_kategori], [kode_kategori], [nama_kategori], [deskripsi], [jenis_obat], [is_active], [created_at]) VALUES (3, N'KAT003', N'Vitamin', N'Suplemen vitamin dan mineral', N'bebas', 1, CAST(N'2026-05-16T17:28:18.527' AS DateTime))
INSERT [dbo].[kategori] ([id_kategori], [kode_kategori], [nama_kategori], [deskripsi], [jenis_obat], [is_active], [created_at]) VALUES (4, N'KAT004', N'Antasida', N'Obat gangguan lambung dan maag', N'bebas', 1, CAST(N'2026-05-16T17:28:18.527' AS DateTime))
INSERT [dbo].[kategori] ([id_kategori], [kode_kategori], [nama_kategori], [deskripsi], [jenis_obat], [is_active], [created_at]) VALUES (5, N'KAT005', N'Antihipertensi', N'Obat tekanan darah tinggi', N'keras', 1, CAST(N'2026-05-16T17:28:18.527' AS DateTime))
SET IDENTITY_INSERT [dbo].[kategori] OFF
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [UQ__kategori__6B23B369C28EA643]    Script Date: 5/17/2026 1:39:38 AM ******/
ALTER TABLE [dbo].[kategori] ADD UNIQUE NONCLUSTERED 
(
	[kode_kategori] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
ALTER TABLE [dbo].[kategori] ADD  DEFAULT ('bebas') FOR [jenis_obat]
GO
ALTER TABLE [dbo].[kategori] ADD  DEFAULT ((1)) FOR [is_active]
GO
ALTER TABLE [dbo].[kategori] ADD  DEFAULT (getdate()) FOR [created_at]
GO
ALTER TABLE [dbo].[kategori]  WITH CHECK ADD CHECK  (([jenis_obat]='psikotropika' OR [jenis_obat]='narkotika' OR [jenis_obat]='keras' OR [jenis_obat]='bebas_terbatas' OR [jenis_obat]='bebas'))
GO

