<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{
    public $eproc_db;
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Auth_model', 'am');
        $this->eproc_db = $this->load->database('eproc', true);
    }

    public function from_external($id_user)
    {
        // Validate input - ensure id_user is numeric and positive
        if (!is_numeric($id_user) || $id_user <= 0) {
            show_error('Invalid user ID', 403);
            return;
        }
        
        // Get and validate user from database
        $user = $this->am->get_user($id_user);
        
        // Verify user exists and is active
        if (!$user || empty($user)) {
            show_error('User not found or inactive', 403);
            return;
        }
        
        // Set user session
        $this->session->set_userdata('admin', $user);
        
        redirect(site_url('dashboard'));
    }

    public function to_vms()
    {
        $admin = $this->session->userdata('admin');
        
        if($admin && isset($admin['id_user'])){
            $id_user = $admin['id_user'];
            $this->session->sess_destroy();
            header('Location: ' . URL_TO_VMS . 'auth/from_internal/' . $id_user);
        } else {
            $this->session->sess_destroy();
            header('Location: ' . URL_TO_VMS);
        }
    }
}
