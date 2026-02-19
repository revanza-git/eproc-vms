<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Katalog extends CI_Controller {

	public function __construct(){
		parent::__construct();
		if(!$this->session->userdata('admin')){
			redirect(site_url());
		}
		$this->load->model('katalog_model','km');
		
		$this->load->library('form');
		
	}


	public function get_field(){
		return array(
			array(
				'label'	=>	'Katalog',
				'filter'=>	array(
								array('table'=>'ms_material|nama' ,'type'=>'text','label'=> 'Nama'),
								// array('table'=>'tr_material_price|price' ,'type'=>'number_range','label'=> 'Harga'),
								// array('table'=>'ms_material|category|get_category_katalog' ,'type'=>'dropdown','label'=> 'Kategori'),							
							)
			),
			
		);
	}

	public function index($category){

		$filter = $this->get_field();
		if ($category == 'barang') {
			$filter[0]['filter'][] = array('table'=>'ms_material|for|get_for_barang' ,'type'=>'dropdown','label'=> 'Pengguna Barang/Jasa');
		} else {
			$filter[0]['filter'][] = array('table'=>'ms_material|for|get_for_jasa' ,'type'=>'dropdown','label'=> 'Pengguna Jasa');
		}

		$data['filter_list'] = $this->filter->group_filter_post($filter);
		$compare			= $this->session->userdata($category);
		$barang_compare 	= $this->km->get_barang_compare($compare);

		$data['category'] = $category;
		$data['compare'] = $compare;
		$data['barang_compare'] = $barang_compare;
		$search = $this->input->get('q');
		$sort = $this->input->get('sort', TRUE) ? $this->input->get('sort', TRUE) : '';
		$page 		= '';
		$per_page	= 12;
		$data['katalog'] 	= $this->km->get_katalog($category, $search, $sort, $page,$per_page,TRUE);
		
		$data['pagination'] = $this->utility->generate_page('katalog/index/'.$category.'',$sort, $per_page, $this->km->get_katalog($category, $search, $sort,  $page ,$per_page,FALSE));
		$data['sort'] 		= $sort;

		$layout['content']	= $this->load->view('content',$data,TRUE);
		$layout['script']	= $this->load->view('content_js',$data,TRUE);
		if($this->session->userdata('admin')['id_role']==6){
			$item['header'] 	= $this->load->view('auction/header',$this->session->userdata('admin'),TRUE);
			$item['content'] 	= $this->load->view('auction/dashboard',$layout,TRUE);
		}else{
			$item['header'] 	= $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
			$item['content'] 	= $this->load->view('admin/dashboard',$layout,TRUE);
		}
		$this->load->view('template',$item);
	}


	public function view($category, $id){

		$id_compare		= $this->session->userdata($category);

		$data['category'] = $category;

		$search 	= $this->input->get('q');
		$page 		= '';
		$data['item'] 		= $this->km->get_data_barang($id);
		

		$per_page=10;
		$data['id'] = $id;
		
		$sort 				= $this->utility->generateSort(array('price','date', 'vendor_name'));
		$data['sort'] 		= $sort;

		$data['chart'] =  $this->km->data_chart($id);
		
		
		$data['list_harga']	= $this->km->get_harga($id);
		
		$data['pagination'] = $this->utility->generate_page('katalog/view/'.$id,$sort, $per_page, $this->km->get_harga($search, $sort, '', '',TRUE));
		$layout['content']	= $this->load->view('view',$data,TRUE);
		$layout['script']	= $this->load->view('content_js',$data,TRUE);

		if($this->session->userdata('admin')['id_role']==6){
			$item['header'] 	= $this->load->view('auction/header',$this->session->userdata('admin'),TRUE);
			$item['content'] 	= $this->load->view('auction/dashboard',$layout,TRUE);
		}else{
			$item['header'] 	= $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
			$item['content'] 	= $this->load->view('admin/dashboard',$layout,TRUE);
		}
		$this->load->view('template',$item);
	}

	
	public function search(){
		$result = $this->km->search_katalog();
		echo json_encode($result);
	}
	public function tambah($category){
		$_POST 	= $this->securities->clean_input($_POST,'save');
		$admin 	= $this->session->userdata('admin');
		// echo [ri]

		$data['category'] = $category;
		$vld 	= array(
			
			array(
				'field'=>'nama',
				'label'=>'Nama Barang',
				'rules'=>'required'
				),
			array(
				'field'=>'remark',
				'label'=>'Remark',
				'rules'=>''
				),
			array(
				'field'=>'category',
				'label'=>'Kategory',
				'rules'=>'required'
				),
			array(
				'field'=>'id_kurs',
				'label'=>'Mata Uang',
				'rules'=>'required'
				),
			array(
				'field'=>'for',
				'label'=>'Pengguna Barang/Jasa',
				'rules'=>'required'
				)			
			);
		if(!empty($_FILES['gambar_barang']['name'])){
			$vld[] = array(
					'field'=>'gambar_barang',
					'label'=>'Lampiran',
					'rules'=>'callback_do_upload[gambar_barang]'
					);
		}
		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			// print_r($this->input->post());die;
			$_POST['entry_stamp'] = date("Y-m-d H:i:s");
			$_POST['id_vendor'] = NULL;

			$result = $this->km->save_barang($this->input->post());
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menambah '.$category.'!</p>');
			redirect(site_url('katalog/index/'.$category));
		}

		$layout['content']= $this->load->view('tambah',$data,TRUE);

		if($this->session->userdata('admin')['id_role']==6){
			$item['header'] 	= $this->load->view('auction/header',$this->session->userdata('admin'),TRUE);
			$item['content'] 	= $this->load->view('auction/dashboard',$layout,TRUE);
		}else{
			$item['header'] 	= $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
			$item['content'] 	= $this->load->view('admin/dashboard',$layout,TRUE);
		}
		$this->load->view('template',$item);
	}
	public function edit_barang($category, $id){
		$_POST 	= $this->securities->clean_input($_POST,'save');
		$admin 	= $this->session->userdata('admin');
		$data 	= $this->km->get_data_barang($id);

		// print_r($data);
		$vld 	= array(
			
			array(
				'field'=>'nama',
				'label'=>'Nama Barang',
				'rules'=>'required'
				),
			array(
				'field'=>'remark',
				'label'=>'Remark',
				'rules'=>''
				),
			array(
				'field'=>'category',
				'label'=>'Katagori',
				'rules'=>'required'
				),
			array(
				'field'=>'for',
				'label'=>'Pengguna Barang/Jasa',
				'rules'=>'required'
				),
			array(
				'field'=>'id_kurs',
				'label'=>'Mata Uang',
				'rules'=>'required'
				),
			array(
				'field'=>'for',
				'label'=>'Pengguna Barang/Jasa',
				'rules'=>'required'
				)
			);
		if(!empty($_FILES['gambar_barang']['name'])){
			$vld[] = array(
					'field'=>'gambar_barang',
					'label'=>'Lampiran',
					'rules'=>'callback_do_upload[gambar_barang]'
					);
		}
		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			unset($_POST['Simpan']);
			$_POST['edit_stamp'] = date("Y-m-d H:i:s");
			

			$result = $this->km->edit_barang($this->input->post(), $id);
			// echo print_r($this->input->post());
			if($result){
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mengubah barang!</p>');
				redirect(site_url('katalog/index/'.$_POST['category']));
			}

		}
		if ($category == 'jasa') {
			$arr = array('6' => 'Jasa Konsultasi','7' => 'Jasa Konstruksi', '8' => 'Jasa Lainnya');
		} else {
			$arr = array('1' => 'Divisi Operasi','10' => 'Divisi Reliability&Quality','18' => 'Departemen Layanan Umum','15' => 'Seksi Sistem Informasi', '3' => 'Departemen Sekretaris Perusahaan', '5' => 'Departemen HSSE');
		}
		$data['v_for'] = $data['for'];
		$data['for'] = $arr;
		$layout['content']= $this->load->view('edit',$data,TRUE);


		if($this->session->userdata('admin')['id_role']==6){
			$item['header'] 	= $this->load->view('auction/header',$this->session->userdata('admin'),TRUE);
			$item['content'] 	= $this->load->view('auction/dashboard',$layout,TRUE);
		}else{
			$item['header'] 	= $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
			$item['content'] 	= $this->load->view('admin/dashboard',$layout,TRUE);
		}
		$this->load->view('template',$item);
	}

	public function hapus_barang($category,$id){
		if($this->km->delete_barang($id)){
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menghapus data!</p>');
			redirect(site_url('katalog/index/'.$category));
		}else{
			$this->session->set_flashdata('msgSuccess','<p class="msgError">Gagal menghapus data!</p>');
			redirect(site_url('katalog/index/'.$category));
		}
	}

	public function do_upload($field, $db_name = ''){	
		
		$file_name = $_FILES[$db_name]['name'] = $db_name.'_'.$this->utility->name_generator($_FILES[$db_name]['name']);
		
		$config['upload_path'] = './lampiran/'.$db_name.'/';
		$config['allowed_types'] = 'jpeg|jpg|png|gif';
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

	public function tambah_harga($category, $id){
		$_POST 	= $this->securities->clean_input($_POST,'save');
		$admin 	= $this->session->userdata('admin');
		$data 	= $this->km->get_data_barang($id);
		
		$vld 	= array(
			
			array(
				'field'=>'date',
				'label'=>'Tanggal',
				'rules'=>'required'
				),
			array(
				'field'=>'price',
				'label'=>'Harga',
				'rules'=>'required'
				)
			);
		
		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			$_POST['id_material'] = $id;
			$_POST['price'] = preg_replace("/[,]/", "", $this->input->post('price'));;
			$_POST['entry_stamp'] = date("Y-m-d H:i:s");

			$result = $this->km->save_harga_barang($id, $this->input->post());
			if($result){
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menambah harga '.$category.'!</p>');
				
				redirect(site_url('katalog/view/'.$category.'/'.$id));
			}
		}

		$data['title'] = "Tambah Harga ".$category;
		$layout['content']= $this->load->view('tambah_harga',$data,TRUE);

		if($this->session->userdata('admin')['id_role']==6){
			$item['header'] 	= $this->load->view('auction/header',$this->session->userdata('admin'),TRUE);
			$item['content'] 	= $this->load->view('auction/dashboard',$layout,TRUE);
		}else{
			$item['header'] 	= $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
			$item['content'] 	= $this->load->view('admin/dashboard',$layout,TRUE);
		}
		$this->load->view('template',$item);
	}

	public function edit_harga($id_material,$id,$category){
		$_POST 	= $this->securities->clean_input($_POST,'save');
		$admin 	= $this->session->userdata('admin');
		$data 	= $this->km->get_harga_barang($id);
		$barang = $this->km->get_data_barang($id_material);
		$vld 	= array(
			
			array(
				'field'=>'date',
				'label'=>'Tanggal',
				'rules'=>'required'
				),
			array(
				'field'=>'price',
				'label'=>'Harga',
				'rules'=>'required'
				)
			);
		
		$this->form_validation->set_rules($vld);
		if($this->form_validation->run()==TRUE){
			$_POST['price'] = preg_replace("/[,]/", "", $this->input->post('price'));;
			$_POST['edit_stamp'] = date("Y-m-d H:i:s");
			unset($_POST['Simpan']);
			$result = $this->km->edit_harga_barang($this->input->post(),$id);
			if($result){
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mengubah harga '.$barang['category'].'!</p>');
				
				redirect(site_url('katalog/view/'.$barang['category'].'/'.$id_material));
			}
		}

		$data['title'] = "Ubah Harga ".$category;
		$layout['content']= $this->load->view('tambah_harga',$data,TRUE);
		if($this->session->userdata('admin')['id_role']==6){
			$item['header'] 	= $this->load->view('auction/header',$this->session->userdata('admin'),TRUE);
			$item['content'] 	= $this->load->view('auction/dashboard',$layout,TRUE);
		}else{
			$item['header'] 	= $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
			$item['content'] 	= $this->load->view('admin/dashboard',$layout,TRUE);
		}
		$this->load->view('template',$item);
	}
	public function hapus_harga($category,$id_material,$id){
		if($this->km->delete_harga($id)){
			$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses menghapus data!</p>');
			redirect(site_url('katalog/view/'.$category.'/'.$id_material));
		}else{
			$this->session->set_flashdata('msgSuccess','<p class="msgError">Gagal menghapus data!</p>');
			redirect(site_url('katalog/view/'.$category.'/'.$id_material));
		}
	}
	public function autocomplete(){
		$keyword	= $this->input->post('keyword');
        $data 		= $this->bm->get_autocomplete($keyword);        
        echo json_encode($data);
	}

	public function compare($category,$id){
			$compare = ($this->session->userdata($category))?$this->session->userdata($category):array();
			if(count($compare)>10){
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Maksimal 10 '.$category.'!</p>');
				redirect('katalog/index/'.$category);
			}else{
				$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Berhasil menambahkan '.$category.' ke daftar perbandingan!</p>');
				$compare[$id] = $id;
				$compare = $this->session->set_userdata($category,$compare);
				redirect('katalog/index/'.$category);
			}
	}

	public function remove_compare($category,$id){
			unset($_SESSION[$category][$id]);
			redirect('katalog/index/'.$category);
	}


	public function get_chart_data($id){
		$result = $this->km->data_chart_compare($id);
		
		return $result;
	}

	public function lihat_perbandingan($category){
		
		$id_compare = $this->session->userdata($category);
		$id_arr = array();
		if ($id_compare != ''){

			$id_arr = $id_compare;
		}
		$kurs = array();

		$data['item'] 		= $this->km->get_data_compare($id_arr);
		// print_r($data['item']);
		$data['kurs']		= "['";
		foreach($data['item'] as $value){
			$kurs[] = $value['symbol'];
		}
		$data['kurs']		.= implode("','", $kurs);
		$data['kurs']		.= "'];";
		// $data['chart'] 		= $this->km->data_chart_compare($id_compare);


		foreach ($id_arr as $key => $value) {
			$data['chart'][$value] 							= $this->get_chart_data($value);
			// $priceYear									= $this->

			// $data['chart'][$value][$key]['avg_years'] 		= $arrayName ;
			foreach ($data['chart'][$value] as $keyyears 	=> $valueyears) {
				// print_r($valueyears);
				if( $valueyears['years']!=''){
					$data['years'][$valueyears['years']]	= $valueyears['years'];
					$data['price'][$value][$valueyears['years']]	= $valueyears['avg_year'];
				}
			// print_r($data['chart'][$value]);
			}
		}
		// print_r($data['price']);
		sort($data['years'],1);
		$layout['content']	= $this->load->view('compare',$data,TRUE);
		$layout['script']	= $this->load->view('compare_js',$data,TRUE);

		if($this->session->userdata('admin')['id_role']==6){
			$item['header'] 	= $this->load->view('auction/header',$this->session->userdata('admin'),TRUE);
			$item['content'] 	= $this->load->view('auction/dashboard',$layout,TRUE);
		}else{
			$item['header'] 	= $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);
			$item['content'] 	= $this->load->view('admin/dashboard',$layout,TRUE);
		}
		$this->load->view('template',$item);
		
	}
	
	
}
