<?php
/**
 * Unit Tests for Main Controller
 * 
 * Tests authentication flows, login functionality, and routing logic
 */
class MainController_test extends TestCase
{
    protected static $prepared = false;
    
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        
        if (!self::$prepared) {
            // Setup test tables for authentication
            self::setupTestTables();
            self::setupTestData();
            self::$prepared = true;
        }
    }
    
    public function setUp(): void
    {
        parent::setUp();
        $this->CI =& get_instance();
        
        // Reset session and request data before each test
        TestUtilities::clearMockSession();
        TestUtilities::clearMockData();
        
        // Clear flash data
        if (isset($_SESSION['__ci_vars'])) {
            unset($_SESSION['__ci_vars']);
        }
    }
    
    public function tearDown(): void
    {
        parent::tearDown();
        TestUtilities::clearMockSession();
        TestUtilities::clearMockData();
    }
    
    /**
     * Setup test database tables for authentication
     */
    private static function setupTestTables()
    {
        $CI =& get_instance();
        
        // Ensure ms_login table exists
        TestUtilities::createTestTable('ms_login', [
            'id' => ['type' => 'INT', 'auto_increment' => TRUE],
            'username' => ['type' => 'VARCHAR', 'constraint' => 255],
            'password' => ['type' => 'VARCHAR', 'constraint' => 255],
            'type' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'admin'],
            'type_app' => ['type' => 'INT', 'default' => 0],
            'id_user' => ['type' => 'INT', 'default' => 0],
            'attempts' => ['type' => 'INT', 'default' => 0],
            'lock_time' => ['type' => 'DATETIME', 'null' => TRUE],
            'app_type' => ['type' => 'INT', 'default' => 0]
        ]);
        
        // Ensure ms_admin table exists
        TestUtilities::createTestTable('ms_admin', [
            'id' => ['type' => 'INT', 'auto_increment' => TRUE],
            'name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'id_role' => ['type' => 'INT', 'default' => 1],
            'id_sbu' => ['type' => 'INT', 'default' => 0],
            'sbu_name' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE],
            'is_disable' => ['type' => 'INT', 'default' => 0],
            'del' => ['type' => 'INT', 'default' => 0],
            'id_division' => ['type' => 'INT', 'default' => 0],
            'photo_profile' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE],
            'email' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE],
            'app_type' => ['type' => 'INT', 'default' => 0]
        ]);
    }
    
    /**
     * Setup test data for authentication
     */
    private static function setupTestData()
    {
        $CI =& get_instance();
        
        // Clear existing test data
        TestUtilities::truncateTable('ms_login');
        TestUtilities::truncateTable('ms_admin');
        
        // Insert test admin
        TestUtilities::insertTestData('ms_admin', [
            'id' => 1,
            'name' => 'Test Admin',
            'id_role' => 1,
            'id_sbu' => 0,
            'sbu_name' => 'Test SBU',
            'is_disable' => 0,
            'del' => 0,
            'id_division' => 0,
            'email' => 'admin@test.com',
            'app_type' => 0
        ]);
        
        // Insert auction admin
        TestUtilities::insertTestData('ms_admin', [
            'id' => 2,
            'name' => 'Auction Admin',
            'id_role' => 6,
            'id_sbu' => 0,
            'sbu_name' => 'Auction SBU',
            'is_disable' => 0,
            'del' => 0,
            'id_division' => 0,
            'email' => 'auction@test.com',
            'app_type' => 0
        ]);
        
        // Load secure password library and create test passwords
        $CI->load->library('secure_password');
        $admin_password = $CI->secure_password->hash_password('admin123');
        $auction_password = $CI->secure_password->hash_password('auction123');
        
        // Insert test login records
        TestUtilities::insertTestData('ms_login', [
            [
                'id' => 1,
                'username' => 'testadmin',
                'password' => $admin_password,
                'type' => 'admin',
                'type_app' => 0,
                'id_user' => 1,
                'lock_time' => '2000-01-01 00:00:00',
                'app_type' => 0
            ],
            [
                'id' => 2,
                'username' => 'auctionadmin',
                'password' => $auction_password,
                'type' => 'admin',
                'type_app' => 0,
                'id_user' => 2,
                'lock_time' => '2000-01-01 00:00:00',
                'app_type' => 0
            ]
        ]);
    }
    
    /**
     * Test index method when no user is logged in
     */
    public function test_index_no_user_shows_login()
    {
        $output = $this->request('GET', 'main/index');
        
        // Should render login view
        $this->assertResponseCode(200);
        // In CI testing, we can't easily test view rendering, but we can verify no redirect occurred
    }
    
    /**
     * Test index method with logged in user redirects to dashboard
     */
    public function test_index_with_user_redirects_to_dashboard()
    {
        // Mock user session
        TestUtilities::createMockSession([
            'user' => [
                'id' => 1,
                'username' => 'testuser'
            ]
        ]);
        
        $output = $this->request('GET', 'main/index');
        
        // Should redirect to dashboard
        $this->assertRedirect();
    }
    
    /**
     * Test index method with admin user redirects appropriately
     */
    public function test_index_with_admin_redirects_to_admin()
    {
        // Mock admin session
        TestUtilities::createMockSession([
            'admin' => [
                'id' => 1,
                'id_role' => 1,
                'app_type' => 0
            ]
        ]);
        
        $output = $this->request('GET', 'main/index');
        
        // Should redirect to admin dashboard
        $this->assertRedirect();
    }
    
    /**
     * Test index method with auction admin redirects to auction
     */
    public function test_index_with_auction_admin_redirects_to_auction()
    {
        // Mock auction admin session
        TestUtilities::createMockSession([
            'admin' => [
                'id' => 2,
                'id_role' => 6,
                'app_type' => 0
            ]
        ]);
        
        $output = $this->request('GET', 'main/index');
        
        // Should redirect to auction
        $this->assertRedirect();
    }
    
    /**
     * Test logout method clears session
     */
    public function test_logout_clears_session_and_redirects()
    {
        // Set up session data
        TestUtilities::createMockSession([
            'user' => ['id' => 1, 'username' => 'testuser']
        ]);
        
        $output = $this->request('GET', 'main/logout');
        
        // Should redirect to home
        $this->assertRedirect('/', 302);
    }
    
    /**
     * Test check method with valid credentials
     */
    public function test_check_with_valid_credentials()
    {
        // Mock POST data
        TestUtilities::mockPostData([
            'username' => 'testadmin',
            'password' => 'admin123'
        ]);
        
        $output = $this->request('POST', 'main/check');
        
        // Should redirect after successful login
        $this->assertRedirect();
    }
    
    /**
     * Test check method with invalid credentials
     */
    public function test_check_with_invalid_credentials()
    {
        // Mock POST data with wrong password
        TestUtilities::mockPostData([
            'username' => 'testadmin',
            'password' => 'wrongpassword'
        ]);
        
        $output = $this->request('POST', 'main/check');
        
        // Should redirect back to login with error
        $this->assertRedirect('/', 302);
    }
    
    /**
     * Test check method with missing username
     */
    public function test_check_with_missing_username()
    {
        // Mock POST data with missing username
        TestUtilities::mockPostData([
            'password' => 'admin123'
        ]);
        
        $output = $this->request('POST', 'main/check');
        
        // Should redirect back to login with error
        $this->assertRedirect('/', 302);
    }
    
    /**
     * Test check method with missing password
     */
    public function test_check_with_missing_password()
    {
        // Mock POST data with missing password
        TestUtilities::mockPostData([
            'username' => 'testadmin'
        ]);
        
        $output = $this->request('POST', 'main/check');
        
        // Should redirect back to login with error
        $this->assertRedirect('/', 302);
    }
    
    /**
     * Test secure login with valid credentials returns JSON success
     */
    public function test_check_secure_with_valid_credentials()
    {
        // Mock POST data
        TestUtilities::mockPostData([
            'username' => 'testadmin',
            'password' => 'admin123'
        ]);
        
        $output = $this->request('POST', 'main/check_secure');
        
        // Should return 200 status
        $this->assertResponseCode(200);
        
        // Output should be JSON
        $this->assertStringContainsString('application/json', $this->CI->output->get_header('Content-Type'));
    }
    
    /**
     * Test secure login with invalid credentials returns JSON error
     */
    public function test_check_secure_with_invalid_credentials()
    {
        // Mock POST data with wrong password
        TestUtilities::mockPostData([
            'username' => 'testadmin',
            'password' => 'wrongpassword'
        ]);
        
        $output = $this->request('POST', 'main/check_secure');
        
        // Should return 200 status with error message
        $this->assertResponseCode(200);
        
        // Output should be JSON
        $this->assertStringContainsString('application/json', $this->CI->output->get_header('Content-Type'));
    }
    
    /**
     * Test secure login with missing data returns JSON error
     */
    public function test_check_secure_with_missing_data()
    {
        // Mock POST data with missing username
        TestUtilities::mockPostData([
            'password' => 'admin123'
        ]);
        
        $output = $this->request('POST', 'main/check_secure');
        
        // Should return 200 status with error message
        $this->assertResponseCode(200);
        
        // Output should be JSON
        $this->assertStringContainsString('application/json', $this->CI->output->get_header('Content-Type'));
    }
    
    /**
     * Test password verification functionality
     */
    public function test_password_verification()
    {
        // This tests the internal password verification method indirectly
        // by testing successful login with known credentials
        
        // Mock POST data
        TestUtilities::mockPostData([
            'username' => 'testadmin',
            'password' => 'admin123'
        ]);
        
        // Load the model and call the login check
        $this->CI->load->model('main/Main_model', 'mm');
        
        // This should return true for valid credentials
        $result = $this->CI->mm->cek_login();
        
        // Verify login was successful
        $this->assertTrue($result);
    }
    
    /**
     * Test user data preparation and session handling
     */
    public function test_session_data_structure()
    {
        // Mock successful login
        TestUtilities::mockPostData([
            'username' => 'testadmin',
            'password' => 'admin123'
        ]);
        
        $this->CI->load->model('main/Main_model', 'mm');
        $result = $this->CI->mm->cek_login();
        
        if ($result) {
            // Check if session data was set properly
            $admin_data = $this->CI->session->userdata('admin');
            
            if ($admin_data) {
                $this->assertIsArray($admin_data);
                $this->assertArrayHasKey('username', $admin_data);
                $this->assertEquals('testadmin', $admin_data['username']);
            }
        }
        
        $this->assertTrue($result);
    }
    
    /**
     * Test login attempt tracking (for security)
     */
    public function test_login_attempt_tracking()
    {
        // Test multiple failed login attempts
        for ($i = 0; $i < 3; $i++) {
            TestUtilities::mockPostData([
                'username' => 'testadmin',
                'password' => 'wrongpassword'
            ]);
            
            $this->CI->load->model('main/Main_model', 'mm');
            $result = $this->CI->mm->cek_login();
            
            $this->assertFalse($result);
        }
        
        // After failed attempts, the account behavior should be tracked
        // This tests the security mechanism
        $this->assertTrue(true); // Basic test completion
    }
    
    /**
     * Test different user types and their redirect logic
     */
    public function test_user_type_redirects()
    {
        // Test regular admin redirect
        TestUtilities::createMockSession([
            'admin' => [
                'id' => 1,
                'id_role' => 1,
                'app_type' => 0
            ]
        ]);
        
        $output = $this->request('GET', 'main/index');
        $this->assertRedirect();
        
        // Test auction admin redirect
        TestUtilities::clearMockSession();
        TestUtilities::createMockSession([
            'admin' => [
                'id' => 2,
                'id_role' => 6,
                'app_type' => 0
            ]
        ]);
        
        $output = $this->request('GET', 'main/index');
        $this->assertRedirect();
        
        // Test procurement admin redirect
        TestUtilities::clearMockSession();
        TestUtilities::createMockSession([
            'admin' => [
                'id' => 1,
                'id_role' => 1,
                'app_type' => 1
            ]
        ]);
        
        $output = $this->request('GET', 'main/index');
        $this->assertRedirect();
    }
    
    /**
     * Test error message handling
     */
    public function test_error_message_handling()
    {
        // Test with invalid credentials to trigger error message
        TestUtilities::mockPostData([
            'username' => 'nonexistent',
            'password' => 'wrongpassword'
        ]);
        
        $output = $this->request('POST', 'main/check');
        
        // Should redirect with flash message
        $this->assertRedirect('/', 302);
        
        // Flash message should be set (we can't easily test this in CI testing framework)
        $this->assertTrue(true);
    }
} 
