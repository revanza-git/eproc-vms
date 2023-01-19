<?php

class Auction_package extends CI_Controller{

	protected $table_setting;
	

	function __construct(){
		parent::__construct();
	
		$this->load->model('auction_package/auction_model');
		
		$this->table_setting = array(
			"param" => array(
				'nama' => array(
					'name' => 'Nama Paket',
					'header' => 'nama',
					'field' => 'nama',
					'type' => 'text',
					'width' => 400
				),
				'tahun_anggaran' => array(
					'name' => 'Tahun Anggaran',
					'header' => 'tahun_anggaran',
					'field' => 'tahun_anggaran',
					'type' => 'text',
					'width' => 100
				),
				'start_date' => array(
					'name' => 'Tanggal',
					'header' => 'start_date',
					'field' => 'start_date',
					'type' => 'date',
					'width' => 100
				),
				'lokasi' => array(
					'name' => 'Lokasi',
					'header' => 'nama_pejabat_pengadaan',
					'field' => 'c.name',
					'type' => 'text',
					'width' => 300
				)
				
			),
			"setting" => array(
				'class_name'	=> 'auction_package',
				'table' 		=> 'table',
				'model' 		=> 'auction_package/auction_model',
				'add'			=> 'form',
				'custom_edit'	=> 'openAuctionPackage',
				'delete'		=> 'delete',
				'duplicate'		=> 'duplicate_lelang',
				'export'		=> false
			)		
		);
	}
	
	
	function index($mode = ''){
		if($mode) $_SESSION['auction_pgn_beranda_selector'] = $mode;
		
		if($mode == "finish" or $mode == "all")
			unset($this->table_setting['setting']['add']);
		
		if($mode == "all")
			unset($this->table_setting['setting']['duplicate']);
			
		$data['table_setting'] = $this->table_setting;
		
		$this->load->view('template/table_master', $data);
	}
	
	function table(){
		if($_SESSION['auction_pgn_beranda_selector'] == "all")
			unset($this->table_setting['setting']['duplicate']);
			
		$this->jctable->generate_table($this->table_setting);
	}

	function form_duplicate($id = ''){
		$data['content'] = "form/auction_package/auction_duplicate";
		$data['width'] = "400";
		
		$data['id'] = $this->form->hidden('id', $id);
		$data['total'] = $this->form->text_box('total', '', array(3,3));
		
		$this->load->view("jc-table/form/jc-form", $data);
	}
	
	function duplicate_auction(){
		$total = $_POST['total'];
		$id = $_POST['id'];
		
		$this->auction_model->duplicate_data($total, $id);
		
		$json = array(
			'status' => 'success',
			'message' => 'Data paket auction telah di duplikasi !'
		);
		die(json_encode($json));
	}
	
	function view($id = ''){
		$data['fill'] = $fill = $this->auction_model->select_data($id);
		$data['multi_tab'] = array(
			array(
				'title' => 'Satuan/Unit/Area Kerja Pengguna Barang/Jasa',
				'url' => 'auction_sbu/index/'.$id
			),	
			array(
				'title' => 'Tata Cara Auction',
				'url' => 'auction_tata_cara/index/'.$id
			),
			array(
				'title' => 'Persyaratan Auction',
				'url' => 'auction_syarat/index/'.$id
			),
			array(
				'title' => 'Peserta Auction',
				'url' => 'auction_peserta/index/'.$id
			),
			array(
				'title' => 'Kurs Auction',
				'url' => 'auction_kurs/index/'.$id
			),
			array(
				'title' => 'Barang/Jasa Auction',
				'url' => 'auction_barang/index/'.$id
			)
		);
		
		$this->load->view("content/auction_package/master", $data);
	}
	
