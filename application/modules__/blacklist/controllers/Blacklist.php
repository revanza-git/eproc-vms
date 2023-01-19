<?php defined('BASEPATH') OR exit('No direct script access allowed');

class blacklist extends CI_Controller {

	public $blacklist_filter;

	public function __construct(){
		parent::__construct();
		if(!$this->session->userdata('admin')){
			redirect(site_url());
		}
		$this->load->model('blacklist_model','bm');
		$this->load->model('vendor/vendor_model', 'vm');
		// $this->load->library('encrypt');
		$this->load->library('form');
		
		$this->blacklist_filter = 	array(
										array(
											'label'	=>	'Penyedia Barang/Jasa',
											'filter'=>	array(
															array('table'=>'ms_vendor|name' ,'type'=>'text','label'=> 'Nama'),
															array('table'=>'ms_vendor_admistrasi|npwp_code' ,'type'=>'text','label'=> 'NPWP','class'=>'npwp_code'),
															array('table'=>'tr_blacklist|start_date' ,'type'=>'date','label'=> 'Tanggal Mulai Blacklist'),
															// array('table'=>'tr_blacklist|end_date' ,'type'=>'lifetime_date','label'=> 'Tanggal Selesai Blacklist')
														)
										),
									);
		$this->whitelist_filter = 	array(
										array(
											'label'	=>	'Penyedia Barang/Jasa',
											'filter'=>	array(
															array('table'=>'ms_vendor|name' ,'type'=>'text','label'=> 'Nama'),
															array('table'=>'ms_vendor_admistrasi|npwp_code' ,'type'=>'text','label'=> 'NPWP','class'=>'npwp_code'),
															array('table'=>'tr_blacklist|start_date' ,'type'=>'date','label'=> 'Tanggal Mulai whitelist'),
															// array('table'=>'tr_blacklist|end_date' ,'type'=>'lifetime_date','label'=> 'Tanggal Selesai Blacklist')
														)
										),
									);



	}

	

	public function index($id_blacklist=1){

		if($id_blacklist==1){
			$this->blacklist_filter[0][0]['filter'][] = array('table'=>'tr_blacklist|end_date' ,'type'=>'date','label'=> 'Tanggal Selesai Blacklist');
		}
		$this->session->unset_userdata('blacklist');
		$data 					= $this->bm->get_form_data($id_blacklist);
		$data['id_blacklist'] 	= $id_blacklist;
		$data['filter_list'] 	= $this->filter->group_filter_post($this->blacklist_filter);

		$search 	= $this->input->get('q');
		$page 		= '';		
		$per_page	= 10;

		$sort = $this->utility->generateSort(array('id_vendor', 'start_date', 'end_date','point','cateegory', 'remark', 'total_bl'));

		$data['blacklist']	= $this->bm->get_blacklist_list($id_blacklist,$search, $sort, $page, $per_page,TRUE);
		
		$data['pagination'] = $this->utility->generate_page('blacklist',$sort, $per_page, $this->bm->get_blacklist_list($id_blacklist,$search, $sort, '','',FALSE));
		$data['sort'] 		= $sort;

		$layout['content']	= $this->load->view('content',$data,TRUE);
		$layout['script']	= $this->load->view('content_js',$data,TRUE);

		// print_r($data['blacklist']);

		$item['header'] 	= $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		$item['content'] 	= $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}
	
	public function search(){
		$result = $this->bm->search_vendor();
		echo json_encode($result);
	}

	public function tambah($id=0){
		$_POST 	= $this->securities->clean_input($_POST,'save');
		$admin 	= $this->session->userdata('admin');
		$vendor = ($this->session->userdata('blacklist')) ? $this->session->userdata('blacklist') : array();



		$id_blacklist = (isset($vendor['id_blacklist'])) ? $vendor['id_blacklist'] : $id;
		$data['id_blacklist'] = $id;
		$data = $this->bm->get_form_data($id_blacklist);
		$data['id_blacklist'] = $id_blacklist;
		$data['vendor'] = $vendor;
		$data['end_date'] = $vendor['end_date'];
		

		// print_r($data['vendor']);
		$vld 	= array(
			array(
				'field'=>'id_vendor',
				'label'=>'Vendor',
				'rules'=>'required'
				),
			array(
				'field'=>'start_date',
				'label'=>'Tanggal Mulai',
				'rules'=>'required'
				),
			
			array(
				'field'=>'remark',
				'label'=>'Alasan (remark)',
				'rules'=>'required'
				),
			array(
				'field'=>'blacklist_file',
				'label'=>'Lampiran Pengesahan',
				'rules'=>'callback_do_upload[blacklist_file]'
				),
			);
		
		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			$_POST['entry_stamp'] = date("Y-m-d H:i:s");
			if($id_blacklist==1){
				$_POST['end_date'] = date('Y-m-d',strtotime($_POST['start_date'] . ' +2 years'));	
			}else{
				$_POST['end_date'] = 'lifetime';
			}
			// $_POST['edit_stamp'] = date("Y-m-d H:i:s");
			$_POST['data_status'] = 0;
			$result = $this->bm->save_data($this->input->post());
			
			if($this->session->userdata('blacklist')['site']){
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Menunggu approval Supervisor!</p>');
				redirect(site_url('blacklist/index/'.$id_blacklist)/*$this->session->userdata('blacklist')['site']*/);
			}
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Menunggu approval Supervisor!</p>');
			redirect(site_url('blacklist/index/'.$id_blacklist));
		}

