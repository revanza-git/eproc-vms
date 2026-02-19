<?php
/**
 * Enhanced Bootstrap for VMS CodeIgniter 3 Testing
 * 
 * This enhanced bootstrap provides better testing capabilities including:
 * - Environment variable support
 * - Database testing configuration
 * - Improved error handling
 * - Test utilities and helpers
 */

// Set memory limit for tests
ini_set('memory_limit', '512M');
ini_set('max_execution_time', 0);

// Define testing environment for ci-phpunit-test
if (!defined('ENVIRONMENT')) {
    $env = getenv('CI_ENV') ?: 'testing';
    define('ENVIRONMENT', $env);
}

// Enhanced Error Reporting for Testing
switch (ENVIRONMENT) {
    case 'testing':
    case 'development':
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        break;
        
    case 'production':
        ini_set('display_errors', 0);
        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
        break;
        
    default:
        header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
        echo 'The application environment is not set correctly.';
        exit(1);
}

// Path Configuration
$system_path = '../../system';
$application_folder = '../../application';
$view_folder = '';

// Set the current directory correctly for CLI requests
chdir(dirname(__FILE__));

// Resolve system path
if (($_temp = realpath($system_path)) !== FALSE) {
    $system_path = $_temp . DIRECTORY_SEPARATOR;
} else {
    $system_path = strtr(
        rtrim($system_path, '/\\'),
        '/\\',
        DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR
    ) . DIRECTORY_SEPARATOR;
}

// Validate system path
if (!is_dir($system_path)) {
    header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
    echo 'Your system folder path does not appear to be set correctly.';
    exit(3);
}

// Define Constants
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
define('TESTPATH', __DIR__ . DIRECTORY_SEPARATOR);
define('BASEPATH', $system_path);
define('FCPATH', realpath(dirname(__FILE__) . '/../..') . DIRECTORY_SEPARATOR);
define('SYSDIR', basename(BASEPATH));

// Application Path
if (is_dir($application_folder)) {
    if (($_temp = realpath($application_folder)) !== FALSE) {
        $application_folder = $_temp;
    } else {
        $application_folder = strtr(
            rtrim($application_folder, '/\\'),
            '/\\',
            DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR
        );
    }
} elseif (is_dir(BASEPATH . $application_folder . DIRECTORY_SEPARATOR)) {
    $application_folder = BASEPATH . strtr(
        trim($application_folder, '/\\'),
        '/\\',
        DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR
    );
} else {
    header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
    echo 'Your application folder path does not appear to be set correctly.';
    exit(3);
}

define('APPPATH', $application_folder . DIRECTORY_SEPARATOR);

// View Path
if (!isset($view_folder[0]) && is_dir(APPPATH . 'views' . DIRECTORY_SEPARATOR)) {
    $view_folder = APPPATH . 'views';
} elseif (is_dir($view_folder)) {
    if (($_temp = realpath($view_folder)) !== FALSE) {
        $view_folder = $_temp;
    } else {
        $view_folder = strtr(
            rtrim($view_folder, '/\\'),
            '/\\',
            DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR
        );
    }
} elseif (is_dir(APPPATH . $view_folder . DIRECTORY_SEPARATOR)) {
    $view_folder = APPPATH . strtr(
        trim($view_folder, '/\\'),
        '/\\',
        DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR
    );
} else {
    header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
    echo 'Your view folder path does not appear to be set correctly.';
    exit(3);
}

define('VIEWPATH', $view_folder . DIRECTORY_SEPARATOR);

// CI PHPUnit Test Path
if (is_file(TESTPATH . '_ci_phpunit_test' . DIRECTORY_SEPARATOR . 'CIPHPUnitTest.php')) {
    define('CI_PHPUNIT_TESTPATH', TESTPATH . '_ci_phpunit_test' . DIRECTORY_SEPARATOR);
} else {
    define('CI_PHPUNIT_TESTPATH', implode(
        DIRECTORY_SEPARATOR,
        [dirname(APPPATH), 'vendor', 'kenjis', 'ci-phpunit-test', 'application', 'tests', '_ci_phpunit_test']
    ) . DIRECTORY_SEPARATOR);
}

// Enhanced Test Environment Setup
class TestEnvironment 
{
    /**
     * Initialize test environment with enhanced capabilities
     */
    public static function init()
    {
        // Set up test database configuration
        self::setupTestDatabase();
        
        // Initialize test helpers
        self::loadTestHelpers();
        
        // Setup test fixtures
        self::setupFixtures();
        
        // Initialize code coverage if available
        self::initCodeCoverage();
    }
    
    /**
     * Setup test database configuration
     */
    private static function setupTestDatabase()
    {
        // Override database configuration for testing
        $test_db = [
            'dsn'       => '',
            'hostname'  => getenv('DB_HOSTNAME') ?: 'localhost',
            'username'  => getenv('DB_USERNAME') ?: 'root',
            'password'  => getenv('DB_PASSWORD') ?: '',
            'database'  => getenv('DB_DATABASE') ?: 'vms_test',
            'dbdriver'  => 'mysqli',
            'dbprefix'  => '',
            'pconnect'  => FALSE,
            'db_debug'  => TRUE,
            'cache_on'  => FALSE,
            'cachedir'  => '',
            'char_set'  => 'utf8',
            'dbcollat'  => 'utf8_general_ci',
            'swap_pre'  => '',
            'encrypt'   => FALSE,
            'compress'  => FALSE,
            'stricton'  => FALSE,
            'failover'  => [],
            'save_queries' => TRUE
        ];
        
        // Store test database configuration
        $GLOBALS['test_db_config'] = $test_db;
    }
    
    /**
     * Load additional test helpers
     */
    private static function loadTestHelpers()
    {
        // Load custom test utilities
        require_once TESTPATH . 'TestUtilities.php';
    }
    
    /**
     * Setup test fixtures directory
     */
    private static function setupFixtures()
    {
        $fixtures_dir = TESTPATH . 'fixtures';
        if (!is_dir($fixtures_dir)) {
            mkdir($fixtures_dir, 0755, true);
        }
    }
    
    /**
     * Initialize code coverage if Xdebug is available
     */
    private static function initCodeCoverage()
    {
        if (extension_loaded('xdebug')) {
            ini_set('xdebug.coverage_enable', 1);
            echo "\n[INFO] Xdebug loaded - Code coverage enabled\n";
        } else {
            echo "\n[WARNING] Xdebug not loaded - Code coverage disabled\n";
        }
    }
}

// Initialize CI PHPUnit Test
require CI_PHPUNIT_TESTPATH . '/CIPHPUnitTest.php';

// Initialize with enhanced autoloading
CIPHPUnitTest::init([
    // Directories for autoloading
    APPPATH . 'models',
    APPPATH . 'libraries', 
    APPPATH . 'controllers',
    APPPATH . 'modules',
    APPPATH . 'helpers'
]);

// Initialize enhanced test environment
TestEnvironment::init();

// Set global test configuration
$GLOBALS['test_config'] = [
    'app_path' => APPPATH,
    'test_path' => TESTPATH,
    'environment' => ENVIRONMENT,
    'coverage_enabled' => extension_loaded('xdebug'),
    'db_config' => $GLOBALS['test_db_config'] ?? null
];

echo "\n[INFO] Enhanced Test Bootstrap initialized for VMS Testing\n";
echo "[INFO] Environment: " . ENVIRONMENT . "\n";
echo "[INFO] Test Path: " . TESTPATH . "\n";
echo "[INFO] Application Path: " . APPPATH . "\n\n"; 