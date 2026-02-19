ALTER TABLE ms_procurement
  ADD COLUMN IF NOT EXISTS id_fppbj int(11) NULL,
  ADD COLUMN IF NOT EXISTS tipe_pengadaan varchar(255) NULL,
  ADD COLUMN IF NOT EXISTS budget_year varchar(20) NULL,
  ADD COLUMN IF NOT EXISTS evaluation_method varchar(45) NULL,
  ADD COLUMN IF NOT EXISTS evaluation_method_desc varchar(45) NULL,
  ADD COLUMN IF NOT EXISTS idr_value decimal(15,2) NULL,
  ADD COLUMN IF NOT EXISTS id_kurs int(5) NULL,
  ADD COLUMN IF NOT EXISTS kurs_value decimal(15,2) NULL,
  ADD COLUMN IF NOT EXISTS edit_stamp timestamp NULL DEFAULT NULL;

