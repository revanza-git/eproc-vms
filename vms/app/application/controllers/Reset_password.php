<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Reset_password extends CI_Controller
{
	public function index($username = null, $password = null)
	{
		if (!is_cli()) {
			show_404();
		}

		$username = is_string($username) ? trim($username) : '';
		$password = is_string($password) ? $password : '';

		if ($username === '') {
			echo "Usage:\n";
			echo "  php index.php reset_password <username> [new_password]\n";
			echo "If new_password is omitted, a random one is generated.\n";
			exit(1);
		}

		$this->load->library('secure_password');

		$repo = new \App\Infrastructure\Persistence\MsLoginUserRepository($this->db);
		$service = new \App\Application\Auth\ResetPasswordService($repo, function ($plain) {
			return $this->secure_password->hash_password($plain);
		});

		$result = $service->reset($username, $password === '' ? null : $password);
		if (!isset($result['ok']) || $result['ok'] !== true) {
			$error = isset($result['error']) ? (string) $result['error'] : 'unknown';
			if ($error === 'user_not_found') {
				echo "User not found: {$username}\n";
				exit(2);
			}
			if ($error === 'hash_failed') {
				echo "Failed to hash password\n";
				exit(3);
			}
			echo "Failed to reset password\n";
			exit(4);
		}

		echo "Password reset for {$result['username']}\n";
		echo "Temporary password: {$result['password']}\n";
	}
}
