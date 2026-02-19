<?php
echo "Testing database connection (simple)...\n";

// Manual database settings from .env file
$host = 'localhost';
$port = 3307;
$user = 'root';
$pass = 'Nusantara1234';
$database = 'eproc';

echo "DB Settings:\n";
echo "Host: $host\n";
echo "Port: $port\n";
echo "User: $user\n";
echo "Database: $database\n";

// Test basic connection
echo "Testing MySQL connection...\n";

try {
    $mysqli = @new mysqli($host, $user, $pass, '', $port);
    
    if ($mysqli->connect_error) {
        echo "MySQL connection FAILED: " . $mysqli->connect_error . "\n";
        echo "Error number: " . $mysqli->connect_errno . "\n";
    } else {
        echo "MySQL connection SUCCESSFUL!\n";
        
        // Test if database exists
        echo "Testing database '$database'...\n";
        $result = $mysqli->select_db($database);
        if ($result) {
            echo "Database '$database' exists and accessible!\n";
            
            // Test a simple query
            $query_result = $mysqli->query("SELECT 1 as test");
            if ($query_result) {
                echo "Database query test SUCCESSFUL!\n";
                $query_result->close();
            } else {
                echo "Database query test FAILED: " . $mysqli->error . "\n";
            }
        } else {
            echo "Database '$database' NOT accessible: " . $mysqli->error . "\n";
        }
        
        $mysqli->close();
    }
} catch (Exception $e) {
    echo "Exception during MySQL connection: " . $e->getMessage() . "\n";
}

echo "Database test completed.\n"; 