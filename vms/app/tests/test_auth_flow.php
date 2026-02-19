<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>VMS to Main Project Authentication Flow Test</h1>";

echo "<h2>1. Database Connection Test</h2>";

$dbHost = getenv('E2E_DB_HOST') ?: 'localhost';
$dbPort = (int) (getenv('E2E_DB_PORT') ?: 3307);
$dbName = getenv('E2E_DB_NAME') ?: 'eproc';
$dbUser = getenv('E2E_DB_USER') ?: 'root';
$dbPass = getenv('E2E_DB_PASSWORD') ?: '';

try {
    // Test VMS database connection
    $vms_pdo = new PDO("mysql:host={$dbHost};port={$dbPort};dbname={$dbName}", $dbUser, $dbPass);
    $vms_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green;'>✅ VMS Database (eproc) connection successful</p>";
    
    // Check if ms_key_value table exists
    $table_check = $vms_pdo->query("SHOW TABLES LIKE 'ms_key_value'");
    if ($table_check->rowCount() > 0) {
        echo "<p style='color: green;'>✅ ms_key_value table exists</p>";
        
        // Show table structure
        $structure = $vms_pdo->query("DESCRIBE ms_key_value")->fetchAll(PDO::FETCH_ASSOC);
        echo "<h3>Current Table Structure:</h3>";
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        
        $has_created_at = false;
        $has_deleted_at = false;
        
        foreach ($structure as $field) {
            echo "<tr>";
            foreach ($field as $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
            
            if ($field['Field'] === 'created_at') $has_created_at = true;
            if ($field['Field'] === 'deleted_at') $has_deleted_at = true;
        }
        echo "</table>";
        
        // Check if we need to add missing columns
        if (!$has_created_at || !$has_deleted_at) {
            echo "<p style='color: orange;'>⚠️ Missing required columns. Adding them...</p>";
            
            try {
                if (!$has_created_at) {
                    $vms_pdo->exec("ALTER TABLE ms_key_value ADD COLUMN created_at TIMESTAMP NULL DEFAULT NULL");
                    echo "<p style='color: green;'>✅ Added created_at column</p>";
                }
                
                if (!$has_deleted_at) {
                    $vms_pdo->exec("ALTER TABLE ms_key_value ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL");
                    echo "<p style='color: green;'>✅ Added deleted_at column</p>";
                }
                
                // Add indexes if they don't exist
                $vms_pdo->exec("ALTER TABLE ms_key_value ADD INDEX IF NOT EXISTS idx_key (`key`)");
                $vms_pdo->exec("ALTER TABLE ms_key_value ADD INDEX IF NOT EXISTS idx_deleted_at (deleted_at)");
                echo "<p style='color: green;'>✅ Added database indexes</p>";
                
            } catch (Exception $e) {
                echo "<p style='color: red;'>❌ Error updating table: " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<p style='color: green;'>✅ Table structure is correct</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ ms_key_value table does not exist</p>";
        echo "<p><strong>Creating table...</strong></p>";
        
        $create_sql = "
        CREATE TABLE `ms_key_value` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `key` varchar(255) NOT NULL,
            `value` text NOT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `deleted_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `key` (`key`),
            KEY `deleted_at` (`deleted_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ";
        
        if ($vms_pdo->exec($create_sql)) {
            echo "<p style='color: green;'>✅ ms_key_value table created successfully</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to create ms_key_value table</p>";
        }
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
}

echo "<h2>2. Manual Key Generation and Insertion Test</h2>";

try {
    // Generate test key like the VMS system would
    $key = uniqid() . time() . bin2hex(openssl_random_pseudo_bytes(5));
    echo "<p>Generated key: <code>$key</code></p>";
    
    // Create test admin data that matches the main project's expectations
    $test_admin_data = array(
        "name" => "Test Admin User",
        "id_user" => 999,
        "id_role" => 2,
        "id_division" => 1,
        "email" => "test.admin@example.com",
        "photo_profile" => "default.jpg",
        "app_type" => 2
    );
    
    $json_data = json_encode($test_admin_data);
    echo "<p>Test admin data JSON:</p>";
    echo "<pre>" . htmlspecialchars($json_data) . "</pre>";
    
    // Insert into database
    if (isset($vms_pdo)) {
        try {
            // Check what columns exist in the table
            $columns = $vms_pdo->query("DESCRIBE ms_key_value")->fetchAll(PDO::FETCH_COLUMN);
            
            $has_created_at = in_array('created_at', $columns);
            
            // Build insert query based on available columns
            if ($has_created_at) {
                $stmt = $vms_pdo->prepare("INSERT INTO ms_key_value (`key`, `value`, `created_at`) VALUES (?, ?, ?)");
                $result = $stmt->execute([$key, $json_data, date('Y-m-d H:i:s')]);
            } else {
                $stmt = $vms_pdo->prepare("INSERT INTO ms_key_value (`key`, `value`) VALUES (?, ?)");
                $result = $stmt->execute([$key, $json_data]);
            }
            
            if ($result) {
                echo "<p style='color: green;'>✅ Test key inserted successfully</p>";
                
                // Generate the URL that would be used
                $test_url = "http://local.eproc.web.com/internal/from_eks?key=" . $key;
                echo "<p><strong>Test URL:</strong></p>";
                echo "<p><a href='$test_url' target='_blank' style='background: #007cba; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>🔗 Test Authentication Flow</a></p>";
                echo "<p><small>Click the link above to test the authentication flow</small></p>";
                
            } else {
                echo "<p style='color: red;'>❌ Failed to insert test key</p>";
            }
            
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Error inserting test key: " . $e->getMessage() . "</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Key generation test failed: " . $e->getMessage() . "</p>";
}

echo "<h2>3. Check Existing Keys</h2>";

if (isset($vms_pdo)) {
    try {
        // First check what columns exist
        $columns = $vms_pdo->query("DESCRIBE ms_key_value")->fetchAll(PDO::FETCH_COLUMN);
        
        $has_created_at = in_array('created_at', $columns);
        $has_deleted_at = in_array('deleted_at', $columns);
        
        // Build query based on available columns
        $select_fields = "`key`, LEFT(`value`, 100) as value_preview";
        if ($has_created_at) $select_fields .= ", `created_at`";
        if ($has_deleted_at) $select_fields .= ", `deleted_at`";
        
        $order_by = $has_created_at ? "ORDER BY created_at DESC" : "ORDER BY `key` DESC";
        
        $stmt = $vms_pdo->query("SELECT $select_fields FROM ms_key_value $order_by LIMIT 10");
        $keys = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($keys) {
            echo "<p>Recent keys in database:</p>";
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            
            // Dynamic headers
            echo "<tr><th>Key</th>";
            if ($has_created_at) echo "<th>Created</th>";
            if ($has_deleted_at) echo "<th>Status</th>";
            echo "<th>Value Preview</th></tr>";
            
            foreach ($keys as $key_row) {
                $deleted_status = "⏳ Active";
                $row_style = "";
                
                if ($has_deleted_at && !empty($key_row['deleted_at'])) {
                    $deleted_status = "✅ Used";
                    $row_style = "background-color: #f8f9fa;";
                }
                
                echo "<tr style='$row_style'>";
                echo "<td><code>" . htmlspecialchars($key_row['key']) . "</code></td>";
                
                if ($has_created_at) {
                    echo "<td>" . htmlspecialchars($key_row['created_at'] ?? 'N/A') . "</td>";
                }
                
                if ($has_deleted_at) {
                    echo "<td>$deleted_status</td>";
                }
                
                echo "<td>" . htmlspecialchars($key_row['value_preview']) . "...</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No keys found in database</p>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error checking existing keys: " . $e->getMessage() . "</p>";
    }
}

echo "<h2>4. Test Main Project Connectivity</h2>";

$main_url = "http://local.eproc.web.com/internal/from_eks";
echo "<p>Testing connectivity to: <code>$main_url</code></p>";

$context = stream_context_create([
    'http' => [
        'timeout' => 10,
        'method' => 'GET'
    ]
]);

$result = @file_get_contents($main_url, false, $context);
if ($result !== false) {
    echo "<p style='color: green;'>✅ Main project is accessible</p>";
    echo "<p>Response length: " . strlen($result) . " bytes</p>";
    
    // Check if it redirects (looking for redirect behavior)
    if (strpos($result, 'Location:') !== false || strpos($result, 'redirect') !== false) {
        echo "<p style='color: blue;'>ℹ️ Main project appears to redirect requests without keys (expected behavior)</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Cannot connect to main project</p>";
    $error = error_get_last();
    if ($error) {
        echo "<p>Error: " . htmlspecialchars($error['message']) . "</p>";
    }
}

echo "<h2>5. Admin Session Simulation</h2>";

echo "<p>Simulating admin login data that would be passed to generate_admin_auth_for_main():</p>";

$sample_admin_session = array(
    'name' => 'Admin User',
    'id_user' => 123,
    'id_role' => 2,
    'id_division' => 1,
    'email' => 'admin@company.com',
    'photo_profile' => 'admin.jpg',
    'app_type' => 2
);

echo "<pre>" . print_r($sample_admin_session, true) . "</pre>";

echo "<h2>6. Next Steps</h2>";
echo "<ul>";
echo "<li><strong>✅ If test key insertion worked:</strong> Try the test authentication link above</li>";
echo "<li><strong>✅ If main project is accessible:</strong> The authentication flow should work</li>";
echo "<li><strong>❌ If there are errors:</strong> Check database permissions and table structure</li>";
echo "<li><strong>🔄 To test real flow:</strong> Login as admin in VMS and check logs</li>";
echo "</ul>";

echo "<div style='background: #e8f4f8; padding: 15px; margin: 20px 0; border-left: 4px solid #007cba;'>";
echo "<strong>🔍 Debugging tip:</strong> Check the application logs at:<br>";
echo "<code>app/application/logs/log-" . date('Y-m-d') . ".php</code>";
echo "</div>";

?>
