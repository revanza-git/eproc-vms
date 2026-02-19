<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test_logout extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Test menu for logout functionality
     */
    public function index() {
        echo "<h2>🔐 Cross-Application Logout Test Menu</h2>";
        echo "<p>Test the cross-application logout functionality between main and VMS applications.</p>";
        
        $admin_session = $this->session->userdata('admin');
        
        if ($admin_session) {
            echo "<div style='background: #d4edda; padding: 15px; border-left: 4px solid #28a745; margin: 15px 0;'>";
            echo "<h3>✅ Current Session Status:</h3>";
            echo "<p><strong>User:</strong> " . (isset($admin_session['name']) ? $admin_session['name'] : 'Unknown') . "</p>";
            echo "<p><strong>Role:</strong> " . (isset($admin_session['role_name']) ? $admin_session['role_name'] : 'Unknown') . "</p>";
            echo "<p><strong>Division:</strong> " . (isset($admin_session['division']) ? $admin_session['division'] : 'Unknown') . "</p>";
            echo "<p><strong>Originated from VMS:</strong> " . (isset($admin_session['originated_from_vms']) ? 'Yes' : 'No') . "</p>";
            echo "</div>";
            
            echo "<h3>🧪 Test Actions:</h3>";
            echo "<div style='margin: 20px 0;'>";
            echo "<a href='" . site_url('test_logout/test_normal_logout') . "' style='display: inline-block; padding: 10px 15px; background: #dc3545; color: white; text-decoration: none; border-radius: 4px; margin-right: 10px;'>🚪 Test Normal Logout</a>";
            echo "<a href='" . site_url('test_logout/test_api_logout') . "' style='display: inline-block; padding: 10px 15px; background: #6c757d; color: white; text-decoration: none; border-radius: 4px; margin-right: 10px;'>🔗 Test API Logout</a>";
            echo "<a href='" . site_url('main/logout_complete?from_vms=1&logout_complete=1') . "' style='display: inline-block; padding: 10px 15px; background: #28a745; color: white; text-decoration: none; border-radius: 4px;'>✅ Test Logout Complete Page</a>";
            echo "</div>";
        } else {
            echo "<div style='background: #f8d7da; padding: 15px; border-left: 4px solid #dc3545; margin: 15px 0;'>";
            echo "<p><strong>❌ No active session found.</strong></p>";
            echo "<p>You need to be logged in to test logout functionality.</p>";
            echo "</div>";
        }
        
        echo "<h3>📊 Session Information:</h3>";
        echo "<div style='margin: 20px 0;'>";
        echo "<a href='" . site_url('test_logout/session_info') . "' style='display: inline-block; padding: 10px 15px; background: #007cba; color: white; text-decoration: none; border-radius: 4px; margin-right: 10px;'>📋 View Session Details</a>";
        echo "<a href='" . site_url('test_logout/simulate_vms_login') . "' style='display: inline-block; padding: 10px 15px; background: #17a2b8; color: white; text-decoration: none; border-radius: 4px;'>🎭 Simulate VMS Login</a>";
        echo "</div>";
    }

    /**
     * Test normal logout flow
     */
    public function test_normal_logout() {
        echo "<h2>🚪 Testing Normal Logout Flow</h2>";
        $admin_session = $this->session->userdata('admin');
        
        if ($admin_session) {
            echo "<p>✅ Current session found. Proceeding with logout...</p>";
            echo "<p>🔄 Redirecting to main logout in 3 seconds...</p>";
            echo "<script>setTimeout(function(){ window.location.href = '" . site_url('main/logout') . "'; }, 3000);</script>";
        } else {
            echo "<p>❌ No session found to logout.</p>";
            echo "<p><a href='" . site_url('test_logout') . "'>🔙 Back to Test Menu</a></p>";
        }
    }

    /**
     * Test API logout endpoint
     */
    public function test_api_logout() {
        echo "<h2>🔗 Testing API Logout Endpoint</h2>";
        
        // Simulate a POST request to the API logout endpoint
        $test_data = array(
            'admin_id' => '123',
            'logout_token' => 'test_token',
            'source' => 'vms_app'
        );
        
        echo "<p>🧪 Simulating API logout request with test data:</p>";
        echo "<pre>" . print_r($test_data, true) . "</pre>";
        
        // Make a CURL request to our own API logout endpoint
        $url = site_url('main/api_logout');
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($test_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        echo "<p><strong>📨 Response from API:</strong></p>";
        echo "<p><strong>HTTP Code:</strong> $http_code</p>";
        echo "<p><strong>Response:</strong> $response</p>";
        
        echo "<p><a href='" . site_url('test_logout') . "'>🔙 Back to Test Menu</a></p>";
    }

    /**
     * Show session information
     */
    public function session_info() {
        echo "<h2>📊 Current Session Information</h2>";
        
        $admin_session = $this->session->userdata('admin');
        
        if ($admin_session) {
            echo "<div style='background: #d4edda; padding: 15px; border-left: 4px solid #28a745; margin: 15px 0;'>";
            echo "<h3>👨‍💼 Admin Session Active:</h3>";
            echo "</div>";
            echo "<table border='1' style='border-collapse: collapse; margin: 15px 0; width: 100%;'>";
            echo "<tr><th style='padding: 8px; background: #f8f9fa;'>Property</th><th style='padding: 8px; background: #f8f9fa;'>Value</th></tr>";
            foreach ($admin_session as $key => $value) {
                $display_value = is_bool($value) ? ($value ? 'true' : 'false') : $value;
                echo "<tr><td style='padding: 8px;'>$key</td><td style='padding: 8px;'>$display_value</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<div style='background: #f8d7da; padding: 15px; border-left: 4px solid #dc3545; margin: 15px 0;'>";
            echo "<p><strong>❌ No active admin session found.</strong></p>";
            echo "</div>";
        }
        
        echo "<p><a href='" . site_url('test_logout') . "'>🔙 Back to Test Menu</a></p>";
    }

    /**
     * Simulate a login from VMS (for testing)
     */
    public function simulate_vms_login() {
        // Create a test session that looks like it came from VMS
        $test_session = array(
            'name' => 'Test User (from VMS)',
            'division' => 'Test Division',
            'id_user' => 999,
            'id_role' => 1,
            'id_division' => 1,
            'email' => 'test@example.com',
            'photo_profile' => '',
            'app_type' => 2,
            'role_name' => 'Test Role',
            'originated_from_vms' => true
        );
        
        $this->session->set_userdata('admin', $test_session);
        
        echo "<h2>🎭 VMS Login Simulation</h2>";
        echo "<p>✅ Test session created with VMS origin flag.</p>";
        echo "<p>🔄 Redirecting to test menu...</p>";
        echo "<script>setTimeout(function(){ window.location.href = '" . site_url('test_logout') . "'; }, 2000);</script>";
    }

    /**
     * Clear test session
     */
    public function clear_session() {
        $this->session->sess_destroy();
        echo "<h2>🧹 Session Cleared</h2>";
        echo "<p>✅ All sessions have been cleared.</p>";
        echo "<p><a href='" . site_url('test_logout') . "'>🔙 Back to Test Menu</a></p>";
    }
}
?> 