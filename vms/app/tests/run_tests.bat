@echo off
REM VMS CodeIgniter 3 Test Runner with Code Coverage
REM This script runs PHPUnit tests and generates code coverage reports

echo ========================================
echo VMS Test Suite Runner
echo ========================================
echo.

REM Set PHP path
set PHP_PATH=C:\tools\php56\php.exe

REM Set project paths
set PROJECT_DIR=%~dp0..\
set APP_DIR=%PROJECT_DIR%application
set TEST_DIR=%APP_DIR%\tests
set COVERAGE_DIR=%TEST_DIR%\coverage

echo Project Directory: %PROJECT_DIR%
echo Test Directory: %TEST_DIR%
echo Coverage Directory: %COVERAGE_DIR%
echo.

REM Check if PHP exists
if not exist "%PHP_PATH%" (
    echo ERROR: PHP not found at %PHP_PATH%
    echo Please update the PHP_PATH in this script.
    pause
    exit /b 1
)

REM Check if phpunit.xml exists
if not exist "%PROJECT_DIR%phpunit.xml" (
    echo ERROR: phpunit.xml not found in project directory
    echo Please ensure phpunit.xml is configured properly.
    pause
    exit /b 1
)

REM Create coverage directory if it doesn't exist
if not exist "%COVERAGE_DIR%" (
    echo Creating coverage directory...
    mkdir "%COVERAGE_DIR%"
    mkdir "%COVERAGE_DIR%\html"
)

echo Checking Xdebug status...
%PHP_PATH% -m | findstr /i "xdebug" >nul
if %errorlevel% equ 0 (
    echo [✓] Xdebug is loaded - Code coverage will be generated
) else (
    echo [!] Xdebug is not loaded - Code coverage will be disabled
)

echo.
echo ========================================
echo Running Test Suites
echo ========================================
echo.

REM Option menu
echo Select test suite to run:
echo 1. All Tests (with coverage)
echo 2. All Tests (without coverage)
echo 3. Smoke Tests Only
echo 4. Model Tests Only  
echo 5. Helper Tests Only
echo 6. Controller Tests Only
echo 0. Exit
echo.

set /p choice="Enter your choice (0-6): "

if "%choice%"=="0" goto :exit
if "%choice%"=="1" goto :all_with_coverage
if "%choice%"=="2" goto :all_without_coverage
if "%choice%"=="3" goto :smoke_tests
if "%choice%"=="4" goto :model_tests
if "%choice%"=="5" goto :helper_tests
if "%choice%"=="6" goto :controller_tests

echo Invalid choice. Exiting...
goto :exit

:all_with_coverage
echo Running all tests with code coverage...
"%PHP_PATH%" "vendor\bin\phpunit" --configuration phpunit.xml --coverage-html "%COVERAGE_DIR%\html" --coverage-clover "%COVERAGE_DIR%\clover.xml" --coverage-text
goto :show_results

:all_without_coverage
echo Running all tests without code coverage...
"%PHP_PATH%" "vendor\bin\phpunit" --configuration phpunit.xml --no-coverage
goto :show_results

:smoke_tests
echo Running smoke tests only...
"%PHP_PATH%" "vendor\bin\phpunit" --configuration phpunit.xml --testsuite "Smoke Tests" --no-coverage
goto :show_results

:model_tests
echo Running model tests only...
"%PHP_PATH%" "vendor\bin\phpunit" --configuration phpunit.xml --testsuite "Models" --coverage-html "%COVERAGE_DIR%\html\models"
goto :show_results

:helper_tests
echo Running helper tests only...
"%PHP_PATH%" "vendor\bin\phpunit" --configuration phpunit.xml --testsuite "Helpers" --no-coverage
goto :show_results

:controller_tests
echo Running controller tests only...
"%PHP_PATH%" "vendor\bin\phpunit" --configuration phpunit.xml --testsuite "Controllers" --coverage-html "%COVERAGE_DIR%\html\controllers"
goto :show_results

:show_results
echo.
echo ========================================
echo Test Results
echo ========================================
echo.

if %errorlevel% equ 0 (
    echo [✓] Tests completed successfully!
    
    if exist "%COVERAGE_DIR%\html\index.html" (
        echo [✓] HTML coverage report generated: %COVERAGE_DIR%\html\index.html
        set /p open_coverage="Open coverage report in browser? (y/n): "
        if /i "%open_coverage%"=="y" (
            start "" "%COVERAGE_DIR%\html\index.html"
        )
    )
    
    if exist "%COVERAGE_DIR%\clover.xml" (
        echo [✓] Clover coverage report generated: %COVERAGE_DIR%\clover.xml
    )
    
    if exist "%TEST_DIR%\coverage\junit.xml" (
        echo [✓] JUnit report generated: %TEST_DIR%\coverage\junit.xml
    )
    
) else (
    echo [✗] Tests failed with exit code %errorlevel%
    echo Please check the output above for details.
)

echo.
echo Test logs are available in: %APP_DIR%\logs\
echo.

:exit
echo.
echo Press any key to exit...
pause >nul
