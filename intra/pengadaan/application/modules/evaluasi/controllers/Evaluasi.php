<?php defined('BASEPATH') or exit('No direct script access allowed');

class Evaluasi extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		if (!$this->session->userdata('user')) {
			redirect(site_url());
		}
		$this->load->model('evaluasi_model', 'em');
		$this->load->model('assessment/assessment_model', 'am');
		$this->load->helpers('utility_helpers');
		$this->load->library('encrypt');
		$this->load->library('utility');
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

		$data['pengadaan_list'] = $this->em->get_pengadaan_list($search, $sort, $page, $per_page, TRUE);
		foreach ($data['pengadaan_list'] as $key => $value) {
			if ($value['pemenang'] == '') {
				$winner = $this->am->get_winner($value['id']);
				$data['pengadaan_list'][$key]['pemenang'] = $winner['pemenang'];
			}
		}
		// print_r($data['pengadaan_list']);die;
		$data['admin']			= $this->session->userdata('admin');
		$data['filter_list'] = $this->filter->group_filter_post($this->get_field_pe());

		$data['pagination'] = $this->utility->generate_page('evaluasi', $sort, $per_page, $this->em->get_pengadaan_list($search, $sort, '', '', FALSE));
		$data['sort'] = $sort;

		$layout['content'] = $this->load->view('content', $data, TRUE);
		$layout['script'] = $this->load->view('content_js', $data, TRUE);
		$item['header'] = $this->load->view('dashboard/header', $this->session->userdata('user'), TRUE);
		$item['content'] = $this->load->view('user/dashboard', $layout, TRUE);
		$this->load->view('template', $item);
	}

	public function form_feedback($id_proc, $id_vendor)
	{
		$_POST 	= $this->securities->clean_input($_POST, 'save');
		$user 	= $this->session->userdata('user');
		$vld 	= 	array(
			array(
				'field' => 'remark',
				'label' => 'Pesan',
				'rules' => 'required'
			)
		);

		$this->form_validation->set_rules($vld);
		if ($this->form_validation->run() == TRUE) {
			$save = $this->input->post();
			unset($save['Simpan']);
			$save['id_procurement'] = $id_proc;
			$save['id_vendor'] = $id_vendor;
			$save['entry_stamp'] = date('Y-m-d H:i:s');

			$result = $this->em->save_feedback($save);
			$vendor = $this->em->get_vendor($id_vendor);
			if ($result) {
				$div_proc = $this->em->get_division(1);
				$proc = $this->em->get_pengadaan($id_proc);
				$msg = $vendor['legal'] . " - " . $vendor['name'] . " memberikan umpan balik ke pengadaan " . $proc['name'] . " dengan pesan sebagai berikut : <br> " . $save['remark'];
				$sub = "UMPAN BALIK EVALUASI KINERJA";

				foreach ($div_proc as $key => $value) {
					$this->utility->mail($value['email'], $msg, $sub);
				}
				$this->session->set_flashdata('msgSuccess', '<p class="msgSuccess">Sukses memberikan umpan balik!</p>');
				redirect(site_url('evaluasi'));
			}
		}

		$layout['content']	= $this->load->view('form_feedback', $data, TRUE);

		$item['header'] = $this->load->view('dashboard/header', $user, TRUE);
		$item['content'] = $this->load->view('user/dashboard', $layout, TRUE);
		$this->load->view('template', $item);
	}

	public function get_field_pe()
	{
		return array(
			array(
				'label'	=>	'Pengadaan',
				'filter' =>	array(
					array('table' => 'ms_procurement|name', 'type' => 'text', 'label' => 'Nama Pengadaan'),
					array('table' => 'ms_vendor|name', 'type' => 'text', 'label' => 'Nama Pemenang'),
					array('table' => 'ms_procurement_bsb|id_bidang|get_bidang', 'type' => 'dropdown', 'label' => 'Bidang'),
				)
			),
			array(
				'label'	=>	'Kontrak',
				'filter' =>	array(
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
				'label'	=>	'Assessment',
				'filter' =>	array(
					array('table' => 'tr_assessment|point', 'type' => 'number_range', 'label' => 'Skor Assessment'),
					array('table' => 'tr_assessment_point|category|get_warna', 'type' => 'dropdown', 'label' => 'Warna'),
				)
			),
		);
	}
}
