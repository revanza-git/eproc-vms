<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Cron extends CI_Controller {
	public function __construct(){
		parent::__construct();
		
	}

	public function drop_dpt(){
		/*
			Query dari tr_email_blast untuk mengambil data email yang akan dikirim
		*/
		$query = "SELECT * FROM tr_email_blast WHERE distance = 0 AND DATE(`date`) <= DATE('".date('Y-m-d')."')  GROUP BY id_doc";
		$query = $this->db->query($query)->result_array();

		foreach ($query as $key => $value) {

			$queryDocument = "SELECT * FROM ".$value['doc_type']." WHERE id = ".$value['id_doc'];
			$queryDocument = $this->db->query($queryDocument);
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

				

					if($this->db->affected_rows()>0){
						$this->db->insert('tr_dpt',array(
							'id_vendor' => $valueDocument['id_vendor'], 
							'id_dpt_type'=> $valueDocument['id_dpt_type'],
							'status'=>0,
							'entry_stamp'=>timestamp()
						));
					}
					$this->db->where('id', $valueDocument['id_vendor'])->update('ms_vendor', array('vendor_status'=> 1));
					
				}
			}


		}
	}
}
