<?php defined('BASEPATH') OR exit('No direct script access allowed');

class K3 extends CI_Controller {

	public function __construct(){
		parent::__construct();
		if(!$this->session->userdata('user')&&!$this->session->userdata('admin')){
			redirect(site_url());
		}
		$this->load->model('K3_model','km');
		$this->load->library('utility');
		
	}
	public function index(){
		$user = $this->session->userdata('user');
		$data_k3['csms_file'] 	= $this->km->get_k3_all_data($user['id_user']);

		if(empty($data_k3['csms_file']['csms_file'])&&empty($data_k3['csms_file']['answer'])){
			redirect('k3/first_form');
		}
		

			$data_k3['data_k3']			= $this->km->get_k3_data($user['id_user']);
			$data_k3['field_quest'] 	= $this->km->get_field_quest();
			$data_k3['ms_quest']		= $this->km->get_header_list();
			$data_k3['sub_quest']		= $this->km->get_sub_quest_list();
			$data_k3['data_quest']		= $this->km->get_quest_list();
			$data_k3['data_field']		= $this->km->get_data_field();
			$data_k3['quest_all']		= array();

			foreach($data_k3['ms_quest'] as $key_ms => $row_ms){
				$data_k3['quest_all'][$key_ms]['label'] 	= $row_ms;
			}

			foreach($data_k3['sub_quest'] as $key_sub_quest => $val_sub_quest){
				foreach($val_sub_quest as $k_sub_quest => $v_sub_quest){
					$data_k3['quest_all'][$key_sub_quest]['data'][$k_sub_quest] = $v_sub_quest;
				}
			}

			foreach($data_k3['data_quest'] as $key_quest => $val_quest){
				$data_k3['quest_all'][$val_quest['id_ms_header']]['data'][$val_quest['id_sub_header']]['data'][$val_quest['id']] = array();
			}

			foreach ($data_k3['data_field'] as $key_data => $value_data) {
				$data_k3['quest_all'][$value_data['id_ms_header']]['data'][$value_data['id_sub_header']]['data'][$value_data['id_question']][$value_data['id']] = $value_data;	
			}
			$layout['content']	= $this->load->view('form_view',$data_k3,TRUE);

		

		$layout['script']	= $this->load->view('form_k3_js',NULL,TRUE);
		
		$item['header'] = $this->load->view('dashboard/header',$this->session->userdata('user'),TRUE);
		$item['content'] = $this->load->view('user/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function reset_csms(){
		$user = $this->session->userdata('user');
		$res = $this->km->reset_csms($this->session->userdata('user')['id_user']);
		if($res){
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mereset data!</p>');
			$this->dpt->non_iu_change($user['id_user']);
			redirect('k3');
		}else{
			redirect('k3');
		}
		
	}
	public function first_form(){
		$user = $this->session->userdata('user');
		if($this->input->post('next')){
			$vld = 	array(
				array(
					'field'=>'csms',
					'label'=>'csms_radio',
					'rules'=>'required'
					)
			);

			if($this->input->post('csms')==1){
				$vld 	= 	array(
								array(
									'field'=>'csms_file',
									'type'=>'file',
									'label'=>'Lampiran CSMS',
									'rules'=>'callback_do_upload_single[csms_file]'
								),
								// array(
								// 	'field'=>'expiry_date',
								// 	'label'=>'Masa Berlaku',
								// 	'rules'=>'required|callback_backdate'
								// ),
								array(
									'field'=>'score',
									'label'=>'Nilai',
									'rules'=>'required'
								)
							);
				// $vld[] = array(
				// 				'field'=>'csms_file',
				// 				'type'=>'file',
				// 				'label'=>'Lampiran CSMS',
				// 				'rules'=>'callback_do_upload_single[csms_file]'
				// 			);

				$this->form_validation->set_rules($vld);
				if($this->form_validation->run()==TRUE){
					print_r($this->form_validation->run());
					$_POST['entry_stamp'] = date("Y-m-d H:i:s");

					$res = $this->km->save_csms_data($this->input->post(),$user['id_user']);
					print_r($res);
					if($res){
						$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menambah data!</p>');
						$this->dpt->non_iu_change($user['id_user']);
						redirect(site_url('k3'));

					}
				}

			}else{
				print_r('failed');
				redirect(site_url('k3/csms_form'));

			}

		}

		$layout['content']	= $this->load->view('form_csms',NULL,TRUE);
		$layout['script']	= $this->load->view('form_k3_js',NULL,TRUE);
		
		$item['header'] = $this->load->view('dashboard/header',$this->session->userdata('user'),TRUE);
		$item['content'] = $this->load->view('user/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function csms_edit(){
		$user = $this->session->userdata('user');
		$data 	= $this->km->get_k3_all_data($user['id_user'])['csms_file'];
		

		$vld 	= 	array(
							array(
								'field'=>'expiry_date',
								'label'=>'Masa Berlaku',
								'rules'=>'required'
							),
							array(
								'field'=>'score',
								'label'=>'Nilai',
								'rules'=>'required'
							)
						);

		if(!empty($_FILES['csms_file']['name'])) {
			$vld[] = array(
								'field'=>'csms_file',
								'label'=>'Lampiran CSMS',
								'rules'=>'callback_do_upload_single[csms_file]'
							);
		}

		if($this->input->post('next')){
			
			$this->form_validation->set_rules($vld);
			if($this->form_validation->run()==TRUE){
				unset($_POST['next']);
				$_POST['entry_stamp'] = date("Y-m-d H:i:s");
				$res = $this->km->edit_csms_data($this->input->post(),$user['id_user'],$data['id']);

				if($res){

					$this->dpt->edit_data($id,'ms_csms');
					$this->dpt->non_iu_change($user['id_user']);
					$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mengubah data!</p>');
					
					redirect(site_url('k3'));

				}
			}
		}

		$layout['content']	= $this->load->view('csms_edit',$data,TRUE);
		$item['header'] = $this->load->view('dashboard/header',$this->session->userdata('user'),TRUE);
		$item['content'] = $this->load->view('user/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function csms_form(){	
		$user = $this->session->userdata('user');
		/*$data['ms_quest']		= $this->km->get_master_header();$data['sub_quest']		= $this->km->get_sub_header();$data['quest']			= $this->km->get_quest();$data['field_quest'] 	= $this->km->get_field_quest();*/
		$data['data_k3']		= $this->km->get_k3_data($user['id_user']);
		$data['csms_file'] 		= $this->km->get_k3_all_data($user['id_user']);
		$data['field_quest'] 	= $this->km->get_field_quest();
		$data['ms_quest']		= $this->km->get_header_list();
		$data['sub_quest']		= $this->km->get_sub_quest_list();
		$data['data_quest']		= $this->km->get_quest_list();
		$data['data_field']		= $this->km->get_data_field();
		$data['quest_all']		= array();
		$vendor					= $this->km->get_vendor_data($user['id_user']);

		$email['subject']	= "Penilaian CSMS (".$vendor['type'].". ".$vendor['name'].") - Sistem Aplikasi Kelogistikan PT Nusantara Regas";
		$email['message']	= $vendor['type'].". ".$vendor['name']." Telah selesai melengkapi data untuk penilaian CSMS dalam Sistem Aplikasi Kelogistikan PT Nusantara Regas.
								<br>
								Untuk melengkapi proses verifikasi, harap login ke sistem dan mengakses menu Penilaian CSMS.
								<br><br>
								Terimakasih,<br>
								PT Nusantara Regas";

		foreach($data['ms_quest'] as $key_ms => $row_ms){
			$data['quest_all'][$key_ms]['label'] 	= $row_ms;
		}

		foreach($data['sub_quest'] as $key_sub_quest => $val_sub_quest){
			foreach($val_sub_quest as $k_sub_quest => $v_sub_quest){
				$data['quest_all'][$key_sub_quest]['data'][$k_sub_quest] = $v_sub_quest;
			}
		}

		foreach($data['data_quest'] as $key_quest => $val_quest){
			$data['quest_all'][$val_quest['id_ms_header']]['data'][$val_quest['id_sub_header']]['data'][$val_quest['id']] = array();
		}

		foreach ($data['data_field'] as $key_data => $value_data) {
			$data['quest_all'][$value_data['id_ms_header']]['data'][$value_data['id_sub_header']]['data'][$value_data['id_question']][$value_data['id']] = $value_data;	
		}
		// echo print_r($data['quest_all']);

		if($this->input->post('simpan')){
				foreach($this->km->get_admin_email(3) as $key => $admin){
					$this->utility->mail($admin['email'],$email['message'],$email['subject']);
				}

			$this->do_upload($_FILES['quest'],$data['field_quest']);
			$res = $this->km->save_k3_data($this->input->post('quest'),$user['id_user']);
			if($res){

				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menyimpan data!</p>');
				redirect('k3');
			}
		}
		// echo print_r($this->km->get_k3_data($user['id_user']));
		if(count($this->km->get_k3_data($user['id_user']))>0){
			$layout['content']= $this->load->view('form_view',$data,TRUE);
		}else{
			$layout['content']= $this->load->view('form',$data,TRUE);
		}

		$item['header'] 	= $this->load->view('dashboard/header',$this->session->userdata('user'),TRUE);
		$item['content'] 	= $this->load->view('user/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function edit(){
		$user = $this->session->userdata('user');
		$data['ms_quest']		= $this->km->get_master_header();
		$data['sub_quest']		= $this->km->get_sub_header();
		$data['quest']			= $this->km->get_quest();
		$data['field_quest'] 	= $this->km->get_field_quest();
		$data['data_k3']		= $this->km->get_k3_data($user['id_user']);
		$vendor					= $this->km->get_vendor_data($user['id_user']);
		// print_r($this->km->get_admin_email(3));die;
		// print_r($vendor);die;

		$email['subject']	= "Penilaian CSMS (".$vendor['type'].". ".$vendor['name'].") - Sistem Aplikasi Kelogistikan PT Nusantara Regas";
		$email['message']	= $vendor['type'].". ".$vendor['name']." Telah selesai melengkapi data untuk penilaian CSMS dalam Sistem Aplikasi Kelogistikan PT Nusantara Regas.
								<br>
								Untuk melengkapi proses verifikasi, harap login ke sistem dan mengakses menu Penilaian CSMS.
								<br><br>
								Terimakasih,<br>
								PT Nusantara Regas";

		if($this->input->post('simpan')){
			$this->do_upload($_FILES['quest'],$data['field_quest']);
			

			$res = $this->km->update_k3_data($this->input->post('quest'),$user['id_user'],'edit');
			if($res){

				foreach($this->km->get_admin_email(3) as $key => $admin){
					$this->utility->mail($admin['email'],$email['message'],$email['subject']);
				}

				// $this->dpt->set_email_blast($res,'ms_csms','Lampiran CSMS',$_POST['expire_date']);
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menyimpan data!</p>');
				redirect('k3');
			}
			
		}

		$layout['content']= $this->load->view('form_edit',$data,TRUE);		

		$item['header'] = $this->load->view('dashboard/header',$this->session->userdata('user'),TRUE);
		$item['content'] = $this->load->view('user/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}
	
	public function get_field(){
		return array(
			array(
				'label'	=>	'User',
				'filter'=>	array(
								array('table'=>'ms_vendor|name' ,'type'=>'text','label'=> 'Nama'),
								array('table'=>'ms_score_k3|score' ,'type'=>'number_range','label'=> 'Skor'),
							)
			),
			
		);
	}
	
	public function get_vendor_group(){
		$this->load->library('form');

		$data['filter_list'] = $this->filter->group_filter_post($this->get_field());
		$search = $this->input->get('q');
		$page = '';


		$per_page=10;

		$sort = $this->utility->generateSort(array('name','score'));
		
		$data['vendor_list']=$this->km->get_k3_vendor($search, $sort, $page, $per_page,TRUE);

		// $data['filter_list'] = $this->form->group_filter_post($this->get_field());

		$data['pagination'] = $this->utility->generate_page('k3/get_vendor_group',$sort, $per_page, $this->km->get_k3_vendor($search, $sort, '','',FALSE));
		$data['sort'] = $sort;
		$layout['content']= $this->load->view('k3/content_dpt',$data,TRUE);
		$item['header'] = $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function backdate($str){
		$date = strtotime($str);
		if($date <= strtotime(date('Y-m-d'))){
			$this->form_validation->set_message('backdate', 'Tanggal tidak boleh lampau');
			return false;
		}else{
			return true;
		}
	}
	
	public function history_nilai($id){
		$this->load->library('form');
		$search = $this->input->get('q');
		$page = '';
		// unset($_POST);

		$per_page=10;

		$sort = $this->utility->generateSort(array('name','score','entry_stamp'));
		$data['pagination'] = $this->utility->generate_page('admin/admin_dpt',$sort, $per_page, $this->km->get_history_nilai($id, $sort, '','',FALSE));
		$data['sort'] = $sort;
		$data['history']=$this->km->get_history_nilai($id);

		$layout['content']= $this->load->view('k3/history_nilai',$data,TRUE);
		$item['header'] = $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}
	public function penilaian_k3($id_vendor,$act = 'create',$id_csms=0){
		$data['act'] = $act;
		$data['vendor'] = $this->vm->get_data($id_vendor);
		$data['ms_quest']=$this->km->get_master_header();
		
		$data['quest']=$this->km->get_quest();
		$data['field_quest'] = $this->km->get_field_quest();
		$data['evaluasi_list'] = $this->km->get_evaluasi_list();
		$data['evaluasi'] = $this->km->get_evaluasi();
		$data['data_k3']=$this->km->get_k3_data($id_vendor);
		$data['get_csms'] = $this->km->get_csms($id_vendor);
		$data['get_hse'] = $this->km->get_hse($id_vendor);
		
		$data['csms_file'] 	= $this->km->get_k3_all_data($id_vendor)['csms_file'];

		$data['value_k3']=$this->km->get_penilaian_value($id_vendor,$data['csms_file']['id']);	
		$vendor					= $this->km->get_vendor_data($id_vendor);


		if($this->input->post('simpan')){
			
			$res = $this->km->save_evaluasi_poin($this->input->post('evaluasi'),$id_vendor,$act,$id_csms);
			
			if($res){
				$email['subject']	= "Penilaian CSMS (".$vendor['type'].". ".$vendor['name'].") - Sistem Aplikasi Kelogistikan PT Nusantara Regas";
				$email['message']	= 'Admin Logistik telah selesai mengadakan penilaian CSMS untuk Penyedia Barang / Jasa '.$vendor['type'].". ".$vendor['name']."  dalam Sistem Aplikasi Kelogistikan PT Nusantara Regas.
										<br>
										Untuk melengkapi proses verifikasi, harap login ke sistem dan mengakses menu Penilaian CSMS.
										<br><br>
										Terimakasih,<br>
										PT Nusantara Regas";
				foreach($this->km->get_admin_email(1) as $key => $admin){
					$this->utility->mail($admin['email'],$email['message'],$email['subject']);
				}
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menyimpan data!</p>');
				redirect('k3/penilaian_view/'.$id_vendor);
			}
		}
		$layout['content']= $this->load->view('k3/penilaian_k3',$data,TRUE);	
		$layout['script']= $this->load->view('k3/penilaian_k3_js',$data,TRUE);

		$item['header'] = $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function penilaian_view($id_vendor){
		$data['id'] = $id_vendor;
		$data['vendor'] = $this->vm->get_data($id_vendor);
		
		$data['ms_quest']=$this->km->get_master_header();
		$data['quest']=$this->km->get_quest();
		$data['field_quest'] = $this->km->get_field_quest();
		$data['evaluasi_list'] = $this->km->get_evaluasi_list();
		$data['evaluasi'] = $this->km->get_evaluasi();
		$data['get_csms'] = $this->km->get_csms($id_vendor);

		$data['data_k3']=$this->km->get_k3_data($id_vendor);
		$data['csms_limit']=$this->km->get_csms_limit($id_vendor);



		$data['data_poin']=$this->km->get_poin($id_vendor);
		$data['csms_file'] 	= $this->km->get_k3_all_data($id_vendor)['csms_file'];
		
		
		$data['value_k3']=$this->km->get_penilaian_value($id_vendor,$data['csms_file']['id']);
		
		// print_r($data);

		$layout['content']= $this->load->view('k3/penilaian_view',$data,TRUE);

		$item['header'] = $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}
	public function do_upload($files,$quest){
		// echo print_r($files);
		$this->load->library('upload');	
		$this->load->helper('url');
		$config['allowed_types'] = 'pdf|jpeg|jpg|png|gif';
		$config['max_size'] = '0';

		$all_success = TRUE;
		foreach($files['name'] as $key => $file){
			if($file=='') continue; // nothing uploaded for this field

			// Sanitize folder name (question label) → lowercase, underscores, alnum only
			$raw_folder = isset($quest[$key]['label']) ? $quest[$key]['label'] : 'k3_files';
			$folder     = url_title($raw_folder, '_', TRUE);
			if($folder === ''){ $folder = 'k3_files'; }

			$file_name = $folder.'_'.$this->utility->name_generator($files['name'][$key]);

			// Re-assign single-file array expected by CI Upload library
			$_FILES['quest']['name']     = $file_name;
			$_FILES['quest']['size']     = $files['size'][$key];
			$_FILES['quest']['tmp_name'] = $files['tmp_name'][$key];
			$_FILES['quest']['type']     = $files['type'][$key];
			$_FILES['quest']['error']    = $files['error'][$key];

			// Build / ensure target directory
			$base = realpath(FCPATH.'../lampiran');
			if($base === FALSE){
				$base = FCPATH.'lampiran';
			}
			$base = rtrim($base,'/\\');
			$config['upload_path'] = $base.'/'.$folder.'/';
			if(!is_dir($config['upload_path'])){
				mkdir($config['upload_path'],0755,true);
			}

			$this->upload->initialize($config);

			if ( ! $this->upload->do_upload('quest')){
				// Preserve file name so DB still gets value (even if upload failed) for debugging
				$_POST['quest'][$key] = $file_name;
				$all_success = FALSE;
				log_message('error','K3::do_upload failed for '.$file_name.' – '.$this->upload->display_errors('',''));
			}else{
				$_POST['quest'][$key] = $file_name;
			}
		}

		return $all_success;
	}
	public function do_upload_single($field, $db_name = 'k3_files'){	
		
		$file_name = $_FILES[$db_name]['name'] = $db_name.'_'.$this->utility->name_generator($_FILES[$db_name]['name']);
		
		// $config['upload_path'] = './assets/lampiran';
		$base = realpath(FCPATH.'../lampiran');
		if($base === FALSE){
			$base = FCPATH.'lampiran';
		}
		$base = rtrim($base,'/\\');
		$config['upload_path'] = $base.'/k3_files/';
		if(!is_dir($config['upload_path'])){
			mkdir($config['upload_path'],0755,true);
		}
		$config['allowed_types'] = 'pdf|jpeg|jpg|png|gif|doc|docx';
		$config['max_size'] = '5096';
		$config['overwrite'] = FALSE;
		$config['remove_spaces'] = TRUE;
		
		$this->load->library('upload');
		$this->upload->initialize($config);
		
		if ( ! $this->upload->do_upload('csms_file')){
			$upload_errors = $this->upload->display_errors('','');
			
			// Check if the file actually exists (sometimes upload "fails" but file is uploaded)
			$uploaded_file_path = $config['upload_path'] . $file_name;
			if (file_exists($uploaded_file_path)) {
				// File exists despite "error" - this is a false positive
				$_POST[$db_name] = $file_name; 
				return true;
			}
			
			// Only log error if file truly failed to upload
			if (strpos($upload_errors, 'Jenis file tidak diperbolehkan') === false) {
				log_message('error','K3::do_upload failed for '.$file_name.' – '.$upload_errors);
			}
			
			$_POST[$db_name] = $file_name;
			$this->form_validation->set_message('do_upload_single', $upload_errors);
			return false;
		}else{
			$_POST[$db_name] = $file_name; 
			return true;
		}
	}

	public function export_excel($title="Data CSMS", $data){
		$data = $this->km->get_k3_vendor($search, $sort, $page, $per_page,TRUE);
		// print_r($data);die;	
		$table = "<table border=1>";

			$table .= "<tr>";
			$table .= "<td style='background: #f6e58d;'>No</td>";
			$table .= "<td style='background: #f6e58d;'>Nama Penyedia Barang/Jasa</td>";
			$table .= "<td style='background: #f6e58d;'>Score</td>";
			$table .= "</tr>";

		foreach ($data as $key => $value) {
			# code...
			$no = $key + 1;
			$table .= "<tr>";
			$table .= "<td>".$no."</td>";
			$table .= "<td>".$value['name']."</td>";
			$table .= "<td>".$value['score']."</td>";
			$table .= "</tr>";
		}
		$table .= "</table>";
		header('Content-type: application/ms-excel');

    	header('Content-Disposition: attachment; filename='.$title.'.xls');



		echo $table;
	}
}
