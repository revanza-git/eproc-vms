<?php

class SessionConfig_test extends TestCase
{
	public function test_session_save_path_respects_environment()
	{
		$expected = getenv('SESSION_SAVE_PATH');
		if (!$expected) {
			$this->markTestSkipped('SESSION_SAVE_PATH is not set');
		}

		$this->assertSame($expected, config_item('sess_save_path'));
	}
}

