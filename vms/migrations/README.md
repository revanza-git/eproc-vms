# VMS eProc Database Migrations

This folder contains all database migration files for the VMS eProc system.

## 📁 Folder Structure

```
migrations/
├── eproc/                          # 93 migration files for eproc database
│   ├── 001_create_migrations_table.sql
│   ├── 002_create_ms_admin_table.sql
│   ├── 003_create_ms_agen_table.sql
│   └── ... (90 more files)
└── eproc_perencanaan/              # 26 migration files for eproc_perencanaan database
    ├── 001_create_ms_fkpbj_table.sql
    ├── 002_create_ms_fp3_table.sql
    ├── 003_create_ms_fppbj_table.sql
    └── ... (23 more files)
```

## 🗄️ Database Summary

| Database | Tables | Migration Files |
|----------|--------|----------------|
| `eproc` | 93 | migrations/eproc/ |
| `eproc_perencanaan` | 26 | migrations/eproc_perencanaan/ |

## 📋 Migration Files Format

Each migration file contains SQL CREATE TABLE statements with:
- Complete table structure
- Primary keys and indexes
- Column types and constraints
- Default values and auto-increment

## 🚀 Usage

### For New Database Setup:

```bash
# Connect to MySQL Docker
mysql -h localhost -P 3307 -u root -p

# Create databases
CREATE DATABASE eproc;
CREATE DATABASE eproc_perencanaan;

# Run migrations
USE eproc;
SOURCE migrations/eproc/001_create_migrations_table.sql;
SOURCE migrations/eproc/002_create_ms_admin_table.sql;
# ... continue with all eproc migrations

USE eproc_perencanaan;
SOURCE migrations/eproc_perencanaan/001_create_ms_fkpbj_table.sql;
SOURCE migrations/eproc_perencanaan/002_create_ms_fp3_table.sql;
# ... continue with all eproc_perencanaan migrations
```

### For Development:

Use these migrations to recreate the database structure on development environments or when setting up the system on new servers.

## 🔄 Generated Files

All migration files were automatically generated from the existing database structure on 2025-01-07. They represent the complete schema of the VMS eProc system.

## ⚠️ Important Notes

- These migrations are for **database structure only**
- No data migrations are included
- Run migrations in numerical order (001, 002, 003, etc.)
- Test migrations on development environment first 