<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Login extends CI_Controller
{
    public $eproc_db;
    public function __construct()
    {
        parent::__construct();
        $this->load->library('pdf');
        include_once APPPATH . 'third_party/dompdf2/dompdf_config.inc.php';

        $this->load->model('Login_model', 'lm');
        $this->eproc_db = $this->load->database('eproc', true);
    }

    public function index()
    {
        if ($this->session->userdata('user')) {
        } elseif ($this->session->userdata('admin')) {
            if ($this->session->userdata('admin')['app_type'] == 1) {
            } else {
                redirect('dashboard');
            }
        } else {
            header("Location: ".URL_TO_LOGIN);
        }
    }

    public function check()
    {
        $data = $this->lm->cek_login();
        if ($data) {
            if ($data['type'] == 'user') {
                header("Location: " . URL_TO_VENDOR . "auth/from_external/" . $data['id_user'] . "/user");
            } else {
                if ($data['app'] == 'eproc') {
                    header("Location: " . URL_TO_EPROC . "auth/from_external/" . $data['id_user']);
                } else {
                    header("Location: " . URL_TO_VMS . "auth/from_external/" . $data['id_user'] . "/admin");
                }
            }
        } else {
            $message = "Username atau Password salah";
            echo "<script type='text/javascript'>alert('$message');</script>";
            $this->index();
        }
    }
}
