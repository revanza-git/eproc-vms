<?php defined('BASEPATH') OR exit('No direct script access allowed');

class admin_reporting extends CI_Controller {
	public function __construct(){
		parent::__construct();
		if(!$this->session->userdata('admin')){
			redirect(site_url());
		}
		$this->load->model('report/admin_report_model','arm');
		$this->load->model('pengadaan_model','pm');
		$this->load->helper('form');




		$this->table_preview = array(
			'nama'				=> array('name' => 'Nama Perusahaan', 'header' => 'nama', 'field' => 'a.nama', 'width' => '250'),
			'badan_usaha'		=> array('name' => 'Badan Usaha', 'header' => 'badan_usaha', 'field' => 'bf.name', 'width' => '100'),
			'alamat'			=> array('name' => 'Alamat', 'header' => 'alamat', 'field' => 'a.alamat', 'width' => '250'),
			'kota'				=> array('name' => 'Kota', 'header' => 'kota', 'field' => 'af.name', 'width' => '100'),
			'administrasi_sbu'	=> array('name' => 'Satuan Unit/Kerja', 'header' => 'administrasi_sbu','field' => 'i.name','type' => 'text','width' => '250'),
			'no_telp' 			=> array('name' => 'No. Telp', 'header' => 'no_telp', 'field' => 'a.no_telp', 'width' => '150'),
			'fax' 				=> array('name' => 'No. Fax', 'header' => 'fax', 'field' => 'a.fax', 'width' => '150'),
			'email' 			=> array('name' => 'Email', 'header' => 'email', 'field' => 'a.email', 'width' => '200'),
			'npwp' 				=> array('name' => 'NPWP', 'header' => 'npwp', 'field' => 'a.npwp', 'width' => '150'),
			'nppkp' 			=> array('name' => 'NPPKP', 'header' => 'nppkp', 'field' => 'a.nppkp', 'width' => '150'),
			'tdp' 				=> array('name' => 'TDP', 'header' => 'tdp', 'width' => '300'),
			'bsb' 				=> array('name' => 'Klasifikasi', 'header' => 'bsb', 'width' => '300'),
			'akta' 				=> array('name' => 'Akta Perusahaan', 'header' => 'akta', 'width' => '500'),
			'pengurus' 			=> array('name' => 'Pengurus', 'header' => 'pengurus', 'width' => '300'),
			'asosiasi' 			=> array('name' => 'Sertifikat Asosiasi/Lainnya', 'header' => 'asosiasi', 'width' => '400'),
			'situ'				=> array('name' => 'SITU/Keterangan Domisili', 'header' => 'situ', 'width' => '600'),
			'siup'				=> array('name' => 'SIUP', 'header' => 'siup', 'width' => '500'),
			'siujk' 			=> array('name' => 'SIUJK', 'header' => 'siujk', 'width' => '500'),
			'sbu' 				=> array('name' => 'SBU', 'header' => 'sbu', 'width' => '600'),	
			'ijin_lain' 		=> array('name' => 'Surat Ijin Usaha Lainnya', 'header' => 'ijin_lain', 'width' => '600'),
			'agen' 				=> array('name' => 'Pabrikan/Keagenan/ Distributor', 'header' => 'agen', 'width' => '600')
		);
	}
	


	public function index(){

		$vld = 	array(
			array(
				'field'=>'report[]',
				'label'=>'Pilihan report',
				'rules'=>'required'
				)
			);
		
		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			unset($_POST['print']);
			

			$input = $this->input->post();
			$this->content($input);
		}else{

			$layout['content']	= $this->load->view('report/custom',null,TRUE);

			$item['header'] 	= $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
			$item['content'] 	= $this->load->view('admin/dashboard',$layout,TRUE);
			$this->load->view('template',$item);
		}
	}



	function filter_parameter(){
		return array( 
			'administrasi' =>
			array(
				'label' => 'Administrasi',	
				'field' => array(
					'administrasi_nama'	=> array('name' => 'Nama Badan Usaha', 'header' => 'administrasi_nama', 'field' => 'a.nama','type' => 'text'),
					'badan_usaha' => array('name' => 'Badan Hukum', 'header' => 'badan_usaha', 'field' => 'bg.name','type' => 'text'),
					'website' => array('name' => 'Website', 'header' => 'website', 'field' => 'a.website','type' => 'text'),
					'administrasi_sbu' => array('name' => 'Satuan Unit/Kerja', 'header' => 'administrasi_sbu','field' => 'i.name','type' => 'text'),
					'npwp' 	=> array('name' => 'NPWP', 'header' => 'npwp', 'field' => 'a.npwp','type' => 'text'),
					'nppkp' => array('name' => 'NPPKP', 'header' => 'nppkp', 'field' => 'a.nppkp','type' => 'text'),
					'administrasi_kategori' => array('name' => 'Kategori', 'header' => 'administrasi_kategori', 'field' => 'c.name','type' => 'text'),
					'alamat' => array('name' => 'Alamat', 'header' => 'alamat', 'field' => 'a.alamat','type' => 'text'),
					'kota' => array('name' => 'Kota', 'header' => 'kota', 'field' => 'af.name','type' => 'text'),
					'email' => array('name' => 'Email', 'header' => 'email', 'field' => 'a.email','type' => 'text'),
					'website' => array('name' => 'Website', 'header' => 'website', 'field' => 'a.website','type' => 'text'),
				),
			),
			'akta' => 
			array(
				'label' => 'Akta Perusahaan',
				'field' => array(
					'no_akta' => array('name' => 'No. Akta', 'header' => 'no_akta', 'field' => 'k.no_akta','type' => 'text'),
				)
			),
		);
	}


	function content($input=''){
		$this->load->library('form');
		$this->load->library('datatables');


		$search = $this->input->get('q');
		$page = '';
		
		$per_page=10;

		$sort = $this->utility->generateSort(array('name','address','type','city'));

		$data['sort'] = $sort;

			foreach ($input as $key => $value) {
				foreach ($value as $zero) {
					if ($zero == "name"){
						$data['name'] = $this->arm->get_name();
					}
					elseif ($zero == "address") {
						$data['address'] = $this->arm->get_address();
					}
					elseif ($zero == "type") {
						// $data['type'] = $this->arm->get_address();
					}
					elseif ($zero == "city") {
						$data['city'] = $this->arm->get_city();
					}

				}
				
			}


		$layout['content']	= $this->load->view('report/content',$data,TRUE);

		$item['header'] 	= $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		$item['content'] 	= $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}


	function asd($input=''){
		
	}
	
}