<?php
class Data_process{

	private $CI;

	public function __construct(){
		$this->CI =& get_instance();
	}
	public function check($id_vendor, $post,$id,$table,$field='id'){

		$admin = $this->CI->session->userdata('admin');
		$data_status = 0;

		if($post['status']==1){			$data_status = 1;		}else if($post['status']==0){			$data_status = 2;		}			$this->CI->db->where($field,$id);
			$a = $this->CI->db->update($table,
			array(
				'data_status'=>$data_status,
				'data_last_check'=>date('Y-m-d H:i:s'),
				'data_checker_id'=>$admin['id_user']
			)
		);		return $a;	}
	public function set_yes_no($val,$data_status){		if($val == 1 && $data_status==1){
				return 'checked';
		}elseif($val == 0 && $data_status==2){
				return 'checked';
		}
	}

	public function generate_note($id){
		$html = '<div class="note"><div class="noteWrap">
		<button class="btnNote"><i class="fa fa-pencil-square-o"></i>&nbsp;Tambah Note</button>
		<div class="noteForm">
		<form method="POST" action="'.site_url('note/index/'.$id).'">
			<div class="noteFormWrap">
				<input type="hidden" value="'.current_url().'" name="url">
				<textarea name="value"></textarea>
				<input type="submit" value="post" class="notePost">
			</div>
		</form></div></div></div>';
		return $html;
	}
	public function generate_progress($step,$id_data){
		$progress = array(
			'administrasi'=>'Administrasi',
			'akta'=>'Akta',
			'situ'=>'SITU',
			'tdp'=>'TDP',
			'pengurus'=>'Pengurus',
			'pemilik'=>'Pemilik',
			'badan_usaha'=>'Izin Usaha',
			'agen'=>'Pabrikan/Keagenan/Distributor',
			'pengalaman'=>'Pengalaman',
			'k3'=>'CSMS',
			'verification'=>'Verifikasi DPT'
			);
		$txt = $this->generate_note($id_data).'<div class="progressBar"><ul>';
		foreach($progress as $key=>$value){
			$txt .='<li class="'.(($step ==$key)?'active':'').'"><a href="'.site_url('approval/'.$key.'/'.$id_data).'">'.$value.'</a></li>';
		}
		$txt .= '</ul></div>';
		return $txt;
	}
}
