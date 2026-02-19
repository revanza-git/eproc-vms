<?php
echo "<h1>HMVC Routing Debug Test</h1>";

// Set up CodeIgniter constants
define('ENVIRONMENT', 'development');
define('BASEPATH', 'system/');
define('APPPATH', 'application/');
define('FCPATH', dirname(__FILE__) . '/');

// Load config
$config = array();
include 'application/config/config.php';
echo "<p>✅ Config loaded</p>";

// Load the HMVC system
require_once 'application/third_party/MX/Modules.php';
echo "<p>✅ MX Modules loaded</p>";

// Check module locations
echo "<h2>Module Locations:</h2>";
if (isset(Modules::$locations)) {
    echo "<pre>" . print_r(Modules::$locations, true) . "</pre>";
} else {
    echo "<p>❌ Modules::\$locations not set</p>";
}

// Check if main module directory exists
$main_controller_path = 'application/modules/main/controllers/Main.php';
echo "<h2>Main Controller Check:</h2>";
if (file_exists($main_controller_path)) {
    echo "<p>✅ Main controller exists at: $main_controller_path</p>";
} else {
    echo "<p>❌ Main controller NOT found at: $main_controller_path</p>";
}

// Test HMVC module location detection
echo "<h2>HMVC Location Test:</h2>";
$module_dir = 'application/modules/main/controllers/';
if (is_dir($module_dir)) {
    echo "<p>✅ Main module controllers directory exists</p>";
    $files = scandir($module_dir);
    echo "<p>Contents: " . implode(', ', $files) . "</p>";
} else {
    echo "<p>❌ Main module controllers directory NOT found</p>";
}

// Test the routes configuration
echo "<h2>Routes Configuration:</h2>";
$routes = array();
include 'application/config/routes.php';
echo "<p>Default controller: " . $route['default_controller'] . "</p>";
echo "<p>404 override: " . $route['404_override'] . "</p>";

// Test if we can manually instantiate the main controller
echo "<h2>Manual Controller Test:</h2>";
try {
    if (file_exists($main_controller_path)) {
        // This is just a file check, not actual instantiation
        echo "<p>✅ Main.php file is readable</p>";
        
        // Check the beginning of the file for issues
        $file_content = file_get_contents($main_controller_path, false, null, 0, 500);
        if (strpos($file_content, 'class Main') !== false) {
            echo "<p>✅ Main class definition found</p>";
        } else {
            echo "<p>❌ Main class definition NOT found in file</p>";
        }
    }
} catch (Exception $e) {
    echo "<p>❌ Error testing Main controller: " . $e->getMessage() . "</p>";
}

?> 