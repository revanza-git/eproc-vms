<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Security extends CI_Security
{
	private function get_csrf_token_from_headers()
	{
		$candidates = array(
			'HTTP_X_CSRF_TOKEN',
			'HTTP_X_XSRF_TOKEN'
		);

		foreach ($candidates as $key) {
			if (!empty($_SERVER[$key])) {
				return (string) $_SERVER[$key];
			}
		}

		return null;
	}

	public function csrf_verify()
	{
		if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST')
		{
			$this->csrf_set_cookie();
			if (!headers_sent()) {
				header('X-CSRF-Token: ' . $this->_csrf_hash);
			}
			return $this;
		}

		if ($exclude_uris = config_item('csrf_exclude_uris'))
		{
			$uri = load_class('URI', 'core');
			foreach ($exclude_uris as $excluded)
			{
				if (preg_match('#^'.$excluded.'$#i'.(UTF8_ENABLED ? 'u' : ''), $uri->uri_string()))
				{
					return $this;
				}
			}
		}

		if ( ! isset($_POST[$this->_csrf_token_name]) && isset($_COOKIE[$this->_csrf_cookie_name]))
		{
			$headerToken = $this->get_csrf_token_from_headers();
			if ($headerToken !== null && $headerToken !== '')
			{
				$_POST[$this->_csrf_token_name] = $headerToken;
			}
		}

		if ( ! isset($_POST[$this->_csrf_token_name], $_COOKIE[$this->_csrf_cookie_name])
			OR $_POST[$this->_csrf_token_name] !== $_COOKIE[$this->_csrf_cookie_name])
		{
			$this->csrf_show_error();
		}

		unset($_POST[$this->_csrf_token_name]);

		if (config_item('csrf_regenerate'))
		{
			unset($_COOKIE[$this->_csrf_cookie_name]);
			$this->_csrf_hash = NULL;
		}

		$this->_csrf_set_hash();
		$this->csrf_set_cookie();
		if (!headers_sent()) {
			header('X-CSRF-Token: ' . $this->_csrf_hash);
		}

		log_message('info', 'CSRF token verified');
		return $this;
	}

	public function csrf_show_error()
	{
		unset($_COOKIE[$this->_csrf_cookie_name]);
		$this->_csrf_hash = NULL;
		$this->_csrf_set_hash();
		$this->csrf_set_cookie();
		if (!headers_sent()) {
			header('X-CSRF-Token: ' . $this->_csrf_hash, true);
		}

		show_error('The action you have requested is not allowed.', 403);
	}
}
