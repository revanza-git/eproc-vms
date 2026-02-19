<?php
class Secure_Password_test extends TestCase
{
    /** @var Secure_Password */
    private $sp;

    public function setUp(): void
    {
        parent::setUp();
        $this->CI =& get_instance();
        // Load library via CI loader; CI converts names to lowercase by default
        $this->CI->load->library('secure_password');
        $this->sp = $this->CI->secure_password;
    }

    public function test_hash_and_verify_success()
    {
        $plain = 'MyS3cureP@ssw0rd';
        $hash  = $this->sp->hash_password($plain);

        // Hash should be non-empty and start with bcrypt signature
        $this->assertNotEmpty($hash);
        $this->assertStringStartsWith('$2', $hash);

        $this->assertTrue($this->sp->verify_password($plain, $hash));
    }

    public function test_hash_and_verify_fail()
    {
        $plain = 'CorrectHorseBatteryStaple';
        $hash  = $this->sp->hash_password($plain);

        $this->assertFalse($this->sp->verify_password('WrongPassword', $hash));
    }

    public function test_needs_rehash_for_legacy_sha1()
    {
        $legacyHash = sha1('legacyPass');
        $this->assertTrue($this->sp->needs_rehash($legacyHash));
    }

    public function test_needs_rehash_for_current_hash()
    {
        $hash = $this->sp->hash_password('abc123!!!');
        $this->assertFalse($this->sp->needs_rehash($hash));
    }
} 