		$data['remark_list']= $this->bm->get_remark($id_blacklist);


		$layout['content']= $this->load->view('tambah',$data,TRUE);
		$layout['script']= $this->load->view('tambah_js',$data,TRUE);


		$item['header'] = $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	

	public function edit($id,$id_blacklist='0'){
		$data 	= $this->bm->get_data($id);

		
		//echo 'asd';print_r($id_blacklist);

		// print_r($data);
		$_POST 	= $this->securities->clean_input($_POST,'save');
		$user 	= $this->session->userdata('user');
		$vld 	= array(
			
			array(
				'field'=>'id_vendor',
				'label'=>'Vendor',
				'rules'=>''
				),
			array(
				'field'=>'start_date',
				'label'=>'Tanggal Mulai',
				'rules'=>''
				),
			/*array(
				'field'=>'end_date',
				'label'=>'Tanggal Selesai',
				'rules'=>''
				),*/
			array(
				'field'=>'remark',
				'label'=>'Alasan (remark)',
				'rules'=>''
				),
			);

		if(!empty($_FILES['blacklist_file']['name'])){
			$vld[] = array(
					'field'=>'blacklist_file',
					'label'=>'Lampiran blacklist',
					'rules'=>'callback_do_upload[blacklist_file]'
					);
		}

		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			$_POST['edit_stamp'] = date("Y-m-d H:i:s");
			unset($_POST['Update']);

			$result = $this->bm->edit_data($this->input->post(),$id);
			if($result){
				$this->dpt->non_iu_change($user['id_user']);
			}

			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mengubah data!</p>');

			redirect(site_url('blacklist/index/'.$id_blacklist));
		}

		

		$layout['content']= $this->load->view('edit',$data,TRUE);

		$item['header'] = $this->load->view('admin/header', $this->session->userdata('admin'),TRUE);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function aktif($id){
		$_POST 	= $this->securities->clean_input($_POST,'save');

		$data = $this->bm->blacklist_data($id);

		$vld 	= array(
			array(
				'field'=>'white_date',
				'label'=>'Tanggal Pemutihan',
				'rules'=>'required'
				),			
			array(
				'field'=>'white_file',
				'label'=>'Lampiran Pengesahan',
				'rules'=>'callback_do_upload[white_file]'
				),
		);
		
		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			$_POST['edit_stamp'] = date("Y-m-d H:i:s");
			$_POST['id']		 = $id;
			$result = $this->bm->aktif($this->input->post());
			if($result){

			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Vendor Menunggu Persetujuan Supervisor Untuk diputihkan !</p>');				
			redirect(site_url('blacklist/index/'.$data['id_blacklist']));
			}
			
		}



		$layout['content']= $this->load->view('putih',$data,TRUE);


