<?php
/**
 * Enable Error Logging Integration
 * Include this file at the top of main/index.php to enable error logging
 */

// Include the error logger
require_once(__DIR__ . '/error_logger.php');

// Optional: Set additional PHP configurations for better error detection
ini_set('display_startup_errors', 1);
ini_set('log_errors_max_len', 0);
ini_set('ignore_repeated_errors', 0);
ini_set('track_errors', 1);

// Log application start
$start_time = microtime(true);
register_shutdown_function(function() use ($start_time) {
    $execution_time = microtime(true) - $start_time;
    $peak_memory = memory_get_peak_usage(true);
    
    error_log(sprintf(
        "Application execution completed in %.3f seconds, Peak memory: %s",
        $execution_time,
        number_format($peak_memory / 1024 / 1024, 2) . 'MB'
    ));
});

echo "<!-- Error logging enabled -->\n";
?> 