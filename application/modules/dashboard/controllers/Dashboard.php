<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Dashboard Controller (cleaned for PHP 7.4+)
 * --------------------------------------------------
 * – No duplicate constructor calls on loaded models
 * – Uses CI best‑practice array syntax
 */
class Dashboard extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        // Redirect guests to login
        if (! $this->session->userdata('user')) {
            redirect(site_url());
        }

        // Load required models **once** – do NOT call them as functions later
        $this->load->model('vendor/Vendor_model',    'vm');
        $this->load->model('dashboard/Dashboard_model', 'bm');

        // Cache the logged‑in user data for later use
        $this->user = $this->session->userdata('user');
    }

    /**
     * Main dashboard view
     */
    public function index(): void
    {
        $data = $this->user;

        // Force PIC completion first‑time login
        if ($this->vm->check_pic($data['id_user']) === 0) {
            redirect(site_url('dashboard/pernyataan'));
        }

        // Additional models needed only for this page
        $this->load->model('approval/Approval_model', 'am');
        $this->load->model('note/Note_model',         'nm');

        // Gather widget data
        $data['approval_data'] = $this->am->get_total_data($data['id_user']);
        $data['note']          = $this->nm->get_note($data['id_user']);

        // Prepare bar‑graph percentages
        $total = max($data['approval_data']['total'], 1); // avoid divide‑by‑zero
        $data['graphBar'] = [
            [
                'val'   => count($data['approval_data'][0])                                  / $total * 100,
                'color' => '#f39c12',
            ],
            [
                'val'   => (count($data['approval_data'][1]) + count($data['approval_data'][2])) / $total * 100,
                'color' => '#2cc36b',
            ],
            [
                'val'   => (count($data['approval_data'][3]) + count($data['approval_data'][4])) / $total * 100,
                'color' => '#c0392b',
            ],
        ];

        // Compose nested views
        $layout['content'] = $this->load->view('content',   $data, TRUE);
        $layout['script']  = $this->load->view('content_js',$data, TRUE);

        $item['header']  = $this->load->view('header',        $data,   TRUE);
        $item['content'] = $this->load->view('user/dashboard',$layout, TRUE);

        $this->load->view('template', $item);
    }

    /**
     * PIC declaration & vendor statement form
     */
    public function pernyataan(): void
    {
        $data = $this->user;

        // Reference data for selects
        $data['id_legal']   = $this->vm->get_legal();
        $data['pernyataan'] = $this->bm->get_pernyataan();
        $data['data_vendor']= $this->vm->get_pt($data['id_user']);

        // Validation rules (only when user hits NEXT)
        if ($this->input->post('next')) {
            $this->form_validation->set_rules([
                ['field'=>'pic_name',       'label'=>'Nama',            'rules'=>'required'],
                ['field'=>'pic_position',   'label'=>'Jabatan',         'rules'=>'required'],
                ['field'=>'pic_phone',      'label'=>'No Telp',         'rules'=>'required|numeric'],
                ['field'=>'pic_email',      'label'=>'Email',           'rules'=>'required|valid_email'],
                ['field'=>'pic_address',    'label'=>'Alamat',          'rules'=>'required'],
                ['field'=>'admin_name',     'label'=>'Nama Admin',      'rules'=>'required'],
                ['field'=>'admin_position', 'label'=>'Jabatan Admin',   'rules'=>'required'],
            ]);
        }

        if ($this->form_validation->run() === TRUE) {
            $_POST['id_vendor']   = $data['id_user'];
            $_POST['entry_stamp'] = date('Y-m-d H:i:s');

            if ($this->vm->save_pic($this->input->post())) {
                unset($_POST['next']);
                $this->session->set_userdata('form', $this->input->post());
                redirect(site_url());
            }
        }

        // Render form page
        $layout['content'] = $this->load->view('pic_form', $data, TRUE);

        $item['header']  = $this->load->view('header',        $data,   TRUE);
        $item['content'] = $this->load->view('user/dashboard',$layout, TRUE);

        $this->load->view('template', $item);
    }
}
