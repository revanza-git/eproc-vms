CREATE TABLE IF NOT EXISTS ms_fppbj_year_anggaran (
  id_fppbj INT NOT NULL,
  year_anggaran INT NOT NULL,
  PRIMARY KEY (id_fppbj, year_anggaran),
  KEY idx_year_anggaran (year_anggaran)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE DATABASE IF NOT EXISTS eproc_perencanaan;
USE eproc_perencanaan;

CREATE TABLE IF NOT EXISTS ms_fppbj_year_anggaran (
  id_fppbj INT NOT NULL,
  year_anggaran INT NOT NULL,
  PRIMARY KEY (id_fppbj, year_anggaran),
  KEY idx_year_anggaran (year_anggaran)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
