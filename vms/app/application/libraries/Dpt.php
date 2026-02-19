<?php 

class Dpt{

	

	private $CI;



	public function __construct(){

		$this->CI =& get_instance(); 

	}

	public function non_iu_change($id){

		$this->CI->db->where('id_vendor',$id);

		$this->CI->db->where('status',1);

		$this->CI->db->update('tr_dpt',

			array(

				'status'=>1,

				'end_date'=>date('Y-m-d H:i:s'),

				'edit_stamp'=>date('Y-m-d H:i:s'),

			)

		);





		$this->CI->db->where('id',$id);

		$this->CI->db->where('vendor_status!=',0);
		$this->CI->db->where('vendor_status IS NOT NULL');

		// $this->CI->db->where('vendor_status!=',1);

		$a = $this->CI->db->update('ms_vendor',

			array(

				'vendor_status'=>1,

				'need_approve'=>0,

				'edit_stamp'=>date('Y-m-d H:i:s'),

			)

		);

	}

	function iu_change($id){

		$user = $this->CI->session->userdata('user');

		

		$this->CI->db->where('id',$id);

		$query = $this->CI->db->get('ms_ijin_usaha');

		$data = $query->row_array();





		/*Ubah TR DPT*/

		$this->CI->db->where('id_dpt_type',$data['id_dpt_type']);

		$this->CI->db->where('status',1);

		$this->CI->db->where('id_vendor',$user['id_user']);

		$this->CI->db->update('tr_dpt',

			array(

				'status'=>2,

				'end_date'=>date('Y-m-d H:i:s'),

				'edit_stamp'=>date('Y-m-d H:i:s'),

			)



		);

		

		if($this->CI->db->affected_rows()>0){

			$this->CI->db->insert('tr_dpt',

				array(

					'id_dpt_type'=>$data['id_dpt_type'],

					'id_vendor'=>$user['id_user'],

					'status'=>0,

					'entry_stamp'=>date('Y-m-d H:i:s'),

				)

			);

		}

	

		/*checking dpt*/

		$this->CI->db->where('id_vendor',$user['id_user']);

		$this->CI->db->where('status',1);

		$num_rows = $this->CI->db->get('tr_dpt')->num_rows();

		if($num_rows==0){

		$this->CI->db->where('id',$user['id_user']);

			$a = $this->CI->db->update('ms_vendor',

				array(

					'vendor_status'=>1,

					'need_approve'=>0,

					'edit_stamp'=>date('Y-m-d H:i:s'),

				)

			);

		}

	}

	function iu_add($id){

		$user = $this->CI->session->userdata('user');

		

		$this->CI->db->where('id',$id);

		$query = $this->CI->db->get('ms_ijin_usaha');

		$data = $query->row_array();



		$this->CI->db->where('id',$data['id_vendor']);

		$a = $this->CI->db->update('ms_vendor',

			array(

				'vendor_status'=>1,

				'edit_stamp'=>date('Y-m-d H:i:s'),

			)

		);



		$this->CI->db->where('id_vendor',$data['id_vendor']);

		$this->CI->db->where('status',1);

		$this->CI->db->or_where('status',2);

		$this->CI->db->where('id_dpt_type',$data['id_dpt_type']);

		$num_rows = $this->CI->db->get('tr_dpt')->num_rows();







		$this->CI->db->where('id_vendor',$data['id_vendor']);

		$this->CI->db->where('status',0);

		$this->CI->db->where('id_dpt_type',$data['id_dpt_type']);

		$num_rows_zero = $this->CI->db->get('tr_dpt')->num_rows();







		if($num_rows>0){

			$a = $this->CI->db->where('id_dpt_type',$data['id_dpt_type'])->where('status',1)->update('tr_dpt',

			array(

				'status'=>2,

				'end_date'=>date('Y-m-d H:i:s'),

				'edit_stamp'=>date('Y-m-d H:i:s'),

			));

			if($this->CI->db->affected_rows()>0){

				$this->CI->db->insert('tr_dpt',

					array(

						'id_dpt_type'=>$data['id_dpt_type'],

						'id_vendor'=>$data['id_vendor'],

						'status'=>0,

						'entry_stamp'=>date('Y-m-d H:i:s'),

					)

				);

			}



		}else if($num_rows_zero==0){

			$this->CI->db->insert('tr_dpt',

				array(

					'id_dpt_type'=>$data['id_dpt_type'],

					'id_vendor'=>$data['id_vendor'],

					'status'=>0,

					'entry_stamp'=>date('Y-m-d H:i:s'),

				)

			);

		}



		

	}

	public function decrease_status_dpt($id){

		$this->CI->db->where('id',$id);

		$a = $this->CI->db->update('tr_dpt',

			array(

				'status'=>0,

				'data_last_check'=>date('Y-m-d H:i:s'),

				'data_checker_id'=>$admin['user_id']

			)

		);

		return $a;

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

	public function edit_data($id,$table){

		$this->CI->db->where('id',$id)->update($table,array('data_status'=>0));

	}

	public function set_message($no,$name_file,$distance){

		$txt = '';

		if($distance==0){

			$txt .= 'Lampiran file '.$name_file.' dengan nomor '.$no.' sudah habis masa berlakunya.\n Harap diperbaharui untuk segera kami proses menjadi syarat vendor.\nTerimakasih.';

		}else if($distance==30){

			$txt .= 'Lampiran file '.$name_file.' dengan nomor '.$no.' menyisakan 30 hari sebelum masa berlakunya habis.\n Harap diperbaharui untuk segera kami proses menjadi syarat vendor.\nTerimakasih.';

		}else{

			$txt .= 'Lampiran file '.$name_file.' dengan nomor '.$no.' menyisakan '.$distance.' hari sebelum masa berlakunya habis.\n Harap diperbaharui untuk segera kami proses menjadi syarat vendor.\nTerimakasih.';

		}

		return $txt;

	}

	public function check_iu($id_user){

		$res = $this->CI->db->select('*')->where('del',0)->where('id_vendor',$id_user)->where_in('id_dpt_type',array(2,3,4,5))->get('ms_ijin_usaha')->num_rows();

		

		return $res;

	}

}