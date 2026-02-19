<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Kontrak extends CI_Controller {

	public $id_pengadaan;
	public $tabNav;

	public function __construct(){
		parent::__construct();

		if(!$this->session->userdata('admin')){
			redirect(site_url());
		}

		$this->load->model('pengadaan/pengadaan_model','pm');
		$this->load->model('izin/izin_model','im');
		$this->load->model('kontrak_model','km');
		$this->load->model('kontrak_model_test','kmt');
		$this->load->model('graph_model','gm');
		
		if ($this->uri->segment(3) === FALSE)
		{
		    $this->id_pengadaan = 0;
		}
		else
		{
		   	$this->id_pengadaan = $this->uri->segment(3);
		}
		$array = array(
						'kontrak'=>	array(
								'url' 	=> site_url('kontrak/view/'.$this->id_pengadaan.'/kontrak#tabNav'),
								'label' => 'Kontrak'
								),

						'spk'=>	array(
								'url' 	=> site_url('kontrak/view/'.$this->id_pengadaan.'/spk#tabNav'),
								'label' => 'SPK'
								),
						'amandemen'=>array(
								'url' 	=> site_url('kontrak/view/'.$this->id_pengadaan.'/amandemen#tabNav'),
								'label'	=>'Amandemen'
								),
						'bast'=>array(
								'url' 	=> site_url('kontrak/view/'.$this->id_pengadaan.'/bast#tabNav'),
								'label'	=>'BAST'
								)
					);
		$this->tabNav = $array;
	}

	
	public function get_field_in(){
		return array(
			array(
				'label'	=>	'Pengadaan',
				'filter'=>	array(
								array('table'=>'ms_procurement|name' ,'type'=>'text','label'=> 'Nama Pengadaan'),
								array('table'=>'ms_procurement|budget_year' ,'type'=>'text','label'=> 'Tahun Anggaran'),
								array('table'=>'ms_vendor|name' ,'type'=>'text','label'=> 'Nama Pemenang'),
								array('table'=>'ms_procurement_bsb|id_bidang|get_bidang' ,'type'=>'dropdown','label'=> 'Bidang'),
								array('table'=>'tb_mekanisme|name','type'=>'text','label'=> 'Metode Pengadaan'),
							)
			),
			array(
				'label'	=>	'Kontrak',
				'filter'=>	array(
								// array('table'=>'ms_vendor mv|mv.name' ,'type'=>'text','label'=> 'Pemenang Kontrak'),
								array('table'=>'ms_contract|contract_date' 	,'type'=>'date','label'=> 'Tanggal Kontrak'),
								array('table'=>'ms_contract|no_contract','type'=>'text','label'=> 'No. Kontrak'),
								array('table'=>'ms_contract|no_sppbj','type'=>'text','label'=> 'SPPBJ'),
								array('table'=>'ms_contract|sppbj_date','type'=>'date','label'=> 'Tanggal SPPBJ'),
								array('table'=>'ms_contract|no_spmk','type'=>'text','label'=> 'SPMK'),
								array('table'=>'ms_contract|spmk_date','type'=>'date','label'=> 'Tanggal SPMK'),
								array('table'=>'ms_contract|contract_price','type'=>'number_range','label'=> 'Nilai Kontrak (Rp)'),
							)
			),
			
		);
	}

	public function index()
	{
		$this->load->library('form');
		$this->load->library('datatables');

		$search = $this->input->get('q');
		$page = '';
		$post = $this->input->post();

		$per_page=10;

		$sort = $this->utility->generateSort(array('ms_procurement.name', 'pemenang', 'proc_date','id','contract_date','metode'));
		
		$data['admin'] = $this->session->userdata('admin');
		$data['pengadaan_list']=$this->pm->get_pengadaan_list($search, $sort, $page, $per_page,TRUE);

		$data['filter_list'] = $this->filter->group_filter_post($this->get_field_in());

		$data['pagination'] = $this->utility->generate_page('kontrak',$sort, $per_page, $this->pm->get_pengadaan_list($search, $sort, '','',FALSE));
		$data['sort'] = $sort;

		$layout['content']= $this->load->view('kontrak/content',$data,TRUE);
		$layout['script']= $this->load->view('kontrak/content_js',$data,TRUE);
		$item['header'] = $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function view($id,$page='kontrak',$opt = null){
		$this->session->set_userdata('summary',$page);
		$data = $this->pm->get_pengadaan($id);
		// echo 'table: '; print_r($data['table']);
		$data['id'] = $id;
		$data['table'] = $this->$page($id,$page,$opt);
		
		$layout['content']= $this->load->view('kontrak/view',$data,TRUE);
		$layout['script']= $this->load->view('kontrak/content_js',$data,TRUE);

		$item['header'] = $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function edit($id)
	{
		$_POST = $this->securities->clean_input($_POST,'save');
		$admin = $this->session->userdata('admin');
		$vld = 	array(
			array(
				'field'=>'name',
				'label'=>'Nama Paket Pengadaan',
				'rules'=>'required'
				),
			array(
				'field'=>'budget_source',
				'label'=>'Sumber Anggaran',
				'rules'=>'required'
				),
			array(
				'field'=>'idr_value',
				'label'=>'Nilai',
				'rules'=>'callback_check_nilai'
				),
			array(
				'field'=>'kurs_value',
				'label'=>'Nilai',
				'rules'=>'callback_check_nilai'
				),
			array(
				'field'=>'id_kurs',
				'label'=>'Kurs',
				'rules'=>'required'
				),
			
			array(
				'field'=>'budget_year',
				'label'=>'Tahun Anggaran',
				'rules'=>'required'
				),
			array(
				'field'=>'budget_holder',
				'label'=>'Budget Holder',
				'rules'=>'required'
				),
			array(
				'field'=>'budget_spender',
				'label'=>'Pemegang Cost Center',
				'rules'=>'required'
				),
			
			array(
				'field'=>'id_mekanisme',
				'label'=>'Metode Pelelangan',
				'rules'=>'required'
				),
			array(
				'field'=>'evaluation_method',
				'label'=>'Metode Evaluasi',
				'rules'=>'required'
				),
			array(
				'field'=>'tipe_transaksi',
				'label'=>'Pilih Tipe Pengadaan',
				'rules'=>''
				)
			);
		
			foreach ($_FILES as $key => $value) {
				if ($value['name'] != '') {
					$vld[] = array(
						'field' => $key,
						'label' => 'Dokumen',
						'rules'=>'callback_do_upload['.$key.']'
					);
				}
			}
		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			
			$_POST['edit_stamp'] 	= date("Y-m-d H:i:s");
			$_POST['idr_value'] 	= preg_replace("/[,]/", "", $this->input->post('idr_value'));
			$_POST['kurs_value'] 	= preg_replace("/[,]/", "", $this->input->post('kurs_value'));
			unset($_POST['Simpan']);
			$res = $this->km->edit_data($this->input->post(),$id);
			if($res){
				$this->session->set_flashdata('msgSuccesss','<p class="msgSuccess">Sukses mengubah data!</p>');

				redirect(site_url('kontrak/view/'.$id.''));
			}
		}
		$data = $this->pm->get_pengadaan($id);

		$arr = array('jasa_konstruksi' => 'Jasa Konstruksi', 'jasa_konsultasi' => 'Jasa Konsultasi', 'jasa_lainnya' => 'Jasa Lainnya','jasa_konsultan_non_konstruksi' => 'Jasa Konsultan non-Konstruksi', 'jasa_konsultan_konstruksi' => 'Jasa Konsultan Perencana/Pengawas Konstruksi');

		$data['ttr']	= $arr;
		$data['v_ttr']	= $data['tipe_transaksi'];

		$data['pejabat_pengadaan'] = $this->pm->get_pejabat();
		$data['budget_holder_list'] = $this->pm->get_budget_holder();
		$data['budget_spender_list'] = $this->pm->get_budget_spender();
		$data['id_mekanisme_list'] = $this->pm->get_mekanisme();
		$data['id'] = $id;
		$layout['content']= $this->load->view('edit',$data,TRUE);
		$item['header'] = $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function check_nilai($field){
		$idr_field = $this->input->post('idr_value');
		$kurs_field = $this->input->post('kurs_value');
		
		if($idr_field==''&&$kurs_field==''){
			$this->form_validation->set_message('check_nilai','Isi salah satu');
			return false;
		}else{
			return true;
		}
	}

	public function kontrak($id){
		$data['sort'] 			= $this->utility->generateSort(array('no', 'contract_date', 'contract_file'));
		$per_page				= 10;
		$page 					= 'kontrak';
		$data['pagination'] 	= $this->utility->generate_page('kontrak/view/'.$id.'/'.$page,$data['sort'], $per_page,  $this->km->get_contract_list($id,'', $data['sort'], '','',FALSE));
		$data['list'] 			= $this->km->get_contract_list($id,'', $data['sort'], '','',FALSE);
		$admin 					= $this->session->userdata('admin');
		$data_kontrak 			= $this->pm->get_kontrak($id);
		$data['tabNav'] 		= $this->tabNav;
		$data['winner'] 		= $this->pm->get_winner_vendor($id);
		
		if (!empty($this->gm->get_graph($id))) {
			$data['graph']			= $this->gm->get_graph($id);
		}
		// print_r($this->gm->get_graph($id));die;
		
		$data['id'] 			= $id;

		return $this->load->view('tab/kontrak',$data,TRUE);
	}

	public function tambah_kontrak($id)
	{
		$admin = $this->session->userdata('admin');

		$form = ($this->session->userdata('form'))?$this->session->userdata('form'):array();
		
		$fill = $this->securities->clean_input($_POST,'save');
		$item = $vld = $save_data = array();
		$user = $this->session->userdata('user');
		$layout['get_dpt_type'] = $this->im->get_dpt_type();
		$layout['winner'] 		= $this->pm->get_winner_vendor($id);
		$layout['kurs'] = $this->km->get_kurs($id);
		$vld = 	array(
					array(
						'field'=>'id_vendor',
						'label'=>'Perusahaan',
						'rules'=>'required',
						// 'rules'=>''
					),
					array(
						'field'=>'no_contract',
						'label'=>'No. Kontrak',
						'rules'=>'required'
					),
					array(
						'field'=>'start_contract',
						'label'=>'Tanggal Mulai',
						'rules'=>'required'
					),
					array(
						'field'=>'end_contract',
						'label'=>'Tanggal Selesai',
						'rules'=>'required'
					),
					array(
						'field'=>'contract_price',
						'label'=>'Nilai Kontrak / PO',
						'rules'=>'required'
					),
					array(
						'field'=>'po_file',
						'label'=>'Dokumen Kontrak',
						'rules'=>'callback_do_upload[po_file]'
					)
				);
		$this->form_validation->set_rules($vld);

		if($this->form_validation->run()==TRUE){
			echo 'form true';
			$_POST['entry_stamp'] = date("Y-m-d H:i:s");
			$_POST['id_procurement'] = $id;
			$_POST['contract_price'] 	= preg_replace("/[,]/", "", $this->input->post('contract_price'));
			unset($_POST['Simpan']);
			$res = $this->km->save_kontrak($this->input->post());
			if($res){
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses membuat kontrak!</p>');

				redirect(site_url('kontrak/view/'.$id.'/kontrak'.'#tabNav'));
			}
		}
		$layout['content']= $this->load->view('tab/tambah_kontrak',$layout,TRUE);

		$item['header'] = $this->load->view('admin/header',$admin,TRUE);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function edit_kontrak($id,$id_proc)
	{
		$admin = $this->session->userdata('admin');

		$data = $this->km->get_data_kontrak($id);
		$form = ($this->session->userdata('form'))?$this->session->userdata('form'):array();
		
		$fill = $this->securities->clean_input($_POST,'save');
		$item = $vld = $save_data = array();
		$user = $this->session->userdata('user');
		$layout['get_dpt_type'] = $this->im->get_dpt_type();
		$data['winner'] 		= $this->pm->get_winner_vendor($id_proc);
		$data['kurs'] = $this->km->get_kurs($id_proc);
		$vld = 	array(
					array(
						'field'=>'no_contract',
						'label'=>'No. Kontrak',
						'rules'=>'required'
					),
					array(
						'field'=>'start_contract',
						'label'=>'Tanggal Mulai',
						'rules'=>'required'
					),
					array(
						'field'=>'end_contract',
						'label'=>'Tanggal Selesai',
						'rules'=>'required'
					),
					// array(
					// 	'field'=>'po_file',
					// 	'label'=>'Dokumen Kontrak',
					// 	'rules'=>'callback_do_upload[po_file]'
					// )
				);

		foreach ($_FILES as $key => $value) {
				if ($value['name'] != '') {
					$vld[] = array(
						'field' => $key,
						'label' => 'Dokumen',
						'rules'=>'callback_do_upload['.$key.']'
					);
				}
			}
		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			// echo "string";die;
			$_POST['edit_stamp'] = date("Y-m-d H:i:s");
			unset($_POST['Update']);

			$res = $this->km->edit_kontrak($this->input->post(),$id);
			if($res){
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mengedit kontrak!</p>');

				redirect(site_url('kontrak/view/'.$id_proc.'/kontrak'.'#tabNav'));
			}
		}
		$layout['content']= $this->load->view('tab/edit_kontrak',$data,TRUE);

		$item['header'] = $this->load->view('admin/header',$admin,TRUE);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
		
	}

	public function hapus_kontrak($id,$id_proc)
	{
		$res = $this->km->hapus_kontrak($id);
		if ($res) {
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menghapus kontrak!</p>');

				redirect(site_url('kontrak/view/'.$id_proc.'/kontrak'.'#tabNav'));
		}
	}

	public function spk($id){
		$data['sort'] 			= $this->utility->generateSort(array('no', 'contract_date', 'contract_file'));
		$per_page				= 10;
		$page 					= 'spk';
		$data['count_contract'] = $this->km->countContract($id);
		$data['pagination'] 	= $this->utility->generate_page('kontrak/view/'.$id.'/'.$page,$data['sort'], $per_page,  $this->km->get_spk_list($id,'', $data['sort'], '','',FALSE));
		$data['list'] 			= $this->km->get_spk_list($id,'', $data['sort'], '','',FALSE);
		$admin 					= $this->session->userdata('admin');
		$data['tabNav'] 		= $this->tabNav;		
		$data['id'] 			= $id;

		if (!empty($this->gm->get_graph($id))) {
			$data['graph']			= $this->gm->get_graph($id);
		}

		// print_r($this->gm->get_graph($id));die;

		return $this->load->view('tab/spk',$data,TRUE);
	}

	public function tambah_spk($id)
	{
		$admin = $this->session->userdata('admin');

		$form = ($this->session->userdata('form'))?$this->session->userdata('form'):array();
		
		$fill = $this->securities->clean_input($_POST,'save');
		$item = $vld = $save_data = array();
		$user = $this->session->userdata('user');
		$layout['get_dpt_type'] = $this->im->get_dpt_type();
		$vld = 	array(
					array(
						'field'=>'no',
						'label'=>'No. SPK',
						'rules'=>'required'
					),
					array(
						'field'=>'start_date',
						'label'=>'Tanggal Mulai',
						'rules'=>'callback_check_mulai['.$id.']'
					),
					array(
						'field'=>'end_date',
						'label'=>'Tanggal Selesai',
						'rules'=>'callback_check_akhir['.$id.']'
					),
					array(
						'field'=>'spk_file',
						'label'=>'Dokumen SPK',
						'rules'=>'callback_do_upload[spk_file]'
					)
				);
		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			echo 'form true';
			$_POST['entry_stamp'] = date("Y-m-d H:i:s");
			$_POST['id_proc'] = $id;
			unset($_POST['Simpan']);
			$res = $this->km->save_spk($this->input->post());
			echo 'form res'; print_r($res);
			// $res = $this->kmt->save_spk($this->input->post());
			if($res){
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses membuat spk!</p>');

				redirect(site_url('kontrak/view/'.$id.'/spk'.'#tabNav'));
			}
		}
		$layout['content']= $this->load->view('tab/tambah_spk',$layout,TRUE);

		$item['header'] = $this->load->view('admin/header',$admin,TRUE);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function edit_spk($id,$id_proc)
	{
		$admin = $this->session->userdata('admin');

		$data = $this->km->get_data_spk($id);
		$form = ($this->session->userdata('form'))?$this->session->userdata('form'):array();
		
		$fill = $this->securities->clean_input($_POST,'save');
		$item = $vld = $save_data = array();
		$user = $this->session->userdata('user');
		$vld = 	array(
					array(
						'field'=>'no',
						'label'=>'No. SPK',
						'rules'=>'required'
					),
					array(
						'field'=>'start_date',
						'label'=>'Tanggal Mulai',
						'rules'=>'callback_check_mulai['.$id_proc.']'
					),
					array(
						'field'=>'end_date',
						'label'=>'Tanggal Selesai',
						'rules'=>'callback_check_akhir['.$id_proc.']'
					),
					// array(
					// 	'field'=>'spk_file',
					// 	'label'=>'Dokumen SPK',
					// 	'rules'=>'callback_do_upload[spk_file]'
					// )
				);
		foreach ($_FILES as $key => $value) {
				if ($value['name'] != '') {
					$vld[] = array(
						'field' => $key,
						'label' => 'Dokumen',
						'rules'=>'callback_do_upload['.$key.']'
					);
				}
			}
		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			$_POST['edit_stamp'] = date("Y-m-d H:i:s");
			unset($_POST['Update']);
			$res = $this->km->edit_spk($this->input->post(),$id);
			if($res){
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mengedit spk!</p>');

				redirect(site_url('kontrak/view/'.$id_proc.'/spk'.'#tabNav'));
			}
		}
		$layout['content']= $this->load->view('tab/edit_spk',$data,TRUE);

		$item['header'] = $this->load->view('admin/header',$admin,TRUE);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
		
	}

	public function hapus_spk($id,$id_proc)
	{
		$res = $this->km->hapus_spk($id);
		if ($res) {
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menghapus SPK!</p>');

				redirect(site_url('kontrak/view/'.$id_proc.'/spk'.'#tabNav'));
		}
	}

	public function amandemen($id=0,$page,$id_amandemen=0){
		$data['sort'] 			= $this->utility->generateSort(array('no', 'contract_date', 'contract_file'));
		$per_page				= 10;
		$data['count_contract'] = $this->km->countContract($id);
		$data['pagination'] 	= $this->utility->generate_page('kontrak/view/'.$id.'/'.$page,$data['sort'], $per_page,  $this->km->get_amandemen_list($id,'', $data['sort'], '','',FALSE));
		$data['list'] 			= $this->km->get_amandemen_list($id,'', $data['sort'], '','',FALSE);
		$admin 					= $this->session->userdata('admin');
		$data['tabNav'] 		= $this->tabNav;		
		$data['id'] = $id;

		if (!empty($this->gm->get_graph($id))) {
			$data['graph']			= $this->gm->get_graph($id);
		}

		return $this->load->view('tab/amandemen',$data,TRUE);
	}

	public function tambah_amandemen($id)
	{
		$admin = $this->session->userdata('admin');

		$form = ($this->session->userdata('form'))?$this->session->userdata('form'):array();
		
		$fill = $this->securities->clean_input($_POST,'save');
		$item = $vld = $save_data = array();
		$user = $this->session->userdata('user');
		$layout['get_dpt_type'] = $this->im->get_dpt_type();
		$vld = 	array(
					array(
						'field'=>'no',
						'label'=>'No. amandemen',
						'rules'=>'required'
					),
					array(
						'field'=>'start_date',
						'label'=>'Tanggal Mulai',
						'rules'=>'callback_check_amandemen['.$id.']'
					),
					array(
						'field'=>'end_date',
						'label'=>'Tanggal Selesai',
						'rules'=>'required'
					),
					array(
						'field'=>'amandemen_file',
						'label'=>'Dokumen amandemen',
						'rules'=>'callback_do_upload[amandemen_file]'
					)
				);
		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			$_POST['entry_stamp'] = date("Y-m-d H:i:s");
			$_POST['id_proc'] = $id;
			unset($_POST['Simpan']);
			$res = $this->km->save_amandemen($this->input->post());
			if($res){
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses membuat amandemen!</p>');

				redirect(site_url('kontrak/view/'.$id.'/amandemen'.'#tabNav'));
			}
		}
		$layout['content']= $this->load->view('tab/tambah_amandemen',$layout,TRUE);

		$item['header'] = $this->load->view('admin/header',$admin,TRUE);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function edit_amandemen($id,$id_proc)
	{
		$admin = $this->session->userdata('admin');

		$data = $this->km->get_data_amandemen($id);
		$form = ($this->session->userdata('form'))?$this->session->userdata('form'):array();
		
		$fill = $this->securities->clean_input($_POST,'save');
		$item = $vld = $save_data = array();
		$user = $this->session->userdata('user');
		$vld = 	array(
					array(
						'field'=>'no',
						'label'=>'No. SPK',
						'rules'=>'required'
					),
					array(
						'field'=>'start_date',
						'label'=>'Tanggal Mulai',
						'rules'=>'callback_check_amandemen['.$id_proc.']'
					),
					array(
						'field'=>'end_date',
						'label'=>'Tanggal Selesai',
						'rules'=>'required'
					),
					// array(
					// 	'field'=>'amandemen_file',
					// 	'label'=>'Dokumen SPK',
					// 	'rules'=>'callback_do_upload[amandemen_file]'
					// )
				);
		foreach ($_FILES as $key => $value) {
				if ($value['name'] != '') {
					$vld[] = array(
						'field' => $key,
						'label' => 'Dokumen',
						'rules'=>'callback_do_upload['.$key.']'
					);
				}
			}
		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			$_POST['edit_stamp'] = date("Y-m-d H:i:s");
			unset($_POST['Update']);
			$res = $this->km->edit_amandemen($this->input->post(),$id);
			if($res){
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mengedit amandemen!</p>');

				redirect(site_url('kontrak/view/'.$id_proc.'/amandemen'.'#tabNav'));
			}
		}
		$layout['content']= $this->load->view('tab/edit_amandemen',$data,TRUE);

		$item['header'] = $this->load->view('admin/header',$admin,TRUE);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
		
	}

	public function hapus_amandemen($id,$id_proc)
	{
		$res = $this->km->hapus_amandemen($id);
		if ($res) {
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menghapus amandemen!</p>');

				redirect(site_url('kontrak/view/'.$id_proc.'/amandemen'.'#tabNav'));
		}
	}

	public function bast($id=0,$page,$id_amandemen=0){
		$data['sort'] 			= $this->utility->generateSort(array('no', 'contract_date', 'contract_file'));
		$per_page				= 10;
		$data['count_contract'] = $this->km->countContract($id);
		$data['pagination'] 	= $this->utility->generate_page('kontrak/view/'.$id.'/'.$page,$data['sort'], $per_page,  $this->km->get_bast_list($id,'', $data['sort'], '','',FALSE));
		$data['list'] 			= $this->km->get_bast_list($id,'', $data['sort'], '','',FALSE);
		$admin 					= $this->session->userdata('admin');
		$data['tabNav'] 		= $this->tabNav;		
		$data['id'] = $id;

		if (!empty($this->gm->get_graph($id))) {
			$data['graph']			= $this->gm->get_graph($id);
		}

		return $this->load->view('tab/bast',$data,TRUE);
	}

	public function tambah_bast($id)
	{
		$admin = $this->session->userdata('admin');

		$form = ($this->session->userdata('form'))?$this->session->userdata('form'):array();
		
		$fill = $this->securities->clean_input($_POST,'save');
		$item = $vld = $save_data = array();
		$user = $this->session->userdata('user');
		$layout['get_dpt_type'] = $this->im->get_dpt_type();
		$vld = 	array(
					array(
						'field'=>'no',
						'label'=>'No. BAST',
						'rules'=>'required'
					),
					array(
						'field'=>'bast_type',
						'label'=>'Tipe BAST',
						'rules'=>'required'
					),
					// array(
					// 	'field'=>'start_date',
					// 	'label'=>'Tanggal Mulai',
					// 	'rules'=>'callback_check_mulai['.$id.']'
					// ),
					array(
						'field'=>'end_date',
						'label'=>'Tanggal Selesai',
						'rules'=>'required'
					),
					array(
						'field'=>'bast_file',
						'label'=>'Dokumen SPK',
						'rules'=>'callback_do_upload[bast_file]'
					)
				);
		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			$_POST['entry_stamp'] = date("Y-m-d H:i:s");
			$_POST['id_proc'] = $id;
			unset($_POST['Simpan']);
			// echo 'pp  '; print_r($_POST);
			$res = $this->km->save_bast($this->input->post());
			if($res){
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses membuat bast!</p>');

				redirect(site_url('kontrak/view/'.$id.'/bast'.'#tabNav'));
			}
		}
		$layout['content']= $this->load->view('tab/tambah_bast',$layout,TRUE);

		$item['header'] = $this->load->view('admin/header',$admin,TRUE);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function edit_bast($id,$id_proc,$id_spk=0)
	{
		$admin = $this->session->userdata('admin');
		
		$data = $this->km->get_data_bast($id);
		// print_r($data);die;
		$form = ($this->session->userdata('form'))?$this->session->userdata('form'):array();
		
		$fill = $this->securities->clean_input($_POST,'save');
		$item = $vld = $save_data = array();
		$user = $this->session->userdata('user');
		$vld = 	array(
					array(
						'field'=>'no',
						'label'=>'No. SPK',
						'rules'=>'required'
					),
					array(
						'field'=>'bast_type',
						'label'=>'Tipe BAST',
						'rules'=>'required'
					),
					// array(
					// 	'field'=>'start_date',
					// 	'label'=>'Tanggal Mulai',
					// 	'rules'=>'callback_check_mulai['.$id_proc.']'
					// ),
					array(
						'field'=>'end_date',
						'label'=>'Tanggal Selesai',
						'rules'=>'required'
					),
					// array(
					// 	'field'=>'bast_file',
					// 	'label'=>'Dokumen SPK',
					// 	'rules'=>'callback_do_upload[bast_file]'
					// )
				);
		foreach ($_FILES as $key => $value) {
				if ($value['name'] != '') {
					$vld[] = array(
						'field' => $key,
						'label' => 'Dokumen',
						'rules'=>'callback_do_upload['.$key.']'
					);
				}
			}
		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			$_POST['edit_stamp'] = date("Y-m-d H:i:s");
			unset($_POST['Update']);
			$res = $this->km->edit_bast($this->input->post(),$id,$id_spk);
			if($res){
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mengedit bast!</p>');

				redirect(site_url('kontrak/view/'.$id_proc.'/bast'.'#tabNav'));
			}
		}
		$arr = array('' => 'Pilih Salah Satu','bast_tahapan' => 'BAST Tahapan', 'bast_final' => 'BAST Final');

		$data['ttr']	= $arr;
		$data['v_ttr']	= $data['bast_type'];
		$layout['content']= $this->load->view('tab/edit_bast',$data,TRUE);

		$item['header'] = $this->load->view('admin/header',$admin,TRUE);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
		
	}

	public function hapus_bast($id,$id_proc)
	{
		$res = $this->km->hapus_bast($id);
		if ($res) {
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menghapus bast!</p>');

				redirect(site_url('kontrak/view/'.$id_proc.'/bast'.'#tabNav'));
		}
	}

	public function do_upload_($file){
		// echo 'test upload';
			$target_dir = "./lampiran/progress_pengadaan/";
			$new_name 	= "progress_lampiran_".$this->utility->name_generator();
			// $target_file = $target_dir.$new_name+".pdf";
			$target_file = $target_dir.$new_name;
			if (!move_uploaded_file($file["tmp_name"], $target_file)) {
				echo "Sorry, there was an error uploading your file.".$file["error"];
			}
			return $new_name;
	}

	public function do_upload($name = '', $db_name = ''){	
		
		$form = $this->session->userdata('form');
		$file_name = $_FILES[$db_name]['name'] = $db_name.'_'.$this->utility->name_generator($_FILES[$db_name]['name']);
		// echo 'name'; print_r($db_name);
		$config['upload_path'] = './lampiran/'.$db_name.'/';
		// $config['upload_path'] = './lampiran/po_file';
		$config['allowed_types'] = 'pdf|jpeg|jpg|png|gif|doc|docx';
		$config['max_size'] = '500000';
		
		$this->load->library('upload');
		$this->upload->initialize($config);
		
		if ( ! $this->upload->do_upload($db_name)){
			$this->form_validation->set_message('do_upload', $this->upload->display_errors('',''));
			return false;
		}else{
			$_POST[$db_name] = $file_name; 
			
			return true;
		}
	}

	function check_mulai($field,$id=0){
		
		$kontrak = $this->km->get_contract_by_procurement($id);

		$amandemen = $this->km->get_amandemen_by_procurement($id);

		if (!empty($amandemen)) {
			$end_date = strtotime($amandemen['start_contract']);
			$lab = 'amandemen';
		} else {
			$end_date = strtotime($kontrak['start_contract']);
			$lab = 'kontrak';
		}

		$start_date = strtotime($this->input->post('start_date'));

		if($start_date < $end_date){
			$this->form_validation->set_message('check_mulai', 'Tanggal mulai tidak boleh dibawah tanggal '.$lab);
			return false;
		}else{
			return true;
		}
	}

	function check_akhir($field,$id=0){
		$kontrak = $this->km->get_contract_by_procurement($id);

		$amandemen = $this->km->get_amandemen_by_procurement($id);

		if (!empty($amandemen)) {
			$e_date = strtotime($amandemen['end_date']);
			$lab = 'amandemen';
		} else {
			$e_date = strtotime($kontrak['end_contract']);
			$lab = 'kontrak';
		}

		$end_date = strtotime($this->input->post('end_date'));

		if($end_date > $e_date){
			$this->form_validation->set_message('check_akhir', 'Tanggal akhir tidak boleh lebih dari tanggal '.$lab);
			return false;
		}else{
			return true;
		}
	}

	function check_amandemen($field,$id=0){
		$kontrak = $this->km->get_contract_by_procurement($id);

		$contract_date = strtotime($kontrak['end_contract']);

		$end_date = strtotime($this->input->post('start_date'));

		if($end_date > $contract_date){
			$this->form_validation->set_message('check_amandemen', 'Tanggal mulai amandemen tidak boleh lebih dari tanggal akhir kontrak');
			return false;
		}else{
			return true;
		}
	}
}