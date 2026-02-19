<?php
/**
 * Check Admin Tables Structure
 * This script examines the structure of admin-related tables in the eproc database
 */

try {
    $pdo = new PDO('mysql:host=localhost;port=3307;dbname=eproc', 'root', 'Nusantara1234');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>🔍 Database Table Analysis</h2>";
    
    // Check if ms_admin table exists
    echo "<h3>📋 ms_admin Table Structure</h3>";
    try {
        $stmt = $pdo->query("DESCRIBE ms_admin");
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr style='background-color: #f0f0f0;'><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>{$row['Field']}</td>";
            echo "<td>{$row['Type']}</td>";
            echo "<td>{$row['Null']}</td>";
            echo "<td>{$row['Key']}</td>";
            echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Show existing data
        echo "<h4>📊 Existing ms_admin Records</h4>";
        $stmt = $pdo->query("SELECT * FROM ms_admin LIMIT 5");
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($records) {
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr style='background-color: #f0f0f0;'>";
            foreach (array_keys($records[0]) as $column) {
                echo "<th>$column</th>";
            }
            echo "</tr>";
            foreach ($records as $record) {
                echo "<tr>";
                foreach ($record as $value) {
                    echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p><em>No records found in ms_admin table</em></p>";
        }
        
    } catch (Exception $e) {
        echo "<p>❌ Error accessing ms_admin: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
    // Check ms_login table
    echo "<h3>🔐 ms_login Table Structure</h3>";
    try {
        $stmt = $pdo->query("DESCRIBE ms_login");
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr style='background-color: #f0f0f0;'><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>{$row['Field']}</td>";
            echo "<td>{$row['Type']}</td>";
            echo "<td>{$row['Null']}</td>";
            echo "<td>{$row['Key']}</td>";
            echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Show existing login data
        echo "<h4>📊 Existing ms_login Records</h4>";
        $stmt = $pdo->query("SELECT * FROM ms_login LIMIT 5");
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($records) {
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr style='background-color: #f0f0f0;'>";
            foreach (array_keys($records[0]) as $column) {
                echo "<th>$column</th>";
            }
            echo "</tr>";
            foreach ($records as $record) {
                echo "<tr>";
                foreach ($record as $value) {
                    echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p><em>No records found in ms_login table</em></p>";
        }
        
    } catch (Exception $e) {
        echo "<p>❌ Error accessing ms_login: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
    // List all tables with 'admin' in the name
    echo "<h3>📝 All Admin-related Tables</h3>";
    $stmt = $pdo->query("SHOW TABLES LIKE '%admin%'");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if ($tables) {
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>$table</li>";
        }
        echo "</ul>";
    } else {
        echo "<p><em>No tables found with 'admin' in the name</em></p>";
    }
    
    // List tables with 'user' in the name  
    echo "<h3>👤 All User-related Tables</h3>";
    $stmt = $pdo->query("SHOW TABLES LIKE '%user%'");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if ($tables) {
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>$table</li>";
        }
        echo "</ul>";
    } else {
        echo "<p><em>No tables found with 'user' in the name</em></p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Database Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?> 