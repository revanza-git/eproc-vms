<?php defined('BASEPATH') OR exit('No direct script access allowed');

class CsrfHeaderHook {
	public function addHeader() {
		$CI =& get_instance();
		if (!isset($CI->security) || !method_exists($CI->security, 'get_csrf_hash')) {
			return;
		}

		$CI->output->set_header('X-CSRF-Token: ' . $CI->security->get_csrf_hash());
	}
}

