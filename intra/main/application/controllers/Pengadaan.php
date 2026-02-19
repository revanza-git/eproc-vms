<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pengadaan extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('pengadaan_model', 'pm');
	}

	public function index($id = null)
	{
		$this->breadcrumb->addlevel(1, array(
			'url' => site_url('pengadaan'),
			'title' => 'Perencanaan Pengadaan'
		));

		$data = array(); // Initialize $data variable
		$this->header = 'Perencanaan Pengadaan';
		$this->content = $this->load->view('pengadaan/list',$data, TRUE);
		$this->script = $this->load->view('pengadaan/list_js', $data, TRUE);
		parent::index($id);
	}

	public function getData($id = null)
	{
		$config['query'] = $this->pm->getData();
		$return = $this->tablegenerator->initialize($config);
		// print_r($return);
		echo json_encode($return);
	}

	public function fp3()
	{
		$this->breadcrumb->addlevel(1, array(
			'url' => site_url('pengadaan/fp3'),
			'title' => 'FP3'
		));
		
		$data['admin'] = $this->session->userdata('admin');
		$data['id'] = date('Y'); // Use current year as default ID for FP3 operations

		$this->header = 'FP3';
		$this->content = $this->load->view('pengadaan/list_fp3',$data, TRUE);
		$this->script = $this->load->view('pengadaan/list_fp3_js', $data, TRUE);
		parent::index();
	}

	public function getDataFP3($id = null)
	{
		$config['query'] = $this->pm->getDataFP3();
		$return = $this->tablegenerator->initialize($config);
		// print_r($return);
		echo json_encode($return);
	}

	public function autoApprove()
	{
		$query = " SELECT * FROM ms_fppbj WHERE is_status = 1 AND del = 0 AND entry_stamp LIKE '%2019%' ";
		$data = $this->db->query($query)->result_array();

		foreach ($data as $k => $v) {
			if ($v['idr_anggaran'] < 100000000) {
				$up_fppbj = array(
					'is_approved'	=>	3
				);
				$up_fp3 = array(
					'is_approved'	=>	3
				);
			} else {
				$up_fppbj = array(
					'is_approved'	=>	4
				);
				$up_fp3 = array(
					'is_approved'	=>	4
				);
			}

			$this->db->where('id', $v['id'])->update('ms_fppbj',$up_fppbj);
			$this->db->where('id_fppbj', $v['id'])->update('ms_fp3',$up_fp3);
		}
	}
}

/* End of file Pengadaan.php */
/* Location: ./application/controllers/Pengadaan.php */