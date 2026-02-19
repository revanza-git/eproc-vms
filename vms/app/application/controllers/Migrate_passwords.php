<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migrate_passwords extends CI_Controller
{
	public function index()
	{
		if (!is_cli()) {
			show_404();
		}

		$this->load->library('secure_password');

		$rows = $this->db->select('id, username, password')->get('ms_login')->result_array();

		$updated = 0;
		$skipped_bcrypt = 0;
		$skipped_legacy_hash = 0;
		$skipped_empty = 0;

		foreach ($rows as $row) {
			$password = isset($row['password']) ? (string) $row['password'] : '';

			if ($password === '') {
				$skipped_empty++;
				continue;
			}

			if (preg_match('/^\\$2[axy]\\$/', $password)) {
				$skipped_bcrypt++;
				continue;
			}

			if (preg_match('/^[a-f0-9]{32}$/i', $password) || preg_match('/^[a-f0-9]{40}$/i', $password)) {
				$skipped_legacy_hash++;
				continue;
			}

			$new_hash = $this->secure_password->hash_password($password);
			if (!$new_hash) {
				continue;
			}

			$this->db->where('id', (int) $row['id'])->update('ms_login', array('password' => $new_hash));
			$updated++;
		}

		echo "updated={$updated} skipped_bcrypt={$skipped_bcrypt} skipped_legacy_hash={$skipped_legacy_hash} skipped_empty={$skipped_empty}" . PHP_EOL;
	}
}

