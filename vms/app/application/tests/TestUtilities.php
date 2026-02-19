<?php
/**
 * Test Utilities Class
 * 
 * Collection of utility methods for testing
 */
class TestUtilities
{
    /**
     * Clear mock session data
     */
    public static function clearMockSession()
    {
        // In test environment, just clear the $_SESSION array
        $_SESSION = [];
    }
    
    /**
     * Create mock session data
     */
    public static function createMockSession($data)
    {
        // In test environment, avoid actual session_start()
        // Just use $_SESSION superglobal for testing
        if (!isset($_SESSION)) {
            $_SESSION = [];
        }
        
        foreach ($data as $key => $value) {
            $_SESSION[$key] = $value;
        }
    }
    
    /**
     * Get mock session data
     */
    public static function getMockSessionData($key)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }
    
    /**
     * Clear mock data (POST, GET, etc.)
     */
    public static function clearMockData()
    {
        $_POST = [];
        $_GET = [];
        $_SERVER = [];
    }
    
    /**
     * Mock POST data
     */
    public static function mockPostData($data)
    {
        $_POST = $data;
    }
    
    /**
     * Mock GET data
     */
    public static function mockGetData($data)
    {
        $_GET = $data;
    }
    
    /**
     * Mock SERVER data
     */
    public static function mockServerData($data)
    {
        foreach ($data as $key => $value) {
            $_SERVER[$key] = $value;
        }
    }
    
    /**
     * Get CodeIgniter instance with database loaded
     */
    private static function getCIWithDatabase()
    {
        $CI = &get_instance();
        
        // Ensure database is loaded
        if (!isset($CI->db) || $CI->db === null) {
            $CI->load->database();
        }
        
        return $CI;
    }
    
    /**
     * Create test table
     */
    public static function createTestTable($table_name, $fields)
    {
        $fields = self::normalizeTableFields($table_name, $fields);
        $CI = self::getCIWithDatabase();
        
        // Drop table if exists
        $CI->db->query("DROP TABLE IF EXISTS `{$table_name}`");
        
        // Build CREATE TABLE query
        $sql = "CREATE TABLE `{$table_name}` (";
        $field_definitions = [];
        
        foreach ($fields as $field_name => $field_config) {
            $definition = "`{$field_name}` ";
            
            // Add data type
            if (isset($field_config['type'])) {
                $definition .= $field_config['type'];
                
                // Add constraint if specified
                if (isset($field_config['constraint'])) {
                    $definition .= "({$field_config['constraint']})";
                }
            }
            
            // Add null/not null
            if (isset($field_config['null']) && $field_config['null'] === TRUE) {
                $definition .= " NULL";
            } else {
                $definition .= " NOT NULL";
            }
            
            // Add default
            if (isset($field_config['default'])) {
                if ($field_config['default'] === 'CURRENT_TIMESTAMP') {
                    $definition .= " DEFAULT CURRENT_TIMESTAMP";
                } else {
                    $definition .= " DEFAULT '{$field_config['default']}'";
                }
            }
            
            // Add auto increment
            if (isset($field_config['auto_increment']) && $field_config['auto_increment'] === TRUE) {
                $definition .= " AUTO_INCREMENT PRIMARY KEY";
            }
            
            $field_definitions[] = $definition;
        }
        
        $sql .= implode(', ', $field_definitions);
        $sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8";
        
        $CI->db->query($sql);
    }

    private static function normalizeTableFields($table_name, $fields)
    {
        if (!is_array($fields)) {
            $fields = [];
        }

        if ($table_name === 'ms_vendor') {
            $defaults = [
                'id_sbu' => ['type' => 'INT', 'default' => 0],
                'vendor_status' => ['type' => 'INT', 'default' => 0],
                'is_active' => ['type' => 'INT', 'default' => 1],
                'nama' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE],
            ];
            foreach ($defaults as $key => $config) {
                if (!isset($fields[$key])) {
                    $fields[$key] = $config;
                }
            }
        }

        return $fields;
    }
    
    /**
     * Insert test data
     */
    public static function insertTestData($table_name, $data)
    {
        $CI = self::getCIWithDatabase();
        
        if (self::isAssociativeArray($data)) {
            // Single row
            $CI->db->insert($table_name, $data);
        } else {
            // Multiple rows
            foreach ($data as $row) {
                $CI->db->insert($table_name, $row);
            }
        }
    }
    
    /**
     * Check if array is associative
     */
    private static function isAssociativeArray($array)
    {
        if (!is_array($array)) {
            return false;
        }
        return array_keys($array) !== range(0, count($array) - 1);
    }
    
    /**
     * Truncate test table
     */
    public static function truncateTable($table_name)
    {
        $CI = self::getCIWithDatabase();
        $CI->db->truncate($table_name);
    }
    
    /**
     * Get table row count with optional conditions
     */
    public static function getTableRowCount($table_name, $conditions = [])
    {
        $CI = self::getCIWithDatabase();
        
        if (!empty($conditions)) {
            $CI->db->where($conditions);
        }
        
        return $CI->db->count_all_results($table_name);
    }
    
    /**
     * Drop test table
     */
    public static function dropTestTable($table_name)
    {
        $CI = self::getCIWithDatabase();
        $CI->db->query("DROP TABLE IF EXISTS `{$table_name}`");
    }
    
    /**
     * Setup test database
     */
    public static function setupTestDatabase()
    {
        $CI = self::getCIWithDatabase();
        
        // Switch to test database if configured
        if (isset($CI->db->database_test)) {
            $CI->db->db_select($CI->db->database_test);
        }
    }
    
    /**
     * Clean up test data
     */
    public static function cleanupTestData()
    {
        // Clean up any global test data
        self::clearMockSession();
        self::clearMockData();
    }
    
    /**
     * Execute query safely
     */
    public static function executeQuery($sql, $bindings = [])
    {
        $CI = self::getCIWithDatabase();
        
        if (!empty($bindings)) {
            return $CI->db->query($sql, $bindings);
        } else {
            return $CI->db->query($sql);
        }
    }
    
    /**
     * Begin transaction
     */
    public static function beginTransaction()
    {
        $CI = self::getCIWithDatabase();
        $CI->db->trans_begin();
    }
    
    /**
     * Commit transaction
     */
    public static function commitTransaction()
    {
        $CI = self::getCIWithDatabase();
        $CI->db->trans_commit();
    }
    
    /**
     * Rollback transaction
     */
    public static function rollbackTransaction()
    {
        $CI = self::getCIWithDatabase();
        $CI->db->trans_rollback();
    }
    
    /**
     * Generate random string
     */
    public static function randomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    
    /**
     * Generate random email
     */
    public static function randomEmail()
    {
        return self::randomString(8) . '@example.com';
    }
    
    /**
     * Create test user with proper database handling
     */
    public static function createTestUser($username, $password, $type = 'user')
    {
        $CI = self::getCIWithDatabase();
        
        $user_data = [
            'username' => $username,
            'password' => md5($password), // Use same hashing as your app
            'user_type' => $type,
            'created_at' => date('Y-m-d H:i:s'),
            'status' => 'active'
        ];
        
        // Check if users table exists, if not create basic structure
        if (!$CI->db->table_exists('users')) {
            self::createTestTable('users', [
                'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
                'username' => ['type' => 'VARCHAR', 'constraint' => 100],
                'password' => ['type' => 'VARCHAR', 'constraint' => 255],
                'user_type' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'user'],
                'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'active'],
                'created_at' => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP']
            ]);
        }
        
        $CI->db->insert('users', $user_data);
        return $CI->db->insert_id();
    }
    
    /**
     * Assert array has key
     */
    public static function assertArrayHasKey($key, $array, $message = '')
    {
        if (!array_key_exists($key, $array)) {
            throw new Exception($message ?: "Array does not contain key: {$key}");
        }
    }
    
    /**
     * Assert equals
     */
    public static function assertEquals($expected, $actual, $message = '')
    {
        if ($expected !== $actual) {
            throw new Exception($message ?: "Expected {$expected}, got {$actual}");
        }
    }
    
    /**
     * Assert true
     */
    public static function assertTrue($condition, $message = '')
    {
        if (!$condition) {
            throw new Exception($message ?: "Condition is not true");
        }
    }
    
    /**
     * Get CI instance (for backward compatibility)
     */
    public static function &getCIInstance()
    {
        return self::getCIWithDatabase();
    }
} 
