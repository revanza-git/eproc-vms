<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * JWT Token Library for Admin Authentication
 * Handles creation and validation of JWT tokens for admin users
 */
class Jwt_token {

    private $CI;
    private $secret_key;
    private $algorithm;
    private $expire_time;

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->config('config');
        
        $this->secret_key = $this->CI->config->item('jwt_secret_key');
        $this->algorithm = $this->CI->config->item('jwt_algorithm');
        $this->expire_time = $this->CI->config->item('jwt_expire_time');
        
        // Validate configuration
        if (empty($this->secret_key) || $this->secret_key === 'your_jwt_secret_key_here_change_in_production') {
            log_message('error', 'JWT: Insecure or missing JWT secret key configuration');
        }
    }

    /**
     * Generate JWT token for admin user
     */
    public function generate_admin_token($admin_data) {
        try {
            $issued_at = time();
            $expiration_time = $issued_at + $this->expire_time;
            
            // Create JWT payload
            $payload = array(
                'iss' => $this->CI->config->item('base_app'), // Issuer
                'iat' => $issued_at, // Issued at
                'exp' => $expiration_time, // Expiration time
                'sub' => $admin_data['id_user'], // Subject (user ID)
                'data' => array(
                    'id_user' => $admin_data['id_user'],
                    'name' => $admin_data['name'],
                    'id_sbu' => $admin_data['id_sbu'],
                    'id_role' => $admin_data['id_role'],
                    'role_name' => $admin_data['role_name'],
                    'sbu_name' => $admin_data['sbu_name'],
                    'app' => $admin_data['app'],
                    'app_type' => isset($admin_data['app_type']) ? $admin_data['app_type'] : null,
                    'id_division' => isset($admin_data['id_division']) ? $admin_data['id_division'] : null,
                    'type' => 'admin'
                )
            );

            // Generate token
            $token = $this->encode($payload);
            
            if ($token) {
                log_message('info', 'JWT: Token generated successfully for admin user: ' . $admin_data['id_user']);
                return $token;
            } else {
                log_message('error', 'JWT: Failed to generate token for admin user: ' . $admin_data['id_user']);
                return false;
            }
            
        } catch (Exception $e) {
            log_message('error', 'JWT: Exception during token generation: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Validate JWT token
     */
    public function validate_token($token) {
        try {
            $decoded = $this->decode($token);
            
            if ($decoded && isset($decoded['exp']) && $decoded['exp'] > time()) {
                return $decoded;
            } else {
                log_message('error', 'JWT: Token validation failed - expired or invalid');
                return false;
            }
            
        } catch (Exception $e) {
            log_message('error', 'JWT: Exception during token validation: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Simple JWT encode function
     */
    private function encode($payload) {
        $header = json_encode(array('typ' => 'JWT', 'alg' => $this->algorithm));
        $payload = json_encode($payload);
        
        $headerEncoded = $this->base64url_encode($header);
        $payloadEncoded = $this->base64url_encode($payload);
        
        $signature = hash_hmac('sha256', $headerEncoded . "." . $payloadEncoded, $this->secret_key, true);
        $signatureEncoded = $this->base64url_encode($signature);
        
        return $headerEncoded . "." . $payloadEncoded . "." . $signatureEncoded;
    }

    /**
     * Simple JWT decode function
     */
    private function decode($jwt) {
        $tokenParts = explode('.', $jwt);
        
        if (count($tokenParts) !== 3) {
            return false;
        }
        
        $header = json_decode($this->base64url_decode($tokenParts[0]), true);
        $payload = json_decode($this->base64url_decode($tokenParts[1]), true);
        $signatureProvided = $tokenParts[2];
        
        // Verify signature
        $expectedSignature = $this->base64url_encode(hash_hmac('sha256', $tokenParts[0] . "." . $tokenParts[1], $this->secret_key, true));
        
        if ($signatureProvided === $expectedSignature) {
            return $payload;
        } else {
            return false;
        }
    }

    /**
     * Base64 URL encode
     */
    private function base64url_encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Base64 URL decode
     */
    private function base64url_decode($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }

    /**
     * Get admin redirect URL with JWT token
     */
    public function get_admin_redirect_url($admin_data) {
        $token = $this->generate_admin_token($admin_data);
        
        if ($token) {
            $intra_url = $this->CI->config->item('admin_intra_url');
            return $intra_url . '?jwt=' . urlencode($token);
        }
        
        return false;
    }
} 
