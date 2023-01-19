<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Approval extends CI_Controller {

	public function __construct(){
		parent::__construct();
		if(!$this->session->userdata('admin')){
			redirect(site_url());
		}
		$this->load->model('vendor/vendor_model','apprm');
		$this->load->library('data_process');
		$this->load->library('email');
	}

	public function administrasi($id){
		$this->load->model('vendor/vendor_model','vm');
		
		$data = $this->vm->get_data($id);
		$vld = 	array(
					array(
						'field'=>'status',
						'label'=>'Status',
						'rules'=>'required'
					)
				);
		
		$this->form_validation->set_rules($vld);

		if($this->input->post('simpan')){
			if($this->form_validation->run()==TRUE){
				$result = $this->data_process->check($id, $this->input->post(),$data['id'],'ms_vendor_admistrasi');
				if($result){
					$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menyimpan data!</p>');	
					redirect(site_url('approval/administrasi/'.$id));
				}
			}
		}
		$data['id_data'] = $id;
		$layout['content'] =  $this->load->view('administrasi',$data,TRUE);
		$item['header'] = $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		
		$this->load->model('approval_model','appm');
		
		$layout['approval_data'] = $this->appm->get_total_data($id);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		
		$this->load->view('template',$item);
	}
	
	public function akta($id,$akta_type='pendirian'){
		$this->load->model('akta/akta_model','am');
		
		$data['akta_list'] = $this->am->get_akta_admin_list($id,$akta_type);
		
		if($this->input->post('simpan')){
			foreach($this->input->post('akta') as $key => $value){
				$this->data_process->check($id, $value,$key,'ms_akta');
			}
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menyimpan data!</p>');	
			redirect(site_url('approval/akta/'.$id.'/'.$akta_type));
				
		}
		$data['id_data'] = $id;

		if($akta_type=='pendirian'){
			$view = 'akta/akta_pendirian';
		}else{
			$view = 'akta/akta_perubahan';
		}
		$layout['content'] =  $this->load->view($view,$data,TRUE);
		$item['header'] = $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		
		$this->load->model('approval_model','appm');
		
		$layout['approval_data'] = $this->appm->get_total_data($id);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		
		$this->load->view('template',$item);
	}
	public function situ($id){
		$this->load->model('situ/situ_model','sm');
		
		$data['situ_list'] = $this->sm->get_situ_admin_list($id);
		
		if($this->input->post('simpan')){
			foreach($this->input->post('situ') as $key => $value){
				$this->data_process->check($id, $value,$key,'ms_situ');
			}
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menyimpan data!</p>');	
			redirect(site_url('approval/situ/'.$id));
				
		}
		$data['id_data'] = $id;

		
		$layout['content'] =  $this->load->view('situ',$data,TRUE);
		$item['header'] = $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		
		$this->load->model('approval_model','appm');
		
		$layout['approval_data'] = $this->appm->get_total_data($id);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		
		$this->load->view('template',$item);
	}
	public function tdp($id){
		$this->load->model('tdp/tdp_model','sm');
		
		$data['tdp_list'] = $this->sm->get_tdp_admin_list($id);
		
		if($this->input->post('simpan')){
			foreach($this->input->post('tdp') as $key => $value){
				$this->data_process->check($id, $value,$key,'ms_tdp');
			}
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menyimpan data!</p>');	
			redirect(site_url('approval/tdp/'.$id));
				
		}
		$data['id_data'] = $id;

		
		$layout['content'] =  $this->load->view('tdp',$data,TRUE);
		$item['header'] = $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		
		$this->load->model('approval_model','appm');
		
		$layout['approval_data'] = $this->appm->get_total_data($id);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		
		$this->load->view('template',$item);
	}
	public function pengurus($id){
		$this->load->model('pengurus/pengurus_model','pm');
		
		$data['pengurus_list'] = $this->pm->get_pengurus_admin_list($id);
		
		if($this->input->post('simpan')){
			foreach($this->input->post('pengurus') as $key => $value){
				$this->data_process->check($id, $value,$key,'ms_pengurus');
			}
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menyimpan data!</p>');	
			redirect(site_url('approval/pengurus/'.$id));
			
		}
		$data['id_data'] = $id;
		$layout['content'] =  $this->load->view('pengurus',$data,TRUE);
		$item['header'] = $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		
		$this->load->model('approval_model','appm');
		
		$layout['approval_data'] = $this->appm->get_total_data($id);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		
		$this->load->view('template',$item);
	}
	public function pemilik($id){
		$this->load->model('pemilik/pemilik_model','pm');
		
		$data['pemilik_list'] = $this->pm->get_pemilik_admin_list($id);
		$data['ubo_file'] = $this->pm->get_ubo($id);
		if($this->input->post('simpan')){
			foreach($this->input->post('pemilik') as $key => $value){
				$this->data_process->check($id, $value,$key,'ms_pemilik');
			}
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menyimpan data!</p>');	
			redirect(site_url('approval/pemilik/'.$id));
			
		}
		$data['id_data'] = $id;
		$layout['content'] =  $this->load->view('pemilik',$data,TRUE);
		$item['header'] = $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		
		$this->load->model('approval_model','appm');
		
		$layout['approval_data'] = $this->appm->get_total_data($id);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		
		$this->load->view('template',$item);
	}
	public function badan_usaha($id,$surat='siup'){
		$this->load->model('izin/izin_model','im');
		$this->load->model('approval_model','am');

		$data['dt_siu'] = array('siup'=>'SIUP','ijin_lain'=>'Surat Izin Usaha Lainnya','asosiasi'=>'Sertifikat Asosiasi/Lainnya','siujk'=>'SIUJK','sbu'=>'SBU');
		$data['dt_dpt'] = $this->am->get_dpt_type();
		$data['surat'] = $surat;


		$table = array(
					'siup' => array(
						'dpt'	=>	'DPT Tipe',
						'no'	=>	'No.',
						'issue_date'=> 'Tanggal',
						'qualification'=>'Kualifikasi',
						'expire_date'=>'Masa Berlaku',
						'izin_file'=>'Lampiran',
					),
					'ijin_lain' => array(
						'dpt'	=>	'DPT Tipe',
						'authorize_by'	=>	'Lembaga Penerbit',
						'no'	=>	'No.',
						'issue_date'=> 'Tanggal',
						'qualification'=>'Kualifikasi',
						'expire_date'=>'Masa Berlaku',
						'izin_file'=>'Lampiran',
					),
					'asosiasi' => array(
						'dpt'	=>	'DPT Tipe',
						'authorize_by'	=>	'Lembaga Penerbit',
						'no'	=>	'No.',
						'issue_date'=> 'Tanggal',
						'expire_date'=>'Masa Berlaku',
						'izin_file'=>'Lampiran',
					),	
					'sbu'=>array(
						'dpt'	=>	'DPT Tipe',
						'authorize_by'	=>	'Anggota Asosiasi',
						'no'	=>	'No.',
						'issue_date'=> 'Tanggal',
						'expire_date'=>'Masa Berlaku',
						'izin_file'=>'Lampiran',
					),
					'siujk'=>array(
						'dpt'	=>	'DPT Tipe',
						'authorize_by'	=>	'Lembaga Penerbit',
						'no'	=>	'No.',
						'issue_date'=> 'Tanggal',
						'qualification'=>'Kualifikasi',
						'expire_date'=>'Masa Berlaku',
						'izin_file'=>'Lampiran',
					)
				);
		
		if($this->input->post('simpan')){

			foreach($this->input->post('ijin_usaha') as $key => $value){
				$a = $this->data_process->check($id, $value,$key,'ms_ijin_usaha');
				if($a){
					$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menyimpan data!</p>');	
				}
			}
			redirect(site_url('approval/badan_usaha/'.$id.'/'.$surat));
			
		}
		$data['table'] = $table;
		$data['id_data'] = $id;
		$data['izin_list'] = $this->im->get_izin_admin_list($id,$surat);

		$layout['content'] =  $this->load->view('izin',$data,TRUE);
		$item['header'] = $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		
		$this->load->model('approval_model','appm');
		
		$layout['approval_data'] = $this->appm->get_total_data($id);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		
		$this->load->view('template',$item);
	}
	public function bsb($id_data,$id){
		$this->load->model('izin/izin_model','im');
		
		$data['bsb_list'] = $this->im->get_bsb_admin_list($id);
		$data['get_data'] = $this->im->get_data($id);
		$data['dt_siu'] = array('siup'=>'SIUP','ijin_lain'=>'Surat Izin Usaha Lainnya','asosiasi'=>'Sertifikat Asosiasi/Lainnya','siujk'=>'SIUJK','sbu'=>'SBU');
		
		$table = array(
					'siup' => array(
						'no'	=>	'No.',
						'issue_date'=> 'Tanggal',
						'qualification'=>'Kualifikasi',
						'expire_date'=>'Masa Berlaku',
						'izin_file'=>'Lampiran',
					),
					'ijin_lain' => array(
						'authorize_by'	=>	'Lembaga Penerbit',
						'no'	=>	'No.',
						'issue_date'=> 'Tanggal',
						'qualification'=>'Kualifikasi',
						'expire_date'=>'Masa Berlaku',
						'izin_file'=>'Lampiran',
					),
					'asosiasi' => array(
						'authorize_by'	=>	'Lembaga Penerbit',
						'no'	=>	'No.',
						'issue_date'=> 'Tanggal',
						'expire_date'=>'Masa Berlaku',
						'izin_file'=>'Lampiran',
					),	
					'sbu'=>array(
						'authorize_by'	=>	'Anggota Asosiasi',
						'no'	=>	'No.',
						'issue_date'=> 'Tanggal',
						'expire_date'=>'Masa Berlaku',
						'izin_file'=>'Lampiran',
					),
					'siujk'=>array(
						'authorize_by'	=>	'Lembaga Penerbit',
						'no'	=>	'No.',
						'issue_date'=> 'Tanggal',
						'qualification'=>'Kualifikasi',
						'expire_date'=>'Masa Berlaku',
						'izin_file'=>'Lampiran',
					)
				);

		if($this->input->post('simpan')){
			foreach($this->input->post('bsb') as $key => $value){
				$this->data_process->check($id, $value,$key,'ms_iu_bsb');
			}
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menyimpan data!</p>');	
			redirect(site_url('approval/bsb/'.$id_data.'/'.$id));
			
		}
		$data['id_data'] = $id_data;
		$data['table'] = $table;
		$layout['content'] =  $this->load->view('bsb',$data,TRUE);
		$item['header'] = $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		
		$this->load->model('approval_model','appm');
		
		$layout['approval_data'] = $this->appm->get_total_data($id);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		
		$this->load->view('template',$item);
	}

	public function agen($id){
		$this->load->model('agen/agen_model','am');
		
		$data['agen_list'] = $this->am->get_agen_admin_list($id);
		
		if($this->input->post('simpan')){
			foreach($this->input->post('agen') as $key => $value){
				$this->data_process->check($id, $value,$key,'ms_agen');
			}
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menyimpan data!</p>');	
			redirect(site_url('approval/agen/'.$id));
			
		}
		$data['id_data'] = $id;
		$layout['content'] =  $this->load->view('agen',$data,TRUE);
		$item['header'] = $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		
		$this->load->model('approval_model','appm');
		
		$layout['approval_data'] = $this->appm->get_total_data($id);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		
		$this->load->view('template',$item);
	}
	public function produk($id_data,$id){
		$this->load->model('agen/agen_model','am');
		$data = $this->am->get_data($id);
		$data['produk_list'] = $this->am->get_produk_admin_list($id_data,$id);
		
		
		if($this->input->post('simpan')){
			foreach($this->input->post('produk') as $key => $value){
				$this->data_process->check($id, $value,$key,'ms_agen_produk');
			}
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menyimpan data!</p>');	
			redirect(site_url('approval/produk/'.$id_data.'/'.$id));
			
		}
		$data['id_data'] = $id_data;
		$layout['content'] =  $this->load->view('produk',$data,TRUE);
		$item['header'] = $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		
		$this->load->model('approval_model','appm');
		
		$layout['approval_data'] = $this->appm->get_total_data($id);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		
		$this->load->view('template',$item);
	}

	public function pengalaman($id){
		$this->load->model('pengalaman/pengalaman_model','pm');
		
		$data['pengalaman_list'] = $this->pm->get_pengalaman_admin_list($id);
		
		if($this->input->post('simpan')){
			foreach($this->input->post('pengalaman') as $key => $value){
				$this->data_process->check($id, $value,$key,'ms_pengalaman');
			}
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menyimpan data!</p>');	
			redirect(site_url('approval/pengalaman/'.$id));
			
		}
		$data['id_data'] = $id;
		$layout['content'] =  $this->load->view('pengalaman',$data,TRUE);
		$item['header'] = $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		
		$this->load->model('approval_model','appm');
		
		$layout['approval_data'] = $this->appm->get_total_data($id);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		
		$this->load->view('template',$item);
	}
	public function pengalaman_detail($id_data,$id){
		$this->load->model('pengalaman/pengalaman_model','pm');
		
		$data = $this->pm->get_data_full($id_data,$id);

		$data['id_data'] = $id_data;
		$data['id_pengalaman'] = $id;
		$layout['content'] =  $this->load->view('pengalaman_detail',$data,TRUE);
		$item['header'] = $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		
		$this->load->model('approval_model','appm');
		
		$layout['approval_data'] = $this->appm->get_total_data($id_data);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		
		$this->load->view('template',$item);
	}
	public function k3($id){
		$this->load->model('approval_model','appm');
		$this->load->model('k3/k3_model','km');
		$data = $this->vm->get_data($id);
		$data['get_csms'] = $this->km->get_csms($id);
		$data['id_data'] = $id;
		
		if(!isset($data['get_csms'])){
		$data['ms_quest']=$this->km->get_master_header();
		$data['quest']=$this->km->get_quest();
		$data['field_quest'] = $this->km->get_field_quest();
		$data['evaluasi_list'] = $this->km->get_evaluasi_list();
		$data['evaluasi'] = $this->km->get_evaluasi();


		$data['data_k3']=$this->km->get_k3_data($id);
		$data['csms_limit']=$this->km->get_csms_limit($id);
		$data['score_k3'] = $this->km->get_poin($id);
		}

		$data['data_poin']=$this->km->get_poin($id);
		$data['csms_file'] 	= $this->km->get_k3_all_data($id)['csms_file'];
		
		
		$data['value_k3']=$this->km->get_penilaian_value($id,$data['csms_file']['id']);


		$layout['content']= $this->load->view('k3',$data,TRUE);
		$vld = 	array(
					array(
						'field'=>'status',
						'label'=>'Status',
						'rules'=>'required'
					)
				);
		
		$this->form_validation->set_rules($vld);
		if($this->input->post('simpan')){
			if($this->form_validation->run()==TRUE){
				$_POST['mandatory'] = 1;
				$result = $this->data_process->check($id, $this->input->post(),$id,'ms_csms','id_vendor');

				if($result){
					if($_POST['status']&&$_POST['mandatory']){
						$array = $this->appm->set_expiry($id);		
						$this->dpt->set_email_blast($res,'ms_csms','Lampiran CSMS',$array['expiry_date']);
					}
					
					$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menyimpan data!</p>');	
					redirect(site_url('approval/k3/'.$id));
				}
			}
		}

		$item['header'] = $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		
		
		
		$layout['approval_data'] = $this->appm->get_total_data($id);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		
		$this->load->view('template',$item);
	}


	public function verification($id){
		$this->load->model('approval_model','am');
		$data = $this->vm->get_vendor_name($id);
		
		$data['approval_data'] = $this->am->get_total_data($id);
		$data['spv']	= $this->am->get_spv_mail(8);
		
		$data['graphBar'] = array(
									0=>
										array('val'=>(count($data['approval_data'][0]))/$data['approval_data']['total']*100,
											'color'=>'#f39c12'
										),
									1=>
										array('val'=>(count($data['approval_data'][1])+count($data['approval_data'][2]))/$data['approval_data']['total']*100,
											'color'=>'#2cc36b'
										),
									3=>array('val'=>(count($data['approval_data'][3])+count($data['approval_data'][4]))/$data['approval_data']['total']*100,
											'color'=>'#c0392b'
										),
								);

		$email['dpt'] 		= $this->vm->get_dpt_mail($id);
		$email['msg'] 		= 
						$email['dpt']['legal_name'].". ".$email['dpt']['name']." telah masuk kedalam daftar tunggu di Sistem Aplikasi Kelogistikan PT Nusantara Regas.<br/>
						Untuk selanjutnya, silahkan memeriksa kembali kelengkapan data - data calon DPT di aplikasi.<br/><br/>
						Terima kasih.<br/>
						PT Nusantara Regas'";
		$email['subject']	= "Daftar Tunggu DPT - Sistem Aplikasi Kelogistikan PT Nusantara Regas";


		if($this->input->post('simpan')){
			
			#email SPV
			foreach ($data['spv'] as $keyspv => $valuespv) {
				$this->utility->mail($valuespv['email'],$email['msg'],$email['subject']);
			}

			if(count($data['approval_data'][0])>0||count($data['approval_data'][3])>0){
				$this->session->set_flashdata('msgError','<p class="errorMsg">Tidak Bisa diangkat menjadi vendor. Ada beberapa data yang harus diperbaiki</p>');	
			}else{
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Data terkirim untuk disetujui!</p>');	

				$data['approval_data'] = $this->am->angkat_vendor($id);
				
				redirect(site_url('approval/verification/'.$id));
			}

		}

		$data['id_data'] = $id;
		$layout['content'] =  $this->load->view('verifikasi',$data,TRUE);
		$layout['script'] =  $this->load->view('verifikasi_js',$data,TRUE);
		$item['header'] = $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		
		$this->load->model('approval_model','appm');
		
		$layout['approval_data'] = $this->appm->get_total_data($id);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		
		$this->load->view('template',$item);
	}

	public function approve($id){
		$this->load->model('approval_model','am');
		$this->load->model('vendor/vendor_model','vm');
		
		//$res = $this->am->approve($id);
		$email['dpt'] 		= $this->vm->get_dpt_mail($id);
		$email['adm'] 		= $this->vm->get_adm_mail($this->session->userdata('admin')['id_role']);
		$email['msg'] 		= "
								Kepada <i>".$email['dpt']['legal_name'].". ".$email['dpt']['name'].".</i>
									<br/><br/>
								Dengan ini kami sampaikan bahwa perusahaan saudara telah diangkat menjadi DPT(Daftar Penyedia Barang/Jasa Terdaftar) di PT Nusantara Regas.
									<br/><br/><br/>
								Terima kasih,
									<br/>
								PT Nusantara Regas.";
		$email['subject']	= "Pemberitahuan Pengangkatan DPT - Sistem Aplikasi Kelogistikan PT Nusantara Regas";
		$email['to'] = array();
		$email['to'][] = $email['dpt']['email'];
		foreach ($email['adm'] as $keyadm => $valueadm) {
			$email['to'][] = $valueadm['email'];
		}
		
		$this->am->approve($id);
		foreach ($email['to'] as $keyto => $valueto) {
			$this->utility->mail($valueto, $email['msg'], $email['subject']);
		}
		
		$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Vendor telah diangkat menjadi DPT!</p>');	
		redirect('admin/admin_vendor/waiting_list');
	}

	function mail($to,$message){
 		$this->email->clear(TRUE);

		$this->email->from('vms-noreply@nusantararegas.com', 'VMS REGAS');
		$this->email->to('arinal.dzikrul@dekodr.co.id'); 

		// $this->email->cc('alicia.ordinary@gmail.com'); 
		$this->email->bcc('muarifgustiar@gmail.com'); 
		$this->email->subject('Sistem Aplikasi Kelogistikan PT Nusantara Regas');
		
		$this->email->message($message);	
		$this->email->send();

		echo $this->email->print_debugger();
	}

	function mail_test(){

	$to = base64_encode("arinaldha@gmail.com");
	$sub = base64_encode("Blablab");
	$message = base64_encode("askjdgjasgdjkadsgjkasgfkjadsgjhagfjkagdsk");

	$url = "http://dekodr.co.id/send_mail/send.php";
	$data = http_build_query(array(
		'to' => $to,
		'sub' => $sub,
		'message' => $message
	));

	$options = array(
  		'http' => array(
    			'header'  => "Content-type: application/x-www-form-urlencoded",
    			'method'  => 'POST',
    			'content' => $data,
  		),
	);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
if($result)
	redirect('dashboard');
else
	echo '<script>alert("Gagal Mengirim Email!");location.reload();</script>';
}

	function mailer_test(){
		return $this->utility('arinaldha@gmail.com','Testing','Testing');
	}
}