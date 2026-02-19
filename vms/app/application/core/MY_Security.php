<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Extended Security class that adds extra diagnostics for CSRF failures.
 */
class MY_Security extends CI_Security {

	/**
	 * Parse CSRF token from multipart form data
	 * This fixes issues with large file uploads where CodeIgniter fails to parse POST data correctly
	 */
	private function parse_csrf_from_multipart() {
		// Only proceed if this is a multipart form and POST data seems incomplete
		$content_type = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '';
		if (strpos($content_type, 'multipart/form-data') === FALSE) {
			return FALSE;
		}

		// Get the raw POST data
		$raw_data = file_get_contents('php://input');
		if (empty($raw_data)) {
			return FALSE;
		}

		// Extract boundary from content type
		preg_match('/boundary=(.*)$/', $content_type, $matches);
		if (empty($matches[1])) {
			return FALSE;
		}
		
		$boundary = $matches[1];
		$csrf_token_name = $this->_csrf_token_name;
		
		// Parse multipart data to find CSRF token
		$parts = preg_split("/-+$boundary/", $raw_data);
		foreach ($parts as $part) {
			if (empty($part)) continue;
			
			// Look for CSRF token field
			if (preg_match('/name="' . preg_quote($csrf_token_name) . '"/', $part)) {
				// Extract the value
				$lines = preg_split('/\\r\\n|\\r|\\n/', $part);
				$in_content = FALSE;
				foreach ($lines as $line) {
					if (empty($line) && !$in_content) {
						$in_content = TRUE;
						continue;
					}
					if ($in_content && !empty(trim($line))) {
						$token = trim($line);
						// Set it in POST data
						$_POST[$csrf_token_name] = $token;
						if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
							log_message('debug', "CSRF Token recovered from multipart");
						}
						return TRUE;
					}
				}
			}
		}
		
		return FALSE;
	}

	/**
	 * Enhanced CSRF verification that handles multipart forms better
	 */
	public function csrf_verify()
	{
		// If CSRF protection is disabled, behave like parent
		if (config_item('csrf_protection') === FALSE)
		{
			return parent::csrf_verify();
		}

		// For non-POST (e.g., GET, HEAD, OPTIONS) simply set the CSRF cookie via parent method
		if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST')
		{
			return parent::csrf_verify();
		}

		// Check if CSRF token is missing from POST but this is a multipart form
		if (empty($_POST[$this->_csrf_token_name]) && isset($_SERVER['CONTENT_TYPE']) && 
			strpos($_SERVER['CONTENT_TYPE'], 'multipart/form-data') !== FALSE) {
			
			if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
				log_message('debug', 'CSRF token missing from POST in multipart form, attempting to parse from raw data');
			}
			$this->parse_csrf_from_multipart();
		}

		// Log debug info (no token values)
		$post_token_present = isset($_POST[$this->_csrf_token_name]) ? 'yes' : 'no';
		$cookie_token_present = isset($_COOKIE[$this->_csrf_cookie_name]) ? 'yes' : 'no';
		$content_length = isset($_SERVER['CONTENT_LENGTH']) ? $_SERVER['CONTENT_LENGTH'] : '0';
		$files_info = !empty($_FILES) ? array_keys($_FILES) : [];
		
		if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
			log_message('debug', "CSRF Debug — token_name: {$this->_csrf_token_name} post_token_present: $post_token_present cookie_token_present: $cookie_token_present content_length: $content_length files: " . json_encode($files_info));
		}

		// If no $_POST data, it could be a CSRF attack
		if (empty($_POST) && config_item('csrf_protection') && ! $this->csrf_exclude_uris())
		{
			$this->csrf_show_error();
		}

		// Check if CSRF token name and cookie exist
		if ( ! isset($_POST[$this->_csrf_token_name], $_COOKIE[$this->_csrf_cookie_name]))
		{
			log_message('error', 'CSRF Mismatch — post_token and cookie_token do not match or are missing');
			$this->csrf_show_error();
		}

		// Check if tokens match
		if ($_POST[$this->_csrf_token_name] !== $_COOKIE[$this->_csrf_cookie_name])
		{
			log_message('error', "CSRF Token mismatch — POST and COOKIE do not match");
			$this->csrf_show_error();
		}

		// If regenerate option is true, remove the token and create a new one
		if (config_item('csrf_regenerate'))
		{
			unset($_POST[$this->_csrf_token_name]);
			$this->_csrf_set_hash();
		}

		unset($_POST[$this->_csrf_token_name]);

		if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
			log_message('debug', 'CSRF token validated successfully');
		}
	}
} 
