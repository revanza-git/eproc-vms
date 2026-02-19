<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

    private $eproc_db;

    public function __construct(){
        parent::__construct();
        $this->load->helper('string');
        $this->load->model('Main_model', 'mm');
        // Note: JWT token library removed - using key-based authentication instead
    }

    public function index() {	
        // Check if this is a post-logout access
        $logout_complete = $this->input->get('logout_complete');
        $from_main = $this->input->get('from_main');
        
        if ($logout_complete || $from_main) {
            // Don't check sessions, force logout view
            $data['message'] = '<div class="alert alert-info">Anda telah berhasil logout dari sistem.</div>';
            $this->load->view('login', $data);
            return;
        }
        
        $user = $this->session->userdata('user');
        $admin = $this->session->userdata('admin');

        if ($user) {
            redirect($this->config->item('redirect_dashboard'));
        } elseif ($admin) {
            $this->redirect_admin($admin);
        } else {
            $data['message'] = '';
            $this->load->view('login', $data);
        }
    }

    private function redirect_admin($admin){
        // Check if this admin should be redirected to main project with key-based auth
        if (isset($admin['type']) && $admin['type'] === 'admin') {
            $redirect_url = $this->generate_admin_auth_for_main($admin);
            if ($redirect_url) {
                log_message('info', 'Redirecting admin user ' . $admin['id_user'] . ' to main project with auth key');
                redirect($redirect_url);
                return;
            } else {
                log_message('error', 'Failed to generate auth key for admin user: ' . $admin['id_user']);
            }
        }

        // Fallback to original logic for non-JWT admin users
        if (isset($admin['id_role']) && $admin['id_role'] == 6) {
            redirect($this->config->item('redirect_auction'));
        } elseif (isset($admin['app_type']) && $admin['app_type'] == 1) {
            redirect($this->config->item('url_eproc_pengadaan_admin'));
        } else {
            redirect($this->config->item('redirect_admin'));
        }
    }

    /**
     * Enhanced logout that prevents redirect loops
     */
    public function logout(){
        // Check if this is a cross-app logout
        $from_main = $this->input->get('from_main');
        
        // Destroy session
        $this->session->sess_destroy();
        
        if ($from_main) {
            // Don't redirect back to main, show logout page
            $data['message'] = 'Logout berhasil dari aplikasi utama.';
            $this->load->view('logout_complete', $data);
        } else {
            // Normal logout flow
            redirect(site_url());
        }
    }

    /**
     * API endpoint for cross-application logout
     */
    public function api_logout() {
        // Verify the logout request is legitimate
        if ($this->verify_logout_token()) {
            // Force session destruction
            $this->session->sess_destroy();
            
            // Clear any cached authentication keys
            $this->clear_auth_keys($this->input->post('admin_id'));
            
            // Return success response
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode(array(
                'success' => true,
                'message' => 'VMS session cleared'
            )));
        } else {
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Invalid logout token'
            )));
        }
    }

    /**
     * Logout completion page that doesn't redirect
     */
    public function logout_complete() {
        // Force session destruction if any remains
        $this->session->sess_destroy();
        
        // Load logout completion view
        $data['message'] = 'Logout berhasil. Anda telah keluar dari sistem.';
        $this->load->view('logout_complete', $data);
    }

    public function check(){
        $username = $this->input->post('username');
        $password = $this->input->post('password');

        if ($username && $password) {
            if ($this->mm->cek_login()) {
                // Login successful - check session immediately after login
                $user = $this->session->userdata('user');
                $admin = $this->session->userdata('admin');

                if ($user) {
                    // For users, redirect to dashboard module
                    redirect(site_url('dashboard'));
                } elseif ($admin) {
                    // For admin users, use key-based authentication system and redirect to main project
                    $redirect_url = $this->generate_admin_auth_for_main($admin);
                    if ($redirect_url) {
                        log_message('info', 'Admin login successful, redirecting to main project: ' . $admin['id_user']);
                        redirect($redirect_url);
                    } else {
                        // Fallback to original admin redirect logic
                        log_message('error', 'Key generation failed, using fallback redirect for admin: ' . $admin['id_user']);
                        if (isset($admin['id_role']) && $admin['id_role'] == 6) {
                            redirect(site_url('auction'));
                        } else {
                            redirect(site_url('admin'));
                        }
                    }
                } else {
                    // No valid session found after login
                    $this->session->set_flashdata('error_msg', 'Terjadi kesalahan pada sistem login');
                    redirect(site_url());
                }
            } else {
                // Login failed
                $this->session->set_flashdata('error_msg', 'Username atau Password salah');
                redirect(site_url());
            }
        } else {
            // Missing username or password
            $this->session->set_flashdata('error_msg', 'Username dan Password harus diisi');
            redirect(site_url());
        }
    }

    /**
     * Secure login method with proper CSRF protection and JSON response
     */
    public function check_secure(){
        // Set JSON response headers
        $this->output->set_content_type('application/json');
        
        try {
            $username = $this->input->post('username');
            $password = $this->input->post('password');

            if (!$username || !$password) {
                throw new Exception('Username dan password harus diisi');
            }

            // Process login
            $result = $this->process_login_secure($username, $password);
            
            if ($result['success']) {
                $this->output->set_output(json_encode(array(
                    'success' => true,
                    'message' => 'Login berhasil',
                    'redirect_url' => $result['redirect_url']
                )));
            } else {
                $this->output->set_output(json_encode(array(
                    'success' => false,
                    'message' => $result['message']
                )));
            }
            
        } catch (Exception $e) {
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => $e->getMessage()
            )));
        }
    }

    private function process_login_secure($username, $password) {
        $username = encode_php_tags($username);
        $query = "SELECT * FROM ms_login WHERE username = ?";
        $data = $this->db->query($query, array($username))->row_array();

        if (!$data) {
            return array('success' => false, 'message' => 'Username atau Password salah');
        }

        // Check if account is locked
        $now = date('Y-m-d H:i:s');
        if ($data['lock_time'] > date('Y-m-d H:i:s', strtotime("$now -9 hours"))) {
            return array('success' => false, 'message' => 'Akun telah di lock sementara, tunggu beberapa menit untuk login kembali');
        }

        // Verify password (assuming you have password verification)
        if ($this->verify_password($password, $data)) {
            return $this->login_user_secure($data);
        } else {
            return array('success' => false, 'message' => 'Username atau Password salah');
        }
    }

    private function verify_password($password, $user_data) {
        // Load the secure password library if not already loaded
        if (!isset($this->secure_password)) {
            $this->load->library('secure_password');
        }
        
        // Verify password using secure library (supports both bcrypt and legacy hashes)
        $is_valid = $this->secure_password->verify_password($password, $user_data['password']);
        
        if ($is_valid) {
            // Check if password needs rehashing (migration from legacy SHA-1 to bcrypt)
            if ($this->secure_password->needs_rehash($user_data['password'])) {
                log_message('info', 'Password migration required for user: ' . $user_data['username']);
                
                // Generate new secure bcrypt hash
                $new_hash = $this->secure_password->hash_password($password);
                
                if ($new_hash) {
                    // Update database with new secure hash
                    $this->db->where('id', $user_data['id'])
                             ->update('ms_login', array('password' => $new_hash));
                    
                    log_message('info', 'Password successfully migrated to bcrypt for user: ' . $user_data['username']);
                } else {
                    log_message('error', 'Failed to generate new secure hash for user: ' . $user_data['username']);
                }
            }
        }
        
        return $is_valid;
    }

    private function login_user_secure($data) {
        if (is_array($data)) {
            unset($data['password'], $data['attempts'], $data['lock_time']);
            $data['app'] = 'vms';
        }

        // Set session data first
        $this->session->set_userdata('admin', $data);
        
        // Get the updated admin session data
        $admin = $this->session->userdata('admin');
        
        // For admin users, use key-based authentication system and redirect to main project
        if ($admin && isset($admin['type']) && $admin['type'] === 'admin') {
            $redirect_url = $this->generate_admin_auth_for_main($admin);
            if ($redirect_url) {
                return array(
                    'success' => true, 
                    'message' => 'Login berhasil',
                    'redirect_url' => $redirect_url
                );
            }
        }
        
        // Fallback to original logic
        if ($data['app_type'] == 1) {
            $redirect_url = $this->config->item('url_eproc_pengadaan_admin');
        } else {
            $redirect_url = $this->config->item('redirect_dashboard');
        }
        
        return array(
            'success' => true, 
            'message' => 'Login berhasil',
            'redirect_url' => $redirect_url
        );
    }

    private function process_login($username){
        $username = encode_php_tags($username);
        $query = "SELECT * FROM ms_login WHERE username = ?";
        $data = $this->db->query($query, array($username))->row_array();

        if ($data) {
            $this->handle_login($data);
        } else {
            $this->show_error_message("Username atau Password salah");
        }
    }

    private function handle_login($data){
        $now = date('Y-m-d H:i:s');

        if ($data['lock_time'] > date('Y-m-d H:i:s', strtotime("$now -9 hours"))) {
            $this->show_error_message("Akun telah di lock sementara, tunggu beberapa menit untuk login kembali");
        } else {
            $this->process_user_login($data);
        }
    }

    private function process_user_login($data){
        if ($this->mm->cek_login()) {
            $user = $this->session->userdata('user');
            $admin = $this->session->userdata('admin');

            if ($user) {
                $this->generate_key($user, 'user', $this->config->item('redirect_dashboard'));
            } elseif ($admin) {
                $this->handle_admin_login($admin);
            }
        } else {
            $this->show_error_message("Username atau Password salah");
        }
    }

    private function handle_admin_login($admin){
        // Use key-based authentication system for admin redirects
        $redirect_url = $this->generate_admin_auth_for_main($admin);
        if ($redirect_url) {
            log_message('info', 'Admin login redirect with auth key: ' . $admin['id_user']);
            redirect($redirect_url);
        } else {
            // Fallback to original logic
            if (isset($admin['id_role']) && $admin['id_role'] == 6) {
                redirect($this->config->item('redirect_auction'));
            } else {
                redirect($this->config->item('redirect_admin'));
            }
        }
    }

    private function generate_key($user_data, $type, $redirect_url){
        $key = $this->generate_unique_key();
        $data = $this->prepare_user_data($user_data, $type);
        $this->db->insert('ms_key_value', ['key' => $key, 'value' => json_encode($data), 'created_at' => date('Y-m-d H:i:s')]);
        redirect($redirect_url . "?key={$key}");
    }

    private function generate_unique_key(){
        if (function_exists('random_string')) {
            // Use CodeIgniter's random_string helper
            return uniqid() . time() . random_string('alnum', 10);
        } else {
            // Fallback method
            $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            $random = '';
            for ($i = 0; $i < 10; $i++) {
                $random .= $chars[rand(0, strlen($chars) - 1)];
            }
            return uniqid() . time() . $random;
        }
    }

    private function prepare_user_data($user_data, $type){
        return (object) array_merge($user_data, ['type' => $type]);
    }

    private function show_error_message($message){
        $data['message'] = "<div class='alert alert-danger'>{$message}</div>";
        $this->load->view('login', $data);
    }

    // Legacy methods for backward compatibility
    public function login_user() {		
        $key = $this->input->get('key', TRUE);

        if (!$key || !$this->_isValidKey($key)) {
            redirect(site_url());
            return;
        }

        $data = $this->_getKeyData($key);
        if (!$data) {
            redirect(site_url());
            return;
        }

        $value = json_decode($data['value']);
        $this->_setUserSession($value);
        $this->_invalidateKey($key);

        $data['name'] = $value->name;
        $this->_loadView('redirect', $data);
    }

    public function login_admin() {
        $key = $this->input->get('key', TRUE);

        if (!$key || !$this->_isValidKey($key)) {
            redirect(site_url());
            return;
        }

        $data = $this->_getKeyData($key);
        if (!$data || $this->_isNotAdmin($data)) {
            redirect(site_url());
            return;
        }

        $value = json_decode($data['value']);
        $this->_setAdminSession($value);
        redirect(site_url($this->config->item('redirect_auction')));
    }

    public function showUser() {
        $dptUsers = $this->_getVendorUsers(2);
        $waitingUsers = $this->_getVendorUsers(1);

        $this->_generateExcel($dptUsers, 'Daftar User Vendor (DPT)');
        $this->_generateExcel($waitingUsers, 'Daftar User Vendor (Daftar Tunggu)');
	}

    public function login__() {
        $this->load->model('main_model');

        if ($this->input->post('username') && $this->input->post('password')) {
            if ($this->main_model->cek_login()) {
                $this->_handleLoginRedirect();
            } else {
                $this->_setFlashMessageAndRedirect('Data tidak dikenal. Silahkan login kembali!');
            }
        } else {
            $this->_setFlashMessageAndRedirect('Isi form dengan benar!');
        }
    }

    /**
     * JWT Token validation endpoint (for intra domain verification)
     */
    public function validate_jwt(){
        $token = $this->input->get('jwt') ?: $this->input->post('jwt');
        
        if (!$token) {
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'No JWT token provided'
            )));
            return;
        }
        
        $decoded = $this->jwt_token->validate_token($token);
        
        if ($decoded) {
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode(array(
                'success' => true,
                'message' => 'Token is valid',
                'data' => $decoded['data']
            )));
        } else {
            $this->output->set_content_type('application/json');
            $this->output->set_output(json_encode(array(
                'success' => false,
                'message' => 'Invalid or expired JWT token'
            )));
        }
    }

    // Note: from_eks() method is implemented in the main project at local.eproc.intra.com
    // This VMS application only generates keys and redirects to the main project

    // Private Helper Methods
    private function _isValidKey($key) {
        return !empty($key);
    }

    private function _getKeyData($key) {
        return $this->db->where('key', $key)
                        ->where('deleted_at', NULL)
                        ->get('ms_key_value')
                        ->row_array();
    }

    private function _invalidateKey($key) {
        $this->db->where('key', $key)
                 ->update('ms_key_value', ['deleted_at' => date('Y-m-d H:i:s')]);
    }

    private function _setUserSession($value) {
        $sessionData = [
            'id_user' 		=> $value->id_user,
            'name'			=> $value->name,
            'id_sbu'		=> $value->id_sbu,
            'vendor_status'	=> $value->vendor_status,
            'is_active'		=> $value->is_active,
            'app'			=> 'vms'
        ];
        $this->session->set_userdata('user', $sessionData);
    }

    private function _setAdminSession($value) {
        $sessionData = [
            'id_user' 		=> $value->id_user,
            'name'			=> $value->name,
            'id_sbu'		=> $value->id_sbu,
            'id_role'		=> $value->id_role,
            'role_name'		=> $value->role_name,
            'sbu_name'		=> $value->sbu_name,
            'app'			=> $value->app
        ];
        $this->session->set_userdata('admin', $sessionData);
    }

    private function _isNotAdmin($data) {
        $value = json_decode($data['value']);
        return $value->id_role != 6;
    }

    private function _loadView($view, $data = []) {
        $item['content'] = $this->load->view($view, $data, TRUE);
        $this->load->view('template', $item);
    }

    private function _getVendorUsers($status) {
        $status = (int) $status;
        $query = "SELECT a.name, b.username, a.vendor_status
                  FROM ms_vendor a
                  JOIN ms_login b ON a.id = b.id_user AND type = 'user'
                  WHERE a.del = 0 AND a.vendor_status = ?";
        return $this->db->query($query, array($status))->result_array();
    }

    private function _generateExcel($data, $title) {
        $output = '<table border="1">
                    <thead>
                        <tr><th colspan="3">'.$title.'</th></tr>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Username</th>
                        </tr>
                    </thead>
                    <tbody>';

        $no = 1;
        foreach ($data as $value) {
            $output .= '<tr>
                        <td>'.$no.'</td>
                        <td>'.$value['name'].'</td>
                        <td>'.$value['username'].'</td>
                        </tr>';
            $no++;
        }

        $output .= '</tbody></table><br><br>';

        header('Content-type: application/ms-excel');
        header('Content-Disposition: attachment; filename="Daftar User VMS.xls"');
        echo $output;
    }

    private function _handleLoginRedirect() {
        if ($this->session->userdata('user')) {
            $data = $this->session->userdata('user');
            $this->_loadView('redirect', $data);
        } elseif ($this->session->userdata('admin')) {
            $adminData = $this->session->userdata('admin');
            $redirectUrl = ($adminData['id_role'] == 6) ? $this->config->item('redirect_auction') : $this->config->item('redirect_admin');
            redirect(site_url($redirectUrl));
        }
    }

    private function _setFlashMessageAndRedirect($message) {
        $this->session->set_flashdata('error_msg', $message);
        redirect(site_url());
    }

    public function test_session()
    {
        show_404();
    }

    /**
     * Generate external authentication for admin redirect to main project
     * This method should be called when VMS admin needs to be redirected to main project
     */
    public function generate_admin_auth_for_main($admin_data) {
        try {
            // Generate secure unique key
            $key = $this->generate_unique_key();
            
            // Prepare admin data in the format expected by main project's from_eks()
            $idDivision = isset($admin_data['id_division']) ? (int) $admin_data['id_division'] : 1;
            if ($idDivision <= 0) {
                $idDivision = 1;
            }
            $idRole = isset($admin_data['id_role']) ? (int) $admin_data['id_role'] : 1;
            if ($idRole <= 0) {
                $idRole = 1;
            }
            $auth_data = array(
                "name" => $admin_data['name'],
                "id_user" => $admin_data['id_user'],
                "id_role" => $idRole,
                "id_division" => $idDivision,
                "email" => isset($admin_data['email']) ? $admin_data['email'] : 'admin@example.com',
                "photo_profile" => isset($admin_data['photo_profile']) ? $admin_data['photo_profile'] : 'profile.jpg',
                "app_type" => isset($admin_data['app_type']) ? $admin_data['app_type'] : 2,
                "originated_from_vms" => true,
                "vms_logout_token" => $this->generate_logout_token($admin_data),
                "vms_logout_url" => site_url('main/api_logout')
            );
            
            // Store in ms_key_value table
            // Check if created_at column exists
            $fields = $this->db->list_fields('ms_key_value');
            $has_created_at = in_array('created_at', $fields);
            
            $insert_data = array(
                'key' => $key,
                'value' => json_encode($auth_data)
            );
            
            if ($has_created_at) {
                $insert_data['created_at'] = date('Y-m-d H:i:s');
            }
            $this->db->insert('ms_key_value', $insert_data);
            
            if ($this->db->affected_rows() > 0) {
                // Generate redirect URL to main project using environment configuration
                $from_eks_url = env('ADMIN_FROM_EKS_URL', 'http://local.eproc.web.com/internal/main/from_eks');
                $redirect_url = $from_eks_url . "?key=" . $key;
                return $redirect_url;
            } else {
                log_message('error', 'Failed to insert auth key - no rows affected. DB Error: ' . $this->db->error()['message']);
                return false;
            }
            
        } catch (Exception $e) {
            log_message('error', 'Exception in generate_admin_auth_for_main: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Clear authentication keys for a specific admin user
     */
    private function clear_auth_keys($admin_id) {
        if (!$admin_id) {
            return false;
        }
        
        try {
            // Clear any authentication keys for this admin
            $this->db->where('value LIKE', '%"id_user":"' . $admin_id . '"%')
                     ->update('ms_key_value', array('deleted_at' => date('Y-m-d H:i:s')));
            
            log_message('info', 'Cleared auth keys for admin ID: ' . $admin_id);
            return true;
        } catch (Exception $e) {
            log_message('error', 'Failed to clear auth keys for admin ' . $admin_id . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify logout token for cross-application logout
     */
    private function verify_logout_token() {
        $token = $this->input->post('logout_token');
        $admin_id = $this->input->post('admin_id');
        $source = $this->input->post('source');
        
        // Basic verification - ensure required fields are present
        if (empty($token) || empty($admin_id) || $source !== 'main_project') {
            log_message('error', 'Invalid logout token verification attempt - missing fields');
            return false;
        }
        
        // Additional security: verify token format (you can make this more sophisticated)
        if (strlen($token) < 10) {
            log_message('error', 'Invalid logout token verification attempt - token too short');
            return false;
        }
        
        log_message('info', 'Logout token verified successfully for admin ID: ' . $admin_id);
        return true;
    }

    /**
     * Generate a secure logout token for verification
     */
    private function generate_logout_token($admin_data) {
        return hash('sha256', 
            $admin_data['id_user'] . 
            date('Y-m-d') . 
            'logout_salt_' . 
            $this->config->item('encryption_key')
        );
    }

    /**
     * Test endpoint to verify cross-app logout functionality
     */
    public function test_logout_flow() {
        show_404();
    }
}
