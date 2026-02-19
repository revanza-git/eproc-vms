<?php
/**
 * Environment Variables Test Script
 * 
 * This script tests if environment variables are being loaded correctly
 * Run this script to verify your .env configuration is working.
 */

echo "<h1>🧪 Environment Variables Test</h1>";

// Test if we can load environment variables directly
echo "<h2>1. Testing Direct Environment Variable Loading</h2>";

// Try to load environment variables manually
$env_file = __DIR__ . '/.env';
if (file_exists($env_file)) {
    echo "✅ .env file found at: " . $env_file . "<br/>";
    
    $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $env_vars_count = 0;
    
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0 || trim($line) === '') {
            continue;
        }
        
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $env_vars_count++;
        }
    }
    
    echo "✅ Found {$env_vars_count} environment variables in .env file<br/>";
} else {
    echo "❌ .env file not found. Please copy .env.example to .env<br/>";
}

// Test loading via helper functions
echo "<h2>2. Testing Environment Helper Functions</h2>";

// Check if we can load the environment helper
$main_helper = __DIR__ . '/main/application/helpers/env_helper.php';
$pengadaan_helper = __DIR__ . '/pengadaan/application/helpers/env_helper.php';

if (file_exists($main_helper)) {
    echo "✅ Main env_helper.php found<br/>";
    require_once $main_helper;
    
    // Test some environment variables
    $test_vars = [
        'APP_ENV' => 'Application Environment',
        'DB_DEFAULT_HOSTNAME' => 'Database Hostname',
        'MAIN_BASE_URL' => 'Main Application URL',
        'EMAIL_SMTP_HOST' => 'Email SMTP Host',
        'PHP_BINARY_PATH' => 'PHP Binary Path'
    ];
    
    echo "<h3>Testing Key Environment Variables:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Variable</th><th>Description</th><th>Value</th><th>Status</th></tr>";
    
    foreach ($test_vars as $var => $description) {
        $value = env($var, 'NOT_SET');
        $status = ($value !== 'NOT_SET') ? '✅ Set' : '❌ Not Set';
        $display_value = ($value !== 'NOT_SET') ? $value : 'Not configured';
        
        echo "<tr>";
        echo "<td><code>{$var}</code></td>";
        echo "<td>{$description}</td>";
        echo "<td>{$display_value}</td>";
        echo "<td>{$status}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} else {
    echo "❌ Main env_helper.php not found<br/>";
}

// Test database configuration
echo "<h2>3. Testing Database Configuration</h2>";

if (function_exists('env')) {
    $db_configs = [
        'Main Default DB' => [
            'hostname' => env('DB_DEFAULT_HOSTNAME', 'NOT_SET'),
            'port' => env('DB_DEFAULT_PORT', 'NOT_SET'),
            'username' => env('DB_DEFAULT_USERNAME', 'NOT_SET'),
            'database' => env('DB_DEFAULT_DATABASE', 'NOT_SET')
        ],
        'Eproc DB' => [
            'hostname' => env('DB_EPROC_HOSTNAME', 'NOT_SET'),
            'port' => env('DB_EPROC_PORT', 'NOT_SET'),
            'username' => env('DB_EPROC_USERNAME', 'NOT_SET'),
            'database' => env('DB_EPROC_DATABASE', 'NOT_SET')
        ]
    ];
    
    foreach ($db_configs as $db_name => $config) {
        echo "<h4>{$db_name}:</h4>";
        echo "<ul>";
        foreach ($config as $key => $value) {
            $status = ($value !== 'NOT_SET') ? '✅' : '❌';
            echo "<li>{$status} {$key}: {$value}</li>";
        }
        echo "</ul>";
    }
} else {
    echo "❌ env() function not available<br/>";
}

// Test URL configuration
echo "<h2>4. Testing URL Configuration</h2>";

if (function_exists('env')) {
    $url_configs = [
        'MAIN_BASE_URL' => 'Main Application Base URL',
        'PENGADAAN_BASE_URL' => 'Pengadaan Application Base URL',
        'MAIN_VMS_URL' => 'VMS System URL',
        'EXTERNAL_EPROC_URL' => 'External Eproc URL'
    ];
    
    echo "<ul>";
    foreach ($url_configs as $var => $description) {
        $value = env($var, 'NOT_SET');
        $status = ($value !== 'NOT_SET') ? '✅' : '❌';
        echo "<li>{$status} {$description}: {$value}</li>";
    }
    echo "</ul>";
}

// Test email configuration
echo "<h2>5. Testing Email Configuration</h2>";

if (function_exists('env')) {
    $email_configs = [
        'EMAIL_PROTOCOL' => 'Email Protocol',
        'EMAIL_SMTP_HOST' => 'SMTP Host',
        'EMAIL_SMTP_PORT' => 'SMTP Port',
        'EMAIL_SMTP_USER' => 'SMTP Username',
        'EMAIL_FROM_ADDRESS' => 'From Address'
    ];
    
    echo "<ul>";
    foreach ($email_configs as $var => $description) {
        $value = env($var, 'NOT_SET');
        $status = ($value !== 'NOT_SET') ? '✅' : '❌';
        
        // Hide sensitive information
        if (in_array($var, ['EMAIL_SMTP_USER']) && $value !== 'NOT_SET') {
            $value = substr($value, 0, 3) . '***@' . substr(strrchr($value, '@'), 1);
        }
        
        echo "<li>{$status} {$description}: {$value}</li>";
    }
    echo "</ul>";
}

// Test helper functions
echo "<h2>6. Testing Helper Functions</h2>";

if (function_exists('env')) {
    echo "✅ env() function is available<br/>";
    
    if (function_exists('is_production')) {
        $is_prod = is_production() ? 'Yes' : 'No';
        echo "✅ is_production() function works. Production mode: {$is_prod}<br/>";
    } else {
        echo "❌ is_production() function not available<br/>";
    }
    
    if (function_exists('is_development')) {
        $is_dev = is_development() ? 'Yes' : 'No';
        echo "✅ is_development() function works. Development mode: {$is_dev}<br/>";
    } else {
        echo "❌ is_development() function not available<br/>";
    }
} else {
    echo "❌ env() function not available<br/>";
}

// System information
echo "<h2>7. System Information</h2>";
echo "<ul>";
echo "<li>PHP Version: " . phpversion() . "</li>";
echo "<li>Current Directory: " . __DIR__ . "</li>";
echo "<li>Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</li>";
echo "<li>Environment File Path: " . $env_file . "</li>";
echo "</ul>";

// Recommendations
echo "<h2>🎯 Recommendations</h2>";

if (!file_exists($env_file)) {
    echo "<div style='background: #ffebee; padding: 10px; margin: 10px 0; border-left: 4px solid #f44336;'>";
    echo "<strong>❌ Missing .env file</strong><br/>";
    echo "1. Copy .env.example to .env<br/>";
    echo "2. Edit .env with your configuration values<br/>";
    echo "</div>";
}

if (!function_exists('env')) {
    echo "<div style='background: #ffebee; padding: 10px; margin: 10px 0; border-left: 4px solid #f44336;'>";
    echo "<strong>❌ Environment functions not loaded</strong><br/>";
    echo "1. Make sure env_helper.php files exist<br/>";
    echo "2. Check if 'env' is added to autoload helper in both applications<br/>";
    echo "</div>";
}

if (function_exists('env') && env('APP_ENV', 'NOT_SET') === 'NOT_SET') {
    echo "<div style='background: #fff3e0; padding: 10px; margin: 10px 0; border-left: 4px solid #ff9800;'>";
    echo "<strong>⚠️ Environment not configured</strong><br/>";
    echo "1. Edit your .env file with proper values<br/>";
    echo "2. Make sure there are no syntax errors in .env<br/>";
    echo "</div>";
}

if (function_exists('env') && env('APP_ENV') === 'development') {
    echo "<div style='background: #e8f5e8; padding: 10px; margin: 10px 0; border-left: 4px solid #4caf50;'>";
    echo "<strong>✅ Environment system working</strong><br/>";
    echo "Your environment variables are loading correctly!<br/>";
    echo "</div>";
}

echo "<hr/>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ol>";
echo "<li>If tests pass, you can delete this test_env.php file</li>";
echo "<li>Test your applications to ensure they work with environment variables</li>";
echo "<li>Create environment-specific .env files for staging/production</li>";
echo "</ol>";

?> 