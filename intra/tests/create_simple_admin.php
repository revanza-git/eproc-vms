<?php
/**
 * Simple Admin User Creation
 * Creates admin user without assuming specific column names
 */

try {
    $pdo = new PDO('mysql:host=localhost;port=3307;dbname=eproc', 'root', 'Nusantara1234');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>🔧 Simple Admin User Creation</h2>";
    
    // First, let's see what columns actually exist
    echo "<h3>📋 ms_admin Table Columns</h3>";
    $stmt = $pdo->query("DESCRIBE ms_admin");
    $columns = [];
    echo "<ul>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $columns[] = $row['Field'];
        echo "<li><strong>{$row['Field']}</strong> - {$row['Type']}</li>";
    }
    echo "</ul>";
    
    echo "<h3>🔐 ms_login Table Columns</h3>";
    $stmt = $pdo->query("DESCRIBE ms_login");
    $login_columns = [];
    echo "<ul>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $login_columns[] = $row['Field'];
        echo "<li><strong>{$row['Field']}</strong> - {$row['Type']}</li>";
    }
    echo "</ul>";
    
    // Check if test admin already exists
    $stmt = $pdo->prepare("SELECT * FROM ms_admin WHERE email = ? OR name = ?");
    $stmt->execute(['admin@test.com', 'Test Administrator']);
    $existing = $stmt->fetch();
    
    if ($existing) {
        echo "<p>✅ Test admin user already exists!</p>";
        echo "<pre>" . print_r($existing, true) . "</pre>";
    } else {
        echo "<h3>🛠️ Creating Admin User</h3>";
        
        // Build INSERT query based on available columns
        $admin_values = [];
        $admin_placeholders = [];
        
        if (in_array('name', $columns)) {
            $admin_values[] = 'Test Administrator';
            $admin_placeholders[] = '?';
        }
        if (in_array('email', $columns)) {
            $admin_values[] = 'admin@test.com';
            $admin_placeholders[] = '?';
        }
        if (in_array('id_division', $columns)) {
            $admin_values[] = 1;
            $admin_placeholders[] = '?';
        }
        if (in_array('id_role', $columns)) {
            $admin_values[] = 1;
            $admin_placeholders[] = '?';
        }
        
        // Basic insert for ms_admin
        $admin_fields = array_slice($columns, 1, count($admin_values)); // Skip ID field
        $sql = "INSERT INTO ms_admin (" . implode(', ', $admin_fields) . ") VALUES (" . implode(', ', $admin_placeholders) . ")";
        
        echo "<p>SQL: $sql</p>";
        echo "<p>Values: " . implode(', ', $admin_values) . "</p>";
        
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($admin_values);
            $admin_id = $pdo->lastInsertId();
            echo "<p>✅ Admin user created with ID: $admin_id</p>";
            
            // Create login entry
            $login_values = [];
            if (in_array('id_user', $login_columns)) {
                $login_values[] = $admin_id;
            }
            if (in_array('username', $login_columns)) {
                $login_values[] = 'admin';
            }
            if (in_array('password', $login_columns)) {
                $login_values[] = password_hash('admin123', PASSWORD_DEFAULT);
            }
            if (in_array('type', $login_columns)) {
                $login_values[] = 'admin';
            }
            
            $login_fields = array_slice($login_columns, 1, count($login_values)); // Skip ID field
            $login_sql = "INSERT INTO ms_login (" . implode(', ', $login_fields) . ") VALUES (" . str_repeat('?, ', count($login_values) - 1) . "?)";
            
            echo "<p>Login SQL: $login_sql</p>";
            
            $stmt = $pdo->prepare($login_sql);
            $stmt->execute($login_values);
            
            echo "<p>✅ Login credentials created!</p>";
            echo "<p><strong>Username:</strong> admin</p>";
            echo "<p><strong>Password:</strong> admin123</p>";
            
        } catch (Exception $e) {
            echo "<p>❌ Error creating user: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
    
    echo "<hr>";
    echo "<p><strong>Test Links:</strong></p>";
    echo "<ul>";
    echo "<li><a href='http://local.eproc.intra.com/main/test_login/direct_admin_login'>🔐 Test Direct Admin Login</a></li>";
    echo "<li><a href='http://local.eproc.intra.com/test_login_page.html'>📋 Test Login Interface</a></li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p>❌ Database Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?> 