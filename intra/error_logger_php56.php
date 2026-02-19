<?php
/**
 * Advanced Error Logger for E-Procurement System (PHP 5.6 Compatible)
 * This script sets up comprehensive error logging to detect errors faster
 */

// Create logs directory if it doesn't exist
$log_dir = __DIR__ . '/logs';
if (!is_dir($log_dir)) {
    mkdir($log_dir, 0755, true);
}

// Define log file with current date
$error_log_file = $log_dir . '/php_errors_' . date('Y-m-d') . '.log';
$access_log_file = $log_dir . '/access_' . date('Y-m-d') . '.log';

// Set custom error handler
set_error_handler(function($severity, $message, $file, $line) use ($error_log_file) {
    $error_types = array(
        E_ERROR => 'FATAL ERROR',
        E_WARNING => 'WARNING',
        E_PARSE => 'PARSE ERROR',
        E_NOTICE => 'NOTICE',
        E_CORE_ERROR => 'CORE ERROR',
        E_CORE_WARNING => 'CORE WARNING',
        E_COMPILE_ERROR => 'COMPILE ERROR',
        E_COMPILE_WARNING => 'COMPILE WARNING',
        E_USER_ERROR => 'USER ERROR',
        E_USER_WARNING => 'USER WARNING',
        E_USER_NOTICE => 'USER NOTICE',
        E_STRICT => 'STRICT NOTICE',
        E_RECOVERABLE_ERROR => 'RECOVERABLE ERROR',
        E_DEPRECATED => 'DEPRECATED',
        E_USER_DEPRECATED => 'USER DEPRECATED'
    );
    
    $error_type = isset($error_types[$severity]) ? $error_types[$severity] : 'UNKNOWN ERROR';
    
    $timestamp = date('Y-m-d H:i:s');
    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Unknown';
    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'Unknown';
    $url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'Unknown';
    
    $backtrace = debug_backtrace(false);
    $stack_trace = '';
    for ($i = 1; $i <= 5 && $i < count($backtrace); $i++) {
        $trace = $backtrace[$i];
        $stack_trace .= sprintf(
            "#%d %s(%d): %s%s%s()\n",
            $i - 1,
            isset($trace['file']) ? $trace['file'] : '[internal function]',
            isset($trace['line']) ? $trace['line'] : 0,
            isset($trace['class']) ? $trace['class'] : '',
            isset($trace['type']) ? $trace['type'] : '',
            isset($trace['function']) ? $trace['function'] : ''
        );
    }
    
    $log_entry = sprintf(
        "[%s] %s: %s in %s on line %d\nURL: %s\nIP: %s\nUser Agent: %s\nStack Trace:\n%s\n%s\n",
        $timestamp,
        $error_type,
        $message,
        $file,
        $line,
        $url,
        $ip,
        $user_agent,
        $stack_trace,
        str_repeat("-", 80)
    );
    
    file_put_contents($error_log_file, $log_entry, FILE_APPEND | LOCK_EX);
    
    // Don't stop execution for non-fatal errors
    if ($severity !== E_ERROR && $severity !== E_CORE_ERROR && $severity !== E_COMPILE_ERROR) {
        return true;
    }
});

// Set exception handler
set_exception_handler(function($exception) use ($error_log_file) {
    $timestamp = date('Y-m-d H:i:s');
    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Unknown';
    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'Unknown';
    $url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'Unknown';
    
    $log_entry = sprintf(
        "[%s] UNCAUGHT EXCEPTION: %s in %s on line %d\nMessage: %s\nURL: %s\nIP: %s\nUser Agent: %s\nStack Trace:\n%s\n%s\n",
        $timestamp,
        get_class($exception),
        $exception->getFile(),
        $exception->getLine(),
        $exception->getMessage(),
        $url,
        $ip,
        $user_agent,
        $exception->getTraceAsString(),
        str_repeat("-", 80)
    );
    
    file_put_contents($error_log_file, $log_entry, FILE_APPEND | LOCK_EX);
});

// Log access information
function log_access() {
    global $access_log_file;
    
    $timestamp = date('Y-m-d H:i:s');
    $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'Unknown';
    $url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'Unknown';
    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'Unknown';
    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Unknown';
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'Direct';
    
    $log_entry = sprintf(
        "[%s] %s %s - IP: %s - Referer: %s - User Agent: %s\n",
        $timestamp,
        $method,
        $url,
        $ip,
        $referer,
        substr($user_agent, 0, 100)
    );
    
    file_put_contents($access_log_file, $log_entry, FILE_APPEND | LOCK_EX);
}

// Log this access
log_access();

// Function to check recent errors
function get_recent_errors($hours = 24) {
    global $error_log_file;
    
    if (!file_exists($error_log_file)) {
        return "No error log file found.";
    }
    
    $content = file_get_contents($error_log_file);
    $lines = explode("\n", $content);
    
    $recent_errors = array();
    $cutoff_time = time() - ($hours * 3600);
    
    foreach ($lines as $line) {
        if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $matches)) {
            $log_time = strtotime($matches[1]);
            if ($log_time >= $cutoff_time) {
                $recent_errors[] = $line;
            }
        }
    }
    
    return implode("\n", array_slice($recent_errors, -50)); // Last 50 errors
}

