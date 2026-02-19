<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->model('main_model','mm');
		if(!$this->session->userdata('admin')){
			redirect(base_url());
		}

	}
	
	public function index(){
		// print_r($this->session->userdata('admin'));
		$this->load->model('vendor/vendor_model','vm');
		$this->load->model('main_model','mm');
				$data['chart'] 				= 	array(
									'daftar_tunggu_chart'	=>	$this->mm->get_daftar_tunggu_chart(),
									'daftar_hitam_chart'	=>	$this->mm->daftar_hitam_chart(),
									'daftar_merah_chart'	=>	$this->mm->daftar_merah_chart(),
									'dpt_chart'				=>	$this->mm->dpt_chart(),
									'dt_aktif'				=>	$this->mm->dt(1),
									'dt_non_aktif'			=>	$this->mm->dt(0)
								);

		if(isset($_POST['nomorBtn']) && count($_POST['nomorBtn'])){
			unset($_POST['nomorBtn']);
			// $_POST['entry_stamp'] = date("Y-m-d H:i:s");

			// $this->aum->save_data($this->input->post());

			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menambah data!</p>');
			// echo $this->input->post('nomor');

			$this->session->set_userdata('nomor', $this->input->post("nomor"));
			// echo $this->session->userdata('nomor');
			// print_r($this->input->post());die;
			redirect(base_url('admin/certificate/dpt/'.$this->input->post('id').'/'));
		}elseif(isset($_POST['nomorBtnCSMS']) && count($_POST['nomorBtnCSMS'])){
				unset($_POST['nomorBtnCSMS']);
				// $_POST['entry_stamp'] = date("Y-m-d H:i:s");

				// $this->aum->save_data($this->input->post());

				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menambah data!</p>');
				// echo $this->input->post('nomor');

				$this->session->set_userdata('nomorCSMS', $this->input->post("nomorCSMS"));
				// echo $this->session->userdata('nomor');
				redirect(base_url('admin/certificate/csms/'.$this->input->post('idCSMS').'/'));
			}
			
		$layout['content'] 		= $this->load->view('admin/admin/dashboard_content',$data,TRUE);
		$layout['script'] 		= $this->load->view('admin/admin/dashboard_content_js',$data,TRUE);

		$item['header'] 		= $this->load->view('header',$this->session->userdata('admin'),TRUE);
		$item['content'] 		= $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function search_bar($q){
		$data = $this->mm->search_bar($q);
		echo json_encode($data);
		// echo json_encode($this->mm->search_data($q));
	}
	// public function search_data(){
	// 	echo json_encode($this->mm->search_data());
	// }

	public function logout(){
		$this->session->sess_destroy();
		header('Location: '.$this->config->item('vms_url'));
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
	
	public function mail_test()
	{
		$this->load->library('utility');
		$a = 'telah masuk kedalam daftar tunggu di Sistem Aplikasi Kelogistikan PT Nusantara Regas. <br/>
		Untuk selanjutnya, silahkan memeriksa kembali kelengkapan data - data calon DPT di aplikasi. <br/><br/>
		Terima kasih.<br/>
		PT Nusantara Regas';
		return $this->utility->mail('arinaldha@gmail.com', $a, 'test');
	}
}
