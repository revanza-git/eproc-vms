<?php
echo "<h1>Direct Controller Test</h1>";

// Set up environment
define('ENVIRONMENT', 'development');
define('BASEPATH', 'system/');
define('APPPATH', 'application/');
define('FCPATH', dirname(__FILE__) . '/');

echo "<p>✅ Constants defined</p>";

// Load configuration
$config = array();
include 'application/config/config.php';
echo "<p>✅ Config loaded</p>";

// Load the HMVC system files manually
echo "<h2>Loading HMVC System:</h2>";

try {
    // Load MX files in correct order
    require_once 'application/third_party/MX/Modules.php';
    echo "<p>✅ MX Modules loaded</p>";
    
    require_once 'application/third_party/MX/Controller.php';
    echo "<p>✅ MX Controller loaded</p>";
    
    require_once 'application/third_party/MX/Router.php';
    echo "<p>✅ MX Router loaded</p>";
    
    require_once 'application/third_party/MX/Loader.php';
    echo "<p>✅ MX Loader loaded</p>";
    
} catch (Exception $e) {
    echo "<p>❌ Error loading MX files: " . $e->getMessage() . "</p>";
}

// Check if MX_Controller class exists
echo "<h2>Class Check:</h2>";
if (class_exists('MX_Controller')) {
    echo "<p>✅ MX_Controller class is available</p>";
} else {
    echo "<p>❌ MX_Controller class NOT available</p>";
}

// Try to include the Main controller file
echo "<h2>Main Controller Load Test:</h2>";
$main_path = 'application/modules/main/controllers/Main.php';

if (file_exists($main_path)) {
    echo "<p>✅ Main controller file exists</p>";
    
    try {
        include_once $main_path;
        echo "<p>✅ Main controller file included successfully</p>";
        
        if (class_exists('Main')) {
            echo "<p>✅ Main class is available</p>";
            
            // Try to instantiate (this might fail if dependencies are missing)
            try {
                echo "<p>Attempting to instantiate Main controller...</p>";
                // This will likely fail, but let's see the error
                //$main = new Main();
                //echo "<p>✅ Main controller instantiated successfully</p>";
                echo "<p>⚠️ Skipping instantiation (requires full CodeIgniter bootstrap)</p>";
                
            } catch (Exception $e) {
                echo "<p>❌ Error instantiating Main: " . $e->getMessage() . "</p>";
            }
            
        } else {
            echo "<p>❌ Main class NOT available after include</p>";
        }
        
    } catch (Exception $e) {
        echo "<p>❌ Error including Main controller: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>❌ Main controller file does not exist</p>";
}

echo "<h2>Analysis:</h2>";
echo "<p>This test shows us if the issue is:</p>";
echo "<ul>";
echo "<li>Missing MX_Controller class</li>";
echo "<li>Problems with the Main controller file</li>";
echo "<li>HMVC system loading issues</li>";
echo "</ul>";
?> 