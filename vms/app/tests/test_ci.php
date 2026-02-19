<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>CodeIgniter Bootstrap Test</h1>";

// Test basic PHP functionality
echo "<h2>1. Basic PHP Test</h2>";
echo "<p>✅ PHP is working: " . phpversion() . "</p>";

// Test file structure
echo "<h2>2. File Structure Test</h2>";
$required_files = [
    'app/system/core/CodeIgniter.php',
    'app/application/config/config.php',
    'app/application/config/database.php',
    'app/application/config/autoload.php',
    'app/index.php'
];

foreach ($required_files as $file) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✅ $file exists</p>";
    } else {
        echo "<p style='color: red;'>❌ $file MISSING</p>";
    }
}

// Test environment loading
echo "<h2>3. Environment Loading Test</h2>";
try {
    // Try to load the env_loader
    if (file_exists('app/application/env_loader.php')) {
        echo "<p>✅ env_loader.php exists</p>";
        include_once 'app/application/env_loader.php';
        
        // Test if env function exists
        if (function_exists('env')) {
            echo "<p>✅ env() function is available</p>";
            
            // Test some environment variables
            $test_vars = ['DB_HOSTNAME', 'DB_PORT', 'DB_USERNAME', 'DB_DATABASE_MAIN'];
            foreach ($test_vars as $var) {
                $value = env($var, 'NOT_SET');
                echo "<p>$var = " . htmlspecialchars($value) . "</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ env() function not available</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ env_loader.php not found</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Environment loading error: " . $e->getMessage() . "</p>";
}

// Test CodeIgniter bootstrap step by step
echo "<h2>4. CodeIgniter Bootstrap Test</h2>";

try {
    // Set the same constants as in app/index.php
    if (!defined('ENVIRONMENT')) {
        define('ENVIRONMENT', 'development');
    }
    
    echo "<p>✅ ENVIRONMENT defined: " . ENVIRONMENT . "</p>";
    
    // Define system path
    $system_path = 'app/system';
    $application_folder = 'app/application';
    
    if (realpath($system_path) !== FALSE) {
        $system_path = realpath($system_path) . '/';
    }
    
    $system_path = rtrim($system_path, '/') . '/';
    
    if (!is_dir($system_path)) {
        echo "<p style='color: red;'>❌ System directory not found: $system_path</p>";
        exit;
    }
    
    echo "<p>✅ System path verified: $system_path</p>";
    
    // Define path constants like CodeIgniter does
    define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
    define('FCPATH', dirname(__FILE__) . '/');
    define('SYSDIR', trim(strrchr(trim($system_path, '/'), '/'), '/'));
    define('BASEPATH', $system_path);
    define('APPPATH', realpath($application_folder) . '/');
    
    echo "<p>✅ CI Constants defined</p>";
    echo "<p>BASEPATH: " . BASEPATH . "</p>";
    echo "<p>APPPATH: " . APPPATH . "</p>";
    
    // Test if we can load the main CodeIgniter file
    if (file_exists(BASEPATH . 'core/CodeIgniter.php')) {
        echo "<p>✅ CodeIgniter.php exists</p>";
        
        // Try to include it (this is where errors might occur)
        echo "<p>Attempting to bootstrap CodeIgniter...</p>";
        
        // Load the framework bootstrap file
        require_once BASEPATH . 'core/CodeIgniter.php';
        
        echo "<p style='color: green;'>✅ CodeIgniter bootstrap completed successfully!</p>";
        
    } else {
        echo "<p style='color: red;'>❌ CodeIgniter.php not found in " . BASEPATH . "core/</p>";
    }
    
} catch (ParseError $e) {
    echo "<p style='color: red;'>❌ Parse Error: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . " Line: " . $e->getLine() . "</p>";
} catch (Fatal $e) {
    echo "<p style='color: red;'>❌ Fatal Error: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . " Line: " . $e->getLine() . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Exception: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . " Line: " . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
} catch (Error $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . " Line: " . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<h2>5. Memory and Execution Status</h2>";
echo "<p>Memory usage: " . memory_get_usage(true) . " bytes</p>";
echo "<p>Peak memory: " . memory_get_peak_usage(true) . " bytes</p>";
echo "<p>Execution time: " . (microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) . " seconds</p>";

?> 