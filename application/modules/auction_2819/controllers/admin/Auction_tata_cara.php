<?php
class Auction_tata_cara extends CI_Controller{
	
	function __construct(){
		parent::__construct();
		
		$this->load->model('auction_package/tata_cara_model');
		$this->load->model('auction_package/syarat_model');
		$this->load->model('auction_package/barang_model');
	}
	
	function index($id_lelang = ''){
		$fill = $this->tata_cara_model->select_data($id_lelang);
		
		$data['content'] = $content = "form/auction_package/auction_tata_cara";
		$data['action'] = "save";
				
		$data['hps'] = $this->form->money('hps', $fill['hps']);
		$data['id_lelang'] = $id_lelang; 
		$data['id'] = $fill['id'];
		
		if($fill){ $data = $fill; $data['action'] = "edit"; $content = "form/auction_package/auction_tata_cara_master"; }
		
		$this->load->view($content, $data);
	}
	
	function form($id_lelang = ''){
		$fill = $this->tata_cara_model->select_data($id_lelang);
		
		$data['content'] = "form/auction_package/auction_tata_cara";
		$data['action'] = "edit";
		$data['width'] = 600;
		
		$data['hps'] = $this->form->money('hps', $fill['hps']);
		$data['metode_lelang'] = $fill['metode_lelang'];
		$data['metode_penawaran'] = $fill['metode_penawaran'];
		
		$data['id_lelang'] = $id_lelang; 
		$data['id'] = $fill['id'];
		
		$this->load->view('jc-table/form/jc-form', $data);
	}
	
	function save(){
		$param = array(
			'id_lelang' => $_POST['id_lelang'],
			'hps' => $_POST['hps'],
			'metode_lelang' => $_POST['metode_lelang'],
			'metode_penawaran' => $_POST['metode_penawaran'],
			'entry_stamp' => date("Y-m-d H:i:s")
		);
		
		$param['hps'] = str_replace(",","",$param['hps']);
		$param['nilai_perkiraan'] = str_replace(",","",$param['nilai_perkiraan']);
		$param['anggaran'] = str_replace(",","",$param['anggaran']);
		
		$this->tata_cara_model->save($param);
		$this->set_syarat($_POST['id_lelang']);
		if($_POST['metode_penawaran'] == "lump_sum") $this->set_barang($_POST['id_lelang']);
		
		$json = array(
			'status' => 'success',
			'message' => 'Data tata cara auction telah di simpan !'
		);
		die(json_encode($json));
		
	}
	
	function edit(){
		$param = array(
			'hps' => $_POST['hps'],
			'metode_lelang' => $_POST['metode_lelang'],
			'metode_penawaran' => $_POST['metode_penawaran'],
			'edit_stamp' => date("Y-m-d H:i:s"),
			'id_lelang' => $_POST['id_lelang']
		);

		$param['hps'] = str_replace(",","",$param['hps']);
		
		$this->tata_cara_model->edit($param);
		$this->set_syarat($_POST['id_lelang'], true);
		
		$json = array(
			'status' => 'success',
			'message' => 'Data tata cara auction telah di edit !'
		);
		die(json_encode($json));
	}
	
	function set_barang($id_lelang = ''){
		$master = $this->syarat_model->get_master($id_lelang);
		
		$param = array(
			'id_lelang' => $id_lelang,
			'name' => $master['nama'],
			'id_kurs' => '',
			'hps' => '',
			'use_tax' => '',
			'volume' => '',
			'entry_stamp' => date("Y-m-d H:i:s")
		);
		
		$this->barang_model->save($param);	
	}
	
	function set_syarat($id_lelang = '', $is_edit = false){
		$master = $this->syarat_model->get_master($id_lelang);
				
		if($master['type_lelang'] == "reverse"){ $limit = "minimum"; $indicator = "rendah"; $reverse = "tinggi"; }
		else if($master['type_lelang'] == "forward"){ $limit = "maximum"; $indicator = "tinggi"; $reverse = "rendah"; }
		
		$value = '
			<ol>
				<li>Harga penawaran sudah termasuk pajak-pajak.</li>
				<li>Durasi auction selama '.$master['duration'].' menit, tanpa ada penambahan waktu.</li>
				<li>';
		
				if($master['metode_lelang'] == "ranking")
					$value .= 'Metode auction menggunakan metode posisi/rangking, dimana para peserta e-auction hanya
					mengetahui posisi/rangking dari penawaran harga yang telah dimasukkan dibandingkan dengan
					penawaran harga peserta e-auction lainnya.';
					
				if($master['metode_lelang'] == "penawaran_terendah")
					$value .= 'Metode auction menggunakan metode indikator, dimana para peserta e-auction akan
					diberikan indikator terhadap penawaran harga yang telah dimasukan, dibandingkan dengan penawaran harga 
					peserta e-auction lainnya.';
		
		$value .= '</li>
				<li>Tidak ada batas harga penawaran '.$limit.' yang dapat dimasukkan.</li>
				<li>Harga penawaran yang dimasukkan tidak boleh sama atau lebih '.$reverse.' dari harga penawaran yang telah dimasukkan sebelumnya.</li>
				<li>';
		if($master['metode_lelang'] == "ranking")
			$value .= 'Apabila terdapat penawaran harga yang sama, maka posisi/rangking yang lebih tinggi akan diberikan kepada penawar harga te'.$indicator.' yang masuk terlebih dahulu'; 
		if($master['metode_lelang'] == "penawaran_terendah")
			$value .= 'Apabila terdapat penawaran harga yang sama, maka indikator lambang medali akan diberikan kepada penawar harga te'.$indicator.' yang masuk terlebih dahulu'; 
		
		$value .= '</li>
				<li>Selama auction berlangsung, peserta tidak diperkenankan menggunakan tombol Back (backspace) dan Refresh (F5). </li>
				<li>Selama auction berlangsung, para peserta dilarang saling berkomunikasi dan dilarang menggunakan alat komunikasi apapun.</li>
				<li>Seluruh peserta auction wajib menjaga ketertiban.</li>
			</ol>';			 
		$data['action'] = "save"; 
		
		if(!$is_edit)
			$param = array(
				$value,
				$id_lelang,
				date("Y-m-d H:i:s")	
			);
		else
			$param = array(
				$value,
				date("Y-m-d H:i:s"),
				$id_lelang
			);
		
		$this->syarat_model->save($is_edit, $param);
	}
}