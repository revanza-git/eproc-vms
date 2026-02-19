<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Auction extends CI_Controller {

	public $id_auction;
	public $tabNav;

	public function __construct(){
		parent::__construct();

		if(!$this->session->userdata('admin')){
			redirect(site_url());
		}

		$this->load->model('auction_model','am');
		
		if ($this->uri->segment(3) === FALSE)
		{
		    $this->id_auction = 0;
		}
		else
		{
		   	$this->id_auction = $this->uri->segment(3);
		}
		$this->tabNav = array(
								'tatacara'		=> array(
										'url' 	=> site_url('auction/ubah/'.$this->id_auction.'/tatacara#tabNav/'),
										'label' => 'Tata Cara'
										),
								'peserta'		=> array(
										'url' 	=> site_url('auction/ubah/'.$this->id_auction.'/peserta#tabNav/'),
										'label'	=>'Peserta'
										),
								'kurs'		=> array(
										'url' 	=> site_url('auction/ubah/'.$this->id_auction.'/kurs#tabNav/'),
										'label'	=>'Kurs'
										),
								'persyaratan'	=> array(
										'url' 	=> site_url('auction/ubah/'.$this->id_auction.'/persyaratan#tabNav/'),
										'label'	=>'Persyaratan'
										),
								'barang'		=> array(
										'url' 	=> site_url('auction/ubah/'.$this->id_auction.'/barang#tabNav/'),
										'label'	=>'Barang'
										),
								'remark'		=> array(
										'url' 	=> site_url('auction/ubah/'.$this->id_auction.'/remark#tabNav/'),
										'label'	=>'Keterangan'
										),
							);
	}

	public function get_field(){
		return array(
			array(
				'label'	=>	'Auction',
				'filter'=>	array(
								array('table'=>'ms_procurement|name' ,'type'=>'text','label'=> 'Nama Paket'),
								array('table'=>'ms_procurement|auction_date' ,'type'=>'date','label'=> 'Tanggal'),
								// array('table'=>'ms_procurement|auction_date' ,'type'=>'single_date','label'=> 'Tanggal'),
								array('table'=>'ms_procurement|work_area' ,'type'=>'text','label'=> 'Lokasi'),
								array('table'=>'ms_procurement|auction_type' ,'type'=>'text','label'=> 'Tipe Auction'),
							)
			),
			
		);
	}
	public function index()
	{	
		$this->load->library('form');
		$this->load->library('datatables');


		$this->load->library('form');
		$search = $this->input->get('q');
		$page 	= '';
		$post 	= $this->input->post();

		$per_page	= 10;
		$sort 		= $this->utility->generateSort(array('ms_procurement.name', 'auction_date', 'work_area'));
		
		$data['auction_list']	= $this->am->get_auction_list($search, $sort, $page, $per_page,TRUE);

		$data['filter_list'] 	= $this->filter->group_filter_post($this->get_field());
		$data['pagination'] 	= $this->utility->generate_page('auction',$sort, $per_page, $this->am->get_auction_list($search, $sort, '','',FALSE));
		$data['sort'] 			= $sort;

		$layout['menu']			= $this->am->get_auction_list();
		$layout['content']		= $this->load->view('auction/content',$data,TRUE);
		$layout['script']		= $this->load->view('auction/content_js',$data,TRUE);
		$item['header'] 		= $this->load->view('auction/header',$this->session->userdata('admin'),TRUE);
		$item['content'] 		= $this->load->view('dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}
	public function langsung()
	{	
		$this->load->library('form');
		$this->load->library('datatables');


		$this->load->library('form');
		$search = $this->input->get('q');
		$page 	= '';
		$post 	= $this->input->post();

		$per_page	= 10;
		$sort 		= $this->utility->generateSort(array('name', 'auction_date', 'work_area'));
		
		$data['auction_list']	= $this->am->get_auction_langsung($search, $sort, $page, $per_page,TRUE);

		$data['filter_list'] 	= $this->filter->group_filter_post($this->get_field());
		$data['pagination'] 	= $this->utility->generate_page('auction',$sort, $per_page, $this->am->get_auction_langsung($search, $sort, '','',FALSE));
		$data['sort'] 			= $sort;

		$layout['menu']			= $this->am->get_auction_langsung();
		$layout['content']		= $this->load->view('auction/content',$data,TRUE);
		$layout['script']		= $this->load->view('auction/content_js',$data,TRUE);
		$item['header'] 		= $this->load->view('auction/header',$this->session->userdata('admin'),TRUE);
		$item['content'] 		= $this->load->view('dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}
	public function selesai()
	{	
		$this->load->library('form');
		$this->load->library('datatables');


		$this->load->library('form');
		$search = $this->input->get('q');
		$page 	= '';
		$post 	= $this->input->post();

		$per_page	= 10;
		$sort 		= $this->utility->generateSort(array('name', 'auction_date', 'work_area'));
		
		$data['auction_list']	= $this->am->get_auction_selesai($search, $sort, $page, $per_page,TRUE);

		$data['filter_list'] 	= $this->filter->group_filter_post($this->get_field());
		$data['pagination'] 	= $this->utility->generate_page('auction',$sort, $per_page, $this->am->get_auction_selesai($search, $sort, '','',FALSE));
		$data['sort'] 			= $sort;
		$data['auction_status'] = 'selesai';
		
		$layout['menu']			= $this->am->get_auction_selesai();
		$layout['content']		= $this->load->view('auction/content',$data,TRUE);
		$layout['script']		= $this->load->view('auction/content_js',$data,TRUE);
		$item['header'] 		= $this->load->view('auction/header',$this->session->userdata('admin'),TRUE);
		$item['content'] 		= $this->load->view('dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}
	

	public function duplikat($id){
		$_POST 	= $this->securities->clean_input($_POST,'save');
		$admin 	= $this->session->userdata('admin');
		if($this->input->post('simpan')){
			$res = $this->am->duplicate_data($this->input->post('total'),$id);
			
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses duplikat data!</p>');
				redirect(site_url('auction'));
			
		}
		

		$layout['content']	= $this->load->view('auction/duplikat',NULL,TRUE);
		$item['header'] 	= $this->load->view('auction/header',$this->session->userdata('admin'),TRUE);
		$item['content'] 	= $this->load->view('dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}


	public function tambah(){
		$_POST 	= $this->securities->clean_input($_POST,'save');
		$admin 	= $this->session->userdata('admin');
		$vld 	= array(
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
				'field'=>'id_pejabat_pengadaan',
				'label'=>'Pejabat Pengadaan',
				'rules'=>'required'
				),			
			array(
				'field'=>'auction_type',
				'label'=>'Tipe Auction',
				'rules'=>'required'
				),			
			array(
				'field'=>'work_area',
				'label'=>'Lokasi',
				'rules'=>'required'
				),
			array(
				'field'=>'room',
				'label'=>'Ruangan',
				'rules'=>'required'
				),
			array(
				'field'=>'auction_date',
				'label'=>'Tanggal',
				'rules'=>'required|backdate'
				),
			array(
				'field'=>'auction_duration',
				'label'=>'Durasi',
				'rules'=>'required'
				),
			array(
				'field'=>'budget_holder',
				'label'=>'Budget Holder',
				'rules'=>'required'
				),
			array(
				'field'=>'budget_spender',
				'label'=>'Budget Spender',
				'rules'=>'required'
				),
			);
		
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

		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			$_POST['entry_stamp'] 	= date("Y-m-d H:i:s");
			$_POST['id_mekanisme'] 	= 1;
			$_POST['id_division']	= $division[$_POST['budget_spender']];
			unset($_POST['Simpan']);

			$res = $this->am->save_data($this->input->post());
			if($res){
				$this->session->set_flashdata('msgSuccesss','<p class="msgSuccess">Sukses menambah data!</p>');
				redirect(site_url('auction/ubah/'.$res.'/tambah_tatacara'));
			}
		}

		$data['pejabat_pengadaan'] 	= $this->am->get_pejabat();
		$data['budget_holder'] 		= $this->am->get_budget_holder();
		$data['budget_spender'] 	= $this->am->get_budget_spender();
		$data['id_mekanisme'] 		= $this->am->get_mekanisme();

		$layout['menu']			= $this->am->get_auction_list();
		$layout['content']	= $this->load->view('auction/tambah',$data,TRUE);
		$item['header'] 	= $this->load->view('auction/header',$this->session->userdata('admin'),TRUE);
		$item['content'] 	= $this->load->view('dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function edit($id){
		$_POST = $this->securities->clean_input($_POST,'save');
		$admin = $this->session->userdata('admin');
		$vld   = array(
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
				'field'=>'id_pejabat_pengadaan',
				'label'=>'Pejabat Pengadaan',
				'rules'=>'required'
				),			
			array(
				'field'=>'auction_type',
				'label'=>'Tipe Auction',
				'rules'=>'required'
				),			
			array(
				'field'=>'work_area',
				'label'=>'Lokasi',
				'rules'=>'required'
				),
			array(
				'field'=>'room',
				'label'=>'Ruangan',
				'rules'=>'required'
				),
			array(
				'field'=>'auction_date',
				'label'=>'Tanggal',
				'rules'=>'required|backdate'
				),
			array(
				'field'=>'auction_duration',
				'label'=>'Durasi',
				'rules'=>'required'
				),
			array(
				'field'=>'budget_holder',
				'label'=>'Budget Holder',
				'rules'=>'required'
				),
			array(
				'field'=>'budget_spender',
				'label'=>'Budget Spender',
				'rules'=>'required'
				),
			);
		
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
		
		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			$_POST['entry_stamp'] = date("Y-m-d H:i:s");
			$_POST['id_division']	= $division[$_POST['budget_spender']];
			unset($_POST['Simpan']);
			unset($_POST['Update']);

			$res = $this->am->edit_data($this->input->post(),$id);
			if($res){
				$this->session->set_flashdata('msgSuccesss','<p class="msgSuccess">Sukses mengubah data!</p>');
				redirect(site_url('auction/ubah/'.$id.'/'));
			}
		}
		$data = $this->am->get_auction($id);
		
		$data['pejabat_pengadaan'] 		= $this->am->get_pejabat();
		$data['budget_holder_list'] 	= $this->am->get_budget_holder();
		$data['budget_spender_list'] 	= $this->am->get_budget_spender();
		$data['id_mekanisme_list'] 		= $this->am->get_mekanisme();
		$data['id'] 					= $id;

		
		$layout['content']	= $this->load->view('edit',$data,TRUE);
		$item['header'] 	= $this->load->view('auction/header',$this->session->userdata('admin'),TRUE);
		$item['content'] 	= $this->load->view('dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}
	public function hapus($id){
		if($this->am->hapus($id,'ms_procurement')){
			
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menghapus data!</p>');
			redirect(site_url('auction'));
		}else{
			$this->session->set_flashdata('msgSuccess','<p class="msgError">Gagal menghapus data!</p>');
			redirect(site_url('auction'));
		}
	}
	public function ubah($id, $page='tatacara'){
		$data 			= $this->am->get_auction($id);
		$data['id'] 	= $id;
		$data['table'] 	= $this->$page($id, $page);

		
		$layout['content']	= $this->load->view('auction/view',$data,TRUE);
		$layout['script']	= $this->load->view('auction/content_js',$data,TRUE);

		$item['header'] 	= $this->load->view('auction/header',$this->session->userdata('admin'),TRUE);
		$item['content'] 	= $this->load->view('dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function autocomplete(){
		// $keyword	= $this->input->post('keyword');
        $data 		= $this->am->search_vendor();        
		echo json_encode($data);
	}

	public function remark($id){
		$data = $this->am->get_auction($id);
		$data['tabNav'] = $this->tabNav;
		$data['id_pengadaan'] = $id;
		if($this->input->post('simpan')){
			$res = $this->am->save_remark($id);
			if($res){
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mengubah keterangan!</p>');

				redirect(site_url('auction/ubah/'.$id.'/remark#contentWrap'));
			}
		}
		$data['sort'] 	= $this->utility->generateSort(array('step_name','supposed','realization'));
		return $this->load->view('tab/remark',$data,TRUE);
	}

	//---------------------
	//		TATA CARA
	//---------------------
	public function tatacara($id,$page){
		$check = $this->am->get_tatacara_peserta($id);

		// echo print_r($check);
		if (!empty($check)){
			$data 				= $this->am->get_tatacara_peserta($id);
			$data['id'] 		= $id;
			$data['sort'] 		= $this->utility->generateSort(array('metode_auction', 'metode_penawaran'));
			$data['tabNav'] 	= $this->tabNav;
			$data['id'] 		= $id;
			$per_page			= 10;
			$data['pagination'] = $this->utility->generate_page('auction/ubah/'.$id.'/'.$page,$data['sort'], $per_page,  $this->am->get_procurement_tatacara($id,'', $data['sort'], '','',FALSE));
			$data['list'] 		= $this->am->get_procurement_tatacara($id,'', $data['sort'], '','',FALSE);

			if($this->input->post('edit')){
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mengubah data!</p>');
				redirect(site_url('auction/ubah/'.$id.'/edit_tatacara/#contentWrap'));
			}
			
			return $this->load->view('tab/tatacara',$data,TRUE);
		}else{
			$this->session->keep_flashdata('msgSuccess');
			 redirect(site_url('auction/ubah/'.$id.'/tambah_tatacara'));
		}
	}

	public function tambah_tatacara($id,$page){
		$form 	= ($this->session->userdata('form'))?$this->session->userdata('form'):array();
		$fill 	= $this->securities->clean_input($_POST,'save');
		$item 	= $vld = $save_data = array();
		$admin 	= $this->session->userdata('admin');
		$vld 	= 	array(
					array(
						'field'=>'metode_auction',
						'label'=>'Metode Auction',
						'rules'=>'required'
					),
					array(
						'field'=>'metode_penawaran',
						'label'=>'Metode Penawaran',
						'rules'=>'required'
					),
				);
		$this->form_validation->set_rules($vld);

		if($this->input->post('simpan')){
			if($this->form_validation->run()==TRUE){
				// print_r($this->input->post('simpan'));
				$form['id_procurement'] 	= $id;
				$form['metode_auction'] 	= $this->input->post('metode_auction');
				$form['metode_penawaran'] 	= $this->input->post('metode_penawaran');
				$form['entry_stamp'] 		= date('Y-m-d H:i:s');
				if($form['metode_penawaran'] =='lump_sum') $this->set_barang($id,true);
				
				// $this->session->set_userdata('form',array_merge($form,$this->input->post()));
				
				$result = $this->am->save_tatacara($form);
				if($result){
					$this->set_syarat($id);
					$this->session->unset_userdata('form');
					$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menambah Tata Cara </p>');
					redirect(site_url('auction/ubah/'.$id.'#contentWrap'));
				}
			}
		}

		$data['sort'] 		= $this->utility->generateSort(array('metode_auction', 'metode_penawaran'));
		$data['tabNav'] 	= $this->tabNav;
		$data['id'] 		= $id;
		$per_page			= 10;
		$data['pagination'] = $this->utility->generate_page('auction/ubah/'.$id.'/'.$page,$data['sort'], $per_page,  $this->am->get_procurement_tatacara($id,'', $data['sort'], '','',FALSE));
		$data['list'] 		= $this->am->get_procurement_tatacara($id,'', $data['sort'], '','',FALSE);
		return $this->load->view('tab/tambah_tatacara',$data,TRUE);
	}



	public function edit_tatacara($id,$page){
		$form 	= ($this->session->userdata('form'))?$this->session->userdata('form'):array();
		$fill 	= $this->securities->clean_input($_POST,'save');
		$item 	= $vld = $save_data = array();
		$admin 	= $this->session->userdata('admin');
		$vld 	= 	array(
						array(
							'field'=>'metode_auction',
							'label'=>'Metode Auction',
							'rules'=>'required'
						),
						array(
							'field'=>'metode_penawaran',
							'label'=>'Metode Penawaran',
							'rules'=>'required'
						),
					);
		$this->form_validation->set_rules($vld);

		if($this->input->post('update')){
			if($this->form_validation->run()==TRUE){
				unset($_POST['update']);
				unset($form['update']);
				$form['id_procurement'] 	= $id;
				$form['metode_auction'] 	= $this->input->post('metode_auction');
				$form['metode_penawaran'] 	= $this->input->post('metode_penawaran');
				$form['edit_stamp'] 		= date('Y-m-d H:i:s');

				
				
				$result = $this->am->edit_tatacara($id, $form);
				if($result){
					$this->session->unset_userdata('form');
					$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses Mengubah Tata Cara </p>');
					redirect(site_url('auction/ubah/'.$id.'/#contentWrap'));
				}
			}
		}
		$data 				= $this->am->get_tatacara_peserta($id);

		$data['sort'] 		= $this->utility->generateSort(array('metode_auction', 'metode_penawaran'));
		$data['tabNav'] 	= $this->tabNav;
		$data['id'] 		= $id;
		return $this->load->view('tab/edit_tatacara',$data,TRUE);
	}

	function set_barang($id_lelang = '',$is_lump = FALSE){
		$master = $this->am->get_auction($id_lelang);
		
		$param = array(
			'id_procurement' => $id_lelang,
			'nama_barang' => $master['name'],
			'id_kurs' => '',
			'nilai_hps' => '',
			// 'use_tax' => '',
			// 'volume' => '',
			'entry_stamp' => date("Y-m-d H:i:s")
		);
		if($is_lump){
			$param['volume'] = TRUE;
		}
		$this->am->save_barang($param);	
	}

	//---------------------
	//		BARANG
	//---------------------
	public function barang($id,$page){

		$data['tatacara'] = $this->am->get_tatacara_peserta($id);

		$data['sort'] 		= $this->utility->generateSort(array('nama_barang', 'volume', 'ms_procurement_barang.id_kurs', 'nilai_hps'));

		$data['tabNav'] 	= $this->tabNav;
		$data['id'] 		= $id;
		$per_page			= 10;
		$data['list'] = $this->am->get_procurement_barang($id,'', $data['sort'], '','',FALSE);
		// $data['pagination'] = $this->utility->generate_page('auction/ubah/'.$id.'/'.$page,$data['sort'], $per_page,  $this->am->get_procurement_barang($id,'', $data['sort'], '','',FALSE));
		
		return $this->load->view('tab/barang',$data,TRUE);
	}

	public function tambah_barang($id){
		$form 	= ($this->session->userdata('form'))?$this->session->userdata('form'):array();
		$fill 	= $this->securities->clean_input($_POST,'save');
		$item 	= $vld = $save_data = array();
		$admin 	= $this->session->userdata('admin');
		
		$vld 	= 	array(
						array(
							'field'=>'nama_barang',
							'label'=>'Nama Barang',
							'rules'=>'required'
						),
						array(
							'field'=>'id_kurs',
							'label'=>'Mata Uang',
							'rules'=>'required'
						),
						array(
							'field'=>'nilai_hps',
							'label'=>'Nilai HPS',
							'rules'=>'required'
						),
						array(
							'field'=>'volume',
							'label'=>'Volume',
							'rules'=>'required'
						),
						array(
							'field'=>'id_material',
							'rules'=>''
						),array(
							'field'=>'is_catalogue',
							'rules'=>''
						),
					);
		$this->form_validation->set_rules($vld);

		if($this->input->post('simpan')){

			if($this->form_validation->run()==TRUE){

				if ($this->input->post('is_catalogue')){

					$katalog['nama']		= $this->input->post('nama_barang');
					$katalog['entry_stamp'] = date('Y-m-d H:i:s');
					$katalog['category'] 	= $this->input->post('category');
					$katalog['id_kurs'] 		= $this->input->post('id_kurs');
					$id_material = $this->am->save_barang_catalogue($katalog);
				}

				$form['id_procurement'] = $id;
				$form['nama_barang'] 	= $this->input->post('nama_barang');
				$form['nilai_hps'] 		= preg_replace("/[,]/", "", $this->input->post('nilai_hps'));
				$form['id_kurs'] 		= $this->input->post('id_kurs');
				$form['id_material'] 	= ($id_material) ? $id_material : $this->input->post('id_material');
				$form['is_catalogue'] 	= ($this->input->post('id_material')) ? 1 : $this->input->post('is_catalogue');
				$form['category'] 	= $this->input->post('category');
				$form['volume'] 	= $this->input->post('volume');
				$form['entry_stamp'] 	= date('Y-m-d H:i:s');

				$result = $this->am->save_barang($form);
				if($result){

					$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menambah Barang!</p>');
					redirect(site_url('auction/ubah/'.$id.'/barang#contentWrap'));

				}


				
			}
		}


		$kurs_list = $this->am->get_procurement_kurs($id,'', NULL, '','',FALSE);
		$where_in = array();
		foreach ($kurs_list as $key => $value) {
			$where_in[] = $value['id_kurs'];
		}
		$data['id'] = $id;
		
		$data['id_kurs'] 	= $this->am->get_kurs_barang($where_in);
		
		$layout['content']	= $this->load->view('tab/tambah_barang',$data,TRUE);
		$layout['script']	= $this->load->view('tab/tambah_barang_js',$data,TRUE);
		$item['header'] 	= $this->load->view('header',$admin,TRUE);
		$item['content'] 	= $this->load->view('dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function edit_barang($id,$id_procurement){
		$form 	= ($this->session->userdata('form'))?$this->session->userdata('form'):array();
		$fill 	= $this->securities->clean_input($_POST,'save');
		$item 	= $vld = $save_data = array();
		$admin 	= $this->session->userdata('admin');

		$data 	= $this->am->get_barang_data($id);

		$vld 	= 	array(
						array(
							'field'=>'id_kurs',
							'label'=>'Mata Uang',
							'rules'=>'required'
						),
						array(
							'field'=>'nilai_hps',
							'label'=>'Nilai HPS',
							'rules'=>'required'
						),
						array(
							'field'=>'volume',
							'label'=>'Volume',
							'rules'=>'required'
						),
					);
		$this->form_validation->set_rules($vld);

		if($this->input->post('simpan')){

			if($this->form_validation->run()==TRUE){
				
				$form['nilai_hps'] 		= preg_replace("/[,]/", "", $this->input->post('nilai_hps'));
				$form['id_kurs'] 		= $this->input->post('id_kurs');
				$form['entry_stamp'] 	= date('Y-m-d H:i:s');
				$form['volume'] 		= $this->input->post('volume');
				
				$result = $this->am->edit_barang($form,$id);
				if($result){
					$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menambah Barang / Jasa!</p>');
					redirect(site_url('auction/ubah/'.$id_procurement.'/barang#contentWrap'));
				}
			}
		}

		$kurs_list = $this->am->get_procurement_kurs($id_procurement,'', NULL, '','',FALSE);
		$where_in = array();
		foreach ($kurs_list as $key => $value) {
			$where_in[] = $value['id_kurs'];
		}
		
		$data['kurs'] 	= $this->am->get_kurs_barang($where_in);
		
		$layout['content']	= $this->load->view('tab/edit_barang',$data,TRUE);
		$item['header'] 	= $this->load->view('header',$admin,TRUE);
		$item['content'] 	= $this->load->view('dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function hapus_barang($id,$id_procurement){
		if($this->am->hapus($id,'ms_procurement_barang')){
			
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menghapus data!</p>');
			redirect(site_url('auction/ubah/'.$id_procurement.'/barang#contentWrap'));
		}else{
			$this->session->set_flashdata('msgSuccess','<p class="msgError">Gagal menghapus data!</p>');
			redirect(site_url('auction/ubah/'.$id_procurement.'/barang#contentWrap'));
		}
	}

	//---------------------
	//		PESERTA
	//---------------------
	public function peserta($id,$page){
		$data['sort'] 		= $this->utility->generateSort(array('name'));
		$data['tabNav'] 	= $this->tabNav;
		$data['id'] 		= $id;
		$per_page			= 10;
		$data['filter_list'] = $this->filter->group_filter_post($this->filter->get_field());
		$data['pagination'] = $this->utility->generate_page('auction/view/'.$id.'/'.$page,$data['sort'], $per_page,  $this->am->get_procurement_peserta($id,'', $data['sort'], '','',FALSE));
		$data['list'] = $this->am->get_procurement_peserta($id,'', $data['sort'], '','',FALSE);

		return $this->load->view('tab/peserta',$data,TRUE);
	}

	public function tambah_peserta($id){
		$admin = $this->session->userdata('admin');
		$page = '';
		$this->load->library('form');

		$per_page=10;
		$sort = $this->utility->generateSort(array('name','point','npwp_code','nppkp_code','id'));
		
		$data['vendor_list']	= $this->am->get_available_peserta($id, $search, $sort, $page, $per_page,TRUE);
		$array = array(
				'label'	=>	'Jenis Penyedia Barang/Jasa',
				'filter'=>	array(
								array('table'=>'ms_vendor|is_vms|get_is_vms' 	,'type'=>'dropdown','label'=> 'Jenis Penyedia Barang/Jasa'),
							)
						);
		$data['filter_list'] 	= $this->filter->group_filter_post($this->filter->get_field($array));
		$data['pagination'] 	= $this->utility->generate_page('auction/tambah_peserta/'.$id,$sort, $per_page, $this->am->get_available_peserta($id, $sort, '', '',TRUE));
		$data['sort'] 			= $sort;

		
		$data['id'] = $id;
		$layout['content']= $this->load->view('tab/tambah_peserta',$data,TRUE);
		$layout['script']= $this->load->view('content_js',$data,TRUE);

		$item['header'] = $this->load->view('header',$admin,TRUE);
		$item['content'] = $this->load->view('dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function add_peserta($id,$id_vendor){
		
		$fill 	= $this->securities->clean_input($_POST,'save');
		
			$form['id_proc'] 		= $id;
			$form['id_vendor'] 		= $id_vendor;
			$form['entry_stamp'] 	= date('Y-m-d H:i:s');
			
			$result = $this->am->save_peserta($form);
			if($result){

				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menambah peserta!</p>');
				redirect(site_url('auction/ubah/'.$id.'/peserta#contentWrap'));
			}
		
	}

	public function hapus_peserta($id,$id_procurement){
		if($this->am->hapus($id,'ms_procurement_peserta')){
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menghapus data!</p>');
			redirect(site_url('auction/ubah/'.$id_procurement.'/peserta#contentWrap'));
		}else{
			$this->session->set_flashdata('msgSuccess','<p class="msgError">Gagal menghapus data!</p>');
			redirect(site_url('auction/ubah/'.$id_procurement.'/peserta#contentWrap'));
		}
	}
	//---------------------
	//		KURS
	//---------------------
	public function kurs($id,$page){
		$data['sort'] 		= $this->utility->generateSort(array('name'));
		$data['tabNav'] 	= $this->tabNav;
		$data['id'] 		= $id;
		$per_page			= 10;
		$data['pagination'] = $this->utility->generate_page('auction/view/'.$id.'/'.$page,$data['sort'], $per_page,  $this->am->get_procurement_peserta($id,'', $data['sort'], '','',FALSE));
		$data['list'] = $this->am->get_procurement_kurs($id,'', $data['sort'], '','',FALSE);
		// echo print_r($data['list']);
		return $this->load->view('tab/kurs',$data,TRUE);
	}

	public function tambah_kurs($id){
		$form 	= ($this->session->userdata('form'))?$this->session->userdata('form'):array();
		$fill 	= $this->securities->clean_input($_POST,'save');
		$item 	= $vld = $save_data = array();
		$admin 	= $this->session->userdata('admin');
		$vld 	= 	array(
						array(
							'field'=>'id_kurs',
							'label'=>'Nama kurs',
							'rules'=>'required'
						),
					);
		if($this->input->post('id_kurs')!='1'){
			$vld[] = array(
							'field'=>'rate',
							'label'=>'Rate',
							'rules'=>'required'
						);
		}
		$this->form_validation->set_rules($vld);

		if($this->input->post('simpan')){

			if($this->form_validation->run()==TRUE){
				$form['id_procurement'] = $id;
				$form['id_kurs'] 		= $this->input->post('id_kurs');
				$form['rate'] 			= ($this->input->post('id_kurs')=='1') ? 1:preg_replace("/[,]/", "", $this->input->post('rate'));
				$form['entry_stamp'] 	= date('Y-m-d H:i:s');
				
				$result = $this->am->save_kurs($form);
				if($result){
					$this->session->unset_userdata('form');
					$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menambah kurs!</p>');
					redirect(site_url('auction/ubah/'.$id.'/kurs#contentWrap'));
				}
			}
		}
		$kurs_list = $this->am->get_procurement_kurs($id,'', NULL, '','',FALSE);
		$where_in = array();
		foreach ($kurs_list as $key => $value) {
			$where_in[] = $value['id_kurs'];
		}
		$data['kurs'] 	= $this->am->get_kurs_list($where_in);
		
		$layout['menu']			= $this->am->get_auction_list();
		$layout['content']	= $this->load->view('tab/tambah_kurs',$data,TRUE);
		$layout['script']	= $this->load->view('content_js',$data,TRUE);
		$item['header'] 	= $this->load->view('header',$admin,TRUE);
		$item['content'] 	= $this->load->view('dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function hapus_kurs($id,$id_procurement){
		if($this->am->hapus($id,'ms_procurement_kurs')){
			
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menghapus data!</p>');
			redirect(site_url('auction/ubah/'.$id_procurement.'/kurs#contentWrap'));
		}else{
			$this->session->set_flashdata('msgSuccess','<p class="msgError">Gagal menghapus data!</p>');
			redirect(site_url('auction/ubah/'.$id_procurement.'/kurs#contentWrap'));
		}
	}



	//---------------------
	//		PERSYARATAN
	//---------------------
	public function persyaratan($id,$page){
		$check = $this->am->get_persyaratan($id);

		// echo print_r($check);
		if (!empty($check)){
			$form 	= ($this->session->userdata('form'))?$this->session->userdata('form'):array();
			$fill 	= $this->securities->clean_input($_POST,'save');
			$item 	= $vld = $save_data = array();
			$admin 	= $this->session->userdata('admin');
			$vld 	= 	array(
							array(
								'field'=>'description',
								'label'=>'Description',
								'rules'=>'required'
							),
						);
				$this->form_validation->set_rules($vld);

			if($this->input->post('update')){
				if($this->form_validation->run()==TRUE){
					$form['id_proc'] 		= $id;
					$form['description'] 	= $_POST['description'];
					$form['edit_stamp'] 	= date('Y-m-d H:i:s');
					unset($_POST['update']);

					$result = $this->am->edit_persyaratan($id, $form);;
					if($result){
						$this->session->unset_userdata('form');
						$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menyimpan persyaratan!</p>');
						redirect(site_url('auction/ubah/'.$id.'/persyaratan#contentWrap'));
					}
				}
			}	
			$data 				= $this->am->get_procurement_persyaratan($id);
			$data['tabNav'] 	= $this->tabNav;
			$data['id'] 		= $id;
			return $this->load->view('tab/persyaratan',$data,TRUE);
		}else{
			 redirect(site_url('auction/ubah/'.$id.'/tambah_persyaratan/'));
		}

		$data['tabNav'] 	= $this->tabNav;
		$data['id'] 		= $id;
		return $this->load->view('tab/persyaratan',$data,TRUE);
	}

	public function tambah_persyaratan($id,$page){
		$check = $this->am->get_persyaratan($id);
		// echo print_r($check);
		if (empty($check)){
			$form 	= ($this->session->userdata('form'))?$this->session->userdata('form'):array();
			$fill 	= $this->securities->clean_input($_POST,'save');
			$item 	= $vld = $save_data = array();
			$admin 	= $this->session->userdata('admin');
			$vld 	= 	array(
						array(
							'field'=>'description',
							'label'=>'Description',
							'rules'=>'required'
						),
					);
			$this->form_validation->set_rules($vld);

			if($this->input->post('simpan')){
				if($this->form_validation->run()==TRUE){
					$_POST['id_proc'] 		= $id;
					$_POST['description'] 	= $this->input->post('description');
					$_POST['entry_stamp'] 	= date('Y-m-d H:i:s');

					$result = $this->am->save_persyaratan($this->input->post());
					if($result){
						$this->session->unset_userdata('form');
						$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menambah persyaratan!</p>');
						redirect(site_url('auction/ubah/'.$id.'/persyaratan#contentWrap'));
					}
				}
			}
				
			$data['tabNav'] 	= $this->tabNav;
			$data['id'] 		= $id;
			return $this->load->view('tab/tambah_persyaratan',$data,TRUE);
		}else{
			redirect(site_url('auction/ubah/'.$id.'/persyaratan/'));
		}
	}
		
	function set_syarat($id_lelang = '', $is_edit = false){
		$master = $this->am->get_auction($id_lelang);

		if($master['auction_type'] == "reverse_auction"){ $limit = "minimum"; $indicator = "rendah"; $reverse = "tinggi"; $winner = "dibawah";}
		else if($master['auction_type'] == "forward_auction"){ $limit = "maximum"; $indicator = "tinggi"; $reverse = "rendah"; $winner = "diatas";}
		
		$value = '
			<ol>
				<li>Harga penawaran sudah termasuk pajak-pajak.</li>
				<li>Durasi auction selama '.$master['auction_duration'].' menit, tanpa ada penambahan waktu.</li>
				<li>';
				if($master['metode_auction'] == "posisi")
					$value .= 'Metode auction menggunakan metode posisi/rangking, dimana para peserta e-auction hanya
					mengetahui posisi/rangking dari penawaran harga yang telah dimasukkan dibandingkan dengan
					penawaran harga peserta e-auction lainnya.';
					
				if($master['metode_auction'] == "indikator")
					$value .= 'Metode auction menggunakan metode indikator, dimana para peserta e-auction akan
					diberikan indikator terhadap penawaran harga yang telah dimasukan, dibandingkan dengan penawaran harga 
					peserta e-auction lainnya.';
		
		$value .= '</li>
				<li>Tidak ada batas harga penawaran '.$limit.' yang dapat dimasukkan.</li>';
		if($master['auction_type'] == "reverse_auction"){ 
			$value .= '<li>Harga penawaran dibawah kewajaran harga tidak secara otomatis menjadi pemenang pengadaan. Penawaran akan dievaluasi lebih lanjut oleh Fungsi Pengadaan PT. Nusantara Regas</li>';
		}
		$value .= '<li>Harga penawaran yang dimasukkan tidak boleh sama atau lebih '.$reverse.' dari harga penawaran yang telah dimasukkan sebelumnya.</li>
				<li>';
		if($master['metode_auction'] == "posisi")
			$value .= 'Apabila terdapat penawaran harga yang sama, maka posisi/rangking yang lebih tinggi akan diberikan kepada penawar harga yang masuk terlebih dahulu'; 
		if($master['metode_auction'] == "indikator")
			$value .= 'Apabila terdapat penawaran harga yang sama, maka indikator lambang medali akan diberikan kepada penawar harga yang masuk terlebih dahulu'; 
		
		$value .= '</li>
				<li>Selama auction berlangsung, peserta tidak diperkenankan menggunakan tombol Back (backspace) dan Refresh (F5). </li>
				<li>Selama auction berlangsung, para peserta dilarang saling berkomunikasi dan dilarang menggunakan alat komunikasi apapun.</li>
				<li>Seluruh peserta auction wajib menjaga ketertiban.</li>
			</ol>';			 
				
		
			$param = array(
				'description'=>$value,
				'id_proc'=>$id_lelang,
				'entry_stamp'=>date("Y-m-d H:i:s")	
			);
		
		if($is_edit)
			$this->am->edit_persyaratan($param);
		else
			$this->am->save_persyaratan($param);
	}
}