<?php

require_once APPPATH . 'src/autoload.php';

class ResetPasswordService_test extends TestCase
{
	public function test_reset_requires_username()
	{
		$repo = new FakeUserRepository([]);
		$service = new \App\Application\Auth\ResetPasswordService($repo, function ($plain) {
			return 'hash:' . $plain;
		});

		$result = $service->reset('');
		$this->assertFalse($result['ok']);
		$this->assertSame('username_required', $result['error']);
	}

	public function test_reset_returns_user_not_found()
	{
		$repo = new FakeUserRepository([]);
		$service = new \App\Application\Auth\ResetPasswordService($repo, function ($plain) {
			return 'hash:' . $plain;
		});

		$result = $service->reset('nope');
		$this->assertFalse($result['ok']);
		$this->assertSame('user_not_found', $result['error']);
	}

	public function test_reset_generates_password_and_updates_hash()
	{
		$repo = new FakeUserRepository([
			'test' => ['id' => 7, 'username' => 'test'],
		]);
		$service = new \App\Application\Auth\ResetPasswordService($repo, function ($plain) {
			return 'hash:' . $plain;
		});

		$result = $service->reset('test', null);
		$this->assertTrue($result['ok']);
		$this->assertSame('test', $result['username']);
		$this->assertNotEmpty($result['password']);
		$this->assertSame('hash:' . $result['password'], $repo->lastPasswordHash);
		$this->assertSame(7, $repo->lastUpdatedUserId);
		$this->assertSame(7, $repo->lastClearedLockoutsUserId);
	}

	public function test_reset_uses_given_password()
	{
		$repo = new FakeUserRepository([
			'admin' => ['id' => 1, 'username' => 'admin'],
		]);
		$service = new \App\Application\Auth\ResetPasswordService($repo, function ($plain) {
			return 'hash:' . $plain;
		});

		$result = $service->reset('admin', 'MyNewPass123');
		$this->assertTrue($result['ok']);
		$this->assertSame('MyNewPass123', $result['password']);
		$this->assertSame('hash:MyNewPass123', $repo->lastPasswordHash);
	}
}

class FakeUserRepository implements \App\Application\Auth\UserRepository
{
	private $usersByUsername;

	public $lastUpdatedUserId;
	public $lastPasswordHash;
	public $lastClearedLockoutsUserId;

	public function __construct(array $usersByUsername)
	{
		$this->usersByUsername = $usersByUsername;
	}

	public function findByUsername(string $username): ?array
	{
		return isset($this->usersByUsername[$username]) ? $this->usersByUsername[$username] : null;
	}

	public function updatePasswordHash(int $id, string $passwordHash): void
	{
		$this->lastUpdatedUserId = $id;
		$this->lastPasswordHash = $passwordHash;
	}

	public function clearLockouts(int $id): void
	{
		$this->lastClearedLockoutsUserId = $id;
	}
}

