<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Note extends CI_Controller {

	public function __construct(){
		parent::__construct();
		
		$this->load->model('Note_model','nm');
		$this->load->model('Vendor_model','vm');
		$this->load->library('email');
	}
	public function index($id){
		$_POST['id_vendor'] = $id;
		$vendor_data = $this->vm->get_data($id);
		$_POST['entry_stamp']=date('Y-m-d H:i:s');
		$res = $this->nm->save_note($this->input->post());
		$url = $this->input->post('url');
		if($res){
			
			$this->mail($vendor_data['vendor_email'],$_POST['value']);
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Note Terkirim!</p>');
			unset($_POST);
			redirect($url);
		}
	}
	public function close($id){
		$result = $this->nm->close($id);
		if($result){
			echo 'success';
		}
	}
	function mail($to,$message){
 		$this->email->clear(TRUE);

		$this->email->from('vms-noreply@nusantararegas.com', 'VMS REGAS');
		// $this->email->bcc('muarifgustiar@gmail.com'); 
		$this->email->to($to); 

		$this->email->subject('Catatan verifikasi berkas Sistem Aplikasi Kelogistikan PT Nusantara Regas');
		
		$this->email->message($message);	
		$this->email->send();
	}
}
