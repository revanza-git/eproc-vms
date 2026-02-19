<?php
// Quick diagnostic script to check routing configuration
require_once 'system/core/Common.php';
require_once 'application/config/config.php';

// Load env helper
if (file_exists('application/helpers/env_helper.php')) {
    require_once 'application/helpers/env_helper.php';
}

echo "<h2>Routing Diagnostic Test</h2>";
echo "<pre>";

echo "1. Config base_url: " . ($config['base_url'] ?? 'NOT SET') . "\n";
echo "2. Config base_app: " . ($config['base_app'] ?? 'NOT SET') . "\n";
echo "3. Config cookie_path: " . ($config['cookie_path'] ?? 'NOT SET') . "\n";
echo "4. Config sess_cookie_name: " . ($config['sess_cookie_name'] ?? 'NOT SET') . "\n\n";

echo "5. Environment Variables:\n";
echo "   PENGADAAN_BASE_URL: " . (function_exists('env') ? env('PENGADAAN_BASE_URL', 'NOT SET') : 'env() not available') . "\n";
echo "   PENGADAAN_BASE_APP: " . (function_exists('env') ? env('PENGADAAN_BASE_APP', 'NOT SET') : 'env() not available') . "\n\n";

echo "6. Server Variables:\n";
echo "   REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'NOT SET') . "\n";
echo "   SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'NOT SET') . "\n";
echo "   HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'NOT SET') . "\n";
echo "   DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'NOT SET') . "\n\n";

echo "7. Expected URLs:\n";
echo "   Should redirect to: http://local.eproc.web.com/internal/pengadaan/admin\n";
echo "   Currently redirecting to: (test by clicking menu)\n";

echo "</pre>";