// Function to clear old logs (older than 30 days)
function cleanup_old_logs() {
    $log_dir = __DIR__ . '/logs';
    $files = glob($log_dir . '/*.log');
    $cutoff = time() - (30 * 24 * 3600); // 30 days
    
    foreach ($files as $file) {
        if (filemtime($file) < $cutoff) {
            unlink($file);
        }
    }
}

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', $error_log_file);

// Cleanup old logs occasionally (1% chance)
if (rand(1, 100) === 1) {
    cleanup_old_logs();
}

// If this file is accessed directly, show log viewer
if (basename($_SERVER['SCRIPT_NAME']) === 'error_logger_php56.php') {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Error Log Viewer (PHP 5.6)</title>
        <style>
            body { font-family: monospace; margin: 20px; background: #f5f5f5; }
            .container { background: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
            .error-log { background: #f8f8f8; padding: 15px; border: 1px solid #ddd; border-radius: 3px; white-space: pre-wrap; max-height: 600px; overflow-y: auto; }
            .controls { margin-bottom: 20px; }
            .controls select, .controls button { padding: 8px; margin-right: 10px; }
            .stats { background: #e8f4fd; padding: 10px; margin-bottom: 20px; border-left: 4px solid #007cba; }
            .error { color: #d32f2f; }
            .warning { color: #f57c00; }
            .notice { color: #1976d2; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>🐛 E-Procurement Error Log Viewer (PHP 5.6 Compatible)</h1>
            
            <div class="stats">
                <strong>📊 Current Status:</strong><br>
                Log File: <?php echo basename($error_log_file); ?><br>
                File Size: <?php echo file_exists($error_log_file) ? number_format(filesize($error_log_file)) . ' bytes' : 'Not found'; ?><br>
                Last Modified: <?php echo file_exists($error_log_file) ? date('Y-m-d H:i:s', filemtime($error_log_file)) : 'N/A'; ?><br>
                PHP Version: <?php echo phpversion(); ?>
            </div>
            
            <div class="controls">
                <form method="GET" style="display: inline;">
                    <select name="hours">
                        <option value="1" <?php echo (isset($_GET['hours']) && $_GET['hours'] == 1) ? 'selected' : ''; ?>>Last 1 hour</option>
                        <option value="6" <?php echo (isset($_GET['hours']) && $_GET['hours'] == 6) ? 'selected' : ''; ?>>Last 6 hours</option>
                        <option value="24" <?php echo (!isset($_GET['hours']) || $_GET['hours'] == 24) ? 'selected' : ''; ?>>Last 24 hours</option>
                        <option value="168" <?php echo (isset($_GET['hours']) && $_GET['hours'] == 168) ? 'selected' : ''; ?>>Last 7 days</option>
                    </select>
                    <button type="submit">Refresh</button>
                </form>
                
                <button onclick="location.reload()">🔄 Auto Refresh</button>
                <button onclick="if(confirm('Clear all logs?')) location.href='?clear=1'">🗑️ Clear Logs</button>
            </div>
            
            <?php
            if (isset($_GET['clear'])) {
                if (file_exists($error_log_file)) {
                    file_put_contents($error_log_file, '');
                    echo "<div style='background: #d4edda; padding: 10px; margin-bottom: 20px; border-left: 4px solid #28a745;'>✅ Logs cleared successfully!</div>";
                }
            }
            
            $hours = isset($_GET['hours']) ? $_GET['hours'] : 24;
            $recent_errors = get_recent_errors($hours);
            ?>
            
            <h2>📋 Recent Errors (Last <?php echo $hours; ?> hours)</h2>
            <div class="error-log"><?php 
                if (empty(trim($recent_errors))) {
                    echo "✅ No errors found in the specified time period!";
                } else {
                    // Color code different error types
                    $colored_errors = preg_replace('/\b(FATAL ERROR|ERROR)\b/', '<span class="error">$1</span>', $recent_errors);
                    $colored_errors = preg_replace('/\b(WARNING)\b/', '<span class="warning">$1</span>', $colored_errors);
                    $colored_errors = preg_replace('/\b(NOTICE)\b/', '<span class="notice">$1</span>', $colored_errors);
                    echo $colored_errors;
                }
            ?></div>
            
            <div style="margin-top: 20px; font-size: 12px; color: #666;">
                <strong>💡 Tips:</strong><br>
                • Include this script at the top of main/index.php to enable automatic error logging<br>
                • Errors are automatically categorized by severity<br>
                • Old logs are automatically cleaned up after 30 days<br>
                • Access logs are also maintained separately<br>
                • This version is compatible with PHP 5.6
            </div>
        </div>
        
        <script>
        // Auto-refresh every 30 seconds if there are errors
        <?php if (!empty(trim($recent_errors))): ?>
        setTimeout(function() { location.reload(); }, 30000);
        <?php endif; ?>
        </script>
    </body>
    </html>
    <?php
}
?> 