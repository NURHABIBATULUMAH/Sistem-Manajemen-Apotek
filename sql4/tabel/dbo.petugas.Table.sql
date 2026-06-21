USE [apotek_db];
GO

-- 1. Hapus tabel lama jika sudah ada agar bisa dicreate ulang dengan bersih
IF OBJECT_ID('dbo.petugas', 'U') IS NOT NULL
    DROP TABLE dbo.petugas;
GO

SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

-- 2. Buat tabel petugas baru (Tanpa kolom role)
CREATE TABLE [dbo].[petugas](
    [id_petugas] [int] IDENTITY(1,1) NOT NULL,
    [kode_petugas] [nvarchar](10) NOT NULL,
    [nama_petugas] [nvarchar](150) NOT NULL,
    [username] [nvarchar](50) NOT NULL,
    [password_hash] [nvarchar](255) NOT NULL,
    [is_active] [bit] NOT NULL,
    [created_at] [datetime] NOT NULL,
PRIMARY KEY CLUSTERED 
(
    [id_petugas] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO

-- 3. Tambahkan Constraint Unique untuk kode_petugas
ALTER TABLE [dbo].[petugas] ADD UNIQUE NONCLUSTERED 
(
    [kode_petugas] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO

SET ANSI_PADDING ON
GO

-- 4. Tambahkan Constraint Unique untuk username
ALTER TABLE [dbo].[petugas] ADD UNIQUE NONCLUSTERED 
(
    [username] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO

-- 5. Tambahkan Nilai Default untuk is_active dan created_at (Baris DEFAULT milik 'role' SUDAH DIHAPUS)
ALTER TABLE [dbo].[petugas] ADD DEFAULT ((1)) FOR [is_active]
GO
ALTER TABLE [dbo].[petugas] ADD DEFAULT (getdate()) FOR [created_at]
GO

-- Catatan: Baris CHECK CONSTRAINT untuk 'role' juga sudah dihapus sepenuhnya di sini