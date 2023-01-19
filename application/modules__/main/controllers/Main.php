<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

	public function index()
	{	
		if($this->session->userdata('user')['id_role'] != 6){
			redirect('dashboard');
		}elseif($this->session->userdata('admin')){
			redirect('admin');
		}else{
			$this->session->sess_destroy('form');
			$item['header'] = '';
			$item['content'] = $this->load->view('login',NULL,TRUE);
			$this->load->view('template',$item);
		}
	}
	public function login(){


		$this->load->model('main_model');
		
		if($this->input->post('username')&&$this->input->post('password')){
			$is_logged = $this->main_model->cek_login();

			if($is_logged){

				if($this->session->userdata('user')){
					$data = $this->session->userdata('user');

					$item['content'] 	= $this->load->view('redirect',$data,TRUE);
					$this->load->view('template',$item);
				}else if($this->session->userdata('admin')){
					if($this->session->userdata('admin')['id_role']==6){
//header('Location:http://eproc.nusantararegas.com/eproc');
						redirect(site_url('auction'));
					}else{
						redirect(site_url('admin'));
					}
				}
			}else{
				$this->session->set_flashdata('error_msg','Data tidak dikenal. Silahkan login kembali!');
				redirect(site_url());
			}
		}else{

			$this->session->set_flashdata('error_msg','Isi form dengan benar!');
			redirect(site_url());
		}
	}
	public function logout(){
		$this->session->sess_destroy();
		redirect(site_url());
	}

}
