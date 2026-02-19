<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Secure Password Library
 * 
 * Provides secure password hashing and verification using bcrypt algorithm
 * Includes migration support from legacy SHA-1 hashes
 * 
 * @package     VMS eProc
 * @subpackage  Libraries  
 * @category    Security
 * @author      VMS Security Team
 * @version     2.0.0
 * @since       2024
 */
class Secure_Password {
    
    private $CI;
    
    // Security configuration
    private $bcrypt_cost = 12;           // Higher cost = more secure but slower
    private $max_password_length = 4096; // Maximum password length
    private $min_password_length = 8;    // Minimum password length for new passwords
    
    // Legacy hash detection patterns
    private $legacy_patterns = array(
        'sha1' => '/^[a-f0-9]{40}$/i',           // SHA-1 hash pattern
        'md5'  => '/^[a-f0-9]{32}$/i',           // MD5 hash pattern  
        'plain' => '/^[^$]/i'                    // Plain text pattern (no $ prefix)
    );
    
    public function __construct() {
        $this->CI =& get_instance();
        
        // Load required helpers
        $this->CI->load->helper('security');
        
        // Log library initialization
        log_message('debug', 'Secure_Password Library Initialized');
    }
    
    /**
     * Hash a password using secure bcrypt algorithm
     *
     * @param string $password The plain text password
     * @param array $options Optional configuration array
     * @return string|false The hashed password or false on failure
     */
    public function hash_password($password, $options = array()) {
        // Validate input
        if (empty($password)) {
            log_message('error', 'Secure_Password: Empty password provided for hashing');
            return false;
        }
        
        // Check password length
        if (strlen($password) > $this->max_password_length) {
            log_message('error', 'Secure_Password: Password exceeds maximum length');
            return false;
        }
        
        // Set cost from options or use default
        $cost = isset($options['cost']) ? (int)$options['cost'] : $this->bcrypt_cost;
        
        // Validate cost range (4-31 for bcrypt)
        if ($cost < 4 || $cost > 31) {
            $cost = $this->bcrypt_cost;
            log_message('error', 'Secure_Password: Invalid cost provided, using default: ' . $cost);
        }
        
        try {
            // Use PHP's password_hash function (available via CodeIgniter compatibility layer)
            if (function_exists('password_hash')) {
                $hash = password_hash($password, PASSWORD_BCRYPT, array('cost' => $cost));
            } else {
                // Fallback to CodeIgniter's implementation
                $hash = $this->bcrypt_hash($password, $cost);
            }
            
            if ($hash === false) {
                log_message('error', 'Secure_Password: Failed to generate password hash');
                return false;
            }
            
            // Log successful hash generation (without revealing the hash)
            log_message('debug', 'Secure_Password: Password hash generated successfully');
            
            return $hash;
            
        } catch (Exception $e) {
            log_message('error', 'Secure_Password: Exception during password hashing: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verify a password against a hash
     * Supports both new bcrypt hashes and legacy SHA-1 hashes
     *
     * @param string $password The plain text password
     * @param string $hash The stored hash
     * @return boolean True if password matches, false otherwise
     */
    public function verify_password($password, $hash) {
        // Validate inputs
        if (empty($password) || empty($hash)) {
            log_message('error', 'Secure_Password: Empty password or hash provided for verification');
            return false;
        }
        
        // Check password length
        if (strlen($password) > $this->max_password_length) {
            log_message('error', 'Secure_Password: Password exceeds maximum length during verification');
            return false;
        }
        
        try {
            // Check if this is a bcrypt hash (starts with $2y$, $2a$, or $2x$)
            if ($this->is_bcrypt_hash($hash)) {
                // Use secure bcrypt verification
                if (function_exists('password_verify')) {
                    $result = password_verify($password, $hash);
                } else {
                    // Fallback to CodeIgniter's implementation
                    $result = $this->bcrypt_verify($password, $hash);
                }
                
                if ($result) {
                    log_message('debug', 'Secure_Password: bcrypt verification successful');
                } else {
                    log_message('info', 'Secure_Password: bcrypt verification failed');
                }
                
                return $result;
            }
            
            // Check for legacy hash patterns and verify
            foreach ($this->legacy_patterns as $type => $pattern) {
                if (preg_match($pattern, $hash)) {
                    $result = $this->verify_legacy_hash($password, $hash, $type);
                    
                    if ($result) {
                        log_message('info', 'Secure_Password: Legacy ' . $type . ' verification successful - REQUIRES MIGRATION');
                    } else {
                        log_message('info', 'Secure_Password: Legacy ' . $type . ' verification failed');
                    }
                    
                    return $result;
                }
            }
            
            // Unknown hash format
            log_message('error', 'Secure_Password: Unknown hash format provided');
            return false;
            
        } catch (Exception $e) {
            log_message('error', 'Secure_Password: Exception during password verification: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if a hash needs to be rehashed (for security upgrades)
     *
     * @param string $hash The stored hash
     * @param array $options Optional configuration
     * @return boolean True if rehashing is needed
     */
    public function needs_rehash($hash, $options = array()) {
        if (empty($hash)) {
            return true;
        }
        
        // If it's not a bcrypt hash, it definitely needs rehashing
        if (!$this->is_bcrypt_hash($hash)) {
            log_message('info', 'Secure_Password: Legacy hash detected - rehashing required');
            return true;
        }
        
        // Check if bcrypt cost has changed
        $cost = isset($options['cost']) ? (int)$options['cost'] : $this->bcrypt_cost;
        
        if (function_exists('password_needs_rehash')) {
            return password_needs_rehash($hash, PASSWORD_BCRYPT, array('cost' => $cost));
        }
        
        // Manual check for cost changes
        $hash_info = $this->get_hash_info($hash);
        if ($hash_info && isset($hash_info['cost']) && $hash_info['cost'] !== $cost) {
            log_message('info', 'Secure_Password: bcrypt cost changed - rehashing recommended');
            return true;
        }
        
        return false;
    }
    
    /**
     * Validate password strength
     *
     * @param string $password The password to validate
     * @param array $requirements Custom requirements
     * @return array Validation result with success status and messages
     */
    public function validate_password_strength($password, $requirements = array()) {
        $result = array(
            'success' => true,
            'messages' => array(),
            'score' => 0
        );
        
        // Default requirements
        $default_requirements = array(
            'min_length' => $this->min_password_length,
            'require_uppercase' => true,
            'require_lowercase' => true, 
            'require_numbers' => true,
            'require_special' => true,
            'forbidden_patterns' => array('password', '123456', 'admin', 'eproc')
        );
        
        $requirements = array_merge($default_requirements, $requirements);
        
        // Check minimum length
        if (strlen($password) < $requirements['min_length']) {
            $result['success'] = false;
            $result['messages'][] = 'Password must be at least ' . $requirements['min_length'] . ' characters long';
        } else {
            $result['score'] += 20;
        }
        
        // Check uppercase letters
        if ($requirements['require_uppercase'] && !preg_match('/[A-Z]/', $password)) {
            $result['success'] = false;
            $result['messages'][] = 'Password must contain at least one uppercase letter';
        } else {
            $result['score'] += 20;
        }
        
        // Check lowercase letters
        if ($requirements['require_lowercase'] && !preg_match('/[a-z]/', $password)) {
            $result['success'] = false;
            $result['messages'][] = 'Password must contain at least one lowercase letter';
        } else {
            $result['score'] += 20;
        }
        
        // Check numbers
        if ($requirements['require_numbers'] && !preg_match('/[0-9]/', $password)) {
            $result['success'] = false;
            $result['messages'][] = 'Password must contain at least one number';
        } else {
            $result['score'] += 20;
        }
        
        // Check special characters
        if ($requirements['require_special'] && !preg_match('/[^a-zA-Z0-9]/', $password)) {
            $result['success'] = false;
            $result['messages'][] = 'Password must contain at least one special character';
        } else {
            $result['score'] += 20;
        }
        
        // Check forbidden patterns
        $password_lower = strtolower($password);
        foreach ($requirements['forbidden_patterns'] as $pattern) {
            if (strpos($password_lower, strtolower($pattern)) !== false) {
                $result['success'] = false;
                $result['messages'][] = 'Password cannot contain common words or patterns';
                $result['score'] -= 30;
                break;
            }
        }
        
        // Ensure score doesn't go below 0
        $result['score'] = max(0, $result['score']);
        
        return $result;
    }
    
    /**
     * Generate a secure random password
     *
     * @param int $length Password length
     * @param array $options Generation options
     * @return string Generated password
     */
    public function generate_password($length = 12, $options = array()) {
        // Default character sets
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $special = '!@#$%^&*()_+-=[]{}|;:,.<>?';
        
        // Build character pool
        $chars = '';
        $chars .= isset($options['include_uppercase']) && $options['include_uppercase'] === false ? '' : $uppercase;
        $chars .= isset($options['include_lowercase']) && $options['include_lowercase'] === false ? '' : $lowercase;
        $chars .= isset($options['include_numbers']) && $options['include_numbers'] === false ? '' : $numbers;
        $chars .= isset($options['include_special']) && $options['include_special'] === false ? '' : $special;
        
        if (empty($chars)) {
            $chars = $uppercase . $lowercase . $numbers; // Fallback
        }
        
        $password = '';
        $chars_length = strlen($chars);
        
        // Generate password
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, $chars_length - 1)];
        }
        
        return $password;
    }
    
    /**
     * Check if hash is a bcrypt hash
     *
     * @param string $hash The hash to check
     * @return boolean True if bcrypt hash
     */
    private function is_bcrypt_hash($hash) {
        return preg_match('/^\$2[axy]?\$\d{2}\$/', $hash);
    }
    
    /**
     * Verify legacy hash formats
     *
     * @param string $password Plain text password
     * @param string $hash Stored hash
     * @param string $type Hash type (sha1, md5, plain)
     * @return boolean True if password matches
     */
    private function verify_legacy_hash($password, $hash, $type) {
        switch ($type) {
            case 'sha1':
                return hash('sha1', $password) === $hash;
                
            case 'md5':
                return hash('md5', $password) === $hash;
                
            case 'plain':
                // EXTREMELY INSECURE - only for emergency migration
                log_message('error', 'Secure_Password: Plain text password detected - CRITICAL SECURITY ISSUE');
                return $password === $hash;
                
            default:
                return false;
        }
    }
    
    /**
     * Fallback bcrypt hash implementation
     *
     * @param string $password The password to hash
     * @param int $cost The cost parameter
     * @return string|false The hash or false on failure
     */
    private function bcrypt_hash($password, $cost) {
        // Generate salt
        $salt = $this->generate_salt();
        $salt_formatted = sprintf('$2y$%02d$%s', $cost, $salt);
        
        // Create hash
        $hash = crypt($password, $salt_formatted);
        
        return (strlen($hash) === 60) ? $hash : false;
    }
    
    /**
     * Fallback bcrypt verification
     *
     * @param string $password The password to verify
     * @param string $hash The hash to verify against
     * @return boolean True if password matches
     */
    private function bcrypt_verify($password, $hash) {
        if (strlen($hash) !== 60) {
            return false;
        }
        
        $test_hash = crypt($password, $hash);
        
        if (strlen($test_hash) !== 60) {
            return false;
        }
        
        // Timing-safe comparison
        $result = 0;
        for ($i = 0; $i < 60; $i++) {
            $result |= (ord($test_hash[$i]) ^ ord($hash[$i]));
        }
        
        return $result === 0;
    }
    
    /**
     * Generate cryptographically secure salt
     *
     * @return string Base64 encoded salt
     */
    private function generate_salt() {
        // Try different secure random sources
        $salt = '';
        
        if (function_exists('random_bytes')) {
            try {
                $salt = random_bytes(16);
            } catch (Exception $e) {
                log_message('error', 'Secure_Password: random_bytes failed: ' . $e->getMessage());
            }
        }
        
        if (empty($salt) && function_exists('openssl_random_pseudo_bytes')) {
            $salt = openssl_random_pseudo_bytes(16, $strong);
            if (!$strong) {
                $salt = '';
                log_message('error', 'Secure_Password: openssl_random_pseudo_bytes not cryptographically strong');
            }
        }
        
        if (empty($salt) && defined('MCRYPT_DEV_URANDOM')) {
            $salt = mcrypt_create_iv(16, MCRYPT_DEV_URANDOM);
        }
        
        if (empty($salt)) {
            // Fallback to less secure method
            log_message('error', 'Secure_Password: Using fallback salt generation method');
            $salt = '';
            for ($i = 0; $i < 16; $i++) {
                $salt .= chr(mt_rand(0, 255));
            }
        }
        
        // Encode for bcrypt
        return substr(strtr(base64_encode($salt), '+', '.'), 0, 22);
    }
    
    /**
     * Get information about a hash
     *
     * @param string $hash The hash to analyze
     * @return array|false Hash information or false
     */
    private function get_hash_info($hash) {
        if (function_exists('password_get_info')) {
            return password_get_info($hash);
        }
        
        // Manual parsing for bcrypt
        if ($this->is_bcrypt_hash($hash)) {
            if (preg_match('/^\$2[axy]?\$(\d{2})\$/', $hash, $matches)) {
                return array(
                    'algo' => 1,
                    'algoName' => 'bcrypt',
                    'cost' => (int)$matches[1]
                );
            }
        }
        
        return false;
    }
}

/* End of file Secure_Password.php */
/* Location: ./application/libraries/Secure_Password.php */ 