		$item['header'] = $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);

		$this->load->view('template',$item);
	}

	public function approve_form($id,$id_blacklist,$blacklist){
		$data 	= $this->bm->get_data($id_blacklist);

			$_POST['edit_stamp'] = date("Y-m-d H:i:s");
			$_POST['id_vendor'] = $id;
$blacklist = $data['id_blacklist'];			$_POST['id_blacklist'] = $id_blacklist;
			
			// echo $blacklist;die;
			$result = $this->bm->approve_blacklist($this->input->post());
			
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menambahkan vendor '.$data['value'].' !</p>');
			redirect(site_url('blacklist/index/'.$blacklist));
		
	}

	public function do_upload($field, $db_name = ''){	
		
		$file_name = $_FILES[$db_name]['name'] = $db_name.'_'.$this->utility->name_generator($_FILES[$db_name]['name']);
		
		$config['upload_path'] = './lampiran/'.$db_name.'/';
		$config['allowed_types'] = 'pdf|jpeg|jpg|png|gif|doc|docx';
		$config['max_size'] = 0;
		
		$this->load->library('upload');
		$this->upload->initialize($config);
		
		if ( ! $this->upload->do_upload($db_name)){
			$_POST[$db_name] = $file_name;
			$this->form_validation->set_message('do_upload', $this->upload->display_errors('',''));
			return false;
		}else{
			$_POST[$db_name] = $file_name; 
			return true;
		}
	}


	public function autocomplete(){
		// $keyword	= $this->input->post('keyword');
        $data 		= $this->bm->search_vendor();        
		echo json_encode($data);
	}


	public function whitelist(){

		$data['filter_list'] 	= $this->filter->group_filter_post($this->whitelist_filter);


		// print_r($data);
		$search 	= $this->input->get('q');
		$page 		= '';		
		$per_page	= 10;

		$sort = $this->utility->generateSort(array('id_vendor', 'white_date', 'point', 'category', 'remark'));

		$data['whitelist']	= $this->bm->whitelist_data();
		// print_r($data['whitelist']);
		$data['pagination'] = $this->utility->generate_page('blacklist/whitelist',$sort, $per_page, $this->bm->whitelist_data($search, $sort, '','',FALSE));
		$data['sort'] 		= $sort;

		$layout['content']	= $this->load->view('content_whitelist',$data,TRUE);
		$layout['script']	= $this->load->view('content_js',$data,TRUE);

		$item['header'] 	= $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		$item['content'] 	= $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function approve_aktif($id_blacklist){
		$data 	= $this->bm->get_data($id_blacklist);


			$_POST['edit_stamp'] = date("Y-m-d H:i:s");
			$_POST['id_vendor'] = $data['id_vendor'];
			$_POST['id_blacklist'] = $id_blacklist;
			


			$result = $this->bm->approve_white($this->input->post());
			
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Masuk daftar putih dan menunggu verifikasi ulang oleh Super Admin! </p>');
			redirect(site_url('blacklist/whitelist'));

	}

	public function approve_aktif_white($id_blacklist){
		$data 	= $this->bm->get_data($id_blacklist);
		// print_r($data);
		// if($this->input->post('approve')==TRUE){
			$_POST['edit_stamp'] = date("Y-m-d H:i:s");
			$_POST['id_vendor'] = $data['id_vendor'];
			$_POST['id_blacklist'] = $id_blacklist;
			
			// print_r($this->input->post());die;
			$result = $this->bm->approve_white($this->input->post());
			
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Vendor masuk ke daftar tunggu Super Admin!</p>');			
			redirect(site_url('blacklist/whitelist'));
		// }

		$layout['content']= $this->load->view('view',$data,TRUE);


		$item['header'] = $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);
		$this->load->view('template',$item);
	}

	public function export_excel($id_blacklist, $title=""){
		if ($id_blacklist ==1) {
			# code...
			$title = "Data Daftar Merah";
		}else{
			$title = "Data Daftar Hitam";			
		}
		$data = $this->bm->get_blacklist_list($id_blacklist,$search, $sort, $page, $per_page,TRUE);
		// print_r($data);die;	
		$table = "<table border=1>";

			$table .= "<tr>";
			$table .= "<td style='background: #f6e58d;'>No</td>";
			$table .= "<td style='background: #f6e58d;'>Nama Penyedia Barang/Jasa</td>";
			$table .= "<td style='background: #f6e58d;'>Tanggal Mulai</td>";
			$table .= "<td style='background: #f6e58d;'>Tanggal Selesai</td>";
			$table .= "<td style='background: #f6e58d;'>Total</td>";
			$table .= "<td style='background: #f6e58d;'>Remark</td>";
			$table .= "</tr>";

		foreach ($data as $key => $value) {
			# code...
			$no = $key + 1;
			$table .= "<tr>";
			$table .= "<td>".$no."</td>";
			$table .= "<td>".$value['name']."</td>";
			$table .= "<td>".$value['start_date']."</td>";
			$table .= "<td>".$value['end_date']."</td>";
			$table .= "<td>".$value['total_bl']."</td>";
			$table .= "<td>".$value['remark']."</td>";
			$table .= "</tr>";
		}
		$table .= "</table>";
		header('Content-type: application/ms-excel');

    	header('Content-Disposition: attachment; filename='.$title.'.xls');



		echo $table;
	}

	public function export_whitelist($title="Data Whitelist", $data){
		$data = $this->bm->whitelist_data();
		// print_r($data);die;	
		$table = "<table border=1>";

			$table .= "<tr>";
			$table .= "<td style='background: #f6e58d;'>No</td>";
			$table .= "<td style='background: #f6e58d;'>Nama Penyedia Barang/Jasa</td>";
			$table .= "<td style='background: #f6e58d;'>Tanggal Mulai</td>";
			$table .= "<td style='background: #f6e58d;'>Tanggal Selesai</td>";
			$table .= "<td style='background: #f6e58d;'>Total</td>";
			$table .= "<td style='background: #f6e58d;'>Remark</td>";
			$table .= "</tr>";

		foreach ($data as $key => $value) {
			# code...
			$no = $key + 1;
			$table .= "<tr>";
			$table .= "<td>".$no."</td>";
			$table .= "<td>".$value['name']."</td>";
			$table .= "<td>".$value['start_date']."</td>";
			$table .= "<td>".$value['end_date']."</td>";
			$table .= "<td>".$value['total_bl']."</td>";
			$table .= "<td>".$value['remark']."</td>";
			$table .= "</tr>";
		}
		$table .= "</table>";
		header('Content-type: application/ms-excel');

    	header('Content-Disposition: attachment; filename='.$title.'.xls');



		echo $table;
	}
}
