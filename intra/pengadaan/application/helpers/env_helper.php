<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$shared = dirname(__DIR__, 4) . '/shared/env.php';
if (is_file($shared)) {
	require_once $shared;
}

/**
 * Environment Variable Helper
 * 
 * This helper provides functions to load and access environment variables
 * from .env files for configuration management.
 */

if (!function_exists('load_env')) {
    /**
     * Load environment variables from .env file
     * 
     * @param string $file_path Path to the .env file (default: project root)
     * @return bool True if file was loaded successfully, false otherwise
     */
    function load_env($file_path = null) {
        // Determine the .env file path
        if ($file_path === null) {
            // Use FCPATH to reliably find the project root (intra/)
            if (defined('FCPATH')) {
                $file_path = dirname(FCPATH) . '/.env';
            } else {
                // Fallback to original logic if FCPATH is missing
                $file_path = dirname(dirname(dirname(__DIR__))) . '/.env';
            }
        }
        
        // Check if .env file exists
        if (!file_exists($file_path)) {
            // Try alternative paths
            $alternative_paths = [
                dirname(dirname(dirname(__DIR__))) . '/.env',
                FCPATH . '.env',
                APPPATH . '../../../.env'
            ];
            
            $found = false;
            foreach ($alternative_paths as $alt_path) {
                if (file_exists($alt_path)) {
                    $file_path = $alt_path;
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                if (function_exists('log_message')) {
                    log_message('error', 'Environment file .env not found');
                }
                return false;
            }
        }
        
        try {
            // Read the .env file
            $lines = file($file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            
            foreach ($lines as $line) {
                // Skip comments and empty lines
                if (strpos(trim($line), '#') === 0 || trim($line) === '') {
                    continue;
                }
                
                // Parse key=value pairs
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value);
                    
                    // Remove quotes if present
                    if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
                        (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
                        $value = substr($value, 1, -1);
                    }
                    
                    // Set environment variable if not already set
                    if (!array_key_exists($key, $_ENV)) {
                        $_ENV[$key] = $value;
                        putenv("$key=$value");
                    }
                }
            }
            
            if (function_exists('log_message')) {
                log_message('debug', 'Environment variables loaded from: ' . $file_path);
            }
            return true;
            
        } catch (Exception $e) {
            if (function_exists('log_message')) {
                log_message('error', 'Error loading environment file: ' . $e->getMessage());
            }
            return false;
        }
    }
}

if (!function_exists('env')) {
    /**
     * Get an environment variable value with optional default
     * 
     * @param string $key The environment variable key
     * @param mixed $default Default value if key is not found
     * @return mixed The environment variable value or default
     */
    function env($key, $default = null) {
        // Check $_ENV first
        if (array_key_exists($key, $_ENV)) {
            return parse_env_value($_ENV[$key]);
        }
        
        // Check getenv()
        $value = getenv($key);
        if ($value !== false) {
            return parse_env_value($value);
        }
        
        // Return default if not found
        return $default;
    }
}

if (!function_exists('parse_env_value')) {
    /**
     * Parse environment variable values to appropriate types
     * 
     * @param string $value The raw environment variable value
     * @return mixed The parsed value
     */
    function parse_env_value($value) {
        // Handle boolean values
        if (strtolower($value) === 'true') {
            return true;
        }
        if (strtolower($value) === 'false') {
            return false;
        }
        
        // Handle null values
        if (strtolower($value) === 'null') {
            return null;
        }
        
        // Handle numeric values
        if (is_numeric($value)) {
            if (strpos($value, '.') !== false) {
                return (float) $value;
            }
            return (int) $value;
        }
        
        // Return as string
        return $value;
    }
}

if (!function_exists('env_required')) {
    /**
     * Get a required environment variable (throws error if not found)
     * 
     * @param string $key The environment variable key
     * @param string $message Custom error message
     * @return mixed The environment variable value
     * @throws Exception If the environment variable is not found
     */
    function env_required($key, $message = null) {
        $value = env($key);
        
        if ($value === null) {
            $error_message = $message ?: "Required environment variable '$key' is not set";
            if (function_exists('log_message')) {
                log_message('error', $error_message);
            }
            throw new Exception($error_message);
        }
        
        return $value;
    }
}

if (!function_exists('is_production')) {
    /**
     * Check if the application is running in production environment
     * 
     * @return bool True if in production, false otherwise
     */
    function is_production() {
        return env('APP_ENV', 'development') === 'production';
    }
}

if (!function_exists('is_development')) {
    /**
     * Check if the application is running in development environment
     * 
     * @return bool True if in development, false otherwise
     */
    function is_development() {
        return env('APP_ENV', 'development') === 'development';
    }
}

// Auto-load environment variables when this helper is loaded
load_env(); 
