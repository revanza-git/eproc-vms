<?php

namespace App\Application\Auth;

interface UserRepository
{
	public function findByUsername(string $username): ?array;

	public function updatePasswordHash(int $id, string $passwordHash): void;

	public function clearLockouts(int $id): void;
}

