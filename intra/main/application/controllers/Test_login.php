<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Test Login Controller
 * 
 * This controller provides multiple methods to test the login flow
 * without requiring the external VMS system at http://local.eproc.vms.com
 * 
 * IMPORTANT: This is for development/testing only!
 */
class Test_login extends CI_Controller
{
    public $eproc_db;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Auth_model', 'am');
        $this->load->model('Main_model', 'mm');
        $this->eproc_db = $this->load->database('eproc', true);
        $this->load->helper('string');
    }

    /**
     * Show available test login methods
     */
    public function index()
    {
        echo "<h1>🧪 E-Procurement Admin Test Login</h1>";
        echo "<p><strong>Note:</strong> This system only accepts <strong>ADMIN users</strong>. Other roles cannot access this application.</p>";
        
        echo "<h2>📋 Available Test Admin Accounts:</h2>";
        echo "<ul>";
        echo "<li><strong>Super Admin:</strong> username=admin, password=admin123</li>";
        echo "<li><strong>Role:</strong> Super Administrator (ID: 1)</li>";
        echo "</ul>";
        
        echo "<h2>🔧 Test Methods:</h2>";
        echo "<ol>";
        echo "<li><a href='" . site_url('test_login/direct_admin_login') . "'>Direct Admin Login (Auto)</a> - Automatically login as admin</li>";
        echo "<li><a href='" . site_url('test_login/form') . "'>Manual Admin Login Form</a> - Test with admin username/password form</li>";
        echo "<li><a href='" . site_url('test_login/key_auth_demo') . "'>Key-based Auth Demo</a> - Test admin key-based authentication</li>";
        echo "<li><a href='" . site_url('test_login/session_info') . "'>Session Info</a> - Check current admin session status</li>";
        echo "</ol>";
        
        echo "<h2>🔄 Admin Application Flow:</h2>";
        echo "<p><strong>Admin Only Flow:</strong> Admin Login → Main Dashboard → Access Pengadaan Menu → Pengadaan Module Dashboard</p>";
        echo "<div style='background: #fff3cd; padding: 10px; border-left: 4px solid #ffc107; margin: 10px 0;'>";
        echo "<strong>⚠️ Access Restriction:</strong> Only users with admin roles can access this e-procurement system.";
        echo "</div>";
    }

    /**
     * Direct admin login without form
     */
    public function direct_admin_login()
    {
        echo "<h2>🔐 Direct Admin Login Test</h2>";
        
        // Get admin user from database (using eproc database)
        $admin_user = $this->eproc_db->where('email', 'admin@test.com')->get('ms_admin')->row_array();
        
        if (!$admin_user) {
            echo "<p>❌ Admin test user not found. Please run create_test_users.php first.</p>";
            return;
        }
        
        // Get division and role info (using eproc database)
        $division = $this->eproc_db->where('id', $admin_user['id_division'])->get('tb_division')->row_array();
        $role = $this->eproc_db->where('id', $admin_user['id_role'])->get('tb_role')->row_array();
        
        // Create admin session
        $admin_session = array(
            'name' => $admin_user['name'],
            'division' => isset($division['name']) ? $division['name'] : 'Default Division',
            'id_user' => $admin_user['id'],
            'id_role' => $admin_user['id_role'],
            'id_division' => $admin_user['id_division'],
            'email' => $admin_user['email'],
            'photo_profile' => 'default.png',
            'app_type' => 0, // Main application
            'role_name' => isset($role['name']) ? $role['name'] : 'Administrator'
        );
        
        $this->session->set_userdata('admin', $admin_session);
        
        echo "<p>✅ Admin session created successfully!</p>";
        echo "<p><strong>Session Data:</strong></p>";
        echo "<pre>" . print_r($admin_session, true) . "</pre>";
        echo "<p><a href='" . site_url('dashboard') . "'>🏠 Go to Main Dashboard</a></p>";
        echo "<p><a href='" . site_url('test_login') . "'>🔙 Back to Test Menu</a></p>";
    }

    /**
     * Create additional admin test accounts with different roles
     */
    public function create_admin_roles()
    {
        echo "<h2>👥 Create Additional Admin Test Accounts</h2>";
        
        // Available admin roles
        $roles = $this->db->get('tb_role')->result_array();
        $divisions = $this->db->where('del', 0)->get('tb_division')->result_array();
        
        echo "<p>Create test accounts for different admin roles:</p>";
        echo "<table border='1' style='border-collapse: collapse; margin: 20px 0;'>";
        echo "<tr><th>Role ID</th><th>Role Name</th><th>Test Account</th></tr>";
        
        foreach ($roles as $role) {
            $username = 'admin_' . strtolower(str_replace(' ', '_', $role['name']));
            echo "<tr>";
            echo "<td>{$role['id']}</td>";
            echo "<td>{$role['name']}</td>";
            echo "<td><code>$username</code> / <code>admin123</code></td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<p><strong>Available Divisions:</strong></p>";
        echo "<ul>";
        foreach ($divisions as $div) {
            echo "<li>{$div['name']} (ID: {$div['id']})</li>";
        }
        echo "</ul>";
        
        echo "<p><a href='" . site_url('test_login') . "'>🔙 Back to Test Menu</a></p>";
    }

    /**
     * Manual admin login form for testing
     */
    public function form()
    {
        echo "<h2>📝 Manual Admin Login Form</h2>";
        echo "<p><strong>Note:</strong> Only admin users can access this system.</p>";
        
        if ($this->input->post('username')) {
            $this->process_form_login();
            return;
        }
        
        echo '<form method="post" style="max-width: 400px; margin: 20px 0;">';
        echo '<div style="margin-bottom: 15px;">';
        echo '<label>Admin Username:</label><br>';
        echo '<input type="text" name="username" placeholder="admin" style="width: 100%; padding: 8px;" required>';
        echo '</div>';
        echo '<div style="margin-bottom: 15px;">';
        echo '<label>Admin Password:</label><br>';
        echo '<input type="password" name="password" placeholder="admin123" style="width: 100%; padding: 8px;" required>';
        echo '</div>';
        echo '<div style="margin-bottom: 15px; background: #e8f4fd; padding: 10px; border-left: 4px solid #007cba;">';
        echo '<strong>ℹ️ Test Credentials:</strong><br>';
        echo 'Username: <code>admin</code><br>';
        echo 'Password: <code>admin123</code>';
        echo '</div>';
        echo '<button type="submit" style="padding: 10px 20px; background: #007cba; color: white; border: none; cursor: pointer;">🔐 Admin Login</button>';
        echo '</form>';
        echo "<p><a href='" . site_url('test_login') . "'>🔙 Back to Test Menu</a></p>";
    }

    /**
     * Process manual admin form login
     */
    private function process_form_login()
    {
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        
        echo "<h3>🔍 Processing Admin Login...</h3>";
        echo "<p>Username: $username</p>";
        echo "<p>Type: Admin Only</p>";
        
        // Check admin credentials only (using eproc database)
        $admin = $this->eproc_db->join('ms_login', 'ms_login.id_user = ms_admin.id')
                          ->where('ms_login.username', $username)
                          ->where('ms_login.type', 'admin')
                          ->get('ms_admin')->row_array();
        
        if ($admin && password_verify($password, $admin['password'])) {
            // Get additional admin details (using eproc database)
            $admin_user = $this->eproc_db->where('id', $admin['id_user'])->get('ms_admin')->row_array();
            $division = $this->eproc_db->where('id', $admin_user['id_division'])->get('tb_division')->row_array();
            $role = $this->eproc_db->where('id', $admin_user['id_role'])->get('tb_role')->row_array();
            
            // Create admin session
            $admin_session = array(
                'name' => $admin_user['name'],
                'division' => isset($division['name']) ? $division['name'] : 'Default Division',
                'id_user' => $admin_user['id'],
                'id_role' => $admin_user['id_role'],
                'id_division' => $admin_user['id_division'],
                'email' => $admin_user['email'],
                'photo_profile' => 'default.png',
                'app_type' => 0, // Main application
                'role_name' => isset($role['name']) ? $role['name'] : 'Administrator'
            );
            
            $this->session->set_userdata('admin', $admin_session);
            
            echo "<p>✅ Admin login successful!</p>";
            echo "<p><strong>Admin Details:</strong></p>";
            echo "<ul>";
            echo "<li>Name: {$admin_session['name']}</li>";
            echo "<li>Role: {$admin_session['role_name']}</li>";
            echo "<li>Division: {$admin_session['division']}</li>";
            echo "</ul>";
            echo "<p><a href='" . site_url('dashboard') . "'>🏠 Go to Admin Dashboard</a></p>";
            echo "<p><a href='" . site_url('test_login') . "'>🔙 Back to Test Menu</a></p>";
            return;
        }
        
        echo "<p>❌ Admin login failed. Invalid credentials or insufficient privileges.</p>";
        echo "<div style='background: #f8d7da; padding: 10px; border-left: 4px solid #dc3545; margin: 10px 0;'>";
        echo "<strong>Access Denied:</strong> Only admin users can access this system.";
        echo "</div>";
        echo "<p><a href='" . site_url('test_login/form') . "'>🔙 Try Again</a></p>";
    }

    /**
     * Test key-based authentication
     */
    public function key_auth_demo()
    {
        echo "<h2>🔑 Key-based Authentication Demo</h2>";
        
        // Create a test key for admin user (using eproc database)
        $admin_user = $this->eproc_db->where('email', 'admin@test.com')->get('ms_admin')->row_array();
        
        if (!$admin_user) {
            echo "<p>❌ Admin test user not found.</p>";
            return;
        }
        
        $division = $this->eproc_db->where('id', $admin_user['id_division'])->get('tb_division')->row_array();
        $role = $this->eproc_db->where('id', $admin_user['id_role'])->get('tb_role')->row_array();
        
        $key_data = array(
            'name' => $admin_user['name'],
            'id_user' => $admin_user['id'],
            'id_role' => $admin_user['id_role'],
            'id_division' => $admin_user['id_division'],
            'email' => $admin_user['email'],
            'photo_profile' => 'default.png',
            'app_type' => 0,
            'role_name' => isset($role['name']) ? $role['name'] : 'Administrator'
        );
        
        $key = random_string('unique') . random_string('unique');
        
        $this->eproc_db->insert('ms_key_value', array(
            'key' => $key,
            'value' => json_encode($key_data),
            'created_at' => date('Y-m-d H:i:s')
        ));
        
        echo "<p>✅ Test key created!</p>";
        echo "<p><strong>Key:</strong> $key</p>";
        echo "<p><a href='" . site_url('main/from_eks?key=' . $key) . "'>🔗 Test Key-based Login</a></p>";
        echo "<p><a href='" . site_url('test_login') . "'>🔙 Back to Test Menu</a></p>";
    }

    /**
     * Show current admin session information
     */
    public function session_info()
    {
        echo "<h2>📊 Current Admin Session Information</h2>";
        
        $admin_session = $this->session->userdata('admin');
        
        if ($admin_session) {
            echo "<h3>👨‍💼 Admin Session Active:</h3>";
            echo "<div style='background: #d4edda; padding: 15px; border-left: 4px solid #28a745; margin: 15px 0;'>";
            echo "<strong>✅ Logged in as Admin</strong>";
            echo "</div>";
            echo "<table border='1' style='border-collapse: collapse; margin: 15px 0; width: 100%;'>";
            echo "<tr><th style='padding: 8px; background: #f8f9fa;'>Property</th><th style='padding: 8px; background: #f8f9fa;'>Value</th></tr>";
            foreach ($admin_session as $key => $value) {
                echo "<tr><td style='padding: 8px;'>$key</td><td style='padding: 8px;'>$value</td></tr>";
            }
            echo "</table>";
            
            echo "<div style='margin: 20px 0;'>";
            echo "<a href='" . site_url('dashboard') . "' style='display: inline-block; padding: 10px 15px; background: #007cba; color: white; text-decoration: none; border-radius: 4px; margin-right: 10px;'>🏠 Go to Admin Dashboard</a>";
            echo "<a href='" . site_url('main/logout') . "' style='display: inline-block; padding: 10px 15px; background: #dc3545; color: white; text-decoration: none; border-radius: 4px;'>🚪 Logout</a>";
            echo "</div>";
        } else {
            echo "<div style='background: #f8d7da; padding: 15px; border-left: 4px solid #dc3545; margin: 15px 0;'>";
            echo "<p><strong>ℹ️ No active admin session found.</strong></p>";
            echo "<p>You need to login as an admin to access this system.</p>";
            echo "</div>";
            
            echo "<div style='margin: 20px 0;'>";
            echo "<a href='" . site_url('test_login/direct_admin_login') . "' style='display: inline-block; padding: 10px 15px; background: #28a745; color: white; text-decoration: none; border-radius: 4px; margin-right: 10px;'>🔐 Quick Admin Login</a>";
            echo "<a href='" . site_url('test_login/form') . "' style='display: inline-block; padding: 10px 15px; background: #007cba; color: white; text-decoration: none; border-radius: 4px;'>📝 Manual Login</a>";
            echo "</div>";
        }
        
        echo "<p><a href='" . site_url('test_login') . "'>🔙 Back to Test Menu</a></p>";
    }

    /**
     * Clear all sessions
     */
    public function clear_session()
    {
        $this->session->sess_destroy();
        echo "<h2>🧹 Session Cleared</h2>";
        echo "<p>✅ All sessions have been cleared.</p>";
        echo "<p><a href='" . site_url('test_login') . "'>🔙 Back to Test Menu</a></p>";
    }
}
?> 