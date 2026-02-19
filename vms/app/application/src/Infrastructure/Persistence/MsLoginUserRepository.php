<?php

namespace App\Infrastructure\Persistence;

use App\Application\Auth\UserRepository;

class MsLoginUserRepository implements UserRepository
{
	private $db;

	public function __construct($db)
	{
		$this->db = $db;
	}

	public function findByUsername(string $username): ?array
	{
		$row = $this->db
			->select('id, username')
			->where('username', $username)
			->get('ms_login')
			->row_array();

		return $row ? $row : null;
	}

	public function updatePasswordHash(int $id, string $passwordHash): void
	{
		$this->db->where('id', $id)->update('ms_login', array('password' => $passwordHash));
	}

	public function clearLockouts(int $id): void
	{
		$this->db->where('id', $id)->update('ms_login', array(
			'attempts' => 0,
			'lock_time' => '2000-01-01 00:00:00'
		));
	}
}

