<?php
class Data_process{
	
	private $CI;

	public function __construct(){
		$this->CI =& get_instance(); 
	}
	public function check($id_vendor, $post,$id,$table,$field='id'){

		$admin = $this->CI->session->userdata('admin');

		if(!is_array($post)){
			$post = array();
		}
		$post['mandatory'] = (isset($post['mandatory'])) ? $post['mandatory'] : 0;
		$post['status'] = (isset($post['status'])) ? $post['status'] : 0;

		$data_status = 0;

		if($post['mandatory']==1&&$post['status']==1){

			$data_status = 1;

		}else if($post['mandatory']==1&&$post['status']==0){

			$data_status = 3;

		}else if($post['mandatory']==0&&$post['status']==1){
			$this->CI->load->model('Vendor_model','vm');
			$vendor_data = $this->CI->vm->get_pt($id);
			$vendor_legal_name = (is_array($vendor_data) && isset($vendor_data['legal_name'])) ? $vendor_data['legal_name'] : '';
			$vendor_name = (is_array($vendor_data) && isset($vendor_data['name'])) ? $vendor_data['name'] : '';
			$vendor_label = trim($vendor_legal_name." ".$vendor_name);
			$email['subject']	= "Verifikasi".(($vendor_label!='') ? " ".$vendor_label : "")." - Sistem Aplikasi Kelogistikan PT Nusantara Regas";
			$email['message']	= "Terdapat data yang salah pada lampiran anda yang telah terunggah dalam Sistem Aplikasi Kelogistikan PT Nusantara Regas.
									<br>
									Mohon segera memperbaiki data perusahaan pada Sistem Aplikasi Kelogistikan PT Nusantara Regas agar proses verifikasi dapat dilanjutkan.
									<br><br>
									Terimakasih,<br>
									PT Nusantara Regas";
			// $this->CI->utility->mail($vendor_data['email'],$email['message'],$email['subject']);


			$data_status = 2;

		}else if($post['mandatory']==0&&$post['status']==0){

			$data_status = 4;

		}

		$this->CI->db->where($field,$id);

		$checker_id = (is_array($admin) && isset($admin['id_user'])) ? $admin['id_user'] : NULL;
		$a = $this->CI->db->update($table,

			array(

				'data_status'=>$data_status,

				'data_last_check'=>date('Y-m-d H:i:s'),

				'data_checker_id'=>$checker_id

			)

		);

		// echo $this->CI->db->last_query();

		return $a;

	}

	public function set_mandatory($data_status){
		if($data_status==1||$data_status==3){
			return 'checked';
		}
	}
	public function set_yes_no($val,$data_status){

		if($val == 1){
			if($data_status==1||$data_status==2){
				return 'checked';
			}
		}elseif($val == 0){
			if($data_status==3||$data_status==4){
				return 'checked';
			}
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
