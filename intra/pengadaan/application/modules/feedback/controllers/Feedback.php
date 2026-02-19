<?php defined('BASEPATH') or exit('No direct script access allowed');

class Feedback extends CI_Controller
{

    public $id_pengadaan;
    public $tabNav;

    public function __construct()
    {
        parent::__construct();

        if (!$this->session->userdata('admin')) {
            redirect(site_url());
        }

        $this->load->model('feedback_model', 'fm');
        $this->load->helpers('utility_helpers');
    }

    public function index()
    {
        // print_r($this->session->userdata());die;
        $this->load->library('form');
        $search = $this->input->get('q');
        $page = '';
        $post = $this->input->post();

        $per_page = 10;

        $sort = $this->utility->generateSort(array('ms_procurement.name', 'pemenang', 'point', 'tr_assessment.category'));

        $data['feedback_list'] = $this->fm->get_feedback_list($search, $sort, $page, $per_page, TRUE);

        $data['admin']            = $this->session->userdata('admin');
        $data['filter_list'] = $this->filter->group_filter_post($this->get_field_pe());

        $data['pagination'] = $this->utility->generate_page('feedback', $sort, $per_page, $this->fm->get_feedback_list($search, $sort, '', '', FALSE));
        $data['sort'] = $sort;

        $layout['content'] = $this->load->view('feedback/content', $data, TRUE);
        $item['header'] = $this->load->view('admin/header', $this->session->userdata('admin'), TRUE);
        $item['content'] = $this->load->view('admin/dashboard', $layout, TRUE);
        $this->load->view('template', $item);
    }

    public function view($id)
    {
        $admin = $this->session->userdata('admin');

        $form = ($this->session->userdata('form')) ? $this->session->userdata('form') : array();

        $fill = $this->securities->clean_input($_POST, 'save');
        $item = $vld = $save_data = array();
        $user = $this->session->userdata('user');
        $layout['get_feedback'] = $this->fm->get_feedback($id);

        $vld =     array(
            array(
                'field' => 'reply',
                'label' => 'Pesan',
                'rules' => 'required'
            )
        );
        $this->form_validation->set_rules($vld);
        // print_r($this->input->post());die;
        if ($this->form_validation->run() == TRUE) {
            $save = $this->input->post();
            $save['edit_stamp'] = date("Y-m-d H:i:s");
            $save['is_reply'] = 1;
            $save['reply_by'] = $admin['id_user'];
            unset($save['Simpan']);

            $res = $this->fm->save_reply($id, $save);
            if ($res) {
                $vendor = $this->fm->get_vendor_email($id);
                $this->session->set_flashdata('msgSuccess', '<p class="msgSuccess">Sukses membalas umpan balik!</p>');
                // $this->utility->mail($vendor['email'], $msg, $sub);
                redirect(site_url('feedback/view/' . $id));
            }
        }
        $layout['content'] = $this->load->view('feedback/view', $layout, TRUE);

        $item['header'] = $this->load->view('admin/header', $admin, TRUE);
        $item['content'] = $this->load->view('admin/dashboard', $layout, TRUE);
        $this->load->view('template', $item);
    }

    public function get_field_pe()
    {
        return array(
            array(
                'label'    =>    'Pengadaan',
                'filter' =>    array(
                    array('table' => 'ms_procurement|name', 'type' => 'text', 'label' => 'Nama Pengadaan'),
                    array('table' => 'ms_vendor|name', 'type' => 'text', 'label' => 'Nama Pemenang'),
                    array('table' => 'ms_procurement_bsb|id_bidang|get_bidang', 'type' => 'dropdown', 'label' => 'Bidang'),
                )
            ),
            array(
                'label'    =>    'Kontrak',
                'filter' =>    array(
                    array('table' => 'ms_contract|contract_date', 'type' => 'date', 'label' => 'Tanggal Kontrak'),
                    array('table' => 'ms_contract|no_contract', 'type' => 'text', 'label' => 'No. Kontrak'),
                    array('table' => 'ms_contract|no_sppbj', 'type' => 'text', 'label' => 'SPPBJ'),
                    array('table' => 'ms_contract|sppbj_date', 'type' => 'date', 'label' => 'Tanggal SPPBJ'),
                    array('table' => 'ms_contract|no_spmk', 'type' => 'text', 'label' => 'SPMK'),
                    array('table' => 'ms_contract|spmk_date', 'type' => 'date', 'label' => 'Tanggal SPMK'),
                    array('table' => 'ms_contract|contract_price', 'type' => 'number_range', 'label' => 'Nilai Kontrak (Rp)'),
                )
            ),
            array(
                'label'    =>    'Assessment',
                'filter' =>    array(
                    array('table' => 'tr_assessment|point', 'type' => 'number_range', 'label' => 'Skor Assessment'),
                    array('table' => 'tr_assessment_point|category|get_warna', 'type' => 'dropdown', 'label' => 'Warna'),
                )
            ),
        );
    }
}
