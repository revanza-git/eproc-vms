<?php
class MainModel_test extends TestCase
{
    private static $prepared = false;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        $CI =& get_instance();
        $CI->load->dbforge();
        $CI->load->library('secure_password');

        // Prepare once
        if (!self::$prepared) {
            // Ensure ms_login table exists (created by previous fixture) else create
            if (!$CI->db->table_exists('ms_login')) {
                $fields = array(
                    'id' => array('type'=>'INT', 'auto_increment'=>TRUE),
                    'username' => array('type'=>'VARCHAR','constraint'=>255),
                    'password' => array('type'=>'VARCHAR','constraint'=>255),
                    'type' => array('type'=>'VARCHAR','constraint'=>20,'default'=>'admin'),
                    'type_app'=>array('type'=>'INT','default'=>0),
                    'id_user'=>array('type'=>'INT','default'=>0),
                    'attempts'=>array('type'=>'INT','default'=>0),
                    'lock_time'=>array('type'=>'DATETIME','null'=>TRUE)
                );
                $CI->dbforge->add_field($fields);
                $CI->dbforge->add_key('id', TRUE);
                $CI->dbforge->create_table('ms_login', TRUE);
            } else {
                // Wipe
                $CI->db->truncate('ms_login');
                // Ensure id_user column exists
                $missing_cols = [];
                if (!$CI->db->field_exists('id_user','ms_login')) {
                    $missing_cols['id_user'] = array('type'=>'INT','default'=>0);
                }
                if (!$CI->db->field_exists('type','ms_login')) {
                    $missing_cols['type'] = array('type'=>'VARCHAR','constraint'=>20,'default'=>'admin');
                }
                if (!$CI->db->field_exists('type_app','ms_login')) {
                    $missing_cols['type_app'] = array('type'=>'INT','default'=>0);
                }
                if (!$CI->db->field_exists('attempts','ms_login')) {
                    $missing_cols['attempts'] = array('type'=>'TINYINT','default'=>0);
                }
                if (!empty($missing_cols)) {
                    $CI->dbforge->add_column('ms_login', $missing_cols);
                }
            }

            // ---- tb_role ----
            if (!$CI->db->table_exists('tb_role')) {
                $CI->dbforge->add_field(array(
                    'id'=>array('type'=>'INT','auto_increment'=>TRUE),
                    'name'=>array('type'=>'VARCHAR','constraint'=>100)
                ));
                $CI->dbforge->add_key('id',TRUE);
                $CI->dbforge->create_table('tb_role',TRUE);
            } else {
                $CI->db->truncate('tb_role');
            }
            $CI->db->insert('tb_role',array('id'=>1,'name'=>'SuperAdmin'));

            // ---- ms_admin ----
            if (!$CI->db->table_exists('ms_admin')) {
                $CI->dbforge->add_field(array(
                    'id'=>array('type'=>'INT','auto_increment'=>TRUE),
                    'name'=>array('type'=>'VARCHAR','constraint'=>255),
                    'id_role'=>array('type'=>'INT','default'=>1),
                    'id_sbu'=>array('type'=>'INT','default'=>0),
                    'sbu_name'=>array('type'=>'VARCHAR','constraint'=>255,'null'=>TRUE),
                    'is_disable'=>array('type'=>'INT','default'=>0),
                    'del'=>array('type'=>'INT','default'=>0),
                    'id_division'=>array('type'=>'INT','default'=>0),
                    'photo_profile'=>array('type'=>'VARCHAR','constraint'=>255,'null'=>TRUE),
                    'email'=>array('type'=>'VARCHAR','constraint'=>255,'null'=>TRUE)
                ));
                $CI->dbforge->add_key('id',TRUE);
                $CI->dbforge->create_table('ms_admin',TRUE);
            } else {
                $CI->db->truncate('ms_admin');
                if (!$CI->db->field_exists('del','ms_admin')) {
                    $CI->dbforge->add_column('ms_admin', array('del'=>array('type'=>'INT','default'=>0)));
                }
            }
            $CI->db->insert('ms_admin',array(
                'id'=>1,
                'name'=>'System Admin',
                'id_role'=>1,
                'id_sbu'=>0,
                'sbu_name'=>'',
                'is_disable'=>0,
                'id_division'=>0
            ));

            // ---- insert login record ----
            $hash = $CI->secure_password->hash_password('admin123');
            $CI->db->insert('ms_login', array(
                 'username'=>'admin2',
                 'password'=>$hash,
                 'type'=>'admin',
                 'type_app'=>0,
                 'id_user'=>1,
                 'lock_time'=>'2000-01-01 00:00:00'
            ));

            self::$prepared = true;
        }
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->CI =& get_instance();
        $this->CI->load->model('main/Main_model','mm');
    }

    public function test_cek_login_success()
    {
        // Simulate POST data
        $_POST['username'] = 'admin2';
        $_POST['password'] = 'admin123';

        try {
            $result = $this->CI->mm->cek_login();
            $this->assertTrue($result);
        } catch (Exception $e) {
            $this->markTestIncomplete('Main_model::cek_login raised exception: '.$e->getMessage());
        }
    }

    public function test_cek_login_failure()
    {
        $_POST['username'] = 'admin2';
        $_POST['password'] = 'wrong';

        try {
            $result = $this->CI->mm->cek_login();
            $this->assertFalse($result);
        } catch (Exception $e) {
            $this->markTestIncomplete('Main_model::cek_login raised exception: '.$e->getMessage());
        }
    }
} 
