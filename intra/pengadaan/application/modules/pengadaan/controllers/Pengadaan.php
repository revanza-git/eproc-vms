<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pengadaan extends CI_Controller {

	public $id_pengadaan;
	public $tabNav;
	public $progressNav;

	public function __construct(){
		parent::__construct();

		if(!$this->session->userdata('admin')){
			redirect(site_url());
		}

		$this->load->model('pengadaan_model','pm');
		$this->load->model('izin/izin_model','im');
		
		if ($this->uri->segment(3) === FALSE)
		{
		    $this->id_pengadaan = 0;
		}
		else
		{
		   	$this->id_pengadaan = $this->uri->segment(3);
		}
		$array = array(
								'remark'=>	array(
										'url' 	=> site_url('pengadaan/view/'.$this->id_pengadaan.'/remark#tabNav'),
										'label' => 'Keterangan'
										),

								'bsb'=>	array(
										'url' 	=> site_url('pengadaan/view/'.$this->id_pengadaan.'/bsb#tabNav'),
										'label' => 'Bidang / Sub-Bidang Pekerjaan'
										),
								'peserta'=>array(
										'url' 	=> site_url('pengadaan/view/'.$this->id_pengadaan.'/peserta#tabNav'),
										'label'	=>'Peserta Yang Diundang'
										),
								'penawaran'=>array(
										'url' 	=> site_url('pengadaan/view/'.$this->id_pengadaan.'/penawaran#tabNav'),
										'label'	=>'Peserta Yang Melakukan Penawaran'
										),
								'negosiasi'=>array(
										'url' 	=> site_url('pengadaan/view/'.$this->id_pengadaan.'/negosiasi#tabNav'),
										'label'	=>'Negosiasi'
										),
								'progress_pengadaan'=>array(
										'url' 	=> site_url('pengadaan/view/'.$this->id_pengadaan.'/progress_pengadaan#tabNav'),
										'label'	=>'Progress Paket Pengadaan'
										),
								'pemenang'=>array(
										'url' 	=> site_url('pengadaan/view/'.$this->id_pengadaan.'/pemenang#tabNav'),
										'label'	=>'Pemenang Pengadaan'
										),
								// 'kontrak'=>array(
								// 		'url' 	=> site_url('pengadaan/view/'.$this->id_pengadaan.'/kontrak#tabNav'),
								// 		'label'	=>'Penandatanganan Kontrak'
								// 		),
								// 'progress_pengerjaan'=>array(
								// 		'url' 	=> site_url('pengadaan/view/'.$this->id_pengadaan.'/progress_pengerjaan#tabNav'),
								// 		'label'	=>'Progress Paket Pekerjaan'
								// ),
								'barang'=>array(
										'url' 	=> site_url('pengadaan/view/'.$this->id_pengadaan.'/barang#tabNav'),
										'label'	=>'Katalog'
										)
							);
		if($this->session->userdata('admin')['id_role']==9){
			$array = array(
								'progress_pengadaan'=>array(
										'url' 	=> site_url('pengadaan/view/'.$this->id_pengadaan.'/progress_pengadaan#tabNav'),
										'label'	=>'Progress Paket Pengadaan'
										),
								
								// 'progress_pengerjaan'=>array(
								// 		'url' 	=> site_url('pengadaan/view/'.$this->id_pengadaan.'/progress_pengerjaan#tabNav'),
								// 		'label'	=>'Progress Paket Pengerjaan'
								// )
							);
		}
		$this->tabNav = $array;
		
		// Initialize progressNav as empty array - can be populated as needed
		$this->progressNav = array();
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
								array('table'=>'ms_iu_bsb|name','type'=>'text','label'=> 'Jenis Pengadaan')
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


	public function export_excel($title="Data Pengadaan", $data){
		$data = $this->pm->get_pengadaan_list($search, $sort, $page, $per_page,TRUE);
		$table = "<table border=1>";

			$table .= "<tr>";
			$table .= "<td  style='background: #f6e58d;'>No</td>";
			$table .= "<td  style='background: #f6e58d;'>Nama</td>";
			$table .= "<td  style='background: #f6e58d;'>Nilai Kontrak</td>";
			$table .= "<td  style='background: #f6e58d;'>Nilai </td>";
			$table .= "<td  style='background: #f6e58d;'>Budget Spender</td>";
			$table .= "<td  style='background: #f6e58d;'>Budget Holder</td>";
			$table .= "<td  style='background: #f6e58d;'>Tahun</td>";
			$table .= "<td  style='background: #f6e58d;'>Pejabat</td>";
			$table .= "<td  style='background: #f6e58d;'>Mekanisme</td>";
			$table .= "</tr>";

		foreach ($data as $key => $value) {
			# code...

			$no = $key + 1;
			$table .= "<tr>";
			$table .= "<td>".$no."</td>";
			$table .= "<td>".$value['name']."</td>";
			$table .= "<td>".$value['idr_value']."</td>";
			$table .= "<td>".$value['kurs_value']."</td>";
			$table .= "<td>".$value['budget_spender_name']."</td>";
			$table .= "<td>".$value['budget_holder_name']."</td>";
			$table .= "<td>".$value['budget_year']."</td>";
			$table .= "<td>".$value['pejabat_pengadaan_name']."</td>";
			$table .= "<td>".$value['mekanisme_name']."</td>";
			$table .= "</tr>";
		}
		$table .= "</table>";
		header('Content-type: application/ms-excel');

    	header('Content-Disposition: attachment; filename='.$title.'.xls');



		echo $table;
	}

	public function index()
	{
		// print_r($this->session->userdata('admin'));die;
		$this->load->library('form');
		$this->load->library('datatables');

		$search = $this->input->get('q');
		$page = '';
		$post = $this->input->post();

		$per_page=10;

		$sort = $this->utility->generateSort(array('ms_procurement.name', 'pemenang', 'proc_date','id','contract_date','metode'));
		
		$data['admin'] = $this->session->userdata('admin');
		$data['pengadaan_list']=$this->pm->get_pengadaan_list($search, $sort, $page, $per_page,TRUE);
		$data['pengadaan_divisi']=$this->pm->get_pengadaan_division($search, $sort, $page, $per_page,TRUE);

		$data['filter_list'] = $this->filter->group_filter_post($this->get_field_in());

		$data['pagination'] = $this->utility->generate_page('pengadaan',$sort, $per_page, $this->pm->get_pengadaan_list($search, $sort, '','',FALSE));
		$data['sort'] = $sort;

		$layout['content']= $this->load->view('pengadaan/content',$data,TRUE);
		$layout['script']= $this->load->view('pengadaan/content_js',$data,TRUE);
		$item['header'] = $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function tambah()
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
				'rules'=>''
				),
			array(
				'field'=>'evaluation_method_desc',
				'label'=>'Metode Evaluasi Scoring',
				'rules'=>''
				),
			array(
				'field'=>'tipe_pengadaan',
				'label'=>'Pilih Jenis Pengadaan',
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
			$_POST['idr_value'] 	= preg_replace("/[,]/", "", $this->input->post('idr_value'));
			$_POST['kurs_value'] 	= preg_replace("/[,]/", "", $this->input->post('kurs_value'));
			$_POST['id_division']	= $division[$_POST['budget_spender']];
			unset($_POST['Simpan']);

			$res = $this->pm->save_data($this->input->post());
			if($res){
				$this->session->set_flashdata('msgSuccesss','<p class="msgSuccess">Sukses menambah data!</p>');

				redirect(site_url('pengadaan/view/'.$res));
			}
		}
		$data['pejabat_pengadaan'] = $this->pm->get_pejabat();
		$data['budget_holder'] = $this->pm->get_budget_holder();
		$data['budget_spender'] = $this->pm->get_budget_spender();
		$data['id_mekanisme'] = $this->pm->get_mekanisme();

		$layout['content']= $this->load->view('tambah',$data,TRUE);
		$layout['script']= $this->load->view('pengadaan/tambah_js',$data,TRUE);
		$item['header'] = $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function import_pengadaan()
	{
		if ($this->pm->import_pengadaan()) {
			redirect('pengadaan');
		}
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
			$_POST['edit_stamp'] 	= date("Y-m-d H:i:s");
			$_POST['idr_value'] 	= preg_replace("/[,]/", "", $this->input->post('idr_value'));
			$_POST['kurs_value'] 	= preg_replace("/[,]/", "", $this->input->post('kurs_value'));
			$_POST['id_division']	= $division[$_POST['budget_spender']];
			unset($_POST['Simpan']);
			$res = $this->pm->edit_data($this->input->post(),$id);
			if($res){
				$this->session->set_flashdata('msgSuccesss','<p class="msgSuccess">Sukses mengubah data!</p>');

				redirect(site_url('pengadaan/view/'.$id.''));
			}
		}
		$data = $this->pm->get_pengadaan($id);

		$arr = array('jasa_konstruksi' => 'Jasa Konstruksi', 'jasa_konsultasi' => 'Jasa Konsultasi', 'jasa_lainnya' => 'Jasa Lainnya','jasa_konsultan_non_konstruksi' => 'Jasa Konsultan non-Konstruksi', 'jasa_konsultan_konstruksi' => 'Jasa Konsultan Perencana/Pengawas Konstruksi');

		$data['ttr']	= $arr;
		$data['v_ttr']	= $data['tipe_transaksi'];
		$penilaian = array(''=>'Pilih Tipe Scoring','scoring'=>'Scoring','non_scoring'=>'Non-Scoring');
		$penilaian_ = array(
						''=>'Pilih Tipe Scoring',
						'kualitas_terbaik'=>'Kualitas/Teknik Terbaik',
						'kualitas'=>'Kualitas/Teknik dan Harga',
						'harga'=>'Harga Terendah');

		$data['pejabat_pengadaan'] = $this->pm->get_pejabat();
		$data['budget_holder_list'] = $this->pm->get_budget_holder();
		$data['budget_spender_list'] = $this->pm->get_budget_spender();
		$data['id_mekanisme_list'] = $this->pm->get_mekanisme();
		$data['evaluation_method_list'] = $penilaian;
		$data['evaluation_method_desc_list'] = $penilaian_;
		
		$data['id'] = $id;
		$layout['content']= $this->load->view('edit',$data,TRUE);
		$item['header'] = $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function view($id,$page='bsb',$opt = null){
		$this->session->set_userdata('summary',$page);
		$data = $this->pm->get_pengadaan($id);
		// print_r($data);
		$data['id'] = $id;
		$data['table'] = $this->$page($id,$page,$opt);
		
		$layout['content']= $this->load->view('pengadaan/view',$data,TRUE);
		$layout['script']= $this->load->view('pengadaan/content_js',$data,TRUE);

		$item['header'] = $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function bsb($id,$page){
		if($this->session->userdata('admin')['id_role']!=3 && $this->session->userdata('admin')['id_role']!=10){ 
			redirect('pengadaan/view/'.$id.'/progress_pengadaan');
		}
		$data['sort'] = $this->utility->generateSort(array('bidang_name', 'sub_bidang_name'));
		$data['tabNav'] = $this->tabNav;
		$data['id'] = $id;
		$per_page=10;
		$data['pagination'] = $this->utility->generate_page('pengadaan/view/'.$id.'/'.$page,$data['sort'], $per_page,  $this->pm->get_procurement_bsb($id,'', $data['sort'], '','',FALSE));
		$data['list'] = $this->pm->get_procurement_bsb($id,'', $data['sort'], '','',FALSE);
		return $this->load->view('tab/bsb',$data,TRUE);
	}

	public function barang($id,$page){
		if($this->session->userdata('admin')['id_role']!=3 && $this->session->userdata('admin')['id_role']!=10){ 
			redirect('pengadaan/view/'.$id.'/progress_pengerjaan');
		}
		// $data['data_pengadaan'] = $this->pm->get_pengadaan($id);
		$data['sort'] = $this->utility->generateSort(array('nama_barang', 'id_kurs', 'nilai_hps'));
		$data['tabNav'] = $this->tabNav;
		$data['id'] = $id;
		$per_page=10;
		$data['pagination'] = $this->utility->generate_page('pengadaan/view/'.$id.'/'.$page,$data['sort'], $per_page,  $this->pm->get_procurement_barang($id,'', $data['sort'], '','',FALSE));
		$data['list'] = $this->pm->get_procurement_barang($id,'', $data['sort'], '','',FALSE);
		return $this->load->view('tab/barang',$data,TRUE);
	}


	public function hapus_barang($id,$id_proc){
		if($this->pm->hapus($id,'ms_procurement_barang')){
			
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menghapus data!</p>');
			redirect(site_url('pengadaan/view/'.$id_proc.'/barang'.'#tabNav'));
		}else{
			$this->session->set_flashdata('msgSuccess','<p class="msgError">Gagal menghapus data!</p>');
			redirect(site_url('pengadaan/view/'.$id_proc.'/barang'.'#tabNav'));
		}
	}

	public function hapus_pengadaan($id){
		if($this->pm->hapus_pengadaan($id)){
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menghapus data!</p>');
			redirect(site_url('pengadaan#deleted'.$id));
		}else{
			$this->session->set_flashdata('msgSuccess','<p class="msgError">Gagal menghapus data!</p>');
			redirect(site_url('pengadaan#deleted'.$id));
		}
	}
	public function klasifikasi($id){
		$admin = $this->session->userdata('admin');
		// print_r($admin);die;

		$form = ($this->session->userdata('form'))?$this->session->userdata('form'):array();
		
		$fill = $this->securities->clean_input($_POST,'save');
		// $item = $vld = $save_data = array();
		$user = $this->session->userdata('user');
		$layout['get_dpt_type'] = $this->im->get_dpt_type();
		$vld = 	array(
					array(
						'field'=>'id_dpt_type',
						'label'=>'Jenis',
						'rules'=>'required'
					)
				);
		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			unset($_POST['next']);
			$this->session->set_userdata('form',array_merge($form,$this->input->post()));
			redirect('pengadaan/tambah_bidang/'.$id.'#tabNav');
		}
		$layout['content']= $this->load->view('tab/klasifikasi',$layout,TRUE);

		$item['header'] = $this->load->view('admin/header',$admin,TRUE);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function tambah_bidang($id){
			// print_r($this->session->userdata('form')['id_dpt_type']);
		if($this->session->userdata('admin')['id_role']!=3 && $this->session->userdata('admin')['id_role']!=10){ 
			redirect('pengadaan/view/'.$id.'/progress_pengerjaan');
		}
		$form = ($this->session->userdata('form'))?$this->session->userdata('form'):array();
		
		$fill = $this->securities->clean_input($_POST,'save');
		$item = $vld = $save_data = array();
		$admin = $this->session->userdata('admin');
		$vld = 	array(
					array(
						'field'=>'id_bidang',
						'label'=>'Bidang',
						'rules'=>'required'
					)
				);
		$this->form_validation->set_rules($vld);
		if($this->input->post('next')){
			if($this->form_validation->run()==TRUE){
				
				$this->session->set_userdata('form',array_merge($form,$this->input->post()));
				redirect(site_url('pengadaan/tambah_sub_bidang/'.$id.'#tabNav'));
			
			}
		}elseif($this->input->post('back')){
			unset($_POST['back']);
			$form = $this->session->userdata('form');
			$this->session->set_userdata('form',array_merge($form,$this->input->post()));
			redirect(site_url('pengadaan/klasifikasi/'.$id.'#tabNav'));
		}

		$data['id_bidang'] = $this->pm->get_bidang_list($this->session->userdata('form')['id_dpt_type']);


		$layout['content']= $this->load->view('tab/tambah_bidang',$data,TRUE);

		$item['header'] = $this->load->view('admin/header',$admin,TRUE);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function tambah_sub_bidang($id){
		if($this->session->userdata('admin')['id_role']!=3 && $this->session->userdata('admin')['id_role']!=10){ 
			redirect('pengadaan/view/'.$id.'/progress_pengerjaan');
		}
		$form = ($this->session->userdata('form'))?$this->session->userdata('form'):array();
		
		$fill = $this->securities->clean_input($_POST,'save');
		$item = $vld = $save_data = array();
		$admin = $this->session->userdata('admin');
		

		$vld = 	array(
					array(
						'field'=>'id_sub_bidang',
						'label'=>'Sub Bidang',
						'rules'=>'required'
					)
				);
		$this->form_validation->set_rules($vld);
		if($this->input->post('simpan')){

			if($this->form_validation->run()==TRUE){
				unset($_POST['next']);
				$form['id_proc'] = $id;
				$form['entry_stamp'] = date('Y-m-d H:i:s');

				$this->session->set_userdata('form',array_merge($form,$this->input->post()));
				
				$result = $this->pm->save_bsb($this->session->userdata('form'));
			// 	echo print_r($this->input->post('form'));
				if($result){
					$this->session->unset_userdata('form');
					$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menambah data bidang!</p>');
					redirect(site_url('pengadaan/view/'.$id.'/'.'#tabNav'));
				}
			}
		}elseif($this->input->post('back')){
			unset($_POST['back']);
			$form = $this->session->userdata('form');
			$this->session->set_userdata('form',array_merge($form,$this->input->post()));
			redirect(site_url('pengadaan/tambah_bidang/'.$id.'#tabNav'));
		}

		$data['id_sub_bidang'] = $this->utility->get_sub_bidang_list($form['id_bidang']);


		$layout['content']= $this->load->view('tab/tambah_sub_bidang',$data,TRUE);

		$item['header'] = $this->load->view('admin/header',$admin,TRUE);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function hapus_bsb($id,$id_proc){
		if($this->session->userdata('admin')['id_role']!=3 && $this->session->userdata('admin')['id_role']!=10){ 
			redirect('pengadaan/view/'.$id.'/progress_pengerjaan');
		}
		if($this->pm->hapus_pengadaan_bsb($id)){
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menghapus data!</p>');
			redirect(site_url('pengadaan/view/'.$id_proc.'#tabNav'));
		}else{
			$this->session->set_flashdata('msgSuccess','<p class="msgError">Gagal menghapus data!</p>');
			redirect(site_url('pengadaan/view/'.$id_proc.'#tabNav'));
		}
	}

	public function peserta($id){
		if($this->session->userdata('admin')['id_role']!=3 && $this->session->userdata('admin')['id_role']!=10){ 
			redirect('pengadaan/view/'.$id.'/progress_pengerjaan');
		}
		$data['sort'] = $this->utility->generateSort(array('peserta_name'));
		
		$page = 'peserta';
		$data['id'] = $id;
		$data['tabNav'] = $this->tabNav;
		
		$data['pagination'] = $this->utility->generate_page('pengadaan/view/'.$id.'/'.$page,$data['sort'], NULL,  $this->pm->get_procurement_peserta($id,NULL, $data['sort'], '','',FALSE));
		$data['list'] = $this->pm->get_procurement_peserta($id,NULL, $data['sort'], '','',FALSE);
		return $this->load->view('tab/peserta',$data,TRUE);
	}

	public function penawaran($id,$page,$act='view'){
		if($this->session->userdata('admin')['id_role']!=3 && $this->session->userdata('admin')['id_role']!=10){ 
			redirect('pengadaan/view/'.$id.'/progress_pengerjaan');
		}
		$data['sort'] = $this->utility->generateSort(array('peserta_name'));
		$data['act'] = $act;
		
		if($this->input->post('simpan')){
            unset($_POST['simpan']);
            // echo 'penawaran:'; print_r($this->input->post());
			$this->pm->save_penawaran($this->input->post());
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menyimpan data!</p>');
			redirect(site_url('pengadaan/view/'.$id.'/penawaran'.'#tabNav'));
		}

		$page = 'peserta';
		$data['id'] = $id;
		$data['tabNav'] = $this->tabNav;
		
		$data['pagination'] = $this->utility->generate_page('pengadaan/view/'.$id.'/'.$page,$data['sort'], NULL,  $this->pm->get_procurement_penawaran_($id,NULL, $data['sort'], '','',FALSE));
		$data['list'] = $this->pm->get_procurement_penawaran_($id,NULL, $data['sort'], '','',FALSE);
		if ($act == "edit") {
			# code...
			$data['list'] = $this->pm->get_procurement_penawaran($id,NULL, $data['sort'], '','',FALSE);
		}
		return $this->load->view('tab/penawaran',$data,TRUE);
	}

	public function negosiasi($id,$page,$act='view'){
		if($this->session->userdata('admin')['id_role']!=3 && $this->session->userdata('admin')['id_role']!=10){ 
			redirect('pengadaan/view/'.$id.'/progress_pengerjaan');
		}
		$data['sort'] = $this->utility->generateSort(array('peserta_name'));
		$data['act'] = $act;
		
		if($this->input->post('simpan')){
			unset($_POST['simpan']);
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menyimpan data!</p>');
			
			$this->pm->save_negosiasi($this->input->post());
			redirect(site_url('pengadaan/view/'.$id.'/negosiasi'.'#tabNav'));
		}

		$page 		= 'peserta';
		$data['id'] = $id;
		$data['tabNav'] = $this->tabNav;
		
        $negosiasi = $this->pm->get_procurement_negosiasi($id,NULL, $data['sort'], '','',FALSE);
        
         // if(empty($negosiasi)){
        //     $data['pagination'] = $this->utility->generate_page('pengadaan/view/'.$id.'/'.$page,$data['sort'], NULL,  $this->pm->get_procurement_penawaran_($id,NULL, $data['sort'], '','',FALSE));
        //     $data['list'] = $this->pm->get_procurement_penawaran_($id,NULL, $data['sort'], '','',FALSE);
        // }else{
		$data['pagination'] = $this->utility->generate_page('pengadaan/view/'.$id.'/'.$page,$data['sort'], NULL,  $this->pm->get_procurement_negosiasi($id,NULL, $data['sort'], '','',FALSE));
		$data['list'] = $this->pm->get_procurement_negosiasi($id,NULL, $data['sort'], '','',FALSE);
        // }}

		return $this->load->view('tab/negosiasi',$data,TRUE);
	}
	
	public function tambah_negosiasi($id_pengadaan)
	{
		$_POST = $this->securities->clean_input($_POST,'save');
		$admin = $this->session->userdata('admin');
		
		if(isset($_POST['Simpan']) && $_POST['Simpan']){
			unset($_POST['Simpan']);

			$save = $this->input->post();
			$save['id_proc'] = $id_pengadaan;
			$save['value'] = preg_replace("/[,]/", "", $save['value']);
			$save['fee'] = preg_replace("/[,]/", "", $save['fee']);
			// echo 'test save '; echo '<pre>'; print_r($save); echo '</pre>';
			$res = $this->pm->save_negosiasi($save);
			if($res){
				$this->session->set_flashdata('msgSuccesss','<p class="msgSuccess">Sukses menambah data!</p>');

				redirect(site_url('pengadaan/view/'.$id_pengadaan.'/negosiasi'));
			}
		}

		$data = array(
			'id_pengadaan' => $id_pengadaan,
			'peserta' => $this->pm->get_peserta($id_pengadaan)
		);

		$layout['content']= $this->load->view('tab/tambah_negosiasi',$data,TRUE);
		$item['header'] = $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function hapus_negosiasi($id, $id_pengadaan)
	{
		$res = $this->pm->hapus_negosiasi($id);
		if($res){
			$this->session->set_flashdata('msgSuccesss','<p class="msgSuccess">Sukses menghapus data!</p>');

			redirect(site_url('pengadaan/view/'.$id_pengadaan.'/negosiasi'));
		}
	}

	public function tambah_vendor($id_proc,$id){
		echo '<pre>';  print_r($id_proc); echo '</pre>';
		echo '<pre>';  print_r($id); echo '</pre>';		
		if($this->session->userdata('admin')['id_role']!=3 && $this->session->userdata('admin')['id_role']!=10){ 
			print_r('user admihn');
			redirect('pengadaan/view/'.$id.'/progress_pengerjaan');
		}
		// $array = $this->pm->get_ijin_list($id_proc,$id);
		// // 					// echo form_dropdown('id_surat', $array,null /*$this->form->get_temp_data('id_surat')*/);
		// 					echo '<pre>';  print_r($array); echo '</pre>';		
		// print_r('user bukan admin');
		$form['id_proc'] 		= $id_proc;
		$form['id_vendor'] 		= $id;
		$form['entry_stamp'] 	= date('Y-m-d H:i:s');
		$form['id_surat']		= $this->input->post('id_surat');
		// $form['id_surat'] = 430;
		// echo 'form: '.$form; 
		echo '<pre>';  print_r($form); echo '</pre>';
	
		$result = $this->pm->save_peserta($form);
		// echo '<pre>';  print_r($result); echo '</pre>';
		if($result){
			// echo 'succes sepertinya';
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menambah peserta!</p>');
			
			redirect(site_url('pengadaan/view/'.$id_proc.'/peserta'.'#tabNav'));	
		}
		// echo 'tidak succes sepertinya';
	}
	public function tambah_peserta($id){
		if($this->session->userdata('admin')['id_role']!=3 && $this->session->userdata('admin')['id_role']!=10){ 
			
			redirect('pengadaan/view/'.$id.'/progress_pengerjaan');
		}
		
		$admin = $this->session->userdata('admin');
		$page = '';
		$this->load->library('form');

		$per_page=10;
		$sort = $this->utility->generateSort(array('name','point','npwp_code','nppkp_code','id'));
		$array = array(
				'label'	=>	'Jenis Penyedia Barang/Jasa',
				'filter'=>	array(
								array('table'=>'ms_vendor|is_vms|get_is_vms' 	,'type'=>'dropdown','label'=> 'Jenis Penyedia Barang/Jasa'),
							)
						);
		$data['filter_list'] 	= $this->filter->group_filter_post($this->filter->get_field($array));
		$data['vendor_list']=$this->pm->get_pengadaan_vendor($id, $sort, $page, $per_page,TRUE);
		// print_r($data['vendor_list']);
		$data['pagination'] = $this->utility->generate_page('pengadaan/tambah_peserta',$sort, $per_page, $this->pm->get_pengadaan_vendor($id, $sort, '', '',TRUE));
		$data['sort'] = $sort;

		
		$data['id_pengadaan'] = $id;
		$layout['content']= $this->load->view('tab/tambah_peserta',$data,TRUE);
		$layout['script']= $this->load->view('content_js',$data,TRUE);

		$item['header'] = $this->load->view('admin/header',$admin,TRUE);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}
	public function search_kandidat($id){
		$search = $this->input->get('q');
		$data_vendor = $this->pm->search_kandidat($id);
	}
	public function hapus_peserta($id,$id_proc){
		if($this->session->userdata('admin')['id_role']!=3 && $this->session->userdata('admin')['id_role']!=10){ 
			redirect('pengadaan/view/'.$id.'/progress_pengerjaan'.'#tabNav');
		}
		if($this->pm->hapus_pengadaan_peserta($id)){
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menghapus data!</p>');
			redirect(site_url('pengadaan/view/'.$id_proc.'/peserta'.'#tabNav'));
		}else{
			$this->session->set_flashdata('msgSuccess','<p class="msgError">Gagal menghapus data!</p>');
			redirect(site_url('pengadaan/view/'.$id_proc.'/peserta'.'#tabNav'));
		}
	}

	public function tambah_ijin_usaha($id){
		if($this->session->userdata('admin')['id_role']!=3 && $this->session->userdata('admin')['id_role']!=10){ 
			redirect('pengadaan/view/'.$id.'/progress_pengerjaan');
		}
		$form = ($this->session->userdata('form'))?$this->session->userdata('form'):array();
		
		$fill = $this->securities->clean_input($_POST,'save');
		$item = $vld = $save_data = array();
		$admin = $this->session->userdata('admin');
		

		$vld = 	array(
					array(
						'field'=>'id_surat',
						'label'=>'Izin Usaha',
						'rules'=>'required'
					),array(
						'field'=>'kurs_value',
						'label'=>'Nilai Mata Uang Asing',
						'rules'=>''
					),
					array(
						'field'=>'id_kurs',
						'label'=>'Kurs',
						'rules'=>''
					),
					array(
						'field'=>'idr_value',
						'label'=>'Kurs',
						'rules'=>''
					),
				);
		$this->form_validation->set_rules($vld);
		if($this->input->post('simpan')){
			if($this->form_validation->run()==TRUE){
				unset($_POST['simpan']);
				$form['id_proc'] = $id;
				$dt = explode('|',$_POST['id_surat']);
				$form['id_surat'] = $dt[0];
				$form['idr_value'] = $_POST['idr_value'];
				$form['id_kurs'] = $_POST['id_kurs'];
				$form['kurs_value'] = $_POST['kurs_value'];
				$form['surat'] = $dt[1];
				$form['entry_stamp'] = date('Y-m-d H:i:s');

				$this->session->set_userdata('form',array_merge($form,$this->input->post()));
				
				$result = $this->pm->save_peserta($this->session->userdata('form'));
				if($result){
					$this->session->unset_userdata('form');
					$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menambah Peserta!</p>');
					redirect(site_url('pengadaan/view/'.$id.'/peserta'.'#tabNav'));
				}
			}
		}elseif($this->input->post('back')){
			unset($_POST['back']);
			$form = $this->session->userdata('form');
			$this->session->set_userdata('form',array_merge($form,$this->input->post()));
			redirect(site_url('pengadaan/tambah_peserta/'.$id.'#tabNav'));
		}

		$data['id_ijin_usaha'] = $this->pm->get_ijin_list($id,$form['id_vendor']);


		$layout['content']= $this->load->view('tab/tambah_ijin_usaha',$data,TRUE);

		$item['header'] = $this->load->view('admin/header',$admin,TRUE);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function pemenang($id){
		if($this->session->userdata('admin')['id_role']!=3 && $this->session->userdata('admin')['id_role']!=10){ 
			redirect('pengadaan/view/'.$id.'/progress_pengerjaan');
		}
		$_POST = $this->securities->clean_input($_POST,'save');
		$admin = $this->session->userdata('admin');
		$data['data'] = $this->pm->get_pemenang($id);

		$vld = 	array(
					array(
						'field'=>'pemenang',
						'label'=>'Pemenang',
						'rules'=>'required'
						),
					array(
						'field'=>'idr_kontrak',
						'label'=>'Nilai',
						'rules'=>'callback_check_nilai_kontrak'
						),
					array(
						'field'=>'kurs_kontrak',
						'label'=>'Nilai',
						'rules'=>'callback_check_nilai_kontrak'
						),
					array(
						'field'=>'id_kurs_kontrak',
						'label'=>'Kurs',
						'rules'=>'required'
						)
				);
		
		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			$_POST['idr_kontrak'] = preg_replace("/[,]/", "", $this->input->post('idr_kontrak'));
			$_POST['kurs_kontrak'] = preg_replace("/[,]/", "", $this->input->post('kurs_kontrak'));
			$_POST['entry_stamp'] = date("Y-m-d H:i:s");
			unset($_POST['simpan']);
			// print_r($_POST);
			$res = $this->pm->proses_pemenang($id,$this->input->post());
			if($res){
			
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses Mengangkat Pemenang!</p>');

				redirect(site_url('pengadaan/view/'.$id.'/pemenang'.'#tabNav'));
			}
		}
		
		$data['tabNav'] = $this->tabNav;
		$data['id'] = $id;
		$per_page=10;
		$data['list'] = $this->pm->get_pemenang_list($id);
		if(empty($data['data'])){
			$view = $this->load->view('tab/pemenang',$data,TRUE);
		}else{
			$view = $this->load->view('tab/pemenang_view',$data,TRUE);
		}
		return $view;
	}
	public function pemenang_edit($id){
		if($this->session->userdata('admin')['id_role']!=3 && $this->session->userdata('admin')['id_role']!=10){ 
			redirect('pengadaan/view/'.$id.'/progress_pengerjaan');
		}
		$_POST = $this->securities->clean_input($_POST,'save');
		$admin = $this->session->userdata('admin');
		$data['data'] = $this->pm->get_pemenang($id);
		// echo print_r($data);
		$vld = 	array(
					array(
						'field'=>'pemenang',
						'label'=>'Pemenang',
						'rules'=>'required'
						),
					array(
						'field'=>'idr_kontrak',
						'label'=>'Nilai',
						'rules'=>'callback_check_nilai_kontrak'
						),
					array(
						'field'=>'kurs_kontrak',
						'label'=>'Nilai',
						'rules'=>'callback_check_nilai_kontrak'
						),
					array(
						'field'=>'id_kurs_kontrak',
						'label'=>'Kurs',
						'rules'=>'required'
						)
				);
		
		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			$_POST['idr_kontrak'] = preg_replace("/[,]/", "", $this->input->post('idr_kontrak'));
			$_POST['kurs_kontrak'] = preg_replace("/[,]/", "", $this->input->post('kurs_kontrak'));
			$_POST['entry_stamp'] = date("Y-m-d H:i:s");
			unset($_POST['Simpan']);

			$res = $this->pm->proses_pemenang($id,$this->input->post());
			if($res){
			
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses Mengangkat Pemenang!</p>');

				redirect(site_url('pengadaan/view/'.$id.'/pemenang'.'#tabNav'));
			}
		}
		
		$data['tabNav'] = $this->tabNav;
		$data['id'] = $id;
		$per_page=10;
		$data['list'] = $this->pm->get_pemenang_list($id);
		
		$view = $this->load->view('tab/pemenang',$data,TRUE);
		
		return $view;
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
	public function check_nilai_kontrak($field){
		$idr_field = $this->input->post('idr_kontrak');
		$kurs_field = $this->input->post('kurs_kontrak');
		
		if($idr_field==''&&$kurs_field==''){
			$this->form_validation->set_message('check_nilai','Isi salah satu');
			return false;
		}else{
			return true;
		}
	}

	public function kontrak($id){

		if($this->session->userdata('admin')['id_role']!=3 && $this->session->userdata('admin')['id_role']!=10){ 
			redirect('pengadaan/view/'.$id.'/progress_pengerjaan');
		}

		$_POST 					= $this->securities->clean_input($_POST,'save');
		$admin 					= $this->session->userdata('admin');
		$data_kontrak 			= $this->pm->get_kontrak($id);
		$data['tabNav'] 		= $this->tabNav;
		$data['winner'] 		= $this->pm->get_winner_vendor($id);
		// print_r($data_kontrak);
		if(count($data_kontrak)>0){
			$data = $data_kontrak;

			$data ['sppbj_date']	= $this->pm->get_sppbj_date($id);
			$data['id'] = $id;
			$data['tabNav'] = $this->tabNav;
			return $this->load->view('tab/kontrak_view',$data,TRUE);
		}else{
			
			$vld = 	array(
				array(
					'field'=>'id_vendor',
					'label'=>'Perusahaan',
					'rules'=>'required'
					),
				array(
					'field'=>'no_sppbj',
					'label'=>'No. SPPBJ',
					'rules'=>''
					),
				array(
					'field'=>'sppbj_date',
					'label'=>'Tanggal SPPBJ',
					'rules'=>''
					),
				array(
					'field'=>'no_spmk',
					'label'=>'No. SPMK',
					'rules'=>''
					),
				array(
					'field'=>'spmk_date',
					'label'=>'Tanggal SPMK',
					'rules'=>''
					),
				array(
					'field'=>'start_work',
					'label'=>'Tanggal Mulai Kerja',
					'rules'=>'callback_check_mulai_kerja|date_range_same[end_work]'
					),
				array(
					'field'=>'end_work',
					'label'=>'Tanggal Akhir Kerja',
					'rules'=>'callback_check_akhir_kerja'
					),
				array(
					'field'=>'no_contract',
					'label'=>'No. Kontrak / PO',
					'rules'=>'required'
					),
				array(
					'field'=>'po_file',
					'label'=>'Lampiran Kontrak',
					'rules'=>'callback_do_upload[po_file]'
					),

				array(
					'field'=>'contract_price',
					'label'=>'Nilai Kontrak / PO',
					'rules'=>'required'
					),
				array(
					'field'=>'contract_kurs',
					'label'=>'Mata Uang',
					'rules'=>'required'
					),
				
				array(
					'field'=>'contract_price_kurs',
					'label'=>'Nilai Kontrak / PO dalam kurs',
					'rules'=>'required'
					),
				array(
					'field'=>'start_contract',
					'label'=>'Tanggal Mulai Kontrak',
					'rules'=>'required|date_range_same[end_contract]'
					),
				array(
					'field'=>'end_contract',
					'label'=>'Tanggal Selesai Kontrak',
					'rules'=>'required'
					),
				array(
					'field'=>'contract_date',
					'label'=>'Tanggal Kontrak',
					'rules'=>'required'
					)
				);
			
			$data['paket_progress'] 	= $this->pm->get_paket_progress($id);
			foreach ($data['paket_progress'] as $key => $value){
					if($value['id']==11) $_POST['sppbj_date']=$value['date'];
					$data['sppbj_date'] = $value['date'];
			}

			$this->form_validation->set_rules($vld);
			if($this->form_validation->run()==TRUE){
				$_POST['entry_stamp'] = date("Y-m-d H:i:s");
				$_POST['id_procurement'] = $id;
				$_POST['contract_price'] 	= preg_replace("/[,]/", "", $this->input->post('contract_price'));
				$_POST['contract_price_kurs'] 	= preg_replace("/[,]/", "", $this->input->post('contract_price_kurs'));
				unset($_POST['Simpan']);
				
				$_POST['sppbj_date']=$data['sppbj_date'];
				// print_r($this->input->post());
				$this->pm->v_exp($this->input->post());
				$res = $this->pm->save_kontrak($this->input->post());
				if($res){
					$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses membuat kontrak!</p>');

					redirect(site_url('pengadaan/view/'.$id.'/kontrak'.'#tabNav'));
				}
			}
			$data['id'] = $id;
			return $this->load->view('tab/kontrak',$data,TRUE);
		}
	}
	public function kontrak_edit($id)
	{
		if($this->session->userdata('admin')['id_role']!=3 && $this->session->userdata('admin')['id_role']!=10){ 
			redirect('pengadaan/view/'.$id.'/progress_pengerjaan');
		}
		$_POST = $this->securities->clean_input($_POST,'save');
		$admin = $this->session->userdata('admin');
		$data_kontrak = $this->pm->get_kontrak($id);
		// print_r($data_kontrak);
		
		$data['data_pemenang'] = $this->pm->get_pemenang($id);

		$data = $data_kontrak;
		$data ['sppbj_date']	= $this->pm->get_sppbj_date($id);
		$data['tabNav'] = $this->tabNav;
		$data['vendor_list'] = $this->pm->get_proc_vendor($id);

			

		$vld = 	array(
			array(
					'field'=>'id_vendor',
					'label'=>'Perusahaan',
					'rules'=>'required'
					),
				array(
					'field'=>'no_sppbj',
					'label'=>'No. SPPBJ',
					'rules'=>''
					),
				array(
					'field'=>'sppbj_date',
					'label'=>'Tanggal SPPBJ',
					'rules'=>''
					),
				array(
					'field'=>'no_spmk',
					'label'=>'No. SPMK',
					'rules'=>''
					),
				array(
					'field'=>'spmk_date',
					'label'=>'Tanggal SPMK',
					'rules'=>''
					),
				array(
					'field'=>'start_work',
					'label'=>'Tanggal Mulai Kerja',
					'rules'=>'callback_check_mulai_kerja|date_range_same[end_work]'
					),
				array(
					'field'=>'end_work',
					'label'=>'Tanggal Akhir Kerja',
					'rules'=>'callback_check_akhir_kerja'
					),
				array(
					'field'=>'no_contract',
					'label'=>'No. Kontrak / PO',
					'rules'=>'required'
					),
				array(
					'field'=>'contract_price',
					'label'=>'Nilai Kontrak / PO',
					'rules'=>'required'
					),
				array(
					'field'=>'contract_kurs',
					'label'=>'Mata Uang',
					'rules'=>'required'
					),
				
				array(
					'field'=>'contract_price_kurs',
					'label'=>'Nilai Kontrak / PO dalam kurs',
					'rules'=>'required'
					),
				array(
					'field'=>'start_contract',
					'label'=>'Tanggal Mulai Kontrak',
					'rules'=>'required|date_range_same[end_contract]'
					),
				array(
					'field'=>'end_contract',
					'label'=>'Tanggal Selesai Kontrak',
					'rules'=>'required'
					),
				array(
					'field'=>'contract_date',
					'label'=>'Tanggal Kontrak',
					'rules'=>'required'
					)
			);
		if(!empty($_FILES['po_file']['name'])){
			$vld[] = array(
					'field'=>'po_file',
					'label'=>'Lampiran',
					'rules'=>'callback_do_upload[po_file]'
					);
		}

		$data['paket_progress'] 	= $this->pm->get_paket_progress($id);
		foreach ($data['paket_progress'] as $key => $value){
				if($value['id']==11) $_POST['sppbj_date']=$value['date'];
				$data ['sppbj_date']	= $this->pm->get_sppbj_date($id);
		}

		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			$_POST['entry_stamp'] = date("Y-m-d H:i:s");
			$_POST['contract_price'] 	= preg_replace("/[,]/", "", $this->input->post('contract_price'));
			$_POST['contract_price_kurs'] 	= preg_replace("/[,]/", "", $this->input->post('contract_price_kurs'));
			$_POST['id_procurement'] = $id;
			

			$_POST['sppbj_date']=$data['sppbj_date'];
			unset($_POST['Simpan']);


			$this->pm->v_exp($this->input->post());
			$res = $this->pm->edit_kontrak($this->input->post(),$id);
			if($res){
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses membuat kontrak!</p>');
				redirect(site_url('pengadaan/view/'.$id.'/kontrak'.'#tabNav'));
			}
		}
		$data['id'] = $id;
		$data['winner'] 		= $this->pm->get_winner_vendor($id);
		
		return $this->load->view('tab/kontrak',$data,TRUE);
		
	}

	function check_mulai_kerja($field){
		$start_work_date = strtotime($this->input->post('start_work'));
		$start_contract_date = strtotime($this->input->post('start_contract'));
		if($start_work_date < $start_contract_date){
			$this->form_validation->set_message('check_mulai_kerja', 'Tanggal mulai kerja tidak boleh dibawah tanggal kontrak');
			return false;
		}else{
			return true;
		}
	}

	function check_akhir_kerja($field){
		$end_work_date = strtotime($this->input->post('end_work'));
		$end_contract_date = strtotime($this->input->post('end_contract'));
		if($end_work_date > $end_contract_date){
			$this->form_validation->set_message('check_akhir_kerja', 'Tanggal akhir kerja tidak boleh lebih dari tanggal kontrak');
			return false;
		}else{
			return true;
		}
	}
	public function submit($id){
		if($this->session->userdata('admin')['id_role']!=3 && $this->session->userdata('admin')['id_role']!=10){ 
			redirect('pengadaan/view/'.$id.'/progress_pengerjaan');
		}
		$data = $this->pm->get_pengadaan($id);
		if($data['status_procurement']!=0){
			redirect('pengadaan/view/'.$id.'/progress');
		}
		$data['tabNav'] = $this->tabNav;
		$data['sort'] = $this->utility->generateSort(array('peserta_name'));
		$data['id_pengadaan'] = $id;
		$_POST = $this->securities->clean_input($_POST,'save');
		$admin = $this->session->userdata('admin');
		
		if($this->input->post('simpan')){
			unset($_POST['simpan']);
			$_POST['proc_date'] = date("Y-m-d H:i:s");
			$_POST['status_procurement'] = 1;
			$res = $this->pm->send_proc($id,$this->input->post());
			if($res){
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Data telah terkirim ke Admin Kontrak. Data akan segera di proses!</p>');
				redirect(site_url('pengadaan/view/'.$id.'/submit'.'#tabNav'));
			}

		}

		return $this->load->view('tab/submit_procurement',$data,TRUE);
	}

	public function progress_pengadaan($id){
		$data['detailPengadaan'] = $this->pm->get_pengadaan($id);
		$data['pengadaan'] = $this->pm->get_paket_progress($id);
		$data['progress'] 	= $this->pm->get_progress_pengadaan($id);
		$data['step_pengadaan'] = $this->pm->get_pengadaan_step();
		$data['custom_step_pengadaan'] = $this->pm->get_custom_progress_pengadaan($id);
		$total_progress = count($data['step_pengadaan']);
		

		$data['tabNav'] = $this->tabNav;
		$data['progressNav'] = $this->progressNav;

		$data_kontrak = $this->pm->get_kontrak($id);
		$data['id_pengadaan'] = $id;
        #print_r($data);die;
		if($this->input->post('simpan')){
			#print_r($_FILES);
				$file_ary = array();
				$file_count = count($_FILES['file_upload']['name']);
				$file_keys = ($_FILES['file_upload']['name']);
				foreach ($file_keys as $key => $value) {
					$file_ary[$key]['name'] 	= $_FILES['file_upload']['name'][$key];
					$file_ary[$key]['type'] 	= $_FILES['file_upload']['type'][$key];
					$file_ary[$key]['size'] 	= $_FILES['file_upload']['size'][$key];
					$file_ary[$key]['tmp_name'] = $_FILES['file_upload']['tmp_name'][$key];
					$file_ary[$key]['error'] 	= $_FILES['file_upload']['error'][$key];
				}
		
			$result = true;
			$last = count($this->input->post('progress'));
			
				/* foreach($this->input->post('progress') as $key => $row){
					if($row==0) continue;

					if($key!=$last){
						if($_POST['progress'][$key+1]!=0){
							if(strtotime($_POST['date'][$key])>strtotime($_POST['date'][$key+1])){
								$result = false;
								$this->session->set_flashdata('msgSuccess','<p class="errorMsg">Tanggal harus berurutan</p>');
								redirect(current_url().'#tabNav');
								break;
							}
						}
					}
				} */
				
			#if($result == true){
				
				foreach($file_ary as $key => $value){
					
					if($value['name'] != ''){
						$_POST['file'][$key]=$this->do_upload_($value);
					}
				}

					$res = $this->pm->save_progress_pengadaan($id);
				
					// if($res){
						$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Berhasil mengubah progress!</p>');
						redirect(current_url().'#tabNav');
					// } 
			#}
		}
		if ($this->input->post('done')) {
			$done = $this->pm->procurement_is_done($id);
			if ($done) {
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Pengadaan Telah Selesai!</p>');
				redirect(current_url().'#tabNav');
			}
		}
		return $this->load->view('tab/progress_pengadaan',$data,TRUE);
	}
	
	public function tambah_progress_pengadaan($id_pengadaan)
	{
		$_POST = $this->securities->clean_input($_POST,'save');
		$admin = $this->session->userdata('admin');
		$vld = 	array(
			array(
				'field'=>'value',
				'label'=>'Judul',
				'rules'=>'required'
				)
			);

		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			unset($_POST['Simpan']);

			$res = $this->pm->save_custom_progress_pengadaan($this->input->post(), $id_pengadaan);
			if($res){
				$this->session->set_flashdata('msgSuccesss','<p class="msgSuccess">Sukses menambah data!</p>');

				redirect(site_url('pengadaan/view/'.$id_pengadaan.'/progress_pengadaan'));
			}
		}

		$data = array(
			'id_pengadaan' => $id_pengadaan
		);

		$layout['content']= $this->load->view('tab/tambah_progress_pengadaan',$data,TRUE);
		$item['header'] = $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}
	
	public function do_upload_($file){
			$target_dir = "./lampiran/new_test/";
			$new_name 	= "progress_lampiran_".$this->utility->name_generator($file);
			$target_file = $target_dir.$new_name.'pdf';
			if (!move_uploaded_file($file["tmp_name"], $target_file)) {
				echo "Sorry, there was an error uploading your file.".$file["error"];
			}
			return $new_name;
	}
	
	public function progress_pengerjaan($id){
		$data 					= $this->pm->get_pengadaan($id);
		$data['user'] 			= $this->session->userdata('admin')['id_role'];
		$data['id']				= $id;
		$data_kontrak 			= $this->pm->get_kontrak($id);
		$data['kontrak_data']	= $data_kontrak;
		$denda 					= $this->pm->get_denda($id);
		$data['id_pengadaan'] 	= $id;
		$data['tabNav'] 		= $this->tabNav;
		$data['color'] 			= array('#A2A2A2','#CACACA','#DCDCDC');
		$data['color_amandemen']= array('#1281FF','#4197FA','#65ACFC','#8BC0FC','#A7CFFC');
		$data['progressNav'] 	= $this->progressNav;
		$data['pengadaan'] 		= $this->pm->get_paket_progress($id);
		$data['contract'] 		= $this->pm->get_contract_progress($data_kontrak['id']);
		$data['denda_price'] 	= ($denda['denda']) ? 'Denda : Rp. '.number_format($denda['denda']) :''; 
		$data['denda_day'] 		= $this->pm->get_denda_day($id);
		$data['graph'] 			= $this->pm->get_graph($data_kontrak['id'],$id);
		// print_r($data['graph']);
		if($data['graph']['total_day']>0){
			
			$data['realization'] 	= $this->pm->get_realization($id);
		}	
		
		$data['date'] 			= date("Y-m-d");
		
		
		return $this->load->view('tab/progress_pengerjaan',$data,TRUE);
	}
	public function denda($id){
		if($this->session->userdata('admin')['id_role']!=3 && $this->session->userdata('admin')['id_role']!=10){ 
			redirect('pengadaan/view/'.$id.'/progress_pengerjaan');
		}
		$data = $this->pm->get_denda($id);
		$data_kontrak = $this->pm->get_kontrak($id);
		$data['tabNav'] = $this->tabNav;
		$_POST = $this->securities->clean_input($_POST,'save');
		$admin = $this->session->userdata('admin');
		$data['denda'] = $this->pm->get_denda();
		$data['range'] = $this->pm->get_date_range_for_denda($id);
		$data['range']['end_date'] = date('Y-m-d H:i:s', strtotime($data['range']['end_date'].' +1 days'));
		// echo $data['range']['end_date'];
		$vld = 	array(
			array(
				'field'=>'start_date',
				'label'=>'Tanggal Mulai',
				'rules'=>'required'
				),
			array(
				'field'=>'end_date',
				'label'=>'Tanggal Berakhir',
				'rules'=>'required|date_low[start_date]'
				)
			);
		
		$this->form_validation->set_rules($vld);

		if($this->form_validation->run()==TRUE){

			$_POST['id_contract'] = $data_kontrak['id'];
			$_POST['entry_stamp'] = date("Y-m-d H:i:s");

			$hari 		  		  = get_range_date( $_POST['end_date'] , $_POST['start_date'] );
			$total_hari			  = ($hari>50)?50:$hari;
			$denda		  		  = $total_hari/1000 * $data_kontrak['contract_price'];
			$max_denda		  	  = 50/100 * $data_kontrak['contract_price'];
			$_POST['denda']		  = ($denda>$max_denda) ? $max_denda : $denda;

			// echo $total_hari.' '.$data_kontrak['contract_price'].' '.$denda.' '.$max_denda.' '.$_POST['denda'];
			
			$res = $this->pm->save_denda($id,$this->input->post());
			if($res){
			
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menambah denda!</p>');

				redirect(site_url('pengadaan/view/'.$id.'/progress_pengerjaan'.'#tabNav'));
			}
		}

		$data['id'] = $id;
		$per_page=10;
		$data['list'] = $this->pm->get_pemenang_list($id);

		return $this->load->view('tab/denda',$data,TRUE);
	}
	public function reset_denda($id){
		
		$res = $this->pm->reset_denda($id);
		if($res){
		
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menghapus denda!</p>');

			redirect(site_url('pengadaan/view/'.$id.'/progress_pengerjaan'.'#tabNav'));
		}
	

	}

	public function amandemen($id=0,$page,$id_amandemen=0){
		if($this->session->userdata('admin')['id_role']!=3 && $this->session->userdata('admin')['id_role']!=10){ 
			redirect('pengadaan/view/'.$id.'/progress_pengerjaan');
		}

		$data = $this->pm->get_amandemen($id_amandemen);

		$data_kontrak 	= $this->pm->get_kontrak($id);
		// print_r($data_kontrak);
		$date_range 	= $this->pm->get_date_range_for_denda($id);
		$data['range'] = $date_range;
		$work_range 	= $this->pm->get_work_day($id);
		$data['tabNav'] = $this->tabNav;
		$_POST = $this->securities->clean_input($_POST,'save');
		$admin = $this->session->userdata('admin');

		$vld = 	array(
			array(
				'field'=>'step_name',
				'label'=>'Tahap Amandemen',
				'rules'=>'required'
				),
			array(
				'field'=>'start_date',
				'label'=>'Tanggal Mulai',
				'rules'=>'required'
				),
			array(
				'field'=>'end_date',
				'label'=>'Tanggal Berakhir',
				'rules'=>'required|date_low[start_date]'
				)
			);
		
		$this->form_validation->set_rules($vld);
		if($this->input->post('Simpan')){
			if($this->form_validation->run()==TRUE){

				$_POST['id_contract'] = $data_kontrak['id'];
				$_POST['entry_stamp'] = date("Y-m-d H:i:s");
				unset($_POST['Simpan']);

				$total_amandemen = $this->pm->get_amandemen_total($id);

				$condition 		= (strtotime($_POST['start_date'])<strtotime($work_range['end_date']));
				$message 		= 'Tanggal mulai amandemen harus lebih dari tanggal pengerjaan!';
				if(!$condition){
					if($total_amandemen==0){

						$condition 	=  (strtotime($_POST['start_date'])>strtotime($date_range['end_date']));
						$message 	= 'Tanggal mulai amandemen lebih dari tanggal pengerjaan!';

						// if(!$condition){
						// 	$condition = (strtotime($_POST['end_date'])<strtotime($date_range['end_date']));
						// 	$message 	= 'Tanggal selesai amandemen kurang dari tanggal pengerjaan!';
						// }
						
					}else{

						if($id_amandemen!=0){
							
							$condition = (strtotime($_POST['end_date'])<strtotime($data_kontrak['end_contract']));
							$message 	= 'Tanggal selesai amandemen kurang dari tanggal kontrak!';
							
						}else{
							$condition	= (strtotime($_POST['start_date'])!=strtotime($date_range['end_date']));
							$message 	= 'Tanggal Mulai Amandemen harus sesuai dengan hari terakhir!';
						}
					}
				}
				if($id!=0){
					$res = $this->pm->save_amandemen($id,$this->input->post(),$id_amandemen);
					if($res){
					
						$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menambah amandemen!</p>');

						redirect(site_url('pengadaan/view/'.$id.'/progress_pengerjaan'.'#tabNav'));
					}
				}
				if($condition){
					$this->session->set_flashdata('msgSuccess','<p class="errorMsg">'.$message.'</p>');

				}else{
					
				}
				
			}
		}


		return $this->load->view('tab/amandemen',$data,TRUE);
	}


	public function penilaian_kerja($id){
		if($this->session->userdata('admin')['id_role']!=3 && $this->session->userdata('admin')['id_role']!=10){ 
			redirect('pengadaan/view/'.$id.'/progress_pengerjaan');
		}
		$data = $this->pm->get_pengadaan($id);
		$data_kontrak = $this->pm->get_kontrak($id);

		$data['contract'] = $this->pm->get_contract_progress($data_kontrak['id']);
		$contract_length = ceil((abs(strtotime($data_kontrak['end_work'])-strtotime($data_kontrak['start_work']))+1)/86400) ;

		$data['graph'] = $this->pm->get_graph($data_kontrak['id']);

		$data['graph']['max'] = ($contract_length>$data['graph']['max'] )?$contract_length:$data['graph']['max'];

		$data['id_pengadaan'] = $id;
 
		$data_cp = $this->pm->get_contract_progress($data_kontrak['id']);
		foreach( $data_cp as $key => $val){
   			$data['graph']['supposed']['percentage'][$val['id']] = (($val['supposed']/$data['graph']['max'])*100);
   			$data['graph']['realization']['percentage'][$val['id']] = (($val['realization']/$data['graph']['max'])*100);
   		}

		$data['sort'] = $this->utility->generateSort(array('step_name','supposed','realization'));

		return $this->load->view('tab/progress_pengadaan',$data,TRUE);
	}

	public function penilaian_pengadaan($id){
		if($this->session->userdata('admin')['id_role']!=3 && $this->session->userdata('admin')['id_role']!=10){ 
			redirect('pengadaan/view/'.$id.'/progress_pengerjaan');
		}
		$data = $this->pm->get_pengadaan($id);
		$data_kontrak = $this->pm->get_kontrak($id);
		$data['id_pengadaan'] = $id;
		
		$data['sort'] = $this->utility->generateSort(array('step_name','supposed','realization'));

		return $this->load->view('tab/progress_pengadaan',$data,TRUE);
	}

	public function stop_pengadaan($id){
		$res = $this->pm->stop_pengadaan($id);
		if($res){
			$data 	= $this->pm->get_pengadaan($id);
			$email_data 	= $this->pm->get_email_user($data['budget_spender']);
			$email['subject']	= "Pengadaan ".$data['name'];
			$email['msg']		= 'Pengadaan '.$data['name'].' telah selesai. Harap melakukan penilaian assessment untuk Penyedia Barang / Jasa terkait. <br>
				Silahkan login ke dalam Sistem Aplikasi Kelogistikan PT Nusantara Regas untuk melihat melakukan penilaian.
				<br>
				<br>
				Terima kasih.<br/>
				PT Nusantara Regas';
			foreach($email_data as $key){
				$this->utility->mail($key, $email['msg'], $email['subject']);

			}

			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menyelesaikan pengadaan!</p>');

			redirect(site_url('pengadaan/view/'.$id.'/progress_pengerjaan'.'#tabNav'));
		}
	}

	public function remark($id){
		if($this->session->userdata('admin')['id_role']!=3 && $this->session->userdata('admin')['id_role']!=10){ 
			redirect('pengadaan/view/'.$id.'/progress_pengerjaan');
		}
		$data = $this->pm->get_pengadaan($id);
		$data['tabNav'] = $this->tabNav;
		$data['id_pengadaan'] = $id;
		if($this->input->post('simpan')){
			$res = $this->pm->save_remark($id);
			if($res){
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mengubah remark!</p>');

				redirect(site_url('pengadaan/view/'.$id.'/remark'.'#tabNav'));
			}
		}
		$data['sort'] = $this->utility->generateSort(array('step_name','supposed','realization'));

		return $this->load->view('tab/remark',$data,TRUE);
	}

	public function do_upload($name = '', $db_name = ''){	
		$form = $this->session->userdata('form');
		$file_name = $_FILES[$db_name]['name'] = $db_name.'_'.$this->utility->name_generator($_FILES[$db_name]['name']);
		
		$config['upload_path'] = './lampiran/'.$db_name.'/';
		$config['allowed_types'] = 'pdf|jpeg|jpg|png|gif|doc|docx';
		$config['max_size'] = '20960';
		
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


	//BARANG

	public function tambah_barang($id){
		if($this->session->userdata('admin')['id_role']!=3 && $this->session->userdata('admin')['id_role']!=10){ 
			redirect('pengadaan/view/'.$id.'/progress_pengerjaan');
		}
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
							'label'=>'Harga Satuan',
							'rules'=>'required'
						),
						array(
							'field'=>'category',
							'label'=>'Katelogi',
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
				$form['id_procurement'] = $id;
				$form['nama_barang'] 	= $this->input->post('nama_barang');
				$form['nilai_hps'] 		= preg_replace("/[,]/", "", $this->input->post('nilai_hps'));
				$form['id_kurs'] 		= $this->input->post('id_kurs');
				// $form['id_material'] 	= $this->input->post('id_material');
				$form['id_material'] 	= 0 ;
				// $id_material 			= $form['id_material'];
				// $id_material 			= 0 ; 
				// $form['is_catalogue'] 	= ($id_material!=''||$id_material!=0) ? 1 : $this->input->post('is_catalogue');
				$form['is_catalogue'] 	= ($id_material!=''||$id_material!=0) ? 1 : 0;
				$form['category'] 		= $this->input->post('category');
				$form['entry_stamp'] 	= date('Y-m-d H:i:s');
				echo 'form barang'; print_r($form);
				$result = $this->pm->save_barang($form);
				if($result){

					// PUSH DATA TO CATALOGUE if user select the option
					if ($this->input->post('is_catalogue')){

						$insert_id = $result;
						$katalog['category'] 	= $this->input->post('category');
						$katalog['id_barang']	= $insert_id;
						$katalog['nama']		= $this->input->post('nama_barang');
						$katalog['id_kurs']		= $this->input->post('id_kurs');
						$katalog['category'] 	= $form['category'];
						$katalog['entry_stamp'] = date('Y-m-d H:i:s');
						
						$id_material = $this->pm->save_barang_catalogue($katalog);

					}

					$history['id_procurement']	= $id;
					$history['id_material']		= $id_material;
					$history['entry_stamp']		= date('Y-m-d H:i:s');
					$history['date']			= date('Y-m-d');
					$history['price']			= preg_replace("/[,]/", "", $this->input->post('nilai_hps'));

					$this->pm->save_barang_harga($history);


					$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menambah Barang / Jasa!</p>');
					redirect(site_url('pengadaan/view/'.$id.'/barang'.'#tabNav'));
				}
			}
		}


		$kurs_list = $this->pm->get_procurement_kurs($id,'', NULL, '','',FALSE);
		$where_in = array();
		foreach ($kurs_list as $key => $value) {
			$where_in[] = $value['id_kurs'];
		}
		
		$data['id_kurs'] 	= $this->pm->get_kurs_barang($where_in);
		// $layout['menu']		= $this->pm->get_auction_list();
		$layout['content']	= $this->load->view('tab/tambah_barang',$data,TRUE);
		$layout['script']	= $this->load->view('tab/tambah_barang_js',$data,TRUE);
		$item['header'] 	= $this->load->view('admin/header',$admin,TRUE);
		$item['content'] 	= $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function edit_barang($id,$id_procurement){
		if($this->session->userdata('admin')['id_role']!=3 && $this->session->userdata('admin')['id_role']!=10){ 
			redirect('pengadaan/view/'.$id.'/progress_pengerjaan');
		}
		$form 	= ($this->session->userdata('form'))?$this->session->userdata('form'):array();
		$fill 	= $this->securities->clean_input($_POST,'save');
		$item 	= $vld = $save_data = array();
		$admin 	= $this->session->userdata('admin');

		$data 	= $this->pm->get_barang_data($id);

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
				);
		if($data['is_catalogue']==1){
			$vld = 	array(
						array(
							'field'=>'nilai_hps',
							'label'=>'Nilai HPS',
							'rules'=>'required'
						)
					);
		}
		$this->form_validation->set_rules($vld);

		if($this->input->post('simpan')){

			if($this->form_validation->run()==TRUE){
				unset($_POST['simpan']);
				$_POST['nilai_hps'] 		= preg_replace("/[,]/", "", $this->input->post('nilai_hps'));
				$_POST['edit_stamp'] 	= date('Y-m-d H:i:s');
				
				$result = $this->pm->edit_barang($this->input->post(),$id);
				if($result){
					$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mengubah Barang / Jasa!</p>');
					redirect(site_url('pengadaan/view/'.$id_procurement.'/barang'.'#tabNav'));
				}
			}
		}

		$kurs_list = $this->pm->get_procurement_kurs($id_procurement,'', NULL, '','',FALSE);
		$where_in = array();
		foreach ($kurs_list as $key => $value) {
			$where_in[] = $value['id_kurs'];
		}
		
		$data['kurs'] 		= $this->pm->get_kurs_barang($where_in);
		
		$layout['menu']		= $this->pm->get_auction_list();
		$layout['content']	= $this->load->view('tab/edit_barang',$data,TRUE);
		$item['header'] 	= $this->load->view('admin/header',$admin,TRUE);
		$item['content'] 	= $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}


	public function reset($id){
		if($this->pm->reset($id)){
			
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menghapus data!</p>');
			redirect(site_url('pengadaan/view/'.$id.'/'.'#tabNav'));
		}else{
			$this->session->set_flashdata('msgSuccess','<p class="msgError">Gagal menghapus data!</p>');
			redirect(site_url('pengadaan/view/'.$id.'/'.'#tabNav'));
		}
	}


	public function hapus_pengerjaan($id_proc, $id){
		if($this->pm->hapus_pengerjaan($id)){
			
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menghapus data!</p>');
			redirect(site_url('pengadaan/view/'.$id_proc.'/progress_pengerjaan'.'#tabNav'));
		}else{
			$this->session->set_flashdata('msgSuccess','<p class="msgError">Gagal menghapus data!</p>');
			redirect(site_url('pengadaan/view/'.$id_proc.'/progress_pengerjaan'.'#tabNav'));
		}
	}
	public function hapus_amandemen($id_proc, $id){
		if($this->pm->hapus_amandemen($id)){
			
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menghapus data!</p>');
			redirect(site_url('pengadaan/view/'.$id_proc.'/progress_pengerjaan'.'#tabNav'));
		}else{
			$this->session->set_flashdata('msgSuccess','<p class="msgError">Gagal menghapus data!</p>');
			redirect(site_url('pengadaan/view/'.$id_proc.'/progress_pengerjaan'.'#tabNav'));
		}
	}
}