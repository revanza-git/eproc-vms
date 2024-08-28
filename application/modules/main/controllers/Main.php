<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

    public function index() {	
        $userSession = $this->session->userdata('user');
        $adminSession = $this->session->userdata('admin');

        if ($userSession) {
            redirect($this->config->item('redirect_dashboard'));
        } elseif (isset($adminSession['id_role']) && $adminSession['id_role'] == 6) {
            redirect($this->config->item('redirect_auction'));
        } else {
            $this->_redirectToExternal();
        }
    }
    
    public function login_user() {		
        $key = $this->input->get('key', TRUE);

        if (!$key || !$this->_isValidKey($key)) {
            $this->_redirectToExternal();
            return;
        }

        $data = $this->_getKeyData($key);
        if (!$data) {
            $this->_redirectToExternal();
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
            $this->_redirectToExternal();
            return;
        }

        $data = $this->_getKeyData($key);
        if (!$data || $this->_isNotAdmin($data)) {
            $this->_redirectToExternal();
            return;
        }

        $value = json_decode($data['value']);
        $this->_setAdminSession($value);
        redirect(site_url($this->config->item('redirect_auction')));
    }
    
    public function logout() {
        $this->session->sess_destroy();
        $this->_redirectToExternal('logout');
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

    private function _redirectToExternal($path = '') {
        $externalUrl = $this->config->item('external_url');
        if ($path) {
            $externalUrl .= "/main/{$path}";
        }
        header("Location: {$externalUrl}");
        exit();
    }

    private function _getVendorUsers($status) {
        $query = "SELECT a.name, b.username, b.password, a.vendor_status
                  FROM ms_vendor a
                  JOIN ms_login b ON a.id = b.id_user AND type = 'user'
                  WHERE a.del = 0 AND a.vendor_status = {$status}";
        return $this->db->query($query)->result_array();
    }

    private function _generateExcel($data, $title) {
        $output = '<table border="1">
                    <thead>
                        <tr><th colspan="4">'.$title.'</th></tr>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Password</th>
                        </tr>
                    </thead>
                    <tbody>';

        $no = 1;
        foreach ($data as $value) {
            $output .= '<tr>
                        <td>'.$no.'</td>
                        <td>'.$value['name'].'</td>
                        <td>'.$value['username'].'</td>
                        <td>'.$value['password'].'</td>
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
}