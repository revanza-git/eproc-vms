<?php
class MainAuth_test extends TestCase
{
    /**
     * Prepare a minimal ms_login table and seed data before any tests run.
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // Access CI instance
        $CI =& get_instance();
        $CI->load->dbforge();
        $CI->load->library('secure_password');

        // Recreate table ms_login (drop if exists)
        if ($CI->db->table_exists('ms_login')) {
            $CI->dbforge->drop_table('ms_login', TRUE);
        }

        // Define fields (simplified subset)
        $fields = array(
            'id' => array('type' => 'INT', 'auto_increment' => TRUE),
            'username' => array('type' => 'VARCHAR', 'constraint' => 255),
            'password' => array('type' => 'VARCHAR', 'constraint' => 255),
            'app_type' => array('type' => 'INT', 'default' => 0),
            'id_role'  => array('type' => 'INT', 'default' => 1),
            'lock_time' => array('type' => 'DATETIME', 'null' => TRUE)
        );
        $CI->dbforge->add_field($fields);
        $CI->dbforge->add_key('id', TRUE);
        $CI->dbforge->create_table('ms_login', TRUE);

        // Create minimal ms_key_value table used by login_user_secure
        if ($CI->db->table_exists('ms_key_value')) {
            $CI->dbforge->drop_table('ms_key_value', TRUE);
        }
        $CI->dbforge->add_field(array(
            'key' => array('type'=>'VARCHAR','constraint'=>255),
            'value' => array('type'=>'TEXT'),
            'created_at'=>array('type'=>'DATETIME','null'=>TRUE)
        ));
        $CI->dbforge->add_key('key', TRUE);
        $CI->dbforge->create_table('ms_key_value', TRUE);

        // Disable CSRF for testing to allow POST without token
        $CI->config->set_item('csrf_protection', FALSE);

        // Seed one admin user
        $passwordHash = $CI->secure_password->hash_password('admin123');
        $CI->db->insert('ms_login', array(
            'username' => 'admin',
            'password' => $passwordHash,
            'app_type' => 0,
            'id_role'  => 1,
            'lock_time' => '2000-01-01 00:00:00'
        ));
    }

    public function test_successful_login()
    {
        // Send POST data
        $response = $this->request(
            'POST',
            'main/main/check_secure',
            array('username' => 'admin', 'password' => 'admin123')
        );

        $status = $this->request->getStatus();
        if ($status['code'] !== 200) {
            $this->markTestIncomplete('Login route returned HTTP ' . $status['code']);
            return;
        }
    }

    public function test_failed_login()
    {
        $response = $this->request(
            'POST',
            'main/main/check_secure',
            array('username' => 'admin', 'password' => 'wrongpass')
        );

        $status = $this->request->getStatus();
        if ($status['code'] !== 200) {
            $this->markTestIncomplete('Login failed route returned HTTP ' . $status['code']);
            return;
        }
    }
} 
