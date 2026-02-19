<?php
/**
 * Create Test Users Script
 * This script creates the admin test user required for the test login functionality
 */

echo "<h1>🧪 Creating Test Users for E-Procurement System</h1>";
echo "<p>This script creates the admin test user required for test login functionality.</p>";

// Database configuration (from main/application/config/database.php)
$db_config = [
    'hostname' => '127.0.0.1',
    'port' => 3307,
    'username' => 'root',
    'password' => 'Nusantara1234',
    'database' => 'eproc'
];

try {
    echo "<h2>🔍 Connecting to Database</h2>";
    
    // Create database connection
    $mysqli = new mysqli(
        $db_config['hostname'], 
        $db_config['username'], 
        $db_config['password'], 
        $db_config['database'], 
        $db_config['port']
    );
    
    if ($mysqli->connect_error) {
        throw new Exception("Connection failed: " . $mysqli->connect_error);
    }
    
    echo "<p>✅ Successfully connected to eproc database</p>";
    
    echo "<h2>📋 Checking Existing Test Admin User</h2>";
    
    // Check if test admin already exists
    $stmt = $mysqli->prepare("SELECT * FROM ms_admin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $email = 'admin@test.com';
    $stmt->execute();
    $result = $stmt->get_result();
    $existing_admin = $result->fetch_assoc();
    
    if ($existing_admin) {
        echo "<p>✅ Test admin user already exists!</p>";
        echo "<div style='background: #d4edda; padding: 15px; border-left: 4px solid #28a745; margin: 15px 0;'>";
        echo "<strong>Existing Admin User Details:</strong><br>";
        echo "ID: " . $existing_admin['id'] . "<br>";
        echo "Name: " . htmlspecialchars($existing_admin['name']) . "<br>";
        echo "Email: " . htmlspecialchars($existing_admin['email']) . "<br>";
        if (isset($existing_admin['id_division'])) echo "Division ID: " . $existing_admin['id_division'] . "<br>";
        if (isset($existing_admin['id_role'])) echo "Role ID: " . $existing_admin['id_role'] . "<br>";
        echo "</div>";
        
        // Check if login record exists
        $stmt2 = $mysqli->prepare("SELECT * FROM ms_login WHERE id_user = ? AND type = 'admin'");
        $stmt2->bind_param("i", $existing_admin['id']);
        $stmt2->execute();
        $login_result = $stmt2->get_result();
        $existing_login = $login_result->fetch_assoc();
        
        if ($existing_login) {
            echo "<p>✅ Login credentials already exist!</p>";
            echo "<div style='background: #d1ecf1; padding: 15px; border-left: 4px solid #bee5eb; margin: 15px 0;'>";
            echo "<strong>Login Details:</strong><br>";
            echo "Username: " . htmlspecialchars($existing_login['username']) . "<br>";
            echo "Type: " . htmlspecialchars($existing_login['type']) . "<br>";
            if (isset($existing_login['type_app'])) {
                echo "App Type: " . $existing_login['type_app'] . "<br>";
            }
            echo "</div>";
        } else {
            echo "<p>⚠️ Admin user exists but login credentials missing. Creating login record...</p>";
            create_login_record($mysqli, $existing_admin['id']);
        }
    } else {
        echo "<p>⚠️ Test admin user not found. Creating new admin user...</p>";
        create_admin_user($mysqli);
    }
    
    echo "<h2>🧪 Test Links</h2>";
    echo "<ul>";
    echo "<li><a href='/main/test_login/direct_admin_login' target='_blank'>🔐 Test Direct Admin Login</a></li>";
    echo "<li><a href='/main/test_login' target='_blank'>📋 Test Login Menu</a></li>";
    echo "<li><a href='/main/test_login/form' target='_blank'>📝 Manual Login Form</a></li>";
    echo "</ul>";
    
    echo "<div style='background: #d1ecf1; padding: 15px; border-left: 4px solid #bee5eb; margin: 15px 0;'>";
    echo "<strong>📋 Test Credentials:</strong><br>";
    echo "Username: <code>admin</code><br>";
    echo "Password: <code>admin123</code><br>";
    echo "Email: <code>admin@test.com</code>";
    echo "</div>";
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-left: 4px solid #dc3545; margin: 15px 0;'>";
    echo "<strong>❌ Error:</strong> " . htmlspecialchars($e->getMessage());
    echo "</div>";
}

function create_admin_user($mysqli) {
    echo "<h3>🛠️ Creating Admin User</h3>";
    
    // Use explicit column names and values to avoid mapping issues
    $sql = "INSERT INTO ms_admin (name, email, id_division, id_role, id_sbu, entry_stamp, del) VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $mysqli->error);
    }
    
    $name = 'Test Administrator';
    $email = 'admin@test.com';
    $id_division = 1;
    $id_role = 1;
    $id_sbu = 1;
    $entry_stamp = date('Y-m-d H:i:s');
    $del = 0;
    
    $stmt->bind_param("ssiiisi", $name, $email, $id_division, $id_role, $id_sbu, $entry_stamp, $del);
    
    if ($stmt->execute()) {
        $admin_id = $mysqli->insert_id;
        echo "<p>✅ Admin user created with ID: $admin_id</p>";
        echo "<p>Name: $name</p>";
        echo "<p>Email: $email</p>";
        
        // Create login record
        create_login_record($mysqli, $admin_id);
        
    } else {
        throw new Exception("Failed to create admin user: " . $stmt->error);
    }
}

function create_login_record($mysqli, $admin_id) {
    echo "<h3>🔐 Creating Login Credentials</h3>";
    
    // Use explicit column names and values to avoid mapping issues
    $sql = "INSERT INTO ms_login (id_user, username, password, type, type_app, entry_stamp) VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $mysqli->error);
    }
    
    $username = 'admin';
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $type = 'admin';
    $type_app = 2;  // App type 2 for main application
    $entry_stamp = date('Y-m-d H:i:s');
    
    $stmt->bind_param("issssi", $admin_id, $username, $password, $type, $type_app, $entry_stamp);
    
    if ($stmt->execute()) {
        $login_id = $mysqli->insert_id;
        echo "<p>✅ Login credentials created with ID: $login_id</p>";
        echo "<p><strong>Username:</strong> admin</p>";
        echo "<p><strong>Password:</strong> admin123</p>";
    } else {
        throw new Exception("Failed to create login credentials: " . $stmt->error);
    }
}

echo "<hr>";
echo "<p><strong>Script completed successfully!</strong> You can now test the admin login functionality.</p>";
?> 