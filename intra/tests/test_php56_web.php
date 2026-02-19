<?php
// Test PHP 5.6 compatibility
echo "<h1>PHP 5.6 Web Test</h1>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Current Time: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>Server: " . (isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : 'Unknown') . "</p>";

// Test that will generate a notice to test error logging
echo "<p>Testing error logging...</p>";
echo $undefined_variable; // This will generate a notice

echo "<p>✅ PHP 5.6 is working through the web server!</p>";
?> 