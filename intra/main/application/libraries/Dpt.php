<?php
class Dpt{
	
	public function __construct(){
		$this->CI =& get_instance(); 
	}

	public function set_email_blast($id_doc,$doc_type,$name_file,$expire_date){

		if($expire_date != 'lifetime'){
			$array[30]['date'] = date('Y-m-d',strtotime($expire_date.' -30 days'));
			for($i = 7; $i>=0;$i--){
				$array[$i]['date'] = date('Y-m-d',strtotime($expire_date.' -'.$i.' days'));
			}

			$result = $this->CI->db->select('no')->where('id',$id_doc)->get($doc_type)->row_array();
			$no = $result['no'];

			foreach($array as $key=>$val){
				$a = $this->CI->db->insert('tr_email_blast',
					array(
						'no'=>$no,
						'id_doc'=>$id_doc,
						'doc_type'=>$doc_type,
						'distance'=>$key,
						'date'=>$val['date'],
						'message'=>$this->set_message($no,$name_file,$key),
					)
				);
			}
		}
	}
	public function edit_email_blast($id_doc,$doc_type,$name_file,$expire_date){

		$this->CI->db->where('id_doc', $id_doc)->where('doc_type', $doc_type)->delete('tr_email_blast');
		
		if($expire_date != 'lifetime'){
			

			$array[30]['date'] = date('Y-m-d',strtotime($expire_date.' -30 days'));
			for($i = 7; $i>=0;$i--){
				$array[$i]['date'] = date('Y-m-d',strtotime($expire_date.' -'.$i.' days'));
			}

			$result = $this->CI->db->select('no')->where('id',$id_doc)->get($doc_type)->row_array();
			$no = $result['no'];

			foreach($array as $key=>$val){
				$a = $this->CI->db->insert('tr_email_blast',
					array(
						'no'=>$no,
						'id_doc'=>$id_doc,
						'doc_type'=>$doc_type,
						'distance'=>$key,
						'date'=>$val['date'],
						'message'=>$this->set_message($no,$name_file,$key),
					)
				);
			}
		}
	}
	public function set_message($no,$name_file,$distance){
		$txt = '';
		if($distance==0){
			$txt .= 'Lampiran file '.$name_file.' dengan nomor '.$no.' sudah habis masa berlakunya. 
			Harap diperbaharui untuk segera kami proses menjadi syarat vendor. 
			Terimakasih.';
		}else if($distance==30){
			$txt .= 'Lampiran file '.$name_file.' dengan nomor '.$no.' menyisakan 30 hari sebelum masa berlakunya habis. 
			Harap diperbaharui untuk segera kami proses menjadi syarat vendor.
			Terimakasih.';
		}else{
			$txt .= 'Lampiran file '.$name_file.' dengan nomor '.$no.' menyisakan '.$distance.' hari sebelum masa berlakunya habis. 
			Harap diperbaharui untuk segera kami proses menjadi syarat vendor. 
			Terimakasih.';
		}
		return $txt;
	}
}