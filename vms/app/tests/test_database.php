<?php
echo "<h1>Database Connection Test</h1>";

// Load environment variables manually
if (file_exists('../.env')) {
    $env_content = file_get_contents('../.env');
    $lines = explode("\n", $env_content);
    
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) continue;
        
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Remove quotes
            if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
                (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
                $value = substr($value, 1, -1);
            }
            
            $_ENV[$key] = $value;
        }
    }
    echo "<p>✅ .env file loaded manually</p>";
} else {
    echo "<p>❌ .env file not found</p>";
}

// Get database settings
$host = isset($_ENV['DB_HOSTNAME']) ? $_ENV['DB_HOSTNAME'] : 'localhost';
$port = isset($_ENV['DB_PORT']) ? $_ENV['DB_PORT'] : 3307;
$user = isset($_ENV['DB_USERNAME']) ? $_ENV['DB_USERNAME'] : 'root';
$pass = isset($_ENV['DB_PASSWORD']) ? $_ENV['DB_PASSWORD'] : '';
$database = isset($_ENV['DB_DATABASE_MAIN']) ? $_ENV['DB_DATABASE_MAIN'] : 'eproc';

echo "<h2>Database Configuration:</h2>";
echo "<p>Host: $host</p>";
echo "<p>Port: $port</p>";
echo "<p>User: $user</p>";
echo "<p>Password: " . (empty($pass) ? '(empty)' : '***') . "</p>";
echo "<p>Database: $database</p>";

echo "<h2>Testing MySQL Connection:</h2>";

try {
    $mysqli = @new mysqli($host, $user, $pass, '', $port);
    
    if ($mysqli->connect_error) {
        echo "<p style='color: red;'>❌ MySQL connection FAILED</p>";
        echo "<p>Error: " . $mysqli->connect_error . "</p>";
        echo "<p>Error Code: " . $mysqli->connect_errno . "</p>";
        
        // Common solutions
        echo "<h3>Possible Solutions:</h3>";
        echo "<ul>";
        echo "<li>Check if MySQL/MariaDB is running</li>";
        echo "<li>Verify the port number (3307 vs 3306)</li>";
        echo "<li>Check username/password</li>";
        echo "<li>Verify database exists</li>";
        echo "</ul>";
        
    } else {
        echo "<p style='color: green;'>✅ MySQL connection SUCCESSFUL!</p>";
        
        // Test database selection
        if ($mysqli->select_db($database)) {
            echo "<p style='color: green;'>✅ Database '$database' accessible!</p>";
            
            // Test a simple query
            $result = $mysqli->query("SELECT 1 as test");
            if ($result) {
                echo "<p style='color: green;'>✅ Database queries work!</p>";
                $result->close();
            } else {
                echo "<p style='color: red;'>❌ Database query failed: " . $mysqli->error . "</p>";
            }
            
            // Test if ms_login table exists (used by the application)
            $result = $mysqli->query("SHOW TABLES LIKE 'ms_login'");
            if ($result && $result->num_rows > 0) {
                echo "<p style='color: green;'>✅ ms_login table exists!</p>";
            } else {
                echo "<p style='color: orange;'>⚠ ms_login table not found (application may need database setup)</p>";
            }
            
        } else {
            echo "<p style='color: red;'>❌ Cannot access database '$database': " . $mysqli->error . "</p>";
        }
        
        $mysqli->close();
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Exception: " . $e->getMessage() . "</p>";
}

echo "<p><strong>Conclusion:</strong> If database connection fails, this is likely why CodeIgniter shows 404 errors.</p>";
?> 