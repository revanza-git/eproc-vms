<?php

class App extends MY_Controller
{
	public $eproc_db;
	
	private function _require_cli()
	{
		if (!$this->input->is_cli_request()) {
			show_404();
		}
	}

	function __construct(){
		parent::__construct();
		$this->admin = $this->session->userdata('admin');
		$this->load->model('main_model','mm');
		$this->load->model('fppbj_model','fm');
		$this->eproc_db = $this->load->database('eproc',true);
		$this->load->helper('string');
	}

	public function index($id = null)
	{
		$admin = $this->session->userdata('admin');
		
		// Validate admin session
		if(!$admin || !isset($admin['id_user'])){
			redirect(site_url());
			return;
		}
		
		$getUser = $this->mm->to_app($admin['id_user']);
		
		// Validate user data
		if(!$getUser){
			redirect(site_url());
			return;
		}

		$data = array(
			'name' 		 => isset($getUser['name']) ? $getUser['name'] : '',
			'id_user' 	 => isset($getUser['id']) ? $getUser['id'] : '',
			'id_role' 	 => isset($getUser['id_role']) ? $getUser['id_role'] : '',
			'role_name' 	 => isset($getUser['role_name']) ? $getUser['role_name'] : '',
			'type' 		 => 'admin',
			'app' 		 => 1,
			'id_sbu' 	 => isset($getUser['id_sbu']) ? $getUser['id_sbu'] : '',
			'sbu_name' 	 => isset($getUser['sbu_name']) ? $getUser['sbu_name'] : '',
			'division'	 => isset($admin['division']) ? $admin['division'] : '',
			'id_division' => isset($admin['id_division']) ? $admin['id_division'] : '',
		);
		
		$key = random_string('unique').random_string('unique').random_string('unique').random_string('unique');
		
		$this->eproc_db->insert('ms_key_value', array(
			'key' => $key,
			'value'=> json_encode($data),
		));

		$this->session->sess_destroy();
		
		header("Location: ".$this->config->item("pengadaan_url")."main/login_admin?key=".$key);
	}

	public function getUsers()
	{
		show_404();
	}

	public function cleanTrEmailBlast()
	{
		$this->_require_cli();
		$fppbj = $this->db->where('del',0)->get('ms_fppbj')->result_array();
		$this->db->where('del',0)->delete('tr_email_blast');
		foreach ($fppbj as $key => $value) {
			$this->fm->insert_tr_email_blast($value['id'],$value['jwpp_start'],$value['metode_pengadaan']);
		}
		echo "string";
	}

	public function show_lampiran()
	{
		$this->_require_cli();
		$query = " SELECT * FROM ms_fppbj WHERE del = 0 AND entry_stamp LIKE '%2020%' AND ((pr_lampiran != null OR pr_lampiran != '') OR (kak_lampiran != '' OR kak_lampiran != null)) ";

		$data = $this->db->query($query)->result_array();

		$a = '<table border=1>
			<tr>
				<td>Nama Pengadaan</td>
				<td>Lampiran PR</td>
				<td>KAK Lampiran</td>
			</tr>';

		foreach ($data as $key => $value) {
			$a .= '<tr>
				<td><a href="'.site_url('pemaketan/division/'.$value['id_division'].'/'.$value['id']).'">'.$value['nama_pengadaan'].'</a></td>
				<td>'.$value['pr_lampiran'].'</td>
				<td>'.$value['kak_lampiran'].'</td>
			</tr>';
		}

		$a .='</table>';

		echo $a;
	}

	public function clean_division()
	{
		$this->_require_cli();
		$procurement = $this->eproc_db->get('ms_procurement')->result_array();

		$division = array(
			6 => 3,// sekper
			7 => 4,//Hukum
			8 => 5,//HSSE
			9 => 2,//spi
			10=> 8,//lng&gas
			11=> 7,//perencanaan&pengembangan bisnis
			12=>10,//reliability
			13=>12,//QMQA
			14=> 9,//Transportasi LNG & FSRU
			15=>11,//gas & ORF
			16=>13,//controller
			17=>14,//perbendaharaan
			18=>18,//layum
			19=>16,//perbendaharaan
			20=>15,//sisteminformasi
			21=> 1,//procurement
			22=> 6,//resiko
		);

		foreach ($procurement as $key => $value) {
			$update = array(
				'id_division' => $division[$value['budget_spender']]
			);

			if ($value['id_division'] == '0' || $value['id_division'] == '' || $value['id_division'] == null) {
				$this->eproc_db->where('id', $value['id'])->update('ms_procurement',$update);
			}
		}
	}
}
