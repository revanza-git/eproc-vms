<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migrate_passwords extends CI_Controller
{
	public function index()
	{
		if (!is_cli()) {
			show_404();
		}

		$db = $this->load->database('eproc', true);
		$rows = $db->select('id, username, password')->get('ms_login')->result_array();

		$updated = 0;
		$skipped_hashed = 0;
		$skipped_legacy_hash = 0;
		$skipped_empty = 0;

		foreach ($rows as $row) {
			$password = isset($row['password']) ? (string) $row['password'] : '';

			if ($password === '') {
				$skipped_empty++;
				continue;
			}

			if (preg_match('/^\\$(2y|2a|argon2id|argon2i)\\$/', $password)) {
				$skipped_hashed++;
				continue;
			}

			if (preg_match('/^[a-f0-9]{32}$/i', $password) || preg_match('/^[a-f0-9]{40}$/i', $password)) {
				$skipped_legacy_hash++;
				continue;
			}

			$new_hash = password_hash($password, PASSWORD_DEFAULT);
			if (!$new_hash) {
				continue;
			}

			$db->where('id', (int) $row['id'])->update('ms_login', array('password' => $new_hash));
			$updated++;
		}

		echo "updated={$updated} skipped_hashed={$skipped_hashed} skipped_legacy_hash={$skipped_legacy_hash} skipped_empty={$skipped_empty}" . PHP_EOL;
	}
}