	function form($id = ''){
		$data['action'] = "save";
		if($id){
			$data['action'] = "edit";	
			$fill = $this->auction_model->select_data($id);
		}
		
		$data['content'] = "form/auction_package/auction_package";
		$data['width'] = 850;
		
		$data['nama'] = $this->form->text_box('nama', $fill['nama'], array(50, 200));

		$setting = array(
			'db' => 'vm_pgn.tb_pengguna',
			'id' => 'id',
			'value' => 'name'
		);
		$data['id_pengguna'] = $this->form->drop_down_db('id_pengguna', $fill['id_pengguna'], $setting);

		$setting = array(
			array('name' => 'Anggaran Investasi', 'value' => 'Anggaran Investasi'),
			array('name' => 'Anggaran Operasi', 'value' => 'Anggaran Operasi'),
			array('name' => 'Anggaran Investasi & Operasi', 'value' => 'Anggaran Investasi & Operasi'),
			array('name' => 'Belum tersedia anggaran', 'value' => 'Belum tersedia anggaran')
		);
		$data['sumber_anggaran_apgn'] = $this->form->drop_down('sumber_anggaran_apgn', $setting, '', $fill['sumber_anggaran']);
		

		$setting = array(
			array('name' => 'DIP/APBN', 'value' => 'DIP/APBN'),
			array('name' => 'World Bank', 'value' => 'World Bank'),
			array('name' => 'ADB', 'value' => 'ADB'),
			array('name' => 'Exim Bank', 'value' => 'Exim Bank'),
			array('name' => 'EIB', 'value' => 'EIB'),
			array('name' => 'JEXIM', 'value' => 'JEXIM'),
			array('name' => 'JBIC', 'value' => 'JBIC'),
			array('name' => 'Lainnya', 'value' => 'other'),
		);
		$data['sumber_anggaran_non_apgn'] = $this->form->drop_down('sumber_anggaran_non_apgn', $setting, '', $fill['sumber_anggaran']);
		
		$setting = array(
			'db' => 'vm_pgn.tb_pejabat_pengadaan',
			'id' => 'id',
			'value' => 'name'
		);
		$data['id_pejabat_pengadaan'] = $this->form->drop_down_db('id_pejabat_pengadaan', $fill['id_pejabat_pengadaan'], $setting);
		
		$data['remark'] = $this->form->text_area('remark', $fill['remark'], array(30, 3));
		
		$data['start_date'] = $this->form->calendar('start_date', $fill['start_date']);
		$data['end_date'] = $this->form->calendar('end_date', $fill['end_date']);
		
		$data['start_hour'] = $this->form->time('start_hour', $fill['start_hour']);
		$data['end_hour'] = $this->form->time('end_hour', $fill['end_hour']);
		
		$data['tahun_anggaran'] = $this->form->text_box('tahun_anggaran', $fill['tahun_anggaran'], array(4,10));
		
		$setting = array(
			'db' => 'ms_area_sbu',
			'id' => 'id',
			'value' => 'name'
		);
		$data['id_lokasi'] = $this->form->drop_down_db('id_lokasi', $fill['id_lokasi'], $setting);
		$data['budget_spender'] = $this->form->drop_down_db('budget_spender', $fill['budget_spender'], $setting);
		
		$data['type_lelang'] = $fill['type_lelang'];
		$data['sumber_anggaran_lain'] = $fill['sumber_anggaran_lain'];
		$data['duration'] = $this->form->text_box('duration', $fill['duration'], array(3 ,4));
		$data['lokasi_bidding'] = $this->form->text_box('lokasi_bidding', $fill['lokasi_bidding'], array(30 , 50));
		
		$data['type_lelang'] = $fill['type_lelang'];
		$data['anggaran'] = $fill['anggaran'];
		$data['lokasi'] = $fill['lokasi'];
		$data['id'] = $fill['id'];
		
		$this->load->view('jc-table/form/jc-form', $data);
	}
	
	function save(){
		if($_POST['sumber_anggaran_apgn']) $_POST['sumber_anggaran'] = $_POST['sumber_anggaran_apgn'];
		if($_POST['sumber_anggaran_non_apgn']) $_POST['sumber_anggaran'] = $_POST['sumber_anggaran_non_apgn'];
		
		$param = array(
			'nama' => $_POST['nama'],
			'tahun_anggaran' => $_POST['tahun_anggaran'],
			'anggaran' => $_POST['anggaran'],
			'sumber_anggaran' => $_POST['sumber_anggaran'],
			'sumber_anggaran_lain' => $_POST['sumber_anggaran_lain'],
			'start_date' => $_POST['start_date'],
			'end_date' => $_POST['start_date'],
			'start_hour' => $_POST['start_hour'],
			'end_hour' => $_POST['end_hour'],
			'duration' => $_POST['duration'],
			'type_lelang' => $_POST['type_lelang'],
			'id_sbu' => $this->utility->get_userdata('id_sbu'),
			'id_pejabat_pengadaan' => $_POST['id_pejabat_pengadaan'],
			'id_pengguna' => $_POST['id_pengguna'],
			'lokasi' => $_POST['lokasi'],
			'id_lokasi' => $_POST['id_lokasi'],
			'lokasi_bidding' => $_POST['lokasi_bidding'],
			'remark' => $_POST['remark'],
			'session_id' => $this->utility->session_id(),
			'entry_stamp' => date("Y-m-d H:i:s")
		);
		$id = $this->auction_model->save_data($param);
		
		$json = array(
			'status' => 'success',
			'message' => 'Data paket auction telah di simpan !',
			'id' => $id
		);
		die(json_encode($json));
	}
	
	function edit(){
		$param = array(
			'nama' => $_POST['nama'],
			'tahun_anggaran' => $_POST['tahun_anggaran'],
			'anggaran' => $_POST['anggaran'],
			'start_date' => $_POST['start_date'],
			'end_date' => $_POST['start_date'],
			'start_hour' => $_POST['start_hour'],
			'end_hour' => $_POST['end_hour'],
			'duration' => $_POST['duration'],
			'type_lelang' => $_POST['type_lelang'],
			'id_pejabat_pengadaan' => $_POST['id_pejabat_pengadaan'],
			'id_pengguna' => $_POST['id_pengguna'],
			'lokasi' => $_POST['lokasi'],
			'id_lokasi' => $_POST['id_lokasi'],
			'lokasi_bidding' => $_POST['lokasi_bidding'],
			'remark' => $_POST['remark'],
			'edit_stamp' => date("Y-m-d H:i:s"),
			'id' => $_POST['id']
		);
		$id = $this->auction_model->edit_data($param);
		
		$json = array(
			'status' => 'success',
			'message' => 'Data paket auction telah di simpan !',
			'is_edit' => true
		);
		die(json_encode($json));
	}

	function post(){
		$this->load->view('content/auction_package/post', $data);
	}
	
	function reset($id_lelang = ""){
		$this->auction_model->reset_penawaran($id_lelang);
		die(json_encode(array('status' => 'success')));
	}
	
	function delete($id = ''){
		$this->auction_model->delete($id);
		
		$json = array(
			'status' => 'success',
			'message' => 'Data paket auction telah di hapus !'
		);
		die(json_encode($json));
	}
}