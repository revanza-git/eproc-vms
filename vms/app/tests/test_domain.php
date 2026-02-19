<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Domain and Redirect Test</h1>";

// Test basic domain accessibility
echo "<h2>1. Domain Accessibility Test</h2>";

$target_domain = "local.eproc.web.com";
$test_url = "http://" . $target_domain . "/internal/from_eks";

echo "<p><strong>Target Domain:</strong> $target_domain</p>";
echo "<p><strong>Target URL:</strong> $test_url</p>";

// Test DNS resolution
$ip = gethostbyname($target_domain);
if ($ip !== $target_domain) {
    echo "<p style='color: green;'>✅ DNS resolves to: $ip</p>";
} else {
    echo "<p style='color: red;'>❌ DNS resolution failed for $target_domain</p>";
    echo "<p><strong>Fix:</strong> Add to C:\\Windows\\System32\\drivers\\etc\\hosts:</p>";
    echo "<pre>127.0.0.1 $target_domain</pre>";
}

// Test basic HTTP connectivity
echo "<h2>2. HTTP Connectivity Test</h2>";

if (function_exists('curl_init')) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $test_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, true);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "<p style='color: red;'>❌ cURL Error: $error</p>";
    } else {
        echo "<p style='color: green;'>✅ HTTP Response Code: $http_code</p>";
        if ($http_code == 200) {
            echo "<p style='color: green;'>✅ Server is responding successfully</p>";
        } elseif ($http_code >= 400) {
            echo "<p style='color: orange;'>⚠️ Server responded but with error code $http_code</p>";
        } else {
            echo "<p style='color: blue;'>ℹ️ Server responded with code $http_code</p>";
        }
        
        // Show first few lines of response
        $lines = explode("\n", $response);
        echo "<h3>Response Headers:</h3>";
        echo "<pre>";
        for ($i = 0; $i < min(10, count($lines)); $i++) {
            echo htmlspecialchars($lines[$i]) . "\n";
        }
        echo "</pre>";
    }
} else {
    echo "<p style='color: red;'>❌ cURL extension not available</p>";
}

// Test with file_get_contents as backup
echo "<h2>3. Alternative HTTP Test (file_get_contents)</h2>";

$context = stream_context_create([
    'http' => [
        'timeout' => 10,
        'method' => 'GET'
    ]
]);

$result = @file_get_contents($test_url, false, $context);
if ($result !== false) {
    echo "<p style='color: green;'>✅ file_get_contents successful</p>";
    echo "<p>Response length: " . strlen($result) . " bytes</p>";
    
    // Show first 500 characters
    echo "<h3>Response Preview:</h3>";
    echo "<pre>" . htmlspecialchars(substr($result, 0, 500)) . "</pre>";
    
} else {
    echo "<p style='color: red;'>❌ file_get_contents failed</p>";
    $error = error_get_last();
    if ($error) {
        echo "<p>Error: " . htmlspecialchars($error['message']) . "</p>";
    }
}

// Test manual key generation
echo "<h2>4. Manual Key Generation Test</h2>";

try {
    // Test key generation like the actual method
    if (function_exists('random_bytes')) {
        $key = uniqid() . time() . bin2hex(random_bytes(5));
    } else {
        // Fallback for older PHP versions
        $key = uniqid() . time() . bin2hex(openssl_random_pseudo_bytes(5));
    }
    echo "<p>✅ Generated test key: <code>$key</code></p>";
    
    // Test JSON encoding
    $test_data = array(
        "name" => "Test Admin",
        "id_user" => 999,
        "id_role" => 2,
        "id_division" => 1,
        "email" => "test@example.com",
        "photo_profile" => "profile.jpg",
        "app_type" => 2
    );
    
    $json = json_encode($test_data);
    echo "<p>✅ JSON encoding successful</p>";
    echo "<p><strong>Test JSON:</strong></p>";
    echo "<pre>" . htmlspecialchars($json) . "</pre>";
    
    // Test manual URL construction
    $manual_url = "http://local.eproc.web.com/internal/from_eks?key=" . $key;
    echo "<p><strong>Test URL would be:</strong></p>";
    echo "<p><a href='$manual_url' target='_blank'>$manual_url</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Key generation test failed: " . $e->getMessage() . "</p>";
}

// Test current admin session
echo "<h2>5. Current Session Test</h2>";

session_start();
if (isset($_SESSION) && !empty($_SESSION)) {
    echo "<p>✅ Session data exists</p>";
    echo "<pre>";
    foreach ($_SESSION as $key => $value) {
        if (is_array($value) || is_object($value)) {
            echo "$key: " . print_r($value, true) . "\n";
        } else {
            echo "$key: " . htmlspecialchars($value) . "\n";
        }
    }
    echo "</pre>";
} else {
    echo "<p>ℹ️ No session data found</p>";
}

echo "<h2>6. Recommendations</h2>";
echo "<ul>";
echo "<li><strong>If DNS fails:</strong> Add 'local.eproc.web.com' to your hosts file</li>";
echo "<li><strong>If HTTP fails:</strong> Ensure IIS is running and configured for both domains</li>";
echo "<li><strong>If everything works:</strong> Try admin login again and check the logs</li>";
echo "</ul>";

?>
