<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

	public function index()
	{	
		if($this->session->userdata('user')){
			redirect('dashboard');
		}elseif($this->session->userdata('admin')['id_role']==6){
			redirect('auction');
		}else{
			/*$this->session->sess_destroy('form');
			$item['header'] = '';
			$item['content'] = $this->load->view('login',NULL,TRUE);
			$this->load->view('template',$item);*/
			header("Location:https://eproc.nusantararegas.com/eproc_nusantararegas/main/logout");
		}
	}
	
	public function login_user($name,$id_user,$id_sbu="0",$vendor_status,$is_active,$type,$app)
	{		
		if($type == 'user'){

			$set_session = array(

				'id_user' 		=> 	$id_user,

				'name'			=>	str_replace('%20', ' ', base64_decode($name)),

				'id_sbu'		=>	$id_sbu,

				'vendor_status'	=>	$vendor_status,

				'is_active'		=>	$data['is_active'],

				'app'			=>	'vms'

			);

			$this->session->set_userdata('user',$set_session);
			$user = $this->session->userdata('user'); 
			// print_r($user);die;
			$data['name']		= $user['name'];
			$item['content'] 	= $this->load->view('redirect',$data,TRUE);
			$this->load->view('template',$item);

		}
	}

	public function login_admin($name,$id_user,$id_role,$role_name,$app,$id_sbu="",$sbu_name="")
	{
		//if($type=='admin'){

			$set_session = array(

				'id_user' 		=> 	$id_user,

				'name'			=>	str_replace('%20', ' ', $name),

				'id_sbu'		=>	$id_sbu,

				'id_role'		=>	$id_role,

				'role_name'		=>	str_replace('%20', ' ', $role_name),

				'sbu_name'		=>	str_replace('%20', ' ', $sbu_name),

				'app'			=>	$app

			);

			$this->session->set_userdata('admin',$set_session);
			// print_r($this->session->userdata('admin'));die;
			if($this->session->userdata('admin')['id_role']==6){
				// header('Location:http://eproc.nusantararegas.com/eproc');
				redirect(site_url('auction'));
			}else{
				$admin = $this->session->userdata('admin');

				$id_user 		= 	$admin['id_user'];
				$name			=	$admin['name'];
				$id_sbu			=	$admin['id_sbu'];
				$id_role		=	$admin['id_role'];
				$role_name		=	$admin['role_name'];
				$sbu_name		=	$admin['sbu_name'];
				$app			=	$admin['app'];

				header('Location:http://10.10.10.4/eproc_pengadaaan/main/login_admin/'.$name.'/'.$id_user.'/'.$id_role.'/'.$role_name.'/'.'admin'.'/'.$app.'/'.$id_sbu.'/'.$sbu_name);
				// redirect(site_url('admin'));
			}
		//}
	}
	
	public function logout(){
		$this->session->sess_destroy();
		// redirect(site_url());
		header('Location: https://eproc.nusantararegas.com/eproc_nusantararegas/main/logout');
	}
	
	public function showUser()
	{
		$query = " 	SELECT 
						a.name,
						b.username,
						b.password,
						a.vendor_status
					FROM
						ms_vendor a
					JOIN
						ms_login b ON a.id=b.id_user AND type='user'
					WHERE
						a.del=0 AND a.vendor_status = 2
		";
		$get_data_admin = $this->db->query($query)->result_array();

		$admin = '<table border=1>
			<thead>
				<tr>
					<th colspan="5">Daftar User Vendor (DPT)</th>
				</tr>
				<tr>
					<th>No</th>
					<th>Nama</th>
					<th>Username</th>
					<th>Password</th>
				</tr>
			</thead>
			<tbody>';
			$no=1;
		foreach ($get_data_admin as $key => $value) {
			$admin .= '<tr>
				<td>'.$no.'</td>
				<td>'.$value['name'].'</td>
				<td>'.$value['username'].'</td>
				<td>'.$value['password'].'</td>
			</tr>';
			$no++;
		}
				
		$admin .='</tbody>
		</table> <br><br>';

		$query = ' 	SELECT 
						a.name,
						b.username,
						b.password,
						a.vendor_status
					FROM
						ms_vendor a
					JOIN
						ms_login b ON a.id=b.id_user AND type="user"
					WHERE
						a.del=0 AND a.vendor_status = 1
		';

		$get_data_vendor = $this->db->query($query)->result_array();
		$admin .='<table border=1>
			<thead>
				<tr>
					<th colspan="5">Daftar User Vendor (Daftar Tunggu)</th>
				</tr>
				<tr>
					<th>No</th>
					<th>Nama</th>
					<th>Username</th>
					<th>Password</th>
				</tr>
			</thead>
			<tbody>';
		$no_ = 1;
		foreach ($get_data_vendor as $key => $value) {
			$admin .= '<tr>
				<td>'.$no_.'</td>
				<td>'.$value['name'].'</td>
				<td>'.$value['username'].'</td>
				<td>'.$value['password'].'</td>
			</tr>';
			$no_++;
		}
		header('Content-type: application/ms-excel');

    	header('Content-Disposition: attachment; filename=Daftar User VMS.xls');
		echo $admin;
	}

	// public function login__(){


	// 	$this->load->model('main_model');
		
	// 	if($this->input->post('username')&&$this->input->post('password')){
	// 		$is_logged = $this->main_model->cek_login();

	// 		if($is_logged){

	// 			if($this->session->userdata('user')){
	// 				$data = $this->session->userdata('user');

	// 				$item['content'] 	= $this->load->view('redirect',$data,TRUE);
	// 				$this->load->view('template',$item);
	// 			}else if($this->session->userdata('admin')){
	// 				if($this->session->userdata('admin')['id_role']==6){
	// 					// header('Location:http://eproc.nusantararegas.com/eproc');
	// 					redirect(site_url('auction'));
	// 				}else{
	// 					redirect(site_url('admin'));
	// 				}
	// 			}
	// 		}else{
	// 			$this->session->set_flashdata('error_msg','Data tidak dikenal. Silahkan login kembali!');
	// 			redirect(site_url());
	// 		}
	// 	}else{

	// 		$this->session->set_flashdata('error_msg','Isi form dengan benar!');
	// 		redirect(site_url());
	// 	}
	// }
}
