<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Fkpbj extends MY_Controller {

	public $form;
	public $modelAlias 	= 'fkm';
	public $alias 		= 'ms_fkpbj';
	public $module 		= 'fkpbj';

	public function __construct(){
		parent::__construct();		
		$this->load->model('Fkpbj_model','fkm');
		$this->load->model('Fppbj_model','fm');
		$this->load->model('Main_model','mm');
		$this->load->model('pemaketan_model','pm');

		$this->form = array(
			'form'=>array(
						array(
							'field'	=> 	'no_pr',
							'type'	=>	'text',
							'label'	=>	'No. PR',
							'rules' =>	'required'
						),array(
							'field'	=> 	'tipe_pr',
							'type'	=>	'dropdown',
							'label'	=>	'Tipe PR',
							'source'=>	array(0 => 'Pilih Dibawah Ini', 'direct_charge' => 'Direct Charges', 'services' => 'Services', 'user_purchase' => 'User Purchase'),
							'rules' =>	'required'
						),
						array(
							'field'	=> 	'nama_pengadaan',
							'type'	=>	'text',
							'label'	=>	'Nama Pengadaan',
						),array(
							'field'	=> 	'pengadaan',
							'type'	=>	'dropdown',
							'label'	=>	'Jenis Pengadaan',
							'source'=>	array(0 => 'Pilih Dibawah Ini', 'jasa' => 'Pengadaan Jasa', 'barang' => 'Pengadaan Barang'),
							'rules'	=>	'required'
						),array(
							'field'	=> 	'jenis_pengadaan',
							'type'	=>	'dropdown',
							'label'	=>	'Jenis Detail Pengadaan',
							'source'=>	array('' => 'Pilih Jenis Pengadaan Diatas'),
							'rules'	=>	'required'
						),array(
							'field'	=> 	'metode_pengadaan',
							'type'	=>	'dropdown',
							'label'	=>	'Metode Pengadaan',
							'source'=>	$this->mm->getProcMethod(),
						),array(
							'field'	=> 	'idr_anggaran',
							'type'	=>	'currency',
							'label'	=>	'Anggaran (IDR)',
							'rules' =>	'required'
						),array(
							'field'	=> 	'usd_anggaran',
							'type'	=>	'currency',
							'label'	=>	'Anggaran (USD)'
						),array(
							'field'	=> 	'year_anggaran',
							'type'	=>	'number',
							'label'	=>	'Tahun Anggaran',
							'rules' => 	'required'
						),array(
							'field'	=> 	'kak_lampiran',
							'type'	=>	'file',
							'label'	=>	'KAK / Spesifikasi Teknis',
							'upload_path'=> base_url('assets/lampiran/fppbj/'),
							'upload_url'=> site_url('fkpbj/upload_lampiran'),
							'allowed_types'=> '*',
							'rules' =>	'required'
						),array(
							'field'	=> 	'hps',
							'type'	=>	'radio',
							'label'	=>	'Ketersediaan HPS',
							'source'=>	array(1 => 'Ada', 0 => 'Tidak Ada'),
							'rules' =>	'required'
						),array(
								'field'	=> 	'lingkup_kerja',
								'type'	=>	'textarea',
								'label'	=>	'Lingkup Kerja',
								'rules' => 	'required'
						),array(
							'field'	=> 	'penggolongan_penyedia',
							'type'	=>	'dropdown',
							'label'	=>	'Penggolongan Penyedia Jasa (Usulan)',
							'source'=>	array(0 => 'Pilih Dibawah Ini', 'perseorangan' => 'Perseorangan', 'usaha_kecil' => 'Usaha Kecil(K)', 'usaha_menengah' => 'Usaha Menengah(M)', 'usaha_besar' => 'Usaha Besar(B)'),
							'rules' =>	'required'
						),array(
							'field'	=> 	array('jwpp_start','jwpp_end'),
							'type'	=>	'date_range',
							'label'	=>	'Jangka Waktu Penyelesaian Pekerjaan ("JWPP")',
						),array(
							'field'	=> 	array('jwp_start','jwp_end'),
							'type'	=>	'date_range',
							'label'	=>	'Masa Pemeliharaan dan/atau Durasi Laporan',
						),array(
							'field'	=> 	'desc_metode_pembayaran',
							'type'	=>	'textarea',
							'label'	=>	'Metode Pembayaran (Usulan)',
							'rules' =>	'required'
						),array(
							'field'	=> 	'jenis_kontrak',
							'type'	=>	'dropdown',
							'label'	=>	'Jenis Kontrak (Usulan)',
							'source'=>	array(	''	 	=> 'Pilih Dibawah Ini',
												'po' 	=> 'Purchase Order (PO)',
												'GTC01' => 'GTC01 - Kontrak Jasa Konstruksi non EPC',
												'GTC02' => 'GTC02 - Kontrak Jasa Konsultan',
												'GTC03' => 'GTC03 - Kontrak Jasa Umum',
												'GTC04' => 'GTC04 - Kontrak Jasa Pemeliharaan',
												'GTC05' => 'GTC05 - Kontrak Jasa Pembuatan Software',
												'GTC06' => 'GTC06 - Kontrak Jasa Sewa Fasilitas dan Alat',
												'GTC07' => 'GTC07 - Kontrak Jasa Tenaga Kerja.',
												'spk'	=> 'SPK'
											),
							'rules' =>	'required'
						),array(
							'field'	=> 	'sistem_kontrak',
							'type'	=>	'multiple',
							'label'	=>	'Sistem Kontrak (Usulan)',
							'source'=>	array(	''	 			=> 'Pilih Dibawah Ini',
												'lumpsum' 		=> 'Perikatan Harga - Lumpsum',
												'unit_price'	=> 'Perikatan Harga - Unit Price',
												'modified' 		=> 'Perikatan Harga - Modified (lumpsum + unit price)',
												'outline' 		=> 'Perikatan Harga - Outline Agreement',
												'turn_key' 		=> 'Delivery - Turn Key',
												'sharing' 		=> 'Delivery - Sharing Contract',
												'success_fee' 	=> 'Delivery - Success Fee',
												'stockless' 	=> 'Delivery - Stockless Purchasing',
												'on_call' 		=> 'Delivery - On Call Basic',
											),
						),
						array(
							'field'	=> 	'desc_dokumen',
							'type'	=>	'textarea',
							'label'	=>	'Keterangan',
							'rules' =>	'required'
						)							
					),

			'successAlert'=>'Berhasil mengubah data!',
			'filter'=>array(
				array(
					'type'	=>	'text',
					'label'	=>	'Nama Pengadaan',
					'field' =>  'nama_pengadaan'
				),
				array(
					'type'	=>	'currency',
					'label'	=>	'Anggaran dalam Rupiah',
					'field' =>  'idr_anggaran'
				),
				array(
					'type'	=>	'number',
					'label'	=>	'Tahun Anggaran',
					'field' =>  'year_anggaran'
				),
				
			)
		);

		$this->insertUrl = site_url('fkpbj/save/');
		$this->updateUrl = 'fkpbj/update/';
		$this->deleteUrl = 'fkpbj/delete/';
		$this->approveURL = 'fkpbj/approve/';
		$this->getData = $this->fkm->getData($this->form);
		
		// Filter form elements to only include valid validation rules
		$validation_rules = array();
		foreach ($this->form['form'] as $element) {
			if (is_array($element) && isset($element['field']) && isset($element['rules']) && !empty($element['field']) && !empty($element['rules'])) {
				// Handle array fields (like date ranges)
				if (is_array($element['field'])) {
					foreach ($element['field'] as $field) {
						if (!empty($field)) {
							$validation_rules[] = array(
								'field' => $field,
								'label' => isset($element['label']) ? $element['label'] : $field,
								'rules' => $element['rules']
							);
						}
					}
				} else {
					$validation_rules[] = $element;
				}
			}
		}
		if (!empty($validation_rules)) {
			$this->form_validation->set_rules($validation_rules);
		}
	}

	public function getData($id_division = "", $id_fppbj = "", $year = "")
	{
		$config['query'] = $this->fkm->getData($id_division, $id_fppbj, $year);
		$return = $this->tablegenerator->initialize($config);
		echo json_encode($return);
	}

	public function index($id_division = "", $id_fppbj = "", $year = ""){
		$this->breadcrumb->addlevel(1, array(
			'url' => site_url('fkpbj'),
			'title' => 'FKPBJ'
		));

		$data['id_division']	= $id_division;
		$data['id_fppbj']		= $id_fppbj;
		$data['year']		 	= $year;
		
		$this->header = 'FKPBJ';
		$this->content = $this->load->view('fkpbj/list',null, TRUE);
		$this->script = $this->load->view('fkpbj/list_js', null, TRUE);
		parent::index();
	}

	public function aktifkan($id) {	
		if ($this->fkm->updateStatus($id,1)) {
			$return['status'] = 'success';
		}
		else {
			$return['status'] = 'error';
		}
		echo json_encode($return);
	}
	
	public function add($id_fppbj){
		$modelAlias = $this->modelAlias;
		$data 		= $this->fm->selectData($id_fppbj);
		
		foreach($this->formWizard['step']['fppbj']['form'] as $key => $element) {
			$this->formWizard['step']['fppbj']['form'][$key]['value'] = $data[$element['field']];

			if($this->formWizard['step']['fppbj']['form'][$key]['type']=='date_range'){
				$_value = array();
				
				foreach ($this->formWizard['step']['fppbj']['form'][$key]['field'] as $keys => $values) {
					$_value[] = $data[$values];
					
				}
				$this->formWizard['step']['fppbj']['form'][$key]['value'] = $_value;
			}

			if($this->formWizard['step']['fppbj']['form'][$key]['field'] == 'nama_pengadaan'){
				$this->formWizard['step']['fppbj']['form'][$key]['readonly'] = true;
			}
			if($this->formWizard['step']['fppbj']['form'][$key]['field'] == 'metode_pengadaan'){
				$this->formWizard['step']['fppbj']['form'][$key]['readonly'] = true;
			}
			if($this->formWizard['step']['fppbj']['form'][$key]['field'] == 'jwpp'){
				$this->formWizard['step']['fppbj']['form'][$key]['readonly'] = true;
			}
			if($this->formWizard['step']['fppbj']['form'][$key]['field'] == 'jwp'){
				$this->formWizard['step']['fppbj']['form'][$key]['readonly'] = true;
			}
		}
		$this->formWizard['url'] = $this->insertUrl;
		$this->formWizard['button'] = array(
			array(
				'type' => 'submit',
				'label' => 'Simpan',
			) ,
			array(
				'type' => 'cancel',
				'label' => 'Batal'
			)
		);
		echo json_encode($this->formWizard);
	}

	public function getSingleData($id=null){
		$this->form = $this->form;
		// define status parameter
		$admin = $this->session->userdata('admin');
		$param_  = ($admin['id_role'] == 4) ? ($param_=1) : '' ;
		$this->form['url'] 		= site_url($this->approveURL.$id.'/'.$param_);
		$this->form['button'] 	= array(
									array(
										'type' 	=> 'export',
										'link'	=> $this->form['url'],
										'label' => '<i style="line-height:25px;" class="fas fa-thumbs-up"></i>&nbsp;Setujui Data'
									),
									array(
										'type' => 'cancel',
										'label' => 'Tutup'
									)
								);

		parent::getSingleData($id);
	}
	
	public function approve($id, $param_){
		$table = "ms_fkpbj";
		// print_r($table);
		$fkpbj = $this->fkm->get_fkpbj($id);
		$param_ = array('is_status' => 2, 'is_approved' => $param_);
		$data 	= $this->mm->approve($table, $id, $param_);

		$division = $this->get_email_division($this->session->userdata('admin')['id_division']);

		$to = '';
		foreach ($division as $key => $value) {
			$to .= $value['email'].', ';
		}
		
		$subject = 'FKPBJ telah disetujui';
		$message = $fkpbj['nama_pengadaan'].'telah di approve oleh '.$this->session->userdata('admin')['name'];
		$this->send_mail($to, $subject, $message, $link);
		redirect($_SERVER['HTTP_REFERER']);
		return $data;
	}

	public function edit($id = null){

		$modelAlias = $this->modelAlias;
		$data = $this->$modelAlias->selectData($id);
		
		foreach($this->form['form'] as $key => $element) {
			$this->form['form'][$key]['value'] = $data[$element['field']];
			if ($this->form['form'][$key]['rules'] == 'required') {
				$this->form['form'][$key]['readonly'] = true;
			}
		}


		$this->form['url'] = site_url($this->updateUrl . '/' . $id);
		$this->form['button'] = array(
			array(
				'type' => 'submit',
				'label' => 'Ubah'
			) ,
			array(
				'type' => 'cancel',
				'label' => 'Batal'
			)
		);
		echo json_encode($this->form);
	}

	public function save($data = null){	
		$id_fppbj = null;
		if (is_numeric($data)) {
			$id_fppbj = (int) $data;
		} else if (is_array($data) && isset($data['id_fppbj']) && is_numeric($data['id_fppbj'])) {
			$id_fppbj = (int) $data['id_fppbj'];
		} else {
			$seg = $this->uri->segment(3);
			if (is_numeric($seg)) {
				$id_fppbj = (int) $seg;
			} else {
				$post_id = $this->input->post('id_fppbj');
				if (is_numeric($post_id)) {
					$id_fppbj = (int) $post_id;
				}
			}
		}
		if (empty($id_fppbj)) {
			show_error('ID FPPBJ tidak valid', 400);
			return;
		}

		$modelAlias = $this->modelAlias;
		$fppbj = $this->fm->selectData($id_fppbj);
		$fp3 = $this->fkm->getDataFp3($id_fppbj);	

		if ($fppbj['is_status'] != 1) {
			$nama_pengadaan = $fppbj['nama_pengadaan'];
			$metode_pengadaan = $fppbj['metode_pengadaan'];
			$jwpp_start = $fppbj['jwpp_start'];
			$jwpp_end = $fppbj['jwpp_end'];
			$kak_lampiran = $fppbj['kak_lampiran'];
		} else {
			$nama_pengadaan = $fp3['nama_pengadaan'];
			$metode_pengadaan = $fp3['metode_pengadaan'];
			$jwpp_start = $fp3['jwpp_start'];
			$jwpp_end = $fp3['jwpp_end'];
			$kak_lampiran = $fp3['kak_lampiran'];
		}
        
		$pr_lampiran = $this->input->post('pr_lampiran', true); // Get from hidden field
		if(empty($pr_lampiran)){
			$config['upload_path'] = './assets/lampiran/pr_lampiran';
			$config['allowed_types'] = 'pdf|doc|docx|jpeg|jpg|png';
			$config['max_size']      = 50000;

			$this->load->library('upload', $config, 'uploadprlampiran');
			$this->uploadprlampiran->initialize($config);

		
			if ( ! $this->uploadprlampiran->do_upload('pr_lampiran')) {
				$error = strip_tags($this->uploadprlampiran->display_errors());
				echo "<script>alert('Dokumen PR: " . addslashes($error) . "');</script>";
				redirect($_SERVER['HTTP_REFERER']); // Redirect to the previous URL		
			} else {
				$this->uploadprlampiran->data();
			}
			$file_name_pr  = $this->uploadprlampiran->data()['file_name'];
		}
		

		$kak_lampiran = $this->input->post('kak_lampiran', true); // Get from hidden field		
		if(empty($kak_lampiran)){
			$config_kak['upload_path'] = './assets/lampiran/kak_lampiran';
			$config_kak['allowed_types'] = 'pdf|doc|docx|jpeg|jpg|png';
			$config_kak['max_size']      = 50000;

			$this->load->library('upload', $config_kak, 'uploadkaklampiran');
			$this->uploadkaklampiran->initialize($config_kak);

			if ( ! $this->uploadkaklampiran->do_upload('kak_lampiran')) {
				$error = strip_tags($this->uploadkaklampiran->display_errors());
				echo "<script>alert('Dokumen KAK:" . $error . "');</script>";
				redirect($_SERVER['HTTP_REFERER']); // Redirect to the previous URL
			} else {
				$this->uploadkaklampiran->data();
			}
			$file_name_kak = $this->uploadkaklampiran->data()['file_name'];
		}
		
		$admin 		   = $this->session->userdata('admin');
		$param_  	   = ($admin['id_role'] == 4) ? ($param_=1) : (($admin['id_role'] == 6) ? ($param_=2) : (($admin['id_role'] == 2) ? ($param_=3) : ''));
		
		$save = $this->input->post();
		$get_usulan = $this->db->where('id_fppbj',$id_fppbj)->get('tr_analisa_risiko')->row_array();
		$usulan_lama = null;
		if (is_array($get_usulan) && isset($get_usulan['dpt_list']) && is_string($get_usulan['dpt_list']) && $get_usulan['dpt_list'] !== '') {
			$usulan_lama = json_decode($get_usulan['dpt_list']);
		}
		if (!is_object($usulan_lama)) {
			$usulan_lama = (object) array('dpt' => array(), 'usulan' => '');
		}

		if ($save['type'] != "" && $save['usulan'] != "") {
			$dpt_list['dpt']	= $save['type'];
			$usulan 			= $save['usulan'];
		}else if ($save['type'] != "") {
			$dpt_list['dpt']	= $save['type'];
			$usulan = $usulan_lama->usulan;				
		} else if ($save['usulan'] != "") {
			$dpt_list['dpt']	= $usulan_lama->dpt;
			$usulan 			= $save['usulan'];				
		} else {
			$dpt_list['dpt']	= $usulan_lama->dpt;
			$usulan 			= $usulan_lama->usulan;	
		}
		
		$dpt_list['usulan']				= $usulan;

		$save_fkpbj['dpt'] 	    		= json_encode($dpt_list);
		$save_fkpbj['id_fppbj'] 		= $id_fppbj;
		$save_fkpbj['no_pr'] 	    	= $save['no_pr'];
		$save_fkpbj['tipe_pr'] 	    	= $save['tipe_pr'];
		$save_fkpbj['pr_lampiran'] 	    = ($file_name_pr == '') ? $fppbj['pr_lampiran'] :$file_name_pr;
		$save_fkpbj['jenis_pengadaan'] 	= $save['jenis_pengadaan'];
		$save_fkpbj['id_division'] 	    = $fppbj['id_division'];
		$save_fkpbj['nama_pengadaan']   = $nama_pengadaan;
		$save_fkpbj['metode_pengadaan'] = $metode_pengadaan;
		$save_fkpbj['jwpp_start'] 		= $jwpp_start;
		$save_fkpbj['jwpp_end'] 		= $jwpp_end;
		$save_fkpbj['jwp_start'] 		= $fppbj['jwp_start'];
		$save_fkpbj['jwp_end'] 			= $fppbj['jwp_end'];
		$save_fkpbj['id_pic']		    = $fppbj['id_pic'];
		$save_fkpbj['kak_lampiran'] 	= ($file_name_kak == '') ? $kak_lampiran : $file_name_kak;
		$save_fkpbj['idr_anggaran']   	= currency($save['idr_anggaran']);
		$save_fkpbj['usd_anggaran']   	= currency($save['usd_anggaran']);
		$save_fkpbj['year_anggaran']   	= $save['year_anggaran'];
		$save_fkpbj['hps']   			= $save['hps'];
		$save_fkpbj['desc_dokumen']   	= $save['desc_dokumen'];
		$save_fkpbj['lingkup_kerja']   	= $save['lingkup_kerja'];
		$save_fkpbj['penggolongan_penyedia']   = $save['penggolongan_penyedia'];
		$save_fkpbj['desc_metode_pembayaran']  = $save['desc_metode_pembayaran'];
		$save_fkpbj['jenis_kontrak']   	= $save['jenis_kontrak'];
		$save_fkpbj['sistem_kontrak']   = json_encode($save['sistem_kontrak']);
		$save_fkpbj['is_status'] 		= 2;
		$save_fkpbj['is_approved']  	= $param_;
		$save_fkpbj['entry_stamp']  	= date('Y-m-d H:i:s');

		if ($this->$modelAlias->insert($fppbj['id'],$save_fkpbj)) {
			$by_division = $this->get_division($this->session->userdata('admin')['id_division']);
			$division = $this->get_email_division($this->session->userdata('admin')['id_division']);

			$to_ = '';
			foreach ($division as $key => $value) {
				$to_ .= $value['email'].' ,';
			}
			$to = substr($to_,substr($to_),-2);
			$subject = 'FKPBJ baru telah dibuat.';
			$message = 'FKPBJ dengan nama pengadaan '.$fppbj['nama_pengadaan'].' telah di buat oleh '.$by_division['name'];

			$activity = $this->session->userdata('admin')['name']." membuat FKPBJ dengan nama pengadaan : ".$fppbj['nama_pengadaan'];

			unset($save_fkpbj['id_fppbj']);
			unset($save_fkpbj['dpt']);

			$save_fkpbj['dpt_list'] = json_encode($dpt_list);
			$this->insertHistoryPengadaan($id_fppbj,'perubahan',$save_fkpbj);

			$this->activity_log($this->session->userdata('admin')['id_user'],$activity,$fppbj['id']);

			$this->send_mail($to, $subject, $message, $link, 'fkpbj');

			$this->db->insert('tr_analisa_risiko',array('id_fppbj' => $id_fppbj,'dpt_list' => json_encode($save['type'])));
			$data_note = array(
				'id_user' => $this->session->userdata('admin')['id_division'],
				'id_fppbj'=> $fppbj['id'],
				'value' => 'FKPBJ dengan nama pengadaan '.$fppbj['nama_pengadaan'].' telah di buat oleh '.$by_division['name'],
				'entry_stamp'=> date('Y-m-d H:i:s'),
				'is_active' => 1
			);
			$this->db->insert('tr_note',$data_note);
			$this->session->set_flashdata('msg', $this->successMessage);
			$this->deleteTemp($save);
			echo '<script>alert("Berhasil");</script>';

			// Get the HTTP_REFERER
			$referer = $_SERVER['HTTP_REFERER'];
			// Parse the URL to get the scheme and host
			$parsed_url = parse_url($referer);
			$scheme = $parsed_url['scheme'];
			$host = $parsed_url['host'];
			// Get the id from the $by_division array
			$id = $by_division['id'];
			$redirect_url = "$scheme://$host/eproc_nusantararegas/pemaketan/division/$id";
			redirect($redirect_url);
		}
	}

	public function add_fkpbj($id_fppbj)
	{
		$tabel = '';
		$valCSMS = $this->fm->getValResiko($id_fppbj);
		$data = $this->fm->selectData($id_fppbj);
		$dataFP3 = $this->fkm->getDataFp3($id_fppbj);
		if (!is_array($dataFP3)) {
			$dataFP3 = array();
		}
		$dataFP3 = array_merge(array(
			'jwpp_start' => null,
			'jwpp_end' => null,
			'jwp_start' => null,
			'jwp_end' => null,
			'metode_pengadaan' => null,
		), $dataFP3);

		if ($data['is_status'] != 1) {
			$no_pr = $data['no_pr'];

			$nama_pengadaan = $data['nama_pengadaan'];

			$desc_pengadaan = $data['desc_dokumen'];

			$getKAKLampiran = $this->getKAKLampiran($data['kak_lampiran']);

			$getPRLampiran	= $this->getPRLampiran($data['pr_lampiran']);

			$jwpp 	= $data['jwpp_start'];
			if ($jwpp != '' && $data['jwpp_end'] != '' || $jwpp != null && $data['jwpp_end'] != null) {
				$jwpp	= date('d M Y', strtotime($jwpp)) . " sampai " . date('d M Y', strtotime($data['jwpp_end']));
			} else {
				$jwpp = '-';
			}
			if ($data['jwpp_start'] != 0000 - 00 - 00 || $data['jwpp_end'] != 0000 - 00 - 00) {
				$jwpp = date('d M Y', strtotime($data['jwpp_start'])) . " sampai " . date('d M Y', strtotime($data['jwpp_end']));
			} else {
				$jwpp = '-';
			}
			if ($data['jwp_start'] != null || $data['jwp_end'] != null) {
				$jwp = date('d M Y', strtotime($data['jwp_start'])) . " sampai " . date('d M Y', strtotime($data['jwp_end']));
			} else {
				$jwp = '-';
			}
			if ($data['jwp_start'] != 0000 - 00 - 00 || $data['jwp_end'] != 0000 - 00 - 00) {
				$jwp = date('d M Y', strtotime($data['jwp_start'])) . " sampai " . date('d M Y', strtotime($data['jwp_end']));
			} else {
				$jwp = '-';
			}

			if ($data['metode_pengadaan'] == 1) {
				$status_metode = 'Pelelangan';
			} else if ($data['metode_pengadaan'] == 2) {
				$status_metode = 'Pemilihan Langsung';
			} else if ($data['metode_pengadaan'] == 3) {
				$status_metode = 'Swakelola';
			} else if ($data['metode_pengadaan'] == 4) {
				$status_metode = 'Penunjukan Langsung';
			} else {
				$status_metode = 'Pengadaan Langsung';
			}
		} else {
			$no_pr = $dataFP3['no_pr'];

			$nama_pengadaan = $dataFP3['nama_pengadaan'];

			$desc_pengadaan = $dataFP3['desc'];
			$getKAKLampiran = $this->getKAKLampiran($dataFP3['kak_lampiran']);

			$getPRLampiran	= $this->getPRLampiran($dataFP3['pr_lampiran']);

			$jwpp 	= $dataFP3['jwpp_start'];
			if ($jwpp != '' && $dataFP3['jwpp_end'] != '' || $jwpp != null && $dataFP3['jwpp_end'] != null) {
				$jwpp	= date('d M Y', strtotime($jwpp)) . " sampai " . date('d M Y', strtotime($dataFP3['jwpp_end']));
			} else {
				$jwpp = '-';
			}
			if ($dataFP3['jwpp_start'] != 0000 - 00 - 00 || $dataFP3['jwpp_end'] != 0000 - 00 - 00) {
				$jwpp = date('d M Y', strtotime($dataFP3['jwpp_start'])) . " sampai " . date('d M Y', strtotime($dataFP3['jwpp_end']));
			} else {
				$jwpp = '-';
			}
			if ($dataFP3['jwp_start'] != null || $dataFP3['jwp_end'] != null) {
				$jwp = date('d M Y', strtotime($dataFP3['jwp_start'])) . " sampai " . date('d M Y', strtotime($dataFP3['jwp_end']));
			} else {
				$jwp = '-';
			}
			if ($dataFP3['jwp_start'] != 0000 - 00 - 00 || $dataFP3['jwp_end'] != 0000 - 00 - 00) {
				$jwp = date('d M Y', strtotime($dataFP3['jwp_start'])) . " sampai " . date('d M Y', strtotime($dataFP3['jwp_end']));
			} else {
				$jwp = '-';
			}

			if ($dataFP3['metode_pengadaan'] == 1) {
				$status_metode = 'Pelelangan';
			} else if ($dataFP3['metode_pengadaan'] == 2) {
				$status_metode = 'Pemilihan Langsung';
			} else if ($dataFP3['metode_pengadaan'] == 3) {
				$status_metode = 'Swakelola';
			} else if ($dataFP3['metode_pengadaan'] == 4) {
				$status_metode = 'Penunjukan Langsung';
			} else {
				$status_metode = 'Pengadaan Langsung';
			}
		}

		if ($data['hps'] != 1) {
		   $radio_ = '<input type="radio" value="0" name="hps" class="form-control" checked required> <label>Tidak Ada</label>
		   <input type="radio" value="1" name="hps" class="form-control" required> <label for="">Ada</label>';
		} else{
			$radio_ = '<input type="radio" value="0" name="hps" class="form-control" required> <label for="">Tidak Ada</label>
					   <input type="radio" value="1" name="hps" class="form-control" checked required> <label>Ada</label>';		
		}

		$val_tipe_pr 		 = $data['tipe_pr'];
		$val_tipe_pengadaan  = $data['tipe_pengadaan'];
		$val_jenis_pengadaan = $data['jenis_pengadaan'];
		$val_metode_pengadaan = ($data['is_status'] == 1) ? $dataFP3['metode_pengadaan'] : $data['metode_pengadaan'];

		// $status_metode 	 		 		= $this->getStatusMetode($dataFP3['metode_pengadaan']);
		$radio_ 				 		= $this->getHps($data['hps']);
		$option_tipe_pr 		 		= $this->getTipePR($val_tipe_pr);
		$option_tipe_pengadaan  	 	= $this->getTipePengadaan($val_tipe_pr,$val_tipe_pengadaan);
		$option_jenis_pengadaan  		= $this->getJenisPengadaan($val_tipe_pr,$val_tipe_pengadaan,$val_jenis_pengadaan);
		$option_metode_pengadaan 		= $this->getMetodePengadaan($val_tipe_pr,$val_tipe_pengadaan,$val_metode_pengadaan);
		$option_penggolongan_penyedia 	= $this->getPenggolongan($data['penggolongan_penyedia']);
		$option_jenis_kontrak 			= $this->getJenisKontrak($data['jenis_kontrak']);
		
		$sistem_kontrak_select = json_decode($data['sistem_kontrak'], true);
		if(!is_array($sistem_kontrak_select)){
			$sistem_kontrak_select = array();
		}

		if (in_array('lumpsum', $sistem_kontrak_select)) {
			$lumpsum_select = 'selected';
		} else {
			$lumpsum_select = '';
		}

		if (in_array('unit_price', $sistem_kontrak_select)) {
			$unit_price_select = 'selected';
		} else {
			$unit_price_select = '';
		}

		if (in_array('modified', $sistem_kontrak_select)) {
			$modified_select = 'selected';
		} else {
			$modified_select = '';
		}

		if (in_array('outline', $sistem_kontrak_select)) {
			$outline_select = 'selected';
		} else {
			$outline_select = '';
		}

		if (in_array('turn_key', $sistem_kontrak_select)) {
			$turn_key_select = 'selected';
		} else {
			$turn_key_select = '';
		}

		if (in_array('sharing', $sistem_kontrak_select)) {
			$sharing_select = 'selected';
		} else {
			$sharing_select = '';
		}

		if (in_array('success_fee', $sistem_kontrak_select)) {
			$success_fee_select = 'selected';
		} else {
			$success_fee_select = '';
		}

		if (in_array('stockless', $sistem_kontrak_select)) {
			$stockless_select = 'selected';
		} else {
			$stockless_select = '';
		}

		if (in_array('on_call', $sistem_kontrak_select)) {
			$on_call_select = 'selected';
		} else {
			$on_call_select = '';
		}

		if (in_array('lain', $sistem_kontrak_select)) {
			$lain_lain_select = 'selected';
		} else {
			$lain_lain_select = '';
		}

		$tabel .= '<div class="form blockWrapper">
						<form id="formStep" action="'.site_url('fkpbj/save/'.$id_fppbj).'" method="POST" enctype="multipart/form-data">
					<div id="tab1"> 
							<fieldset class="form-group form0 " for="'.$data['no_pr'].'">
								<label for="'.$data['no_pr'].'">No.PR*</label>
								<input type="text" class="form-control" name="no_pr" value="'.$no_pr.'" required>
							</fieldset>

							<fieldset class="form-group form1 " for="'.$data['tipe_pr'].'">
								<label for="'.$data['tipe_pr'].'">Tipe PR*</label>
								<select class="form-control" name="tipe_pr" value="'.$data['tipe_pr'].'" required>
									'.$option_tipe_pr.'
								</select>
							</fieldset>

							<fieldset class="form-group   form2" for="">
								<label for="">Lampiran PR*</label>
								'.$getPRLampiran.'
							</fieldset>

							<fieldset class="form-group read_only form2 " for="'.$data['nama_pengadaan'].'">
								<label for="'.$data['nama_pengadaan'].'">Nama Pengadaan</label>
								<b>:</b>
								<span>'.$nama_pengadaan.'</span>
							</fieldset>

							<fieldset class="form-group form3 " for="'.$data['tipe_pengadaan'].'">
								<label for="'.$data['tipe_pengadaan'].'">Jenis Pengadaan*</label>
								<select class="form-control" name="tipe_pengadaan" value="'.$data['tipe_pengadaan'].'" required>
									'.$option_tipe_pengadaan.'
								</select>
							</fieldset>

							<fieldset class="form-group form4 " for="'.$data['jenis_pengadaan'].'">
								<label for="'.$data['jenis_pengadaan'].'">Jenis Detail Pengadaan*</label>
								<select class="form-control" name="jenis_pengadaan" value="'.$data['jenis_pengadaan'].'" required>
									'.$option_jenis_pengadaan.'
								</select>
							</fieldset>

							<fieldset class="form-group read_only form5 " for="'.$data['metode_pengadaan'].'">
								<label for="'.$data['metode_pengadaan'].'">Metode Pengadaan</label>
								<b>:</b>
								<span>'.$status_metode.'</span>
							</fieldset>

							<fieldset class="form-group form6 " for="'.$data['idr_anggaran'].'">
								<label for="'.$data['idr_anggaran'].'">Anggaran (IDR)*</label>
								<input type="tel" class="form-control money" name="idr_anggaran" value="'.$data['idr_anggaran'].'" required>
							</fieldset>

							<fieldset class="form-group form7 " for="'.$data['usd_anggaran'].'">
								<label for="'.$data['usd_anggaran'].'">Anggaran (USD)</label>
								<input type="tel" class="form-control money" name="usd_anggaran" value="'.$data['usd_anggaran'].'">
							</fieldset>

							<fieldset class="form-group form8 " for="'.$data['year_anggaran'].'">
								<label for="'.$data['year_anggaran'].'">Tahun Anggaran*</label>
								<input type="number" class="form-control" name="year_anggaran" value="'.$data['year_anggaran'].'" required>
							</fieldset>

							<fieldset class="form-group   form10" for="">
								<label for="">KAK / Spesifikasi Teknis</label>
								'.$getKAKLampiran.'
							</fieldset>

							<fieldset class="form-group form10 " for="'.$data['hps'].'">
								<label for="'.$data['hps'].'">Ketersediaan HPS*</label>
								<div class="radioWrapper">
									'.$radio_.'
								</div>
							</fieldset>

							<fieldset class="form-group form11 " for="'.$data['lingkup_kerja'].'">
								<label for="'.$data['lingkup_kerja'].'">Lingkup Kerja*</label>
								<textarea name="lingkup_kerja" class="form-control" required>'.$data['lingkup_kerja'].'</textarea>
							</fieldset>

							<fieldset class="form-group   form13" for="">
								<label for="">Penggolongan Penyedia Jasa (Usulan)</label>
								<select name="penggolongan_penyedia" id="" class="form-control">
									'.$option_penggolongan_penyedia.'
								</select>
							</fieldset>

							<fieldset class="form-group read_only form13 " for="'.$data['jwpp'].'"><label for="'.$data['jwpp'].'">Jangka Waktu Penyelesaian Pekerjaan ("JWPP")</label><b>:</b><span>'.$jwpp.'</span></fieldset>

							<fieldset class="form-group read_only form14 " for="'.$data['jwp'].'"><label for="'.$data['jwp'].'">Masa Pemeliharaan</label><b>:</b><span>'.$jwp.'</span></fieldset>

							<fieldset class="form-group form15" for="'.$data['desc_metode_pembayaran'].'">
								<label for="'.$data['desc_metode_pembayaran'].'">Metode Pembayaran (Usulan)*</label>
								<textarea name="desc_metode_pembayaran" class="form-control">'.$data['desc_metode_pembayaran'].'</textarea>
							</fieldset>

							<fieldset class="form-group form16 " for="'.$data['jenis_kontrak'].'">
								<label for="'.$data['jenis_kontrak'].'">Jenis Kontrak (Usulan)*</label>
								<select class="form-control" name="jenis_kontrak" value="'.$data['jenis_kontrak'].'">
									'.$option_jenis_kontrak.'
								</select>
							</fieldset>

							<fieldset class="form-group form17 " for="'.$data['sistem_kontrak'].'">
								<label for="'.$data['sistem_kontrak'].'">Sistem Kontrak (Usulan)*</label>
								<select class="form-control formMultiple" name="sistem_kontrak[]" value="'.$data['sistem_kontrak'].'" multiple>
									<option value="lumpsum" '.$lumpsum_select.'>Perikatan Harga - Lumpsum</option>
									<option value="unit_price" '.$unit_price_select.'>Perikatan Harga - Unit Price</option>
									<option value="modified" '.$modified_select.'>Perikatan Harga - Modified (lumpsum + unit price)</option>
									<option value="outline" '.$outline_select.'>Perikatan Harga - Outline Agreement</option>
									<option value="turn_key" '.$turn_key_select.'>Delivery - Turn Key</option>
									<option value="sharing" '.$sharing_select.'>Delivery - Sharing Contract</option>
									<option value="success_fee" '.$success_fee_select.'>Delivery - Success Fee</option>
									<option value="stockless" '.$stockless_select.'>Delivery - Stockless Purchasing</option>
									<option value="on_call" '.$on_call_select.'>Delivery - On Call Basic</option>
									<option value="lain" '.$lain_lain_select.'>Lain-Lain</option>
								</select>
							</fieldset>

							<fieldset class="form-group form18" for="'.$data['desc_dokumen'].'">
								<label for="'.$data['desc_dokumen'].'">Keterangan*</label>
								<textarea name="desc_dokumen" class="form-control">'.$desc_pengadaan.'</textarea>
							</fieldset>

							<fieldset class="form-group read_only formValcsms " for="'.$valCSMS.'">
							<input type="hidden" name="valcsms" value="'.$valCSMS.'">
							</fieldset>
						<div class="tab-footer">
					      <a class="button" href="#modalWrap" id="nextBtn2">Next</a>
						</div>
					</div>
					<div class="tab" id="tab2">
						<div class="tab-content">
							<fieldset class="form-group form0">
								<label style="float:left;">Daftar DPT</label>
								<div class="checkboxWrapper">
									
								</div>
							</fieldset>
						</div>
						<div class="tab-footer">
					      <a class="button" href="#modalWrap" id="prevBtn1">Previous</a>
					      <button>Simpan</button>
						</div>
					</div>
						</form>
					</div>
					</form>
				   </div>';
				   // <fieldset class="form-group form19" for="'.$data['entry_stamp'].'">
							// 	<label for="'.$data['entry_stamp'].'">Entry Date*</label>
							// 	<input type="date" name="entry_stamp" class="form-control" value="'.$data['entry_stamp'].'">
							// </fieldset>
		echo $tabel;
	}

	public function getPRLampiran($data)
	{
		// if ($data != '') {
		// 	$pr_lama = '<a href="'.base_url().'/assets/lampiran/pr_lampiran/'.$data.'" target="blank"><i class="fas fa-file"></i></a>';
		// 	$field_lampiran_pr = '<input type="file" class="form-control closeInput1" id="" name="pr_lampiran" style="display: none;">
		// 						<input class="closeHidden1" type="hidden" name="pr_lampiran" value="'.$data.'">
		// 						<div class="fileUploadBlock close1">
		// 							<i class="fa fa-upload"></i>&nbsp;
		// 								<a href="'.base_url().'assets/lampiran/pr_lampiran/'.$data.'" target="blank">
		// 								'.$data.'
		// 								</a>
		// 							<div class="deleteFile" data-id="1">
		// 							<i class="fa fa-trash"></i>
		// 							</div>
		// 						</div>';
								
		// } else{
			$pr_lama = '-';
			$field_lampiran_pr = '<input type="file" class="form-control" id="" name="pr_lampiran">';
		// }

		return $field_lampiran_pr;
	}

	public function getStatusMetode($data)
	{
		if($data == 1){
			$status_metode = 'Pelelangan';
		} else if($data == 2){
			$status_metode = 'Pemilihan Langsung';
		} else if($data == 3){
			$status_metode = 'Swakelola';
		} else if($data == 4){
			$status_metode = 'Penunjukan Langsung';
		}else{
			$status_metode = 'Pengadaan Langsung';
		}

		return $status_metode;
	}

	public function getHps($data)
	{
		if ($data != 1) {
		   $radio_ = '<input type="radio" value="0" name="hps" class="form-control" checked required> <label>Tidak Ada</label>
		   <input type="radio" value="1" name="hps" class="form-control" required> <label for="">Ada</label>';
		} else{
			$radio_ = '<input type="radio" value="0" name="hps" class="form-control" required> <label for="">Tidak Ada</label>
					   <input type="radio" value="1" name="hps" class="form-control" checked required> <label>Ada</label>';		
		}
		return $radio_;
	}

	public function getTipePR($val_tipe_pr)
	{
		if ($val_tipe_pr == 'direct_charge') {
			$option_tipe_pr ="<option value=''>Pilih Salah Satu</option><option value='direct_charge' selected>Direct Charge</option><option value='services'>Services</option><option value='user_purchase'>User Purchase</option><option value='nda'>NDA</option>";
		} else if($val_tipe_pr == 'nda'){
			$option_tipe_pr = "<option value=''>Pilih Salah Satu</option><option value='direct_charge'>Direct Charge</option><option value='services'>Services</option><option value='user_purchase'>User Purchase</option><option value='nda' selected>NDA</option>";
		} else if($val_tipe_pr == 'services'){
			$option_tipe_pr ="<option value=''>Pilih Salah Satu</option><option value='direct_charge'>Direct Charge</option><option value='services' selected>Services</option><option value='user_purchase'>User Purchase</option><option value='nda'>NDA</option>";
		} else{
			$option_tipe_pr ="<option value=''>Pilih Salah Satu</option><option value='direct_charge'>Direct Charge</option><option value='services'>Services</option><option value='user_purchase' selected>User Purchase</option><option value='nda'>NDA</option>";
		}

		return $option_tipe_pr;
	}

	public function getTipePengadaan($val_tipe_pr,$val_tipe_pengadaan)
	{
		// echo $val_tipe_pr.'-'.$val_tipe_pengadaan;
		if ($val_tipe_pr == 'direct_charge' && $val_tipe_pengadaan == 'barang') {
			$option_tipe_pengadaan ="<option value=''>Pilih Salah Satu</option><option value='barang' selected>Barang</option>";
		} else if ($val_tipe_pr == 'services' && $val_tipe_pengadaan == 'jasa') {
			$option_tipe_pengadaan ="<option value=''>Pilih Salah Satu</option><option value='jasa' selected>Jasa</option>";
		} else if ($val_tipe_pr == 'user_purchase' && $val_tipe_pengadaan == 'barang') {
			$option_tipe_pengadaan ="<option value=''>Pilih Salah Satu</option><option value='barang' selected>Barang</option>";
		} else if ($val_tipe_pr == 'user_purchase' && $val_tipe_pengadaan == 'jasa') {
			$option_tipe_pengadaan ="<option value=''>Pilih Salah Satu</option><option value='jasa' selected>Jasa</option>";
		} else if (($val_tipe_pr == 'nda' || $val_tipe_pr == 0) && $val_tipe_pengadaan == 'barang') {
			$option_tipe_pengadaan ="<option value=''>Pilih Salah Satu</option><option value='barang' selected>Barang</option>";
		} else if (($val_tipe_pr == 'nda' || $val_tipe_pr == 0) && $val_tipe_pengadaan == 'jasa') {
			$option_tipe_pengadaan ="<option value=''>Pilih Salah Satu</option><option value='jasa' selected>Jasa</option>";
		}

		return $option_tipe_pengadaan;
	}

	public function getJenisPengadaan($val_tipe_pr,$val_tipe_pengadaan,$val_jenis_pengadaan)
	{
		if ($val_tipe_pr == 'direct_charge' && $val_tipe_pengadaan == 'barang' && $val_jenis_pengadaan == 'stock') {
			$option_jenis_pengadaan ="<option value=''>Pilih Salah Satu</option><option value='stock' selected>Stock</option><option value='non_stock'>Non Stock</option>";
		} else if ($val_tipe_pr == 'direct_charge' && $val_tipe_pengadaan == 'barang' && $val_jenis_pengadaan == 'non_stock') {
			$option_jenis_pengadaan ="<option value=''>Pilih Salah Satu</option><option value='stock'>Stock</option><option value='non_stock' selected>Non Stock</option>";
		} else if ($val_tipe_pr == 'services' && $val_tipe_pengadaan == 'jasa' && $val_jenis_pengadaan == 'jasa_konstruksi') {
			$option_jenis_pengadaan ="<option value=''>Pilih Salah Satu</option><option value='jasa_konstruksi' selected>Jasa Konstruksi</option><option value='jasa_konsultasi'>Jasa Konsultasi</option><option value='jasa_lainnya'>Jasa Lainnya</option>";
		} else if ($val_tipe_pr == 'services' && $val_tipe_pengadaan == 'jasa' && $val_jenis_pengadaan == 'jasa_konsultasi') {
			$option_jenis_pengadaan ="<option value=''>Pilih Salah Satu</option><option value='jasa_konstruksi'>Jasa Konstruksi</option><option value='jasa_konsultasi' selected>Jasa Konsultasi</option><option value='jasa_lainnya'>Jasa Lainnya</option>";
		} else if ($val_tipe_pr == 'services' && $val_tipe_pengadaan == 'jasa' && $val_jenis_pengadaan == 'jasa_lainnya') {
			$option_jenis_pengadaan ="<option value=''>Pilih Salah Satu</option><option value='jasa_konstruksi'>Jasa Konstruksi</option><option value='jasa_konsultasi'>Jasa Konsultasi</option><option value='jasa_lainnya' selected>Jasa Lainnya</option>";
		} else if ($val_tipe_pr == 'user_purchase' && $val_tipe_pengadaan == 'barang' && $val_jenis_pengadaan == 'stock') {
			$option_jenis_pengadaan ="<option value=''>Pilih Salah Satu</option><option value='stock' selected>Stock</option><option value='non_stock'>Non Stock</option>";
		} else if ($val_tipe_pr == 'user_purchase' && $val_tipe_pengadaan == 'barang' && $val_jenis_pengadaan == 'non_stock') {
			$option_jenis_pengadaan ="<option value=''>Pilih Salah Satu</option><option value='stock'>Stock</option><option value='non_stock' selected>Non Stock</option>";
		} else if ($val_tipe_pr == 'user_purchase' && $val_tipe_pengadaan == 'jasa' && $val_jenis_pengadaan == 'jasa_konstruksi') {
			$option_jenis_pengadaan ="<option value=''>Pilih Salah Satu</option><option value='jasa_konstruksi' selected>Jasa Konstruksi</option><option value='jasa_konsultasi'>Jasa Konsultasi</option><option value='jasa_lainnya'>Jasa Lainnya</option>";
		} else if ($val_tipe_pr == 'user_purchase' && $val_tipe_pengadaan == 'jasa' && $val_jenis_pengadaan == 'jasa_konsultasi') {
			$option_jenis_pengadaan ="<option value=''>Pilih Salah Satu</option><option value='jasa_konstruksi'>Jasa Konstruksi</option><option value='jasa_konsultasi' selected>Jasa Konsultasi</option><option value='jasa_lainnya'>Jasa Lainnya</option>";
		} else if ($val_tipe_pr == 'user_purchase' && $val_tipe_pengadaan == 'jasa' && $val_jenis_pengadaan == 'jasa_lainnya') {
			$option_jenis_pengadaan ="<option value=''>Pilih Salah Satu</option><option value='jasa_konstruksi'>Jasa Konstruksi</option><option value='jasa_konsultasi'>Jasa Konsultasi</option><option value='jasa_lainnya' selected>Jasa Lainnya</option>";
		} else if ($val_tipe_pr == 'nda' && $val_tipe_pengadaan == 'barang' && $val_jenis_pengadaan == 'stock') {
			$option_jenis_pengadaan ="<option value=''>Pilih Salah Satu</option><option value='stock' selected>Stock</option><option value='non_stock'>Non Stock</option>";
		} else if (($val_tipe_pr == 'nda' || $val_tipe_pr == 0) && $val_tipe_pengadaan == 'barang' && $val_jenis_pengadaan == 'non_stock') {
			$option_jenis_pengadaan ="<option value=''>Pilih Salah Satu</option><option value='stock'>Stock</option><option value='non_stock' selected>Non Stock</option>";
		} else if (($val_tipe_pr == 'nda' || $val_tipe_pr == 0) && $val_tipe_pengadaan == 'jasa' && $val_jenis_pengadaan == 'jasa_konstruksi') {
			$option_jenis_pengadaan ="<option value=''>Pilih Salah Satu</option><option value='jasa_konstruksi' selected>Jasa Konstruksi</option><option value='jasa_konsultasi'>Jasa Konsultasi</option><option value='jasa_lainnya'>Jasa Lainnya</option>";
		} else if (($val_tipe_pr == 'nda' || $val_tipe_pr == 0) && $val_tipe_pengadaan == 'jasa' && $val_jenis_pengadaan == 'jasa_konsultasi') {
			$option_jenis_pengadaan ="<option value=''>Pilih Salah Satu</option><option value='jasa_konstruksi'>Jasa Konstruksi</option><option value='jasa_konsultasi' selected>Jasa Konsultasi</option><option value='jasa_lainnya'>Jasa Lainnya</option>";
		} else if (($val_tipe_pr == 'nda' || $val_tipe_pr == 0) && $val_tipe_pengadaan == 'jasa' && $val_jenis_pengadaan == 'jasa_lainnya') {
			$option_jenis_pengadaan ="<option value=''>Pilih Salah Satu</option><option value='jasa_konstruksi'>Jasa Konstruksi</option><option value='jasa_konsultasi'>Jasa Konsultasi</option><option value='jasa_lainnya' selected>Jasa Lainnya</option>";
		}

		return $option_jenis_pengadaan;
	}

	public function getMetodePengadaan($val_tipe_pr,$val_tipe_pengadaan,$val_metode_pengadaan)
	{
		$option_metode_pengadaan = "<option value=''>Pilih Salah Satu</option>";
		$val_metode_pengadaan = is_numeric($val_metode_pengadaan) ? (int) $val_metode_pengadaan : $val_metode_pengadaan;

		$is_dc_or_services = in_array($val_tipe_pr, array('direct_charge', 'service', 'services'), true);
		$is_user_purchase = ($val_tipe_pr === 'user_purchase');
		$is_nda = in_array($val_tipe_pr, array('nda', 0, '0', null, ''), true);

		if ($is_dc_or_services) {
			$selected1 = ($val_metode_pengadaan === 1) ? ' selected' : '';
			$selected2 = ($val_metode_pengadaan === 2) ? ' selected' : '';
			$selected4 = ($val_metode_pengadaan === 4) ? ' selected' : '';
			$selected5 = ($val_metode_pengadaan === 5) ? ' selected' : '';
			$option_metode_pengadaan =
				"<option value=''>Pilih Salah Satu</option>" .
				"<option value='1'{$selected1}>Pelelangan</option>" .
				"<option value='2'{$selected2}>Pemilihan Langsung</option>" .
				"<option value='4'{$selected4}>Penunjukan Langsung</option>" .
				"<option value='5'{$selected5}>Pengadaan Langsung</option>";
		} else if ($is_user_purchase) {
			$selected5 = ($val_metode_pengadaan === 5) ? ' selected' : '';
			$option_metode_pengadaan =
				"<option value=''>Pilih Salah Satu</option>" .
				"<option value='5'{$selected5}>Pengadaan Langsung</option>";
		} else if ($is_nda) {
			$selected5 = ($val_metode_pengadaan === 5) ? ' selected' : '';
			$selected3 = ($val_metode_pengadaan === 3) ? ' selected' : '';
			$option_metode_pengadaan =
				"<option value=''>Pilih Salah Satu</option>" .
				"<option value='5'{$selected5}>Pengadaan Langsung</option>" .
				"<option value='3'{$selected3}>Swakelola</option>";
		}

		return $option_metode_pengadaan;
	}

	public function getKAKLampiran($data)
	{
		if ($data != '') {
			$kak_lama = '<a href="'.base_url().'/assets/lampiran/kak_lampiran/'.$data.'" target="blank"><i class="fas fa-file"></i></a>';
			$field_lampiran_kak = '<input type="file" class="form-control closeInput2" id="" name="kak_lampiran" style="display: none;"><input class="closeHidden2" type="hidden" name="kak_lampiran" value="'.$data.'">
								<div class="fileUploadBlock close2">
									<i class="fa fa-upload"></i>&nbsp;
										<a href="'.base_url().'/assets/lampiran/kak_lampiran/'.$data.'" target="blank">
										'.$data.'
										</a>
									<div class="deleteFile" data-id="2">
									<i class="fa fa-trash"></i>
									</div>
								</div>';
			
		} else {
			$kak_lama = '-';
			$field_lampiran_kak = '<input type="file" class="form-control" id="" name="kak_lampiran">';
		}

		return $field_lampiran_kak;
	}

	public function getPenggolongan($data)
	{
		if ($data == 'perseorangan') {
			$option = ' <option value="0">Pilih Dibawah Ini</option>
						<option value="perseorangan" selected>Perseorangan</option>
						<option value="usaha_kecil">Usaha Kecil(K)</option>
						<option value="usaha_menengah">Usaha Menengah(M)</option>
						<option value="usaha_besar">Usaha Besar(B)</option>';
		} else if ($data == 'usaha_kecil') {
			$option = ' <option value="0">Pilih Dibawah Ini</option>
						<option value="perseorangan">Perseorangan</option>
						<option value="usaha_kecil" selected>Usaha Kecil(K)</option>
						<option value="usaha_menengah" >Usaha Menengah(M)</option>
						<option value="usaha_besar" >Usaha Besar(B)</option>';
		} else if ($data == 'usaha_menengah') {
			$option = ' <option value="0">Pilih Dibawah Ini</option>
						<option value="perseorangan"Perseorangan</option>
						<option value="usaha_kecil">Usaha Kecil(K)</option>
						<option value="usaha_menengah" selected>Usaha Menengah(M)</option>
						<option value="usaha_besar" >Usaha Besar(B)</option>';
		} else if ($data == 'usaha_besar') {
			$option = ' <option value="0">Pilih Dibawah Ini</option>
						<option value="perseorangan">Perseorangan</option>
						<option value="usaha_kecil">Usaha Kecil(K)</option>
						<option value="usaha_menengah">Usaha Menengah(M)</option>
						<option value="usaha_besar" selected>Usaha Besar(B)</option>';
		} else {
			$option = ' <option value="0" selected>Pilih Dibawah Ini</option>
						<option value="perseorangan">Perseorangan</option>
						<option value="usaha_kecil">Usaha Kecil(K)</option>
						<option value="usaha_menengah" >Usaha Menengah(M)</option>
						<option value="usaha_besar" >Usaha Besar(B)</option>';
		}
		return $option;
	}

	public function getJenisKontrak($data)
	{
		if ($data == 'po') {
			$option = ' <option value="0">Pilih Dibawah</option>
						<option value="po" selected>Purchase Order (PO)</option>
						<option value="GTC01">GTC01 - Kontrak Jasa Konstruksi non EPC</option>
						<option value="GTC02">GTC02 - Kontrak Jasa Konsultan</option>
						<option value="GTC03">GTC03 - Kontrak Jasa Umum</option>
						<option value="GTC04">GTC04 - Kontrak Jasa Pemeliharaan</option>
						<option value="GTC05">GTC05 - Kontrak Jasa Pembuatan Software</option>
						<option value="GTC06">GTC06 - Kontrak Jasa Sewa Fasilitas dan Alat</option>
						<option value="GTC07">GTC07 - Kontrak Jasa Tenaga Kerja.</option>
						<option value="spk">SPK</option>
						<option value="lain">Lain-Lain</option>';
		} else if ($data == 'GTC01') {
			$option = ' <option value="0">Pilih Dibawah</option>
						<option value="po">Purchase Order (PO)</option>
						<option value="GTC01" selected>GTC01 - Kontrak Jasa Konstruksi non EPC</option>
						<option value="GTC02">GTC02 - Kontrak Jasa Konsultan</option>
						<option value="GTC03">GTC03 - Kontrak Jasa Umum</option>
						<option value="GTC04">GTC04 - Kontrak Jasa Pemeliharaan</option>
						<option value="GTC05">GTC05 - Kontrak Jasa Pembuatan Software</option>
						<option value="GTC06">GTC06 - Kontrak Jasa Sewa Fasilitas dan Alat</option>
						<option value="GTC07">GTC07 - Kontrak Jasa Tenaga Kerja.</option>
						<option value="spk">SPK</option>
						<option value="lain">Lain-Lain</option>';
		} else if ($data == 'GTC02') {
			$option = ' <option value="0">Pilih Dibawah</option>
						<option value="po">Purchase Order (PO)</option>
						<option value="GTC01">GTC01 - Kontrak Jasa Konstruksi non EPC</option>
						<option value="GTC02" selected>GTC02 - Kontrak Jasa Konsultan</option>
						<option value="GTC03">GTC03 - Kontrak Jasa Umum</option>
						<option value="GTC04">GTC04 - Kontrak Jasa Pemeliharaan</option>
						<option value="GTC05">GTC05 - Kontrak Jasa Pembuatan Software</option>
						<option value="GTC06">GTC06 - Kontrak Jasa Sewa Fasilitas dan Alat</option>
						<option value="GTC07">GTC07 - Kontrak Jasa Tenaga Kerja.</option>
						<option value="spk">SPK</option>
						<option value="lain">Lain-Lain</option>';
		} else if ($data == 'GTC03') {
			$option = ' <option value="0">Pilih Dibawah</option>
						<option value="po">Purchase Order (PO)</option>
						<option value="GTC01">GTC01 - Kontrak Jasa Konstruksi non EPC</option>
						<option value="GTC02">GTC02 - Kontrak Jasa Konsultan</option>
						<option value="GTC03" selected>GTC03 - Kontrak Jasa Umum</option>
						<option value="GTC04">GTC04 - Kontrak Jasa Pemeliharaan</option>
						<option value="GTC05">GTC05 - Kontrak Jasa Pembuatan Software</option>
						<option value="GTC06">GTC06 - Kontrak Jasa Sewa Fasilitas dan Alat</option>
						<option value="GTC07">GTC07 - Kontrak Jasa Tenaga Kerja.</option>
						<option value="spk">SPK</option>
						<option value="lain">Lain-Lain</option>';
		} else if ($data == 'GTC04') {
			$option = ' <option value="0">Pilih Dibawah</option>
						<option value="po">Purchase Order (PO)</option>
						<option value="GTC01">GTC01 - Kontrak Jasa Konstruksi non EPC</option>
						<option value="GTC02">GTC02 - Kontrak Jasa Konsultan</option>
						<option value="GTC03">GTC03 - Kontrak Jasa Umum</option>
						<option value="GTC04" selected>GTC04 - Kontrak Jasa Pemeliharaan</option>
						<option value="GTC05">GTC05 - Kontrak Jasa Pembuatan Software</option>
						<option value="GTC06">GTC06 - Kontrak Jasa Sewa Fasilitas dan Alat</option>
						<option value="GTC07">GTC07 - Kontrak Jasa Tenaga Kerja.</option>
						<option value="spk">SPK</option>
						<option value="lain">Lain-Lain</option>';
		} else if ($data == 'GTC05') {
			$option = ' <option value="0">Pilih Dibawah</option>
						<option value="po">Purchase Order (PO)</option>
						<option value="GTC01">GTC01 - Kontrak Jasa Konstruksi non EPC</option>
						<option value="GTC02">GTC02 - Kontrak Jasa Konsultan</option>
						<option value="GTC03">GTC03 - Kontrak Jasa Umum</option>
						<option value="GTC04">GTC04 - Kontrak Jasa Pemeliharaan</option>
						<option value="GTC05" selected>GTC05 - Kontrak Jasa Pembuatan Software</option>
						<option value="GTC06">GTC06 - Kontrak Jasa Sewa Fasilitas dan Alat</option>
						<option value="GTC07">GTC07 - Kontrak Jasa Tenaga Kerja.</option>
						<option value="spk">SPK</option>
						<option value="lain">Lain-Lain</option>';
		} else if ($data == 'GTC06') {
			$option = ' <option value="0">Pilih Dibawah</option>
						<option value="po">Purchase Order (PO)</option>
						<option value="GTC01">GTC01 - Kontrak Jasa Konstruksi non EPC</option>
						<option value="GTC02">GTC02 - Kontrak Jasa Konsultan</option>
						<option value="GTC03">GTC03 - Kontrak Jasa Umum</option>
						<option value="GTC04">GTC04 - Kontrak Jasa Pemeliharaan</option>
						<option value="GTC05">GTC05 - Kontrak Jasa Pembuatan Software</option>
						<option value="GTC06" selected>GTC06 - Kontrak Jasa Sewa Fasilitas dan Alat</option>
						<option value="GTC07">GTC07 - Kontrak Jasa Tenaga Kerja.</option>
						<option value="spk">SPK</option>
						<option value="lain">Lain-Lain</option>';
		} else if ($data == 'GTC07') {
			$option = ' <option value="0">Pilih Dibawah</option>
						<option value="po">Purchase Order (PO)</option>
						<option value="GTC01">GTC01 - Kontrak Jasa Konstruksi non EPC</option>
						<option value="GTC02">GTC02 - Kontrak Jasa Konsultan</option>
						<option value="GTC03">GTC03 - Kontrak Jasa Umum</option>
						<option value="GTC04">GTC04 - Kontrak Jasa Pemeliharaan</option>
						<option value="GTC05">GTC05 - Kontrak Jasa Pembuatan Software</option>
						<option value="GTC06">GTC06 - Kontrak Jasa Sewa Fasilitas dan Alat</option>
						<option value="GTC07" selected>GTC07 - Kontrak Jasa Tenaga Kerja.</option>
						<option value="spk">SPK</option>
						<option value="lain">Lain-Lain</option>';
		} else if ($data == 'spk') {
			$option = ' <option value="0">Pilih Dibawah</option>
						<option value="po">Purchase Order (PO)</option>
						<option value="GTC01">GTC01 - Kontrak Jasa Konstruksi non EPC</option>
						<option value="GTC02">GTC02 - Kontrak Jasa Konsultan</option>
						<option value="GTC03">GTC03 - Kontrak Jasa Umum</option>
						<option value="GTC04">GTC04 - Kontrak Jasa Pemeliharaan</option>
						<option value="GTC05">GTC05 - Kontrak Jasa Pembuatan Software</option>
						<option value="GTC06">GTC06 - Kontrak Jasa Sewa Fasilitas dan Alat</option>
						<option value="GTC07">GTC07 - Kontrak Jasa Tenaga Kerja.</option>
						<option value="spk" selected>SPK</option>
						<option value="lain">Lain-Lain</option>';
		} else if ($data == 'lain') {
			$option = ' <option value="0">Pilih Dibawah</option>
						<option value="po">Purchase Order (PO)</option>
						<option value="GTC01">GTC01 - Kontrak Jasa Konstruksi non EPC</option>
						<option value="GTC02">GTC02 - Kontrak Jasa Konsultan</option>
						<option value="GTC03">GTC03 - Kontrak Jasa Umum</option>
						<option value="GTC04">GTC04 - Kontrak Jasa Pemeliharaan</option>
						<option value="GTC05">GTC05 - Kontrak Jasa Pembuatan Software</option>
						<option value="GTC06">GTC06 - Kontrak Jasa Sewa Fasilitas dan Alat</option>
						<option value="GTC07">GTC07 - Kontrak Jasa Tenaga Kerja.</option>
						<option value="spk">SPK</option>
						<option value="lain" selected>Lain-Lain</option>';
		} else {
			$option = ' <option value="0" selected>Pilih Dibawah</option>
						<option value="po">Purchase Order (PO)</option>
						<option value="GTC01">GTC01 - Kontrak Jasa Konstruksi non EPC</option>
						<option value="GTC02">GTC02 - Kontrak Jasa Konsultan</option>
						<option value="GTC03">GTC03 - Kontrak Jasa Umum</option>
						<option value="GTC04">GTC04 - Kontrak Jasa Pemeliharaan</option>
						<option value="GTC05">GTC05 - Kontrak Jasa Pembuatan Software</option>
						<option value="GTC06">GTC06 - Kontrak Jasa Sewa Fasilitas dan Alat</option>
						<option value="GTC07">GTC07 - Kontrak Jasa Tenaga Kerja.</option>
						<option value="spk">SPK</option>
						<option value="lain">Lain-Lain</option>';
		}

		return $option;
	}
	
	function showUser() {
		$query = " SELECT 
						a.username,
						a.raw_password,
						b.name
					FROM
						ms_user a
					INNER JOIN
						tb_division b ON b.id=a.id_division";
	}
}	
