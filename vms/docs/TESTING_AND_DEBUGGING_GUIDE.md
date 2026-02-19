# VMS CodeIgniter 3 Testing and Debugging Guide

## Overview

This guide provides comprehensive instructions for setting up and using the enhanced testing and debugging environment for the VMS (Vendor Management System) project built with CodeIgniter 3, PHP 5.6, and MySQL 5.7.

## Table of Contents

1. [Xdebug Configuration](#xdebug-configuration)
2. [PHPUnit Testing Setup](#phpunit-testing-setup)
3. [IDE Integration](#ide-integration)
4. [Running Tests](#running-tests)
5. [Code Coverage Reports](#code-coverage-reports)
6. [Debugging Workflows](#debugging-workflows)
7. [Troubleshooting](#troubleshooting)

## Xdebug Configuration

### Step 1: Apply Xdebug Configuration

1. Copy the optimized settings from `xdebug_config.ini` to your PHP configuration file:
   ```
   Location: C:\tools\php56\php.ini
   ```

2. Find the `[xdebug]` section in php.ini and replace/add these settings:
   ```ini
   [xdebug]
   ; Basic Xdebug settings
   zend_extension=php_xdebug.dll
   xdebug.default_enable=1

   ; Remote debugging settings (for IDE integration)
   xdebug.remote_enable=1
   xdebug.remote_autostart=0
   xdebug.remote_connect_back=0
   xdebug.remote_host=127.0.0.1
   xdebug.remote_port=9000
   xdebug.remote_handler=dbgp
   xdebug.idekey=PHPSTORM

   ; Code coverage settings (essential for unit testing)
   xdebug.coverage_enable=1

   ; Performance profiling settings
   xdebug.profiler_enable=0
   xdebug.profiler_enable_trigger=1
   xdebug.profiler_enable_trigger_value="XDEBUG_PROFILE"
   xdebug.profiler_output_dir="C:\inetpub\eproc\vms\app\application\logs"
   
   ; Enhanced debugging settings
   xdebug.collect_params=4
   xdebug.collect_return=1
   xdebug.collect_vars=1
   xdebug.show_exception_trace=1
   xdebug.show_local_vars=1
   xdebug.max_nesting_level=512
   ```

3. Restart your web server (Apache/IIS) after making changes.

4. Verify Xdebug is working:
   ```bash
   C:\tools\php56\php.exe -m | findstr -i xdebug
   ```

### Step 2: Verify Configuration

Create a simple PHP file to test Xdebug:
```php
<?php
phpinfo();
// Look for the Xdebug section
```

## PHPUnit Testing Setup

### Configuration Files

The project includes several testing configuration files:

1. **phpunit.xml** - Main PHPUnit configuration
2. **Bootstrap_enhanced.php** - Enhanced test bootstrap with utilities
3. **TestUtilities.php** - Helper class for testing

### Test Structure

```
app/application/tests/
├── coverage/           # Coverage reports
├── fixtures/          # Test data fixtures
├── models/            # Model tests
├── controllers/       # Controller tests
├── helpers/           # Helper tests
├── libraries/         # Library tests
├── Bootstrap.php      # Original bootstrap
├── Bootstrap_enhanced.php  # Enhanced bootstrap
├── TestUtilities.php  # Test utilities
└── Smoke_test.php     # Smoke tests
```

## IDE Integration

### PhpStorm Setup

1. **Configure PHP Interpreter:**
   - File → Settings → Languages & Frameworks → PHP
   - Set CLI Interpreter to: `C:\tools\php56\php.exe`
   - Verify Xdebug is detected

2. **Configure PHPUnit:**
   - File → Settings → Languages & Frameworks → PHP → Test Frameworks
   - Add PHPUnit by Remote Interpreter or Local
   - Set Configuration file: `app/phpunit.xml`
   - Set Bootstrap file: `app/application/tests/Bootstrap.php`

3. **Configure Debug Server:**
   - File → Settings → Languages & Frameworks → PHP → Servers
   - Name: `VMS Local`
   - Host: `localhost`
   - Port: `80` (or your server port)
   - Debugger: `Xdebug`
   - Use path mappings if necessary

4. **Configure Debug Configuration:**
   - Run → Edit Configurations → Add → PHP Web Page
   - Name: `Debug VMS`
   - Server: `VMS Local`
   - Start URL: `/vms/` (adjust to your project path)

### Visual Studio Code Setup

1. **Install Extensions:**
   - PHP Debug (Felix Becker)
   - PHP Intelephense

2. **Configure launch.json:**
   ```json
   {
       "version": "0.2.0",
       "configurations": [
           {
               "name": "Listen for Xdebug",
               "type": "php",
               "request": "launch",
               "port": 9000,
               "pathMappings": {
                   "/path/to/server": "${workspaceFolder}"
               }
           }
       ]
   }
   ```

## Running Tests

### Using the Test Runner Script

Execute `run_tests.bat` from the app/tests directory:

```bash
cd app/tests
run_tests.bat
```

Options available:
1. **All Tests (with coverage)** - Complete test suite with HTML coverage
2. **All Tests (without coverage)** - Faster execution without coverage
3. **Smoke Tests Only** - Basic availability tests
4. **Model Tests Only** - Database and business logic tests
5. **Helper Tests Only** - Utility function tests
6. **Controller Tests Only** - Web request tests

### Manual PHPUnit Execution

```bash
# All tests with coverage
C:\tools\php56\php.exe vendor\phpunit\phpunit\phpunit --configuration phpunit.xml

# Specific test suite
C:\tools\php56\php.exe vendor\phpunit\phpunit\phpunit --testsuite "Models"

# Single test file
C:\tools\php56\php.exe vendor\phpunit\phpunit\phpunit application/tests/models/AuctionModel_test.php

# With coverage
C:\tools\php56\php.exe vendor\phpunit\phpunit\phpunit --coverage-html application/tests/coverage/html
```

## Code Coverage Reports

### HTML Reports

After running tests with coverage, open:
```
app/application/tests/coverage/html/index.html
```

### Understanding Coverage Metrics

- **Lines Coverage**: Percentage of code lines executed
- **Functions Coverage**: Percentage of functions called
- **Classes Coverage**: Percentage of classes instantiated
- **Methods Coverage**: Percentage of methods called

### Coverage Thresholds

Current configuration targets:
- **High Coverage**: 80%+ (Green)
- **Medium Coverage**: 50-80% (Yellow)
- **Low Coverage**: <50% (Red)

## Debugging Workflows

### Web Application Debugging

1. **Start Debug Session:**
   - Set breakpoints in your IDE
   - Start listening for debug connections
   - Access your web application with debug parameter:
     ```
     http://localhost/vms/index.php?XDEBUG_SESSION_START=PHPSTORM
     ```

2. **Step Through Code:**
   - Use F8 (Step Over), F7 (Step Into), Shift+F8 (Step Out)
   - Examine variables in the Variables panel
   - Use the Console for expression evaluation

### Unit Test Debugging

1. **Debug Specific Test:**
   ```bash
   C:\tools\php56\php.exe vendor\phpunit\phpunit\phpunit --debug application/tests/models/AuctionModel_test.php::test_save_data
   ```

2. **Set Breakpoints in Test Code:**
   - Place breakpoints in test methods
   - Run test in debug mode from IDE

### Performance Profiling

1. **Enable Profiling:**
   - Add `XDEBUG_PROFILE=1` to URL parameters
   - Or set `xdebug.profiler_enable=1` in php.ini

2. **Analyze Profile:**
   - Profiles saved to: `app/application/logs/`
   - Use tools like QCacheGrind or PHPStorm profiler

## Troubleshooting

### Common Issues

#### Xdebug Not Working

**Problem**: IDE not connecting to Xdebug
**Solutions**:
1. Check if Xdebug is loaded: `php -m | findstr xdebug`
2. Verify port 9000 is not blocked by firewall
3. Check `xdebug.remote_enable=1` in php.ini
4. Restart web server after configuration changes

#### Tests Not Running

**Problem**: PHPUnit not found or tests failing
**Solutions**:
1. Verify PHPUnit installation: `php vendor/phpunit/phpunit/phpunit --version`
2. Check phpunit.xml configuration
3. Ensure database test connection is configured
4. Verify file permissions

#### Code Coverage Not Generated

**Problem**: No coverage reports created
**Solutions**:
1. Ensure Xdebug is loaded
2. Check `xdebug.coverage_enable=1`
3. Run tests with `--coverage-html` flag
4. Verify write permissions on coverage directory

#### Database Connection Issues

**Problem**: Tests failing due to database connection
**Solutions**:
1. Create test database: `vms_test`
2. Update database configuration in phpunit.xml
3. Ensure MySQL service is running
4. Check database credentials

### Performance Optimization

#### Test Execution Speed

1. **Run Tests Without Coverage** for faster feedback
2. **Use Specific Test Suites** instead of all tests
3. **Optimize Database Setup** in test fixtures
4. **Use Test Doubles** for external dependencies

#### Memory Usage

1. **Increase PHP Memory Limit** in php.ini:
   ```ini
   memory_limit = 512M
   ```
2. **Clean Up Test Data** after each test
3. **Use Database Transactions** for test isolation

## Best Practices

### Writing Tests

1. **Follow AAA Pattern**: Arrange, Act, Assert
2. **Use Descriptive Test Names**: `test_save_auction_with_valid_data`
3. **Test Edge Cases**: Empty inputs, invalid data, boundary conditions
4. **Mock External Dependencies**: Database, APIs, file systems
5. **Keep Tests Independent**: Each test should work in isolation

### Debugging

1. **Use Breakpoints Strategically**: Place at decision points
2. **Examine Variable State**: Check values at each step
3. **Use Conditional Breakpoints**: Break only when specific conditions are met
4. **Log Important Events**: Use custom logging for complex flows

### Code Coverage

1. **Aim for High Coverage**: Target 80%+ for critical modules
2. **Focus on Business Logic**: Prioritize model and library coverage
3. **Review Coverage Reports**: Identify untested code paths
4. **Write Tests for Bugs**: Add tests when fixing issues

## Environment Variables

Set these environment variables for testing:

```bash
CI_ENV=testing
DB_DATABASE=vms_test
DB_USERNAME=root
DB_PASSWORD=
DB_HOSTNAME=localhost
```

## Conclusion

This testing and debugging setup provides a robust foundation for developing and maintaining the VMS application. Regular use of these tools will help ensure code quality, reduce bugs, and improve overall development efficiency.

For additional support or questions, refer to the project documentation or contact the development team.
