<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Cron extends CI_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->library('email');
	}
	public function index(){
		
	}
	public function drop_dpt(){
		error_reporting(E_ALL);
		/*
			Query dari tr_email_blast untuk mengambil data email yang akan dikirim
		*/
		$query = "SELECT * FROM tr_email_blast WHERE DATE(`date`) <= DATE('".date('Y-m-d')."') AND id_doc != 'ms_pengurus' GROUP BY id_doc";
		$query = $this->db->query($query)->result_array();
		foreach($query as $key => $value){
			$sql = "SELECT a.*, b.id_vendor, c.name nama_vendor FROM tr_email_blast a JOIN ".$value['doc_type']." b ON a.id_doc=b.id JOIN ms_vendor c ON c.id=b.id_vendor WHERE a.id = ".$value['id']." ";
$query_[]= $this->db->query($sql)->row_array();
		}
		print_r($query_);die;
		foreach ($query as $key => $value) {
			if($value['id_doc']!=''){
				$queryDocument = "SELECT * FROM ".$value['doc_type']." WHERE id = ".$value['id_doc'];
				$queryDocument = $this->db->query($queryDocument);
				$_queryDocument = $queryDocument->row_array();
				$data_vendor = "SELECT *,c.name badan_usaha, a.name name  FROM ms_vendor a JOIN ms_vendor_admistrasi b ON a.id = b.id_vendor JOIN tb_legal c ON b.id_legal = c.id JOIN ms_vendor_pic d ON a.id = d.id_vendor WHERE a.id = ?";
				$data_vendor = $this->db->query($data_vendor, array($_queryDocument['id_vendor']))->row_array();
				
				if($value['distance']==0){ 
					if($value['doc_type']=='ms_ijin_usaha'){
						foreach($queryDocument->result_array() as $keyDocument => $valueDocument){
							$selectTransaction = $this->db->where('id_vendor', $valueDocument['id_vendor'])->where('id_dpt_type', $valueDocument['id_dpt_type'])->where('end_date IS NULL')->get('tr_dpt');

							$queryTransaction = $this->db->where('id_vendor', $valueDocument['id_vendor'])->where('id_dpt_type', $valueDocument['id_dpt_type'])->where('end_date IS NULL')->update('tr_dpt', array('status'=>2,'end_date'=>date('Y-m-d H:i:s')));

						

							if($this->db->affected_rows()>0 || $selectTransaction->num_rows()==0){
								$this->db->where('id_vendor' , $valueDocument['id_vendor'])->where('id_dpt_type',$valueDocument['id_dpt_type'])->update('tr_dpt',array(
										'end_date'=> date('Y-m-d H:i:s'), 
										'status'=>2
									)
								);
								$this->db->insert('tr_dpt',array(
									'id_vendor' => $valueDocument['id_vendor'], 
									'id_dpt_type'=> $valueDocument['id_dpt_type'],
									'status'=>0,
									'entry_stamp'=>date('Y-m-d H:i:s')
								));
							}

							//Apakah masih ada DPT yang aktif?
							$queryDPTCheck = $this->db->where(
								array('id_vendor'=> $valueDocument['id_vendor'],
								'status'=> 1,
								'end_date'=>NULL,
								'start_date'=>NULL))->get('tr_dpt');

							//Turunkan dari DPT
							if($queryDPTCheck->num_rows()==0){
								$this->db->where('id', $valueDocument['id_vendor'])->update('ms_vendor', array('vendor_status'=> 1));
							}
							
						}
						
					}else{
						
						foreach($queryDocument->result_array() as $keyDocument => $valueDocument){
								
							$queryTransaction = $this->db->where('id_vendor', $valueDocument['id_vendor'])->where('end_date IS NULL')->update('tr_dpt', array('status'=>2,'end_date'=>date('Y-m-d H:i:s')));
							$selectDPT = $this->db->query("SELECT * FROM tr_dpt WHERE id_vendor = ? GROUP BY id_dpt_type", array($valueDocument['id_vendor']))->result_array();

						

							if($this->db->affected_rows()>0){
								foreach($selectDPT as $keys=>$values){
									$this->db->insert('tr_dpt',array(
										'id_vendor' => $valueDocument['id_vendor'], 
										'id_dpt_type'=> $values['id_dpt_type'],
										'status'=>0,
										'entry_stamp'=>date('Y-m-d H:i:s')
									));
								}
								
							}
							$this->db->where('id', $valueDocument['id_vendor'])->update('ms_vendor', array('vendor_status'=> 1));
							
						}
					}
				
				}
				$value['message'] = str_replace('\n','<br>',$value['message']);
				$message = "
					Kepada Yth. <br/>
					".$data_vendor['badan_usaha']." ".$data_vendor['name'].", <br/><br/>
					
					Bersama ini disampaikan data  perusahaan Saudara pada aplikasi 
					Manajemen Penyedia Barang/Jasa PT Nusantara Regas yang perlu diperhatikan :

					<br/>
					".$value['message']."
					<br/>
					
					Silahkan memperbarui  data perusahaan Saudara<br/><br/>
					
					Demikian disampaikan untuk dapat segera ditindaklanjuti. <br/> 
					Manajemen Penyedia Barang/Jasa  - PT Nusantara Regas
				";
				
				//echo $message."<br><br><br><br>";

				
				

				$this->email->from('vms-noreply@nusantararegas.com', 'VMS REGAS');
				$this->email->to($data_vendor['vendor_email'].",logistik@nusantararegas.com"); 
				$this->email->cc($data_vendor['pic_email']); 
				$this->email->bcc('hanafi@nusantararegas.com'); 
				$this->email->bcc('muarifgustiar@gmail.com'); 
				$this->email->subject('Notifikasi Masa Berlaku Dokumen Aplikasi Manajemen Penyedia Barang/Jasa PT Nusantara Regas');
				
				$this->email->message($message);	
				$this->email->send();


			}
			
			
			
		}
	}
}
