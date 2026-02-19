@echo off
echo ========================================
echo VMS eProc Database Migration Runner
echo ========================================
echo.

set MYSQL_HOST=localhost
set MYSQL_PORT=3307
set MYSQL_USER=root
set MYSQL_PASS=Nusantara1234

echo Connecting to MySQL Docker on %MYSQL_HOST%:%MYSQL_PORT%
echo.

echo Running all migrations...
mysql -h %MYSQL_HOST% -P %MYSQL_PORT% -u %MYSQL_USER% -p%MYSQL_PASS% < run_all_migrations.sql

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ========================================
    echo ✅ Migration completed successfully!
    echo ========================================
    echo.
    echo Databases created:
    echo - eproc (93 tables)
    echo - eproc_perencanaan (26 tables)
    echo.
) else (
    echo.
    echo ========================================
    echo ❌ Migration failed!
    echo ========================================
    echo Please check MySQL connection and try again.
    echo.
)

pause 