<?php
echo "<h1>HMVC Debug Test</h1>";

define('ENVIRONMENT', 'development');
define('BASEPATH', 'system/');
define('APPPATH', 'application/');
define('FCPATH', dirname(__FILE__) . '/');

echo "<p>✅ Constants defined</p>";

// Load basic config
$config = array();
include 'application/config/config.php';
echo "<p>✅ Config loaded - Base URL: " . $config['base_url'] . "</p>";

// Check modules_locations
if (isset($config['modules_locations'])) {
    echo "<p>✅ HMVC modules_locations defined:</p>";
    foreach ($config['modules_locations'] as $path => $offset) {
        echo "<p>&nbsp;&nbsp;- $path => $offset</p>";
        $real_path = realpath($path);
        if ($real_path) {
            echo "<p>&nbsp;&nbsp;&nbsp;&nbsp;Real path: $real_path</p>";
            if (is_dir($real_path)) {
                echo "<p>&nbsp;&nbsp;&nbsp;&nbsp;✅ Directory exists</p>";
            } else {
                echo "<p>&nbsp;&nbsp;&nbsp;&nbsp;❌ Directory does NOT exist</p>";
            }
        } else {
            echo "<p>&nbsp;&nbsp;&nbsp;&nbsp;❌ Path cannot be resolved</p>";
        }
    }
} else {
    echo "<p>❌ HMVC modules_locations NOT defined</p>";
}

// Check main controller specifically
$main_controller_path = 'application/modules/main/controllers/Main.php';
echo "<h2>Main Controller Check:</h2>";
if (file_exists($main_controller_path)) {
    echo "<p>✅ Main controller file exists: $main_controller_path</p>";
    
    // Try to read and check content
    $content = file_get_contents($main_controller_path);
    $lines = explode("\n", $content);
    
    echo "<p>File size: " . strlen($content) . " bytes</p>";
    echo "<p>Lines: " . count($lines) . "</p>";
    
    // Check first few lines
    echo "<h3>First 10 lines:</h3>";
    echo "<pre>";
    for ($i = 0; $i < min(10, count($lines)); $i++) {
        echo ($i + 1) . ": " . htmlspecialchars($lines[$i]) . "\n";
    }
    echo "</pre>";
    
    // Look for class definition
    if (strpos($content, 'class Main') !== false) {
        echo "<p>✅ 'class Main' found in file</p>";
    } else {
        echo "<p>❌ 'class Main' NOT found in file</p>";
    }
    
    // Look for extends
    if (strpos($content, 'extends MX_Controller') !== false) {
        echo "<p>✅ Extends MX_Controller (HMVC)</p>";
    } elseif (strpos($content, 'extends CI_Controller') !== false) {
        echo "<p>⚠️ Extends CI_Controller (Standard)</p>";
    } else {
        echo "<p>❌ No controller extension found</p>";
    }
    
} else {
    echo "<p>❌ Main controller file does NOT exist</p>";
}

// Check if MX files are working
echo "<h2>HMVC MX Files Check:</h2>";
$mx_files = [
    'application/third_party/MX/Modules.php',
    'application/third_party/MX/Router.php',
    'application/third_party/MX/Loader.php',
    'application/third_party/MX/Controller.php'
];

foreach ($mx_files as $file) {
    if (file_exists($file)) {
        echo "<p>✅ $file exists</p>";
        
        // Quick syntax check
        $content = file_get_contents($file, false, null, 0, 100);
        if (strpos($content, '<?php') === 0) {
            echo "<p>&nbsp;&nbsp;✅ Starts with valid PHP tag</p>";
        } else {
            echo "<p>&nbsp;&nbsp;❌ Invalid start</p>";
        }
    } else {
        echo "<p>❌ $file missing</p>";
    }
}

// Check routes
$route = array();
include 'application/config/routes.php';
echo "<h2>Routes Check:</h2>";
echo "<p>Default controller: " . $route['default_controller'] . "</p>";

// Try to simulate HMVC loading
echo "<h2>HMVC Simulation:</h2>";
echo "<p>Trying to locate main controller through HMVC...</p>";

// Check if we can include MX files
try {
    echo "<p>Attempting to load MX system...</p>";
    
    // Try to include core MX files
    if (file_exists('application/third_party/MX/Modules.php')) {
        // Don't actually include to avoid conflicts, just check syntax
        $mx_content = file_get_contents('application/third_party/MX/Modules.php');
        if (strpos($mx_content, 'class Modules') !== false) {
            echo "<p>✅ MX Modules class found</p>";
        } else {
            echo "<p>❌ MX Modules class NOT found</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error loading MX: " . $e->getMessage() . "</p>";
}

echo "<h2>Recommendation:</h2>";
echo "<p>This debug should show us exactly where the HMVC system is failing.</p>";
echo "<p><a href='index.php/main'>Try direct main controller access</a></p>";
echo "<p><a href='index.php/welcome'>Try welcome controller (should work)</a></p>";
?> 