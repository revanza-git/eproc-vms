<?php
require_once "cron_dpt.php";

class cek_blacklist extends cron{
	
	private $dpt;
	
	function __construct(){
		parent::__construct();
		
		$this->dpt = new dpt();
	}
	
	
	function index(){
		$sql = "SELECT * FROM tr_blacklist WHERE end_date = '".date('Y-m-d')."'";

		$sql = $this->query($sql);

		foreach($this->result($sql) as $data){

			$sql_1 = "	SELECT 	a.*, 
								a.id, 
								b.name vendor_name,
								b.id id_vendor,
								d.name legal_name,
								e.username email
						FROM ".$data['doc_type']." a 

						LEFT JOIN ms_vendor b ON a.id_vendor = b.id 
						LEFT JOIN ms_vendor_admistrasi c ON c.id_vendor = b.id 
						LEFT JOIN tb_legal d ON c.id = c.id_legal 
						LEFT JOIN ms_login e ON (e.id_user = b.id AND type = 'user')

						WHERE a.id = ".$data['id_doc'];
			// echo $sql_1;
			$sql_1 = $this->query($sql_1);
			$sql_1 = $this->row_array($sql_1);
			$message = "Kepada ".$sql_1['legal_name']." ".$sql_1['vendor_name'].". Perusahaan anda telah kembali menjadi vendor dalam Sistem Aplikasi Manajemen Penyedia Barang / Jasa PT Nusantara Regas.
			Tolong lengkapi segera berkas untuk dapat diangkat kembali menjadi DPT. 
			Terimakasih. 


			PT Nusantara Regas";

			$this->send_email($sql_1['email'], "Penghapusan Daftar Hitam PT Nusantara Regas", $message);	
			$query = "UPDATE tr_blacklist SET del = 1 WHERE id_vendor = ".$sql_1['id'];
			$query = $this->query($query);
			if($query){
				$query = "UPDATE ms_vendor SET is_active = 1 WHERE id_vendor = ".$sql_1['id'];
				$query = $this->query($query);
			}
			// print_r($sql_1);
		}
	}
	function __destruct(){
		mysql_close();
	}
}
	
	

$execute = new cek_blacklist();
$execute->index();
