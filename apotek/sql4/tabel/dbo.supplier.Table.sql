USE [apotek_db];
GO

/****** Object:  Table [dbo].[supplier]    Script Date: 6/10/2026 7:40:39 AM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[supplier](
	[id_supplier] [int] IDENTITY(1,1) NOT NULL,
	[kode_supplier] [nvarchar](10) NOT NULL,
	[nama_supplier] [nvarchar](150) NOT NULL,
	[alamat] [nvarchar](max) NULL,
	[no_telepon] [nvarchar](20) NULL,
	[email] [nvarchar](100) NULL,
	[contact_person] [nvarchar](100) NULL,
	[is_active] [bit] NOT NULL,
	[created_at] [datetime] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id_supplier] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
ALTER TABLE [dbo].[supplier] ADD  DEFAULT ((1)) FOR [is_active]
GO
ALTER TABLE [dbo].[supplier] ADD  DEFAULT (getdate()) FOR [created_at]
GO