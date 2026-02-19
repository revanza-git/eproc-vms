# How to Run Controller Tests

## Prerequisites
- PHP 7.0+
- Install dependencies: run `composer install` inside `vms/app`
- CodeIgniter framework setup
- Database configured for testing

## Method 1: Using Batch Files (Easiest)

### Run All Controller Tests:
```batch
run_controller_tests.bat
```

### Run Individual Test:
```batch
test_main.bat
```

## Method 2: Command Line (From app directory)

### From /app directory:
```bash
# Test individual controllers
php vendor\bin\phpunit application\tests\controllers\MainController_test.php
php vendor\bin\phpunit application\tests\controllers\AuctionController_test.php  
php vendor\bin\phpunit application\tests\controllers\DashboardController_test.php

# Test all controllers
php vendor\bin\phpunit application\tests\controllers\

# Test with configuration
php vendor\bin\phpunit --configuration application\phpunit.xml application\tests\controllers\

# Test with coverage
php vendor\bin\phpunit --configuration application\phpunit.xml --coverage-html application\tests\coverage\
```

### From /app/application directory:
```bash
# Test individual controllers  
php ..\vendor\bin\phpunit tests\controllers\MainController_test.php
php ..\vendor\bin\phpunit tests\controllers\AuctionController_test.php
php ..\vendor\bin\phpunit tests\controllers\DashboardController_test.php

# Test all controllers
php ..\vendor\bin\phpunit tests\controllers\

# Test with configuration
php ..\vendor\bin\phpunit --configuration phpunit.xml tests\controllers\
```

## Method 3: IDE Integration

### PHPStorm:
1. Right-click on test file → "Run 'MainController_test'"
2. Or use Run/Debug Configuration for PHPUnit

### VS Code:
1. Install PHP Unit extension
2. Use Command Palette → "PHPUnit: Run Test"

## Method 4: CI/CD Pipeline

### GitHub Actions example:
```yaml
- name: Run Controller Tests
  run: |
    cd app
    php vendor/bin/phpunit application/tests/controllers/
```

## Troubleshooting

### Common Issues:

1. **"Could not open input file"**
   - Check you're in the correct directory
   - Verify vendor/bin/phpunit exists

2. **"Class not found"**
   - Run: `composer install`
   - Check autoloader: `composer dump-autoload`

3. **Database connection errors**
   - Configure test database in `application/config/database.php`
   - Ensure test database exists

4. **PowerShell display issues**
   - Use batch files instead
   - Or use Command Prompt instead of PowerShell

### Test Files Created:

- ✅ `MainController_test.php` - Authentication & login testing
- ✅ `AuctionController_test.php` - Auction CRUD & workflow testing  
- ✅ `DashboardController_test.php` - Dashboard functionality testing
- ✅ `IntegrationTest.php` - End-to-end workflow testing

## Test Coverage

Run with coverage to see how much code is tested:
```bash
php vendor\bin\phpunit --configuration application\phpunit.xml --coverage-html application\tests\coverage\
```

Then open `application/tests/coverage/index.html` in browser.

## Best Practices

1. **Run tests frequently** during development
2. **Test individual files** when debugging specific issues
3. **Use coverage reports** to identify untested code
4. **Run all tests** before committing code
5. **Set up CI/CD** to run tests automatically

## What Tests Cover

### MainController_test.php:
- User authentication flows
- Login/logout functionality
- Session management
- Password verification
- Role-based access control
- Security features

### AuctionController_test.php:
- Auction CRUD operations
- Participant management
- Item management
- Workflow components (tatacara, persyaratan)
- Pagination and filtering
- Authentication requirements

### DashboardController_test.php:
- Dashboard data aggregation
- User-specific content
- Statistics and notifications
- Performance testing
- Mobile compatibility

### IntegrationTest.php:
- Complete authentication workflow
- End-to-end auction creation
- Data integrity testing
- Security across access levels
- Performance under load 
