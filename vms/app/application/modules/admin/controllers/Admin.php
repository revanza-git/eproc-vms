<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {
	public function __construct(){
		parent::__construct();
		if(!$this->session->userdata('admin')){
			redirect(site_url());
		}

	}
	
	public function index(){
		$this->load->library('datatables');
		$this->load->model('vendor/vendor_model','vm');
		$this->load->model('main_model','mm');
		$data['chart'] 				= 	array(
										'daftar_tunggu_chart'	=>	$this->mm->get_daftar_tunggu_chart(),
										'daftar_hitam_chart'	=>	$this->mm->daftar_hitam_chart(),
										'daftar_merah_chart'	=>	$this->mm->daftar_merah_chart(),
										'dpt_chart'				=>	$this->mm->dpt_chart()
									);

			if(count($_POST['nomorBtn'])){
				unset($_POST['nomorBtn']);
				// $_POST['entry_stamp'] = date("Y-m-d H:i:s");

				// $this->aum->save_data($this->input->post());

				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menambah data!</p>');
				// echo $this->input->post('nomor');

				$this->session->set_userdata('nomor', $this->input->post("nomor"));
				// echo $this->session->userdata('nomor');
				// print_r($this->input->post());die;
				redirect(site_url('admin/certificate/dpt/'.$this->input->post('id').'/'));
			}elseif(count($_POST['nomorBtnCSMS'])){
				unset($_POST['nomorBtnCSMS']);
				// $_POST['entry_stamp'] = date("Y-m-d H:i:s");

				// $this->aum->save_data($this->input->post());

				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menambah data!</p>');
				// echo $this->input->post('nomor');

				$this->session->set_userdata('nomorCSMS', $this->input->post("nomorCSMS"));
				// echo $this->session->userdata('nomor');
				redirect(site_url('admin/certificate/csms/'.$this->input->post('idCSMS').'/'));
			}
			
		$layout['content'] 		= $this->load->view('admin/admin/dashboard_content',$data,TRUE);
		$layout['script'] 		= $this->load->view('admin/admin/dashboard_content_js',$data,TRUE);

		$item['header'] 		= $this->load->view('header',$this->session->userdata('admin'),TRUE);
		$item['content'] 		= $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function logout(){
		$this->session->sess_destroy();
		redirect(site_url());
	}


	function get_dpt_list(){
		$this->load->library('datatables');

		$this->datatables	->select('tb_legal.name as legal_name, ms_vendor.name as vendor_name, ms_vendor.edit_stamp as last_activity')
							->join('ms_vendor','ms_vendor.id=ms_vendor_admistrasi.id_vendor')
							->join('tb_legal','tb_legal.id=ms_vendor_admistrasi.id_legal')
							->where('ms_vendor.vendor_status',1)
							->from('ms_vendor_admistrasi');

		echo $this->datatables->generate();
	}
}
