<?php
echo "<h1>Simple CodeIgniter Test (No HMVC)</h1>";

// Test if we can load CodeIgniter without HMVC
define('ENVIRONMENT', 'development');
define('BASEPATH', 'system/');
define('APPPATH', 'application/');
define('FCPATH', dirname(__FILE__) . '/');

echo "<p>✅ Constants defined</p>";

// Try to load basic CodeIgniter config
$config = array();
try {
    include 'application/config/config.php';
    echo "<p>✅ Config loaded successfully</p>";
    echo "<p>Base URL: " . $config['base_url'] . "</p>";
} catch (Exception $e) {
    echo "<p>❌ Config error: " . $e->getMessage() . "</p>";
}

// Test routes
$route = array();
try {
    include 'application/config/routes.php';
    echo "<p>✅ Routes loaded</p>";
    echo "<p>Default controller: " . $route['default_controller'] . "</p>";
} catch (Exception $e) {
    echo "<p>❌ Routes error: " . $e->getMessage() . "</p>";
}

// Check if main controller file exists
$main_path = 'application/modules/main/controllers/Main.php';
if (file_exists($main_path)) {
    echo "<p>✅ Main controller file exists</p>";
    
    // Try to read first few lines to check for corruption
    $content = file_get_contents($main_path, false, null, 0, 200);
    if (strpos($content, '<?php') === 0) {
        echo "<p>✅ Main controller starts with valid PHP tag</p>";
    } else {
        echo "<p>❌ Main controller has invalid start</p>";
    }
    
    if (strpos($content, 'class Main') !== false) {
        echo "<p>✅ Main class found</p>";
    } else {
        echo "<p>❌ Main class NOT found</p>";
    }
} else {
    echo "<p>❌ Main controller file missing</p>";
}

// Test if we can create a simple controller in regular controllers directory
$regular_controller_dir = 'application/controllers/';
if (is_dir($regular_controller_dir)) {
    echo "<p>✅ Regular controllers directory exists</p>";
} else {
    echo "<p>❌ Regular controllers directory missing</p>";
}

echo "<h2>Recommendation:</h2>";
echo "<p>If this test works but HMVC doesn't, we need to fix or replace the HMVC system.</p>";
?> 