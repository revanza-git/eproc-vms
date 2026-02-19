<?php defined('BASEPATH') OR exit('No direct script access allowed');



class Admin_dpt extends CI_Controller {

	public function __construct(){

		parent::__construct();

		if(!$this->session->userdata('admin')){

			redirect(site_url());

		}

		$this->load->model('user/admin_user_model','aum');

		$this->load->model('vendor/vendor_model','vm');
		
	}

	public function get_field(){
		return array(
			array(
				'label'	=>	'DPT (Daftar Penyedia Terpilih)',
				'filter'=>	array(
								array('table'=>'ms_vendor|name' ,'type'=>'text','label'=> 'Nama Penyedia Barang/Jasa'),
								array('table'=>'tb_legal|name' ,'type'=>'text','label'=> 'Badan Usaha'),
								array('table'=>'ms_vendor_admistrasi|npwp_code' ,'type'=>'text','label'=> 'NPWP'),
								array('table'=>'ms_vendor_admistrasi|vendor_address' ,'type'=>'text','label'=> 'Alamat'),
								array('table'=>'ms_vendor_admistrasi|vendor_phone' ,'type'=>'text','label'=> 'Telepon'),
								array('table'=>'ms_vendor|dpt_first_date' ,'type'=>'date','label'=> 'Tanggal Pengangkatan Awal'),
								array('table'=>'tr_dpt|start_date' ,'type'=>'date','label'=> 'Tanggal Pengangkatan Terakhir')
							)
			),
		);
	}

	public function index(){

		$this->load->library('form');

		$this->load->library('datatables');

		$search = $this->input->get('q');

		// Fix: Remove the empty page assignment
		// $page = '';

		

			if(isset($_POST['simpan']) && count($_POST['simpan'])){
				// print_r($_POST);
				unset($_POST['simpan']);
				$nomor = $_POST['certificate_no'];
				$id_vendor = $_POST['certificate_id'];

				$update = $this->vm->update_certificate($id_vendor, $nomor);

				if($update){
					$this->load->driver('cache', array('adapter' => 'file', 'backup' => 'file'));
					$this->cache->clean();
					$this->session->set_flashdata('msgSuccess','<p class="msgSuccess">Sukses mengubah nomor sertifikat!</p>');

					redirect(site_url('admin/admin_dpt/'));
				}

				

			}

			

	
		// Increase per_page for better performance
		$per_page = 25; // Increased from 10 to 25

		$sort = $this->utility->generateSort(array('name','category','npwp_code','vendor_address','id','vendor_phone'));
		$filter = $this->input->post('filter'); // Get filter from POST

		// Fix: Properly get the current page from URL parameters  
		$current_page = ($this->input->get('per_page')) ? (int)$this->input->get('per_page') : 1;
		
		// Load cache driver for performance
		$this->load->driver('cache', array('adapter' => 'file', 'backup' => 'file'));

		// Create cache keys - Fix: Include current page in cache key
		$cache_key = 'dpt_list_' . md5(serialize([
			'search' => $search,
			'page' => $current_page,
			'sort' => $sort,
			'filter' => $filter
		]));
		
		$count_cache_key = 'dpt_count_' . md5(serialize(['search' => $search, 'filter' => $filter]));

		// Check if we have cached results (cache for 5 minutes)
		$cache_duration = 300; // 5 minutes
		$vendor_list = $this->cache->get($cache_key);
		$total_records = $this->cache->get($count_cache_key);

		if ($vendor_list === FALSE || $total_records === FALSE) {
			// Cache miss - get fresh data from database
			// Fix: Pass empty string for page parameter since pagination is handled in model via input->get()
			$vendor_list = $this->vm->get_dpt_list($search, $sort, '', $per_page, TRUE, $filter);
			$total_records = $this->vm->get_dpt_list_count($search, $filter);
			
			// Store in cache
			$this->cache->save($cache_key, $vendor_list, $cache_duration);
			$this->cache->save($count_cache_key, $total_records, $cache_duration);
		}

		// Fix: Get CSMS limit data without pagination  
		$csms_cache_key = 'csms_limit_' . md5(serialize(['search' => $search, 'sort' => $sort]));
		$csms_limit = $this->cache->get($csms_cache_key);
		
		if ($csms_limit === FALSE) {
			// Pass FALSE for is_page to get all records for CSMS limit
			$csms_limit = $this->vm->get_csms_limit($search, $sort, '', $per_page, FALSE);
			$this->cache->save($csms_cache_key, $csms_limit, $cache_duration);
		}

		$data['vendor_list'] = $vendor_list;
		$data['csms_limit'] = $csms_limit;
		$data['filter_list'] = $this->filter->group_filter_post($this->filter->get_field());
		$data['pagination'] = $this->utility->generate_page('admin/admin_dpt', $sort, $per_page, $total_records);
		$data['sort'] = $sort;

		$layout['content']= $this->load->view('dpt/content_dpt',$data,TRUE);

		$layout['script']= $this->load->view('dpt/content_dpt_js',$data,TRUE);

		// $layout['script']= $this->load->view('dpt/form_filter',$data,TRUE);

		$item['header'] = $this->load->view('admin/header',$this->session->userdata('admin'),TRUE);

		$item['content'] = $this->load->view('admin/dashboard',$layout,TRUE);

		$this->load->view('template',$item);

	}

	public function export_excel($title="Data Penyedia Barang/Jasa"){
		$search = '';
		$sort = array();
		$page = '';
		$per_page = 0;
		$data = $this->vm->get_dpt_list($search, $sort, $page, $per_page,TRUE);
		$csms_limit = $this->vm->get_csms_limit();
		// print_r($data);die;	
		$table = "<table border=1>";

			$table .= "<tr>";
			$table .= "<td style='background: #f6e58d;'>No</td>";
			$table .= "<td style='background: #f6e58d;'>Nama Badan Usaha</td>";
			$table .= "<td style='background: #f6e58d;'>Nama Penyedia Barang/Jasa</td>";
			$table .= "<td style='background: #f6e58d;'>Kategori CSMS</td>";
			$table .= "<td style='background: #f6e58d;'>Nomor Sertifikat</td>";
			$table .= "<td style='background: #f6e58d;'>NPWP</td>";
			$table .= "<td style='background: #f6e58d;'>Alamat</td>";
			$table .= "<td style='background: #f6e58d;'>Telepon</td>";
			$table .= "<td style='background: #f6e58d;'>Email</td>";
			$table .= "</tr>";

		foreach ($data as $key => $value) {
			# code...
			$no = $key + 1;
			$table .= "<tr>";
			$table .= "<td>".$no."</td>";
			$table .= "<td>".$value['legal']."</td>";
			$table .= "<td>".$value['name']."</td>";
            
			    $table_ = "<td>-</td>";
			foreach ($csms_limit as $key_ => $value_) {
                # code...

                if ($value['score'] > $value_['end_score'] && $value['score'] < $value_['start_score']) {
                    # code...
			        $table_ = "<td>".$value_['value']."</td>";
                }
			}
			$table .= $table_;
			$table .= "<td>".$value['certificate_no']."</td>";
			$table .= "<td>".$value['npwp_code']."</td>";
			$table .= "<td>".$value['vendor_address']."</td>";
			$table .= "<td>".$value['vendor_phone']."</td>";
			$table .= "<td>".$value['email']."</td>";
			$table .= "</tr>";
		}
		$table .= "</table>";
		header('Content-type: application/ms-excel');

    	header('Content-Disposition: attachment; filename='.$title.'.xls');



		echo $table;
	}

}
