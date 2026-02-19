<?php

namespace App\Application\Auth;

class ResetPasswordService
{
	private $users;
	private $hashPassword;

	public function __construct(UserRepository $users, callable $hashPassword)
	{
		$this->users = $users;
		$this->hashPassword = $hashPassword;
	}

	public function reset(string $username, ?string $newPassword = null): array
	{
		$username = trim($username);
		if ($username === '') {
			return array('ok' => false, 'error' => 'username_required');
		}

		$user = $this->users->findByUsername($username);
		if (!$user) {
			return array('ok' => false, 'error' => 'user_not_found');
		}

		$password = $newPassword;
		if ($password === null || $password === '') {
			$password = rtrim(strtr(base64_encode(random_bytes(18)), '+/', 'ab'), '=');
		}

		$hash = call_user_func($this->hashPassword, $password);
		if (!$hash) {
			return array('ok' => false, 'error' => 'hash_failed');
		}

		$this->users->updatePasswordHash((int) $user['id'], $hash);
		$this->users->clearLockouts((int) $user['id']);

		return array('ok' => true, 'username' => $username, 'password' => $password);
	}
}

