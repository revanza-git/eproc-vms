<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

    public function __construct(){
        parent::__construct();
        
        // Load vendor model manually after basic CI is ready
        try {
            $this->load->model('vendor/vendor_model', 'vm');
            echo "<!-- Vendor model loaded successfully -->";
        } catch (Exception $e) {
            echo "<!-- Vendor model load failed: " . $e->getMessage() . " -->";
        }
    }

    public function index() {
        // Check if HMVC is available (Modules class exists)
        if (class_exists('Modules')) {
            try {
                // Use HMVC to run the main module
                echo Modules::run('main');
                return;
            } catch (Exception $e) {
                echo "<!-- HMVC Modules::run failed: " . $e->getMessage() . " -->";
            }
        }
        
        // If HMVC is not available or failed, use fallback
        echo "<h1>VMS E-Procurement System</h1>";
        echo "<p>Loading system...</p>";
        
        // Try to load the login view directly
        try {
            $data['message'] = '<div class="alert alert-info">Selamat datang di sistem VMS E-Procurement. Silakan login untuk melanjutkan.</div>';
            
            // Try to find the login view
            if (file_exists(APPPATH.'modules/main/views/login.php')) {
                $this->load->view('main/login', $data);
            } elseif (file_exists(APPPATH.'views/login.php')) {
                $this->load->view('login', $data);
            } else {
                // Simple login form if view file doesn't exist
                echo $this->create_simple_login_form();
            }
            
        } catch (Exception $e) {
            echo "<p>View load failed: " . $e->getMessage() . "</p>";
            echo $this->create_simple_login_form();
        }
    }
    
    private function create_simple_login_form() {
        return '
        <div style="max-width: 400px; margin: 50px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
            <h2>VMS E-Procurement Login</h2>
            <form method="post" action="index.php/main/login">
                <div style="margin-bottom: 15px;">
                    <label>Username:</label><br>
                    <input type="text" name="username" style="width: 100%; padding: 8px;" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label>Password:</label><br>
                    <input type="password" name="password" style="width: 100%; padding: 8px;" required>
                </div>
                <button type="submit" style="width: 100%; padding: 10px; background: #007bff; color: white; border: none; border-radius: 3px;">
                    Login
                </button>
            </form>
            <p style="margin-top: 20px; font-size: 12px; color: #666;">
                HMVC Status: Not fully initialized<br>
                Fallback mode active
            </p>
        </div>';
    }
}
?> 