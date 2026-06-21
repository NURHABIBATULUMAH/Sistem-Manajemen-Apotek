USE [apotek_db];
GO

-- 1. Hapus tabel lama jika sudah ada agar tidak bentrok saat dicreate ulang
IF OBJECT_ID('dbo.pelanggan', 'U') IS NOT NULL
    DROP TABLE dbo.pelanggan;
GO

SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

-- 2. Buat tabel baru (Tanpa kolom foto)
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

-- 3. Tambahkan Constraint Unique untuk kode_pelanggan
ALTER TABLE [dbo].[pelanggan] ADD UNIQUE NONCLUSTERED 
(
    [kode_pelanggan] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO

-- 4. Tambahkan Nilai Default (Baris untuk 'foto' SUDAH DIHAPUS)
ALTER TABLE [dbo].[pelanggan] ADD DEFAULT ('umum') FOR [jenis_pelanggan]
GO
ALTER TABLE [dbo].[pelanggan] ADD DEFAULT (getdate()) FOR [created_at]
GO
ALTER TABLE [dbo].[pelanggan] ADD DEFAULT ((1)) FOR [is_active]
GO

-- 5. Tambahkan Validasi Check Constraint untuk jenis_pelanggan
ALTER TABLE [dbo].[pelanggan] WITH CHECK ADD CHECK (([jenis_pelanggan]='bpjs' OR [jenis_pelanggan]='umum'))
GO