USE eproc;

SET @table_exists = (
  SELECT COUNT(*)
  FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'ms_procurement'
);

SET @sql = IF(
  @table_exists > 0,
  'ALTER TABLE ms_procurement
    ADD COLUMN IF NOT EXISTS id_fppbj int(11) NULL,
    ADD COLUMN IF NOT EXISTS tipe_pengadaan varchar(255) NULL,
    ADD COLUMN IF NOT EXISTS budget_year varchar(20) NULL,
    ADD COLUMN IF NOT EXISTS evaluation_method varchar(45) NULL,
    ADD COLUMN IF NOT EXISTS evaluation_method_desc varchar(45) NULL,
    ADD COLUMN IF NOT EXISTS idr_value decimal(15,2) NULL,
    ADD COLUMN IF NOT EXISTS id_kurs int(5) NULL,
    ADD COLUMN IF NOT EXISTS kurs_value decimal(15,2) NULL,
    ADD COLUMN IF NOT EXISTS edit_stamp timestamp NULL DEFAULT NULL',
  'SELECT \"skip: ms_procurement does not exist\"'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
